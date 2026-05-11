<?php
/**
 * GET /backendpart/department/get_department_detail.php?id=1
 *
 * Returns one department with its full course list grouped by year/semester.
 */

require_once '../config/db.php';
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') sendJSON([], 200);

$id = (int)($_GET['id'] ?? 0);
if (!$id) sendError('Department ID is required.', 400);

// ── fetch department ───────────────────────────────────────
$stmt = $conn->prepare('SELECT * FROM departments WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$dept = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dept) sendError('Department not found.', 404);

// ── fetch courses grouped by year & semester ───────────────
$cStmt = $conn->prepare(
    'SELECT * FROM courses WHERE department_id = ? ORDER BY year, semester, course_code'
);
$cStmt->bind_param('i', $id);
$cStmt->execute();
$courses = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cStmt->close();

// ── group courses into nested structure ────────────────────
$grouped = [];
foreach ($courses as $c) {
    $field  = $c['field'];
    $year   = 'Year ' . $c['year'];
    $sem    = 'Semester ' . $c['semester'];
    $grouped[$field][$year][$sem][] = $c;
}

$dept['courses_grouped'] = $grouped;
$dept['total_courses']   = count($courses);

// ── fetch recent announcements ─────────────────────────────
$aStmt = $conn->prepare(
    'SELECT da.*, u.fullname AS posted_by_name
     FROM department_announcements da
     JOIN users u ON u.id = da.posted_by
     WHERE da.department_id = ?
     ORDER BY da.is_pinned DESC, da.created_at DESC
     LIMIT 5'
);
$aStmt->bind_param('i', $id);
$aStmt->execute();
$dept['announcements'] = $aStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$aStmt->close();

// ── audit log ──────────────────────────────────────────────
session_start();
$userId = $_SESSION['user_id'] ?? null;
logAudit($conn, $id, 'VIEW', $userId, 'Detail view');

sendSuccess($dept, 'Department fetched successfully');
