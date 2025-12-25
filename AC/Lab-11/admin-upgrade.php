<?php
session_start();
require_once 'config.php';

// VULNERABLE CODE: Only checks if method is POST for admin requirement
// But doesn't validate for GET method - allows bypass!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST method requires admin privileges
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        die('Access denied. Admin privileges required.');
    }
    
    $username = $_POST['username'] ?? '';
} else {
    // GET method doesn't check admin privileges - VULNERABILITY!
    // This allows any authenticated user to promote themselves
    $username = $_GET['username'] ?? '';
}

if (empty($username)) {
    http_response_code(400);
    die('Username parameter is required.');
}

// Update user role to admin
$stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    // If current user promoted themselves, update session
    if (isset($_SESSION['username']) && $_SESSION['username'] === $username) {
        $_SESSION['role'] = 'admin';
        
        // Redirect to success page
        header("Location: success.php");
        exit;
    }
    
    // Admin promoted someone else
    header("Location: admin.php?promoted=" . urlencode($username));
    exit;
} else {
    http_response_code(500);
    die('Error updating user role.');
}
?>
