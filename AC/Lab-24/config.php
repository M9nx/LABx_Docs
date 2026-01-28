<?php
// Lab 24: IDOR Exposes All Machine Learning Models
// Configuration and Database Connection

// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
define('DB_HOST', $creds['host']);
define('DB_USER', $creds['user']);
define('DB_PASS', $creds['pass']);
define('DB_NAME', 'ac_lab24');

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

// Application settings
define('APP_NAME', 'MLRegistry');
define('APP_VERSION', '16.2.0');
define('LAB_NUMBER', 24);

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed. Please run setup_db.php first.");
    }
}

// Initialize database connection
try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    $pdo = null;
}

/**
 * Encode internal ID to GraphQL-style GID
 * Format: gid://gitlab/Ml::Model/{internal_id}
 */
function encodeModelGid($internalId) {
    return base64_encode("gid://gitlab/Ml::Model/{$internalId}");
}

/**
 * Decode GID to internal ID
 */
function decodeModelGid($encodedGid) {
    $decoded = base64_decode($encodedGid);
    if (preg_match('/Ml::Model\/(\d+)$/', $decoded, $matches)) {
        return (int)$matches[1];
    }
    return null;
}

/**
 * Encode model version to GID
 * Format: gid://gitlab/Ml::ModelVersion/{internal_id}
 */
function encodeVersionGid($internalId) {
    return base64_encode("gid://gitlab/Ml::ModelVersion/{$internalId}");
}

/**
 * Decode version GID to internal ID
 */
function decodeVersionGid($encodedGid) {
    $decoded = base64_decode($encodedGid);
    if (preg_match('/Ml::ModelVersion\/(\d+)$/', $decoded, $matches)) {
        return (int)$matches[1];
    }
    return null;
}

/**
 * Format GID for display (raw format)
 */
function formatGid($type, $internalId) {
    return "gid://gitlab/Ml::{$type}/{$internalId}";
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require login - redirect if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
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
 * Log activity
 */
function logActivity($pdo, $action, $resourceType = null, $resourceId = null, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, resource_type, resource_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $action,
            $resourceType,
            $resourceId,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        // Silently fail logging
    }
}

/**
 * Sanitize output for HTML
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format framework badge color
 */
function getFrameworkColor($framework) {
    $colors = [
        'TensorFlow' => '#FF6F00',
        'PyTorch' => '#EE4C2C',
        'scikit-learn' => '#F7931E',
        'XGBoost' => '#337AB7',
        'Transformers' => '#FFD21E',
        'Keras' => '#D00000'
    ];
    return $colors[$framework] ?? '#6b7280';
}

/**
 * Format visibility badge
 */
function getVisibilityBadge($visibility) {
    $badges = [
        'public' => '<span class="badge badge-green">🌐 Public</span>',
        'private' => '<span class="badge badge-red">🔒 Private</span>',
        'internal' => '<span class="badge badge-yellow">🔐 Internal</span>'
    ];
    return $badges[$visibility] ?? $badges['private'];
}
