<?php
// Lab 23: Logout
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id'], 'User logged out');
}

session_destroy();
header('Location: login.php');
exit;
?>
