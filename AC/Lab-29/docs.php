<?php
// Lab 29: LinkedPro Newsletter Platform - Documentation Hub
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 29: Newsletter Subscriber IDOR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f1419 50%, #0a0a0f 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .nav-bar {
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .nav-logo span {
            color: #057642;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #0a66c2;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(10, 102, 194, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(10, 102, 194, 0.2);
            border-color: #0a66c2;
            color: #0a66c2;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: #0a66c2;
        }
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .page-header h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, #0a66c2, #057642);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
        }
        .page-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .doc-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        .doc-card:hover {
            transform: translateY(-4px);
            border-color: rgba(10, 102, 194, 0.4);
            box-shadow: 0 8px 25px rgba(10, 102, 194, 0.15);
        }
        .doc-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .doc-card h3 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .doc-card p {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .doc-card .tag {
            display: inline-block;
            background: rgba(10, 102, 194, 0.15);
            color: #7fc4fd;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-top: 1rem;
        }
        
        /* Documentation Content Sections */
        .section {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #0a66c2;
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section h3 {
            color: #057642;
            font-size: 1rem;
            margin: 1.5rem 0 0.75rem 0;
        }
        .section p {
            color: #aaa;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #aaa;
            margin-left: 1.5rem;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section li {
            margin-bottom: 0.5rem;
        }
        .section code {
            background: rgba(10, 102, 194, 0.15);
            color: #7fc4fd;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .code-block {
            background: #1a1a2e;
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            margin: 0;
            color: #7fc4fd;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .code-block .comment {
            color: #6a737d;
        }
        .code-block .keyword {
            color: #ff79c6;
        }
        .code-block .string {
            color: #f1fa8c;
        }
        .code-block .variable {
            color: #50fa7b;
        }
        .warning-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .warning-box strong {
            color: #ffa500;
        }
        .success-box {
            background: rgba(5, 118, 66, 0.1);
            border: 1px solid rgba(5, 118, 66, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .success-box strong {
            color: #057642;
        }
        .nav-section {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0a66c2, #004182);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(10, 102, 194, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #0a66c2;
            border: 1px solid rgba(10, 102, 194, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(10, 102, 194, 0.1);
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">Linked<span>Pro</span></a>
        <div class="nav-links">
            <a href="../index.php" class="btn-back">← All Labs</a>
            <a href="index.php">Home</a>
            <a href="lab-description.php">Lab Info</a>
            <a href="docs.php" class="active">Documentation</a>
            <a href="login.php">Login</a>
        </div>
    </nav>
    
    <div class="container">
        
        <div class="page-header">
            <h1>📚 Lab 29 Documentation</h1>
            <p>Comprehensive guide to understanding and exploiting the Newsletter Subscriber IDOR vulnerability</p>
        </div>
        
        <div class="docs-grid">
            <a href="docs-technical.php" class="doc-card">
                <div class="icon">🔍</div>
                <h3>Technical Deep Dive</h3>
                <p>Detailed analysis of the vulnerability, including step-by-step exploitation walkthrough and why the exploit works.</p>
                <span class="tag">Exploitation Guide</span>
            </a>
            
            <a href="docs-mitigation.php" class="doc-card">
                <div class="icon">🛡️</div>
                <h3>Mitigation Guide</h3>
                <p>How to fix this vulnerability with secure code examples, best practices, and comparison of vulnerable vs. secure implementations.</p>
                <span class="tag">Secure Coding</span>
            </a>
        </div>
        
        <!-- Overview Section -->
        <div class="section">
            <h2>📖 Overview: What is IDOR?</h2>
            <p><strong>Insecure Direct Object Reference (IDOR)</strong> is a type of access control vulnerability that occurs when an application uses user-supplied input to access objects directly without proper authorization checks.</p>
            
            <h3>How IDOR Works</h3>
            <p>In this vulnerability pattern:</p>
            <ol>
                <li>An application exposes a reference to an internal object (like a database ID or URN)</li>
                <li>Users can modify this reference to access objects they shouldn't have access to</li>
                <li>The server fails to verify that the requesting user is authorized to access the referenced object</li>
            </ol>
            
            <h3>IDOR in This Lab</h3>
            <p>The LinkedPro newsletter platform has an IDOR vulnerability in its subscriber list API:</p>
            <div class="code-block">
                <pre>GET /api/get_subscribers.php?seriesUrn=urn:li:fsd_contentSeries:7890123456&count=10&start=0</pre>
            </div>
            <p>The <code>seriesUrn</code> parameter identifies which newsletter's subscribers to retrieve. The API returns subscriber data for ANY newsletter URN provided, without checking if the requesting user owns that newsletter.</p>
        </div>
        
        <!-- Impact Section -->
        <div class="section">
            <h2>⚠️ Security Impact</h2>
            
            <h3>Data Exposed</h3>
            <p>When exploited, this vulnerability exposes:</p>
            <ul>
                <li><strong>Email Addresses:</strong> Professional emails of all subscribers</li>
                <li><strong>Professional Information:</strong> Job titles, companies, and headlines</li>
                <li><strong>Location Data:</strong> Geographic locations of subscribers</li>
                <li><strong>Network Information:</strong> Connection counts indicating professional influence</li>
                <li><strong>Behavioral Data:</strong> Subscription dates and notification preferences</li>
            </ul>
            
            <h3>Real-World Consequences</h3>
            <div class="warning-box">
                <strong>⚠️ This type of vulnerability can lead to:</strong>
                <ul style="margin-top: 0.5rem; margin-bottom: 0;">
                    <li>Privacy violations and regulatory fines (GDPR, CCPA)</li>
                    <li>Targeted phishing and social engineering attacks</li>
                    <li>Competitive intelligence gathering</li>
                    <li>Reputation damage to the platform</li>
                    <li>Loss of user trust</li>
                </ul>
            </div>
        </div>
        
        <!-- Quick Attack Summary -->
        <div class="section">
            <h2>🎯 Quick Exploitation Summary</h2>
            <ol>
                <li>Login with any account (attacker / attacker123)</li>
                <li>Browse newsletters and note public URNs</li>
                <li>Access the subscribers page directly: <code>/subscribers.php?id=1</code> (or 2, 3...)</li>
                <li>Or call the API directly with any URN: <code>/api/get_subscribers.php?seriesUrn=urn:li:fsd_contentSeries:7890123456</code></li>
                <li>Receive full subscriber list with PII!</li>
            </ol>
            
            <div class="success-box">
                <strong>✓ Flag Location:</strong> The flag is displayed when you successfully access subscribers of a newsletter you don't own.
            </div>
        </div>
        
        <div class="nav-section">
            <a href="docs-technical.php" class="btn btn-primary">🔍 Technical Deep Dive →</a>
            <a href="docs-mitigation.php" class="btn btn-secondary">🛡️ Mitigation Guide →</a>
            <a href="login.php" class="btn btn-secondary">🚀 Start Lab</a>
        </div>
    </div>
</body>
</html>
