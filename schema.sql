DROP DATABASE IF EXISTS student_portal;
CREATE DATABASE student_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_portal;

-- Admins table: store admin credentials (use PHP password_hash for passwords)
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    role ENUM('superadmin','admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students table: improved constraints, UTF8 support, and unique indexes
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    unique_student_number VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    gender VARCHAR(20),
    date_of_birth DATE,
    email VARCHAR(150) NOT NULL UNIQUE,
    address VARCHAR(255),
    contact_number VARCHAR(30),
    college VARCHAR(255),
    course VARCHAR(100),
    year_level VARCHAR(50),
    section VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications referencing students
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: create an admin account by generating a hash in PHP and inserting it:
-- php -r "echo password_hash('admin123', PASSWORD_DEFAULT).PHP_EOL;"
-- INSERT INTO admins (username, password, email, role) VALUES ('admin', '<PASSWORD_HASH>', 'admin@example.com', 'superadmin');

-- Colleges table
CREATE TABLE colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Programs / Courses table
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed colleges and programs
INSERT INTO colleges (name) VALUES
('College of Health and Allied Professions'),
('College of Accountancy, Business Education and Computer Studies'),
('College of Engineering'),
('College of Liberal Arts, Sciences and Education');

-- Programs for College of Health and Allied Professions
INSERT INTO programs (college_id, name) VALUES
((SELECT id FROM colleges WHERE name = 'College of Health and Allied Professions'), 'BS Nursing'),
((SELECT id FROM colleges WHERE name = 'College of Health and Allied Professions'), 'BS Pharmacy'),
((SELECT id FROM colleges WHERE name = 'College of Health and Allied Professions'), 'BS Medical Technology');

-- Programs for College of Accountancy, Business Education and Computer Studies
INSERT INTO programs (college_id, name) VALUES
((SELECT id FROM colleges WHERE name = 'College of Accountancy, Business Education and Computer Studies'), 'BS Tourism Management'),
((SELECT id FROM colleges WHERE name = 'College of Accountancy, Business Education and Computer Studies'), 'BS Hospitality Management'),
((SELECT id FROM colleges WHERE name = 'College of Accountancy, Business Education and Computer Studies'), 'BS Accounting Information System'),
((SELECT id FROM colleges WHERE name = 'College of Accountancy, Business Education and Computer Studies'), 'BS Information Technology'),
((SELECT id FROM colleges WHERE name = 'College of Accountancy, Business Education and Computer Studies'), 'BS Accountancy'),
((SELECT id FROM colleges WHERE name = 'College of Accountancy, Business Education and Computer Studies'), 'BS Business Administration');

-- Programs for College of Engineering
INSERT INTO programs (college_id, name) VALUES
((SELECT id FROM colleges WHERE name = 'College of Engineering'), 'BS Civil Engineering'),
((SELECT id FROM colleges WHERE name = 'College of Engineering'), 'BS Mechanical Engineering'),
((SELECT id FROM colleges WHERE name = 'College of Engineering'), 'BS Chemical Engineering');

-- Programs for College of Liberal Arts, Sciences and Education
INSERT INTO programs (college_id, name) VALUES
((SELECT id FROM colleges WHERE name = 'College of Liberal Arts, Sciences and Education'), 'BS Psychology'),
((SELECT id FROM colleges WHERE name = 'College of Liberal Arts, Sciences and Education'), 'BS Political Science'),
((SELECT id FROM colleges WHERE name = 'College of Liberal Arts, Sciences and Education'), 'BS Education');
