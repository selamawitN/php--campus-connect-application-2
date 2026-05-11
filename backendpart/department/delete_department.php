<?php
/**
 * DELETE /backendpart/department/delete_department.php?id=1
 *
 * Soft-deletes (sets is_active = 0) to preserve referential data.
 * Hard-delete: pass &hard=1  (admin only).
 * Requires admin session.
 */

require_once '../config/db.php';
require_once 'helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') sendJSON([], 200);

// ── auth ───────────────────────────────────────────────────
session_start();
if (empty($_SESSION['user_id']))        sendError('Authentication required.', 401);
if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

// ── params ─────────────────────────────────────────────────
$id   = (int)($_GET['id'] ?? 0);
$hard = ($_GET['hard'] ?? '0') === '1';

if (!$id) sendError('Department ID is required.', 400);

// ── exists? ────────────────────────────────────────────────
$chk = $conn->prepare('SELECT id, name FROM departments WHERE id = ?');
$chk->bind_param('i', $id);
$chk->execute();
$dept = $chk->get_result()->fetch_assoc();
$chk->close();
if (!$dept) sendError('Department not found.', 404);

if ($hard) {
    // Hard delete – cascades to courses via FK
    $stmt = $conn->prepare('DELETE FROM departments WHERE id = ?');
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) sendError('Hard delete failed: ' . $conn->error, 500);
    $stmt->close();
    logAudit($conn, $id, 'DELETE', $_SESSION['user_id'], 'HARD DELETE: ' . $dept['name']);
    sendSuccess(null, 'Department permanently deleted.');
} else {
    // Soft delete
    $stmt = $conn->prepare('UPDATE departments SET is_active = 0 WHERE id = ?');
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) sendError('Soft delete failed: ' . $conn->error, 500);
    $stmt->close();
    logAudit($conn, $id, 'DELETE', $_SESSION['user_id'], 'SOFT DELETE: ' . $dept['name']);
    sendSuccess(null, 'Department deactivated successfully.');
}
