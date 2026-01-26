<?php
require_once 'config.php';

// Log logout
if (isset($_SESSION['user_id']) && $pdo) {
    logActivity($pdo, 'LOGOUT', 'user', $_SESSION['user_id'], 'User logged out');
}

// Destroy session
session_destroy();

header('Location: login.php');
exit;
