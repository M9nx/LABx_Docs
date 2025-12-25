<?php
session_start();
require_once 'config.php';

// ============================================================
// VULNERABLE CODE: X-Original-URL Header Bypass for Delete
// ============================================================
// This endpoint deletes users and is also protected by front-end
// but vulnerable to X-Original-URL header bypass
// ============================================================

// Simulate front-end blocking: Direct requests to admin-delete.php are blocked
$originalUrl = $_SERVER['HTTP_X_ORIGINAL_URL'] ?? null;
$requestUri = $_SERVER['REQUEST_URI'];

// Check if direct access (blocked by "front-end")
$isDirectAccess = (strpos($requestUri, 'admin-delete.php') !== false);

if ($isDirectAccess && !$originalUrl) {
    $checkBypass = isset($_SERVER['HTTP_X_ORIGINAL_URL']);
    
    if (!$checkBypass) {
        http_response_code(403);
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-box {
            text-align: center;
            padding: 3rem;
        }
        h1 {
            font-size: 4rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        p {
            color: #888;
            font-size: 1.1rem;
        }
        a {
            color: #ff4444;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>403</h1>
        <p>Access denied</p>
        <p style="margin-top: 1rem; font-size: 0.9rem;">Path "/admin/delete" is blocked by external access control.</p>
        <p style="margin-top: 2rem;"><a href="index.php">← Back to Home</a></p>
    </div>
</body>
</html>';
        exit;
    }
}

// ============================================================
// VULNERABLE: If X-Original-URL header bypass works, process delete
// ============================================================

$username = $_GET['username'] ?? '';

if (empty($username)) {
    header("Location: admin.php");
    exit;
}

// Don't allow deleting administrator
if ($username === 'administrator') {
    header("Location: admin.php?error=protected");
    exit;
}

// Delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Check if carlos was deleted - lab complete!
    if ($username === 'carlos') {
        header("Location: success.php");
        exit;
    }
    header("Location: admin.php?deleted=" . urlencode($username));
} else {
    header("Location: admin.php?error=notfound");
}
exit;
?>
