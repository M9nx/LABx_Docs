<?php
/**
 * Lab 27: Configuration File
 * IDOR in Stats API Endpoint - Exness-style Trading Platform
 * 
 * INTENTIONALLY VULNERABLE FOR EDUCATIONAL PURPOSES
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'ac_lab27');

// Application settings
define('APP_NAME', 'Exness Personal Area');
define('APP_VERSION', '2.0.1');
define('LAB_FLAG', 'FLAG{idor_stats_api_trading_secrets_exposed_2024}');

// Session configuration
session_start();

/**
 * Database connection
 */
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
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['pa_id']);
}

/**
 * Require authentication
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
 * Get user's MT accounts
 */
function getUserAccounts($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM mt_accounts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * VULNERABLE FUNCTION - Get account stats without ownership check
 * This is intentionally vulnerable for the lab!
 */
function getAccountStats($pdo, $accountNumber, $timeRange = 365, $statType = 'equity') {
    // VULNERABILITY: No user ownership verification!
    // Any authenticated user can query any account's stats
    
    $validStatTypes = ['equity', 'net_profit', 'orders_count', 'trading_volume'];
    if (!in_array($statType, $validStatTypes)) {
        $statType = 'equity';
    }
    
    $stmt = $pdo->prepare("
        SELECT stat_date, $statType as value 
        FROM trading_stats 
        WHERE account_number = ? 
        AND stat_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        ORDER BY stat_date ASC
    ");
    $stmt->execute([$accountNumber, $timeRange]);
    return $stmt->fetchAll();
}

/**
 * VULNERABLE FUNCTION - Get account info without ownership check
 */
function getAccountByNumber($pdo, $accountNumber) {
    // VULNERABILITY: Returns any account without checking if user owns it
    $stmt = $pdo->prepare("SELECT * FROM mt_accounts WHERE account_number = ?");
    $stmt->execute([$accountNumber]);
    return $stmt->fetch();
}

/**
 * Log API access (for demonstration)
 */
function logApiAccess($pdo, $userId, $endpoint, $requestedAccount, $isIdor = false) {
    $stmt = $pdo->prepare("
        INSERT INTO api_logs (user_id, endpoint, requested_account, ip_address, user_agent, is_idor_attempt)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $endpoint,
        $requestedAccount,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        $isIdor ? 1 : 0
    ]);
}

/**
 * Log activity
 */
function logActivity($pdo, $userId, $action, $details = '') {
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (user_id, action, details, ip_address)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
}

/**
 * Format currency
 */
function formatMoney($amount, $currency = 'USD') {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'USC' => '¢'
    ];
    $symbol = $symbols[$currency] ?? '$';
    return $symbol . number_format($amount, 2);
}

/**
 * Format large numbers
 */
function formatLargeNumber($num) {
    if ($num >= 1000000) {
        return number_format($num / 1000000, 2) . 'M';
    } elseif ($num >= 1000) {
        return number_format($num / 1000, 2) . 'K';
    }
    return number_format($num, 2);
}

/**
 * Get account type badge color
 */
function getAccountTypeBadge($type) {
    $colors = [
        'Standard' => '#ffd700',
        'Pro' => '#00d4ff',
        'Raw Spread' => '#ff6b6b',
        'Zero' => '#7c3aed',
        'Standard Cent' => '#10b981'
    ];
    return $colors[$type] ?? '#ffd700';
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Check if request wants JSON
 */
function wantsJson() {
    return isset($_SERVER['HTTP_ACCEPT']) && 
           strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
}
