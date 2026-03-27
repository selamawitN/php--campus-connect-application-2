<?php
require_once '../config/db.php';
require_once '../config/constants.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

if (!isset($_POST['csrf_token']) || !Validation::verifyCSRFToken($_POST['csrf_token'])) {
    Response::error('Invalid security token', 403);
}

$email = Validation::sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember_me = isset($_POST['remember_me']) && $_POST['remember_me'] === 'true';

$errors = [];

if (empty($email)) $errors[] = "Email is required";
if (empty($password)) $errors[] = "Password is required";
if (!empty($email) && !Validation::validateEmail($email)) {
    $errors[] = "Invalid email format";
}

if (!empty($errors)) {
    Response::error('Validation failed', 400, $errors);
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, fullname, email, password, student_id, department, year, phone, role, is_active FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 0) {
        Response::error('Invalid email or password', 401);
    }
    
    $user = $stmt->fetch();
    
    if (!password_verify($password, $user['password'])) {
        Response::error('Invalid email or password', 401);
    }
    
    if ($user['is_active'] != 1) {
        Response::error('Account is deactivated. Please contact administrator', 403);
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_fullname'] = $user['fullname'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_student_id'] = $user['student_id'];
    $_SESSION['login_time'] = time();
    
    if ($remember_me) {
        $token = Validation::generateToken();
        $expiry = date('Y-m-d H:i:s', time() + (86400 * 30));
        
        $updateStmt = $db->prepare("UPDATE users SET remember_token = ?, remember_token_expiry = ? WHERE id = ?");
        $updateStmt->execute([$token, $expiry, $user['id']]);
        
        setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
    }
    
    $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);
    
    Response::success([
        'id' => $user['id'],
        'fullname' => $user['fullname'],
        'email' => $user['email'],
        'student_id' => $user['student_id'],
        'department' => $user['department'],
        'year' => $user['year'],
        'role' => $user['role']
    ], 'Login successful');
    
} catch (PDOException $e) {
    Response::error('Database error: ' . $e->getMessage(), 500);
}
?>

