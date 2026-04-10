<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "material_sharing");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

$sql = "SELECT * FROM materials ORDER BY id DESC";
$result = $conn->query($sql);

$materials = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}

echo json_encode($materials);

$conn->close();
?>
