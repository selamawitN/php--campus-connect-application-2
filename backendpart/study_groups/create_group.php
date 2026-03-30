<?php
include 'config/db.php';

if(isset($_POST['group_name'], $_POST['max_members'])) {
    $group_name = $conn->real_escape_string($_POST['group_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $max_members = (int)$_POST['max_members'];

    if($max_members <= 0) {
        echo "Max members must be greater than 0!";
    } else {
        $sql = "INSERT INTO groups (group_name, description, max_members) 
                VALUES ('$group_name', '$description', $max_members)";
        if($conn->query($sql) === TRUE) {
            echo "Group '$group_name' created successfully with max $max_members members!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Fetch groups with member count
$result = $conn->query("
    SELECT g.group_id, g.group_name, g.description, g.max_members, COUNT(gm.user_name) AS members
    FROM groups g
    LEFT JOIN group_members gm ON g.group_id = gm.group_id
    GROUP BY g.group_id
");
?>

<h2>Create Group</h2>
<form method="POST">
    <input type="text" name="group_name" placeholder="Group Name" required>
    <input type="text" name="description" placeholder="Group Description">
    <input type="number" name="max_members" placeholder="Max Members" required min="1">
    <button type="submit">Create Group</button>
</form>

<h3>Existing Groups</h3>
<ul>
<?php while($row = $result->fetch_assoc()): ?>
    <li>
        <?= htmlspecialchars($row['group_name']) ?> 
        (<?= $row['members'] ?>/<?= $row['max_members'] ?> members)
        <?php if($row['description']) echo "- " . htmlspecialchars($row['description']); ?>
    </li>
<?php endwhile; ?>
</ul>
