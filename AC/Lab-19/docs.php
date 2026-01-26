<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(19);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 19 IDOR Delete</title>
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
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #a5b4fc; text-decoration: none; transition: color 0.3s; }
        .nav-links a:hover { color: #c7d2fe; }
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 80px);
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.02);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem 1rem;
            position: sticky;
            top: 80px;
            height: calc(100vh - 80px);
            overflow-y: auto;
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
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(99, 102, 241, 0.1);
            color: #a5b4fc;
        }
        .sidebar-nav a.active {
            border-left: 3px solid #6366f1;
        }
        .nav-icon { font-size: 1.1rem; }
        .main-content {
            flex: 1;
            padding: 2rem 3rem;
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
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .doc-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        .doc-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(99, 102, 241, 0.3);
            transform: translateY(-5px);
        }
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .card-icon.purple { background: rgba(99, 102, 241, 0.2); }
        .card-icon.red { background: rgba(239, 68, 68, 0.2); }
        .card-icon.green { background: rgba(16, 185, 129, 0.2); }
        .card-icon.blue { background: rgba(59, 130, 246, 0.2); }
        .card-icon.yellow { background: rgba(245, 158, 11, 0.2); }
        .doc-card h3 { color: #e2e8f0; margin-bottom: 0.5rem; }
        .doc-card p { color: #64748b; font-size: 0.9rem; line-height: 1.6; }
        .overview-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem 0;
        }
        .overview-section h2 { color: #a5b4fc; margin-bottom: 1rem; }
        .overview-section p { color: #94a3b8; line-height: 1.8; margin-bottom: 1rem; }
        .highlight-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
            padding: 1rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
        }
        .highlight-box p { color: #a5b4fc; margin: 0; }
        .status-banner {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .status-banner.solved {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-banner.unsolved {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status-icon { font-size: 1.5rem; }
        .status-text { flex: 1; }
        .status-text h4 { color: #e2e8f0; margin-bottom: 0.25rem; }
        .status-text p { color: #94a3b8; font-size: 0.875rem; margin: 0; }
        .quick-links {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
        .quick-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: #a5b4fc;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .quick-link:hover {
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
                <a href="lab-description.php">Instructions</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3 class="sidebar-title">Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php" class="active"><span class="nav-icon">📚</span> Overview</a></li>
                <li><a href="docs-vulnerability.php"><span class="nav-icon">🔓</span> Vulnerability</a></li>
                <li><a href="docs-exploitation.php"><span class="nav-icon">⚡</span> Exploitation</a></li>
                <li><a href="docs-prevention.php"><span class="nav-icon">🛡️</span> Prevention</a></li>
                <li><a href="docs-comparison.php"><span class="nav-icon">⚖️</span> Code Comparison</a></li>
                <li><a href="docs-references.php"><span class="nav-icon">📖</span> References</a></li>
            </ul>
            
            <h3 class="sidebar-title" style="margin-top: 2rem;">Lab Resources</h3>
            <ul class="sidebar-nav">
                <li><a href="lab-description.php"><span class="nav-icon">📋</span> Instructions</a></li>
                <li><a href="login.php"><span class="nav-icon">🚀</span> Start Lab</a></li>
                <li><a href="success.php"><span class="nav-icon">🏆</span> Success Page</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="../index.php">Labs</a> / <a href="index.php">Lab 19</a> / Documentation
            </div>

            <?php if ($labSolved): ?>
            <div class="status-banner solved">
                <span class="status-icon">✅</span>
                <div class="status-text">
                    <h4>Lab Completed</h4>
                    <p>You've successfully exploited the IDOR vulnerability in this lab!</p>
                </div>
            </div>
            <?php else: ?>
            <div class="status-banner unsolved">
                <span class="status-icon">⚠️</span>
                <div class="status-text">
                    <h4>Lab Not Completed</h4>
                    <p>Complete the lab to test your understanding of IDOR vulnerabilities.</p>
                </div>
            </div>
            <?php endif; ?>

            <h1 class="page-title">Lab 19 Documentation</h1>
            <p class="page-subtitle">IDOR Vulnerability: Deleting Other Users' Saved Projects</p>

            <div class="quick-links">
                <a href="lab-description.php" class="quick-link">📋 Step-by-Step Guide</a>
                <a href="login.php" class="quick-link">🚀 Start Exploit</a>
                <a href="../index.php" class="quick-link">🏠 All Labs</a>
            </div>

            <div class="overview-section">
                <h2>🎯 What You'll Learn</h2>
                <p>
                    This lab demonstrates an <strong>Insecure Direct Object Reference (IDOR)</strong> 
                    vulnerability in a delete operation. The application allows users to save/bookmark 
                    projects to their portfolio, but the delete endpoint doesn't verify that the user 
                    owns the saved project before deleting it.
                </p>
                <div class="highlight-box">
                    <p>
                        💡 <strong>Key Insight:</strong> IDOR vulnerabilities occur when an application 
                        uses user-controllable input to directly access objects without proper authorization 
                        checks. In delete operations, this can lead to unauthorized data destruction.
                    </p>
                </div>
                <p>
                    The attacker can enumerate saved project IDs and delete items that belong to other 
                    users, demonstrating a critical access control flaw that could lead to data loss.
                </p>
            </div>

            <h2 style="color: #a5b4fc; margin: 2rem 0 1rem;">📖 Documentation Sections</h2>

            <div class="card-grid">
                <a href="docs-vulnerability.php" class="doc-card">
                    <div class="card-icon purple">🔓</div>
                    <h3>Understanding the Vulnerability</h3>
                    <p>Learn what makes IDOR vulnerabilities so dangerous and why missing ownership validation leads to data breaches.</p>
                </a>

                <a href="docs-exploitation.php" class="doc-card">
                    <div class="card-icon red">⚡</div>
                    <h3>Exploitation Techniques</h3>
                    <p>Step-by-step attack methods using URL manipulation, Burp Suite, cURL, and browser developer tools.</p>
                </a>

                <a href="docs-prevention.php" class="doc-card">
                    <div class="card-icon green">🛡️</div>
                    <h3>Prevention Strategies</h3>
                    <p>Best practices for securing delete operations including ownership validation, session checks, and audit logging.</p>
                </a>

                <a href="docs-comparison.php" class="doc-card">
                    <div class="card-icon blue">⚖️</div>
                    <h3>Code Comparison</h3>
                    <p>Side-by-side comparison of vulnerable vs secure code with detailed explanations of each fix.</p>
                </a>

                <a href="docs-references.php" class="doc-card">
                    <div class="card-icon yellow">📖</div>
                    <h3>External References</h3>
                    <p>Links to OWASP guidelines, HackerOne reports, and additional learning resources about IDOR vulnerabilities.</p>
                </a>
            </div>

            <div class="overview-section" style="margin-top: 2rem;">
                <h2>🔬 Lab Scenario</h2>
                <p>
                    <strong>ProjectHub</strong> is a creative portfolio platform where designers can browse, 
                    save, and showcase projects. Users can bookmark projects they like to their "Saved 
                    Projects" collection.
                </p>
                <p>
                    The vulnerability exists in the <code>/api/delete_saved.php</code> endpoint which 
                    accepts a <code>saved_id</code> parameter but fails to verify ownership before 
                    deleting the record.
                </p>
                <div class="highlight-box">
                    <p>
                        🎯 <strong>Your Goal:</strong> Log in as <code>attacker_user</code> and delete 
                        one of <code>victim_designer</code>'s saved projects (IDs: 101-105).
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
