<?php
session_start();
require_once 'config.php';

// Admin authentication for demotion
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    
    if (empty($username)) {
        die('Username parameter is required.');
    }
    
    // Prevent self-demotion
    if ($username === $_SESSION['username']) {
        die('You cannot demote yourself.');
    }
    
    // Update user role to user
    $stmt = $conn->prepare("UPDATE users SET role = 'user' WHERE username = ?");
    $stmt->bind_param("s", $username);
    
    if ($stmt->execute()) {
        header("Location: admin.php?demoted=" . urlencode($username));
        exit;
    } else {
        die('Error updating user role.');
    }
}

header("Location: admin.php");
exit;
?>
