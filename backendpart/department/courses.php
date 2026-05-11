<?php
/**
 * /backendpart/department/courses.php
 *
 * GET    ?department_id=1&year=3&semester=1&field=...&search=...&page=1
 * POST   { department_id, course_code, course_name, credit_hours, prerequisites, year, semester, field, description }
 * PUT    { id, ...updatable fields }
 * DELETE ?id=5
 *
 * POST/PUT/DELETE require admin session.
 */

require_once '../config/db.php';
require_once 'helpers.php';
require_once 'validator.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') sendJSON([], 200);

session_start();

// ════════════════════════════════════════════════════════════
// GET – list courses
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $deptId   = (int)($_GET['department_id'] ?? 0);
    $year     = isset($_GET['year'])     ? (int)$_GET['year']     : null;
    $semester = isset($_GET['semester']) ? (int)$_GET['semester'] : null;
    $field    = trim($_GET['field']    ?? '');
    $search   = trim($_GET['search']   ?? '');
    $page     = max(1, (int)($_GET['page']  ?? 1));
    $limit    = min(100, max(1, (int)($_GET['limit'] ?? 50)));
    $offset   = ($page - 1) * $limit;

    $conds  = [];
    $params = [];
    $types  = '';

    if ($deptId) { $conds[] = 'department_id = ?'; $params[] = $deptId; $types .= 'i'; }
    if ($year)   { $conds[] = 'year = ?';           $params[] = $year;   $types .= 'i'; }
    if ($semester){ $conds[] = 'semester = ?';       $params[] = $semester; $types .= 'i'; }
    if ($field)  { $conds[] = 'field = ?';           $params[] = $field;  $types .= 's'; }
    if ($search) {
        $conds[] = '(course_code LIKE ? OR course_name LIKE ?)';
        $like = "%$search%";
        $params = array_merge($params, [$like, $like]);
        $types .= 'ss';
    }

    $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';

    // total
    $cntStmt = $conn->prepare("SELECT COUNT(*) AS total FROM courses $where");
    if ($types) $cntStmt->bind_param($types, ...$params);
    $cntStmt->execute();
    $total = $cntStmt->get_result()->fetch_assoc()['total'];
    $cntStmt->close();

    // data
    $sql  = "SELECT * FROM courses $where ORDER BY year, semester, course_code LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $allParams = array_merge($params, [$limit, $offset]);
    $allTypes  = $types . 'ii';
    $stmt->bind_param($allTypes, ...$allParams);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // group by field > year > semester for frontend convenience
    $grouped = [];
    foreach ($courses as $c) {
        $f = $c['field'];
        $y = 'Year ' . $c['year'];
        $s = 'Semester ' . $c['semester'];
        $grouped[$f][$y][$s][] = $c;
    }

    sendSuccess([
        'courses'    => $courses,
        'grouped'    => $grouped,
        'pagination' => [
            'total'       => (int)$total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => (int)ceil($total / $limit),
        ]
    ], 'Courses fetched successfully');
}

// ════════════════════════════════════════════════════════════
// POST – create course (admin)
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
    if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) sendError('Invalid JSON.', 400);

    $errors = validateCourse($data);
    if ($errors) sendError('Validation failed.', 422, ['errors' => $errors]);

    // dept exists?
    $chk = $conn->prepare('SELECT id FROM departments WHERE id = ?');
    $deptId = (int)$data['department_id'];
    $chk->bind_param('i', $deptId);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) sendError('Department not found.', 404);
    $chk->close();

    $stmt = $conn->prepare(
        'INSERT INTO courses (department_id, course_code, course_name, credit_hours, prerequisites, year, semester, field, description, is_elective)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $code    = strtoupper(trim($data['course_code']));
    $cname   = trim($data['course_name']);
    $cr      = (int)($data['credit_hours'] ?? 3);
    $pre     = trim($data['prerequisites'] ?? 'None');
    $year    = (int)$data['year'];
    $sem     = (int)$data['semester'];
    $field   = trim($data['field'] ?? '');
    $desc    = trim($data['description'] ?? '');
    $elec    = (int)($data['is_elective'] ?? 0);

    $stmt->bind_param('issisissi i', $deptId, $code, $cname, $cr, $pre, $year, $sem, $field, $desc, $elec);
    // fix bind_param string
    $stmt->close();

    $stmt2 = $conn->prepare(
        'INSERT INTO courses (department_id, course_code, course_name, credit_hours, prerequisites, year, semester, field, description, is_elective)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt2->bind_param('issisisssi', $deptId, $code, $cname, $cr, $pre, $year, $sem, $field, $desc, $elec);
    if (!$stmt2->execute()) sendError('Failed to create course: ' . $conn->error, 500);
    $newId = $conn->insert_id;
    $stmt2->close();

    logAudit($conn, $deptId, 'CREATE', $_SESSION['user_id'],
        json_encode(['course_code' => $code, 'course_name' => $cname]));

    sendSuccess(['id' => $newId], 'Course created successfully');
}

// ════════════════════════════════════════════════════════════
// PUT – update course (admin)
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
    if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) sendError('Invalid JSON.', 400);

    $id = (int)($data['id'] ?? 0);
    if (!$id) sendError('Course ID is required.', 400);

    $errors = validateCourse($data, true);
    if ($errors) sendError('Validation failed.', 422, ['errors' => $errors]);

    $allowed = ['course_code','course_name','credit_hours','prerequisites','year','semester','field','description','is_elective'];
    $setClauses = [];
    $setParams  = [];
    $setTypes   = '';

    foreach ($allowed as $f) {
        if (array_key_exists($f, $data)) {
            $setClauses[] = "$f = ?";
            $val = ($f === 'course_code') ? strtoupper(trim($data[$f])) : $data[$f];
            $setParams[] = $val;
            $setTypes   .= in_array($f, ['credit_hours','year','semester','is_elective']) ? 'i' : 's';
        }
    }

    if (!$setClauses) sendError('No valid fields to update.', 400);

    $sql   = 'UPDATE courses SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
    $setParams[] = $id;
    $setTypes   .= 'i';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($setTypes, ...$setParams);
    if (!$stmt->execute()) sendError('Failed to update course: ' . $conn->error, 500);
    $stmt->close();

    logAudit($conn, $data['department_id'] ?? null, 'UPDATE', $_SESSION['user_id'],
        json_encode(['course_id' => $id]));

    sendSuccess(null, 'Course updated successfully');
}

// ════════════════════════════════════════════════════════════
// DELETE – remove course (admin)
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
    if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('Course ID is required.', 400);

    $chk = $conn->prepare('SELECT department_id FROM courses WHERE id = ?');
    $chk->bind_param('i', $id);
    $chk->execute();
    $row = $chk->get_result()->fetch_assoc();
    $chk->close();
    if (!$row) sendError('Course not found.', 404);

    $stmt = $conn->prepare('DELETE FROM courses WHERE id = ?');
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) sendError('Failed to delete course.', 500);
    $stmt->close();

    logAudit($conn, $row['department_id'], 'DELETE', $_SESSION['user_id'],
        json_encode(['course_id' => $id]));

    sendSuccess(null, 'Course deleted successfully');
}

sendError('Method not allowed.', 405);
