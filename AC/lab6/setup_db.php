<?php
// Reset lab progress
require_once '../progress.php';
resetLab(6);

/**
 * Lab 6 Database Setup Script
 * Run this file in browser to create the database
 */

$host = 'localhost';
$user = 'root';
$pass = 'root';

echo "<h2>Lab 6: GUID-based IDOR - Database Setup</h2>";

try {
    // Connect without database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS lab6_guid");
    echo "<p>✓ Database 'lab6_guid' created</p>";
    
    // Use database
    $pdo->exec("USE lab6_guid");
    
    // Drop existing tables
    $pdo->exec("DROP TABLE IF EXISTS blog_posts");
    $pdo->exec("DROP TABLE IF EXISTS users");
    echo "<p>✓ Cleaned existing tables</p>";
    
    // Create users table
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guid VARCHAR(36) NOT NULL UNIQUE,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            api_key VARCHAR(64) NOT NULL,
            department VARCHAR(50),
            phone VARCHAR(20),
            address TEXT,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL
        )
    ");
    echo "<p>✓ Users table created</p>";
    
    // Create blog posts table
    $pdo->exec("
        CREATE TABLE blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_guid VARCHAR(36) NOT NULL,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_guid) REFERENCES users(guid) ON DELETE CASCADE
        )
    ");
    echo "<p>✓ Blog posts table created</p>";
    
    // Insert sample users
    $users = [
        ['a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d', 'administrator', 'admin123', 'admin@guidlab.local', 'System Administrator', 'sk-admin-9f8e7d6c5b4a3210-masterkey', 'IT Security', '+1-555-0100', '100 Admin Tower, Secure City, SC 10000', 'Master admin account.'],
        ['f47ac10b-58cc-4372-a567-0e02b2c3d479', 'carlos', 'carlos123', 'carlos@guidlab.local', 'Carlos Rodriguez', 'sk-carlos-x7y8z9a0b1c2d3e4-targetkey', 'Engineering', '+1-555-0201', '201 Engineering Ave, Tech District, TD 20100', 'Senior Engineer. API key grants access to internal systems.'],
        ['8d7e6f5a-4b3c-2d1e-0f9a-8b7c6d5e4f3a', 'wiener', 'peter', 'wiener@guidlab.local', 'Peter Wiener', 'sk-wiener-m1n2o3p4q5r6s7t8-userkey', 'Development', '+1-555-0301', '301 Developer Lane, Code City, CC 30100', 'Junior developer.'],
        ['c9d8e7f6-a5b4-3c2d-1e0f-9a8b7c6d5e4f', 'alice', 'alice123', 'alice@guidlab.local', 'Alice Johnson', 'sk-alice-u9v8w7x6y5z4a3b2-financekey', 'Finance', '+1-555-0401', '401 Finance Blvd, Money Town, MT 40100', 'Senior accountant.'],
        ['b8c7d6e5-f4a3-2b1c-0d9e-8f7a6b5c4d3e', 'bob', 'bob123', 'bob@guidlab.local', 'Bob Smith', 'sk-bob-j1k2l3m4n5o6p7q8-hrkey', 'Human Resources', '+1-555-0501', '501 HR Street, People City, PC 50100', 'HR manager.']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (guid, username, password, email, full_name, api_key, department, phone, address, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    echo "<p>✓ Sample users inserted (5 users)</p>";
    
    // Insert blog posts
    $posts = [
        ['f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Introduction to Secure API Design', 'In this post, I will discuss the fundamentals of designing secure APIs...'],
        ['f47ac10b-58cc-4372-a567-0e02b2c3d479', 'My Journey in Security Engineering', 'After 5 years working in security engineering, I have learned many lessons...'],
        ['a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d', 'Security Best Practices for 2024', 'As we approach a new year, it is important to review our security practices...'],
        ['c9d8e7f6-a5b4-3c2d-1e0f-9a8b7c6d5e4f', 'Financial Systems and Data Protection', 'Working in finance requires strict adherence to data protection...'],
        ['8d7e6f5a-4b3c-2d1e-0f9a-8b7c6d5e4f3a', 'Getting Started with Web Development', 'As a junior developer, I have been learning about web development...']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO blog_posts (user_guid, title, content) VALUES (?, ?, ?)");
    foreach ($posts as $post) {
        $stmt->execute($post);
    }
    echo "<p>✓ Blog posts inserted (5 posts)</p>";
    
    // Verify users
    $result = $pdo->query("SELECT guid, username, api_key FROM users ORDER BY id");
    echo "<h3>Users Created:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
    echo "<tr style='background: #333; color: #fff;'><th style='padding: 8px;'>GUID</th><th style='padding: 8px;'>Username</th><th style='padding: 8px;'>API Key</th></tr>";
    while ($row = $result->fetch()) {
        $highlight = ($row['username'] === 'carlos') ? 'background: #ff4444; color: white;' : '';
        echo "<tr style='$highlight'><td style='padding: 8px; font-family: monospace;'>{$row['guid']}</td><td style='padding: 8px;'>{$row['username']}</td><td style='padding: 8px; font-family: monospace;'>{$row['api_key']}</td></tr>";
    }
    echo "</table>";
    
    // Verify blog posts
    $result = $pdo->query("SELECT p.title, u.username, p.user_guid FROM blog_posts p JOIN users u ON p.user_guid = u.guid ORDER BY p.id");
    echo "<h3>Blog Posts Created:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr style='background: #333; color: #fff;'><th style='padding: 8px;'>Title</th><th style='padding: 8px;'>Author</th><th style='padding: 8px;'>Author GUID</th></tr>";
    while ($row = $result->fetch()) {
        $highlight = ($row['username'] === 'carlos') ? 'background: #ffaa00; color: black;' : '';
        echo "<tr style='$highlight'><td style='padding: 8px;'>{$row['title']}</td><td style='padding: 8px;'>{$row['username']}</td><td style='padding: 8px; font-family: monospace;'>{$row['user_guid']}</td></tr>";
    }
    echo "</table>";
    
    echo "<br><p style='color: green; font-weight: bold;'>✓ Lab 6 database setup complete!</p>";
    echo "<p style='color: #ff4444;'><strong>Target:</strong> Find carlos's GUID through his blog posts, then access his profile to get his API key.</p>";
    echo "<p><a href='index.php'>Go to Lab 6</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

