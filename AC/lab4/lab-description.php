<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - RoleLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #ff4444;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .page-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 2rem;
        }
        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .card-title {
            color: #ff6666;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-content {
            color: #ccc;
            line-height: 1.8;
        }
        .card-content p {
            margin-bottom: 1rem;
        }
        .card-content ul, .card-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .card-content li {
            margin-bottom: 0.5rem;
        }
        .card-content code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #ff6666;
            font-family: 'Consolas', monospace;
        }
        .card-content pre {
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1rem 0;
            font-family: 'Consolas', monospace;
            color: #88ff88;
        }
        .difficulty-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff8800, #cc6600);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 1rem;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 RoleLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">📋 Lab Description</h1>

        <div class="info-card">
            <h2 class="card-title">🎯 Objective</h2>
            <div class="card-content">
                <p>
                    This lab demonstrates a <strong>privilege escalation vulnerability</strong> through 
                    mass assignment in user profile updates. The application's admin panel is protected 
                    by role-based access control, but the vulnerability allows you to modify your own role.
                </p>
                <p><span class="difficulty-badge">APPRENTICE</span></p>
            </div>
        </div>

        <div class="info-card">
            <h2 class="card-title">📝 Lab Details</h2>
            <div class="card-content">
                <ul>
                    <li>Admin panel location: <code>/admin.php</code></li>
                    <li>Admin panel requires: <code>roleid = 2</code></li>
                    <li>Your starting role: <code>roleid = 1</code> (Regular User)</li>
                    <li>Target: Delete user <code>carlos</code></li>
                </ul>
            </div>
        </div>

        <div class="info-card">
            <h2 class="card-title">🔑 Credentials</h2>
            <div class="card-content">
                <p>Login with:</p>
                <ul>
                    <li>Username: <code>wiener</code></li>
                    <li>Password: <code>peter</code></li>
                </ul>
            </div>
        </div>

        <div class="info-card">
            <h2 class="card-title">💡 Hints</h2>
            <div class="card-content">
                <ol>
                    <li>Login and go to your profile page</li>
                    <li>Update your email and observe the request/response in your browser's developer tools or Burp Suite</li>
                    <li>Notice what fields are included in the JSON response</li>
                    <li>Try adding additional fields to your JSON request</li>
                    <li>What happens if you include <code>"roleid": 2</code> in your update request?</li>
                </ol>
            </div>
        </div>

        <div class="info-card">
            <h2 class="card-title">🔧 Exploitation Steps</h2>
            <div class="card-content">
                <ol>
                    <li>Login as <code>wiener:peter</code></li>
                    <li>Go to Profile and update your email</li>
                    <li>Intercept the request or use browser dev tools</li>
                    <li>Modify the JSON payload:
                        <pre>{
    "email": "any@email.com",
    "roleid": 2
}</pre>
                    </li>
                    <li>Your role will be updated to Administrator</li>
                    <li>Access <code>/admin.php</code></li>
                    <li>Delete user <code>carlos</code></li>
                </ol>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <a href="login.php" class="btn">Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">View Documentation</a>
        </div>
    </div>
</body>
</html>
