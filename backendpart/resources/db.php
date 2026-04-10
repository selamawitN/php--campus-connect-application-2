<?php
$conn = new mysqli("localhost", "root", "", "material_sharing");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
