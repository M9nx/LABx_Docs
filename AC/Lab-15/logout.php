<?php
session_start();
require_once 'config.php';

// Log the logout
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, ip_address, user_agent) VALUES (?, 'logout', ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown']);
}

// Destroy session
session_unset();
session_destroy();

header("Location: login.php");
exit;
?>
