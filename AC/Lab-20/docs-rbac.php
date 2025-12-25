<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role-Based Access Control - KeyVault Docs</title>
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
        .role-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        .role-table th, .role-table td {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .role-table th { color: #94a3b8; font-weight: 500; }
        .role-table td { color: #e0e0e0; }
        .role-table th:first-child, .role-table td:first-child { text-align: left; }
        .role-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .role-owner { background: linear-gradient(135deg, #f59e0b, #d97706); color: #000; }
        .role-admin { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; }
        .role-member { background: rgba(100, 116, 139, 0.3); color: #94a3b8; }
        .check { color: #22c55e; font-size: 1.25rem; }
        .cross { color: #ef4444; font-size: 1.25rem; }
        .hierarchy-diagram {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin: 1.5rem 0;
            text-align: center;
        }
        .hierarchy-level {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .hierarchy-role {
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
        }
        .hierarchy-arrow {
            color: #64748b;
            font-size: 1.5rem;
            margin: 0.5rem 0;
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
                <li><a href="docs-rbac.php" class="active">👥 Role-Based Access</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation Guide</a></li>
                <li><a href="docs-mitigation.php">🛡️ Mitigation</a></li>
            </ul>
        </aside>

        <main class="docs-content">
            <div class="breadcrumb">
                <a href="docs.php">Docs</a>
                <span>/</span>
                <span>Role-Based Access Control</span>
            </div>

            <h1>👥 Role-Based Access Control</h1>
            <p class="subtitle">Implementing proper RBAC in multi-tenant applications with organization hierarchies</p>

            <div class="content-section">
                <h2>What is RBAC?</h2>
                <p>
                    <strong>Role-Based Access Control (RBAC)</strong> is a method of regulating access to resources 
                    based on the roles of individual users within an organization. It's a fundamental security model 
                    used in enterprise systems, SaaS applications, and cloud platforms.
                </p>

                <div class="info-box">
                    <h4>💡 RBAC Components</h4>
                    <ul style="margin-left: 1.5rem; margin-top: 0.5rem; color: #94a3b8;">
                        <li><strong>Users:</strong> Individual accounts that need access</li>
                        <li><strong>Roles:</strong> Named collections of permissions (owner, admin, member)</li>
                        <li><strong>Permissions:</strong> Specific actions that can be performed</li>
                        <li><strong>Resources:</strong> Objects being protected (API keys, data)</li>
                    </ul>
                </div>
            </div>

            <div class="content-section">
                <h2>KeyVault Role Hierarchy</h2>
                
                <div class="hierarchy-diagram">
                    <div class="hierarchy-level">
                        <div class="hierarchy-role role-owner">👑 Owner</div>
                    </div>
                    <div class="hierarchy-arrow">↓</div>
                    <div class="hierarchy-level">
                        <div class="hierarchy-role role-admin">🔧 Admin</div>
                    </div>
                    <div class="hierarchy-arrow">↓</div>
                    <div class="hierarchy-level">
                        <div class="hierarchy-role role-member">👤 Member</div>
                    </div>
                </div>

                <p>
                    In KeyVault, organizations have a three-tier role system. Each role inherits permissions 
                    from roles below it, plus has additional capabilities.
                </p>
            </div>

            <div class="content-section">
                <h2>Permission Matrix</h2>
                <p>This table shows what each role SHOULD be able to do (expected behavior):</p>

                <table class="role-table">
                    <tr>
                        <th>Permission</th>
                        <th><span class="role-badge role-owner">Owner</span></th>
                        <th><span class="role-badge role-admin">Admin</span></th>
                        <th><span class="role-badge role-member">Member</span></th>
                    </tr>
                    <tr>
                        <td>View API Keys</td>
                        <td><span class="check">✓</span></td>
                        <td><span class="check">✓</span></td>
                        <td><span class="check">✓</span> (Limited)</td>
                    </tr>
                    <tr>
                        <td>Create API Keys</td>
                        <td><span class="check">✓</span></td>
                        <td><span class="check">✓</span></td>
                        <td><span class="cross">✗</span></td>
                    </tr>
                    <tr>
                        <td>Delete API Keys</td>
                        <td><span class="check">✓</span></td>
                        <td><span class="check">✓</span></td>
                        <td><span class="cross">✗</span></td>
                    </tr>
                    <tr>
                        <td>Manage Members</td>
                        <td><span class="check">✓</span></td>
                        <td><span class="check">✓</span></td>
                        <td><span class="cross">✗</span></td>
                    </tr>
                    <tr>
                        <td>Delete Organization</td>
                        <td><span class="check">✓</span></td>
                        <td><span class="cross">✗</span></td>
                        <td><span class="cross">✗</span></td>
                    </tr>
                    <tr>
                        <td>Transfer Ownership</td>
                        <td><span class="check">✓</span></td>
                        <td><span class="cross">✗</span></td>
                        <td><span class="cross">✗</span></td>
                    </tr>
                </table>

                <div class="danger-box">
                    <h4>⚠️ The Vulnerability</h4>
                    <p>
                        In this lab, the actual permission enforcement is broken. Members can perform ALL actions 
                        (CREATE, DELETE) because the API only checks membership, not role permissions!
                    </p>
                </div>
            </div>

            <div class="content-section">
                <h2>Database Schema</h2>
                <p>The organization membership is stored in the <code>org_members</code> table:</p>

                <div class="code-block">
<span class="keyword">CREATE TABLE</span> org_members (
    id <span class="function">INT</span> PRIMARY KEY AUTO_INCREMENT,
    org_id <span class="function">INT</span> NOT NULL,
    user_id <span class="function">INT</span> NOT NULL,
    role <span class="function">ENUM</span>(<span class="string">'owner'</span>, <span class="string">'admin'</span>, <span class="string">'member'</span>) DEFAULT <span class="string">'member'</span>,
    joined_at <span class="function">TIMESTAMP</span> DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (org_id) REFERENCES organizations(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY (org_id, user_id)
);
                </div>

                <h3>Role Check Implementation</h3>
                <p>Here's how role checking SHOULD be implemented:</p>

                <div class="code-block">
<span class="keyword">function</span> <span class="function">hasPermission</span>($pdo, $org_id, $user_id, $required_roles) {
    $stmt = $pdo->prepare(<span class="string">"
        SELECT role FROM org_members 
        WHERE org_id = ? AND user_id = ?
    "</span>);
    $stmt->execute([$org_id, $user_id]);
    $membership = $stmt->fetch();
    
    <span class="keyword">if</span> (!$membership) {
        <span class="keyword">return false</span>; <span class="comment">// Not a member</span>
    }
    
    <span class="comment">// Check if user's role is in required roles</span>
    <span class="keyword">return</span> <span class="function">in_array</span>($membership[<span class="string">'role'</span>], $required_roles);
}

<span class="comment">// Usage</span>
<span class="keyword">if</span> (!<span class="function">hasPermission</span>($pdo, $org_id, $user_id, [<span class="string">'owner'</span>, <span class="string">'admin'</span>])) {
    http_response_code(<span class="string">403</span>);
    <span class="keyword">exit</span>(<span class="string">'Insufficient permissions'</span>);
}
                </div>
            </div>

            <div class="content-section">
                <h2>RBAC Best Practices</h2>
                <ul style="margin-left: 1.5rem; color: #94a3b8;">
                    <li><strong>Principle of Least Privilege:</strong> Grant minimum permissions needed</li>
                    <li><strong>Deny by Default:</strong> Start with no permissions and add explicitly</li>
                    <li><strong>Centralize Authorization:</strong> Use a single authorization layer</li>
                    <li><strong>Audit Role Changes:</strong> Log all permission modifications</li>
                    <li><strong>Regular Reviews:</strong> Periodically review role assignments</li>
                    <li><strong>Separation of Duties:</strong> Prevent single user from having conflicting permissions</li>
                </ul>
            </div>

            <div class="nav-buttons">
                <a href="docs-api-security.php" class="nav-btn prev">← API Security</a>
                <a href="docs-exploitation.php" class="nav-btn next">Exploitation Guide →</a>
            </div>
        </main>
    </div>
</body>
</html>
