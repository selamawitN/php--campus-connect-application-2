<?php
header("Content-Type: application/json");
include_once "../config/db.php";

if (!isset($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Department id is required"
    ]);
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT id, department_name, department_description FROM departments WHERE id = $id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $department = mysqli_fetch_assoc($result);

    echo json_encode([
        "success" => true,
        "message" => "Department detail fetched successfully",
        "data" => $department
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Department not found"
    ]);
}
?>
