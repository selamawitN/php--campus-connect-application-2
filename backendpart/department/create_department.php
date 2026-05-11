<?php
/**
 * POST /backendpart/department/create_department.php
 *
 * Body (JSON):
 *   name, code, description, head, email, phone,
 *   office_location, established_year, total_students
 *
 * Requires admin session.
 */

require_once '../config/db.php';
require_once 'helpers.php';
require_once 'validator.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') sendJSON([], 200);
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    sendError('Method not allowed.', 405);

// ── auth check ─────────────────────────────────────────────
session_start();
if (empty($_SESSION['user_id'])) sendError('Authentication required.', 401);
if ($_SESSION['user_role'] !== 'admin') sendError('Admin access required.', 403);

// ── parse body ─────────────────────────────────────────────
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) sendError('Invalid JSON body.', 400);

// ── validate ───────────────────────────────────────────────
$errors = validateDepartment($data);
if ($errors) sendError('Validation failed.', 422, ['errors' => $errors]);

// ── duplicate code check ───────────────────────────────────
$code = strtoupper(trim($data['code']));
$chk  = $conn->prepare('SELECT id FROM departments WHERE code = ?');
$chk->bind_param('s', $code);
$chk->execute();
if ($chk->get_result()->num_rows > 0) sendError("Department code '$code' already exists.", 409);
$chk->close();

// ── insert ─────────────────────────────────────────────────
$stmt = $conn->prepare(
    'INSERT INTO departments (name, code, description, head, email, phone, office_location, established_year, total_students)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$name     = trim($data['name']);
$desc     = trim($data['description'] ?? '');
$head     = trim($data['head'] ?? '');
$email    = trim($data['email'] ?? '');
$phone    = trim($data['phone'] ?? '');
$office   = trim($data['office_location'] ?? '');
$estYear  = !empty($data['established_year']) ? (int)$data['established_year'] : null;
$students = (int)($data['total_students'] ?? 0);

$stmt->bind_param('sssssssii', $name, $code, $desc, $head, $email, $phone, $office, $estYear, $students);

if (!$stmt->execute()) sendError('Failed to create department: ' . $conn->error, 500);

$newId = $conn->insert_id;
$stmt->close();

// ── audit log ──────────────────────────────────────────────
logAudit($conn, $newId, 'CREATE', $_SESSION['user_id'],
    json_encode(['name' => $name, 'code' => $code]));

sendSuccess(['id' => $newId], 'Department created successfully');
