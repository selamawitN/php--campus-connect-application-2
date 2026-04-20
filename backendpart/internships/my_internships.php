<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM internships WHERE posted_by=? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$list = $stmt->fetchAll();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Internships</title>
</head>
<body>
    <h2>My Internships</h2>
    <p><a href="post_form.php">+ Post New Internship</a></p>
    
    <?php if ($msg == 'updated'): ?>
        <p style="color:green">Updated successfully!</p>
    <?php elseif ($msg == 'deleted'): ?>
        <p style="color:green">Deleted successfully!</p>
    <?php endif; ?>
    
    <?php if (count($list) == 0): ?>
        <p>No internships posted yet.</p>
    <?php else: ?>
        <?php foreach ($list as $item): ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
                <h3><?= $item['title'] ?> at <?= $item['company'] ?></h3>
                <p>Location: <?= $item['location'] ?: 'N/A' ?> | Stipend: <?= $item['stipend'] ?: 'N/A' ?></p>
                <p>Deadline: <?= $item['deadline'] ?></p>
                <p>
                    <a href="edit_internship.php?id=<?= $item['id'] ?>">Edit</a> | 
                    <a href="delete_internship.php?id=<?= $item['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <p><a href="../dashboard.php">Back to Dashboard</a></p>
</body>
</html>
