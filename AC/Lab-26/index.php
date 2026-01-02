<?php
/**
 * Lab 26: Lab Home/Index Page
 */

require_once 'config.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 26: API Application IDOR - Pressable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #00b4d8;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .nav-links a {
            color: #aaa;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            color: #00b4d8;
            background: rgba(0, 180, 216, 0.1);
        }
        .hero {
            max-width: 1000px;
            margin: 0 auto;
            padding: 4rem 2rem;
            text-align: center;
        }
        .lab-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 20px;
            color: #ff6b6b;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: 2.75rem;
            color: #fff;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .hero h1 span {
            color: #00b4d8;
        }
        .hero p {
            font-size: 1.2rem;
            color: #888;
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 180, 216, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }
        .features {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .feature-card h3 {
            color: #00b4d8;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .feature-card p {
            color: #888;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .vulnerability-box {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .vuln-card {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 2rem;
        }
        .vuln-card h2 {
            color: #ff6b6b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vuln-card p {
            color: #ccc;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .vuln-card ul {
            color: #aaa;
            padding-left: 1.5rem;
            line-height: 1.8;
        }
        .vuln-card code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">⚡</span>
                Pressable
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="lab-badge">🔬 Lab 26 - Access Control Vulnerability</div>
        <h1>API Application <span>IDOR</span><br>Credential Leak</h1>
        <p>
            Exploit an Insecure Direct Object Reference vulnerability in the API application 
            management system to leak other users' Client ID and Client Secret credentials.
        </p>
        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Details</a>
        </div>
    </section>

    <div class="features">
        <div class="feature-card">
            <h3>🎯 Real-World Scenario</h3>
            <p>Based on a critical HackerOne report on Pressable's API application management system that allowed access to any user's API credentials.</p>
        </div>
        <div class="feature-card">
            <h3>🔑 API Credential Leak</h3>
            <p>The vulnerability exposes Client ID and Client Secret values, enabling full account takeover via the API.</p>
        </div>
        <div class="feature-card">
            <h3>📊 Sequential IDs</h3>
            <p>Application IDs are sequential, making enumeration trivial. No guessing required!</p>
        </div>
    </div>

    <div class="vulnerability-box">
        <div class="vuln-card">
            <h2>⚠️ Vulnerability Overview</h2>
            <p>
                The update endpoint accepts an <code>application[id]</code> parameter that references 
                any API application without verifying ownership. When the update fails validation 
                (e.g., missing required name field), the error response includes the full application 
                details including sensitive credentials.
            </p>
            <ul>
                <li>Change <code>application[id]</code> to another user's application ID</li>
                <li>Remove required fields to trigger a validation error</li>
                <li>The error response leaks <strong>Client ID</strong> and <strong>Client Secret</strong></li>
                <li>Use leaked credentials to access the victim's resources via API</li>
            </ul>
        </div>
    </div>
</body>
</html>
