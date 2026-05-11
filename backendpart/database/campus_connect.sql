
CREATE DATABASE IF NOT EXISTS campus_connect;
USE campus_connect;

cREATE TABLE IF NOT EXISTS users (
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


CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    full_name VARCHAR(100),
    year VARCHAR(20),
    department VARCHAR(100),
    material_type VARCHAR(50),
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    downloads_count INT DEFAULT 0,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- DEPARTMENTS (core table)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    head VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(30),
    office_location VARCHAR(100),
    established_year YEAR,
    total_students INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- COURSES (belongs to a department)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    course_name VARCHAR(200) NOT NULL,
    credit_hours INT NOT NULL DEFAULT 3,
    prerequisites TEXT,
    year INT NOT NULL,
    semester INT NOT NULL,
    field VARCHAR(100) NOT NULL,
    description TEXT,
    is_elective BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_code_dept (course_code, department_id),
    INDEX idx_dept (department_id),
    INDEX idx_year_sem (year, semester),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- DEPARTMENT ANNOUNCEMENTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS department_announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    posted_by INT NOT NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_dept (department_id),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- DEPARTMENT AUDIT LOG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS department_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    department_id INT,
    action ENUM('CREATE','UPDATE','DELETE','VIEW') NOT NULL,
    performed_by INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dept (department_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO departments (name, code, description, head, email, phone, office_location, established_year, total_students) VALUES
('Software Engineering','SE','Focuses on software development, system design, AI, and modern computing technologies.','Dr. Abebe Tadesse','se@university.edu.et','+251-911-001','Block A, Room 101',2010,320),
('Computer Science','CS','Covers algorithms, data structures, networks, and theoretical computing.','Dr. Meron Haile','cs@university.edu.et','+251-911-002','Block A, Room 102',2008,280),
('Electrical Engineering','EE','Power systems, electronics, control systems and telecommunications.','Dr. Solomon Girma','ee@university.edu.et','+251-911-003','Block B, Room 201',2005,250),
('Civil Engineering','CE','Structural, geotechnical, hydraulics and construction management.','Dr. Tigist Bekele','ce@university.edu.et','+251-911-004','Block C, Room 301',2003,300),
('Mechanical Engineering','ME','Thermodynamics, fluid mechanics, manufacturing and design.','Dr. Dawit Lemma','me@university.edu.et','+251-911-005','Block D, Room 401',2003,270);

-- ────────────────────────────────────────────────────────────
-- SEED – Courses (SE dept = id 1)
-- ────────────────────────────────────────────────────────────
INSERT IGNORE INTO courses (department_id,course_code,course_name,credit_hours,prerequisites,year,semester,field) VALUES
(1,'MATH101','Mathematics for Natural Science',3,'None',1,1,'Freshman (Common Courses)'),
(1,'PSYC101','General Psychology',3,'None',1,1,'Freshman (Common Courses)'),
(1,'ENGL101','Communicative English Skills I',3,'None',1,1,'Freshman (Common Courses)'),
(1,'PHYS101','General Physics',3,'None',1,1,'Freshman (Common Courses)'),
(1,'LOGI101','Logic & Critical Thinking',3,'None',1,1,'Freshman (Common Courses)'),
(1,'GEOG101','Geography of Ethiopia and the Horn',3,'None',1,1,'Freshman (Common Courses)'),
(1,'PE101','Physical Fitness',0,'None',1,1,'Freshman (Common Courses)'),
(1,'ETEG102','Emerging Technology for Engineers',3,'None',1,2,'Freshman (Common Courses)'),
(1,'ENTR102','Entrepreneurship for Engineers',3,'None',1,2,'Freshman (Common Courses)'),
(1,'ENGL102','Communicative English Skills II',3,'ENGL101',1,2,'Freshman (Common Courses)'),
(1,'CIVE102','Civic & Ethical Education',2,'None',1,2,'Freshman (Common Courses)'),
(1,'MATH102','Applied Mathematics I',4,'MATH101',1,2,'Freshman (Common Courses)'),
(1,'ANTH102','Social Anthropology',2,'None',1,2,'Freshman (Common Courses)'),
(1,'INCL102','Inclusiveness',2,'None',1,2,'Freshman (Common Courses)'),
(1,'HIST211','History of Ethiopia and the Horn',3,'None',2,1,'Software Engineering'),
(1,'SE2111','Fundamentals of Programming I',3,'None',2,1,'Software Engineering'),
(1,'MATH211','Discrete Mathematics',3,'MATH102',2,1,'Software Engineering'),
(1,'SE2112','Intro to Software Engineering and Computing',4,'None',2,1,'Software Engineering'),
(1,'GTRN211','Global Trends',2,'None',2,1,'Software Engineering'),
(1,'ECON211','Economics',3,'None',2,1,'Software Engineering'),
(1,'SE2121','Fundamentals of Programming II',3,'SE2111',2,2,'Software Engineering'),
(1,'SE2122','Object Oriented Programming',4,'SE2111',2,2,'Software Engineering'),
(1,'MATH221','Applied Mathematics II',4,'MATH211',2,2,'Software Engineering'),
(1,'SE2123','Digital Logic Design',3,'None',2,2,'Software Engineering'),
(1,'SE2124','Computer Organization',3,'SE2123',2,2,'Software Engineering'),
(1,'SE3111','Data Structures and Algorithms',4,'SE2121',3,1,'Software Engineering'),
(1,'SE3112','Database Systems',4,'SE2122',3,1,'Software Engineering'),
(1,'SE3113','Operating Systems',3,'SE2124',3,1,'Software Engineering'),
(1,'SE3114','Computer Networks',3,'None',3,1,'Software Engineering'),
(1,'SE3115','Software Requirements Engineering',3,'SE2112',3,1,'Software Engineering'),
(1,'SE3121','Software Design & Architecture',4,'SE3115',3,2,'Software Engineering'),
(1,'SE3122','Web Technologies',3,'SE2122',3,2,'Software Engineering'),
(1,'SE3123','Human Computer Interaction',3,'None',3,2,'Software Engineering'),
(1,'SE3124','Theory of Computation',3,'MATH211',3,2,'Software Engineering'),
(1,'SE4111','Software Testing & QA',3,'SE3121',4,1,'Software Engineering'),
(1,'SE4112','Artificial Intelligence',4,'SE3111',4,1,'Software Engineering'),
(1,'SE4113','Mobile Application Development',3,'SE3122',4,1,'Software Engineering'),
(1,'SE4114','Software Project Management',3,'None',4,1,'Software Engineering'),
(1,'SE4121','Machine Learning',4,'SE4112',4,2,'Software Engineering'),
(1,'SE4122','Cloud Computing',3,'SE3122',4,2,'Software Engineering'),
(1,'SE4123','Distributed Systems',3,'SE3113',4,2,'Software Engineering'),
(1,'SE4124','Senior Project I',4,'None',4,2,'Software Engineering'),
(1,'SE5111','Cybersecurity',3,'SE3114',5,1,'Software Engineering'),
(1,'SE5112','Big Data Analytics',3,'SE4121',5,1,'Software Engineering'),
(1,'SE5113','Senior Project II',6,'SE4124',5,1,'Software Engineering'),
(1,'SE5121','Internship / Industrial Attachment',6,'None',5,2,'Software Engineering'),
(1,'SE5122','Ethics in Computing',2,'None',5,2,'Software Engineering');

