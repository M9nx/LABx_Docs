<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Delete Account Handler
 * 
 * VULNERABILITY: Uses avatar_link from DESERIALIZED COOKIE, not from database!
 * An attacker can modify the avatar_link to delete any file on the server.
 */
require_once 'config.php';
require_once '../progress.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-account.php');
    exit;
}

// Get session from cookie
$session = getSessionFromCookie();

if (!$session) {
    header('Location: login.php');
    exit;
}

// Validate session exists in database
if (!validateSession($session)) {
    setcookie('session', '', time() - 3600, '/');
    header('Location: login.php');
    exit;
}

// VULNERABLE: Delete user account using the deserialized session data
// The avatar_link comes from the COOKIE, not the database!
$result = deleteUserAccount($session);

// Check if morale.txt was deleted (lab completion)
$moraleExists = file_exists(__DIR__ . '/home/carlos/morale.txt');
$labSolved = !$moraleExists;

if ($labSolved) {
    markLabSolved(3);
}

// Redirect to success page
header('Location: success.php?deleted=1' . ($labSolved ? '&solved=1' : ''));
exit;
