<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Logout Handler
 */

// Clear the session cookie
setcookie('session', '', time() - 3600, '/');

// Redirect to login page
header('Location: login.php');
exit;
