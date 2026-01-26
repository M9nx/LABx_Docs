<?php
/**
 * Lab 28: Landing Page
 * MTN Developers Portal - IDOR Team Member Removal
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 28 - MTN Developers Portal IDOR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 50%, #0a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 50px;
            height: 50px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            color: #ffcc00;
        }
        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: #000;
        }
        .logo-text span { font-weight: 400; }
        .nav-links a {
            color: #000;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: rgba(0,0,0,0.1);
        }
        .hero {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(180deg, rgba(255,204,0,0.1) 0%, transparent 100%);
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 0.75rem;
        }
        .hero h1 span { color: #ffcc00; }
        .hero p {
            color: #888;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 900px) {
            .main-content { grid-template-columns: 1fr; }
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 1.75rem;
        }
        .card h2 {
            color: #ffcc00;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card p, .card li {
            color: #aaa;
            line-height: 1.7;
        }
        .card ul, .card ol {
            padding-left: 1.25rem;
            margin: 0.75rem 0;
        }
        .card li { margin-bottom: 0.5rem; }
        .full-width {
            grid-column: 1 / -1;
        }
        .accounts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .account-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.15);
            border-radius: 8px;
            padding: 1rem;
        }
        .account-card.attacker {
            border-color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
        }
        .account-card.victim {
            border-color: #4488ff;
            background: rgba(68, 136, 255, 0.1);
        }
        .account-card h4 {
            color: #fff;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .account-card code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }
        .account-card p {
            font-size: 0.85rem;
            margin: 0.3rem 0;
        }
        .highlight-box {
            background: rgba(255, 204, 0, 0.1);
            border-left: 4px solid #ffcc00;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight-box h3 {
            color: #ffcc00;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .danger-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff4444;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .danger-box h3 {
            color: #ff4444;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 0.5rem;
            margin-right: 0.5rem;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 204, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ffcc00;
            color: #ffcc00;
        }
        .btn-secondary:hover {
            background: rgba(255, 204, 0, 0.1);
            box-shadow: none;
        }
        .attack-flow {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin: 1rem 0;
        }
        .flow-step {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            border-left: 3px solid #ffcc00;
        }
        .flow-step .number {
            width: 28px;
            height: 28px;
            background: #ffcc00;
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .flow-step p {
            margin: 0;
            color: #ccc;
        }
        code.block {
            display: block;
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            margin: 0.75rem 0;
            overflow-x: auto;
            white-space: pre;
            color: #c9d1d9;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            border: 1px solid #30363d;
        }
        .footer {
            text-align: center;
            padding: 2rem;
            color: #555;
            border-top: 1px solid rgba(255, 204, 0, 0.1);
            margin-top: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .back-link:hover { color: #ffcc00; }
    </style>
</head>
<body>
    <header class="header">
        <a href="#" class="logo">
            <div class="logo-icon">MTN</div>
            <div class="logo-text">Developers <span>Portal</span></div>
        </a>
        <nav class="nav-links">
            <a href="../">← All Labs</a>
            <a href="login.php">Login</a>
            <a href="lab-description.php">Description</a>
            <a href="docs.php">Docs</a>
        </nav>
    </header>

    <section class="hero">
        <div class="lab-badge">Lab 28 • IDOR + Information Disclosure</div>
        <h1>Remove Every User, Admin, and Owner<br>from <span>Any Team</span></h1>
        <p>Exploit an IDOR vulnerability in the team member removal API to remove any user from any team, regardless of your permissions.</p>
    </section>

    <main class="main-content">
        <a href="../" class="back-link full-width">← Back to All Labs</a>

        <div class="card">
            <h2>📖 Scenario Overview</h2>
            <p>
                The MTN Developers Portal allows teams to collaborate on API projects. 
                Team owners and admins can remove members from their teams through the portal.
            </p>
            <p>
                However, the <code>remove_member</code> API endpoint fails to verify that the 
                requester has permission to modify the target team, leading to a critical 
                <strong>IDOR vulnerability</strong>.
            </p>
            
            <div class="danger-box">
                <h3>🔥 The Vulnerability</h3>
                <p>Any authenticated user can remove ANY user from ANY team by manipulating 
                the <code>team_id</code> and <code>user_id</code> parameters.</p>
            </div>
        </div>

        <div class="card">
            <h2>🎯 Your Objective</h2>
            <ol>
                <li>Login as the <strong>attacker</strong> account</li>
                <li>Navigate to your team management page</li>
                <li>Intercept the "Remove Member" request</li>
                <li>Change <code>team_id</code> to Bob's team (<code>0002</code>)</li>
                <li>Change <code>user_id</code> to Carol (<code>1113</code>)</li>
                <li>Successfully remove Carol from a team you don't own!</li>
            </ol>
            
            <div class="highlight-box">
                <h3>💡 Information Disclosure Bonus</h3>
                <p>The API response leaks sensitive data: username, full name, email, and team details!</p>
            </div>
        </div>

        <div class="card full-width">
            <h2>🔐 Test Accounts</h2>
            <div class="accounts-grid">
                <div class="account-card attacker">
                    <h4>👹 Attacker (Account A)</h4>
                    <p>Username: <code>attacker</code></p>
                    <p>Password: <code>attacker123</code></p>
                    <p>User ID: <code>1111</code></p>
                    <p style="color: #ff6b6b;">Owns Team A (0001)</p>
                </div>
                <div class="account-card victim">
                    <h4>👤 Bob (Account B - Team Owner)</h4>
                    <p>Username: <code>bob_dev</code></p>
                    <p>Password: <code>bob123</code></p>
                    <p>User ID: <code>1112</code></p>
                    <p style="color: #4dabf7;">Owns Team B (0002)</p>
                </div>
                <div class="account-card victim">
                    <h4>👤 Carol (Account C - Target)</h4>
                    <p>Username: <code>carol_admin</code></p>
                    <p>Password: <code>carol123</code></p>
                    <p>User ID: <code>1113</code></p>
                    <p style="color: #4dabf7;">Member of Team B</p>
                </div>
            </div>
        </div>

        <div class="card full-width">
            <h2>⚡ Attack Flow</h2>
            <div class="attack-flow">
                <div class="flow-step">
                    <span class="number">1</span>
                    <p>Login as <code>attacker</code> and go to Team A (0001) management</p>
                </div>
                <div class="flow-step">
                    <span class="number">2</span>
                    <p>Click "Remove" on any member in YOUR team and intercept with Burp Suite</p>
                </div>
                <div class="flow-step">
                    <span class="number">3</span>
                    <p>Modify the request: change <code>team_id=0001</code> to <code>team_id=0002</code></p>
                </div>
                <div class="flow-step">
                    <span class="number">4</span>
                    <p>Modify the request: change <code>user_id</code> to <code>1113</code> (Carol)</p>
                </div>
                <div class="flow-step">
                    <span class="number">5</span>
                    <p>Send the modified request and observe the successful removal + leaked PII</p>
                </div>
            </div>

            <p><strong>Vulnerable Request:</strong></p>
            <code class="block">POST /Lab-28/api/remove_member.php HTTP/1.1
Content-Type: application/json
Cookie: PHPSESSID=...

{
    "team_id": "0002",
    "user_id": "1113"
}</code>
        </div>

        <div class="card full-width">
            <h2>🚀 Get Started</h2>
            <p>Ready to exploit the vulnerability? Click below to begin:</p>
            <br>
            <a href="login.php" class="btn">Login to Portal</a>
            <a href="lab-description.php" class="btn btn-secondary">Lab Description</a>
            <a href="docs.php" class="btn btn-secondary">Documentation</a>
            <a href="success.php" class="btn btn-secondary">Submit Flag</a>
        </div>
    </main>

    <footer class="footer">
        <p>Lab 28 - MTN Developers Portal IDOR | Based on HackerOne Report #1448475</p>
        <p style="margin-top: 0.5rem;">For educational purposes only.</p>
    </footer>
</body>
</html>
