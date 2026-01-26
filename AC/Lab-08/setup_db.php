<?php
// Reset lab progress
require_once '../progress.php';
resetLab(8);

// Database setup script for Lab 8
$host = 'localhost';
$user = 'root';
$pass = 'root';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS lab8_password");
$conn->select_db("lab8_password");

// Drop existing table
$conn->query("DROP TABLE IF EXISTS users");

// Create users table
$sql = "CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Insert sample users
$users = [
    ['administrator', 'x4dm1n_s3cr3t_p@ss!', 'admin@passlab.local', 'admin'],
    ['wiener', 'peter', 'wiener@passlab.local', 'user'],
    ['carlos', 'montoya', 'carlos@passlab.local', 'user'],
    ['alice', 'wonderland123', 'alice@passlab.local', 'user'],
    ['bob', 'builder456', 'bob@passlab.local', 'user']
];

foreach ($users as $u) {
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $u[0], $u[1], $u[2], $u[3]);
    $stmt->execute();
}

$conn->close();
header("Location: index.php?setup=success");
exit;
?>
