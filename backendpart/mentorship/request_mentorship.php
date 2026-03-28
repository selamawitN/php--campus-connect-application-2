<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mentee_name = trim($_POST['mentee_name']);
    $mentor_name = trim($_POST['mentor_name']);
    $department = trim($_POST['department']);
    $message = trim($_POST['message']);

    if (!empty($mentee_name) && !empty($mentor_name) && !empty($department) && !empty($message)) {
        $sql = "INSERT INTO mentorship_requests (mentee_name, mentor_name, department, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $mentee_name, $mentor_name, $department, $message);

        if ($stmt->execute()) {
            echo "Mentorship request submitted successfully.";
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
