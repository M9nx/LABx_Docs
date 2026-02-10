<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Logout Handler
 */

// Clear the session cookie
setcookie('session', '', time() - 3600, '/');

// Redirect to home
header('Location: index.php');
exit;
?>
