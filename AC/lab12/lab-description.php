<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - MultiStep Admin</title>
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
        .section ul {
            list-style: none;
            padding: 0;
        }
        .section li {
            padding: 0.8rem 0;
            padding-left: 2rem;
            position: relative;
            color: #ccc;
        }
        .section li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #ff4444;
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
        .hint-box p {
            color: #ccc;
        }
        .code-block {
            background: #1a1a1a;
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
        }
        .step-list li::before {
            content: counter(step) ".";
            background: #ff4444;
            color: white;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
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
        .flow-diagram {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin: 1.5rem 0;
            flex-wrap: wrap;
        }
        .flow-step {
            padding: 0.8rem 1.2rem;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .flow-step.vulnerable {
            border-color: #ff4444;
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
        }
        .flow-arrow {
            color: #666;
            font-size: 1.2rem;
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
            <span class="lab-badge">Lab 12 - Access Control</span>
            <h1>Multi-Step Process Bypass</h1>
            <p>Exploit missing access control on confirmation step</p>
        </div>

        <div class="section">
            <h2>🎯 Objective</h2>
            <p>
                This lab implements a multi-step administrative process for changing user roles.
                Steps 1 and 2 correctly verify that the user has admin privileges, but <strong>Step 3 
                (the confirmation step) fails to validate authorization</strong>.
            </p>
            <p>
                To solve this lab, log in as <strong>wiener</strong> and exploit the vulnerability
                to upgrade your account to administrator.
            </p>
        </div>

        <div class="section">
            <h2>🔄 Process Flow</h2>
            <p>The multi-step admin process works as follows:</p>
            <div class="flow-diagram">
                <div class="flow-step">Step 1<br><small>Select User</small><br>✅ Protected</div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">Step 2<br><small>Choose Role</small><br>✅ Protected</div>
                <span class="flow-arrow">→</span>
                <div class="flow-step vulnerable">Step 3<br><small>Confirm</small><br>⚠️ VULNERABLE</div>
            </div>
            <p style="text-align: center; color: #ff6666; margin-top: 1rem;">
                The confirmation endpoint doesn't verify if the requester is an admin!
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
                    <span>Regular User:</span>
                    <span>carlos : montoya</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>💡 Solution Walkthrough</h2>
            <ul class="step-list">
                <li>Log in as <code>wiener:peter</code></li>
                <li>Observe that the admin panel is not accessible (Steps 1 & 2 are protected)</li>
                <li>Identify the confirmation endpoint: <code>admin-confirm.php</code></li>
                <li>Use Burp Suite or a tool like curl to send a direct POST request</li>
                <li>Craft the payload with your username and the admin role</li>
                <li>Submit the request to bypass the multi-step verification</li>
            </ul>
            
            <div class="hint-box">
                <h3>🔍 Key Insight</h3>
                <p>
                    The confirmation step expects requests to come from Step 2, so it assumes authorization
                    was already verified. This is a classic example of broken access control in multi-step processes.
                </p>
            </div>

            <h3 style="color: #ff6666; margin: 1.5rem 0 1rem;">Exploitation Request:</h3>
            <div class="code-block">POST /AC/lab12/admin-confirm.php HTTP/1.1
Host: localhost
Cookie: PHPSESSID=[your-session-cookie]
Content-Type: application/x-www-form-urlencoded

username=wiener&role=admin&action=upgrade&confirmed=true</div>
        </div>

        <div class="section">
            <h2>📚 Learn More</h2>
            <p>
                Multi-step processes are common in administrative functions where sensitive actions
                require multiple confirmations. If any step in the process doesn't properly verify
                authorization, an attacker can skip directly to that step.
            </p>
            <ul>
                <li>Read the comprehensive documentation for in-depth analysis</li>
                <li>Understand how to prevent multi-step access control vulnerabilities</li>
                <li>Learn about other access control bypass techniques</li>
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
