<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - SecureShop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #ff4444;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .page-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 1.5rem;
        }
        .section {
            margin-bottom: 2.5rem;
        }
        .section:last-child {
            margin-bottom: 0;
        }
        .section-title {
            color: #ff6666;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section p:last-child {
            margin-bottom: 0;
        }
        .info-box {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .info-box h4 {
            color: #00ff00;
            margin-bottom: 0.8rem;
        }
        .info-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            color: #00ff00;
            font-family: 'Consolas', monospace;
        }
        .warning-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #ffa500;
            margin-bottom: 0.8rem;
        }
        .step-list {
            list-style: none;
            counter-reset: step;
        }
        .step-list li {
            counter-increment: step;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.1);
            border-radius: 8px;
            margin-bottom: 0.8rem;
            position: relative;
            padding-left: 3.5rem;
        }
        .step-list li::before {
            content: counter(step);
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .step-list li strong {
            color: #ff6666;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block code {
            font-family: 'Consolas', 'Monaco', monospace;
            color: #ff6666;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
        .btn-info {
            background: linear-gradient(135deg, #00aaff, #0077cc);
            color: white;
        }
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 170, 255, 0.4);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🛒 SecureShop</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="content-card">
            <span class="lab-badge">INFORMATION DISCLOSURE</span>
            <h1 class="page-title">Lab 1: Unprotected Admin Functionality</h1>

            <div class="section">
                <h2 class="section-title">📋 Lab Overview</h2>
                <p>
                    This lab demonstrates an <strong>unprotected admin functionality</strong> vulnerability 
                    where administrative features are accessible without any authentication or authorization.
                </p>
                <p>
                    The admin panel exists but lacks any authentication mechanisms. Additionally, the 
                    application inadvertently discloses the admin panel location through a common 
                    website file used for SEO purposes.
                </p>
            </div>

            <div class="section">
                <h2 class="section-title">🎯 Objective</h2>
                <p>
                    Discover the admin panel through <strong>information disclosure</strong> and use it 
                    to delete the user <strong>carlos</strong> from the system.
                </p>
                <div class="info-box">
                    <h4>🔍 Discovery Target</h4>
                    <p>Check the file: <code>robots.txt</code></p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">🔍 Vulnerability Type</h2>
                <p>
                    <strong>Broken Access Control + Information Disclosure</strong> - The admin panel has 
                    no authentication requirement, and its location is leaked through the robots.txt file 
                    which is commonly used to instruct search engine crawlers.
                </p>
                <p>
                    Many developers mistakenly believe that hiding URLs is a security control. The 
                    robots.txt file often reveals paths that should be protected but aren't.
                </p>
            </div>

            <div class="section">
                <h2 class="section-title">📝 Steps to Solve</h2>
                <ol class="step-list">
                    <li><strong>Navigate</strong> to the robots.txt file at <code>/robots.txt</code></li>
                    <li><strong>Identify</strong> any disallowed paths that might be admin panels</li>
                    <li><strong>Access</strong> the admin panel URL directly in your browser</li>
                    <li><strong>Find</strong> the user management functionality</li>
                    <li><strong>Delete</strong> the user carlos to complete the lab</li>
                </ol>
            </div>

            <div class="section">
                <h2 class="section-title">💡 Hint</h2>
                <div class="warning-box">
                    <h4>robots.txt Structure</h4>
                    <p>The robots.txt file typically looks like:</p>
                    <div class="code-block">
                        <code>User-agent: *<br>Disallow: /administrator-panel</code>
                    </div>
                    <p>Paths in "Disallow" are meant for crawlers, but humans can still access them!</p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">⚠️ Real-World Impact</h2>
                <p>
                    This vulnerability pattern is common in real applications:
                </p>
                <ul style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                    <li>Admin panels without authentication</li>
                    <li>Sensitive paths disclosed in robots.txt</li>
                    <li>Security through obscurity failures</li>
                    <li>Directory enumeration vulnerabilities</li>
                    <li>Full system compromise through admin access</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="setup_db.php" target="_blank" class="btn btn-primary">
                    🗄️ Setup Database
                </a>
                <a href="index.php" class="btn btn-info">
                    🚀 Access Lab
                </a>
                <a href="docs.php" class="btn btn-secondary">
                    📚 View Documentation
                </a>
            </div>
        </div>
    </div>
</body>
</html>