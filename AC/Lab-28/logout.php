<?php
/**
 * Lab 28: Logout
 * MTN Developers Portal
 */

session_start();

// Clear all session data for this lab
unset($_SESSION['lab28_user_id']);
unset($_SESSION['lab28_username']);
unset($_SESSION['lab28_logged_in']);

header('Location: login.php');
exit;
