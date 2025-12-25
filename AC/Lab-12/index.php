<?php
session_start();
require_once '../progress.php';
$isSolved = isLabSolved(12);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MultiStep Admin - Home</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 4rem;
        }
        .hero h1 {
            font-size: 3rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.2rem;
            color: #999;
            max-width: 600px;
            margin: 0 auto;
        }
        .status-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 3rem;
            text-align: center;
        }
        .status-box.success {
            background: rgba(0, 255, 0, 0.1);
            border-color: rgba(0, 255, 0, 0.3);
        }
        .solved-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #ff4444;
        }
        .feature-card h3 {
            color: #ff4444;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .feature-card p {
            color: #999;
            line-height: 1.6;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
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
        .btn-info {
            background: linear-gradient(135deg, #00aaff, #0077cc);
            color: white;
        }
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 170, 255, 0.4);
        }
        .step-flow {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        .step-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            text-align: center;
            position: relative;
        }
        .step-item .step-num {
            background: #ff4444;
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
        }
        .step-item .step-title {
            color: #ff6666;
            font-weight: 600;
        }
        .step-item .step-desc {
            color: #888;
            font-size: 0.9rem;
        }
        .step-arrow {
            color: #ff4444;
            font-size: 1.5rem;
            align-self: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔄 MultiStep Admin</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
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
        <div class="hero">
            <h1>🔄 MultiStep Admin</h1>
            <p>Lab 12: Multi-step process with no access control on one step</p>
        </div>

        <?php if ($isSolved): ?>
        <div class="solved-banner">
            <h3>✅ Lab Already Solved!</h3>
            <p>You've completed this lab. Run <strong>Setup Database</strong> to reset and try again.</p>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['setup']) && $_GET['setup'] === 'success'): ?>
        <div class="status-box success">
            ✅ Database setup complete! You can now login.
        </div>
        <?php endif; ?>

        <div class="status-box">
            <h3>🎯 Lab Objective</h3>
            <p>This lab has an admin panel with a flawed <strong>multi-step process</strong> for changing user roles.</p>
            <p style="margin-top: 0.5rem;">Log in using <code style="background: rgba(0,0,0,0.3); padding: 0.2rem 0.5rem; border-radius: 4px;">wiener:peter</code> and exploit the flawed access controls to <strong>promote yourself to administrator</strong>.</p>
        </div>

        <h3 style="text-align: center; color: #ff6666; margin-bottom: 1rem;">The Multi-Step Process:</h3>
        <div class="step-flow">
            <div class="step-item">
                <div class="step-num">1</div>
                <div class="step-title">Select User</div>
                <div class="step-desc">Admin panel</div>
            </div>
            <div class="step-arrow">→</div>
            <div class="step-item">
                <div class="step-num">2</div>
                <div class="step-title">Choose Role</div>
                <div class="step-desc">Role selection</div>
            </div>
            <div class="step-arrow">→</div>
            <div class="step-item" style="border-color: rgba(255, 150, 0, 0.5);">
                <div class="step-num" style="background: #ff9600;">3</div>
                <div class="step-title" style="color: #ffaa66;">Confirm</div>
                <div class="step-desc">⚠️ No auth check!</div>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <h3>🔐 User Authentication</h3>
                <p>Login as admin to understand the multi-step role change process, then exploit it as a regular user.</p>
            </div>
            <div class="feature-card">
                <h3>📝 Multi-Step Process</h3>
                <p>Role changes require 3 steps: select user, choose role, confirm. But is every step protected?</p>
            </div>
            <div class="feature-card">
                <h3>⚠️ Missing Access Control</h3>
                <p>One step in the process lacks authorization checks - exploit it to bypass security!</p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="setup_db.php" class="btn btn-primary">🗄️ Setup Database</a>
            <a href="login.php" class="btn btn-info">🚀 Start Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Description</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
        </div>
    </div>
</body>
</html>
