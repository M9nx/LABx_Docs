<?php
// Lab 29: LinkedPro Newsletter Platform - Configuration
// Database connection settings
// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'ac_lab29';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

// Create connection
$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "<br>Please run setup_db.php first.");
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Helper function to get current user
function getCurrentUser($conn) {
    if (!isLoggedIn()) return null;
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Helper function to format URN for display
function formatUrn($urn) {
    return str_replace('fsd_contentSeries:', '', $urn);
}

// Helper function to log activity
function logActivity($conn, $user_id, $action, $target_type, $target_id, $details = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, target_type, target_id, ip_address, details) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $action, $target_type, $target_id, $ip, $details);
    $stmt->execute();
}

// Platform colors (LinkedIn-inspired blue theme)
$theme = [
    'primary' => '#0a66c2',
    'primary_dark' => '#004182',
    'secondary' => '#057642',
    'background' => '#f3f2ef',
    'card' => '#ffffff',
    'text' => '#000000',
    'text_secondary' => '#666666'
];
?>
