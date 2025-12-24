<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDOR Slowvote Bypass</title>
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
            font-size: 1.3rem;
            font-weight: bold;
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #9370DB; }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #9370DB;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .objective-card {
            background: rgba(147, 112, 219, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .objective-card h2 {
            color: #9370DB;
            margin-bottom: 1rem;
        }
        .objective-card p { line-height: 1.8; }
        .credentials-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .credentials-section h2 {
            color: #9370DB;
            margin-bottom: 1rem;
        }
        .credential-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .credential-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
        }
        .credential-card h4 {
            color: #9370DB;
            margin-bottom: 0.75rem;
        }
        .credential-card .role {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        .role.creator { background: rgba(0, 200, 0, 0.2); color: #66ff66; }
        .role.no-access { background: rgba(255, 68, 68, 0.2); color: #ff6666; }
        .role.has-access { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .role.admin { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
        .credential-card code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 0.5rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            color: #88ff88;
        }
        .steps-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .steps-section h2 {
            color: #9370DB;
            margin-bottom: 1.5rem;
        }
        .step {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(106, 90, 205, 0.2);
        }
        .step:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .step-num {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .step-content h4 {
            color: #9370DB;
            margin-bottom: 0.5rem;
        }
        .step-content p { color: #aaa; line-height: 1.6; }
        .hint-box {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .hint-box code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 0.75rem;
            border-radius: 5px;
            color: #88ff88;
            font-family: monospace;
            margin-top: 0.5rem;
        }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
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
        .btn:hover { transform: translateY(-3px); }
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
                <a href="index.php">Lab Home</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>📋 Lab 16: IDOR Slowvote Visibility Bypass</h1>
            <p style="color: #888;">Based on CVE-2017-7606 - Phabricator Slowvote API Authorization Bypass</p>
        </div>

        <div class="objective-card">
            <h2>🎯 Objective</h2>
            <p>
                Exploit an Insecure Direct Object Reference (IDOR) vulnerability in the Slowvote API endpoint.
                The web UI properly enforces visibility restrictions on polls, but the API endpoint 
                <strong>/api/slowvote.php</strong> only checks if you're logged in (authenticated) without 
                verifying if you have permission to view specific polls (authorized).
            </p>
            <p style="margin-top: 1rem;">
                Your goal: <strong>Access a poll that is set to "visible to nobody" or "specific users" 
                while logged in as a user who doesn't have permission.</strong>
            </p>
        </div>

        <div class="credentials-section">
            <h2>🔐 Test Credentials</h2>
            <div class="credential-grid">
                <div class="credential-card">
                    <span class="role creator">Poll Creator</span>
                    <h4>alice</h4>
                    <code>alice / alice123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        Creates polls with restricted visibility
                    </p>
                </div>
                <div class="credential-card">
                    <span class="role no-access">No Access (Attacker)</span>
                    <h4>bob</h4>
                    <code>bob / bob123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        Has NO permission to private polls. Use this account!
                    </p>
                </div>
                <div class="credential-card">
                    <span class="role has-access">Has Access</span>
                    <h4>charlie</h4>
                    <code>charlie / charlie123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        Has permission to view some restricted polls
                    </p>
                </div>
                <div class="credential-card">
                    <span class="role admin">Admin</span>
                    <h4>admin</h4>
                    <code>admin / admin123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        System administrator
                    </p>
                </div>
            </div>
        </div>

        <div class="steps-section">
            <h2>📝 Step-by-Step Guide</h2>
            
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-content">
                    <h4>Login as Bob (Unprivileged User)</h4>
                    <p>Login using the credentials bob/bob123. Bob is a regular user who does NOT have permission to view private polls.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-content">
                    <h4>Explore the Dashboard</h4>
                    <p>Visit the dashboard and notice which polls you can see. Private polls (visibility: "nobody") won't appear in the list.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-content">
                    <h4>Try Accessing Private Poll via UI</h4>
                    <p>Try visiting view-poll.php?id=2 directly (Confidential Employee Survey). You'll get "Access Denied" - the UI correctly enforces permissions.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-content">
                    <h4>Discover the Vulnerable API Endpoint</h4>
                    <p>Look for API endpoints in the application. The API at /api/slowvote.php accepts poll_id as a parameter.</p>
                    <div class="hint-box">
                        <strong>💡 Hint:</strong> The API endpoint uses action and poll_id parameters
                        <code>POST /api/slowvote.php?action=info&poll_id=2</code>
                    </div>
                </div>
            </div>
            
            <div class="step">
                <div class="step-num">5</div>
                <div class="step-content">
                    <h4>Exploit the IDOR Vulnerability</h4>
                    <p>Call the API endpoint directly with a private poll_id. The API will return the poll data without checking if you have permission!</p>
                    <div class="hint-box">
                        <strong>🚨 Vulnerability:</strong> The API checks authentication but NOT authorization
                        <code>// API only checks: "Is user logged in?" ✓
// API doesn't check: "Can this user view this poll?" ✗</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Full Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>
</body>
</html>
