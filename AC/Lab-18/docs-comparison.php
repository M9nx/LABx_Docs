<?php
require_once 'config.php';
require_once '../progress.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Comparison - Lab 18</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(150, 191, 72, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #96bf48;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #96bf48; }
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 70px);
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.02);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem 1rem;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
        }
        .sidebar h3 {
            color: #96bf48;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1rem;
            color: #888;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.25rem;
        }
        .sidebar-nav a:hover { background: rgba(150, 191, 72, 0.1); color: #e0e0e0; }
        .sidebar-nav a.active {
            background: rgba(150, 191, 72, 0.2);
            color: #96bf48;
            border-left: 3px solid #96bf48;
        }
        .main-content {
            flex: 1;
            padding: 2rem 3rem;
            max-width: 1000px;
        }
        .breadcrumb { color: #888; margin-bottom: 2rem; }
        .breadcrumb a { color: #96bf48; text-decoration: none; }
        h1 { color: #e0e0e0; font-size: 2rem; margin-bottom: 1rem; }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .code-panel {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            overflow: hidden;
        }
        .code-panel-header {
            padding: 1rem 1.5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vulnerable-header {
            background: rgba(255, 68, 68, 0.2);
            color: #ff8888;
            border-bottom: 2px solid #ff6666;
        }
        .secure-header {
            background: rgba(0, 200, 100, 0.2);
            color: #66ff99;
            border-bottom: 2px solid #66ff99;
        }
        .code-block {
            background: #0d0d0d;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.8rem;
            overflow-x: auto;
            min-height: 300px;
        }
        .code-block code { color: #ccc; white-space: pre; display: block; }
        .vulnerable { color: #ff6666; }
        .secure { color: #66ff99; }
        .comment { color: #666; }
        .doc-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .doc-section h2 {
            color: #96bf48;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .doc-section p, .doc-section li {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .diff-highlight {
            background: rgba(255, 255, 0, 0.1);
            display: block;
            margin: 0 -1rem;
            padding: 0 1rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn-primary { background: linear-gradient(135deg, #96bf48, #5c6ac4); color: white; }
        .btn:hover { transform: translateY(-2px); }
        @media (max-width: 900px) {
            .comparison-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 109 124" fill="none">
                    <path d="M74.7 14.8L62.2 55.4H46.7L34.2 14.8C33.1 11 29.5 8.3 25.5 8.3H0L31.5 115.5H40.8L54.5 67.8L68.2 115.5H77.5L109 8.3H83.5C79.5 8.3 75.8 11 74.7 14.8Z" fill="#96bf48"/>
                </svg>
                Shopify Admin
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="docs.php" style="color: #96bf48;">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">📖 Overview</a></li>
                <li><a href="docs-vulnerability.php">🔓 The Vulnerability</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-comparison.php" class="active">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php">🔗 References</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="docs.php">Documentation</a> / Code Comparison
            </div>

            <h1>⚖️ Vulnerable vs. Secure Code</h1>

            <div class="comparison-grid">
                <div class="code-panel">
                    <div class="code-panel-header vulnerable-header">
                        ❌ Vulnerable Code
                    </div>
                    <div class="code-block">
<code><span class="comment">// api/expire_sessions.php (VULNERABLE)</span>
&lt;?php
session_start();
require_once '../config.php';

<span class="comment">// No authentication check!</span>
<span class="vulnerable">$account_id = $_POST['account_id'];</span>
$action = $_POST['action'];

if ($action === 'expire_all') {
    <span class="comment">// Blindly trusts user input</span>
    $stmt = $pdo->prepare("
        UPDATE sessions 
        SET expired = 1 
        <span class="vulnerable">WHERE user_id = ?</span>
    ");
    <span class="vulnerable">$stmt->execute([$account_id]);</span>
    
    echo json_encode([
        'success' => true
    ]);
}
?&gt;</code>
                    </div>
                </div>

                <div class="code-panel">
                    <div class="code-panel-header secure-header">
                        ✅ Secure Code
                    </div>
                    <div class="code-block">
<code><span class="comment">// api/expire_sessions.php (SECURE)</span>
&lt;?php
session_start();
require_once '../config.php';

<span class="comment">// Check if user is logged in</span>
<span class="secure">if (!isset($_SESSION['user_id'])) {</span>
<span class="secure">    http_response_code(401);</span>
<span class="secure">    exit(json_encode(['error' => 'Unauthorized']));</span>
<span class="secure">}</span>

<span class="comment">// Use session data, ignore user input</span>
<span class="secure">$user_id = $_SESSION['user_id'];</span>
$action = $_POST['action'];

if ($action === 'expire_all') {
    <span class="comment">// Only affects authenticated user</span>
    $stmt = $pdo->prepare("
        UPDATE sessions 
        SET expired = 1 
        <span class="secure">WHERE user_id = ?</span>
    ");
    <span class="secure">$stmt->execute([$user_id]);</span>
    
    echo json_encode([
        'success' => true
    ]);
}
?&gt;</code>
                    </div>
                </div>
            </div>

            <div class="doc-section">
                <h2>Key Differences</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(150, 191, 72, 0.3);">
                            <th style="text-align: left; padding: 1rem; color: #96bf48;">Aspect</th>
                            <th style="text-align: left; padding: 1rem; color: #ff8888;">Vulnerable</th>
                            <th style="text-align: left; padding: 1rem; color: #66ff99;">Secure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            <td style="padding: 1rem; color: #aaa;">Source of account_id</td>
                            <td style="padding: 1rem; color: #ff8888;">$_POST (user input)</td>
                            <td style="padding: 1rem; color: #66ff99;">$_SESSION (server-side)</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            <td style="padding: 1rem; color: #aaa;">Authentication Check</td>
                            <td style="padding: 1rem; color: #ff8888;">None</td>
                            <td style="padding: 1rem; color: #66ff99;">Yes, validates session</td>
                        </tr>
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                            <td style="padding: 1rem; color: #aaa;">Can target other users?</td>
                            <td style="padding: 1rem; color: #ff8888;">Yes</td>
                            <td style="padding: 1rem; color: #66ff99;">No</td>
                        </tr>
                        <tr>
                            <td style="padding: 1rem; color: #aaa;">IDOR Vulnerable?</td>
                            <td style="padding: 1rem; color: #ff8888;">Yes</td>
                            <td style="padding: 1rem; color: #66ff99;">No</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="doc-section">
                <h2>Form Comparison</h2>
                <div class="comparison-grid">
                    <div class="code-panel">
                        <div class="code-panel-header vulnerable-header">
                            ❌ Vulnerable Form
                        </div>
                        <div class="code-block">
<code><span class="comment">&lt;!-- Hidden field can be modified --&gt;</span>
&lt;form action="api/expire_sessions.php" 
      method="POST"&gt;
    <span class="vulnerable">&lt;input type="hidden" 
           name="account_id" 
           value="&lt;?= $user_id ?&gt;"&gt;</span>
    &lt;input type="hidden" 
           name="action" 
           value="expire_all"&gt;
    &lt;button type="submit"&gt;
        Expire All Sessions
    &lt;/button&gt;
&lt;/form&gt;</code>
                        </div>
                    </div>
                    <div class="code-panel">
                        <div class="code-panel-header secure-header">
                            ✅ Secure Form
                        </div>
                        <div class="code-block">
<code><span class="comment">&lt;!-- No user identifier needed --&gt;</span>
&lt;form action="api/expire_sessions.php" 
      method="POST"&gt;
    <span class="secure">&lt;!-- Server uses session data --&gt;</span>
    &lt;input type="hidden" 
           name="action" 
           value="expire_all"&gt;
    <span class="secure">&lt;input type="hidden" 
           name="csrf_token" 
           value="&lt;?= $csrf_token ?&gt;"&gt;</span>
    &lt;button type="submit"&gt;
        Expire All Sessions
    &lt;/button&gt;
&lt;/form&gt;</code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nav-buttons">
                <a href="docs-prevention.php" class="btn btn-secondary">← Prevention</a>
                <a href="docs-references.php" class="btn btn-primary">References →</a>
            </div>
        </main>
    </div>
</body>
</html>
