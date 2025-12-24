<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR Slowvote Bypass</title>
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
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
            padding: 1rem 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #9370DB; }
        .docs-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            padding-top: 70px;
        }
        .sidebar {
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(106, 90, 205, 0.3);
            padding: 2rem 0;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #9370DB;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #aaa;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(147, 112, 219, 0.1);
            color: #9370DB;
            border-left-color: #9370DB;
        }
        .sidebar-nav a.sub-item { padding-left: 2.5rem; font-size: 0.9rem; }
        .main-content {
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .page-title {
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #9370DB;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #9370DB;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
        }
        .section h3 {
            color: #b794f4;
            margin: 1.5rem 0 0.75rem;
        }
        .section p, .section li { line-height: 1.8; color: #ccc; margin-bottom: 0.75rem; }
        .section ul { padding-left: 1.5rem; }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .code-block code { color: inherit; }
        .vulnerability-badge {
            display: inline-block;
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            padding: 0.25rem 0.75rem;
            border-radius: 5px;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }
        .secure-badge {
            display: inline-block;
            background: rgba(0, 200, 0, 0.2);
            color: #66ff66;
            padding: 0.25rem 0.75rem;
            border-radius: 5px;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .info-box {
            background: rgba(0, 150, 255, 0.1);
            border: 1px solid rgba(0, 150, 255, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .tip-box {
            background: rgba(0, 200, 0, 0.1);
            border: 1px solid rgba(0, 200, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid rgba(106, 90, 205, 0.3);
        }
        th { background: rgba(147, 112, 219, 0.2); color: #9370DB; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(106, 90, 205, 0.3);
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            background: rgba(147, 112, 219, 0.2);
            color: #9370DB;
            transition: all 0.3s;
        }
        .nav-btn:hover { background: rgba(147, 112, 219, 0.4); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">P</span>
                Phabricator
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="index.php">Lab Home</a>
                <a href="login.php">Start Lab</a>
            </nav>
        </div>
    </header>

    <div class="docs-layout">
        <nav class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php" class="active">Overview</a></li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-vulnerability.php#auth-vs-authz" class="sub-item">Auth vs AuthZ</a></li>
                <li><a href="docs-vulnerability.php#api-flaw" class="sub-item">API Design Flaw</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li><a href="docs-exploitation.php#step-by-step" class="sub-item">Step by Step</a></li>
                <li><a href="docs-exploitation.php#payloads" class="sub-item">Attack Payloads</a></li>
                <li><a href="docs-prevention.php">Prevention</a></li>
                <li><a href="docs-prevention.php#secure-code" class="sub-item">Secure Code</a></li>
                <li><a href="docs-prevention.php#best-practices" class="sub-item">Best Practices</a></li>
                <li><a href="docs-testing.php">Testing Guide</a></li>
                <li><a href="docs-references.php">References</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="page-title">
                <h1>Lab 16: IDOR Slowvote Visibility Bypass</h1>
                <p style="color: #888;">Understanding API authorization bypasses through object reference manipulation</p>
            </div>

            <div class="section" id="overview">
                <h2>📋 Overview</h2>
                <p>
                    This lab demonstrates an <strong>Insecure Direct Object Reference (IDOR)</strong> vulnerability 
                    based on a real-world issue found in Phabricator's Slowvote feature (CVE-2017-7606). The vulnerability 
                    allows authenticated users to bypass visibility restrictions and access polls they shouldn't be able to see.
                </p>
                
                <h3>What is IDOR?</h3>
                <p>
                    IDOR (Insecure Direct Object Reference) occurs when an application exposes internal object references 
                    (like database IDs) and fails to verify whether the authenticated user is authorized to access those objects.
                </p>
                
                <div class="info-box">
                    <strong>🔑 Key Insight:</strong> The web UI properly enforces access controls, but the API endpoint 
                    only checks authentication (is the user logged in?) without checking authorization (can this user access this resource?).
                </div>
            </div>

            <div class="section" id="scenario">
                <h2>🎭 Lab Scenario</h2>
                <p>
                    You're testing a Phabricator-like application that includes a "Slowvote" polling feature. 
                    Polls can be configured with different visibility settings:
                </p>
                <table>
                    <tr>
                        <th>Visibility</th>
                        <th>Description</th>
                        <th>Who Can See</th>
                    </tr>
                    <tr>
                        <td><code>everyone</code></td>
                        <td>Public poll</td>
                        <td>All authenticated users</td>
                    </tr>
                    <tr>
                        <td><code>specific</code></td>
                        <td>Limited visibility</td>
                        <td>Only users with explicit permission</td>
                    </tr>
                    <tr>
                        <td><code>nobody</code></td>
                        <td>Private poll</td>
                        <td>Only the creator</td>
                    </tr>
                </table>

                <h3>The Players</h3>
                <ul>
                    <li><strong>Alice</strong> - Creates polls with private visibility settings</li>
                    <li><strong>Bob</strong> - Regular user with NO special permissions (attacker)</li>
                    <li><strong>Charlie</strong> - User with explicit access to some polls</li>
                </ul>
            </div>

            <div class="section" id="vulnerability">
                <h2>⚠️ The Vulnerability</h2>
                <p>
                    The application has two paths to access poll data:
                </p>
                
                <h3>1. Web UI Path <span class="secure-badge">SECURE</span></h3>
                <div class="code-block">
// view-poll.php - Properly checks authorization
if ($poll['visibility'] === 'nobody' && $poll['creator_id'] !== $userId) {
    die("Access Denied");
}
if ($poll['visibility'] === 'specific') {
    $stmt = $pdo->prepare("SELECT can_view FROM poll_permissions WHERE poll_id = ? AND user_id = ?");
    // ... checks explicit permission
}
                </div>

                <h3>2. API Path <span class="vulnerability-badge">VULNERABLE</span></h3>
                <div class="code-block">
// api/slowvote.php - Missing authorization!
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized"); // ✅ Checks authentication
}

// ❌ MISSING: Authorization check!
// Returns poll data for ANY poll_id without checking visibility
$stmt = $pdo->prepare("SELECT * FROM slowvotes WHERE id = ?");
$stmt->execute([$pollId]);
                </div>

                <div class="warning-box">
                    <strong>⚠️ The Flaw:</strong> The API verifies the user is logged in but doesn't verify 
                    they have permission to access the specific poll being requested.
                </div>
            </div>

            <div class="section" id="impact">
                <h2>💥 Impact</h2>
                <p>An attacker exploiting this vulnerability could:</p>
                <ul>
                    <li>Access confidential poll results (e.g., employee layoff decisions)</li>
                    <li>View sensitive organizational data exposed in private polls</li>
                    <li>Enumerate and discover all polls in the system via ID manipulation</li>
                    <li>Breach confidentiality of decision-making processes</li>
                </ul>
                
                <div class="tip-box">
                    <strong>💡 Real-World Example:</strong> In the Phabricator vulnerability, attackers could use 
                    the API to access polls that were specifically configured to be invisible to certain users, 
                    bypassing the visibility restrictions completely.
                </div>
            </div>

            <div class="nav-buttons">
                <a href="index.php" class="nav-btn">← Back to Lab Home</a>
                <a href="docs-vulnerability.php" class="nav-btn">The Vulnerability →</a>
            </div>
        </main>
    </div>
</body>
</html>
