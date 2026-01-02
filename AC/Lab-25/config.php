<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'ac_lab25');

// Create database connection
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
} catch (PDOException $e) {
    // If database doesn't exist, redirect to setup
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        if (basename($_SERVER['PHP_SELF']) !== 'setup_db.php') {
            header('Location: setup_db.php');
            exit();
        }
    }
    $pdo = null;
}

// Simple password verification (for lab purposes)
function verifyPassword($input, $stored) {
    // For simplicity in this lab, we use a basic check
    $passwords = [
        'attacker' => 'attacker123',
        'victim' => 'victim123',
        'alice' => 'alice123',
        'admin' => 'admin123'
    ];
    
    global $pdo;
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE password = ?");
        $stmt->execute([$stored]);
        $user = $stmt->fetch();
        if ($user && isset($passwords[$user['username']])) {
            return $input === $passwords[$user['username']];
        }
    }
    return false;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Get current user
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn() || !$pdo) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Log activity (this is where snippet titles get leaked!)
function logActivity($userId, $action, $targetType, $targetId, $targetTitle, $details = '') {
    global $pdo;
    if (!$pdo) return;
    
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (user_id, action, target_type, target_id, target_title, details)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $action, $targetType, $targetId, $targetTitle, $details]);
}

// VULNERABLE: Get noteable by type and id WITHOUT proper authorization check
function getNoteable($noteableType, $noteableId) {
    global $pdo;
    if (!$pdo) return null;
    
    // VULNERABILITY: No authorization check!
    // This mimics GitLab's vulnerable NotesFinder that allows access to ANY personal_snippet
    switch ($noteableType) {
        case 'issue':
            $stmt = $pdo->prepare("SELECT * FROM issues WHERE id = ?");
            $stmt->execute([$noteableId]);
            return $stmt->fetch();
            
        case 'personal_snippet':
            // VULNERABLE: Returns ANY snippet without checking ownership or visibility
            $stmt = $pdo->prepare("SELECT * FROM personal_snippets WHERE id = ?");
            $stmt->execute([$noteableId]);
            return $stmt->fetch();
            
        default:
            return null;
    }
}

// Format date
function formatDate($date) {
    return date('M j, Y H:i', strtotime($date));
}

// JSON response helper
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
