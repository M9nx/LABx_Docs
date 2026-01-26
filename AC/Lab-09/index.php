<?php
session_start();
require_once '../progress.php';
$isSolved = isLabSolved(9);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatLab - Home</title>
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
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        .btn-chat {
            background: linear-gradient(135deg, #44ff44, #00cc00);
            color: white;
        }
        .btn-chat:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(68, 255, 68, 0.4);
        }
    .solved-banner { background: rgba(0, 255, 0, 0.1); border: 1px solid rgba(0, 255, 0, 0.3); border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; text-align: center; } .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">💬 ChatLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="chat.php">Live Chat</a>
                    <a href="profile.php">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="hero">
            <h1>💬 ChatLab</h1>
            <p>Lab 9: Insecure Direct Object References - Chat Transcript IDOR</p>
        

        <?php if ($isSolved): ?>
        <div class='solved-banner'>
            <h3>✅ Lab Already Solved!</h3>
            <p>You've completed this lab. Run <strong>Setup Database</strong> to reset and try again.</p>
        </div>
        <?php endif; ?>

        </div>

        <?php if (isset($_GET['setup']) && $_GET['setup'] === 'success'): ?>
        <div class="status-box success">
            ✅ Database setup complete! Chat transcripts created. You can now login with <strong>wiener:peter</strong>
        </div>
        <?php endif; ?>

        <div class="status-box">
            <h3>🎯 Lab Objective</h3>
            <p>This lab stores user chat logs directly on the server's file system, and retrieves them using static URLs.</p>
            <p style="margin-top: 0.5rem;">Find the password for the user <strong>carlos</strong>, and log into their account.</p>
            <p style="margin-top: 0.5rem;">Credentials: <code style="background: rgba(0,0,0,0.3); padding: 0.2rem 0.5rem; border-radius: 4px;">wiener:peter</code></p>
        </div>

        <div class="features">
            <div class="feature-card">
                <h3>💬 Live Chat Support</h3>
                <p>Connect with our support team via real-time chat for immediate assistance.</p>
            </div>
            <div class="feature-card">
                <h3>📄 Chat Transcripts</h3>
                <p>Download your chat history as text files for future reference.</p>
            </div>
            <div class="feature-card">
                <h3>⚠️ Static File URLs</h3>
                <p>Transcripts are stored with predictable filenames and retrieved via direct URLs.</p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="setup_db.php" class="btn btn-primary">🗄️ Setup Database</a>
            <a href="login.php" class="btn btn-info">🚀 Start Lab</a>
            <a href="chat.php" class="btn btn-chat">💬 Live Chat</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Description</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
        </div>
    </div>
</body>
</html>


