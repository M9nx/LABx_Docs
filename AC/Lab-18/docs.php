<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(18);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 18</title>
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
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; transition: color 0.3s; }
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
            overflow-y: auto;
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
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1rem;
            color: #888;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
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
        .breadcrumb {
            color: #888;
            margin-bottom: 2rem;
        }
        .breadcrumb a { color: #96bf48; text-decoration: none; }
        .doc-header {
            margin-bottom: 2rem;
        }
        .doc-header h1 {
            font-size: 2.5rem;
            color: #e0e0e0;
            margin-bottom: 0.5rem;
        }
        .doc-header p { color: #888; font-size: 1.1rem; }
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
        .topic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }
        .topic-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .topic-card:hover {
            transform: translateY(-5px);
            border-color: rgba(150, 191, 72, 0.5);
            background: rgba(150, 191, 72, 0.1);
        }
        .topic-card h3 {
            color: #96bf48;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .topic-card p { color: #888; font-size: 0.9rem; margin: 0; }
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #96bf48, #5c6ac4); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn:hover { transform: translateY(-3px); }
        .solved-notice {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            color: #66ff99;
        }
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
                <li><a href="docs.php" class="active">📖 Overview</a></li>
                <li><a href="docs-vulnerability.php">🔓 The Vulnerability</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-comparison.php">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php">🔗 References</a></li>
            </ul>
            <h3 style="margin-top: 2rem;">Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="lab-description.php">📋 Lab Description</a></li>
                <li><a href="login.php">🚀 Start Lab</a></li>
                <li><a href="success.php">🏆 Check Progress</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="../index.php">Labs</a> / <a href="index.php">Lab 18</a> / Documentation
            </div>

            <?php if ($labSolved): ?>
            <div class="solved-notice">
                ✅ You've completed this lab! Review the documentation to reinforce your understanding.
            </div>
            <?php endif; ?>

            <div class="doc-header">
                <h1>Lab 18 Documentation</h1>
                <p>IDOR Vulnerability in Session Expiration Feature</p>
            </div>

            <div class="doc-section">
                <h2>📋 Overview</h2>
                <p>
                    This lab demonstrates an <strong>Insecure Direct Object Reference (IDOR)</strong> 
                    vulnerability in a session management feature. The vulnerability allows an attacker 
                    to expire any user's sessions by manipulating the <code>account_id</code> parameter.
                </p>
                <p>
                    This is based on a real-world vulnerability discovered in Shopify's admin panel, 
                    where the session expiration endpoint did not properly validate that the requested 
                    account belonged to the authenticated user.
                </p>
            </div>

            <div class="doc-section">
                <h2>📚 Documentation Topics</h2>
                <div class="topic-grid">
                    <a href="docs-vulnerability.php" class="topic-card">
                        <h3>🔓 The Vulnerability</h3>
                        <p>Understanding IDOR and how it manifests in session management</p>
                    </a>
                    <a href="docs-exploitation.php" class="topic-card">
                        <h3>⚔️ Exploitation</h3>
                        <p>Step-by-step guide to exploiting the vulnerability</p>
                    </a>
                    <a href="docs-prevention.php" class="topic-card">
                        <h3>🛡️ Prevention</h3>
                        <p>Best practices for preventing IDOR vulnerabilities</p>
                    </a>
                    <a href="docs-comparison.php" class="topic-card">
                        <h3>⚖️ Code Comparison</h3>
                        <p>Side-by-side vulnerable vs. secure code examples</p>
                    </a>
                    <a href="docs-references.php" class="topic-card">
                        <h3>🔗 References</h3>
                        <p>External resources, HackerOne reports, and further reading</p>
                    </a>
                </div>
            </div>

            <div class="doc-section">
                <h2>🎯 Learning Objectives</h2>
                <ul>
                    <li>Understand how IDOR vulnerabilities occur in real-world applications</li>
                    <li>Learn to identify missing authorization checks in session management</li>
                    <li>Practice manipulating hidden form fields to exploit IDOR</li>
                    <li>Implement proper server-side validation to prevent such attacks</li>
                </ul>
            </div>

            <div class="quick-links">
                <a href="docs-vulnerability.php" class="btn btn-primary">Start Reading →</a>
                <a href="login.php" class="btn btn-secondary">🚀 Try the Lab</a>
            </div>
        </main>
    </div>
</body>
</html>
