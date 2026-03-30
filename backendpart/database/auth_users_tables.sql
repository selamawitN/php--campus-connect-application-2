USE campus_connect;
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100),
    year INT DEFAULT 1,
    phone VARCHAR(15),
    bio TEXT,
    profile_picture VARCHAR(255) DEFAULT 'assets/images/default-avatar.png',
    role ENUM('student', 'mentor', 'admin') DEFAULT 'student',
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(255) NULL,
    remember_token_expiry DATETIME NULL,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_student_id (student_id),
    INDEX idx_role (role),
    INDEX idx_remember_token (remember_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS password_changes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO users (fullname, email, password, student_id, role) VALUES 
('Admin User', 'admin@aastu.edu.et', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN001', 'admin')
ON DUPLICATE KEY UPDATE id=id;
