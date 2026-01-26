<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDOR PII Leakage</title>
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
            border-bottom: 1px solid rgba(255, 204, 0, 0.3);
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffcc00;
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
        .nav-links a:hover { color: #ffcc00; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
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
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2.5rem;
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .lab-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #ffcc00;
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
            border: 1px solid rgba(255, 204, 0, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .credentials-box h3 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .credential {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-family: monospace;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .credential:last-child { border-bottom: none; }
        .credential .role { color: #888; }
        .credential .creds { color: #88ff88; }
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
            color: #ffcc00;
            font-weight: bold;
        }
        .impact-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .impact-item {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
        }
        .impact-item h4 {
            color: #ff6666;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .impact-item p {
            font-size: 0.85rem;
            margin: 0;
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
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
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
            <a href="index.php" class="logo">📱 MTN MobAd</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
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

    <div class="container">
        <div class="lab-header">
            <span class="lab-badge">Lab 15 - Access Control</span>
            <h1>IDOR PII Leakage</h1>
            <p>Exploit IDOR to access other users' personal information</p>
        </div>

        <div class="section">
            <h2>🎯 Objective</h2>
            <p>
                This lab simulates a vulnerability found in MTN MobAd platform's <code>/api/getUserNotes</code> 
                endpoint. The API returns user data based on an email parameter without verifying if the 
                requester is authorized to access that email's data.
            </p>
            <p>
                To solve the lab, log in as <code>attacker@example.com</code> and successfully retrieve 
                PII (phone number, address, notes) belonging to another user by exploiting the IDOR 
                vulnerability.
            </p>
        </div>

        <div class="section">
            <h2>🔑 Credentials</h2>
            <div class="credentials-box">
                <h3>Available Accounts:</h3>
                <div class="credential">
                    <span class="role">Attacker (Use this):</span>
                    <span class="creds">attacker@example.com / attacker123</span>
                </div>
                <div class="credential">
                    <span class="role">Victim 1 (Business):</span>
                    <span class="creds">victim1@mtnbusiness.com / victim123</span>
                </div>
                <div class="credential">
                    <span class="role">Victim 2 (CEO):</span>
                    <span class="creds">ceo@bigcorp.ng / ceo2024secure</span>
                </div>
                <div class="credential">
                    <span class="role">Victim 3 (Finance):</span>
                    <span class="creds">finance@acme.com.ng / finance@2024</span>
                </div>
                <div class="credential">
                    <span class="role">Admin:</span>
                    <span class="creds">admin@mtnmobad.com / admin@mtn2024!</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>💡 Solution Walkthrough</h2>
            <ol class="step-list">
                <li>Log in as the attacker using <code>attacker@example.com:attacker123</code></li>
                <li>Navigate to the Dashboard and find the API test section</li>
                <li>Click "Test API Endpoint" or go to <code>api-test.php</code></li>
                <li>Note the default request uses your own email address</li>
                <li>Change the <code>userEmail</code> parameter to a victim's email (e.g., <code>victim1@mtnbusiness.com</code>)</li>
                <li>Submit the request and observe the victim's PII in the response</li>
            </ol>
            
            <div class="hint-box">
                <h3>🔍 Key Insight</h3>
                <p>
                    The API only checks if you're logged in (authenticated), but never verifies if you're 
                    <strong>authorized</strong> to view the requested email's data. This is the classic 
                    IDOR pattern - authentication without authorization!
                </p>
            </div>

            <h3 style="color: #ffcc00; margin: 1.5rem 0 0.5rem;">Vulnerable Request:</h3>
            <div class="code-block">POST /api/getUserNotes.php HTTP/1.1
Content-Type: application/json
Cookie: PHPSESSID=[your_session]

{
  "params": {
    "updates": [{
      "param": "user",
      "value": { "userEmail": "victim1@mtnbusiness.com" },
      "op": "a"
    }]
  }
}</div>
        </div>

        <div class="section">
            <h2>💥 Impact</h2>
            <p>
                This vulnerability enables mass enumeration of user PII:
            </p>
            <div class="impact-list">
                <div class="impact-item">
                    <h4>Phone Numbers</h4>
                    <p>Personal and business phone numbers exposed</p>
                </div>
                <div class="impact-item">
                    <h4>Physical Addresses</h4>
                    <p>Home and office addresses leaked</p>
                </div>
                <div class="impact-item">
                    <h4>Financial Data</h4>
                    <p>Tax IDs, bank accounts, billing info</p>
                </div>
                <div class="impact-item">
                    <h4>Private Notes</h4>
                    <p>Confidential business memos and secrets</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📚 Learn More</h2>
            <p>
                This vulnerability is an example of Insecure Direct Object Reference (IDOR) combined 
                with improper access control. The application uses email as a direct reference to 
                user data without validating ownership.
            </p>
            <ul>
                <li>Read the comprehensive documentation for in-depth analysis</li>
                <li>Understand why checking authentication is not enough</li>
                <li>Learn how to properly implement authorization checks</li>
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
