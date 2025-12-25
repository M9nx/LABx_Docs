<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - KeyVault IDOR Lab</title>
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
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #fff; }
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
        .docs-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .docs-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        .docs-card h2 {
            color: #5eead4;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .docs-card p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .doc-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .doc-link:hover {
            background: rgba(20, 184, 166, 0.1);
            border-left-color: #14b8a6;
            transform: translateX(5px);
        }
        .doc-link .info h4 {
            color: #f8fafc;
            margin-bottom: 0.25rem;
        }
        .doc-link .info p {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        .doc-link .arrow {
            color: #14b8a6;
            font-size: 1.5rem;
        }
        .topic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .topic-card {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid transparent;
        }
        .topic-card:hover {
            background: rgba(20, 184, 166, 0.1);
            border-color: rgba(20, 184, 166, 0.3);
            transform: translateY(-3px);
        }
        .topic-card .icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        .topic-card h4 {
            color: #f8fafc;
            margin-bottom: 0.5rem;
        }
        .topic-card p {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        .quick-start {
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.2), rgba(13, 148, 136, 0.1));
            border: 1px solid rgba(20, 184, 166, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .quick-start h3 {
            color: #5eead4;
            margin-bottom: 1rem;
        }
        .quick-start ol {
            margin-left: 1.5rem;
            color: #94a3b8;
        }
        .quick-start li {
            margin-bottom: 0.75rem;
            line-height: 1.6;
        }
        .quick-start code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #5eead4;
            font-size: 0.9rem;
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
                <a href="login.php">Login</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="docs-layout">
        <aside class="sidebar">
            <h3>Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php" class="active">📚 Overview</a></li>
                <li><a href="docs-idor.php">🔓 IDOR Fundamentals</a></li>
                <li><a href="docs-api-security.php">🔐 API Security</a></li>
                <li><a href="docs-rbac.php">👥 Role-Based Access</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation Guide</a></li>
                <li><a href="docs-mitigation.php">🛡️ Mitigation</a></li>
            </ul>
            <h3 style="margin-top: 2rem;">Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="lab-description.php">📖 Lab Instructions</a></li>
                <li><a href="login.php">🚀 Start Lab</a></li>
                <li><a href="setup_db.php">🔄 Reset Database</a></li>
            </ul>
        </aside>

        <main class="docs-content">
            <h1>📚 Documentation</h1>
            <p class="subtitle">Comprehensive guide to understanding and exploiting IDOR vulnerabilities in API key management systems</p>

            <div class="quick-start">
                <h3>🚀 Quick Start</h3>
                <ol>
                    <li>Run <code>setup_db.php</code> to initialize the database</li>
                    <li>Login as <code>attacker_member / attacker123</code></li>
                    <li>Navigate to TechCorp Inc organization</li>
                    <li>Try to CREATE, VIEW, and DELETE API keys</li>
                    <li>Observe that role permissions are not enforced!</li>
                </ol>
            </div>

            <div class="docs-card">
                <h2>📖 Documentation Topics</h2>
                <p>Explore comprehensive guides on IDOR vulnerabilities, API security, and role-based access control.</p>

                <a href="docs-idor.php" class="doc-link">
                    <div class="info">
                        <h4>🔓 IDOR Fundamentals</h4>
                        <p>Understanding Insecure Direct Object Reference vulnerabilities</p>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="docs-api-security.php" class="doc-link">
                    <div class="info">
                        <h4>🔐 API Security Best Practices</h4>
                        <p>Securing API endpoints and preventing unauthorized access</p>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="docs-rbac.php" class="doc-link">
                    <div class="info">
                        <h4>👥 Role-Based Access Control</h4>
                        <p>Implementing proper RBAC in multi-tenant applications</p>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="docs-exploitation.php" class="doc-link">
                    <div class="info">
                        <h4>⚔️ Exploitation Guide</h4>
                        <p>Step-by-step guide to exploiting this lab's vulnerability</p>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <a href="docs-mitigation.php" class="doc-link">
                    <div class="info">
                        <h4>🛡️ Mitigation Strategies</h4>
                        <p>How to fix and prevent IDOR vulnerabilities</p>
                    </div>
                    <span class="arrow">→</span>
                </a>
            </div>

            <div class="docs-card">
                <h2>🎯 Key Concepts</h2>
                <div class="topic-grid">
                    <div class="topic-card">
                        <div class="icon">🔑</div>
                        <h4>API Key Management</h4>
                        <p>Understanding how organizations manage sensitive API credentials</p>
                    </div>
                    <div class="topic-card">
                        <div class="icon">👤</div>
                        <h4>User Roles</h4>
                        <p>Owner, Admin, and Member role hierarchies</p>
                    </div>
                    <div class="topic-card">
                        <div class="icon">🏢</div>
                        <h4>Multi-Tenancy</h4>
                        <p>Organization-based data isolation</p>
                    </div>
                    <div class="topic-card">
                        <div class="icon">⚠️</div>
                        <h4>Broken Access Control</h4>
                        <p>OWASP Top 10 #1 vulnerability type</p>
                    </div>
                </div>
            </div>

            <div class="docs-card">
                <h2>🔍 Lab Vulnerability Summary</h2>
                <p>
                    This lab demonstrates an IDOR vulnerability where the API endpoint <code>api/keys.php</code> 
                    checks if a user is a <strong>member</strong> of an organization but fails to verify their 
                    <strong>role permissions</strong> before allowing sensitive operations.
                </p>
                <p style="margin-top: 1rem;">
                    As a result, users with <code>member</code> role can:
                </p>
                <ul style="margin-left: 1.5rem; margin-top: 0.5rem; color: #94a3b8;">
                    <li>VIEW all API keys including sensitive production credentials</li>
                    <li>CREATE new API keys with elevated scopes</li>
                    <li>DELETE existing API keys, causing service disruption</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
