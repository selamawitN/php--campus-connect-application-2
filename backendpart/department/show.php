<?php
require_once '../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["error" => "ID required"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM departments WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
