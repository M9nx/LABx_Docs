-- Lab 25: Notes IDOR on Personal Snippets
-- Based on GitLab HackerOne Report

DROP DATABASE IF EXISTS ac_lab25;
CREATE DATABASE ac_lab25;
USE ac_lab25;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Projects table (like GitLab projects)
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    path VARCHAR(100) NOT NULL,
    description TEXT,
    visibility ENUM('private', 'internal', 'public') DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Issues table (within projects)
CREATE TABLE issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    author_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Personal Snippets table (separate from projects)
CREATE TABLE personal_snippets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    filename VARCHAR(255),
    content TEXT,
    visibility ENUM('private', 'internal', 'public') DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Notes table (can be attached to issues OR snippets) - VULNERABLE DESIGN
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    noteable_type ENUM('issue', 'personal_snippet') NOT NULL,
    noteable_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
    -- Note: No foreign key constraint on noteable_id to allow polymorphic association
);

-- Activity log (shows user actions including snippet titles - INFORMATION LEAK)
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50),
    target_id INT,
    target_title VARCHAR(255), -- Stores the title of the target (LEAKS PRIVATE SNIPPET TITLES)
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert test users
INSERT INTO users (username, password, email, role) VALUES
('attacker', '$2y$10$YWRtaW4xMjM0NTY3ODkw', 'attacker@evil.com', 'user'),
('victim', '$2y$10$dXNlcjEyMzQ1Njc4OTAx', 'victim@company.com', 'user'),
('alice', '$2y$10$YWxpY2UxMjM0NTY3ODkw', 'alice@company.com', 'user'),
('admin', '$2y$10$YWRtaW5hZG1pbjEyMzQ1', 'admin@platform.com', 'admin');

-- Create attacker's project
INSERT INTO projects (owner_id, name, path, description, visibility) VALUES
(1, 'PrivateAttackerProject', 'attacker/privateattackerproject', 'Attacker private project for testing', 'private'),
(1, 'PublicAttackerProject', 'attacker/publicattackerproject', 'Attacker public project', 'public');

-- Create issues in attacker's project
INSERT INTO issues (project_id, author_id, title, description, status) VALUES
(1, 1, 'Attacker Issue', 'This is an issue in the attacker project. Comments here can be manipulated.', 'open'),
(1, 1, 'Another Test Issue', 'Testing issue functionality', 'open'),
(2, 1, 'Public Issue', 'Public project issue', 'open');

-- Create victim's PRIVATE personal snippets (targets for the attack)
INSERT INTO personal_snippets (owner_id, title, filename, content, visibility) VALUES
(2, 'SECRET_API_KEYS_DO_NOT_SHARE', 'api-keys.txt', 'AWS_KEY=AKIAIOSFODNN7EXAMPLE\nAWS_SECRET=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY\nSTRIPE_KEY=sk_live_51ABC123...', 'private'),
(2, 'Company Financial Report Q4 2024', 'financial-q4.md', '# Q4 Financial Summary\n\nRevenue: $45.2M\nProfit: $12.8M\nProjected Growth: 23%\n\nConfidential - Internal Only', 'private'),
(2, 'Employee Salary Database Export', 'salaries.csv', 'name,position,salary\nJohn Doe,CEO,450000\nJane Smith,CTO,380000\nBob Johnson,CFO,350000', 'private'),
(2, 'Production Database Credentials', 'db-creds.env', 'DB_HOST=prod-db.internal.company.com\nDB_USER=prod_admin\nDB_PASS=Sup3rS3cr3tPr0dP@ss!\nDB_NAME=production', 'private'),
(2, 'Upcoming Product Launch Plans', 'product-launch.md', '# Project Phoenix Launch\n\nLaunch Date: March 15, 2025\nBudget: $2.5M\nTarget: Enterprise customers', 'private');

-- Create alice's snippets
INSERT INTO personal_snippets (owner_id, title, filename, content, visibility) VALUES
(3, 'Public Code Snippet', 'hello.py', 'print("Hello, World!")', 'public'),
(3, 'My Private Notes', 'notes.txt', 'Personal notes about work projects', 'private');

-- Create admin's snippets
INSERT INTO personal_snippets (owner_id, title, filename, content, visibility) VALUES
(4, 'Admin Panel Access Codes', 'admin-codes.txt', 'Master Admin Code: ADMIN-2024-MASTER\nBackup Code: BACKUP-RECOVERY-KEY', 'private');

-- Add some legitimate notes to issues
INSERT INTO notes (author_id, noteable_type, noteable_id, content) VALUES
(1, 'issue', 1, 'Initial comment on my own issue'),
(1, 'issue', 1, 'Adding more details to the issue');

-- Add legitimate activity
INSERT INTO activity_log (user_id, action, target_type, target_id, target_title, details) VALUES
(1, 'created_project', 'project', 1, 'PrivateAttackerProject', 'Created new project'),
(1, 'created_issue', 'issue', 1, 'Attacker Issue', 'Created new issue'),
(2, 'created_snippet', 'personal_snippet', 1, 'SECRET_API_KEYS_DO_NOT_SHARE', 'Created private snippet');
