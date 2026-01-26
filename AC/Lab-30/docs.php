<?php
// Lab 30: Stocky Inventory App - Documentation Hub with Sidebar
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 30: Low Stock Settings IDOR</title>
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
        .nav-bar {
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(124, 58, 237, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #a78bfa;
            text-decoration: none;
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
        .nav-links a:hover, .nav-links a.active {
            color: #a78bfa;
        }
        .docs-layout {
            display: flex;
            min-height: calc(100vh - 60px);
        }
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(124, 58, 237, 0.2);
            padding: 2rem 0;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(124, 58, 237, 0.1);
            margin-bottom: 1rem;
        }
        .sidebar-header h3 {
            color: #a78bfa;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar-nav {
            list-style: none;
        }
        .sidebar-nav li {
            margin-bottom: 0.25rem;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover {
            background: rgba(124, 58, 237, 0.1);
            color: #a78bfa;
        }
        .sidebar-nav a.active {
            background: rgba(124, 58, 237, 0.15);
            color: #a78bfa;
            border-left-color: #7c3aed;
        }
        .sidebar-nav .icon {
            width: 20px;
            text-align: center;
        }
        .sidebar-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(124, 58, 237, 0.1);
        }
        .sidebar-section-title {
            color: #666;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
        }
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: #a78bfa;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
        }
        .page-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #a78bfa;
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section h3 {
            color: #7c3aed;
            font-size: 1rem;
            margin: 1.5rem 0 0.75rem 0;
        }
        .section p {
            color: #aaa;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #aaa;
            margin-left: 1.5rem;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section li {
            margin-bottom: 0.5rem;
        }
        .section code {
            background: rgba(124, 58, 237, 0.15);
            color: #c4b5fd;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .code-block {
            background: #1a1a2e;
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            margin: 0;
            color: #c4b5fd;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .warning-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .warning-box strong {
            color: #ffa500;
        }
        .success-box {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .success-box strong {
            color: #22c55e;
        }
        .info-box {
            background: rgba(124, 58, 237, 0.1);
            border-left: 4px solid #7c3aed;
            border-radius: 0 8px 8px 0;
            padding: 1rem 1rem 1rem 1.25rem;
            margin: 1rem 0;
        }
        .info-box strong {
            color: #a78bfa;
        }
        .nav-buttons {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(124, 58, 237, 0.1);
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
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
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #a78bfa;
            border: 1px solid rgba(124, 58, 237, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(124, 58, 237, 0.1);
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">📦 <span>Stocky</span></a>
        <div class="nav-links">
            <a href="index.php">🏠 Lab Home</a>
            <a href="lab-description.php">📋 Description</a>
            <a href="docs.php" class="active">📚 Documentation</a>
            <a href="login.php">🔐 Login</a>
        </div>
    </nav>
    
    <div class="docs-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>📚 Documentation</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="docs.php" class="active"><span class="icon">📖</span> Overview</a></li>
                <li><a href="docs.php#what-is-idor"><span class="icon">❓</span> What is IDOR?</a></li>
                <li><a href="docs.php#vulnerability"><span class="icon">🔓</span> The Vulnerability</a></li>
                <li><a href="docs.php#impact"><span class="icon">⚠️</span> Security Impact</a></li>
            </ul>
            
            <div class="sidebar-section">
                <div class="sidebar-section-title">Exploitation</div>
                <ul class="sidebar-nav">
                    <li><a href="docs-technical.php"><span class="icon">🔍</span> Technical Deep Dive</a></li>
                    <li><a href="docs-technical.php#walkthrough"><span class="icon">👣</span> Step-by-Step</a></li>
                    <li><a href="docs-technical.php#code-analysis"><span class="icon">💻</span> Code Analysis</a></li>
                    <li><a href="docs-technical.php#attack-vectors"><span class="icon">⚔️</span> Attack Vectors</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-section-title">Defense</div>
                <ul class="sidebar-nav">
                    <li><a href="docs-mitigation.php"><span class="icon">🛡️</span> Mitigation Guide</a></li>
                    <li><a href="docs-mitigation.php#secure-code"><span class="icon">✅</span> Secure Code</a></li>
                    <li><a href="docs-mitigation.php#best-practices"><span class="icon">📋</span> Best Practices</a></li>
                    <li><a href="docs-mitigation.php#testing"><span class="icon">🧪</span> Testing Guide</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-section-title">Resources</div>
                <ul class="sidebar-nav">
                    <li><a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" target="_blank"><span class="icon">🌐</span> OWASP BOLA</a></li>
                    <li><a href="https://portswigger.net/web-security/access-control/idor" target="_blank"><span class="icon">📚</span> PortSwigger IDOR</a></li>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <a href="index.php" class="back-link">← Back to Lab Home</a>
            
            <div class="page-header">
                <h1>📚 Lab 30 Documentation</h1>
                <p>Comprehensive guide to understanding and exploiting the Low Stock Settings IDOR vulnerability</p>
            </div>
            
            <!-- Overview Section -->
            <div class="section" id="what-is-idor">
                <h2>📖 What is IDOR?</h2>
                <p><strong>Insecure Direct Object Reference (IDOR)</strong> is a type of access control vulnerability that occurs when an application uses user-supplied input to access objects directly without proper authorization checks.</p>
                
                <h3>How IDOR Works</h3>
                <p>In this vulnerability pattern:</p>
                <ol>
                    <li>An application exposes a reference to an internal object (like a database ID)</li>
                    <li>Users can modify this reference to access objects they shouldn't have access to</li>
                    <li>The server fails to verify that the requesting user is authorized to access the referenced object</li>
                </ol>
                
                <div class="info-box">
                    <strong>OWASP Classification:</strong> IDOR falls under <strong>Broken Object Level Authorization (BOLA)</strong>, which is #1 on the OWASP API Security Top 10 (2023).
                </div>
            </div>
            
            <!-- Vulnerability Section -->
            <div class="section" id="vulnerability">
                <h2>🔓 The Vulnerability in This Lab</h2>
                <p>The Stocky inventory management app has an IDOR vulnerability in its Settings page:</p>
                
                <div class="code-block">
                    <pre>POST /Lab-30/settings.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

action=update&settings_id=2&show_product_title=1&show_sku=1...</pre>
                </div>
                
                <p>The <code>settings_id</code> parameter identifies which store's settings to update. The API modifies ANY settings record provided, without checking if the requesting user owns that settings record.</p>
                
                <h3>Two Attack Vectors</h3>
                <ul>
                    <li><strong>Direct Modification:</strong> Change <code>settings_id</code> to update another user's column settings</li>
                    <li><strong>Import Settings:</strong> Use <code>import_from_id</code> to copy another user's settings configuration</li>
                </ul>
            </div>
            
            <!-- Impact Section -->
            <div class="section" id="impact">
                <h2>⚠️ Security Impact</h2>
                
                <h3>Potential Damage</h3>
                <p>When exploited, this vulnerability allows attackers to:</p>
                <ul>
                    <li><strong>Sabotage Competitors:</strong> Hide critical columns from their Low Stock dashboard</li>
                    <li><strong>Expose Configuration:</strong> View other stores' dashboard preferences</li>
                    <li><strong>Cause Inventory Issues:</strong> Disable visibility of reorder points, lead times, stock levels</li>
                    <li><strong>Business Disruption:</strong> Force other stores to troubleshoot sudden UI changes</li>
                </ul>
                
                <div class="warning-box">
                    <strong>⚠️ Real-World Context:</strong> This vulnerability pattern was reported in Shopify's Stocky app. Competitors could disrupt each other's inventory management workflows by manipulating column visibility settings.
                </div>
            </div>
            
            <!-- Quick Attack Summary -->
            <div class="section">
                <h2>🎯 Quick Exploitation Summary</h2>
                <ol>
                    <li>Login with any account (<code>alice_shop / password123</code>)</li>
                    <li>Navigate to Dashboard → Low Stock → Settings</li>
                    <li>Edit the <code>settings_id</code> value from <code>1</code> to <code>2</code></li>
                    <li>Submit the form - Bob's settings are now modified!</li>
                    <li>Or use "Import Settings" with ID <code>2</code>, <code>3</code>, <code>4</code></li>
                </ol>
                
                <div class="success-box">
                    <strong>✓ Flag Location:</strong> The flag is displayed when you successfully modify or import settings from a store you don't own.
                </div>
            </div>
            
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-secondary">← Back to Lab</a>
                <a href="docs-technical.php" class="btn btn-primary">Technical Deep Dive →</a>
            </div>
        </main>
    </div>
</body>
</html>
