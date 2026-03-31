<?php
header("Content-Type: application/json");
include_once "../config/db.php";

$sql = "SELECT id, department_name, department_description FROM departments";
$result = mysqli_query($conn, $sql);

$departments = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Departments fetched successfully",
        "data" => $departments
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch departments"
    ]);
}
?>
