<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References - Lab 19 Documentation</title>
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
            max-width: 900px;
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
        .content-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-section h2 {
            color: #a5b4fc;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .resource-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .resource-card:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
        }
        .resource-card h3 {
            color: #e2e8f0;
            margin-bottom: 0.5rem;
        }
        .resource-card h3 a {
            color: #93c5fd;
            text-decoration: none;
        }
        .resource-card h3 a:hover {
            text-decoration: underline;
        }
        .resource-card p {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }
        .resource-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .tag {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .tag.owasp { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .tag.hackerone { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
        .tag.bugcrowd { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .tag.video { background: rgba(139, 92, 246, 0.2); color: #c4b5fd; }
        .hackerone-report {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .report-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .report-severity {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .severity-critical { background: #dc2626; color: white; }
        .severity-high { background: #ea580c; color: white; }
        .severity-medium { background: #ca8a04; color: white; }
        .report-bounty {
            margin-left: auto;
            color: #6ee7b7;
            font-weight: 600;
        }
        .hackerone-report h4 {
            color: #e2e8f0;
            margin-bottom: 0.5rem;
        }
        .hackerone-report h4 a {
            color: #6ee7b7;
            text-decoration: none;
        }
        .hackerone-report p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin: 0;
        }
        .cwe-box {
            background: rgba(245, 158, 11, 0.1);
            border-left: 4px solid #f59e0b;
            padding: 1rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
        }
        .cwe-box h4 { color: #fcd34d; margin-bottom: 0.5rem; }
        .cwe-box p { color: #94a3b8; margin: 0; font-size: 0.9rem; }
        .book-card {
            display: flex;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .book-icon {
            font-size: 2.5rem;
            opacity: 0.7;
        }
        .book-info h4 { color: #e2e8f0; margin-bottom: 0.25rem; }
        .book-info p { color: #64748b; font-size: 0.85rem; margin: 0; }
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
                <li><a href="docs-comparison.php">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php" class="active">📖 References</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="docs.php">Documentation</a> / External References
            </div>

            <h1 class="page-title">External References</h1>
            <p class="page-subtitle">Learning resources, bug bounty reports, and security guidelines</p>

            <div class="cwe-box">
                <h4>CWE-639: Authorization Bypass Through User-Controlled Key</h4>
                <p>
                    The system's authorization functionality does not prevent one user from gaining access 
                    to another user's data by modifying the key value identifying the data.
                </p>
            </div>

            <div class="content-section">
                <h2>🏆 Real-World Bug Bounty Reports</h2>
                <p style="color: #94a3b8; margin-bottom: 1.5rem;">
                    These HackerOne reports demonstrate real IDOR vulnerabilities found in production applications:
                </p>

                <div class="hackerone-report">
                    <div class="report-header">
                        <span class="report-severity severity-high">HIGH</span>
                        <span class="report-bounty">$5,000</span>
                    </div>
                    <h4><a href="https://hackerone.com/reports/1167782" target="_blank">IDOR in Delete Photos Feature</a></h4>
                    <p>Attacker could delete any user's photos by manipulating the photo_id parameter in delete requests.</p>
                </div>

                <div class="hackerone-report">
                    <div class="report-header">
                        <span class="report-severity severity-critical">CRITICAL</span>
                        <span class="report-bounty">$10,000</span>
                    </div>
                    <h4><a href="https://hackerone.com/reports/1031527" target="_blank">IDOR Allows Account Deletion</a></h4>
                    <p>Missing ownership validation allowed attackers to delete any user account on the platform.</p>
                </div>

                <div class="hackerone-report">
                    <div class="report-header">
                        <span class="report-severity severity-medium">MEDIUM</span>
                        <span class="report-bounty">$2,500</span>
                    </div>
                    <h4><a href="https://hackerone.com/reports/746000" target="_blank">IDOR in Saved Items Deletion</a></h4>
                    <p>Similar to this lab - allowed deletion of other users' saved/bookmarked items.</p>
                </div>
            </div>

            <div class="content-section">
                <h2>📚 OWASP Resources</h2>

                <div class="resource-card">
                    <h3><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank">OWASP Top 10: A01 Broken Access Control</a></h3>
                    <p>
                        Comprehensive guide on broken access control vulnerabilities, including IDOR, 
                        with prevention strategies and examples.
                    </p>
                    <div class="resource-tags">
                        <span class="tag owasp">OWASP</span>
                        <span class="tag">Top 10</span>
                        <span class="tag">Access Control</span>
                    </div>
                </div>

                <div class="resource-card">
                    <h3><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" target="_blank">OWASP Testing Guide: IDOR</a></h3>
                    <p>
                        Detailed testing methodology for discovering IDOR vulnerabilities, with step-by-step 
                        instructions for security testers.
                    </p>
                    <div class="resource-tags">
                        <span class="tag owasp">OWASP</span>
                        <span class="tag">Testing Guide</span>
                        <span class="tag">Methodology</span>
                    </div>
                </div>

                <div class="resource-card">
                    <h3><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" target="_blank">OWASP Authorization Cheat Sheet</a></h3>
                    <p>
                        Best practices for implementing proper authorization, including object-level 
                        access control and IDOR prevention.
                    </p>
                    <div class="resource-tags">
                        <span class="tag owasp">OWASP</span>
                        <span class="tag">Cheat Sheet</span>
                        <span class="tag">Best Practices</span>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h2>🎥 Video Resources</h2>

                <div class="resource-card">
                    <h3><a href="https://www.youtube.com/watch?v=x6F6g5Bdy2E" target="_blank">PwnFunction: IDOR Explained</a></h3>
                    <p>
                        Animated explanation of IDOR vulnerabilities, how they work, and how to exploit them.
                    </p>
                    <div class="resource-tags">
                        <span class="tag video">Video</span>
                        <span class="tag">Beginner Friendly</span>
                    </div>
                </div>

                <div class="resource-card">
                    <h3><a href="https://www.youtube.com/watch?v=3K1-a7dnA60" target="_blank">InsiderPhD: Finding IDORs</a></h3>
                    <p>
                        Bug bounty hunter's guide to discovering IDOR vulnerabilities in web applications.
                    </p>
                    <div class="resource-tags">
                        <span class="tag video">Video</span>
                        <span class="tag hackerone">Bug Bounty</span>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h2>🛠️ Testing Tools</h2>

                <div class="resource-card">
                    <h3><a href="https://portswigger.net/burp" target="_blank">Burp Suite</a></h3>
                    <p>
                        Industry-standard web security testing tool. The Autorize extension is specifically 
                        designed for detecting IDOR vulnerabilities automatically.
                    </p>
                    <div class="resource-tags">
                        <span class="tag">Tool</span>
                        <span class="tag">Proxy</span>
                        <span class="tag">Professional</span>
                    </div>
                </div>

                <div class="resource-card">
                    <h3><a href="https://github.com/Quitten/Autorize" target="_blank">Autorize (Burp Extension)</a></h3>
                    <p>
                        Automatically detects authorization issues including IDOR by comparing responses 
                        between different user sessions.
                    </p>
                    <div class="resource-tags">
                        <span class="tag">Extension</span>
                        <span class="tag">Automation</span>
                        <span class="tag">Open Source</span>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h2>📖 Recommended Reading</h2>

                <div class="book-card">
                    <span class="book-icon">📕</span>
                    <div class="book-info">
                        <h4>The Web Application Hacker's Handbook</h4>
                        <p>By Dafydd Stuttard & Marcus Pinto - Chapter 8 covers access controls</p>
                    </div>
                </div>

                <div class="book-card">
                    <span class="book-icon">📗</span>
                    <div class="book-info">
                        <h4>Bug Bounty Bootcamp</h4>
                        <p>By Vickie Li - Dedicated chapter on IDOR vulnerabilities</p>
                    </div>
                </div>

                <div class="book-card">
                    <span class="book-icon">📘</span>
                    <div class="book-info">
                        <h4>Real-World Bug Hunting</h4>
                        <p>By Peter Yaworski - Includes real IDOR case studies</p>
                    </div>
                </div>
            </div>

            <div class="nav-buttons">
                <a href="docs-comparison.php" class="nav-btn">← Code Comparison</a>
                <a href="docs.php" class="nav-btn">Back to Overview →</a>
            </div>
        </main>
    </div>
</body>
</html>
