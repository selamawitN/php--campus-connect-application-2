<?php
include 'config/db.php';

if(isset($_POST['group_id'], $_POST['user_name'])) {
    $group_id = (int)$_POST['group_id'];
    $user_name = $conn->real_escape_string($_POST['user_name']);

    // Check if name already joined
    $check = $conn->query("SELECT * FROM group_members WHERE group_id=$group_id AND user_name='$user_name'");
    if($check->num_rows > 0){
        echo "$user_name is already in the group!";
    } else {
        $conn->query("INSERT INTO group_members (user_id, group_id, user_name) VALUES (NULL, $group_id, '$user_name')");
        echo "$user_name joined the group successfully!";
    }
}

// Fetch all groups
$groups = $conn->query("SELECT g.group_id, g.group_name, COUNT(gm.user_name) AS members
    FROM groups g
    LEFT JOIN group_members gm ON g.group_id = gm.group_id
    GROUP BY g.group_id
");
?>

<h2>Join Group</h2>
<form method="POST">
    <input type="text" name="user_name" placeholder="Your Name" required>
    <select name="group_id">
        <?php while($row = $groups->fetch_assoc()): ?>
            <option value="<?= $row['group_id'] ?>">
                <?= $row['group_name'] ?> (<?= $row['members'] ?> members)
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Join Group</button>
</form>
