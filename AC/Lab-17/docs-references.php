<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References - IDOR Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(252, 109, 38, 0.3); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; font-size: 1.3rem; font-weight: bold; color: #fc6d26; text-decoration: none; }
        .logo svg { width: 32px; height: 32px; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .layout { display: flex; max-width: 1400px; margin: 0 auto; }
        .sidebar { width: 280px; min-height: calc(100vh - 60px); background: rgba(0, 0, 0, 0.3); border-right: 1px solid rgba(252, 109, 38, 0.2); padding: 1.5rem; position: sticky; top: 60px; height: calc(100vh - 60px); overflow-y: auto; }
        .sidebar h3 { color: #fc6d26; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(252, 109, 38, 0.3); }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a { display: block; padding: 0.6rem 1rem; color: #aaa; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-size: 0.9rem; }
        .sidebar-nav a:hover { background: rgba(252, 109, 38, 0.1); color: #fc6d26; }
        .sidebar-nav a.active { background: rgba(252, 109, 38, 0.2); color: #fc6d26; font-weight: 500; }
        .sidebar-nav .section-title { color: #666; font-size: 0.75rem; text-transform: uppercase; padding: 1rem 1rem 0.5rem; margin-top: 0.5rem; }
        .content { flex: 1; padding: 2rem 3rem; max-width: 900px; }
        .content h1 { color: #fc6d26; font-size: 2rem; margin-bottom: 0.5rem; }
        .content h2 { color: #fc6d26; font-size: 1.5rem; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(252, 109, 38, 0.3); }
        .content h3 { color: #e0e0e0; font-size: 1.2rem; margin: 1.5rem 0 0.75rem; }
        .content p { color: #aaa; line-height: 1.8; margin-bottom: 1rem; }
        .content ul, .content ol { color: #aaa; line-height: 1.8; margin-bottom: 1rem; padding-left: 1.5rem; }
        .content li { margin-bottom: 0.5rem; }
        .content a { color: #fc6d26; text-decoration: none; }
        .content a:hover { text-decoration: underline; }
        .nav-buttons { display: flex; justify-content: space-between; margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .nav-btn { padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(252, 109, 38, 0.3); border-radius: 8px; color: #ccc; text-decoration: none; transition: all 0.3s; }
        .nav-btn:hover { background: rgba(252, 109, 38, 0.2); color: #fc6d26; }
        .ref-card { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(252, 109, 38, 0.2); border-radius: 12px; padding: 1.25rem; margin: 0.75rem 0; transition: all 0.3s; }
        .ref-card:hover { border-color: #fc6d26; }
        .ref-card h4 { color: #fc6d26; margin-bottom: 0.5rem; }
        .ref-card p { margin: 0; font-size: 0.9rem; }
        .ref-card .type { display: inline-block; background: rgba(252, 109, 38, 0.2); color: #fc6d26; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.7rem; margin-bottom: 0.5rem; }
        @media (max-width: 900px) { .sidebar { display: none; } .content { padding: 1.5rem; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                    <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                    <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                    <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
                </svg>
                GitLab
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">Overview</a></li>
                <li class="section-title">Understanding</li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li class="section-title">Defense</li>
                <li><a href="docs-prevention.php">Prevention</a></li>
                <li><a href="docs-testing.php">Testing Techniques</a></li>
                <li class="section-title">Resources</li>
                <li><a href="docs-references.php" class="active">References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>References</h1>
            <p>External resources, standards, and further reading on IDOR and access control vulnerabilities.</p>

            <h2>GitLab Security Advisories</h2>
            <p>This lab is inspired by real vulnerabilities found in GitLab:</p>
            
            <div class="ref-card">
                <span class="type">CVE</span>
                <h4>External Status Check API IDOR</h4>
                <p>Vulnerability in GitLab's external status checks feature that allowed unauthorized access to status check configurations across projects.</p>
            </div>

            <div class="ref-card">
                <span class="type">Advisory</span>
                <h4>GitLab Security Release Blog</h4>
                <p>Regular security updates and patches for access control issues: <a href="https://about.gitlab.com/releases/categories/releases/" target="_blank">GitLab Releases</a></p>
            </div>

            <h2>OWASP Resources</h2>

            <div class="ref-card">
                <span class="type">Standard</span>
                <h4>OWASP API Security Top 10 - API1:2023 BOLA</h4>
                <p>Broken Object Level Authorization is the #1 API security risk. <a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" target="_blank">Read more</a></p>
            </div>

            <div class="ref-card">
                <span class="type">Guide</span>
                <h4>OWASP Testing Guide - IDOR</h4>
                <p>Comprehensive testing methodology for IDOR vulnerabilities. <a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" target="_blank">Testing Guide</a></p>
            </div>

            <div class="ref-card">
                <span class="type">Cheatsheet</span>
                <h4>Authorization Cheat Sheet</h4>
                <p>Best practices for implementing authorization. <a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" target="_blank">Cheat Sheet</a></p>
            </div>

            <h2>Bug Bounty Resources</h2>

            <div class="ref-card">
                <span class="type">Platform</span>
                <h4>HackerOne - GitLab Program</h4>
                <p>GitLab's official bug bounty program: <a href="https://hackerone.com/gitlab" target="_blank">GitLab on HackerOne</a></p>
            </div>

            <div class="ref-card">
                <span class="type">Writeups</span>
                <h4>Pentester Land - IDOR Writeups</h4>
                <p>Collection of real-world IDOR bug bounty reports: <a href="https://pentester.land/list-of-bug-bounty-writeups.html" target="_blank">Bug Bounty Writeups</a></p>
            </div>

            <h2>Tools</h2>

            <div class="ref-card">
                <span class="type">Tool</span>
                <h4>Burp Suite</h4>
                <p>Industry-standard web security testing tool: <a href="https://portswigger.net/burp" target="_blank">Burp Suite</a></p>
            </div>

            <div class="ref-card">
                <span class="type">Extension</span>
                <h4>Autorize - Burp Extension</h4>
                <p>Automated authorization testing: <a href="https://github.com/PortSwigger/autorize" target="_blank">GitHub - Autorize</a></p>
            </div>

            <div class="ref-card">
                <span class="type">Tool</span>
                <h4>Postman</h4>
                <p>API testing and development tool: <a href="https://www.postman.com/" target="_blank">Postman</a></p>
            </div>

            <h2>Educational Resources</h2>

            <div class="ref-card">
                <span class="type">Course</span>
                <h4>PortSwigger Web Security Academy</h4>
                <p>Free web security training including access control labs: <a href="https://portswigger.net/web-security/access-control" target="_blank">Access Control Labs</a></p>
            </div>

            <div class="ref-card">
                <span class="type">Video</span>
                <h4>IDOR Explained - Bug Bounty Hunting</h4>
                <p>Video tutorials on finding and exploiting IDOR vulnerabilities in bug bounty programs.</p>
            </div>

            <div class="ref-card">
                <span class="type">Book</span>
                <h4>The Web Application Hacker's Handbook</h4>
                <p>Comprehensive guide to web application security testing by Dafydd Stuttard and Marcus Pinto.</p>
            </div>

            <h2>CWE References</h2>
            <ul>
                <li><a href="https://cwe.mitre.org/data/definitions/639.html" target="_blank">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                <li><a href="https://cwe.mitre.org/data/definitions/284.html" target="_blank">CWE-284: Improper Access Control</a></li>
                <li><a href="https://cwe.mitre.org/data/definitions/285.html" target="_blank">CWE-285: Improper Authorization</a></li>
            </ul>

            <h2>Related Labs</h2>
            <p>Practice more access control vulnerabilities:</p>
            <ul>
                <li><a href="../lab1/">Lab 1: Unprotected Admin Functionality</a></li>
                <li><a href="../lab2/">Lab 2: Security Through Obscurity</a></li>
                <li><a href="../lab6/">Lab 6: Horizontal Privilege Escalation</a></li>
                <li><a href="../lab14/">Lab 14: IDOR with UUID</a></li>
            </ul>

            <div class="nav-buttons">
                <a href="docs-testing.php" class="nav-btn">← Testing Techniques</a>
                <a href="index.php" class="nav-btn">Back to Lab →</a>
            </div>
        </main>
    </div>
</body>
</html>
