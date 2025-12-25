<?php
session_start();
require_once 'config.php';

// Clear CSRF tokens for this user
if (isset($_SESSION['manager_id'])) {
    $stmt = $conn->prepare("DELETE FROM csrf_tokens WHERE manager_id = ?");
    $stmt->bind_param("i", $_SESSION['manager_id']);
    $stmt->execute();
}

// Destroy session
session_unset();
session_destroy();

header("Location: login.php");
exit;
?>
