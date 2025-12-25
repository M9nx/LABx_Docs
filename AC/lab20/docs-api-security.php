<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Security - KeyVault Docs</title>
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
        .code-block .method { color: #22c55e; font-weight: bold; }
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
        .api-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .api-table th, .api-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .api-table th { color: #94a3b8; font-weight: 500; }
        .api-table td { color: #e0e0e0; }
        .method-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .method-get { background: #22c55e; color: #000; }
        .method-post { background: #3b82f6; color: #fff; }
        .method-put { background: #f59e0b; color: #000; }
        .method-delete { background: #ef4444; color: #fff; }
        .checklist {
            list-style: none;
            margin-left: 0;
        }
        .checklist li {
            padding: 0.5rem 0;
            padding-left: 2rem;
            position: relative;
        }
        .checklist li::before {
            content: '☐';
            position: absolute;
            left: 0;
            color: #64748b;
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
                <li><a href="docs-api-security.php" class="active">🔐 API Security</a></li>
                <li><a href="docs-rbac.php">👥 Role-Based Access</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation Guide</a></li>
                <li><a href="docs-mitigation.php">🛡️ Mitigation</a></li>
            </ul>
        </aside>

        <main class="docs-content">
            <div class="breadcrumb">
                <a href="docs.php">Docs</a>
                <span>/</span>
                <span>API Security</span>
            </div>

            <h1>🔐 API Security Best Practices</h1>
            <p class="subtitle">Securing REST APIs and preventing unauthorized access to sensitive endpoints</p>

            <div class="content-section">
                <h2>API Security Fundamentals</h2>
                <p>
                    APIs are the backbone of modern applications, but they also represent a significant attack surface. 
                    Proper API security involves multiple layers of protection including authentication, authorization, 
                    input validation, and rate limiting.
                </p>

                <div class="info-box">
                    <h4>🔑 The Three A's of API Security</h4>
                    <ul>
                        <li><strong>Authentication:</strong> Verify WHO is making the request</li>
                        <li><strong>Authorization:</strong> Verify WHAT they're allowed to do</li>
                        <li><strong>Auditing:</strong> Log EVERYTHING for accountability</li>
                    </ul>
                </div>
            </div>

            <div class="content-section">
                <h2>Lab API Endpoints</h2>
                <p>The vulnerable API in this lab (<code>api/keys.php</code>) supports the following operations:</p>

                <table class="api-table">
                    <tr>
                        <th>Method</th>
                        <th>Endpoint</th>
                        <th>Description</th>
                        <th>Required Role</th>
                    </tr>
                    <tr>
                        <td><span class="method-badge method-get">GET</span></td>
                        <td><code>/api/keys.php?org_uuid=...</code></td>
                        <td>List all API keys</td>
                        <td>Member+ (actual: any member)</td>
                    </tr>
                    <tr>
                        <td><span class="method-badge method-post">POST</span></td>
                        <td><code>/api/keys.php</code></td>
                        <td>Create new API key</td>
                        <td>Admin+ (actual: any member)</td>
                    </tr>
                    <tr>
                        <td><span class="method-badge method-delete">DELETE</span></td>
                        <td><code>/api/keys.php</code></td>
                        <td>Delete API key</td>
                        <td>Admin+ (actual: any member)</td>
                    </tr>
                </table>

                <div class="danger-box">
                    <h4>⚠️ Security Issue</h4>
                    <p>
                        Notice the discrepancy between "Required Role" and "actual" access. All endpoints allow any 
                        organization member to perform the action, regardless of their role permissions.
                    </p>
                </div>
            </div>

            <div class="content-section">
                <h2>Common API Vulnerabilities</h2>

                <h3>1. Broken Object Level Authorization (BOLA)</h3>
                <p>APIs often expose endpoints that handle object identifiers. Attackers can manipulate these to access unauthorized data.</p>
                <div class="code-block">
<span class="comment">// Vulnerable: No ownership check</span>
<span class="method">GET</span> /api/keys/<span class="string">{key_uuid}</span>

<span class="comment">// Attacker can access any key by changing the UUID</span>
<span class="method">GET</span> /api/keys/<span class="string">another-users-key-uuid</span>
                </div>

                <h3>2. Broken Function Level Authorization</h3>
                <p>Administrative functions may be accessible to regular users if role checks are missing.</p>
                <div class="code-block">
<span class="comment">// Vulnerable: No role check</span>
<span class="method">DELETE</span> /api/keys/<span class="string">{key_uuid}</span>

<span class="comment">// A "member" user can delete keys like an "admin"</span>
                </div>

                <h3>3. Mass Assignment</h3>
                <p>APIs that automatically bind request parameters to object properties can be exploited.</p>
                <div class="code-block">
<span class="comment">// Vulnerable: Accepting all parameters</span>
<span class="method">POST</span> /api/users
{
  <span class="string">"username"</span>: <span class="string">"attacker"</span>,
  <span class="string">"role"</span>: <span class="string">"admin"</span>  <span class="comment">// Attacker elevates privileges!</span>
}
                </div>
            </div>

            <div class="content-section">
                <h2>API Security Checklist</h2>
                <ul class="checklist">
                    <li>Implement proper authentication (JWT, OAuth 2.0, API Keys)</li>
                    <li>Verify authorization for EVERY endpoint and action</li>
                    <li>Use UUIDs instead of sequential IDs</li>
                    <li>Validate all input parameters</li>
                    <li>Implement rate limiting</li>
                    <li>Use HTTPS for all API traffic</li>
                    <li>Log all API requests for auditing</li>
                    <li>Return appropriate error messages (don't leak info)</li>
                    <li>Implement CORS properly</li>
                    <li>Version your APIs</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Secure API Implementation</h2>
                <div class="code-block">
<span class="comment">// Secure implementation with role check</span>
<span class="keyword">function</span> <span class="function">deleteApiKey</span>($pdo, $org_id, $key_uuid, $user_id) {
    <span class="comment">// 1. Check membership</span>
    $membership = <span class="function">getOrgMembership</span>($pdo, $org_id, $user_id);
    
    <span class="keyword">if</span> (!$membership) {
        <span class="keyword">throw new</span> <span class="function">UnauthorizedException</span>(<span class="string">'Not a member'</span>);
    }
    
    <span class="comment">// 2. Check role permissions</span>
    <span class="keyword">if</span> (!<span class="function">in_array</span>($membership['role'], [<span class="string">'admin'</span>, <span class="string">'owner'</span>])) {
        <span class="keyword">throw new</span> <span class="function">ForbiddenException</span>(<span class="string">'Insufficient permissions'</span>);
    }
    
    <span class="comment">// 3. Verify key belongs to org</span>
    $key = <span class="function">getApiKey</span>($pdo, $key_uuid);
    <span class="keyword">if</span> ($key['org_id'] !== $org_id) {
        <span class="keyword">throw new</span> <span class="function">NotFoundException</span>(<span class="string">'Key not found'</span>);
    }
    
    <span class="comment">// 4. Perform action</span>
    <span class="function">deleteKey</span>($pdo, $key_uuid);
    
    <span class="comment">// 5. Audit log</span>
    <span class="function">logAction</span>($user_id, <span class="string">'DELETE_API_KEY'</span>, $key_uuid);
}
                </div>
            </div>

            <div class="nav-buttons">
                <a href="docs-idor.php" class="nav-btn prev">← IDOR Fundamentals</a>
                <a href="docs-rbac.php" class="nav-btn next">Role-Based Access →</a>
            </div>
        </main>
    </div>
</body>
</html>
