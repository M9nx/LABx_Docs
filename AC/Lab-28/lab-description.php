<?php
/**
 * Lab 28: Lab Description Page
 * MTN Developers Portal - IDOR Team Member Removal
 * Based on HackerOne Report #1448475
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Lab 28</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 100%);
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
            width: 45px;
            height: 45px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            color: #ffcc00;
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #000;
        }
        .nav-links a {
            color: #000;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
        }
        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #ffcc00; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 8px;
            color: #ffcc00;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(255, 204, 0, 0.2);
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h1, .card h2 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .card h1 { font-size: 1.75rem; }
        .card h2 { font-size: 1.3rem; margin-top: 1.5rem; }
        .card p {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .card ul, .card ol {
            padding-left: 1.5rem;
            color: #aaa;
            line-height: 1.8;
        }
        .card li { margin-bottom: 0.5rem; }
        .report-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .meta-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 8px;
            border-left: 3px solid #ffcc00;
        }
        .meta-item label {
            display: block;
            color: #888;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        .meta-item span {
            color: #fff;
            font-weight: 500;
        }
        .severity-critical {
            color: #ff4444 !important;
        }
        .highlight-box {
            background: rgba(255, 204, 0, 0.1);
            border-left: 4px solid #ffcc00;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .danger-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff4444;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .info-box {
            background: rgba(68, 136, 255, 0.1);
            border-left: 4px solid #4488ff;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .danger-box h3, .highlight-box h3, .info-box h3 {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .danger-box h3 { color: #ff4444; }
        .highlight-box h3 { color: #ffcc00; }
        .info-box h3 { color: #4488ff; }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
        code.block {
            display: block;
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            overflow-x: auto;
            white-space: pre;
            color: #c9d1d9;
            border: 1px solid #30363d;
        }
        .poc-section {
            margin: 1.5rem 0;
        }
        .poc-step {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .poc-step .number {
            width: 32px;
            height: 32px;
            background: #ffcc00;
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .poc-step-content h4 {
            color: #fff;
            margin-bottom: 0.25rem;
        }
        .poc-step-content p {
            margin: 0;
            color: #aaa;
            font-size: 0.95rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 0.5rem 0.5rem 0.5rem 0;
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
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">
            <div class="logo-icon">MTN</div>
            <div class="logo-text">Developers Portal</div>
        </a>
        <nav class="nav-links">
            <a href="index.php">← Back to Lab</a>
            <a href="login.php">Login</a>
            <a href="docs.php">Documentation</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="nav-buttons">
            <a href="index.php" class="nav-btn">← Back to Lab Home</a>
            <a href="docs.php" class="nav-btn">Go to Documentation →</a>
        </div>

        <div class="card">
            <h1>📋 Lab Description: MTN Developers Portal IDOR</h1>
            
            <div class="report-meta">
                <div class="meta-item">
                    <label>Vulnerability Type</label>
                    <span>IDOR + Information Disclosure</span>
                </div>
                <div class="meta-item">
                    <label>Severity</label>
                    <span class="severity-critical">High (7.1 CVSS)</span>
                </div>
                <div class="meta-item">
                    <label>HackerOne Report</label>
                    <span>#1448475</span>
                </div>
                <div class="meta-item">
                    <label>Target</label>
                    <span>developers.mtn.com</span>
                </div>
            </div>

            <h2>📝 Summary</h2>
            <p>
                The MTN Developers Portal is a platform for developers to manage API projects, 
                collaborate with team members, and build applications using MTN APIs. 
                The portal allows users to create teams and manage team memberships.
            </p>
            <p>
                A critical IDOR vulnerability exists in the team member removal functionality. 
                The <code>/api/remove_member.php</code> endpoint accepts <code>team_id</code> and 
                <code>user_id</code> parameters but <strong>fails to verify that the authenticated 
                user has permission to remove members from the specified team</strong>.
            </p>

            <div class="danger-box">
                <h3>🔥 Impact</h3>
                <p>
                    Any authenticated user can remove ANY user (including owners and admins) from 
                    ANY team on the platform. This could lead to:
                </p>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <li>Mass disruption of team operations</li>
                    <li>Denial of service for legitimate team members</li>
                    <li>Information disclosure of user PII and team details</li>
                    <li>Potential takeover of abandoned teams</li>
                </ul>
            </div>

            <h2>🔍 Technical Details</h2>
            <p>The vulnerable endpoint:</p>
            <code class="block">POST /api/team-members/remove
Content-Type: application/json
Authorization: Bearer &lt;any_valid_token&gt;

{
    "team_id": "target_team_uuid",
    "user_id": "target_user_uuid"
}</code>

            <p>The server only checks:</p>
            <ol>
                <li>Is the request authenticated? ✓</li>
                <li>Are both parameters provided? ✓</li>
                <li><strong style="color: #ff4444;">Does the requester have permission to manage this team? ✗ MISSING!</strong></li>
            </ol>

            <h2>🎯 Proof of Concept Scenario</h2>
            
            <div class="highlight-box">
                <h3>Setup</h3>
                <ul>
                    <li><strong>Account A (Attacker):</strong> user_id = 1111, owns Team A (0001)</li>
                    <li><strong>Account B (Victim):</strong> user_id = 1112, owns Team B (0002)</li>
                    <li><strong>Account C (Target):</strong> user_id = 1113, member of Team B</li>
                </ul>
            </div>

            <div class="poc-section">
                <div class="poc-step">
                    <span class="number">1</span>
                    <div class="poc-step-content">
                        <h4>Attacker logs in as Account A</h4>
                        <p>Login with username <code>attacker</code> and password <code>attacker123</code></p>
                    </div>
                </div>
                <div class="poc-step">
                    <span class="number">2</span>
                    <div class="poc-step-content">
                        <h4>Navigate to Team Management</h4>
                        <p>Go to Team A management page and attempt to remove a member</p>
                    </div>
                </div>
                <div class="poc-step">
                    <span class="number">3</span>
                    <div class="poc-step-content">
                        <h4>Intercept the Request</h4>
                        <p>Use Burp Suite to intercept the POST request to <code>/api/remove_member.php</code></p>
                    </div>
                </div>
                <div class="poc-step">
                    <span class="number">4</span>
                    <div class="poc-step-content">
                        <h4>Modify Team ID</h4>
                        <p>Change <code>team_id</code> from <code>0001</code> to <code>0002</code> (Bob's team)</p>
                    </div>
                </div>
                <div class="poc-step">
                    <span class="number">5</span>
                    <div class="poc-step-content">
                        <h4>Modify User ID</h4>
                        <p>Change <code>user_id</code> to <code>1113</code> (Carol's ID)</p>
                    </div>
                </div>
                <div class="poc-step">
                    <span class="number">6</span>
                    <div class="poc-step-content">
                        <h4>Send Modified Request</h4>
                        <p>Forward the request and observe successful removal + information disclosure</p>
                    </div>
                </div>
            </div>

            <h2>💀 Malicious Response (Information Disclosure)</h2>
            <code class="block">{
    "success": true,
    "message": "Member removed successfully",
    "removed_user": {
        "user_id": "1113",
        "username": "carol_admin",
        "full_name": "Carol Administrator",
        "email": "carol@mtn.com"
    },
    "from_team": {
        "team_id": "0002",
        "name": "Team B - API Integration",
        "description": "Bob's team for API integrations"
    }
}</code>

            <div class="info-box">
                <h3>💡 Information Disclosure</h3>
                <p>
                    The response leaks sensitive information about both the removed user AND the team, 
                    including email addresses and team descriptions. This could be used for:
                </p>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <li>Harvesting user email addresses</li>
                    <li>Enumerating team structures</li>
                    <li>Social engineering attacks</li>
                </ul>
            </div>

            <h2>🛡️ Expected Secure Behavior</h2>
            <p>The server should verify:</p>
            <ol>
                <li>The authenticated user is an owner OR admin of the target team</li>
                <li>The user being removed is actually a member of that team</li>
                <li>Owners cannot be removed (must transfer ownership first)</li>
                <li>Response should not leak PII on errors</li>
            </ol>

            <h2>🚀 Start the Lab</h2>
            <p>Ready to exploit the vulnerability?</p>
            <a href="login.php" class="btn">Login to Portal</a>
            <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            <a href="success.php" class="btn btn-secondary">Submit Flag</a>
        </div>

        <div class="nav-buttons">
            <a href="index.php" class="nav-btn">← Back to Lab Home</a>
            <a href="docs.php" class="nav-btn">Go to Documentation →</a>
        </div>
    </main>
</body>
</html>
