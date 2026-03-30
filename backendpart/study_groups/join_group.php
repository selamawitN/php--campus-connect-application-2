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
        // Check if the group has reached max members
        $group_info = $conn->query("SELECT COUNT(gm.user_name) AS members_count, g.max_members 
                                    FROM groups g 
                                    LEFT JOIN group_members gm ON g.group_id = gm.group_id 
                                    WHERE g.group_id = $group_id 
                                    GROUP BY g.group_id")->fetch_assoc();

        if($group_info['members_count'] >= $group_info['max_members']){
            echo "Cannot join $user_name: the group has reached its maximum members!";
        } else {
            $conn->query("INSERT INTO group_members (group_id, user_name) VALUES ($group_id, '$user_name')");
            echo "$user_name joined the group successfully!";
        }
    }
}

// Fetch groups with member count
$groups = $conn->query("SELECT g.group_id, g.group_name, COUNT(gm.user_name) AS members, g.max_members
                        FROM groups g
                        LEFT JOIN group_members gm ON g.group_id = gm.group_id
                        GROUP BY g.group_id");
?>

<h2>Join Group</h2>
<form method="POST">
    <input type="text" name="user_name" placeholder="Your Name" required>
    <select name="group_id">
        <?php while($row = $groups->fetch_assoc()): ?>
            <option value="<?= $row['group_id'] ?>">
                <?= htmlspecialchars($row['group_name']) ?> 
                (<?= $row['members'] ?>/<?= $row['max_members'] ?> members)
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Join Group</button>
</form>
