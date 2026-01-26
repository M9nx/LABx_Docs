<?php
session_start();
require_once 'config.php';

$setupMessage = '';
if (isset($_GET['setup']) && $_GET['setup'] === 'success') {
    $setupMessage = 'Database initialized successfully! All users reset to default roles.';
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 13 - Referer-based Access Control</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            color: #ff4444;
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
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }
        .hero-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .hero p {
            color: #888;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .setup-msg {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #66ff66;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .vulnerability-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .vulnerability-card h2 {
            color: #ff6666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vulnerability-card p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .referer-demo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        .referer-box {
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            text-align: center;
        }
        .referer-box.admin {
            border-color: #00cc00;
        }
        .referer-box.user {
            border-color: #ff4444;
        }
        .referer-box .title {
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .referer-box .status {
            font-size: 0.85rem;
        }
        .referer-box.admin .status {
            color: #66ff66;
        }
        .referer-box.user .status {
            color: #ff6666;
        }
        .flow-arrow {
            color: #666;
            font-size: 2rem;
        }
        .code-example {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.3);
        }
        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .credential-card {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
        }
        .credential-card h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .credential-card code {
            color: #88ff88;
            font-family: monospace;
        }
        .admin-access {
            background: rgba(0, 200, 0, 0.1);
            border: 1px solid rgba(0, 200, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        .admin-access h3 {
            color: #66ff66;
            margin-bottom: 0.5rem;
        }
        .admin-access a {
            color: #88ff88;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📋 Referer Lab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">My Account</a>
                    <?php if ($isAdmin): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($setupMessage): ?>
            <div class="setup-msg">✅ <?php echo htmlspecialchars($setupMessage); ?></div>
        <?php endif; ?>

        <div class="hero">
            <div class="hero-icon">📋</div>
            <h1>Referer-based Access Control</h1>
            <p>Exploit flawed access control that trusts the Referer header to authorize admin actions</p>
        </div>

        <div class="vulnerability-card">
            <h2>🔓 The Vulnerability</h2>
            <p>
                This application uses the HTTP <strong>Referer header</strong> to determine if a user is 
                authorized to perform administrative actions. The logic assumes that if the request comes 
                from the admin page, the user must be an admin.
            </p>
            <p>
                However, the Referer header is <strong>client-controlled</strong> and can be easily 
                spoofed. An attacker can capture a legitimate admin request and replay it with their 
                own session cookie while maintaining the original Referer header.
            </p>

            <div class="referer-demo">
                <div class="referer-box admin">
                    <div class="title">Request with Admin Referer</div>
                    <div class="status">✓ Access Granted</div>
                </div>
                <span class="flow-arrow">vs</span>
                <div class="referer-box user">
                    <div class="title">Request without Referer</div>
                    <div class="status">✗ Access Denied</div>
                </div>
            </div>

            <h3 style="color: #ff6666; margin: 1.5rem 0 0.5rem;">Vulnerable Header Check:</h3>
            <div class="code-example">// VULNERABLE: Only checks Referer header, not actual permissions
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (strpos($referer, '/admin') === false) {
    die('Unauthorized');  // Easily bypassed!
}</div>
        </div>

        <div class="vulnerability-card">
            <h2>🔑 Available Credentials</h2>
            <p>Use these accounts to explore and exploit the vulnerability:</p>
            <div class="credentials-grid">
                <div class="credential-card">
                    <h4>👑 Administrator</h4>
                    <code>administrator : admin</code>
                </div>
                <div class="credential-card">
                    <h4>👤 Target User</h4>
                    <code>wiener : peter</code>
                </div>
                <div class="credential-card">
                    <h4>👤 Other User</h4>
                    <code>carlos : montoya</code>
                </div>
            </div>
        </div>

        <?php if ($isAdmin): ?>
        <div class="admin-access">
            <h3>🛡️ Admin Access Available</h3>
            <p>You're logged in as admin. <a href="admin.php">Go to Admin Panel</a> to manage user roles.</p>
        </div>
        <?php endif; ?>

        <div class="actions">
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="btn btn-primary">👤 My Profile</a>
                <?php if ($isAdmin): ?>
                    <a href="admin.php" class="btn btn-primary">👑 Admin Panel</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">🔐 Login</a>
            <?php endif; ?>
            <a href="lab-description.php" class="btn btn-secondary">📖 Lab Info</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
        </div>
    </div>
</body>
</html>
