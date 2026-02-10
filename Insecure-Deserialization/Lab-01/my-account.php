<?php
/**
 * My Account Page - Shows session information
 * VULNERABLE: Displays and trusts deserialized session data
 */

require_once 'config.php';

// Get session from cookie
$session = getSessionFromCookie();

// Redirect if not logged in
if (!$session) {
    header('Location: login.php');
    exit;
}

// Get user details from database (for display purposes)
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$session->username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check admin privileges from the DESERIALIZED session (VULNERABLE)
$hasAdminPrivs = isAdmin();

// Get the raw cookie value for display
$rawCookie = $_COOKIE['session'] ?? '';
$decodedCookie = urldecode($rawCookie);
$base64Decoded = base64_decode($decodedCookie);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - SerialLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(249,115,22,0.3);
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #f97316;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #f97316; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(249,115,22,0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(249,115,22,0.2);
            border-color: #f97316;
            color: #f97316;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .page-title {
            font-size: 2rem;
            color: #f97316;
            margin-bottom: 2rem;
        }
        .card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }
        .card h3 {
            color: #fb923c;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            border-bottom: 1px solid rgba(249,115,22,0.2);
            padding-bottom: 0.75rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #999; font-weight: 500; }
        .info-value { color: #fff; font-family: 'Consolas', monospace; }
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .user-badge {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .cookie-display {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            overflow-x: auto;
        }
        .cookie-display pre {
            color: #fb923c;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .cookie-label {
            color: #999;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .admin-panel-link {
            display: inline-block;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        .admin-panel-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(239, 68, 68, 0.4);
        }
        .hint-box {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .hint-box p {
            color: #93c5fd;
            font-size: 0.9rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📦 SerialLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="my-account.php">My Account</a>
                <?php if ($hasAdminPrivs): ?>
                    <a href="admin.php" style="color: #ef4444; font-weight: bold;">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">👤 My Account</h1>

        <div class="card">
            <h3>Account Information</h3>
            <div class="info-row">
                <span class="info-label">Username</span>
                <span class="info-value"><?php echo htmlspecialchars($session->username); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Full Name</span>
                <span class="info-value"><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Database Role</span>
                <span class="info-value"><?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Session Admin Flag</span>
                <span class="info-value">
                    <?php if ($hasAdminPrivs): ?>
                        <span class="admin-badge">ADMIN (true)</span>
                    <?php else: ?>
                        <span class="user-badge">USER (false)</span>
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($hasAdminPrivs): ?>
            <a href="admin.php" class="admin-panel-link">🔧 Access Admin Panel</a>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>🍪 Session Cookie Details</h3>
            <p style="color: #999; margin-bottom: 1rem;">Your session is stored in a cookie containing serialized PHP data.</p>
            
            <div class="cookie-label">Raw Cookie (URL + Base64 Encoded):</div>
            <div class="cookie-display">
                <pre><?php echo htmlspecialchars($rawCookie); ?></pre>
            </div>

            <div class="cookie-label" style="margin-top: 1rem;">Base64 Decoded (Serialized PHP Object):</div>
            <div class="cookie-display">
                <pre><?php echo htmlspecialchars($base64Decoded); ?></pre>
            </div>

            <div class="hint-box">
                <p>💡 <strong>Hint:</strong> Notice the serialized PHP object structure. The <code>admin</code> property uses <code>b:0</code> for false and <code>b:1</code> for true. What happens if you modify this value?</p>
            </div>
        </div>

        <div class="card">
            <h3>🔍 Understanding the Serialized Format</h3>
            <div style="color: #b0b0b0; line-height: 1.8;">
                <p>PHP serialized objects follow this format:</p>
                <div class="cookie-display" style="margin: 1rem 0;">
                    <pre>O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:0;s:7:"user_id";i:3;}</pre>
                </div>
                <ul style="margin-left: 1.5rem; margin-top: 1rem;">
                    <li><code>O:8:"stdClass"</code> - Object of class "stdClass" (8 chars)</li>
                    <li><code>:3:</code> - Object has 3 properties</li>
                    <li><code>s:8:"username"</code> - String property "username" (8 chars)</li>
                    <li><code>s:6:"wiener"</code> - String value "wiener" (6 chars)</li>
                    <li><code>s:5:"admin"</code> - String property "admin" (5 chars)</li>
                    <li><code>b:0</code> - Boolean value false (b:1 = true)</li>
                    <li><code>s:7:"user_id"</code> - String property "user_id" (7 chars)</li>
                    <li><code>i:3</code> - Integer value 3</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
