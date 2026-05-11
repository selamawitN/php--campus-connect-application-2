<?php
/**
 * GET /backendpart/department/get_departments.php
 *
 * Query params:
 *   search  – name/code partial match
 *   page    – page number (default 1)
 *   limit   – rows per page (default 10, max 50)
 *   active  – 1 | 0 | '' (all)
 *
 * Returns paginated department list with course counts.
 */

require_once '../config/db.php';
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJSON([], 200);
}

// ── params ─────────────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page']  ?? 1));
$limit  = min(50, max(1, (int)($_GET['limit'] ?? 10)));
$active = $_GET['active'] ?? '';          // '', '0', '1'
$offset = ($page - 1) * $limit;

// ── build WHERE ────────────────────────────────────────────
$conditions = [];
$params     = [];
$types      = '';

if ($search !== '') {
    $conditions[] = '(d.name LIKE ? OR d.code LIKE ? OR d.head LIKE ?)';
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}
if ($active !== '') {
    $conditions[] = 'd.is_active = ?';
    $params[] = (int)$active;
    $types .= 'i';
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// ── total count ────────────────────────────────────────────
$countSql  = "SELECT COUNT(*) AS total FROM departments d $where";
$countStmt = $conn->prepare($countSql);
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();

// ── paginated data ─────────────────────────────────────────
$dataSql = "
    SELECT d.*,
           COUNT(c.id) AS course_count
    FROM departments d
    LEFT JOIN courses c ON c.department_id = d.id
    $where
    GROUP BY d.id
    ORDER BY d.name ASC
    LIMIT ? OFFSET ?
";

$dataStmt = $conn->prepare($dataSql);
$allParams = array_merge($params, [$limit, $offset]);
$allTypes  = $types . 'ii';
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$rows = $dataStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$dataStmt->close();

// ── audit log ──────────────────────────────────────────────
session_start();
$userId = $_SESSION['user_id'] ?? null;
logAudit($conn, null, 'VIEW', $userId, json_encode(['search' => $search, 'page' => $page]));

// ── respond ────────────────────────────────────────────────
sendSuccess([
    'departments' => $rows,
    'pagination'  => [
        'total'        => (int)$total,
        'page'         => $page,
        'limit'        => $limit,
        'total_pages'  => (int)ceil($total / $limit),
    ]
], 'Departments fetched successfully');
