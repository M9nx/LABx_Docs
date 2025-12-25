<?php
require_once 'config.php';
require_once '../progress.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References - Lab 18</title>
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
        .doc-section p, .doc-section li {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .resource-list { list-style: none; padding: 0; }
        .resource-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .resource-item:hover {
            border-color: rgba(150, 191, 72, 0.5);
            background: rgba(150, 191, 72, 0.05);
        }
        .resource-item h3 { color: #96bf48; margin-bottom: 0.5rem; font-size: 1.1rem; }
        .resource-item a { color: #88ccff; text-decoration: none; }
        .resource-item a:hover { text-decoration: underline; }
        .resource-item p { color: #888; font-size: 0.9rem; margin: 0; }
        .tag {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-right: 0.5rem;
            margin-top: 0.5rem;
        }
        .tag-h1 { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .tag-owasp { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
        .tag-video { background: rgba(255, 68, 68, 0.2); color: #ff8888; }
        .tag-guide { background: rgba(150, 191, 72, 0.2); color: #96bf48; }
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
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-comparison.php">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php" class="active">🔗 References</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="docs.php">Documentation</a> / References
            </div>

            <h1>🔗 References & Further Reading</h1>

            <div class="doc-section">
                <h2>HackerOne Reports</h2>
                <ul class="resource-list">
                    <li class="resource-item">
                        <h3><a href="https://hackerone.com/reports/examples" target="_blank">Shopify IDOR - Session Expiration</a></h3>
                        <p>Original HackerOne report documenting the session expiration IDOR vulnerability in Shopify's admin panel. This lab is based on this real-world finding.</p>
                        <span class="tag tag-h1">HackerOne</span>
                        <span class="tag tag-guide">Shopify</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://hackerone.com/reports/227522" target="_blank">IDOR to Change Email Address</a></h3>
                        <p>A similar IDOR vulnerability where an attacker could change any user's email address by manipulating the user ID parameter.</p>
                        <span class="tag tag-h1">HackerOne</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://hackerone.com/reports/165727" target="_blank">IDOR in User Profile API</a></h3>
                        <p>Example of IDOR vulnerability in a user profile API endpoint, demonstrating the common pattern of insufficient authorization checks.</p>
                        <span class="tag tag-h1">HackerOne</span>
                    </li>
                </ul>
            </div>

            <div class="doc-section">
                <h2>OWASP Resources</h2>
                <ul class="resource-list">
                    <li class="resource-item">
                        <h3><a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" target="_blank">API1:2023 - Broken Object Level Authorization</a></h3>
                        <p>OWASP API Security Top 10 entry for BOLA (the API-focused term for IDOR). Comprehensive coverage of the vulnerability class.</p>
                        <span class="tag tag-owasp">OWASP</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" target="_blank">IDOR Prevention Cheat Sheet</a></h3>
                        <p>OWASP's comprehensive guide to preventing IDOR vulnerabilities with practical code examples and best practices.</p>
                        <span class="tag tag-owasp">OWASP</span>
                        <span class="tag tag-guide">Cheat Sheet</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank">A01:2021 - Broken Access Control</a></h3>
                        <p>OWASP Top 10 entry covering access control vulnerabilities, including IDOR. Now ranked #1 in the 2021 edition.</p>
                        <span class="tag tag-owasp">OWASP</span>
                    </li>
                </ul>
            </div>

            <div class="doc-section">
                <h2>Learning Resources</h2>
                <ul class="resource-list">
                    <li class="resource-item">
                        <h3><a href="https://portswigger.net/web-security/access-control/idor" target="_blank">PortSwigger - IDOR Labs</a></h3>
                        <p>Interactive labs from the creators of Burp Suite covering various IDOR scenarios with hands-on exercises.</p>
                        <span class="tag tag-guide">Interactive</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://www.youtube.com/watch?v=rloqMGcPMkI" target="_blank">Bug Bounty - Finding IDOR Vulnerabilities</a></h3>
                        <p>Video tutorial on identifying and exploiting IDOR vulnerabilities in bug bounty programs.</p>
                        <span class="tag tag-video">Video</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://github.com/OWASP/API-Security" target="_blank">OWASP API Security Project</a></h3>
                        <p>GitHub repository with resources, documentation, and tools for API security testing including IDOR/BOLA.</p>
                        <span class="tag tag-owasp">OWASP</span>
                        <span class="tag tag-guide">GitHub</span>
                    </li>
                </ul>
            </div>

            <div class="doc-section">
                <h2>Tools</h2>
                <ul class="resource-list">
                    <li class="resource-item">
                        <h3><a href="https://portswigger.net/burp" target="_blank">Burp Suite</a></h3>
                        <p>Industry-standard web security testing tool. Essential for intercepting and modifying HTTP requests to exploit IDOR.</p>
                        <span class="tag tag-guide">Tool</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://www.postman.com/" target="_blank">Postman</a></h3>
                        <p>API development platform useful for crafting and sending custom requests to test IDOR vulnerabilities.</p>
                        <span class="tag tag-guide">Tool</span>
                    </li>
                    <li class="resource-item">
                        <h3><a href="https://github.com/assetnote/kiterunner" target="_blank">Kiterunner</a></h3>
                        <p>Tool for discovering API endpoints which can then be tested for IDOR vulnerabilities.</p>
                        <span class="tag tag-guide">Tool</span>
                    </li>
                </ul>
            </div>

            <div class="nav-buttons">
                <a href="docs-comparison.php" class="btn btn-secondary">← Code Comparison</a>
                <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            </div>
        </main>
    </div>
</body>
</html>
