<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get current user info
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'] ?? '';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'];

// Check Admin cookie status
$admin_cookie = isset($_COOKIE['Admin']) ? $_COOKIE['Admin'] : 'not set';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3 - User Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
        }

        .header h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #ff6666;
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .info-item {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #ff4444;
        }

        .info-label {
            color: #ff9999;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .cookie-status {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .cookie-status h4 {
            color: #ffd54f;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .cookie-value {
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #666;
            color: #ffff66;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.1);
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .role-admin {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
        }

        .role-user {
            background: linear-gradient(45deg, #4CAF50, #388E3C);
            color: white;
        }

        .hint-box {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid #00ffff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .hint-box h4 {
            color: #00ffff;
            margin-bottom: 10px;
        }

        .hint-box p {
            color: #ccffff;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Profile</h1>
            <p style="color: #cccccc; font-size: 1.1rem;">Welcome back, <?= htmlspecialchars($full_name) ?>!</p>
        </div>

        <div class="profile-card">
            <h2 class="section-title">Account Information</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Username</div>
                    <div class="info-value"><?= htmlspecialchars($username) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= htmlspecialchars($full_name) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($email) ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Role</div>
                    <div class="info-value">
                        <span class="role-badge <?= $role === 'admin' ? 'role-admin' : 'role-user' ?>">
                            <?= htmlspecialchars($role) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="cookie-status">
            <h4>🍪 Cookie Status Information</h4>
            <p style="color: #fff3cd; margin-bottom: 10px;">Current Admin cookie value:</p>
            <div class="cookie-value"><?= htmlspecialchars($admin_cookie) ?></div>
            <p style="color: #fff3cd; margin-top: 10px; font-size: 0.9rem;">
                This cookie controls access to administrative functions. 
                Try modifying it using your browser's developer tools!
            </p>
        </div>

        <div class="hint-box">
            <h4>💡 Exploitation Hint</h4>
            <p>
                Open your browser's Developer Tools (F12) → Application/Storage → Cookies. 
                Find the "Admin" cookie and try changing its value to "true". 
                Then visit the admin panel to see what happens!
            </p>
        </div>

        <div class="action-buttons">
            <a href="admin.php" class="btn btn-primary">🛡️ Admin Panel</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>
</body>
</html>