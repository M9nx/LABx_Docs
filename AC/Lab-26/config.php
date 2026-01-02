<?php
/**
 * Lab 26: IDOR in API Applications - Configuration
 * Pressable-style API credential leak vulnerability
 */

session_start();

// Database configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = 'root';
$dbName = 'ac_lab26';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Database connection failed. Please run database_setup.sql first. Error: " . $e->getMessage());
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Require login - redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Get current user data
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate random client ID
 */
function generateClientId() {
    return 'cli_' . bin2hex(random_bytes(12));
}

/**
 * Generate random client secret
 */
function generateClientSecret() {
    return 'sec_' . bin2hex(random_bytes(24));
}

/**
 * Log activity
 */
function logActivity($pdo, $userId, $action, $targetType, $targetId, $details = '') {
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (user_id, action, target_type, target_id, details, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $action, $targetType, $targetId, $details, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
}

/**
 * VULNERABLE FUNCTION - Get application by ID without ownership check
 * This is intentionally vulnerable for the lab!
 */
function getApplicationById($pdo, $appId) {
    // VULNERABILITY: No user ownership verification!
    $stmt = $pdo->prepare("SELECT * FROM api_applications WHERE id = ?");
    $stmt->execute([$appId]);
    return $stmt->fetch();
}

/**
 * Get user's applications (secure - for listing own apps)
 */
function getUserApplications($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM api_applications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Mask client secret for display
 */
function maskSecret($secret) {
    if (strlen($secret) <= 8) return '********';
    return substr($secret, 0, 8) . str_repeat('*', strlen($secret) - 12) . substr($secret, -4);
}

/**
 * Format date for display
 */
function formatDate($date) {
    return date('M j, Y g:i A', strtotime($date));
}

// Make CSRF token available
$csrfToken = generateCSRFToken();
