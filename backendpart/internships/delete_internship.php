<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("DELETE FROM internships WHERE id=? AND posted_by=?");
$stmt->execute([$id, $_SESSION['user_id']]);

header('Location: my_internships.php?msg=deleted');
?>
