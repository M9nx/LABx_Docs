<?php
// Database configuration for Lab 9 - Chat Log IDOR
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'ac_lab9';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // If database doesn't exist, show setup message
    $conn_check = new mysqli($db_host, $db_user, $db_pass);
    if ($conn_check->connect_error) {
        die("Database connection failed. Please check your MySQL server.");
    }
    $conn_check->close();
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>