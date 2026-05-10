<?php
require_once '../config/db.php';

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$searchParam = "%" . $search . "%";

$stmt = $conn->prepare(
    "SELECT * FROM departments WHERE name LIKE ? LIMIT ? OFFSET ?"
);
$stmt->bind_param("sii", $searchParam, $limit, $offset);

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
