<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SecureCorp</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        .profile-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2.5rem;
        }
        .profile-box h1 {
            color: #ff4444;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 2rem;
        }
        .profile-field {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .profile-field:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .profile-field label {
            display: block;
            color: #888;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        .profile-field span {
            display: block;
            color: #e0e0e0;
            font-size: 1.1rem;
        }
        .profile-field .role-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .profile-field .role-badge.admin {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
        }
        .profile-field .role-badge.user {
            background: rgba(0, 200, 100, 0.2);
            color: #00c864;
        }
        .hint-box {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.3);
            border-radius: 10px;
        }
        .hint-box h3 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        .hint-box p {
            color: #ddcc88;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .hint-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #ff6666;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🏢 SecureCorp</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="profile.php">My Account</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="profile-box">
            <div class="profile-avatar">👤</div>
            <h1>My Profile</h1>
            
            <div class="profile-field">
                <label>Username</label>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            
            <div class="profile-field">
                <label>Email</label>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            
            <div class="profile-field">
                <label>Role</label>
                <span class="role-badge <?php echo $user['role']; ?>">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </div>
            
            <div class="profile-field">
                <label>Member Since</label>
                <span><?php echo htmlspecialchars($user['created_at']); ?></span>
            </div>
        </div>
        
        <div class="hint-box">
            <h3>💡 Lab Hint</h3>
            <p>There's an admin panel at <code>/admin</code> that's blocked by the front-end. But what if the back-end processes URLs differently via HTTP headers?</p>
            <p style="margin-top: 0.5rem;">Try intercepting requests with Burp Suite and experimenting with the <code>X-Original-URL</code> header.</p>
        </div>
    </div>
</body>
</html>
