<?php
/**
 * Logout - Clears the session cookie
 */

// Clear the session cookie
setcookie('session', '', time() - 3600, '/');

// Redirect to home page
header('Location: index.php');
exit;
?>
