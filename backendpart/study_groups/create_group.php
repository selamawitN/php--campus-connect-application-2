<?php
include 'config/db.php';

if(isset($_POST['group_name'])) {
    $group_name = $conn->real_escape_string($_POST['group_name']);

    $sql = "INSERT INTO groups (group_name) VALUES ('$group_name')";

    if($conn->query($sql) === TRUE) {
        echo "Group created successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<form method="POST">
    <input type="text" name="group_name" placeholder="Group Name" required>
    <button type="submit">Create Group</button>
</form>
