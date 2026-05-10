<?php
$conn = new mysqli("localhost", "root", "", "campus_connect");
$id = $_GET['id'] ?? 0;
$conn->query("UPDATE materials SET downloads_count = downloads_count + 1 WHERE id = $id");
$result = $conn->query("SELECT file_path, file_name FROM materials WHERE id = $id");
$file = $result->fetch_assoc();
//continue from here 
if ($file && file_exists($file['file_path'])) {
//stopped here
//ask tsi if this clearly works 
//i am actually so scared of the finals 
// am i a fast writer or not I am actually so confused
//typing 100 words per minute is what makes u a fast writer or nott
// and i will see if i can do that or not
// i am actually so proud of myself for writing tis fast yayyyy
