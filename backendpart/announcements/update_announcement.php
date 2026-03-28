<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    if (!empty($id) && !empty($title) && !empty($message)) {
        $sql = "UPDATE announcements SET title = ?, message = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $message, $id);

        if ($stmt->execute()) {
            echo "Announcement updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "ID, title, and message are required.";
    }
}

$conn->close();
?>
