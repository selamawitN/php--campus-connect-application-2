<?php
/**
 * /backendpart/department/announcements.php
 *
 * GET    ?department_id=1&page=1
 * POST   { department_id, title, body, is_pinned }   – admin
 * PUT    { id, title, body, is_pinned }               – admin
 * DELETE ?id=3                                        – admin
 */

require_once '../config/db.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') sendJSON([], 200);

session_start();

// ── GET ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $deptId = (int)($_GET['department_id'] ?? 0);
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $where  = $deptId ? 'WHERE da.department_id = ?' : '';
    $params = $deptId ? [$deptId] : [];
    $types  = $deptId ? 'i' : '';

    $cntStmt = $conn->prepare("SELECT COUNT(*) AS total FROM department_announcements da $where");
    if ($types) $cntStmt->bind_param($types, ...$params);
    $cntStmt->execute();
    $total = $cntStmt->get_result()->fetch_assoc()['total'];
    $cntStmt->close();

    $sql  = "SELECT da.*, u.fullname AS posted_by_name, d.name AS department_name
             FROM department_announcements da
             JOIN users u ON u.id = da.posted_by
             JOIN departments d ON d.id = da.department_id
             $where
             ORDER BY da.is_pinned DESC, da.created_at DESC
             LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $allP = array_merge($params, [$limit, $offset]);
    $allT = $types . 'ii';
    $stmt->bind_param($allT, ...$allP);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    sendSuccess([
        'announcements' => $rows,
        'pagination'    => ['total' => (int)$total, 'page' => $page, 'limit' => $limit, 'total_pages' => (int)ceil($total / $limit)]
    ], 'Announcements fetched');
}

// ── POST ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
    if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['department_id'])) sendError('department_id required.', 400);
    if (empty(trim($data['title'] ?? ''))) sendError('Title is required.', 400);
    if (empty(trim($data['body']  ?? ''))) sendError('Body is required.', 400);

    $deptId  = (int)$data['department_id'];
    $title   = trim($data['title']);
    $body    = trim($data['body']);
    $pinned  = (int)($data['is_pinned'] ?? 0);
    $userId  = (int)$_SESSION['user_id'];

    $stmt = $conn->prepare(
        'INSERT INTO department_announcements (department_id, title, body, posted_by, is_pinned) VALUES (?,?,?,?,?)'
    );
    $stmt->bind_param('issii', $deptId, $title, $body, $userId, $pinned);
    if (!$stmt->execute()) sendError('Failed to create announcement.', 500);
    $newId = $conn->insert_id;
    $stmt->close();

    logAudit($conn, $deptId, 'CREATE', $userId, "Announcement: $title");
    sendSuccess(['id' => $newId], 'Announcement created');
}

// ── PUT ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
    if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int)($data['id'] ?? 0);
    if (!$id) sendError('Announcement ID required.', 400);

    $title  = trim($data['title'] ?? '');
    $body   = trim($data['body']  ?? '');
    $pinned = (int)($data['is_pinned'] ?? 0);

    $stmt = $conn->prepare('UPDATE department_announcements SET title=?, body=?, is_pinned=? WHERE id=?');
    $stmt->bind_param('ssii', $title, $body, $pinned, $id);
    if (!$stmt->execute()) sendError('Update failed.', 500);
    $stmt->close();

    sendSuccess(null, 'Announcement updated');
}

// ── DELETE ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
    if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) sendError('ID required.', 400);

    $stmt = $conn->prepare('DELETE FROM department_announcements WHERE id=?');
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) sendError('Delete failed.', 500);
    $stmt->close();

    sendSuccess(null, 'Announcement deleted');
}

sendError('Method not allowed.', 405);
