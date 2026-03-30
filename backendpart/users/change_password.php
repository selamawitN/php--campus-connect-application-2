<?php

require_once '../../config/db.php'; 

session_start();
header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}


if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];

if (empty($current_password)) $errors[] = "Current password is required";
if (empty($new_password)) $errors[] = "New password is required";
if ($new_password !== $confirm_password) $errors[] = "New passwords do not match";

// Password strength validation
if (!empty($new_password) && (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password))) {
    $errors[] = "Password must be at least 8 characters and contain uppercase, lowercase, and numbers";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}


?>
