<?php
include 'config/db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 1;

if(isset($_POST['group_id'])) {
    $group_id = (int)$_POST['group_id'];

    // Check if user joined the group
    $check = $conn->query("SELECT * FROM group_members WHERE user_id=$user_id AND group_id=$group_id");
    if($check->num_rows > 0){
        // Leave the group
        $conn->query("DELETE FROM group_members WHERE user_id=$user_id AND group_id=$group_id");
        echo "You have left the group.";
    } else {
        echo "You are not a member of this group.";
    }
}

// Show all groups user joined
$result = $conn->query("SELECT g.group_id, g.group_name 
                        FROM groups g
                        JOIN group_members gm ON g.group_id = gm.group_id
                        WHERE gm.user_id=$user_id");
?>
<form method="POST">
    <select name="group_id">
        <?php while($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['group_id'] ?>"><?= $row['group_name'] ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Leave Group</button>
</form>
