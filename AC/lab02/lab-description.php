<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - TechCorp</title>
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
            <a href="index.php" class="logo">🏢 TechCorp</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="services.php">Services</a>
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
            <span class="lab-badge">CLIENT-SIDE DISCLOSURE</span>
            <h1 class="page-title">Lab 2: Unprotected Admin with Unpredictable URL</h1>

            <div class="section">
                <h2 class="section-title">📋 Lab Overview</h2>
                <p>
                    This lab demonstrates <strong>information disclosure through client-side code</strong> 
                    where the admin panel has an unpredictable URL that cannot be easily guessed.
                </p>
                <p>
                    However, the application includes JavaScript code that reveals the admin panel 
                    location. Even though the URL is complex and unpredictable, it's exposed in 
                    the page source code.
                </p>
            </div>

            <div class="section">
                <h2 class="section-title">🎯 Objective</h2>
                <p>
                    Find the hidden admin panel URL by <strong>analyzing JavaScript code</strong> in the 
                    page source, then use it to delete the user <strong>carlos</strong>.
                </p>
                <div class="info-box">
                    <h4>🔍 Discovery Method</h4>
                    <p>View Page Source (Ctrl+U) or DevTools (F12)</p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">🔍 Vulnerability Type</h2>
                <p>
                    <strong>Security Through Obscurity Failure</strong> - The developers relied on the 
                    unpredictable URL to protect the admin panel, but exposed it in client-side code.
                </p>
                <p>
                    This demonstrates why unpredictable URLs alone are not a valid security control. 
                    Any information in client-side code is visible to attackers.
                </p>
            </div>

            <div class="section">
                <h2 class="section-title">📝 Steps to Solve</h2>
                <ol class="step-list">
                    <li><strong>Open</strong> the main page and view the page source (Ctrl+U)</li>
                    <li><strong>Search</strong> for JavaScript code or look at &lt;script&gt; tags</li>
                    <li><strong>Find</strong> the admin panel URL in the JavaScript</li>
                    <li><strong>Navigate</strong> to the discovered admin panel URL</li>
                    <li><strong>Delete</strong> the user carlos to complete the lab</li>
                </ol>
            </div>

            <div class="section">
                <h2 class="section-title">💡 Hint</h2>
                <div class="warning-box">
                    <h4>JavaScript Disclosure</h4>
                    <p>Look for JavaScript code that checks user roles:</p>
                    <div class="code-block">
                        <code>var isAdmin = false;<br>if (isAdmin) {<br>&nbsp;&nbsp;adminPanel.href = '/admin-panel-x7k9p2m5q8w1.php';<br>}</code>
                    </div>
                    <p>The URL is visible even when the condition is false!</p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">⚠️ Real-World Impact</h2>
                <p>
                    This vulnerability pattern appears in real applications:
                </p>
                <ul style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                    <li>Sensitive URLs in JavaScript files</li>
                    <li>API endpoints exposed in client code</li>
                    <li>Hidden features revealed through source code</li>
                    <li>Debug information left in production</li>
                    <li>Security decisions made client-side</li>
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