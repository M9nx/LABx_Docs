<?php
// Lab 22: IDOR in Booking Detail and Bids - Configuration
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'ac_lab22');

// Application settings
define('APP_NAME', 'RideKea');
define('APP_VERSION', '1.0.169');
define('LAB_NUMBER', 22);

// Theme colors (Cyan/Teal for ride-sharing theme)
define('PRIMARY_COLOR', '#06b6d4');
define('SECONDARY_COLOR', '#0891b2');
define('ACCENT_COLOR', '#22d3ee');
define('DARK_BG', '#0f172a');

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
    return isset($_SESSION['user_id']) && isset($_SESSION['access_token']);
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Get user's bookings
function getUserBookings($user_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE passenger_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Generate unique IDs
function generateBookingId() {
    return 'BKG_' . substr(md5(uniqid(mt_rand(), true)), 0, 12);
}

function generateTripNo() {
    return 'PKX' . mt_rand(100000000, 999999999);
}

function generateBidId() {
    return 'BID_' . substr(md5(uniqid(mt_rand(), true)), 0, 10);
}

// Format currency
function formatCurrency($amount) {
    return 'Rs. ' . number_format($amount, 0);
}

// Format distance
function formatDistance($meters) {
    if ($meters >= 1000) {
        return number_format($meters / 1000, 1) . ' km';
    }
    return $meters . ' m';
}

// Format time
function formatTime($seconds) {
    if ($seconds >= 3600) {
        return floor($seconds / 3600) . 'h ' . floor(($seconds % 3600) / 60) . 'm';
    }
    return floor($seconds / 60) . ' min';
}

// Get status badge color
function getStatusColor($status) {
    $colors = [
        'pending' => '#f59e0b',
        'accepted' => '#3b82f6',
        'in_progress' => '#8b5cf6',
        'completed' => '#10b981',
        'cancelled' => '#ef4444'
    ];
    return $colors[$status] ?? '#64748b';
}

// JSON response helper
function jsonResponse($data, $code = 200, $success = true, $message = '') {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode([
        'code' => $code,
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Sanitize output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
