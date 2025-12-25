<?php
require_once 'config.php';

if (isLoggedIn()) {
    // Deactivate current session in database
    $stmt = $pdo->prepare("UPDATE user_sessions SET is_active = 0 WHERE user_id = ? AND session_token = ?");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['session_token']]);
    
    // Log activity
    $stmt = $pdo->prepare("INSERT INTO session_activity_log (user_id, action, details, ip_address) VALUES (?, 'logout', 'User logged out', ?)");
    $stmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
}

// Destroy PHP session
session_destroy();

header('Location: login.php');
exit;
?>
