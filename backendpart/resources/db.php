<?php
$conn = new mysqli("localhost", "root", "", "material_sharing");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
//a little bit confused here but ask what is happening
