<?php
session_start();
require_once 'config.php';

// STEP 3: CONFIRMATION - VULNERABLE! NO ACCESS CONTROL CHECK!
// This step should verify the user is an admin, but it doesn't!

// Only check if user is logged in (not if they're admin)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get parameters from POST
$username = $_POST['username'] ?? '';
$role = $_POST['role'] ?? '';
$action = $_POST['action'] ?? '';
$confirmed = $_POST['confirmed'] ?? '';

// Validate basic parameters exist
if (empty($username) || empty($role) || $action !== 'upgrade' || $confirmed !== 'true') {
    header("Location: admin.php");
    exit;
}

// Validate role value
if (!in_array($role, ['admin', 'user'])) {
    die('Invalid role specified.');
}

// VULNERABILITY: No admin check here!
// The developer assumed users would go through the multi-step process
// and that steps 1 and 2 would filter out non-admins.
// But an attacker can directly POST to this endpoint!

// Execute the role change
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
$stmt->bind_param("ss", $role, $username);

if ($stmt->execute()) {
    // Log the change (for audit trail)
    $log_stmt = $conn->prepare("INSERT INTO role_change_requests (target_username, new_role, requested_by, confirmed) VALUES (?, ?, ?, TRUE)");
    $log_stmt->bind_param("sss", $username, $role, $_SESSION['username']);
    $log_stmt->execute();
    
    // Check if the user promoted themselves
    if ($username === $_SESSION['username'] && $role === 'admin') {
        $_SESSION['role'] = 'admin';
        header("Location: success.php");
        exit;
    }
    
    // If admin changed someone else's role
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php?updated=" . urlencode($username));
        exit;
    }
    
    // Non-admin changed their own role
    header("Location: success.php");
    exit;
} else {
    die('Error updating role: ' . $conn->error);
}
?>
