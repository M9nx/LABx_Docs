<?php
// Reset lab progress
require_once '../progress.php';
resetLab(7);

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS lab7_redirect");
$conn->select_db("lab7_redirect");

// Drop existing table
$conn->query("DROP TABLE IF EXISTS users");

// Create users table
$sql = "CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    api_key VARCHAR(64) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Insert sample users
$users = [
    ['administrator', 'admin123', 'admin@redirectlab.local', 'ADMIN-KEY-a9f8e7d6c5b4a3210fedcba987654321', 'admin'],
    ['wiener', 'peter', 'wiener@redirectlab.local', 'USER-KEY-wiener-1234567890abcdef', 'user'],
    ['carlos', 'montoya', 'carlos@redirectlab.local', 'API-KEY-carlos-Xt7Kp9Qm2Wn5Bv8J', 'user'],
    ['alice', 'password123', 'alice@redirectlab.local', 'API-KEY-alice-Hj3Lm6Yn9Rp2Dk5F', 'user'],
    ['bob', 'secret456', 'bob@redirectlab.local', 'API-KEY-bob-Zw8Qc1Vx4Bt7Ng0K', 'user']
];

foreach ($users as $u) {
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, api_key, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $u[0], $u[1], $u[2], $u[3], $u[4]);
    $stmt->execute();
}

$conn->close();
header("Location: index.php?setup=success");
exit;
?>
