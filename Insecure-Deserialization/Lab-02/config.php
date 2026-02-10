<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Database Configuration
 * 
 * VULNERABILITY: PHP Type Juggling with Loose Comparison
 * The access_token is compared using == instead of ===
 * When comparing boolean true to any non-empty string, result is TRUE
 * Alternative: In PHP 7, integer 0 == "string" is also TRUE
 */

// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
define('DB_HOST', $creds['host']);
define('DB_USERNAME', $creds['user']);
define('DB_PASSWORD', $creds['pass']);
define('DB_NAME', 'deserial_lab2');

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fef3c7;border:1px solid #f59e0b;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

/**
 * User class for serialization
 * Contains username and access_token for session management
 */
class User {
    public $username;
    public $access_token;
    
    public function __construct($username, $access_token) {
        $this->username = $username;
        $this->access_token = $access_token;
    }
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
 * Creates a User object with username and access_token
 */
function createSerializedSession($user) {
    // Create User object with the user's access token
    $sessionData = new User($user['username'], $user['access_token']);
    
    // Serialize and encode for cookie storage
    $serialized = serialize($sessionData);
    $encoded = base64_encode($serialized);
    return $encoded;
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
        $decoded = base64_decode($_COOKIE['session']);
        
        // VULNERABLE: Directly unserialize user-controlled data
        $sessionData = @unserialize($decoded);
        
        if ($sessionData === false) {
            return null;
        }
        
        return $sessionData;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * INTENTIONALLY VULNERABLE: Validate session using loose comparison
 * Uses == instead of === for access_token comparison
 * 
 * VULNERABILITY EXPLANATION:
 * In PHP, "some_string" == 0 returns TRUE because:
 * 1. PHP converts the string to an integer for comparison
 * 2. Non-numeric strings convert to 0
 * 3. 0 == 0 is TRUE
 * 
 * This allows bypass by setting access_token to boolean true (or integer 0 in PHP 7)
 */
function validateSession($sessionData) {
    if (!$sessionData || !isset($sessionData->username) || !isset($sessionData->access_token)) {
        return false;
    }
    
    try {
        $pdo = getDBConnection();
        
        // Get the user's real access token from database
        $stmt = $pdo->prepare("SELECT access_token FROM users WHERE username = ?");
        $stmt->execute([$sessionData->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        // VULNERABLE: Using loose comparison (==) instead of strict (===)
        // boolean true == "any_string" evaluates to TRUE in PHP
        // This is because PHP converts non-empty strings to boolean true
        if ($sessionData->access_token == $user['access_token']) {
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Check if current session has admin privileges
 */
function isAdmin() {
    $session = getSessionFromCookie();
    if (!$session || !validateSession($session)) {
        return false;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
        $stmt->execute([$session->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['role'] === 'admin';
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get current logged-in username from session
 */
function getCurrentUsername() {
    $session = getSessionFromCookie();
    if (!$session || !validateSession($session)) {
        return null;
    }
    return $session->username;
}

/**
 * Get current user info from database
 */
function getCurrentUser() {
    $username = getCurrentUsername();
    if (!$username) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, email, full_name, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Generate a random access token
 */
function generateAccessToken() {
    return bin2hex(random_bytes(32));
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
                access_token VARCHAR(64) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Check if users exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            // Generate unique access tokens for each user
            $users = [
                ['administrator', 'admin_secret_pass', 'admin@seriallab.com', 'Administrator', 'admin', generateAccessToken()],
                ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user', generateAccessToken()],
                ['wiener', 'peter', 'wiener@example.com', 'Peter Wiener', 'user', generateAccessToken()],
            ];
            
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role, access_token) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($users as $u) {
                $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4], $u[5]]);
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
