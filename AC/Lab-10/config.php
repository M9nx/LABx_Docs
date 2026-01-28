<?php
// Database Configuration for Lab 10
// URL-based Access Control Bypass via X-Original-URL Header
// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'ac_lab10';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

// Create connection
$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

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
