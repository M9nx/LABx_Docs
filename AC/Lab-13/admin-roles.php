<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

// VULNERABLE: Referer-based Access Control
// This endpoint checks the Referer header instead of actual user permissions!

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get parameters
$username = $_GET['username'] ?? '';
$action = $_GET['action'] ?? '';

// Validate parameters
if (empty($username) || !in_array($action, ['upgrade', 'downgrade'])) {
    header("Location: index.php");
    exit;
}

// VULNERABILITY: Referer-based access control!
// The code only checks if the Referer header contains '/admin'
// This can be easily bypassed by an attacker who captures a legitimate request
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// Log the access attempt
$log_stmt = $conn->prepare("INSERT INTO access_logs (user_id, action, target_user, referer_header, ip_address, success) VALUES (?, ?, ?, ?, ?, ?)");

if (strpos($referer, '/admin') === false) {
    // Referer doesn't contain /admin - deny access
    $success = false;
    $log_stmt->bind_param("issssi", $_SESSION['user_id'], $action, $username, $referer, $_SERVER['REMOTE_ADDR'], $success);
    $log_stmt->execute();
    
    http_response_code(401);
    die('Unauthorized - Invalid Referer header. Access must originate from the admin panel.');
}

// VULNERABLE: If Referer contains '/admin', allow the action regardless of user role!
// This is the security flaw - we trust the Referer header instead of checking actual permissions

// Determine new role
$newRole = ($action === 'upgrade') ? 'admin' : 'user';

// Execute role change
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
$stmt->bind_param("ss", $newRole, $username);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    // Log successful action
    $success = true;
    $log_stmt->bind_param("issssi", $_SESSION['user_id'], $action, $username, $referer, $_SERVER['REMOTE_ADDR'], $success);
    $log_stmt->execute();
    
    // Check if user upgraded themselves
    if ($username === $_SESSION['username'] && $action === 'upgrade') {
        $_SESSION['role'] = 'admin';
        markLabSolved(13);
        header("Location: success.php");
        exit;
    }
    
    // Redirect back to admin panel
    header("Location: admin.php?{$action}d=" . urlencode($username));
    exit;
} else {
    die('Error: User not found or no changes made.');
}
?>
