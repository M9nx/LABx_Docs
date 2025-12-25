<?php
// Lab 18: IDOR Expire Other User Sessions
// Database Configuration

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'ac_lab18';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Please run setup_db.php first. Error: " . $e->getMessage());
}

// Session configuration - use database sessions
session_start();

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
