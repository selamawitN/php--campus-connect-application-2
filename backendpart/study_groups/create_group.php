<?php
include 'config/db.php';

if(isset($_POST['group_name'])) {
    $group_name = $conn->real_escape_string($_POST['group_name']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "INSERT INTO groups (group_name, description) VALUES ('$group_name', '$description')";

    if($conn->query($sql) === TRUE) {
        echo "Group '$group_name' created successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch groups with member count
$result = $conn->query("
    SELECT g.group_id, g.group_name, g.description, COUNT(gm.user_name) AS members
    FROM groups g
    LEFT JOIN group_members gm ON g.group_id = gm.group_id
    GROUP BY g.group_id
");
?>

<h2>Create Group</h2>
<form method="POST">
    <input type="text" name="group_name" placeholder="Group Name" required>
    <input type="text" name="description" placeholder="Group Description">
    <button type="submit">Create Group</button>
</form>

<h3>Existing Groups</h3>
<ul>
<?php while($row = $result->fetch_assoc()): ?>
    <li>
        <?= $row['group_name'] ?> (<?= $row['members'] ?> members)
        <?php if($row['description']) echo "- " . $row['description']; ?>
    </li>
<?php endwhile; ?>
</ul>
