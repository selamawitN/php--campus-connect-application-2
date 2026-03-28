<?php
include("../config/db.php");

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p>" . htmlspecialchars($row['message']) . "</p>";
        echo "<small>Posted by: " . htmlspecialchars($row['created_by']) . " | " . $row['created_at'] . "</small>";
        echo "<hr>";
        echo "</div>";
    }
} else {
    echo "No announcements found.";
}

$conn->close();
?>
