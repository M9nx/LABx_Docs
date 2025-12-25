<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 16 - IDOR Slowvote Visibility Bypass</title>
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
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #9370DB; }
        .hero {
            text-align: center;
            padding: 4rem 2rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #9370DB, #BA55D3);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            color: #888;
            font-size: 1.2rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
        }
        .attack-flow {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .attack-flow h2 {
            color: #9370DB;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .flow-steps {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .flow-step {
            flex: 1;
            min-width: 200px;
            background: rgba(106, 90, 205, 0.1);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }
        .flow-step-number {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-weight: bold;
        }
        .flow-step h3 {
            color: #BA55D3;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .flow-step p {
            color: #888;
            font-size: 0.85rem;
        }
        .vulnerable-request {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .vulnerable-request h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .code-block {
            background: #0d0d0d;
            border-radius: 10px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            overflow-x: auto;
            color: #88ff88;
        }
        .code-block .comment { color: #666; }
        .code-block .param { color: #ff79c6; }
        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .user-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
        }
        .user-card h3 {
            color: #9370DB;
            margin-bottom: 0.5rem;
        }
        .user-card .role {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .user-card .role.creator {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
        }
        .user-card .role.no-access {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
        }
        .user-card .role.has-access {
            background: rgba(0, 200, 255, 0.2);
            color: #00ccff;
        }
        .user-card .creds {
            font-family: monospace;
            color: #aaa;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
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
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(106, 90, 205, 0.3);
        }
        .info-box {
            background: rgba(106, 90, 205, 0.1);
            border: 1px solid rgba(106, 90, 205, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-box h4 {
            color: #9370DB;
            margin-bottom: 0.5rem;
        }
        .info-box p {
            color: #aaa;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">P</span>
                Phabricator
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="hero">
        <span class="lab-badge">Lab 16 - Access Control</span>
        <h1>🗳️ IDOR Slowvote Visibility Bypass</h1>
        <p>
            This lab simulates a vulnerability in Phabricator's Slowvote feature. Users can create 
            polls with restricted visibility, but the API endpoint fails to enforce these restrictions, 
            allowing any authenticated user to view private polls.
        </p>
    </div>

    <div class="container">
        <div class="attack-flow">
            <h2>🎯 Attack Flow</h2>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="flow-step-number">1</div>
                    <h3>Alice Creates Poll</h3>
                    <p>Sets visibility to "No One" or specific users only</p>
                </div>
                <div class="flow-step">
                    <div class="flow-step-number">2</div>
                    <h3>Bob Tries UI Access</h3>
                    <p>Visits /V2 and gets "Access Denied"</p>
                </div>
                <div class="flow-step">
                    <div class="flow-step-number">3</div>
                    <h3>Bob Calls API</h3>
                    <p>POST to /api/slowvote.php with poll_id</p>
                </div>
                <div class="flow-step">
                    <div class="flow-step-number">4</div>
                    <h3>Data Leaked!</h3>
                    <p>API returns poll data without checking permissions</p>
                </div>
            </div>
        </div>

        <div class="vulnerable-request">
            <h3>⚠️ Vulnerable API Request</h3>
            <div class="code-block">
POST /api/slowvote.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=your_session_cookie

<span class="param">action</span>=info&<span class="param">poll_id</span>=2&output=json

<span class="comment">// Change poll_id to access ANY poll regardless of visibility!</span>
            </div>
        </div>

        <div class="info-box">
            <h4>💡 The Vulnerability</h4>
            <p>
                The web UI properly checks if the current user has permission to view a poll. However, 
                the <code>/api/slowvote.php</code> endpoint only verifies that the user is logged in 
                (authenticated) but never checks if they're authorized to view the specific poll. This 
                is a classic case of authentication without authorization.
            </p>
        </div>

        <h2 style="color: #9370DB; margin-bottom: 1.5rem;">🔑 Test Accounts</h2>
        <div class="users-grid">
            <div class="user-card">
                <h3>👩 Alice (User A)</h3>
                <span class="role creator">Poll Creator</span>
                <p class="creds">Username: alice<br>Password: alice123</p>
                <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                    Creates private polls with restricted visibility
                </p>
            </div>
            <div class="user-card">
                <h3>👨 Bob (User B)</h3>
                <span class="role no-access">No Permission</span>
                <p class="creds">Username: bob<br>Password: bob123</p>
                <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                    Cannot see private polls - use this account to exploit!
                </p>
            </div>
            <div class="user-card">
                <h3>👤 Charlie (User C)</h3>
                <span class="role has-access">Has Permission</span>
                <p class="creds">Username: charlie<br>Password: charlie123</p>
                <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                    Explicitly granted access to some private polls
                </p>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">Start Lab →</a>
            <a href="lab-description.php" class="btn btn-secondary">Lab Description</a>
            <a href="docs.php" class="btn btn-secondary">Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
        </div>
    </div>
</body>
</html>
