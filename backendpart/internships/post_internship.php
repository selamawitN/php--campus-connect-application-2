<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$title = $_POST['title'] ?? '';
$company = $_POST['company'] ?? '';
$deadline = $_POST['deadline'] ?? '';

if (empty($title) || empty($company) || empty($deadline)) {
    echo json_encode(['status' => 'error', 'message' => 'Title, company and deadline required']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$sql = "INSERT INTO internships (title, company, description, location, stipend, duration, deadline, requirements, year_requirement, work_type, posted_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$result = $stmt->execute([
    $title,
    $company,
    $_POST['description'] ?? '',
    $_POST['location'] ?? '',
    $_POST['stipend'] ?? '',
    $_POST['duration'] ?? '',
    $deadline,
    $_POST['requirements'] ?? '',
    $_POST['year_requirement'] ?? '',
    $_POST['work_type'] ?? '',
    $_SESSION['user_id']
]);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Internship posted!', 'id' => $conn->lastInsertId()]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to post']);
}
?>
