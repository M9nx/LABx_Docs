<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - URL-based Access Control Bypass</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .lab-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .lab-header h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .lab-header .difficulty {
            display: inline-block;
            padding: 0.4rem 1.2rem;
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .lab-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .lab-section h2 {
            color: #ff6666;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .lab-section p {
            color: #b0b0b0;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .lab-section ul, .lab-section ol {
            color: #b0b0b0;
            line-height: 1.8;
            margin-left: 1.5rem;
        }
        .lab-section li {
            margin-bottom: 0.5rem;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            color: #ff6666;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }
        .warning-box {
            background: rgba(255, 200, 68, 0.1);
            border: 1px solid rgba(255, 200, 68, 0.3);
            border-radius: 10px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #ffcc44;
            margin-bottom: 0.5rem;
        }
        .warning-box p {
            color: #ddcc88;
            margin: 0;
        }
        .credentials-box {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
            border-radius: 10px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .credentials-box h4 {
            color: #00c864;
            margin-bottom: 0.5rem;
        }
        .credentials-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #00ff88;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
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
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-header">
            <h1>🏢 Lab 10: URL-based Access Control Bypass</h1>
            <span class="difficulty">🟠 Practitioner</span>
        </div>

        <div class="lab-section">
            <h2>📋 Description</h2>
            <p>This website has an unauthenticated admin panel at <code style="background: rgba(0,0,0,0.3); padding: 0.2rem 0.5rem; border-radius: 4px;">/admin</code>, but a front-end system has been configured to block external access to that path.</p>
            <p>However, the back-end application is built on a framework that supports the <strong>X-Original-URL</strong> header.</p>
            
            <div class="warning-box">
                <h4>⚠️ Security Architecture</h4>
                <p>A front-end proxy/WAF blocks direct access to /admin paths, but the back-end application processes URLs from HTTP headers, creating a bypass opportunity.</p>
            </div>
        </div>

        <div class="lab-section">
            <h2>🎯 Objective</h2>
            <p>Access the admin panel and <strong>delete the user carlos</strong>.</p>
            
            <div class="credentials-box">
                <h4>🔑 Test Credentials</h4>
                <p>Username: <code>wiener</code> | Password: <code>peter</code></p>
            </div>
        </div>

        <div class="lab-section">
            <h2>💡 Solution Approach</h2>
            <ol>
                <li>Try to load <code>/admin</code> and observe that you get blocked. Notice that the response is very plain, suggesting it may originate from a front-end system.</li>
                <li>Send the request to Burp Repeater. Change the URL in the request line to <code>/</code> and add the HTTP header <code>X-Original-URL: /invalid</code>. Observe that the application returns a "not found" response. This indicates that the back-end system is processing the URL from the X-Original-URL header.</li>
                <li>Change the value of the X-Original-URL header to <code>/admin</code>. Observe that you can now access the admin page.</li>
                <li>To delete carlos, add <code>?username=carlos</code> to the real query string, and change the X-Original-URL path to <code>/admin/delete</code>.</li>
            </ol>
            
            <div class="code-block">
                <pre>GET /?username=carlos HTTP/1.1
Host: localhost
X-Original-URL: /admin/delete
...</pre>
            </div>
        </div>

        <div class="lab-section">
            <h2>🔬 Key Concepts</h2>
            <ul>
                <li><strong>Front-end vs Back-end</strong> - Security controls at the front-end can differ from back-end processing</li>
                <li><strong>X-Original-URL Header</strong> - Some frameworks (like certain Spring configurations) process this header to determine the actual URL</li>
                <li><strong>X-Rewrite-URL Header</strong> - Similar header that can be used for the same purpose</li>
                <li><strong>Access Control Bypass</strong> - When front-end and back-end disagree on the URL, access controls can be bypassed</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="setup_db.php" class="btn btn-primary">🗄️ Setup Database</a>
            <a href="login.php" class="btn btn-info">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Full Documentation</a>
        </div>
        
        <div class="nav-buttons">
            <a href="../lab9/lab-description.php" class="nav-btn">← Previous Lab</a>
            <a href="../index.php" class="nav-btn">All Labs →</a>
        </div>
    </div>
</body>
</html>
