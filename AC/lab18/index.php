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
    <title>Lab 18 - IDOR Expire Other User Sessions</title>
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #96bf48;
            text-decoration: none;
        }
        .logo svg {
            width: 36px;
            height: 36px;
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
        .nav-links a:hover { color: #96bf48; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .hero p {
            color: #888;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
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
        .solved-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
        .scenario-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(150, 191, 72, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .scenario-card h2 {
            color: #96bf48;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .scenario-card p {
            color: #aaa;
            line-height: 1.8;
        }
        .attack-flow {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .attack-step {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(150, 191, 72, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        .attack-step::after {
            content: '→';
            position: absolute;
            right: -1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #96bf48;
            font-size: 1.5rem;
        }
        .attack-step:last-child::after { display: none; }
        .step-num {
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-weight: bold;
        }
        .attack-step h4 { color: #96bf48; margin-bottom: 0.5rem; font-size: 0.95rem; }
        .attack-step p { color: #888; font-size: 0.8rem; }
        .credential-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .credential-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(150, 191, 72, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
        }
        .credential-card h4 {
            color: #96bf48;
            margin-bottom: 0.5rem;
        }
        .credential-card .role {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 5px;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .role.victim { background: rgba(255, 68, 68, 0.2); color: #ff6666; }
        .role.attacker { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .role.admin { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
        .credential-card code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 0.5rem;
            border-radius: 5px;
            color: #88ff88;
            margin-top: 0.5rem;
        }
        .vulnerable-request {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .vulnerable-request .comment { color: #666; }
        .vulnerable-request .param { color: #ff6666; }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-3px); }
        .impact-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .impact-box h4 { color: #ff6666; margin-bottom: 0.5rem; }
        .impact-box ul {
            list-style: none;
            padding: 0;
        }
        .impact-box li {
            padding: 0.3rem 0;
            color: #ccc;
        }
        .impact-box li::before {
            content: '⚠️ ';
        }
        @media (max-width: 768px) {
            .attack-step::after { display: none; }
            .hero h1 { font-size: 1.8rem; }
        }
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
                <a href="../index.php">← All Labs</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="hero">
            <span class="lab-badge">Lab 18 • Practitioner</span>
            <h1>🔐 IDOR Expire Other User Sessions</h1>
            <p>Exploit a session management vulnerability to forcefully terminate other users' sessions by manipulating the account_id parameter.</p>
        </div>

        <?php if ($labSolved): ?>
        <div class="solved-banner">
            <h3>🎉 Lab Solved!</h3>
            <p>You've successfully exploited the IDOR vulnerability!</p>
        </div>
        <?php endif; ?>

        <div class="scenario-card">
            <h2>🎯 Lab Scenario</h2>
            <p>
                This lab simulates a <strong>Shopify-like</strong> admin panel with session management features. 
                Store owners can view their active sessions and use the "Expire All Sessions" feature for security.
                However, the API endpoint <code>/api/expire_sessions.php</code> contains an IDOR vulnerability - 
                the <code>account_id</code> parameter is directly used without verifying that the requesting user 
                actually owns that account.
            </p>
            
            <div class="impact-box">
                <h4>Impact of This Vulnerability:</h4>
                <ul>
                    <li>Force logout any user from all their devices</li>
                    <li>Denial of Service against store owners</li>
                    <li>Business disruption during critical periods</li>
                    <li>Combined with other attacks for account takeover</li>
                </ul>
            </div>
        </div>

        <div class="scenario-card">
            <h2>🔓 Attack Flow</h2>
            <div class="attack-flow">
                <div class="attack-step">
                    <div class="step-num">1</div>
                    <h4>Login</h4>
                    <p>As attacker_store</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">2</div>
                    <h4>Navigate</h4>
                    <p>Account Settings</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">3</div>
                    <h4>Intercept</h4>
                    <p>Expire request</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">4</div>
                    <h4>Modify</h4>
                    <p>account_id → 2</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">5</div>
                    <h4>Exploit</h4>
                    <p>Sessions expired!</p>
                </div>
            </div>
        </div>

        <div class="scenario-card">
            <h2>🔑 Test Credentials</h2>
            <div class="credential-grid">
                <div class="credential-card">
                    <h4>victim_store</h4>
                    <span class="role victim">🎯 Target</span>
                    <code>victim123</code>
                    <p style="color:#888;font-size:0.8rem;margin-top:0.5rem;">User ID: 2</p>
                </div>
                <div class="credential-card">
                    <h4>attacker_store</h4>
                    <span class="role attacker">⚔️ Attacker</span>
                    <code>attacker123</code>
                    <p style="color:#888;font-size:0.8rem;margin-top:0.5rem;">User ID: 3</p>
                </div>
                <div class="credential-card">
                    <h4>admin</h4>
                    <span class="role admin">👑 Admin</span>
                    <code>admin123</code>
                    <p style="color:#888;font-size:0.8rem;margin-top:0.5rem;">User ID: 1</p>
                </div>
            </div>
        </div>

        <div class="scenario-card">
            <h2>📝 Vulnerable Request</h2>
            <div class="vulnerable-request">
<span class="comment">// Normal request (your sessions)</span>
POST /AC/lab18/api/expire_sessions.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

account_id=3&action=expire_all

<span class="comment">// Modified request (victim's sessions)</span>
POST /AC/lab18/api/expire_sessions.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

account_id=<span class="param">2</span>&action=expire_all  <span class="comment">← Changed to victim's ID!</span>
            </div>
        </div>

        <div class="actions">
            <a href="lab-description.php" class="btn btn-primary">📋 View Lab Guide</a>
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← Back to Labs</a>
        </div>
    </div>
</body>
</html>
