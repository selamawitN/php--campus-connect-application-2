<?php
sesiion_start();
header('Content-Type: application/json');
header('Access-control-Allow-Orign: *');
include('../config/db.php");
$input=json_decode(file_get_contents('php://input'),true);
$id=$input['id'] ?? 0;
//continue here..
//the input has been a comment ask that
$result = $conn->query("SELECT file_path FROM materials WHERE id = $id");
$file = $result->fetch_assoc();

if ($file && file_exists($file['file_path'])) {
    unlink($file['file_path']);
}

$conn->query("DELETE FROM materials WHERE id = $id");
echo json_encode(['status' => 'success', 'message' => 'Deleted']);
$conn->close();
?>
