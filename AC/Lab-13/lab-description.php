<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Referer Lab</title>
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
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
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
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #ff6666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            margin: 1rem 0 1rem 1.5rem;
            color: #ccc;
        }
        .section li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .credentials-box {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .credentials-box h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .credential {
            display: flex;
            gap: 2rem;
            padding: 0.5rem 0;
            font-family: monospace;
            color: #88ff88;
        }
        .hint-box {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .hint-box h3 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            color: #88ff88;
            margin: 1rem 0;
        }
        .step-list {
            counter-reset: step;
        }
        .step-list li {
            counter-increment: step;
            padding-left: 0.5rem;
        }
        .step-list li::marker {
            content: counter(step) ". ";
            color: #ff4444;
            font-weight: bold;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
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
            <span class="lab-badge">Lab 13 - Access Control</span>
            <h1>Referer-based Access Control</h1>
            <p>Exploit flawed header-based authorization</p>
        </div>

        <div class="section">
            <h2>🎯 Objective</h2>
            <p>
                This lab controls access to certain admin functionality based on the <strong>Referer header</strong>. 
                You can familiarize yourself with the admin panel by logging in using the credentials 
                <code>administrator:admin</code>.
            </p>
            <p>
                To solve the lab, log in using the credentials <code>wiener:peter</code> and exploit 
                the flawed access controls to <strong>promote yourself to become an administrator</strong>.
            </p>
        </div>

        <div class="section">
            <h2>🔑 Credentials</h2>
            <div class="credentials-box">
                <h3>Available Accounts:</h3>
                <div class="credential">
                    <span>Administrator:</span>
                    <span>administrator : admin</span>
                </div>
                <div class="credential">
                    <span>Regular User:</span>
                    <span>wiener : peter</span>
                </div>
                <div class="credential">
                    <span>Other User:</span>
                    <span>carlos : montoya</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>💡 Solution Walkthrough</h2>
            <ol class="step-list">
                <li>Log in using the admin credentials: <code>administrator:admin</code></li>
                <li>Browse to the admin panel and click to promote carlos (or any user)</li>
                <li>Send the HTTP request to Burp Repeater and observe the Referer header</li>
                <li>Open a private/incognito browser window and log in with <code>wiener:peter</code></li>
                <li>Try to browse directly to <code>/admin-roles.php?username=carlos&action=upgrade</code> - observe it fails due to missing Referer header</li>
                <li>Copy wiener's session cookie (PHPSESSID) from the browser</li>
                <li>In Burp Repeater, replace the admin's session cookie with wiener's cookie</li>
                <li>Change the username parameter to <code>wiener</code></li>
                <li>Send the request - the Referer header tricks the server into allowing the action!</li>
            </ol>
            
            <div class="hint-box">
                <h3>🔍 Key Insight</h3>
                <p>
                    The Referer header is client-controlled and can be trivially spoofed. The server 
                    trusts that if the Referer contains '/admin', the request must have come from 
                    an authorized admin session - but it never verifies the user's actual role!
                </p>
            </div>

            <h3 style="color: #ff6666; margin: 1.5rem 0 0.5rem;">Exploit Request:</h3>
            <div class="code-block">GET /AC/lab13/admin-roles.php?username=wiener&action=upgrade HTTP/1.1
Host: localhost
Cookie: PHPSESSID=[wiener's-session-cookie]
Referer: http://localhost/AC/lab13/admin</div>
        </div>

        <div class="section">
            <h2>📚 Learn More</h2>
            <p>
                The Referer header was never designed for security purposes. It indicates where a 
                request originated from, but this information is entirely under the client's control.
            </p>
            <ul>
                <li>Read the comprehensive documentation for in-depth analysis</li>
                <li>Understand why header-based access control is fundamentally flawed</li>
                <li>Learn about proper authorization patterns</li>
            </ul>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>
</body>
</html>
