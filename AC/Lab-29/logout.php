<?php
// Lab 29: LinkedPro Newsletter Platform - Logout
require_once 'config.php';

// Log the logout
if (isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id'], 'User logged out');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: login.php');
exit();
?>
