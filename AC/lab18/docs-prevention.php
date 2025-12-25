<?php
require_once 'config.php';
require_once '../progress.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prevention - Lab 18</title>
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
            max-width: 900px;
        }
        .breadcrumb { color: #888; margin-bottom: 2rem; }
        .breadcrumb a { color: #96bf48; text-decoration: none; }
        h1 { color: #e0e0e0; font-size: 2rem; margin-bottom: 1rem; }
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
        .doc-section h3 { color: #ffaa00; margin: 1.5rem 0 1rem; }
        .doc-section p, .doc-section li {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .code-block code { color: #88ff88; }
        .secure { color: #66ff99; }
        .success-box {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
            border-radius: 10px;
            padding: 1.25rem;
            margin: 1rem 0;
        }
        .success-box h4 { color: #66ff99; margin-bottom: 0.5rem; }
        .checklist { list-style: none; padding: 0; }
        .checklist li {
            padding: 0.75rem 0;
            padding-left: 2rem;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .checklist li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #66ff99;
            font-weight: bold;
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
                <li><a href="docs-prevention.php" class="active">🛡️ Prevention</a></li>
                <li><a href="docs-comparison.php">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php">🔗 References</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="docs.php">Documentation</a> / Prevention
            </div>

            <h1>🛡️ Prevention Strategies</h1>

            <div class="doc-section">
                <h2>The Fix: Server-Side Authorization</h2>
                <p>
                    The key to preventing IDOR vulnerabilities is to <strong>never trust user input</strong> 
                    for authorization decisions. Always verify that the authenticated user has permission 
                    to perform the requested action on the specified resource.
                </p>
                <div class="code-block">
<code><span class="secure">// SECURE: Verify ownership before action</span>
session_start();
$authenticated_user_id = $_SESSION['user_id'];
$requested_account_id = $_POST['account_id'];

<span class="secure">// Critical check: User can only manage their own account</span>
if ($authenticated_user_id !== $requested_account_id) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Now safe to proceed
$stmt = $pdo->prepare("UPDATE sessions SET expired = 1 WHERE user_id = ?");
$stmt->execute([$authenticated_user_id]);</code>
                </div>
            </div>

            <div class="doc-section">
                <h2>Better Approach: Ignore User Input</h2>
                <p>
                    Even better: don't accept the account ID from the client at all. Use the 
                    authenticated session to determine which account to affect.
                </p>
                <div class="code-block">
<code><span class="secure">// BEST PRACTICE: Use session data, not user input</span>
session_start();

// Get user ID from the trusted session, NOT from POST data
$user_id = $_SESSION['user_id'];

// No account_id parameter needed - user can only affect their own sessions
$stmt = $pdo->prepare("UPDATE sessions SET expired = 1 WHERE user_id = ?");
$stmt->execute([$user_id]);

echo json_encode(['success' => true, 'message' => 'Your sessions have been expired']);</code>
                </div>
                
                <div class="success-box">
                    <h4>✅ Why This Works</h4>
                    <p>
                        By using session data instead of client input, there's no way for an attacker 
                        to specify a different account. The server automatically uses the authenticated 
                        user's ID from the trusted session.
                    </p>
                </div>
            </div>

            <div class="doc-section">
                <h2>IDOR Prevention Checklist</h2>
                <ul class="checklist">
                    <li>Never trust user-supplied object references for authorization</li>
                    <li>Validate ownership on every request that accesses user data</li>
                    <li>Use indirect references (UUIDs, tokens) instead of sequential IDs</li>
                    <li>Implement role-based access control (RBAC)</li>
                    <li>Log all access attempts for audit purposes</li>
                    <li>Use parameterized queries to prevent SQL injection</li>
                    <li>Apply the principle of least privilege</li>
                    <li>Conduct regular security code reviews</li>
                </ul>
            </div>

            <div class="doc-section">
                <h2>Additional Security Measures</h2>
                
                <h3>1. Use Indirect Object References</h3>
                <div class="code-block">
<code>// Instead of: account_id=2
// Use: account_token=a1b2c3d4-e5f6-7890-abcd-ef1234567890

$token = $_POST['account_token'];
$stmt = $pdo->prepare("
    SELECT id FROM users 
    WHERE session_token = ? AND id = ?
");
$stmt->execute([$token, $_SESSION['user_id']]);</code>
                </div>

                <h3>2. Rate Limiting</h3>
                <p>
                    Implement rate limiting to slow down enumeration attacks where attackers 
                    try multiple account IDs to find valid targets.
                </p>

                <h3>3. Logging and Monitoring</h3>
                <p>
                    Log all session management activities and set up alerts for suspicious patterns, 
                    such as a user attempting to access multiple different accounts.
                </p>
            </div>

            <div class="nav-buttons">
                <a href="docs-exploitation.php" class="btn btn-secondary">← Exploitation</a>
                <a href="docs-comparison.php" class="btn btn-primary">Code Comparison →</a>
            </div>
        </main>
    </div>
</body>
</html>
