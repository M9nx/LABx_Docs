<?php
// Lab 21: Lab Description - Step-by-Step Exploitation Guide
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Instructions - IDOR Column Settings | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .nav-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .nav-top a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-top a:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }
        .lab-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .lab-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 50px;
            color: #f87171;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .lab-header p {
            color: #94a3b8;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        .objective-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .objective-box h2 {
            color: #a5b4fc;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .objective-box p {
            color: #e2e8f0;
            font-size: 1.05rem;
            line-height: 1.7;
        }
        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .credential-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .credential-card.victim {
            border-color: rgba(239, 68, 68, 0.4);
            background: rgba(239, 68, 68, 0.05);
        }
        .credential-card.attacker {
            border-color: rgba(245, 158, 11, 0.4);
            background: rgba(245, 158, 11, 0.05);
        }
        .credential-card h3 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .credential-card.victim h3 { color: #f87171; }
        .credential-card.attacker h3 { color: #fbbf24; }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(99, 102, 241, 0.15);
            font-size: 0.9rem;
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { color: #94a3b8; }
        .cred-value { color: #e2e8f0; font-family: 'Consolas', monospace; }
        .steps-section {
            margin-bottom: 2rem;
        }
        .steps-section h2 {
            color: #e2e8f0;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .step-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .step-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .step-number {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .step-content h3 {
            color: #a5b4fc;
            margin-bottom: 0.25rem;
        }
        .step-content p {
            color: #94a3b8;
            font-size: 0.95rem;
        }
        .code-block {
            background: #0d1117;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            overflow-x: auto;
        }
        .code-block code {
            color: #e2e8f0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            white-space: pre;
        }
        .code-block .highlight { color: #10b981; font-weight: 600; }
        .code-block .danger { color: #ef4444; font-weight: 600; }
        .code-block .comment { color: #64748b; }
        .tip-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .tip-box h4 {
            color: #fbbf24;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tip-box p {
            color: #fcd34d;
            font-size: 0.9rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .impact-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .impact-box h3 {
            color: #f87171;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .impact-box ul {
            color: #fca5a5;
            padding-left: 1.5rem;
        }
        .impact-box li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-top">
            <a href="index.php">← Back to Lab Home</a>
            <a href="../index.php">🏠 All Labs</a>
            <a href="docs.php">📚 Documentation</a>
        </nav>
        
        <header class="lab-header">
            <span class="lab-badge">🔓 Access Control Vulnerability</span>
            <h1>Lab 21: IDOR on Column Settings</h1>
            <p>Exploit an Insecure Direct Object Reference vulnerability in a Stocky-like inventory application to modify another user's column settings.</p>
        </header>
        
        <div class="objective-box">
            <h2>🎯 Mission Objective</h2>
            <p>You are <strong>User B</strong> (attacker) with your own store. Your goal is to modify <strong>User A's</strong> (victim) Low Stock Variants column settings by exploiting the IDOR vulnerability in the settings update endpoint. Change their column visibility preferences without having access to their account.</p>
        </div>
        
        <div class="credentials-grid">
            <div class="credential-card victim">
                <h3>🎯 User A (Victim)</h3>
                <div class="cred-row">
                    <span class="cred-label">Username</span>
                    <span class="cred-value">user_a</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-value">usera123</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Store</span>
                    <span class="cred-value">test.myshopify.com</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Settings ID</span>
                    <span class="cred-value">111111</span>
                </div>
            </div>
            <div class="credential-card attacker">
                <h3>⚔️ User B (Attacker)</h3>
                <div class="cred-row">
                    <span class="cred-label">Username</span>
                    <span class="cred-value">user_b</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-value">userb123</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Store</span>
                    <span class="cred-value">test1.myshopify.com</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Settings ID</span>
                    <span class="cred-value">111112</span>
                </div>
            </div>
        </div>
        
        <section class="steps-section">
            <h2>📝 Step-by-Step Exploitation</h2>
            
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Understand the Setup</h3>
                        <p>We have two users (User A and User B), each with their own store and column settings. Each settings record has a unique ID that identifies whose settings they are.</p>
                    </div>
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Login as User A (Victim) - Observe Initial Settings</h3>
                        <p>First, log in as user_a (password: usera123) and navigate to Settings → Column Settings. Note that all columns are enabled (showing 14/14 columns). User A prefers to see all available data.</p>
                    </div>
                </div>
                <div class="tip-box">
                    <h4>💡 Tip</h4>
                    <p>Note the Settings ID displayed: <strong>111111</strong>. This is User A's settings identifier.</p>
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Logout and Login as User B (Attacker)</h3>
                        <p>Log out and log in as user_b (password: userb123). Navigate to Settings → Column Settings. Notice User B has a different Settings ID: <strong>111112</strong>.</p>
                    </div>
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Modify Settings and Intercept the Request</h3>
                        <p>Change some column visibility settings (uncheck some boxes). Before clicking "Update", enable your browser's Developer Tools Network tab or Burp Suite to intercept the request.</p>
                    </div>
                </div>
                <div class="code-block">
                    <code>POST /lab21/settings.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded

settings_id=<span class="highlight">111112</span>&show_grade=0&show_product_title=1&show_variant_title=0...
<span class="comment">                 ↑ This is User B's settings ID</span></code>
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h3>Exploit: Change the Settings ID</h3>
                        <p>Modify the <code>settings_id</code> parameter from User B's ID (111112) to User A's ID (111111). You can do this directly in the form's hidden input field or by intercepting the request.</p>
                    </div>
                </div>
                <div class="code-block">
                    <code>POST /lab21/settings.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded

settings_id=<span class="danger">111111</span>&show_grade=0&show_product_title=0&show_variant_title=0...
<span class="comment">                 ↑ Changed to User A's settings ID!</span></code>
                </div>
                <div class="tip-box">
                    <h4>💡 Easy Method</h4>
                    <p>In the Settings page, there's an editable "Settings ID" field. Simply change the number from 111112 to 111111 and click Update!</p>
                </div>
            </div>
            
            <div class="step-card">
                <div class="step-header">
                    <div class="step-number">6</div>
                    <div class="step-content">
                        <h3>Verify the Attack</h3>
                        <p>After submitting, you'll be redirected to the success page. Log out and log back in as User A to verify their column settings have been changed by the attacker!</p>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="impact-box">
            <h3>⚠️ Real-World Impact</h3>
            <ul>
                <li><strong>Privacy Violation:</strong> Attacker can view or infer other users' preferences</li>
                <li><strong>Denial of Service:</strong> Attacker could disable all columns, making the dashboard useless</li>
                <li><strong>Business Disruption:</strong> Changing settings could hide critical low-stock alerts</li>
                <li><strong>Reputation Damage:</strong> Users lose trust in the application's security</li>
                <li><strong>Regulatory Issues:</strong> May violate data protection regulations (GDPR, etc.)</li>
            </ul>
        </div>
        
        <div class="action-buttons">
            <a href="login.php" class="btn btn-primary">
                🚀 Start the Lab
            </a>
            <a href="docs.php" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);">
                📚 Read Documentation
            </a>
        </div>
    </div>
</body>
</html>
