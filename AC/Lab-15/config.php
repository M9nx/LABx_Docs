<?php
// Database configuration for Lab 15 - IDOR PII Leakage
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'ac_lab15';

// Create database if it doesn't exist
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
