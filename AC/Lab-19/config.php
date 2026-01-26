<?php
/**
 * Lab 19: IDOR - Delete Users Saved Projects
 * Database Configuration
 * 
 * VULNERABILITY: This lab demonstrates IDOR in project deletion
 * The delete endpoint doesn't verify ownership before deleting
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'ac_lab19';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Database might not exist yet - that's okay for initial setup
    if (strpos($e->getMessage(), 'Unknown database') === false) {
        error_log("Database connection failed: " . $e->getMessage());
    }
}
?>
