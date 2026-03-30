<?php
$conn = new mysqli("localhost", "root", "", "campus_connect");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM materials ORDER BY id DESC";
$result = $conn->query($sql);

$materials = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}

echo json_encode($materials);

$conn->close();
?>
