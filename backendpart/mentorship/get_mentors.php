<?php
include("../config/db.php");

$sql = "SELECT * FROM mentorship_requests ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>Mentor: " . htmlspecialchars($row['mentor_name']) . "</h3>";
        echo "<p><strong>Mentee:</strong> " . htmlspecialchars($row['mentee_name']) . "</p>";
        echo "<p><strong>Department:</strong> " . htmlspecialchars($row['department']) . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($row['message']) . "</p>";
        echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
        echo "<small>" . $row['created_at'] . "</small>";
        echo "<hr>";
        echo "</div>";
    }
} else {
    echo "No mentorship requests found.";
}

$conn->close();
?>
