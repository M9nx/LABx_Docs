<?php
// Lab 21: Documentation Hub
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR on Settings | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .nav-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .nav-top a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-top a:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .page-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .page-header p {
            color: #94a3b8;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .doc-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .doc-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.15);
        }
        .doc-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.25rem;
        }
        .doc-card h3 {
            color: #e2e8f0;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .doc-card p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .doc-topics {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .topic-tag {
            padding: 0.25rem 0.75rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            font-size: 0.75rem;
            color: #a5b4fc;
        }
        .doc-card.overview .doc-icon { background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(139, 92, 246, 0.3)); }
        .doc-card.walkthrough .doc-icon { background: linear-gradient(135deg, rgba(245, 158, 11, 0.3), rgba(249, 115, 22, 0.3)); }
        .doc-card.technical .doc-icon { background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3)); }
        .doc-card.mitigation .doc-icon { background: linear-gradient(135deg, rgba(16, 185, 129, 0.3), rgba(5, 150, 105, 0.3)); }
        .doc-card.comparison .doc-icon { background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(37, 99, 235, 0.3)); }
        .doc-card.references .doc-icon { background: linear-gradient(135deg, rgba(168, 85, 247, 0.3), rgba(139, 92, 246, 0.3)); }
        .quick-links {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
        }
        .quick-links h2 {
            color: #a5b4fc;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .quick-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 8px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .quick-link:hover {
            background: rgba(99, 102, 241, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-top">
            <a href="index.php">← Back to Lab Home</a>
            <a href="lab-description.php">📖 Lab Instructions</a>
            <a href="../index.php">🏠 All Labs</a>
        </nav>
        
        <header class="page-header">
            <h1>📚 Documentation</h1>
            <p>Comprehensive guides covering IDOR vulnerabilities, exploitation techniques, and secure coding practices for settings management.</p>
        </header>
        
        <div class="docs-grid">
            <a href="docs-overview.php" class="doc-card overview">
                <div class="doc-icon">📋</div>
                <h3>1. Lab Overview</h3>
                <p>Understand what IDOR and Broken Access Control vulnerabilities are, why they occur, and their impact on application security.</p>
                <div class="doc-topics">
                    <span class="topic-tag">IDOR Definition</span>
                    <span class="topic-tag">Access Control</span>
                    <span class="topic-tag">OWASP Top 10</span>
                </div>
            </a>
            
            <a href="docs-walkthrough.php" class="doc-card walkthrough">
                <div class="doc-icon">🚶</div>
                <h3>2. Step-by-Step Walkthrough</h3>
                <p>Detailed guide on exploiting the IDOR vulnerability including logging in, capturing requests, and modifying settings.</p>
                <div class="doc-topics">
                    <span class="topic-tag">Exploitation</span>
                    <span class="topic-tag">Burp Suite</span>
                    <span class="topic-tag">Request Tampering</span>
                </div>
            </a>
            
            <a href="docs-technical.php" class="doc-card technical">
                <div class="doc-icon">⚙️</div>
                <h3>3. Why The Exploit Works</h3>
                <p>Technical deep-dive into the vulnerability: missing authorization checks, trusting client input, and direct object access.</p>
                <div class="doc-topics">
                    <span class="topic-tag">Root Cause</span>
                    <span class="topic-tag">Trust Boundary</span>
                    <span class="topic-tag">Direct References</span>
                </div>
            </a>
            
            <a href="docs-vulnerable-code.php" class="doc-card technical">
                <div class="doc-icon">🔴</div>
                <h3>4. Vulnerable Code Analysis</h3>
                <p>Line-by-line breakdown of the flawed code, identifying the missing session validation and authorization logic.</p>
                <div class="doc-topics">
                    <span class="topic-tag">Code Review</span>
                    <span class="topic-tag">PHP Security</span>
                    <span class="topic-tag">SQL Queries</span>
                </div>
            </a>
            
            <a href="docs-mitigation.php" class="doc-card mitigation">
                <div class="doc-icon">🛡️</div>
                <h3>5. Secure Code & Mitigation</h3>
                <p>Learn the correct implementation with ownership validation, RBAC, and why each security measure prevents exploitation.</p>
                <div class="doc-topics">
                    <span class="topic-tag">Secure Code</span>
                    <span class="topic-tag">RBAC</span>
                    <span class="topic-tag">Best Practices</span>
                </div>
            </a>
            
            <a href="docs-comparison.php" class="doc-card comparison">
                <div class="doc-icon">⚖️</div>
                <h3>6. Code Comparison</h3>
                <p>Side-by-side comparison of vulnerable vs secure code, highlighting the key differences and security improvements.</p>
                <div class="doc-topics">
                    <span class="topic-tag">Before/After</span>
                    <span class="topic-tag">Diff Analysis</span>
                    <span class="topic-tag">Security Delta</span>
                </div>
            </a>
        </div>
        
        <div class="quick-links">
            <h2>🔗 Quick Links</h2>
            <div class="links-grid">
                <a href="login.php" class="quick-link">🚀 Start Lab</a>
                <a href="lab-description.php" class="quick-link">📖 Instructions</a>
                <a href="settings.php" class="quick-link">⚙️ Settings Page</a>
                <a href="low-stock.php" class="quick-link">📊 Low Stock</a>
                <a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" class="quick-link" target="_blank">🌐 OWASP A01</a>
                <a href="https://portswigger.net/web-security/access-control/idor" class="quick-link" target="_blank">🌐 PortSwigger</a>
            </div>
        </div>
    </div>
</body>
</html>
