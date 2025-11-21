<?php
/**
 * Database Configuration for SQL Injection Lab
 * 
 * WARNING: This is a deliberately vulnerable configuration for educational purposes only!
 * Never use this type of configuration in production!
 */

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Default XAMPP MySQL username
define('DB_PASS', '');               // Default XAMPP MySQL password (empty)
define('DB_NAME', 'sqli_lab');

// Global database connection variable
$connection = null;

/**
 * Establish database connection
 */
function connect_db() {
    global $connection;
    
    if ($connection === null) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($connection->connect_error) {
            die("<div class='error'>Connection failed: " . $connection->connect_error . "</div>");
        }
        
        // Set charset
        $connection->set_charset("utf8");
    }
    
    return $connection;
}

/**
 * Close database connection
 */
function close_db() {
    global $connection;
    if ($connection) {
        $connection->close();
        $connection = null;
    }
}
?>