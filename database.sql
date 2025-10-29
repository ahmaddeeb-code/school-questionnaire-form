CREATE DATABASE IF NOT EXISTS school_forms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_forms;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO roles (name) VALUES ('admin'), ('employee'), ('family');

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (role, name, email, password_hash) VALUES
('admin', 'System Admin', 'admin@example.com', '$2y$12$QBau83jLhy/3MdSTKxfVDOSyovPl5rCjgM3gibDFQPqwfjCISxgTa'),
('employee', 'Counselor Emma', 'emma@example.com', '$2y$12$KcI3DWpoXCC7ad6cuxoeiuF.lOvS/iTiXqGZGLf/eSI5jCq9nNdNG'),
('family', 'Parent Omar', 'omar@example.com', '$2y$12$062L7a/cQrIOfbbwbrMbQ.m7O7fyKZwuhROiwakbEv1BhL.7IG7ga');

CREATE TABLE audiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('grade','class','department','group') NOT NULL,
    name VARCHAR(100) NOT NULL
);

INSERT INTO audiences (type, name) VALUES
('grade', 'Grade 6 Families'),
('grade', 'Grade 7 Families'),
('department', 'Science Department'),
('group', 'All Employees');

CREATE TABLE forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('draft','published') DEFAULT 'draft',
    allow_anonymous TINYINT(1) DEFAULT 0,
    allow_edits TINYINT(1) DEFAULT 0,
    target_audience ENUM('families','employees','both') DEFAULT 'families',
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    text TEXT NOT NULL,
    description TEXT,
    required TINYINT(1) DEFAULT 0,
    settings JSON,
    position INT DEFAULT 0,
    FOREIGN KEY (form_id) REFERENCES forms(id)
);

CREATE TABLE question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    label VARCHAR(255) NOT NULL,
    value VARCHAR(255) NOT NULL,
    position INT DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

CREATE TABLE form_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    audience_id INT NOT NULL,
    scheduled_at DATETIME,
    FOREIGN KEY (form_id) REFERENCES forms(id),
    FOREIGN KEY (audience_id) REFERENCES audiences(id)
);

CREATE TABLE access_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    recipient_id INT NOT NULL,
    token VARCHAR(128) NOT NULL,
    expires_at DATETIME,
    used_at DATETIME NULL,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY token_unique (token),
    FOREIGN KEY (form_id) REFERENCES forms(id)
);

CREATE TABLE responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    recipient_id INT,
    token_id INT NOT NULL,
    submitted_at DATETIME,
    duration_seconds INT,
    is_complete TINYINT(1) DEFAULT 0,
    FOREIGN KEY (form_id) REFERENCES forms(id),
    FOREIGN KEY (token_id) REFERENCES access_tokens(id)
);

CREATE TABLE response_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_text TEXT,
    answer_option_id INT NULL,
    answer_numeric DECIMAL(10,2) NULL,
    answer_date DATE NULL,
    answer_file VARCHAR(255) NULL,
    FOREIGN KEY (response_id) REFERENCES responses(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id INT NOT NULL,
    question_id INT NOT NULL,
    filename_original VARCHAR(255) NOT NULL,
    filename_stored VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) NOT NULL,
    size INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (response_id) REFERENCES responses(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Sample forms
INSERT INTO forms (owner_id, title, description, status, allow_anonymous, allow_edits, target_audience, settings)
VALUES
(1, 'School Climate Survey', 'Gauge family satisfaction with school climate.', 'published', 1, 0, 'families', JSON_OBJECT('grade_filters', JSON_ARRAY('Grade 6 Families'), 'department_filters', JSON_ARRAY())),
(2, 'Employee Wellness Check', 'Quick pulse check for staff wellbeing.', 'published', 0, 1, 'employees', JSON_OBJECT('department_filters', JSON_ARRAY('Science Department')));

INSERT INTO questions (form_id, type, text, description, required, settings, position) VALUES
(1, 'short_text', 'What do you appreciate most about the school?', 'Optional comment', 0, JSON_OBJECT('placeholder','Share your thoughts'), 1),
(1, 'linear_scale', 'Rate the overall communication from the school.', '1=Poor, 5=Excellent', 1, JSON_OBJECT('min',1,'max',5), 2),
(1, 'multiple_choice', 'How satisfied are you with the learning environment?', '', 1, JSON_OBJECT('logic', JSON_OBJECT()), 3),
(1, 'checkbox', 'Which channels do you prefer for updates?', '', 0, JSON_OBJECT(), 4),
(1, 'date', 'When did you last attend a school event?', '', 0, JSON_OBJECT(), 5),
(2, 'linear_scale', 'How would you rate your current workload?', '', 1, JSON_OBJECT('min',1,'max',5), 1),
(2, 'long_text', 'What support would help you succeed?', '', 0, JSON_OBJECT(), 2),
(2, 'multiple_choice', 'Do you feel recognized for your work?', '', 1, JSON_OBJECT(), 3);

INSERT INTO question_options (question_id, label, value, position) VALUES
(3, 'Very satisfied', 'very_satisfied', 1),
(3, 'Satisfied', 'satisfied', 2),
(3, 'Neutral', 'neutral', 3),
(3, 'Dissatisfied', 'dissatisfied', 4),
(4, 'Email', 'email', 1),
(4, 'SMS', 'sms', 2),
(4, 'Mobile App', 'app', 3),
(8, 'Yes', 'yes', 1),
(8, 'Sometimes', 'sometimes', 2),
(8, 'No', 'no', 3);

INSERT INTO form_assignments (form_id, audience_id, scheduled_at) VALUES
(1, 1, NOW()),
(1, 2, NOW()),
(2, 3, NOW()),
(2, 4, NOW());

-- Generate tokens for testing
INSERT INTO access_tokens (form_id, recipient_id, token, expires_at, attempts)
VALUES
(1, 1, 'FAKEFAMILYTOKEN123', DATE_ADD(NOW(), INTERVAL 7 DAY), 0),
(2, 4, 'FAKEEMPLOYEETOKEN456', DATE_ADD(NOW(), INTERVAL 7 DAY), 0);
