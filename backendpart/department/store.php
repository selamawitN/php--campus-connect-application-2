<?php
require_once '../config/db.php';
require_once '../auth/check_auth.php';

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare(
    "INSERT INTO departments (name, description, head, email)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssss",
    $data['name'],
    $data['description'],
    $data['head'],
    $data['email']
);

$stmt->execute();

echo json_encode(["success" => true]);
?>
