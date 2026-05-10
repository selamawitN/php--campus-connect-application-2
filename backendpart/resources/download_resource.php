<?php
$conn = new mysqli("localhost", "root", "", "campus_connect");
$id = $_GET['id'] ?? 0;
$conn->query("UPDATE materials SET downloads_count = downloads_count + 1 WHERE id = $id");
$result = $conn->query("SELECT file_path, file_name FROM materials WHERE id = $id");
$file = $result->fetch_assoc();
//continue from here 
