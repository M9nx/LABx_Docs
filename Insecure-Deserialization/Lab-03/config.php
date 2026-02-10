<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Database Configuration
 * 
 * VULNERABILITY: File Deletion via Deserialized Object
 * The User object contains an avatar_link attribute pointing to the user's avatar file.
 * When the account is deleted, the server deletes whatever file is specified in avatar_link.
 * An attacker can modify the serialized avatar_link to point to any file on the server.
 */

// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
define('DB_HOST', $creds['host']);
define('DB_USERNAME', $creds['user']);
define('DB_PASSWORD', $creds['pass']);
define('DB_NAME', 'deserial_lab3');

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fef3c7;border:1px solid #f59e0b;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

/**
 * User class for serialization
 * Contains username and avatar_link for session management
 * 
 * VULNERABILITY: The avatar_link is stored in the serialized session cookie.
 * When the account is deleted, the file at avatar_link is also deleted.
 * An attacker can modify avatar_link to point to any file (e.g., /home/carlos/morale.txt)
 */
class User {
    public $username;
    public $avatar_link;
    
    public function __construct($username, $avatar_link) {
        $this->username = $username;
        $this->avatar_link = $avatar_link;
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
 * Creates a User object with username and avatar_link
 */
function createSerializedSession($user) {
    // Create User object with the user's avatar path
    $sessionData = new User($user['username'], $user['avatar_link']);
    
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
        // No validation of the avatar_link path
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
 * Validate session against database
 */
function validateSession($sessionData) {
    if (!$sessionData || !isset($sessionData->username)) {
        return false;
    }
    
    try {
        $pdo = getDBConnection();
        
        // Check if user exists in database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$sessionData->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get current logged-in user info from database
 */
function getCurrentUser() {
    $session = getSessionFromCookie();
    if (!$session || !validateSession($session)) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, email, full_name, avatar_link FROM users WHERE username = ?");
        $stmt->execute([$session->username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * INTENTIONALLY VULNERABLE: Delete user account and their avatar file
 * The avatar_link comes from the DESERIALIZED SESSION COOKIE, not the database!
 * This means an attacker can modify the avatar_link to delete any file.
 */
function deleteUserAccount($sessionData) {
    if (!$sessionData || !isset($sessionData->username)) {
        return ['success' => false, 'message' => 'Invalid session'];
    }
    
    try {
        $pdo = getDBConnection();
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$sessionData->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // VULNERABLE: Delete the file specified in the DESERIALIZED session data
        // NOT from the database! An attacker can modify avatar_link to any path.
        $avatarPath = $sessionData->avatar_link;
        
        if (!empty($avatarPath) && file_exists($avatarPath)) {
            // DANGEROUS: Deletes whatever file is specified in the cookie!
            unlink($avatarPath);
        }
        
        // Delete user from database
        $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
        $stmt->execute([$sessionData->username]);
        
        // Clear the session cookie
        setcookie('session', '', time() - 3600, '/');
        
        return ['success' => true, 'message' => 'Account deleted successfully'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting account: ' . $e->getMessage()];
    }
}

/**
 * Get the avatar link from session (for display purposes)
 */
function getAvatarFromSession() {
    $session = getSessionFromCookie();
    if ($session && isset($session->avatar_link)) {
        return $session->avatar_link;
    }
    return null;
}
