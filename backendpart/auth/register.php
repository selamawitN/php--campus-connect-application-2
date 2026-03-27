
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

$fullname = Validation::sanitize($_POST['fullname'] ?? '');
$email = Validation::sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$student_id = Validation::sanitize($_POST['student_id'] ?? '');
$department = Validation::sanitize($_POST['department'] ?? '');
$year = intval($_POST['year'] ?? 0);
$phone = Validation::sanitize($_POST['phone'] ?? '');
$role = Validation::sanitize($_POST['role'] ?? 'student');

$errors = [];

if (empty($fullname)) $errors[] = "Full name is required";
if (empty($email)) $errors[] = "Email is required";
if (empty($password)) $errors[] = "Password is required";
if (empty($student_id)) $errors[] = "Student ID is required";

if (!empty($email) && !Validation::validateEmail($email)) {
    $errors[] = "Invalid email format";
}
if (!empty($email) && !Validation::validateAASTUEmail($email)) {
    $errors[] = "Please use your AASTU email address (@aastu.edu.et)";
}

if (!empty($password) && !Validation::validatePassword($password)) {
    $errors[] = "Password must be at least 8 characters and contain uppercase, lowercase, and numbers";
}

if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match";
}

if ($year < 1 || $year > 5) {
    $errors[] = "Invalid year of study (1-5)";
}

if (!empty($phone) && !Validation::validatePhone($phone)) {
    $errors[] = "Invalid phone number format (must be 09xxxxxxxx or 07xxxxxxxx)";
}

if (!in_array($role, [ROLE_STUDENT, ROLE_MENTOR])) {
    $role = ROLE_STUDENT;
}

if (!empty($errors)) {
    Response::error('Validation failed', 400, $errors);
}

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        Response::error('Email already registered', 409);
    }
    
    $stmt = $db->prepare("SELECT id FROM users WHERE student_id = ?");
    $stmt->execute([$student_id]);
    if ($stmt->rowCount() > 0) {
        Response::error('Student ID already registered', 409);
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (fullname, email, password, student_id, department, year, phone, role, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([$fullname, $email, $hashed_password, $student_id, $department, $year, $phone, $role]);
    
    if ($result) {
        $user_id = $db->lastInsertId();
        
        if ($role === ROLE_MENTOR) {
            $mentorSql = "INSERT INTO mentors (user_id, expertise, available_for_mentoring) VALUES (?, ?, 1)";
            $mentorStmt = $db->prepare($mentorSql);
            $mentorStmt->execute([$user_id, '']);
        }
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_fullname'] = $fullname;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_student_id'] = $student_id;
        
        Response::success([
            'id' => $user_id,
            'fullname' => $fullname,
            'email' => $email,
            'student_id' => $student_id,
            'role' => $role
        ], 'Registration successful');
    } else {
        Response::error('Registration failed', 500);
    }
    
} catch (PDOException $e) {
    Response::error('Database error: ' . $e->getMessage(), 500);
}
?>
