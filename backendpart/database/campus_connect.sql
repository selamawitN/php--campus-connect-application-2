
CREATE DATABASE IF NOT EXISTS campus_connect;
USE campus_connect;

CREATE TABLE mentorship_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mentee_name VARCHAR(100) NOT NULL,
    mentor_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS internships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    company VARCHAR(100) NOT NULL,
    description TEXT,
    location VARCHAR(200),
    stipend VARCHAR(100),
    stipend_type ENUM('paid', 'unpaid') DEFAULT 'unpaid',
    duration VARCHAR(50),
    deadline DATE,
    requirements TEXT,
    year_requirement VARCHAR(50) COMMENT 'e.g., "3,4,5" or "4,5"',
    work_type ENUM('remote', 'on-site', 'hybrid') DEFAULT 'on-site',
    posted_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
);

