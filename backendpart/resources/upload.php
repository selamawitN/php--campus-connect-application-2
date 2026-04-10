<?php
include "db.php";

$name = $_POST['name'];
$student_id = $_POST['student_id'];
$year = $_POST['year'];
$type = $_POST['type'];

$file = $_FILES['file']['name'];
$temp = $_FILES['file']['tmp_name'];

$upload_folder = "uploads/";
move_uploaded_file($temp, $upload_folder . $file);

$sql = "INSERT INTO materials 
(student_name, student_id, year_level, material_type, file_name)
VALUES 
('$name', '$student_id', '$year', '$type', '$file')";

if ($conn->query($sql) === TRUE) {
    echo "File uploaded successfully! <br><a href='index.php'>Go Back</a>";
} else {
    echo "Error: " . $conn->error;
}
?>
