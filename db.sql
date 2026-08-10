-- AUCA STUDENT PORTAL DATABASE
CREATE DATABASE IF NOT EXISTS auca_portal;
USE auca_portal;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','teacher','admin') DEFAULT 'student',
  contact VARCHAR(30), address VARCHAR(200), dob DATE,
  occupation VARCHAR(60), civil_status VARCHAR(30),
  gender VARCHAR(20), religion VARCHAR(60), bio TEXT,
  status ENUM('active','disabled') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL, name VARCHAR(120) NOT NULL, credits INT DEFAULT 3
);

CREATE TABLE groups_table (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT, name VARCHAR(50), teacher_id INT,
  schedule VARCHAR(100), capacity INT DEFAULT 30,
  FOREIGN KEY (course_id) REFERENCES courses(id),
  FOREIGN KEY (teacher_id) REFERENCES users(id)
);

CREATE TABLE enrollments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT, group_id INT, status VARCHAR(20) DEFAULT 'active',
  enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES users(id),
  FOREIGN KEY (group_id) REFERENCES groups_table(id)
);

CREATE TABLE results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT, course_id INT, assessment VARCHAR(60),
  score DECIMAL(5,2), grade VARCHAR(5),
  FOREIGN KEY (student_id) REFERENCES users(id),
  FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_id INT, student_id INT, att_date DATE, status VARCHAR(20),
  FOREIGN KEY (group_id) REFERENCES groups_table(id),
  FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE fees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT, description VARCHAR(120), amount DECIMAL(10,2),
  paid TINYINT DEFAULT 0, due_date DATE,
  FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fee_id INT, student_id INT, amount DECIMAL(10,2),
  method VARCHAR(30), paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fee_id) REFERENCES fees(id)
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT, receiver_id INT, subject VARCHAR(120), body TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- default admin: email admin@auca.ac.rw / password Admin123
INSERT INTO users (full_name,email,password,role) VALUES
('System Admin','admin@auca.ac.rw','$2b$12$qhZ9pssIyHOjQPuRNFoareS2P5qwoyzMDUzuYsZ6GaIEJboWBzCtu','admin');
