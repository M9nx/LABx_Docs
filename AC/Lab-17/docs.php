<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(17);
$currentPage = 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR External Status Check Disclosure</title>
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
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
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
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
        }
        .sidebar {
            width: 280px;
            min-height: calc(100vh - 60px);
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(252, 109, 38, 0.2);
            padding: 1.5rem;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #fc6d26;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: block;
            padding: 0.6rem 1rem;
            color: #aaa;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        .sidebar-nav a:hover { background: rgba(252, 109, 38, 0.1); color: #fc6d26; }
        .sidebar-nav a.active {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            font-weight: 500;
        }
        .sidebar-nav .section-title {
            color: #666;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            margin-top: 0.5rem;
        }
        .content {
            flex: 1;
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .content h1 {
            color: #fc6d26;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .content h2 {
            color: #fc6d26;
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
        }
        .content h3 {
            color: #e0e0e0;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }
        .content p {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content ul, .content ol {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .content li { margin-bottom: 0.5rem; }
        .content code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.25rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
            margin: 1rem 0;
            line-height: 1.6;
        }
        .code-block .comment { color: #666; }
        .code-block .vulnerable { color: #ff6666; }
        .code-block .secure { color: #00c853; }
        .code-block .highlight { color: #fc6d26; }
        .info-box {
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
        }
        .info-box.warning {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
        }
        .info-box.danger {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .info-box.success {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
        }
        .info-box.info {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
        }
        .info-box h4 {
            margin-bottom: 0.5rem;
        }
        .info-box.warning h4 { color: #ffaa00; }
        .info-box.danger h4 { color: #ff6666; }
        .info-box.success h4 { color: #66ff99; }
        .info-box.info h4 { color: #fc6d26; }
        .info-box p { margin-bottom: 0; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 8px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .feature-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
            text-align: center;
        }
        .feature-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .feature-card h4 { color: #fc6d26; margin-bottom: 0.25rem; }
        .feature-card p { color: #888; font-size: 0.85rem; margin: 0; }
        @media (max-width: 900px) {
            .sidebar { display: none; }
            .content { padding: 1.5rem; }
        }
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
                <a href="lab-description.php">Lab Info</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php" class="active">Overview</a></li>
                <li class="section-title">Understanding</li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li class="section-title">Defense</li>
                <li><a href="docs-prevention.php">Prevention</a></li>
                <li><a href="docs-testing.php">Testing Techniques</a></li>
                <li class="section-title">Resources</li>
                <li><a href="docs-references.php">References</a></li>
            </ul>
            
            <h3 style="margin-top: 2rem;">🔗 Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="lab-description.php">📋 Lab Description</a></li>
                <li><a href="login.php">🚀 Start Lab</a></li>
                <li><a href="api-test.php">🧪 API Tester</a></li>
                <li><a href="setup_db.php">🔄 Reset Lab</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>Lab 17: IDOR External Status Check Disclosure</h1>
            <p style="color: #888; font-size: 1.1rem;">
                Learn how IDOR vulnerabilities in API endpoints can lead to sensitive data disclosure across project boundaries.
            </p>

            <?php if ($labSolved): ?>
            <div class="info-box success">
                <h4>✓ Lab Completed</h4>
                <p>You've successfully exploited this vulnerability!</p>
            </div>
            <?php endif; ?>

            <h2>What is This Lab About?</h2>
            <p>
                This lab simulates a real vulnerability found in GitLab's External Status Checks feature. 
                External status checks allow projects to integrate with external CI/CD systems that must 
                approve merge requests before they can be merged.
            </p>
            <p>
                The vulnerability exists in the API endpoint that handles status check responses. The 
                <code>external_status_check_id</code> parameter is not validated to ensure the status 
                check actually belongs to the project specified in the request.
            </p>

            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon">🔓</div>
                    <h4>IDOR</h4>
                    <p>Insecure Direct Object Reference</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📡</div>
                    <h4>API</h4>
                    <p>REST API Vulnerability</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🔐</div>
                    <h4>Info Leak</h4>
                    <p>Cross-Project Data Access</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🎯</div>
                    <h4>GitLab</h4>
                    <p>Real-World CVE</p>
                </div>
            </div>

            <h2>Lab Features</h2>
            <ul>
                <li><strong>GitLab-like Interface:</strong> Simulates project management with merge requests</li>
                <li><strong>External Status Checks:</strong> Configure validation endpoints for MRs</li>
                <li><strong>Personal Access Tokens:</strong> API authentication system</li>
                <li><strong>Interactive API Tester:</strong> Built-in tool to test the vulnerable endpoint</li>
                <li><strong>Private Projects:</strong> Data isolation between users</li>
            </ul>

            <h2>Attack Scenario</h2>
            <div class="info-box danger">
                <h4>🎭 The Setup</h4>
                <p>
                    <strong>Victim (victim01):</strong> Has a private project "Confidential Infrastructure" with 
                    external status checks containing sensitive API keys and internal URLs.<br><br>
                    <strong>Attacker (attacker01):</strong> Has access to their own public project with status checks 
                    configured. By exploiting IDOR, they can access victim01's private status check configurations.
                </p>
            </div>

            <h3>What Gets Leaked?</h3>
            <ul>
                <li>Private project names and identifiers</li>
                <li>External validation endpoint URLs</li>
                <li>API keys and secrets embedded in URLs</li>
                <li>Protected branch configurations</li>
                <li>Internal infrastructure information</li>
            </ul>

            <h2>Learning Objectives</h2>
            <ol>
                <li>Understand how IDOR vulnerabilities work in REST APIs</li>
                <li>Learn to identify missing authorization checks</li>
                <li>Practice exploiting cross-resource access vulnerabilities</li>
                <li>Understand the impact of information disclosure</li>
                <li>Learn secure coding practices for resource ownership validation</li>
            </ol>

            <div class="nav-buttons">
                <a href="index.php" class="nav-btn">← Back to Lab</a>
                <a href="docs-vulnerability.php" class="nav-btn">Next: The Vulnerability →</a>
            </div>
        </main>
    </div>
</body>
</html>
