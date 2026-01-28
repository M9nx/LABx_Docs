<?php
// Lab 23: IDOR on AddTagToAssets - Configuration

// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
define('DB_HOST', $creds['host']);
define('DB_USER', $creds['user']);
define('DB_PASS', $creds['pass']);
define('DB_NAME', 'ac_lab23');

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

// Application settings
define('APP_NAME', 'TagScope');
define('APP_VERSION', '2.4.1');
define('LAB_NUMBER', 23);

// Theme colors (Indigo/Purple for security platform theme)
define('PRIMARY_COLOR', '#6366f1');
define('SECONDARY_COLOR', '#8b5cf6');
define('ACCENT_COLOR', '#a78bfa');
define('DARK_BG', '#0f172a');

// Tag ID format (mimics HackerOne's gid://hackerone/AsmTag/XXXXXXXX)
define('TAG_GID_PREFIX', 'gid://tagscope/AsmTag/');

// Database connection
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

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Get current user
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Escape HTML output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// JSON response helper
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

// Encode tag ID to base64 (mimics HackerOne format)
// Format: gid://tagscope/AsmTag/INTERNAL_ID -> base64
function encodeTagId($internalId) {
    $gid = TAG_GID_PREFIX . $internalId;
    return base64_encode($gid);
}

// Decode tag ID from base64
function decodeTagId($encodedTagId) {
    $decoded = base64_decode($encodedTagId);
    if (strpos($decoded, TAG_GID_PREFIX) === 0) {
        return (int) substr($decoded, strlen(TAG_GID_PREFIX));
    }
    return null;
}

// Get tag by internal ID
function getTagByInternalId($internalId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE internal_id = ?");
        $stmt->execute([$internalId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Log activity
function logActivity($userId, $action, $targetType = null, $targetId = null, $details = null) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, target_type, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $targetType, $targetId, $details, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    } catch (PDOException $e) {
        // Silently fail
    }
}

// Generate unique ID
function generateId($prefix = 'ID') {
    return $prefix . '_' . bin2hex(random_bytes(8));
}

// Generate sequential internal tag ID (for new tags)
function generateInternalTagId() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT MAX(internal_id) as max_id FROM tags");
        $result = $stmt->fetch();
        return ($result['max_id'] ?? 49790000) + 1;
    } catch (PDOException $e) {
        return rand(49790000, 49799999);
    }
}

// Get risk level badge class
function getRiskBadgeClass($level) {
    $classes = [
        'critical' => 'risk-critical',
        'high' => 'risk-high',
        'medium' => 'risk-medium',
        'low' => 'risk-low',
        'info' => 'risk-info'
    ];
    return $classes[$level] ?? 'risk-info';
}
?>
