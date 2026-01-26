<?php
/**
 * Lab 27: Logout Page
 */

require_once 'config.php';

$pdo = getDBConnection();
if ($pdo && isset($_SESSION['user_id'])) {
    logActivity($pdo, $_SESSION['user_id'], 'logout', 'User logged out');
}

session_destroy();
header('Location: login.php');
exit;
