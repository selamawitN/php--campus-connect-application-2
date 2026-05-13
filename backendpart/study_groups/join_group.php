<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../config/db.php");

$input = json_decode(file_get_contents('php://input'), true);

$group_id = $input['group_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;
$user_name = trim($input['user_name'] ?? $_SESSION['user_fullname'] ?? '');

if (empty($group_id) || empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Group ID and user are required']);
    exit;
}

// Check if already a member
$check_sql = "SELECT * FROM group_members WHERE group_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $group_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'You are already a member of this group']);
    exit;
}

// Add to group members - includes the 'role' column
$sql = "INSERT INTO group_members (group_id, user_id, role, joined_at) VALUES (?, ?, 'member', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $group_id, $user_id);

if ($stmt->execute()) {
    // Update member count in groups table
    $update_sql = "UPDATE `groups` SET member_count = (SELECT COUNT(*) FROM group_members WHERE group_id = ?) WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $group_id, $group_id);
    $update_stmt->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'Successfully joined the group']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?><?php
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
