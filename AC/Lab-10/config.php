<?php
// Database Configuration for Lab 10
// URL-based Access Control Bypass via X-Original-URL Header

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'ac_lab10';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // Don't expose connection details in production
    die("Connection failed. Please run setup_db.php first.");
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
