<?php
// Lab 14: IDOR Banner Deletion Vulnerability
// Database Configuration

$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'ac_lab14';

// Create connection
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Set charset
$conn->set_charset("utf8mb4");
?>
