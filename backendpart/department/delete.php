<?php
require_once '../config/db.php';
require_once '../auth/check_auth.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM departments WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["success" => true]);
?>
