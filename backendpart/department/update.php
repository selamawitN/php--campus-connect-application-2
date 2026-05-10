<?php
require_once '../config/db.php';
require_once '../auth/check_auth.php';

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare(
    "UPDATE departments SET name=?, description=?, head=?, email=? WHERE id=?"
);

$stmt->bind_param(
    "ssssi",
    $data['name'],
    $data['description'],
    $data['head'],
    $data['email'],
    $data['id']
);

$stmt->execute();

echo json_encode(["success" => true]);
?>
