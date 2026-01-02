<?php
// Lab 30: Stocky Inventory App - Technical Deep Dive Documentation
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Deep Dive - Lab 30: Low Stock Settings IDOR</title>
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
            background: #0d1117;
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 8px;
            padding: 1.25rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            margin: 0;
            color: #e0e0e0;
            font-size: 0.85rem;
            line-height: 1.6;
            white-space: pre;
        }
        .code-block .comment { color: #6a9955; }
        .code-block .keyword { color: #c586c0; }
        .code-block .function { color: #dcdcaa; }
        .code-block .string { color: #ce9178; }
        .code-block .variable { color: #9cdcfe; }
        .code-block .number { color: #b5cea8; }
        .code-block .danger { color: #f44336; }
        .code-title {
            background: rgba(124, 58, 237, 0.2);
            color: #c4b5fd;
            padding: 0.5rem 1rem;
            border-radius: 8px 8px 0 0;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        .code-block.with-title {
            border-radius: 0 0 8px 8px;
            margin-top: 0;
        }
        .warning-box {
            background: rgba(255, 0, 0, 0.08);
            border-left: 4px solid #ff4444;
            border-radius: 0 8px 8px 0;
            padding: 1rem 1rem 1rem 1.25rem;
            margin: 1rem 0;
        }
        .warning-box strong {
            color: #ff6b6b;
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
        .success-box {
            background: rgba(34, 197, 94, 0.1);
            border-left: 4px solid #22c55e;
            border-radius: 0 8px 8px 0;
            padding: 1rem 1rem 1rem 1.25rem;
            margin: 1rem 0;
        }
        .success-box strong {
            color: #22c55e;
        }
        .step-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            border-radius: 50%;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
            margin-right: 0.75rem;
        }
        .attack-step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
        }
        .attack-step-content {
            flex: 1;
        }
        .attack-step-content h4 {
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .attack-step-content p {
            margin-bottom: 0.5rem;
        }
        .http-request {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            overflow: hidden;
            margin: 1rem 0;
        }
        .http-request-header {
            background: #161b22;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #30363d;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .http-method {
            background: #7c3aed;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .http-method.get {
            background: #22c55e;
        }
        .http-url {
            color: #c4b5fd;
            font-family: monospace;
            font-size: 0.85rem;
        }
        .http-body {
            padding: 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #e0e0e0;
            white-space: pre;
            overflow-x: auto;
        }
        .attack-diagram {
            background: #0d1117;
            border: 1px solid rgba(124, 58, 237, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.8rem;
            line-height: 1.4;
            overflow-x: auto;
            color: #c4b5fd;
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
                <li><a href="docs.php"><span class="icon">📖</span> Overview</a></li>
                <li><a href="docs.php#what-is-idor"><span class="icon">❓</span> What is IDOR?</a></li>
                <li><a href="docs.php#vulnerability"><span class="icon">🔓</span> The Vulnerability</a></li>
                <li><a href="docs.php#impact"><span class="icon">⚠️</span> Security Impact</a></li>
            </ul>
            
            <div class="sidebar-section">
                <div class="sidebar-section-title">Exploitation</div>
                <ul class="sidebar-nav">
                    <li><a href="docs-technical.php" class="active"><span class="icon">🔍</span> Technical Deep Dive</a></li>
                    <li><a href="#walkthrough"><span class="icon">👣</span> Step-by-Step</a></li>
                    <li><a href="#code-analysis"><span class="icon">💻</span> Code Analysis</a></li>
                    <li><a href="#attack-vectors"><span class="icon">⚔️</span> Attack Vectors</a></li>
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
            <a href="docs.php" class="back-link">← Back to Documentation</a>
            
            <div class="page-header">
                <h1>🔍 Technical Deep Dive</h1>
                <p>Detailed analysis of the IDOR vulnerability and step-by-step exploitation walkthrough</p>
            </div>
            
            <!-- Walkthrough Section -->
            <div class="section" id="walkthrough">
                <h2>👣 Step-by-Step Exploitation</h2>
                
                <div class="attack-step">
                    <span class="step-indicator">1</span>
                    <div class="attack-step-content">
                        <h4>Login as Attacker</h4>
                        <p>Access the login page and authenticate with the attacker account:</p>
                        <code>alice_shop / password123</code>
                    </div>
                </div>
                
                <div class="attack-step">
                    <span class="step-indicator">2</span>
                    <div class="attack-step-content">
                        <h4>Navigate to Settings</h4>
                        <p>From the Dashboard, go to Low Stock → Settings page. Note your Settings ID (displayed on the page).</p>
                    </div>
                </div>
                
                <div class="attack-step">
                    <span class="step-indicator">3</span>
                    <div class="attack-step-content">
                        <h4>Identify the Vulnerability</h4>
                        <p>Look at the form's HTML. Find the hidden/editable <code>settings_id</code> field or the Import Settings form.</p>
                    </div>
                </div>
                
                <div class="attack-step">
                    <span class="step-indicator">4</span>
                    <div class="attack-step-content">
                        <h4>Exploit Method 1: Direct Modification</h4>
                        <p>Change <code>settings_id</code> from <code>1</code> to <code>2</code> and submit:</p>
                        <div class="http-request">
                            <div class="http-request-header">
                                <span class="http-method">POST</span>
                                <span class="http-url">/Lab-30/settings.php</span>
                            </div>
                            <div class="http-body">action=update
settings_id=<span style="color:#f44336">2</span>  ← Changed from 1 to 2 (Bob's ID)
show_product_title=1
show_sku=0
show_stock=1
...</div>
                        </div>
                    </div>
                </div>
                
                <div class="attack-step">
                    <span class="step-indicator">5</span>
                    <div class="attack-step-content">
                        <h4>Exploit Method 2: Import Settings</h4>
                        <p>Use the Import feature to read another user's settings:</p>
                        <div class="http-request">
                            <div class="http-request-header">
                                <span class="http-method">POST</span>
                                <span class="http-url">/Lab-30/settings.php</span>
                            </div>
                            <div class="http-body">action=import
import_from_id=<span style="color:#f44336">2</span>  ← Bob's Settings ID</div>
                        </div>
                    </div>
                </div>
                
                <div class="success-box">
                    <strong>✓ Success!</strong> You've successfully modified or imported settings from another store without authorization!
                </div>
            </div>
            
            <!-- Code Analysis Section -->
            <div class="section" id="code-analysis">
                <h2>💻 Vulnerable Code Analysis</h2>
                
                <p>The vulnerability exists in <code>settings.php</code> where user-controlled input is used without ownership verification:</p>
                
                <div class="code-title">settings.php - Vulnerable Direct Modification</div>
                <div class="code-block with-title">
                    <pre><span class="comment">// Handle form submission</span>
<span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="keyword">if</span> (<span class="variable">$action</span> === <span class="string">'update'</span>) {
        <span class="comment">// VULNERABILITY: settings_id from user input without ownership check!</span>
        <span class="variable danger">$settingsId</span> = <span class="variable">$_POST</span>[<span class="string">'settings_id'</span>] ?? <span class="variable">$settings</span>[<span class="string">'id'</span>];
        
        <span class="comment">// Update query uses attacker-controlled ID</span>
        <span class="variable">$sql</span> = <span class="string">"UPDATE settings_for_low_stock_variants 
               SET show_product_title = ?, show_sku = ?, ...
               WHERE id = ?"</span>;
        <span class="variable">$params</span>[] = <span class="variable danger">$settingsId</span>; <span class="comment">// No ownership check!</span>
    }
}</pre>
                </div>
                
                <div class="code-title">settings.php - Vulnerable Import Function</div>
                <div class="code-block with-title">
                    <pre><span class="keyword">if</span> (<span class="variable">$action</span> === <span class="string">'import'</span>) {
    <span class="comment">// VULNERABILITY: Import from any settings ID without authorization!</span>
    <span class="variable danger">$importFromId</span> = <span class="variable">$_POST</span>[<span class="string">'import_from_id'</span>];
    
    <span class="comment">// Fetch settings - NO OWNERSHIP CHECK</span>
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM settings_for_low_stock_variants 
                                WHERE id = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable danger">$importFromId</span>]);
    <span class="variable">$sourceSettings</span> = <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    
    <span class="comment">// Copies configuration without verifying ownership</span>
}</pre>
                </div>
                
                <div class="warning-box">
                    <strong>⚠️ Critical Issues:</strong>
                    <ul style="margin-top: 0.5rem; margin-bottom: 0;">
                        <li><code>settings_id</code> comes directly from POST data - user-controlled</li>
                        <li>No verification that the logged-in user owns this settings record</li>
                        <li>The WHERE clause only checks ID, not user ownership</li>
                        <li>Import feature exposes any user's configuration</li>
                    </ul>
                </div>
            </div>
            
            <!-- Attack Vectors Section -->
            <div class="section" id="attack-vectors">
                <h2>⚔️ Attack Vectors</h2>
                
                <h3>Vector 1: Direct Settings Modification</h3>
                <div class="attack-diagram">
┌──────────────────┐         ┌──────────────────┐         ┌──────────────────┐
│    Attacker      │         │     Server       │         │    Database      │
│  (alice_shop)    │         │   settings.php   │         │    ac_lab30      │
└────────┬─────────┘         └────────┬─────────┘         └────────┬─────────┘
         │                            │                            │
         │ POST settings_id=2         │                            │
         │ (Bob's settings ID)        │                            │
         ├───────────────────────────>│                            │
         │                            │                            │
         │                            │ UPDATE ... WHERE id=2      │
         │                            ├───────────────────────────>│
         │                            │                            │
         │                            │        Success ✓           │
         │        200 OK              │<───────────────────────────┤
         │<───────────────────────────┤                            │
         │                            │                            │
         ▼                            ▼                            ▼
    Bob's column settings are now modified by Alice!
                </div>
                
                <h3>Vector 2: Import Settings (Information Disclosure)</h3>
                <div class="attack-diagram">
┌──────────────────┐         ┌──────────────────┐         ┌──────────────────┐
│    Attacker      │         │     Server       │         │    Database      │
│  (alice_shop)    │         │   settings.php   │         │    ac_lab30      │
└────────┬─────────┘         └────────┬─────────┘         └────────┬─────────┘
         │                            │                            │
         │ POST import_from_id=2      │                            │
         │ (Bob's settings ID)        │                            │
         ├───────────────────────────>│                            │
         │                            │                            │
         │                            │ SELECT * WHERE id=2        │
         │                            ├───────────────────────────>│
         │                            │                            │
         │                            │   Bob's config data        │
         │   200 OK + Bob's config    │<───────────────────────────┤
         │<───────────────────────────┤                            │
         │                            │                            │
         ▼                            ▼                            ▼
    Alice now sees/copies Bob's private settings configuration!
                </div>
                
                <div class="info-box">
                    <strong>💡 Enumeration Tip:</strong> Settings IDs are sequential integers. Attackers can easily enumerate all settings by trying IDs 1, 2, 3, 4, etc.
                </div>
            </div>
            
            <div class="nav-buttons">
                <a href="docs.php" class="btn btn-secondary">← Overview</a>
                <a href="docs-mitigation.php" class="btn btn-primary">Mitigation Guide →</a>
            </div>
        </main>
    </div>
</body>
</html>
