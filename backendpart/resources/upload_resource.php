<?php
$conn = new mysqli("localhost", "root", "", "campus_connect");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data safely
$title = $_POST['title'] ?? '';
$fullName = $_POST['fullName'] ?? 'Anonymous';
$year = $_POST['year'] ?? '';
$department = $_POST['department'] ?? '';
$type = $_POST['type'] ?? '';

// File upload
$fileName = $_FILES['file']['name'];
$tempName = $_FILES['file']['tmp_name'];
$folder = "uploads/" . $fileName;

// Move file
if (move_uploaded_file($tempName, $folder)) {

    // Insert into database
    $sql = "INSERT INTO materials (title, full_name, year, department, type, file_name)
            VALUES ('$title', '$fullName', '$year', '$department', '$type', '$fileName')";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "database error";
    }

} else {
    echo "file upload failed";
}

$conn->close();
?>
