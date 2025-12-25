<?php
// Database configuration for Lab 13 - Referer-based Access Control
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'ac_lab13';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
$conn->select_db($db_name);

// Set charset
$conn->set_charset("utf8mb4");
?>
