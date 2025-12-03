<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3: User role controlled by request parameter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
        }

        .lab-title {
            color: #ff4444;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .lab-subtitle {
            color: #cccccc;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }

        .difficulty-badge {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
        }

        .description-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #ff6666;
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .vulnerability-info {
            background: linear-gradient(45deg, rgba(255, 193, 7, 0.1), rgba(255, 152, 0, 0.1));
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .vulnerability-info h4 {
            color: #ffd54f;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .vulnerability-info p {
            color: #fff3cd;
            line-height: 1.6;
            margin: 10px 0;
        }

        .setup-steps {
            background: rgba(0, 123, 255, 0.1);
            border: 1px solid #007bff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .setup-steps h4 {
            color: #66ccff;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .step-list {
            list-style: none;
            padding: 0;
            counter-reset: step-counter;
        }

        .step-list li {
            counter-increment: step-counter;
            background: rgba(0, 0, 0, 0.2);
            margin: 10px 0;
            padding: 15px 20px;
            border-left: 4px solid #007bff;
            border-radius: 5px;
            position: relative;
        }

        .step-list li:before {
            content: counter(step-counter);
            position: absolute;
            left: -15px;
            top: 50%;
            transform: translateY(-50%);
            background: #007bff;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .credentials-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px dashed #666;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }

        .credentials-box h4 {
            color: #ff9999;
            margin-bottom: 15px;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #333;
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .username {
            color: #66ff66;
            font-weight: bold;
        }

        .password {
            color: #ffff66;
            font-weight: bold;
        }

        .objective-list {
            list-style: none;
            padding: 0;
        }

        .objective-list li {
            background: rgba(255, 68, 68, 0.1);
            margin: 10px 0;
            padding: 15px 20px;
            border-left: 4px solid #ff4444;
            border-radius: 5px;
            position: relative;
        }

        .objective-list li:before {
            content: "🎯";
            margin-right: 10px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
            border-color: #17a2b8;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
        }

        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.1);
        }

        .btn-info:hover {
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-link {
            color: #ff6666;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 102, 102, 0.1);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .lab-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="lab-title">Lab 3</h1>
            <p class="lab-subtitle">User role controlled by request parameter</p>
            <div class="difficulty-badge">Apprentice</div>
        </div>

        <div class="description-card">
            <h2 class="section-title">🎯 Lab Objectives</h2>
            <ul class="objective-list">
                <li>Understand how client-side role parameters can be manipulated</li>
                <li>Exploit cookie-based access control vulnerabilities</li>
                <li>Gain unauthorized admin access by modifying browser cookies</li>
                <li>Delete a user account using elevated privileges</li>
            </ul>
        </div>

        <div class="vulnerability-info">
            <h4>🔐 Vulnerability Overview</h4>
            <p>
                This lab contains an access control vulnerability where user roles are determined by a client-side parameter.
                The application relies on an "Admin" cookie to determine whether a user has administrative privileges,
                without proper server-side validation.
            </p>
            <p>
                <strong>Attack Vector:</strong> Cookie manipulation to elevate privileges from regular user to administrator.
            </p>
        </div>

        <div class="setup-steps">
            <h4>🛠️ Setup Instructions</h4>
            <ol class="step-list">
                <li>Click "Setup Database" to open phpMyAdmin and create the lab database</li>
                <li>Execute the provided SQL script to create users table and sample data</li>
                <li>Return to this page and click "Access Lab" to start the exercise</li>
                <li>Follow the exploitation steps to complete the lab</li>
            </ol>
        </div>

        <div class="credentials-box">
            <h4>🔑 Test Credentials</h4>
            <div class="credential-item">
                <span class="username">wiener</span>
                <span class="password">peter</span>
            </div>
            <div class="credential-item">
                <span class="username">carlos</span>
                <span class="password">secret</span>
            </div>
            <div class="credential-item">
                <span class="username">administrator</span>
                <span class="password">admin123</span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="http://localhost/phpmyadmin/" target="_blank" class="btn btn-primary">
                🗄️ Setup Database
            </a>
            <a href="index.php" class="btn btn-info">
                🚀 Access Lab
            </a>
            <a href="docs.php" class="btn btn-secondary">
                📚 View Documentation
            </a>
        </div>

        <div class="description-card">
            <h2 class="section-title">📋 Database Setup SQL</h2>
            <p style="color: #cccccc; margin-bottom: 15px;">Copy and paste this SQL script in phpMyAdmin:</p>
            <div style="background: #1a1a1a; border: 1px solid #444; border-radius: 8px; padding: 20px; overflow-x: auto; font-family: 'Courier New', monospace; color: #f8f8f2;">
                <pre style="margin: 0; white-space: pre-wrap;">-- Lab 3 Database Schema
CREATE DATABASE IF NOT EXISTS lab3_db;
USE lab3_db;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    department VARCHAR(50) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users (passwords are hashed with password_hash())
INSERT INTO users (username, password, email, full_name, role, department) VALUES
-- Test user credentials: wiener/peter
('wiener', '$2y$10$E4.qXjbGqJ9ZK7VDtf8KLOVLsVQHGlGUXQOYjW1vNqTQk.9GBa7mG', 'wiener@example.com', 'Wiener User', 'user', 'Quality Assurance'),

-- Administrator credentials: administrator/admin123
('administrator', '$2y$10$LLRuqCJLF8fLBBfGGGQGOu3E8wQf8UQz8.J7VhOZfVeH8hFgKgN5O', 'admin@company.com', 'System Administrator', 'admin', 'IT Security'),

-- Regular user credentials: carlos/secret
('carlos', '$2y$10$N2.OJVxF8KJ8KbWTxWfFyOQGQ3c8q8fz9GH3Kz7.L8d6cE4.aB2OG', 'carlos@example.com', 'Carlos Rodriguez', 'user', 'Marketing'),

-- Additional test users
('alice', '$2y$10$8.3f3C9q2A1kJfDGHhGJBu7r6f8g1f9E3qWrTyUqWE4d5cF6gH1.B', 'alice@company.com', 'Alice Johnson', 'user', 'Human Resources'),
('bob', '$2y$10$F9.2g4B8r5C6dEgHJhBKCv2s7g9h2e0D4pQwRtYuIE5e6dF7gI1.A', 'bob@company.com', 'Bob Wilson', 'user', 'Finance'),
('eve', '$2y$10$G1.4h5C9s6D7eHjKkCLDx3t8h0i3f1E5qXyStZvJF6f7eG8hJ2.C', 'eve@company.com', 'Eve Thompson', 'user', 'Research');

-- Display the created users
SELECT 'Database setup completed successfully!' as status;
SELECT id, username, email, full_name, role, department FROM users ORDER BY id;</pre>
            </div>
        </div>

        <div class="navigation">
            <a href="../lab2/" class="nav-link">← Lab 2</a>
            <a href="../" class="nav-link">🏠 AC Labs Home</a>
            <a href="#" class="nav-link" style="color: #666; border-color: #666;">Lab 4 →</a>
        </div>
    </div>
</body>
</html>