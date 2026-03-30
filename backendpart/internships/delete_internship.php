<?php


require_once '../config/db.php';
session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to delete internship']);
    exit;
}

$internship_id = $_POST['id'] ?? $_GET['id'] ?? '';

if (empty($internship_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Internship ID required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // First check if user owns this internship (optional security)
    $check_stmt = $db->prepare("SELECT posted_by FROM internships WHERE id = ?");
    $check_stmt->execute([$internship_id]);
    $internship = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($internship && $internship['posted_by'] != $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You can only delete your own internships']);
        exit;
    }
    
    // Delete internship
    $stmt = $db->prepare("DELETE FROM internships WHERE id = ?");
    $result = $stmt->execute([$internship_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Internship deleted successfully'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Internship not found or already deleted']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
