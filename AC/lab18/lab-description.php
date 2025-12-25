<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(18);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDOR Expire Sessions</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(150, 191, 72, 0.3);
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #96bf48;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #96bf48; }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .breadcrumb a { color: #96bf48; text-decoration: none; }
        .lab-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .lab-badge {
            display: inline-block;
            background: rgba(150, 191, 72, 0.2);
            color: #96bf48;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2rem;
            color: #96bf48;
            margin-bottom: 0.5rem;
        }
        .lab-header p { color: #888; }
        .solved-badge {
            display: inline-block;
            background: rgba(0, 200, 100, 0.2);
            color: #66ff99;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-top: 1rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(150, 191, 72, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #96bf48;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card p, .card li {
            color: #aaa;
            line-height: 1.8;
        }
        .objective-box {
            background: rgba(150, 191, 72, 0.1);
            border-left: 4px solid #96bf48;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
        }
        .objective-box h3 { color: #96bf48; margin-bottom: 0.5rem; }
        .credentials-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .credentials-table th, .credentials-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .credentials-table th { color: #96bf48; }
        .credentials-table code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .role-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .role-badge.victim { background: rgba(255, 68, 68, 0.2); color: #ff8888; }
        .role-badge.attacker { background: rgba(255, 170, 0, 0.2); color: #ffcc00; }
        .role-badge.admin { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
        .steps-list {
            counter-reset: step;
            list-style: none;
            padding: 0;
        }
        .steps-list li {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.5rem;
        }
        .steps-list li::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
            margin: 0.75rem 0;
        }
        .vulnerable { color: #ff6666; }
        .hint-box {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .hint-box h4 { color: #ffaa00; margin-bottom: 0.5rem; }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #96bf48, #5c6ac4); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn:hover { transform: translateY(-3px); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 109 124" fill="none">
                    <path d="M74.7 14.8L62.2 55.4H46.7L34.2 14.8C33.1 11 29.5 8.3 25.5 8.3H0L31.5 115.5H40.8L54.5 67.8L68.2 115.5H77.5L109 8.3H83.5C79.5 8.3 75.8 11 74.7 14.8Z" fill="#96bf48"/>
                </svg>
                Shopify Admin
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="../index.php">Labs</a> / <a href="index.php">Lab 18</a> / Description
        </div>

        <div class="lab-header">
            <span class="lab-badge">Lab 18 • Practitioner • IDOR</span>
            <h1>IDOR Expire Other User Sessions</h1>
            <p>Exploit a session management vulnerability to force logout other users</p>
            <?php if ($labSolved): ?>
            <span class="solved-badge">✓ Solved</span>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>🎯 Lab Objective</h2>
            <div class="objective-box">
                <h3>Your Mission</h3>
                <p>
                    Exploit the IDOR vulnerability in the session expiration API to terminate 
                    <strong>victim_store's</strong> active sessions. You must force the victim 
                    to be logged out by manipulating the <code>account_id</code> parameter.
                </p>
            </div>
            <p>
                This lab simulates a real Shopify vulnerability where the session management 
                endpoint did not validate that the requested account belongs to the authenticated user.
            </p>
        </div>

        <div class="card">
            <h2>🔑 Test Credentials</h2>
            <table class="credentials-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>ID</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>victim_store</code></td>
                        <td><code>victim123</code></td>
                        <td><span class="role-badge victim">🎯 Target</span></td>
                        <td><strong>2</strong></td>
                    </tr>
                    <tr>
                        <td><code>attacker_store</code></td>
                        <td><code>attacker123</code></td>
                        <td><span class="role-badge attacker">⚔️ Use This</span></td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td><code>admin</code></td>
                        <td><code>admin123</code></td>
                        <td><span class="role-badge admin">Admin</span></td>
                        <td>1</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>📝 Step-by-Step Guide</h2>
            <ol class="steps-list">
                <li>
                    <strong>Login as the attacker</strong>
                    <p>Use <code>attacker_store</code> / <code>attacker123</code></p>
                </li>
                <li>
                    <strong>Navigate to Account Settings</strong>
                    <p>Go to Dashboard → Settings → Security section</p>
                </li>
                <li>
                    <strong>Open Developer Tools (F12)</strong>
                    <p>Switch to the Network tab to monitor requests</p>
                </li>
                <li>
                    <strong>Find the hidden field</strong>
                    <p>In Elements tab, locate: <code>&lt;input type="hidden" name="account_id" value="3"&gt;</code></p>
                </li>
                <li>
                    <strong>Modify the account_id</strong>
                    <p>Change the value from <code>3</code> to <code class="vulnerable">2</code> (victim's ID)</p>
                </li>
                <li>
                    <strong>Click "Expire All Sessions"</strong>
                    <p>The request will be sent with the modified parameter</p>
                </li>
                <li>
                    <strong>Verify the attack</strong>
                    <p>Check if the server confirms the victim's sessions were expired</p>
                </li>
            </ol>
        </div>

        <div class="card">
            <h2>📡 Vulnerable Request</h2>
            <div class="code-block">
POST /AC/lab18/api/expire_sessions.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=your_session_cookie

account_id=<span class="vulnerable">2</span>&action=expire_all
            </div>
            
            <div class="hint-box">
                <h4>💡 Alternative Methods</h4>
                <p>You can also exploit this using:</p>
                <ul style="margin-top:0.5rem;">
                    <li><strong>Burp Suite:</strong> Intercept and modify the POST request</li>
                    <li><strong>Browser Console:</strong> Use fetch() to send the modified request</li>
                    <li><strong>cURL:</strong> Command-line HTTP request with custom parameters</li>
                </ul>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="success.php" class="btn btn-secondary">🏆 Check Solution</a>
            <a href="index.php" class="btn btn-secondary">← Back</a>
        </div>
    </div>
</body>
</html>
