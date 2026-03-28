<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $created_by = trim($_POST['created_by']);

    if (!empty($title) && !empty($message) && !empty($created_by)) {
        $sql = "INSERT INTO announcements (title, message, created_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $title, $message, $created_by);

        if ($stmt->execute()) {
            echo "Announcement created successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>
