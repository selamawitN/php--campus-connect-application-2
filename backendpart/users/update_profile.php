<?php


require_once '../../config/db.php'; 

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Simple CSRF check
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

$user_id = $_SESSION['user_id'];

$fullname = trim(htmlspecialchars($_POST['fullname'] ?? ''));
$department = trim(htmlspecialchars($_POST['department'] ?? ''));
$year = intval($_POST['year'] ?? 0);
$phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
$bio = trim(htmlspecialchars($_POST['bio'] ?? ''));

if (empty($fullname)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full name is required']);
    exit;
}

if ($year < 1 || $year > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid year of study']);
    exit;
}

try {
    global $conn;
    
    $sql = "UPDATE users SET fullname = ?, department = ?, year = ?, phone = ?, bio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $fullname, $department, $year, $phone, $bio, $user_id);
    
    if ($stmt->execute()) {
        // Update session
        $_SESSION['user_fullname'] = $fullname;
        
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        throw new Exception("Update failed");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
}
?>
