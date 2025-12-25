<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Chat Transcript IDOR</title>
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
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
            padding: 2rem;
        }
        .lab-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .lab-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 25px;
            color: #ff6666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2.5rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .lab-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .difficulty {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.2rem;
            background: rgba(255, 170, 0, 0.2);
            border: 1px solid rgba(255, 170, 0, 0.4);
            border-radius: 25px;
            color: #ffaa00;
            font-weight: 600;
            margin-top: 1rem;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-card h2 {
            color: #ff4444;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .content-card p {
            color: #b0b0b0;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content-card ul {
            list-style: none;
            padding-left: 0;
        }
        .content-card li {
            color: #b0b0b0;
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }
        .content-card li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #ff4444;
        }
        .credentials-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .credentials-box h3 {
            color: #ff6666;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        .credentials-box code {
            display: block;
            color: #ff8888;
            font-size: 1.1rem;
            font-family: 'Consolas', monospace;
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
        .solution-toggle {
            background: rgba(255, 200, 68, 0.1);
            border: 1px solid rgba(255, 200, 68, 0.3);
            border-radius: 10px;
            margin-top: 1rem;
            overflow: hidden;
        }
        .solution-toggle summary {
            padding: 1rem 1.5rem;
            cursor: pointer;
            color: #ffcc44;
            font-weight: 600;
            outline: none;
            user-select: none;
        }
        .solution-toggle summary:hover {
            background: rgba(255, 200, 68, 0.1);
        }
        .solution-content {
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 200, 68, 0.2);
        }
        .solution-content ol {
            color: #ffdd88;
            padding-left: 1.5rem;
        }
        .solution-content li {
            margin-bottom: 0.8rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">💬 ChatLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="chat.php">Live Chat</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-header">
            <span class="lab-badge">Lab 9</span>
            <h1>💬 Insecure Direct Object References</h1>
            <p>Chat Transcript IDOR Vulnerability</p>
            <div class="difficulty">🟠 Practitioner</div>
        </div>

        <div class="content-card">
            <h2>📋 Lab Description</h2>
            <p>This lab stores user chat logs directly on the server's file system, and retrieves them using static URLs.</p>
            <p>Solve the lab by finding the password for the user <strong>carlos</strong>, and logging into their account.</p>
        </div>

        <div class="content-card">
            <h2>🎯 Objective</h2>
            <ul>
                <li>Access the live chat feature and send a message</li>
                <li>Download your chat transcript and observe the URL pattern</li>
                <li>Enumerate other transcript files to find sensitive information</li>
                <li>Find carlos's password in a chat transcript</li>
                <li>Login as carlos to solve the lab</li>
            </ul>
            
            <div class="credentials-box">
                <h3>📝 Your Credentials</h3>
                <code>wiener:peter</code>
            </div>
        </div>

        <div class="content-card">
            <h2>💡 Hint</h2>
            <p>Pay attention to how transcripts are named and retrieved. What happens if you change the filename in the URL?</p>
        </div>

        <div class="content-card">
            <details class="solution-toggle">
                <summary>🔓 Click to reveal solution</summary>
                <div class="solution-content">
                    <ol>
                        <li>Login with credentials <strong>wiener:peter</strong></li>
                        <li>Navigate to the <strong>Live Chat</strong> tab</li>
                        <li>Send a message and click <strong>View Transcript</strong></li>
                        <li>Observe the URL: <code>download-transcript.php?file=5.txt</code></li>
                        <li>Notice that transcript files are named with incrementing numbers</li>
                        <li>Change the filename to <strong>1.txt</strong> in the URL</li>
                        <li>Review the transcript - find carlos's password: <strong>h5a2xfj8k3</strong></li>
                        <li>Return to login page and authenticate as <strong>carlos:h5a2xfj8k3</strong></li>
                        <li>Lab is solved when you successfully login as carlos!</li>
                    </ol>
                </div>
            </details>
        </div>

        <div class="action-buttons">
            <a href="setup_db.php" class="btn btn-primary">🗄️ Setup Database</a>
            <a href="login.php" class="btn btn-info">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
        </div>
    </div>
</body>
</html>