<?php
// Database configuration for Lab 15 - IDOR PII Leakage
// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$username = $creds['user'];
$password = $creds['pass'];
$dbname = 'ac_lab15';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

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
