<?php
/**
 * PUT /backendpart/department/update_department.php
 *
 * Body (JSON): id + any updatable fields
 * Requires admin session.
 */

require_once '../config/db.php';
require_once 'helpers.php';
require_once 'validator.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') sendJSON([], 200);
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) sendError('Method not allowed.', 405);

// ── auth ───────────────────────────────────────────────────
session_start();
if (empty($_SESSION['user_id']))         sendError('Authentication required.', 401);
if ($_SESSION['user_role'] !== 'admin')  sendError('Admin access required.', 403);

// ── parse ──────────────────────────────────────────────────
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) sendError('Invalid JSON body.', 400);

$id = (int)($data['id'] ?? 0);
if (!$id) sendError('Department ID is required.', 400);

// ── exists? ────────────────────────────────────────────────
$chk = $conn->prepare('SELECT id FROM departments WHERE id = ?');
$chk->bind_param('i', $id);
$chk->execute();
if ($chk->get_result()->num_rows === 0) sendError('Department not found.', 404);
$chk->close();

// ── validate ───────────────────────────────────────────────
$errors = validateDepartment($data, true);
if ($errors) sendError('Validation failed.', 422, ['errors' => $errors]);

// ── build dynamic SET clause ───────────────────────────────
$allowed = ['name','code','description','head','email','phone','office_location','established_year','total_students','is_active'];
$setClauses = [];
$setParams  = [];
$setTypes   = '';

foreach ($allowed as $field) {
    if (array_key_exists($field, $data)) {
        $setClauses[] = "$field = ?";
        $value = ($field === 'code') ? strtoupper(trim($data[$field])) : $data[$field];
        $setParams[] = $value;
        $setTypes   .= (in_array($field, ['established_year','total_students','is_active'])) ? 'i' : 's';
    }
}

if (!$setClauses) sendError('No valid fields provided to update.', 400);

$sql   = 'UPDATE departments SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
$setParams[] = $id;
$setTypes   .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($setTypes, ...$setParams);
if (!$stmt->execute()) sendError('Failed to update department: ' . $conn->error, 500);
$stmt->close();

// ── audit ──────────────────────────────────────────────────
logAudit($conn, $id, 'UPDATE', $_SESSION['user_id'], json_encode($data));

sendSuccess(null, 'Department updated successfully');
