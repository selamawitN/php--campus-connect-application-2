<?php
require_once '../config/db.php'; 
session_start();
header('Content-Type: application/json');
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}
$input= json_decode(file_get_contents('php://input'), true);
if ($input) {
    $_POST = array_merge($_post, input);
}
if(!isset($_POST['csrf_token']) || !verfiyCSRFToken($_POST['csrf_torken'])){
    http_respose_code(403);
    echo json_encode(['success'=> false, 
                     'message'=> 'Invalid security token']);
    exit;
}
$email = trim(htmlspecialchars($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me'] && $_POST['remember_me'] === 'true');
if(eempty($email) || empty($password)){
    http_respose_code(400);
    echo json_encode(['success' => false,
                    'message' => 'Email and password required']);
    exit;
}
