<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitigation Strategies - KeyVault Docs</title>
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
        .content-section ul {
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
            border-left: 4px solid #22c55e;
        }
        .code-block.vulnerable {
            border-left-color: #ef4444;
        }
        .code-block .comment { color: #64748b; }
        .code-block .keyword { color: #f472b6; }
        .code-block .string { color: #a5f3fc; }
        .code-block .function { color: #fcd34d; }
        .code-block .good { color: #86efac; }
        .code-block .bad { color: #fca5a5; }
        .fix-card {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .fix-card h4 {
            color: #86efac;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vuln-card {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .vuln-card h4 {
            color: #fca5a5;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        @media (max-width: 800px) {
            .comparison-grid { grid-template-columns: 1fr; }
        }
        .checklist {
            list-style: none;
            margin-left: 0 !important;
        }
        .checklist li {
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
            position: relative;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .checklist li::before {
            content: '✓';
            position: absolute;
            left: 1rem;
            color: #22c55e;
            font-weight: bold;
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
                <li><a href="docs-idor.php">🔓 IDOR Fundamentals</a></li>
                <li><a href="docs-api-security.php">🔐 API Security</a></li>
                <li><a href="docs-rbac.php">👥 Role-Based Access</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation Guide</a></li>
                <li><a href="docs-mitigation.php" class="active">🛡️ Mitigation</a></li>
            </ul>
        </aside>

        <main class="docs-content">
            <div class="breadcrumb">
                <a href="docs.php">Docs</a>
                <span>/</span>
                <span>Mitigation Strategies</span>
            </div>

            <h1>🛡️ Mitigation Strategies</h1>
            <p class="subtitle">How to properly fix and prevent IDOR vulnerabilities in API key management systems</p>

            <div class="content-section">
                <h2>Root Cause Analysis</h2>
                <p>
                    The vulnerability exists because the API endpoint only verifies that a user is a 
                    <strong>member</strong> of the organization, but never checks their <strong>role permissions</strong>
                    before allowing sensitive operations.
                </p>

                <div class="comparison-grid">
                    <div class="vuln-card">
                        <h4>❌ Vulnerable Code</h4>
                        <div class="code-block vulnerable">
<span class="comment">// Only checks membership</span>
$stmt = $pdo->prepare(<span class="string">"
  SELECT role FROM org_members 
  WHERE org_id = ? AND user_id = ?
"</span>);
$stmt->execute([$org_id, $user_id]);
$membership = $stmt->fetch();

<span class="keyword">if</span> (!$membership) {
    <span class="function">deny</span>();
}

<span class="comment">// ❌ Missing role check!</span>
<span class="bad">deleteApiKey($key_uuid);</span>
                        </div>
                    </div>

                    <div class="fix-card">
                        <h4>✅ Fixed Code</h4>
                        <div class="code-block">
<span class="comment">// Check membership AND role</span>
$stmt = $pdo->prepare(<span class="string">"
  SELECT role FROM org_members 
  WHERE org_id = ? AND user_id = ?
"</span>);
$stmt->execute([$org_id, $user_id]);
$membership = $stmt->fetch();

<span class="keyword">if</span> (!$membership) {
    <span class="function">deny</span>();
}

<span class="comment">// ✅ Check role permissions</span>
<span class="good"><span class="keyword">if</span> ($membership[<span class="string">'role'</span>] === <span class="string">'member'</span>) {
    <span class="function">denyWithMessage</span>(<span class="string">'Insufficient permissions'</span>);
}</span>

<span class="function">deleteApiKey</span>($key_uuid);
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h2>Complete Fixed Implementation</h2>
                <p>Here's how the API endpoint should be implemented with proper authorization:</p>

                <div class="code-block">
<span class="comment">/**
 * SECURE API Implementation
 * api/keys.php - Fixed version
 */</span>

<span class="keyword">session_start</span>();
<span class="keyword">require_once</span> <span class="string">'../config.php'</span>;

<span class="keyword">function</span> <span class="function">checkPermission</span>($pdo, $org_id, $user_id, $required_roles) {
    <span class="comment">// Get user's membership and role</span>
    $stmt = $pdo->prepare(<span class="string">"
        SELECT role FROM org_members 
        WHERE org_id = ? AND user_id = ?
    "</span>);
    $stmt->execute([$org_id, $user_id]);
    $membership = $stmt->fetch();
    
    <span class="comment">// Not a member at all</span>
    <span class="keyword">if</span> (!$membership) {
        http_response_code(<span class="string">403</span>);
        <span class="keyword">echo</span> json_encode([<span class="string">'error'</span> => <span class="string">'Not a member of this organization'</span>]);
        <span class="keyword">exit</span>;
    }
    
    <span class="comment">// Check if role has required permissions</span>
    <span class="keyword">if</span> (!<span class="function">in_array</span>($membership[<span class="string">'role'</span>], $required_roles)) {
        http_response_code(<span class="string">403</span>);
        <span class="keyword">echo</span> json_encode([
            <span class="string">'error'</span> => <span class="string">'Insufficient permissions'</span>,
            <span class="string">'your_role'</span> => $membership[<span class="string">'role'</span>],
            <span class="string">'required_roles'</span> => $required_roles
        ]);
        <span class="keyword">exit</span>;
    }
    
    <span class="keyword">return</span> $membership[<span class="string">'role'</span>];
}

<span class="comment">// For DELETE operations</span>
<span class="keyword">case</span> <span class="string">'DELETE'</span>:
    <span class="comment">// Require admin or owner role</span>
    <span class="function">checkPermission</span>($pdo, $org[<span class="string">'id'</span>], $_SESSION[<span class="string">'user_id'</span>], [<span class="string">'admin'</span>, <span class="string">'owner'</span>]);
    
    <span class="comment">// Now safe to delete</span>
    <span class="function">deleteApiKey</span>($pdo, $key_uuid);
    <span class="keyword">break</span>;
                </div>
            </div>

            <div class="content-section">
                <h2>Security Checklist</h2>
                <p>Follow this checklist to prevent IDOR vulnerabilities in your applications:</p>

                <ul class="checklist">
                    <li>Always verify resource ownership before access</li>
                    <li>Check role permissions for every sensitive action</li>
                    <li>Use UUIDs instead of sequential IDs</li>
                    <li>Implement centralized authorization middleware</li>
                    <li>Log all access attempts for auditing</li>
                    <li>Apply the principle of least privilege</li>
                    <li>Deny access by default, then grant explicitly</li>
                    <li>Write unit tests for authorization logic</li>
                    <li>Perform regular security code reviews</li>
                    <li>Use automated security scanning tools</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Additional Defenses</h2>

                <h3>1. Centralized Authorization Layer</h3>
                <div class="code-block">
<span class="comment">// middleware/authorization.php</span>
<span class="keyword">class</span> <span class="function">Authorization</span> {
    <span class="keyword">private</span> $permissions = [
        <span class="string">'view_keys'</span>   => [<span class="string">'member'</span>, <span class="string">'admin'</span>, <span class="string">'owner'</span>],
        <span class="string">'create_keys'</span> => [<span class="string">'admin'</span>, <span class="string">'owner'</span>],
        <span class="string">'delete_keys'</span> => [<span class="string">'admin'</span>, <span class="string">'owner'</span>],
        <span class="string">'manage_org'</span>  => [<span class="string">'owner'</span>]
    ];
    
    <span class="keyword">public function</span> <span class="function">can</span>($user_role, $action) {
        <span class="keyword">return</span> <span class="function">in_array</span>($user_role, $this->permissions[$action] ?? []);
    }
}
                </div>

                <h3>2. Rate Limiting</h3>
                <p>Limit API requests to slow down enumeration attacks:</p>
                <div class="code-block">
<span class="comment">// Limit to 100 requests per minute per user</span>
<span class="keyword">if</span> (<span class="function">isRateLimited</span>($_SESSION[<span class="string">'user_id'</span>], <span class="string">100</span>, <span class="string">60</span>)) {
    http_response_code(<span class="string">429</span>);
    <span class="keyword">exit</span>(<span class="string">'Too many requests'</span>);
}
                </div>

                <h3>3. Audit Logging</h3>
                <p>Log all sensitive operations for forensics:</p>
                <div class="code-block">
<span class="keyword">function</span> <span class="function">logAction</span>($user_id, $action, $resource_id, $org_id) {
    $stmt = $pdo->prepare(<span class="string">"
        INSERT INTO audit_log (user_id, action, resource_id, org_id, ip_address, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    "</span>);
    $stmt->execute([$user_id, $action, $resource_id, $org_id, $_SERVER[<span class="string">'REMOTE_ADDR'</span>]]);
}

<span class="comment">// Usage</span>
<span class="function">logAction</span>($user_id, <span class="string">'DELETE_API_KEY'</span>, $key_uuid, $org_id);
                </div>
            </div>

            <div class="content-section">
                <h2>Testing for IDOR</h2>
                <p>Include these tests in your security testing:</p>
                <ul>
                    <li>Try accessing resources with different user IDs</li>
                    <li>Test with users of different role levels</li>
                    <li>Attempt to modify request parameters (UUIDs, IDs)</li>
                    <li>Check for parameter pollution attacks</li>
                    <li>Verify error messages don't leak information</li>
                </ul>
            </div>

            <div class="fix-card">
                <h4>✅ Key Takeaways</h4>
                <p style="color: #94a3b8;">
                    <strong>1.</strong> Always verify BOTH membership AND role permissions<br>
                    <strong>2.</strong> Implement authorization at the API layer, not just UI<br>
                    <strong>3.</strong> Use a centralized authorization system<br>
                    <strong>4.</strong> Log all sensitive operations<br>
                    <strong>5.</strong> Test authorization with different user roles
                </p>
            </div>

            <div class="nav-buttons">
                <a href="docs-exploitation.php" class="nav-btn prev">← Exploitation Guide</a>
                <a href="lab-description.php" class="nav-btn next">Start Lab →</a>
            </div>
        </main>
    </div>
</body>
</html>
