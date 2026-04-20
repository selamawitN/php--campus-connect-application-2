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

$sql = "INSERT INTO internships (title, company, description, location, stipend, duration, deadline, requirements, year_requirement, work_type, posted_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query']);
    exit;
}

$description = $_POST['description'] ?? '';
$location = $_POST['location'] ?? '';
$stipend = $_POST['stipend'] ?? '';
$duration = $_POST['duration'] ?? '';
$requirements = $_POST['requirements'] ?? '';
$yearRequirement = $_POST['year_requirement'] ?? '';
$workType = $_POST['work_type'] ?? '';
$postedBy = (int) $_SESSION['user_id'];

$stmt->bind_param(
    "ssssssssssi",
    $title,
    $company,
    $description,
    $location,
    $stipend,
    $duration,
    $deadline,
    $requirements,
    $yearRequirement,
    $workType,
    $postedBy
);

$result = $stmt->execute();

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Internship posted!', 'id' => $conn->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to post']);
}
?>
