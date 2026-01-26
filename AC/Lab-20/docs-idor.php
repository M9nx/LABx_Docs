<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDOR Fundamentals - KeyVault Docs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .nav-links a {
            color: #5eead4;
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .docs-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 70px);
        }
        .sidebar {
            background: rgba(0, 0, 0, 0.2);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem 1rem;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover {
            background: rgba(20, 184, 166, 0.1);
            color: #5eead4;
        }
        .sidebar-nav a.active {
            background: rgba(20, 184, 166, 0.2);
            color: #5eead4;
            border-left: 3px solid #14b8a6;
        }
        .docs-content {
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .breadcrumb a { color: #64748b; text-decoration: none; }
        .breadcrumb a:hover { color: #5eead4; }
        .breadcrumb span { color: #64748b; }
        .docs-content h1 {
            font-size: 2.5rem;
            color: #f8fafc;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #14b8a6, #5eead4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .docs-content .subtitle {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .content-section {
            margin-bottom: 3rem;
        }
        .content-section h2 {
            color: #5eead4;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .content-section h3 {
            color: #f8fafc;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }
        .content-section p {
            color: #94a3b8;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content-section ul, .content-section ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
            color: #94a3b8;
        }
        .content-section li {
            margin-bottom: 0.5rem;
            line-height: 1.7;
        }
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #5eead4;
            font-size: 0.9rem;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
            overflow-x: auto;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            border-left: 4px solid #14b8a6;
        }
        .code-block .comment { color: #64748b; }
        .code-block .keyword { color: #f472b6; }
        .code-block .string { color: #a5f3fc; }
        .code-block .function { color: #fcd34d; }
        .info-box {
            background: rgba(20, 184, 166, 0.1);
            border: 1px solid rgba(20, 184, 166, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .info-box h4 {
            color: #5eead4;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .warning-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #fcd34d;
            margin-bottom: 0.75rem;
        }
        .danger-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .danger-box h4 {
            color: #fca5a5;
            margin-bottom: 0.75rem;
        }
        .example-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .example-card h4 {
            color: #f8fafc;
            margin-bottom: 1rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        .nav-btn.prev {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .nav-btn.next {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
        }
        .nav-btn:hover { transform: translateX(3px); }
        .nav-btn.prev:hover { transform: translateX(-3px); }
        @media (max-width: 900px) {
            .docs-layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="lab-description.php">Instructions</a>
                <a href="docs.php">Docs</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="docs-layout">
        <aside class="sidebar">
            <h3>Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">📚 Overview</a></li>
                <li><a href="docs-idor.php" class="active">🔓 IDOR Fundamentals</a></li>
                <li><a href="docs-api-security.php">🔐 API Security</a></li>
                <li><a href="docs-rbac.php">👥 Role-Based Access</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation Guide</a></li>
                <li><a href="docs-mitigation.php">🛡️ Mitigation</a></li>
            </ul>
        </aside>

        <main class="docs-content">
            <div class="breadcrumb">
                <a href="docs.php">Docs</a>
                <span>/</span>
                <span>IDOR Fundamentals</span>
            </div>

            <h1>🔓 IDOR Fundamentals</h1>
            <p class="subtitle">Understanding Insecure Direct Object Reference vulnerabilities and their impact on modern applications</p>

            <div class="content-section">
                <h2>What is IDOR?</h2>
                <p>
                    <strong>Insecure Direct Object Reference (IDOR)</strong> is a type of access control vulnerability 
                    that occurs when an application uses user-supplied input to access objects directly without proper 
                    authorization checks. It's ranked as part of the <strong>#1 vulnerability</strong> in the 
                    OWASP Top 10 (Broken Access Control).
                </p>

                <div class="info-box">
                    <h4>💡 Key Concept</h4>
                    <p>IDOR vulnerabilities arise when developers assume that because a user can only see 
                    certain links or identifiers, they won't try to access others. This is a dangerous assumption!</p>
                </div>

                <h3>Types of IDOR Vulnerabilities</h3>
                <ul>
                    <li><strong>Horizontal IDOR:</strong> Accessing resources belonging to other users at the same privilege level</li>
                    <li><strong>Vertical IDOR:</strong> Accessing resources that require higher privileges (privilege escalation)</li>
                    <li><strong>Object-level IDOR:</strong> Manipulating object references (IDs, UUIDs, filenames)</li>
                    <li><strong>Function-level IDOR:</strong> Accessing functions without proper role verification</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>IDOR in This Lab</h2>
                <p>
                    This lab demonstrates a <strong>function-level IDOR</strong> combined with <strong>role bypass</strong>. 
                    The vulnerability exists because the API endpoint checks <em>membership</em> but not <em>role permissions</em>.
                </p>

                <div class="example-card">
                    <h4>Vulnerable Pattern</h4>
                    <div class="code-block">
<span class="comment">// The API checks membership...</span>
$stmt = $pdo->prepare(<span class="string">"SELECT role FROM org_members WHERE org_id = ? AND user_id = ?"</span>);
$stmt->execute([$org['id'], $_SESSION['user_id']]);
$membership = $stmt->fetch();

<span class="keyword">if</span> (!$membership) {
    <span class="comment">// ✅ Good: Deny non-members</span>
    http_response_code(<span class="string">403</span>);
    <span class="keyword">exit</span>;
}

<span class="comment">// ❌ BAD: Missing role check!</span>
<span class="comment">// Members can now perform admin actions...</span>
<span class="function">deleteApiKey</span>($key_uuid);
                    </div>
                </div>

                <div class="danger-box">
                    <h4>⚠️ The Vulnerability</h4>
                    <p>
                        The code retrieves the user's <code>role</code> from the database but <strong>never uses it</strong> 
                        to restrict actions. A <code>member</code> can perform <code>owner</code>-level operations 
                        like creating and deleting API keys.
                    </p>
                </div>
            </div>

            <div class="content-section">
                <h2>Real-World Impact</h2>
                <p>IDOR vulnerabilities can have severe consequences:</p>

                <ul>
                    <li><strong>Data Breach:</strong> Unauthorized access to sensitive information (API keys, credentials)</li>
                    <li><strong>Account Takeover:</strong> Modifying other users' accounts or credentials</li>
                    <li><strong>Financial Loss:</strong> Accessing payment information or transferring funds</li>
                    <li><strong>Service Disruption:</strong> Deleting critical resources or configurations</li>
                    <li><strong>Compliance Violations:</strong> GDPR, HIPAA, PCI-DSS breaches</li>
                </ul>

                <div class="warning-box">
                    <h4>📊 Statistics</h4>
                    <p>
                        According to HackerOne's 2023 report, IDOR vulnerabilities accounted for 
                        <strong>16.5%</strong> of all reported security issues, making it one of the 
                        most common vulnerability types found in bug bounty programs.
                    </p>
                </div>
            </div>

            <div class="content-section">
                <h2>Common IDOR Patterns</h2>

                <h3>1. Predictable IDs</h3>
                <div class="code-block">
<span class="comment">// Vulnerable: Sequential IDs are easy to guess</span>
GET /api/users/<span class="string">1</span>/profile
GET /api/users/<span class="string">2</span>/profile  <span class="comment">// Just increment!</span>
                </div>

                <h3>2. Parameter Tampering</h3>
                <div class="code-block">
<span class="comment">// Vulnerable: User can modify the user_id parameter</span>
POST /api/transfer
{
  <span class="string">"from_account"</span>: <span class="string">"12345"</span>,
  <span class="string">"to_account"</span>: <span class="string">"67890"</span>,
  <span class="string">"amount"</span>: <span class="string">1000</span>
}
                </div>

                <h3>3. Missing Function-Level Access Control</h3>
                <div class="code-block">
<span class="comment">// Vulnerable: No role check before sensitive action</span>
<span class="keyword">if</span> (isOrgMember(user, org)) {
    <span class="comment">// Should also check: isAdmin(user, org) || isOwner(user, org)</span>
    <span class="function">deleteResource</span>(resource_id);
}
                </div>
            </div>

            <div class="nav-buttons">
                <a href="docs.php" class="nav-btn prev">← Overview</a>
                <a href="docs-api-security.php" class="nav-btn next">API Security →</a>
            </div>
        </main>
    </div>
</body>
</html>
