<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Comparison - Lab 19 Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
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
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #a5b4fc; text-decoration: none; }
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.02);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem 1rem;
            position: sticky;
            top: 80px;
            height: calc(100vh - 80px);
        }
        .sidebar-title {
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding: 0 0.75rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.25rem;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(99, 102, 241, 0.1);
            color: #a5b4fc;
        }
        .sidebar-nav a.active { border-left: 3px solid #6366f1; }
        .main-content {
            flex: 1;
            padding: 2rem 3rem;
        }
        .breadcrumb {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
        .breadcrumb a { color: #a5b4fc; text-decoration: none; }
        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .page-subtitle {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin: 2rem 0;
        }
        @media (max-width: 1000px) {
            .comparison-grid { grid-template-columns: 1fr; }
        }
        .code-panel {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
        }
        .code-panel.vulnerable {
            border-color: rgba(239, 68, 68, 0.3);
        }
        .code-panel.secure {
            border-color: rgba(16, 185, 129, 0.3);
        }
        .panel-header {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .vulnerable .panel-header {
            background: rgba(239, 68, 68, 0.1);
        }
        .secure .panel-header {
            background: rgba(16, 185, 129, 0.1);
        }
        .panel-icon {
            font-size: 1.5rem;
        }
        .panel-title {
            font-weight: 600;
        }
        .vulnerable .panel-title { color: #fca5a5; }
        .secure .panel-title { color: #6ee7b7; }
        .panel-badge {
            margin-left: auto;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .vulnerable .panel-badge {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        .secure .panel-badge {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }
        .code-block {
            background: #0d1117;
            padding: 1.5rem;
            overflow-x: auto;
            max-height: 500px;
        }
        .code-block pre {
            color: #e6edf3;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.7;
        }
        .highlight-bad { background: rgba(239, 68, 68, 0.2); padding: 0 4px; border-radius: 3px; }
        .highlight-good { background: rgba(16, 185, 129, 0.2); padding: 0 4px; border-radius: 3px; }
        .diff-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            margin: 2rem 0;
            overflow: hidden;
        }
        .diff-header {
            background: rgba(99, 102, 241, 0.1);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .diff-header h3 { color: #a5b4fc; }
        .diff-content {
            padding: 1.5rem;
        }
        .diff-row {
            display: flex;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.8;
            padding: 0.25rem 0;
        }
        .diff-line-num {
            width: 40px;
            color: #6e7681;
            text-align: right;
            padding-right: 1rem;
            user-select: none;
        }
        .diff-code { flex: 1; }
        .diff-row.removed {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }
        .diff-row.added {
            background: rgba(16, 185, 129, 0.1);
            color: #6ee7b7;
        }
        .diff-row.context { color: #8b949e; }
        .explanation-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
            padding: 1rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
        }
        .explanation-box h4 { color: #a5b4fc; margin-bottom: 0.5rem; }
        .explanation-box p { color: #94a3b8; margin: 0; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            color: #a5b4fc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: #6366f1;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="docs.php">Documentation</a>
                <a href="lab-description.php">Instructions</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3 class="sidebar-title">Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">📚 Overview</a></li>
                <li><a href="docs-vulnerability.php">🔓 Vulnerability</a></li>
                <li><a href="docs-exploitation.php">⚡ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-comparison.php" class="active">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php">📖 References</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="docs.php">Documentation</a> / Code Comparison
            </div>

            <h1 class="page-title">Code Comparison</h1>
            <p class="page-subtitle">Side-by-side analysis of vulnerable vs secure code</p>

            <div class="comparison-grid">
                <!-- Vulnerable Code -->
                <div class="code-panel vulnerable">
                    <div class="panel-header">
                        <span class="panel-icon">⚠️</span>
                        <span class="panel-title">Vulnerable Code</span>
                        <span class="panel-badge">INSECURE</span>
                    </div>
                    <div class="code-block">
                        <pre>&lt;?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

<span style="color: #8b949e;">// Basic auth check only</span>
if (!isset($_SESSION['user_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]));
}

$saved_id = $_GET['saved_id'] ?? null;

if (!$saved_id) {
    die(json_encode([
        'success' => false,
        'message' => 'Missing saved_id'
    ]));
}

<span class="highlight-bad">// VULNERABLE: No ownership check!</span>
<span class="highlight-bad">$stmt = $pdo->prepare("</span>
<span class="highlight-bad">    DELETE FROM saved_projects</span>
<span class="highlight-bad">    WHERE id = ?</span>
<span class="highlight-bad">");</span>
<span class="highlight-bad">$stmt->execute([$saved_id]);</span>

echo json_encode([
    'success' => true,
    'message' => 'Deleted'
]);
?&gt;</pre>
                    </div>
                </div>

                <!-- Secure Code -->
                <div class="code-panel secure">
                    <div class="panel-header">
                        <span class="panel-icon">✅</span>
                        <span class="panel-title">Secure Code</span>
                        <span class="panel-badge">PROTECTED</span>
                    </div>
                    <div class="code-block">
                        <pre>&lt;?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

<span style="color: #8b949e;">// Basic auth check</span>
if (!isset($_SESSION['user_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]));
}

$saved_id = $_GET['saved_id'] ?? null;
<span class="highlight-good">$user_id = $_SESSION['user_id'];</span>

if (!$saved_id) {
    die(json_encode([
        'success' => false,
        'message' => 'Missing saved_id'
    ]));
}

<span class="highlight-good">// SECURE: Include user_id in query</span>
<span class="highlight-good">$stmt = $pdo->prepare("</span>
<span class="highlight-good">    DELETE FROM saved_projects</span>
<span class="highlight-good">    WHERE id = ? AND user_id = ?</span>
<span class="highlight-good">");</span>
<span class="highlight-good">$stmt->execute([$saved_id, $user_id]);</span>

<span class="highlight-good">// Check if deletion occurred</span>
<span class="highlight-good">if ($stmt->rowCount() === 0) {</span>
<span class="highlight-good">    die(json_encode([</span>
<span class="highlight-good">        'success' => false,</span>
<span class="highlight-good">        'message' => 'Not found or not authorized'</span>
<span class="highlight-good">    ]));</span>
<span class="highlight-good">}</span>

echo json_encode([
    'success' => true,
    'message' => 'Deleted'
]);
?&gt;</pre>
                    </div>
                </div>
            </div>

            <div class="explanation-box">
                <h4>🔑 Key Differences</h4>
                <p>
                    The secure version includes <code>user_id</code> in the WHERE clause and checks 
                    <code>rowCount()</code> to verify the operation succeeded. This ensures users can 
                    only delete their own saved projects.
                </p>
            </div>

            <!-- Diff View -->
            <div class="diff-section">
                <div class="diff-header">
                    <h3>📝 Unified Diff View</h3>
                </div>
                <div class="diff-content">
                    <div class="diff-row context">
                        <span class="diff-line-num">15</span>
                        <span class="diff-code">$saved_id = $_GET['saved_id'] ?? null;</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">$user_id = $_SESSION['user_id'];</span>
                    </div>
                    <div class="diff-row context">
                        <span class="diff-line-num">17</span>
                        <span class="diff-code"></span>
                    </div>
                    <div class="diff-row removed">
                        <span class="diff-line-num">-</span>
                        <span class="diff-code">// VULNERABLE: No ownership check!</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">// SECURE: Include user_id in query</span>
                    </div>
                    <div class="diff-row context">
                        <span class="diff-line-num">25</span>
                        <span class="diff-code">$stmt = $pdo->prepare("</span>
                    </div>
                    <div class="diff-row context">
                        <span class="diff-line-num">26</span>
                        <span class="diff-code">    DELETE FROM saved_projects</span>
                    </div>
                    <div class="diff-row removed">
                        <span class="diff-line-num">-</span>
                        <span class="diff-code">    WHERE id = ?</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">    WHERE id = ? AND user_id = ?</span>
                    </div>
                    <div class="diff-row context">
                        <span class="diff-line-num">28</span>
                        <span class="diff-code">");</span>
                    </div>
                    <div class="diff-row removed">
                        <span class="diff-line-num">-</span>
                        <span class="diff-code">$stmt->execute([$saved_id]);</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">$stmt->execute([$saved_id, $user_id]);</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code"></span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">// Check if deletion occurred</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">if ($stmt->rowCount() === 0) {</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">    die(json_encode([</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">        'success' => false,</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">        'message' => 'Not found or not authorized'</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">    ]));</span>
                    </div>
                    <div class="diff-row added">
                        <span class="diff-line-num">+</span>
                        <span class="diff-code">}</span>
                    </div>
                </div>
            </div>

            <!-- Additional Comparison: SQL Query -->
            <div class="comparison-grid">
                <div class="code-panel vulnerable">
                    <div class="panel-header">
                        <span class="panel-icon">🔴</span>
                        <span class="panel-title">Vulnerable SQL Query</span>
                        <span class="panel-badge">INSECURE</span>
                    </div>
                    <div class="code-block">
                        <pre><span style="color: #ff7b72;">DELETE FROM</span> saved_projects
<span style="color: #ff7b72;">WHERE</span> id = ?

<span style="color: #8b949e;">-- Deletes ANY record with matching ID</span>
<span style="color: #8b949e;">-- No user verification</span>
<span style="color: #8b949e;">-- Attacker can delete victim's data</span></pre>
                    </div>
                </div>

                <div class="code-panel secure">
                    <div class="panel-header">
                        <span class="panel-icon">🟢</span>
                        <span class="panel-title">Secure SQL Query</span>
                        <span class="panel-badge">PROTECTED</span>
                    </div>
                    <div class="code-block">
                        <pre><span style="color: #7ee787;">DELETE FROM</span> saved_projects
<span style="color: #7ee787;">WHERE</span> id = ? <span class="highlight-good">AND user_id = ?</span>

<span style="color: #8b949e;">-- Only deletes if user owns the record</span>
<span style="color: #8b949e;">-- Ownership verified in query</span>
<span style="color: #8b949e;">-- Attacker's request fails silently</span></pre>
                    </div>
                </div>
            </div>

            <div class="nav-buttons">
                <a href="docs-prevention.php" class="nav-btn">← Prevention Strategies</a>
                <a href="docs-references.php" class="nav-btn">External References →</a>
            </div>
        </main>
    </div>
</body>
</html>
