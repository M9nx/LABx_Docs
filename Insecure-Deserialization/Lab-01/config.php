<?php
/**
 * Lab 01: Modifying Serialized Objects
 * Database Configuration
 */

// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
define('DB_HOST', $creds['host']);
define('DB_USERNAME', $creds['user']);
define('DB_PASSWORD', $creds['pass']);
define('DB_NAME', 'deserial_lab1');

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fef3c7;border:1px solid #f59e0b;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

/**
 * Create database connection using PDO
 */
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

/**
 * INTENTIONALLY VULNERABLE: Serialize user data for session cookie
 * This creates a serialized PHP object that will be stored in a cookie
 */
function createSerializedSession($user) {
    $sessionData = new stdClass();
    $sessionData->username = $user['username'];
    $sessionData->admin = ($user['role'] === 'admin') ? true : false;
    $sessionData->user_id = $user['id'];
    
    // Serialize and encode for cookie storage
    $serialized = serialize($sessionData);
    $encoded = base64_encode($serialized);
    return urlencode($encoded);
}

/**
 * INTENTIONALLY VULNERABLE: Deserialize session cookie without validation
 * This trusts the client-provided serialized data completely
 */
function getSessionFromCookie() {
    if (!isset($_COOKIE['session'])) {
        return null;
    }
    
    try {
        // Decode the cookie value
        $decoded = urldecode($_COOKIE['session']);
        $unserialized = base64_decode($decoded);
        
        // VULNERABLE: Directly unserialize user-controlled data
        $sessionData = @unserialize($unserialized);
        
        if ($sessionData === false) {
            return null;
        }
        
        return $sessionData;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Check if current session has admin privileges
 * VULNERABLE: Trusts the serialized admin flag from cookie
 */
function isAdmin() {
    $session = getSessionFromCookie();
    if (!$session) {
        return false;
    }
    
    // VULNERABLE: Directly trusts the 'admin' property from deserialized cookie
    return isset($session->admin) && $session->admin === true;
}

/**
 * Get current logged-in username from session
 */
function getCurrentUsername() {
    $session = getSessionFromCookie();
    if (!$session || !isset($session->username)) {
        return null;
    }
    return $session->username;
}

/**
 * Initialize database and tables if they don't exist
 */
function initDatabase() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Check if users exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $users = [
                ['administrator', 'admin_secret_pass', 'admin@seriallab.com', 'Administrator', 'admin'],
                ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user'],
                ['wiener', 'peter', 'wiener@example.com', 'Peter Wiener', 'user'],
            ];
            
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
            foreach ($users as $u) {
                $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4]]);
            }
        }
        
        return $pdo;
    } catch(PDOException $e) {
        die("Database initialization failed: " . $e->getMessage());
    }
}

// Initialize database when config is loaded
initDatabase();
?>
