<?php
// Lab 21: Documentation - Step-by-Step Walkthrough
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exploitation Walkthrough - IDOR Documentation | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .nav-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
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
        .nav-top a:hover { background: rgba(99, 102, 241, 0.2); }
        .doc-header {
            margin-bottom: 3rem;
        }
        .doc-header h1 {
            font-size: 2.25rem;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .doc-header p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        .step-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .step-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .step-number {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
            color: white;
        }
        .step-content h2 {
            color: #fbbf24;
            font-size: 1.3rem;
            margin-bottom: 0.25rem;
        }
        .step-content .subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .step-body {
            margin-left: 60px;
        }
        .step-body p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .step-body ul {
            color: #94a3b8;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .step-body li {
            margin-bottom: 0.5rem;
        }
        .code-block {
            background: #0d1117;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
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
        .code-block .method { color: #f59e0b; font-weight: 600; }
        .tip-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .tip-box h4 {
            color: #34d399;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tip-box p {
            color: #6ee7b7;
            font-size: 0.9rem;
            margin: 0;
        }
        .warning-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .warning-box h4 {
            color: #f87171;
            margin-bottom: 0.5rem;
        }
        .warning-box p {
            color: #fca5a5;
            font-size: 0.9rem;
            margin: 0;
        }
        .method-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .method-option {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
        }
        .method-option h5 {
            color: #a5b4fc;
            margin-bottom: 0.5rem;
        }
        .method-option p {
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 0;
        }
        .nav-pagination {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
        }
        .nav-pagination a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-pagination a:hover { background: rgba(99, 102, 241, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-top">
            <a href="docs.php">← Back to Docs</a>
            <a href="lab-description.php">📖 Lab Guide</a>
            <a href="login.php">🚀 Start Lab</a>
        </nav>
        
        <header class="doc-header">
            <h1>🚶 2. Step-by-Step Walkthrough</h1>
            <p>A detailed guide on exploiting the IDOR vulnerability in the Stocky application's column settings feature.</p>
        </header>
        
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h2>Setup: Observe Victim's Settings</h2>
                    <span class="subtitle">Login as User A to see initial state</span>
                </div>
            </div>
            <div class="step-body">
                <p>First, log in as the victim (User A) to observe their current column settings. This helps us verify that our attack actually changes something.</p>
                <ul>
                    <li>Navigate to the login page</li>
                    <li>Login with: <strong>user_a</strong> / <strong>usera123</strong></li>
                    <li>Go to <strong>Dashboard → Column Settings</strong></li>
                    <li>Note that User A has <strong>all 14 columns enabled</strong></li>
                    <li>Note the <strong>Settings ID: 111111</strong></li>
                </ul>
                <div class="tip-box">
                    <h4>💡 Pro Tip</h4>
                    <p>Take a screenshot or note of the current settings before the attack. This makes it easier to verify the attack was successful.</p>
                </div>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h2>Login as Attacker</h2>
                    <span class="subtitle">Switch to User B's account</span>
                </div>
            </div>
            <div class="step-body">
                <p>Log out from User A and log in as the attacker (User B).</p>
                <ul>
                    <li>Click <strong>Logout</strong></li>
                    <li>Login with: <strong>user_b</strong> / <strong>userb123</strong></li>
                    <li>Navigate to <strong>Column Settings</strong></li>
                    <li>Note User B's <strong>Settings ID: 111112</strong></li>
                </ul>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h2>Modify Your Own Settings</h2>
                    <span class="subtitle">Prepare the attack payload</span>
                </div>
            </div>
            <div class="step-body">
                <p>Change some column visibility toggles. You'll send these changes to the victim's settings instead of your own.</p>
                <ul>
                    <li>Uncheck: <strong>Grade</strong>, <strong>Product Title</strong>, <strong>Variant Title</strong></li>
                    <li>Uncheck: <strong>SKU</strong>, <strong>Lost Per Day</strong>, <strong>Reorder Point</strong></li>
                    <li>Keep some checkboxes checked so the victim can still see some columns</li>
                </ul>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h2>Intercept/Modify the Request</h2>
                    <span class="subtitle">Change the target settings ID</span>
                </div>
            </div>
            <div class="step-body">
                <p>This is the key step. You need to change the <code>settings_id</code> from your ID (111112) to the victim's ID (111111).</p>
                
                <div class="method-options">
                    <div class="method-option">
                        <h5>🔧 Method 1: Form Field Edit</h5>
                        <p>Simply edit the "Settings ID" input field in the form from 111112 to 111111, then click Update.</p>
                    </div>
                    <div class="method-option">
                        <h5>🔍 Method 2: Browser DevTools</h5>
                        <p>Open Network tab, submit the form, find the POST request, and use "Edit and Resend" to modify the ID.</p>
                    </div>
                    <div class="method-option">
                        <h5>🎯 Method 3: Burp Suite</h5>
                        <p>Intercept the request, modify settings_id in the body, then forward the modified request.</p>
                    </div>
                </div>
                
                <p><strong>Original Request:</strong></p>
                <div class="code-block">
                    <code><span class="method">POST</span> /lab21/settings.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded

settings_id=<span class="highlight">111112</span>&show_grade=0&show_product_title=0&show_variant_title=0&show_sku=0...</code>
                </div>
                
                <p><strong>Modified Request (Attack):</strong></p>
                <div class="code-block">
                    <code><span class="method">POST</span> /lab21/settings.php HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded

settings_id=<span class="danger">111111</span>&show_grade=0&show_product_title=0&show_variant_title=0&show_sku=0...
<span class="comment">         ↑ Changed from 111112 to victim's ID!</span></code>
                </div>
                
                <div class="warning-box">
                    <h4>⚠️ Important</h4>
                    <p>The application accepts this modified ID without any validation that you own those settings!</p>
                </div>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">5</div>
                <div class="step-content">
                    <h2>Exploit Successful!</h2>
                    <span class="subtitle">Verify the attack worked</span>
                </div>
            </div>
            <div class="step-body">
                <p>After submitting the modified request:</p>
                <ul>
                    <li>You should see a <strong>success message</strong> or be redirected to the success page</li>
                    <li>Log out and log back in as <strong>user_a</strong></li>
                    <li>Navigate to <strong>Column Settings</strong></li>
                    <li>Verify that User A's settings have been modified without their consent!</li>
                </ul>
                
                <div class="tip-box">
                    <h4>✅ Attack Verification</h4>
                    <p>User A should now see fewer columns in their Low Stock Variants table. The columns you unchecked as User B are now disabled for User A!</p>
                </div>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">6</div>
                <div class="step-content">
                    <h2>Using cURL (Alternative)</h2>
                    <span class="subtitle">Command-line exploitation</span>
                </div>
            </div>
            <div class="step-body">
                <p>You can also perform this attack using cURL:</p>
                <div class="code-block">
                    <code><span class="comment"># First, get a valid session cookie by logging in as user_b</span>
curl -c cookies.txt -d "username=user_b&password=userb123" http://localhost/AC/lab21/login.php

<span class="comment"># Then, send the IDOR attack request</span>
curl -b cookies.txt -X POST http://localhost/AC/lab21/settings.php \
  -d "settings_id=<span class="danger">111111</span>&show_grade=0&show_product_title=0&show_variant_title=0&show_sku=0&show_need=1&show_stock=1"</code>
                </div>
            </div>
        </div>
        
        <nav class="nav-pagination">
            <a href="docs-overview.php">← Previous: Overview</a>
            <a href="docs-technical.php">Next: Why It Works →</a>
        </nav>
    </div>
</body>
</html>
