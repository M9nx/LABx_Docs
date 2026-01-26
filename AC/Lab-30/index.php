<?php
// Lab 30: Stocky Inventory App - Lab Landing Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 30: Low Stock Settings IDOR - Stocky</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #12081a 50%, #0a0a0f 100%);
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
                radial-gradient(ellipse at 20% 20%, rgba(124, 58, 237, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(167, 139, 250, 0.06) 0%, transparent 50%);
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
            border-bottom: 1px solid rgba(124, 58, 237, 0.2);
        }
        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #a78bfa;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-logo span {
            color: #7c3aed;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .nav-links a:hover {
            color: #a78bfa;
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
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
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
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
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
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
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
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .info-card h3 {
            color: #a78bfa;
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
            background: rgba(124, 58, 237, 0.15);
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.8rem;
            color: #c4b5fd;
        }
        .credentials-box {
            background: rgba(124, 58, 237, 0.08);
            border: 1px solid rgba(124, 58, 237, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .credentials-box h3 {
            color: #a78bfa;
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
            color: #a78bfa;
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
        .vulnerability-info code {
            background: rgba(255, 165, 0, 0.15);
            color: #ffc107;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
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
            color: #a78bfa;
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
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #a78bfa;
            border: 1px solid rgba(124, 58, 237, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(124, 58, 237, 0.1);
        }
        .btn-docs {
            background: rgba(167, 139, 250, 0.1);
            color: #c4b5fd;
            border: 1px solid rgba(167, 139, 250, 0.3);
        }
        .btn-docs:hover {
            background: rgba(167, 139, 250, 0.2);
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">📦 <span>Stocky</span></a>
        <div class="nav-links">
            <a href="lab-description.php">📋 Lab Description</a>
            <a href="docs.php">📚 Documentation</a>
            <a href="login.php">🔐 Login</a>
            <a href="../index.php">🏠 All Labs</a>
        </div>
    </nav>
    
    <div class="container">
        <a href="../index.php" class="back-link">← Back to All Labs</a>
        
        <div class="hero">
            <div class="lab-badge">🔬 Lab 30 - IDOR Vulnerability</div>
            <h1>Low Stock Variant Settings Manipulation</h1>
            <p>Exploit an IDOR vulnerability in the Stocky inventory management app to view and modify other stores' column display settings without authorization.</p>
        </div>
        
        <div class="platform-preview">
            <div class="icon">📦</div>
            <h3>Stocky Inventory Management</h3>
            <p>A Shopify-integrated inventory app with customizable Low Stock Variant dashboards</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>🎯 Objective</h3>
                <p>Modify or import column settings from stores you don't own by exploiting the missing authorization check in settings endpoints.</p>
            </div>
            <div class="info-card">
                <h3>🔓 Vulnerability Type</h3>
                <p>Insecure Direct Object Reference (IDOR) - The API accepts any <code>settings_id</code> or <code>import_from_id</code> without ownership verification.</p>
            </div>
            <div class="info-card">
                <h3>📡 Vulnerable Endpoints</h3>
                <p><code>POST settings.php</code> with <code>settings_id</code> parameter allows updating any store's column preferences.</p>
            </div>
            <div class="info-card">
                <h3>⚠️ Impact</h3>
                <p>Disrupt competitors by hiding critical inventory columns or exposing their dashboard configuration preferences.</p>
            </div>
        </div>
        
        <div class="vulnerability-info">
            <h3>🔍 Attack Vectors</h3>
            <ul>
                <li><strong>Direct Modification:</strong> Change <code>settings_id</code> in the update form to modify another store's settings</li>
                <li><strong>Import Settings:</strong> Use the "Import from Store" feature with another user's Settings ID</li>
                <li>Navigate to Settings page and edit the hidden <code>settings_id</code> field</li>
                <li>Or use the Import feature to enter any Settings ID (1, 2, 3, 4...)</li>
                <li>The server updates or reads ANY settings record without ownership verification!</li>
            </ul>
        </div>
        
        <div class="credentials-box">
            <h3>🧪 Test Credentials</h3>
            <div class="cred-grid">
                <div class="cred-item">
                    <div class="role">⚔️ Attacker</div>
                    <div class="creds">alice_shop / password123</div>
                </div>
                <div class="cred-item">
                    <div class="role">👔 Victim 1</div>
                    <div class="creds">bob_tech / password123</div>
                </div>
                <div class="cred-item">
                    <div class="role">👗 Victim 2</div>
                    <div class="creds">carol_home / password123</div>
                </div>
                <div class="cred-item">
                    <div class="role">⚽ Victim 3</div>
                    <div class="creds">david_sports / password123</div>
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
