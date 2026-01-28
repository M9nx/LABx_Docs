<?php
// Lab 18: IDOR Expire Other User Sessions
// Database Configuration
// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'ac_lab18';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Please run setup_db.php first. Error: " . $e->getMessage());
}

// Helper function to generate session token
function generateSessionToken() {
    return bin2hex(random_bytes(32));
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['session_token']);
}

// Helper function to get current user
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    // Verify session is still valid in database
    $stmt = $pdo->prepare("SELECT us.*, u.username, u.email, u.role, u.store_name 
                           FROM user_sessions us 
                           JOIN users u ON us.user_id = u.id 
                           WHERE us.user_id = ? AND us.session_token = ? AND us.is_active = 1");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['session_token']]);
    return $stmt->fetch();
}

// Helper function to validate session
function validateSession() {
    $user = getCurrentUser();
    if (!$user) {
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    return $user;
}
?>
