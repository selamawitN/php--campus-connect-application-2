<?php
include 'config/db.php';
session_start();

// Example: logged-in user
$user_id = $_SESSION['user_id'] ?? 1; // replace with actual login system

if(isset($_POST['group_id'])) {
    $group_id = (int)$_POST['group_id'];

    // Check if user already joined
    $check = $conn->query("SELECT * FROM group_members WHERE user_id=$user_id AND group_id=$group_id");
    if($check->num_rows > 0){
        echo "You already joined this group!";
    } else {
        $conn->query("INSERT INTO group_members (user_id, group_id) VALUES ($user_id, $group_id)");
        echo "Joined group successfully!";
    }
}

// Show all groups
$result = $conn->query("SELECT * FROM groups");
?>
<form method="POST">
    <select name="group_id">
        <?php while($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['group_id'] ?>"><?= $row['group_name'] ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Join Group</button>
</form>
