<?php
// Lab 29: LinkedPro Newsletter Platform - Lab Landing Page
session_start();
require_once '../progress.php';
$isSolved = isLabSolved(29);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 29: Newsletter Subscriber IDOR - LinkedPro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f1419 50%, #0a0a0f 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(10, 102, 194, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(5, 118, 66, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        .nav-bar {
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .nav-logo span {
            color: #057642;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
        }
        .nav-links a:hover {
            color: #0a66c2;
            background: rgba(10, 102, 194, 0.1);
        }
        .nav-links a.active {
            color: #0a66c2;
            font-weight: 600;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(10, 102, 194, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(10, 102, 194, 0.2);
            border-color: #0a66c2;
            color: #0a66c2;
        }
        .solved-badge {
            background: linear-gradient(135deg, #057642, #034d2e);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .hero {
            text-align: center;
            padding: 3rem 0;
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #0a66c2, #004182);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #0a66c2, #057642);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            color: #888;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .platform-preview {
            background: linear-gradient(135deg, #0a66c2, #004182);
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        .platform-preview .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .platform-preview h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .platform-preview p {
            color: rgba(255,255,255,0.8);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .info-card h3 {
            color: #0a66c2;
            font-size: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-card p {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .info-card code {
            background: rgba(10, 102, 194, 0.1);
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.8rem;
            color: #7fc4fd;
        }
        .credentials-box {
            background: rgba(5, 118, 66, 0.1);
            border: 1px solid rgba(5, 118, 66, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .credentials-box h3 {
            color: #057642;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .cred-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .cred-item {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .cred-item .role {
            color: #057642;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        .cred-item .creds {
            color: #e0e0e0;
            font-family: monospace;
            font-size: 0.9rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0a66c2, #004182);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(10, 102, 194, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #0a66c2;
            border: 1px solid rgba(10, 102, 194, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(10, 102, 194, 0.1);
        }
        .btn-docs {
            background: rgba(5, 118, 66, 0.1);
            color: #057642;
            border: 1px solid rgba(5, 118, 66, 0.3);
        }
        .btn-docs:hover {
            background: rgba(5, 118, 66, 0.2);
        }
        .vulnerability-info {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .vulnerability-info h3 {
            color: #ffa500;
            margin-bottom: 1rem;
        }
        .vulnerability-info ul {
            color: #888;
            margin-left: 1.5rem;
            line-height: 1.8;
        }
        .vulnerability-info li {
            margin-bottom: 0.5rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: #0a66c2;
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">Linked<span>Pro</span></a>
        <div class="nav-links">
            <a href="../index.php" class="btn-back">← All Labs</a>
            <a href="index.php" class="active">Home</a>
            <a href="lab-description.php">Lab Info</a>
            <a href="docs.php">Documentation</a>
            <a href="setup_db.php">Setup DB</a>
            <a href="login.php">Login</a>
            <?php if ($isSolved): ?>
                <span class="solved-badge">✓ Solved</span>
            <?php endif; ?>
        </div>
    </nav>
    
    <div class="container">
        
        <div class="hero">
            <div class="lab-badge">🔬 Lab 29 - IDOR Vulnerability</div>
            <h1>Newsletter Subscriber Data Exposure</h1>
            <p>Exploit an IDOR vulnerability in a professional networking platform's newsletter API to access subscriber lists of other users' newsletters.</p>
        </div>
        
        <div class="platform-preview">
            <div class="icon">📰</div>
            <h3>LinkedPro Newsletter Platform</h3>
            <p>A LinkedIn-style professional platform with newsletter creation and subscription features</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>🎯 Objective</h3>
                <p>Access the subscriber list of newsletters you don't own by exploiting the missing authorization check in the API endpoint.</p>
            </div>
            <div class="info-card">
                <h3>🔓 Vulnerability Type</h3>
                <p>Insecure Direct Object Reference (IDOR) - The API accepts any <code>seriesUrn</code> without verifying ownership.</p>
            </div>
            <div class="info-card">
                <h3>📡 Vulnerable Endpoint</h3>
                <p><code>GET /api/get_subscribers.php?seriesUrn=...</code> returns subscriber data without authorization checks.</p>
            </div>
            <div class="info-card">
                <h3>⚠️ Impact</h3>
                <p>Exposure of subscriber PII including emails, job titles, locations, and professional connections.</p>
            </div>
        </div>
        
        <div class="vulnerability-info">
            <h3>🔍 Attack Steps</h3>
            <ul>
                <li>Login as any user (e.g., attacker account)</li>
                <li>Browse newsletters and note the public <code>newsletter_urn</code> values</li>
                <li>If you're a creator, click "Subscribers" on your own newsletter and capture the API request</li>
                <li>Replay the request with a different <code>seriesUrn</code> from another creator's newsletter</li>
                <li>The API returns all subscriber data without verifying you own that newsletter!</li>
            </ul>
        </div>
        
        <div class="credentials-box">
            <h3>🧪 Test Credentials</h3>
            <div class="cred-grid">
                <div class="cred-item">
                    <div class="role">⚔️ Attacker</div>
                    <div class="creds">attacker / attacker123</div>
                </div>
                <div class="cred-item">
                    <div class="role">👩‍💼 Creator (Victim 1)</div>
                    <div class="creds">alice_ceo / alice123</div>
                </div>
                <div class="cred-item">
                    <div class="role">👨‍💼 Creator (Victim 2)</div>
                    <div class="creds">bob_investor / bob123</div>
                </div>
                <div class="cred-item">
                    <div class="role">👩‍🏫 Creator (Victim 3)</div>
                    <div class="creds">carol_professor / carol123</div>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Description</a>
            <a href="docs.php" class="btn btn-docs">📚 Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">⚙️ Setup Database</a>
        </div>
    </div>
</body>
</html>
