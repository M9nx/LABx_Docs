<?php
// Lab 21: Documentation - Overview
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Overview - IDOR Documentation | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 900px; margin: 0 auto; }
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
        }
        .doc-header {
            margin-bottom: 3rem;
        }
        .doc-header h1 {
            font-size: 2.25rem;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .doc-header p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        .section {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .section h2 {
            color: #a5b4fc;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.4rem;
        }
        .section h3 {
            color: #e2e8f0;
            margin: 1.5rem 0 0.75rem;
            font-size: 1.1rem;
        }
        .section p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #94a3b8;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .section li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .highlight-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight-box p {
            margin: 0;
            color: #c7d2fe;
        }
        .warning-box {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .warning-box p {
            margin: 0;
            color: #fca5a5;
        }
        .owasp-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .owasp-box h4 {
            color: #fbbf24;
            margin-bottom: 0.75rem;
        }
        .owasp-box p {
            color: #fcd34d;
            margin: 0;
        }
        .nav-pagination {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
        }
        .nav-pagination a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-pagination a:hover {
            background: rgba(99, 102, 241, 0.2);
        }
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #f87171;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-top">
            <a href="docs.php">← Back to Docs</a>
            <a href="lab-description.php">📖 Lab Guide</a>
            <a href="login.php">🚀 Start Lab</a>
        </nav>
        
        <header class="doc-header">
            <h1>📋 1. Lab Overview</h1>
            <p>Understanding IDOR (Insecure Direct Object Reference) and Broken Access Control vulnerabilities - what they are, why they happen, and their critical impact on application security.</p>
        </header>
        
        <section class="section">
            <h2>🔓 What is IDOR?</h2>
            <p><strong>Insecure Direct Object Reference (IDOR)</strong> is a type of access control vulnerability that occurs when an application uses user-supplied input to directly access objects without proper authorization checks.</p>
            
            <div class="highlight-box">
                <p><strong>In simple terms:</strong> An application lets you access or modify data by changing an ID in a URL or form field, without verifying you have permission to do so.</p>
            </div>
            
            <h3>Real-World Analogy</h3>
            <p>Imagine a hotel where room keys are just numbered cards (101, 102, 103...). If you have card 101, you could potentially create card 102 and access someone else's room because the lock only checks the number, not whether you're actually the guest assigned to that room.</p>
        </section>
        
        <section class="section">
            <h2>⚠️ What is Broken Access Control?</h2>
            <p>Broken Access Control is a broader category of vulnerabilities (OWASP #1 in 2021) where restrictions on what authenticated users can do are not properly enforced. IDOR is a specific type of broken access control.</p>
            
            <h3>Types of Access Control Failures</h3>
            <ul>
                <li><strong>Horizontal Privilege Escalation:</strong> Accessing another user's resources at the same permission level (e.g., User A accesses User B's settings)</li>
                <li><strong>Vertical Privilege Escalation:</strong> Gaining higher privileges (e.g., regular user accesses admin functions)</li>
                <li><strong>Insecure Direct Object References:</strong> Manipulating identifiers to access unauthorized objects</li>
                <li><strong>Missing Function Level Access Control:</strong> Accessing restricted functions by guessing URLs</li>
            </ul>
            
            <div class="owasp-box">
                <h4>📊 OWASP Top 10 (2021)</h4>
                <p><strong>A01:2021 - Broken Access Control</strong> moved up from #5 to the #1 position in OWASP Top 10, with 94% of applications tested having some form of broken access control. This category has the most occurrences in applications.</p>
            </div>
        </section>
        
        <section class="section">
            <h2>🎯 This Lab's Vulnerability</h2>
            <p>In this lab, we simulate a vulnerability found in inventory management applications like Stocky. The vulnerability allows one user to modify another user's column visibility settings.</p>
            
            <h3>The Scenario</h3>
            <ul>
                <li><strong>Application:</strong> Stocky-like inventory management system</li>
                <li><strong>Feature:</strong> Low Stock Variants dashboard with customizable column visibility</li>
                <li><strong>Vulnerability:</strong> Settings ID is accepted from user input without ownership verification</li>
                <li><strong>Impact:</strong> Any authenticated user can modify any other user's column settings</li>
            </ul>
            
            <h3>Vulnerable Request</h3>
            <div class="warning-box">
                <p><code>POST /settings_for_low_stock_variants/111111</code><br><br>
                The application accepts settings ID <code>111111</code> from the request without checking if the current user owns those settings. An attacker can change this to any other user's settings ID.</p>
            </div>
        </section>
        
        <section class="section">
            <h2>💥 Why Does This Happen?</h2>
            <p>IDOR vulnerabilities typically occur due to:</p>
            <ol>
                <li><strong>Trust in Client Input:</strong> Assuming users won't modify hidden form fields or URL parameters</li>
                <li><strong>Missing Authorization Checks:</strong> Verifying authentication (who you are) but not authorization (what you can do)</li>
                <li><strong>Direct Database References:</strong> Using user-provided IDs directly in database queries</li>
                <li><strong>Predictable Identifiers:</strong> Using sequential or easily guessable IDs (111111, 111112, etc.)</li>
                <li><strong>Security Through Obscurity:</strong> Believing hidden fields or complex URLs are secure</li>
            </ol>
        </section>
        
        <section class="section">
            <h2>📈 Impact Assessment</h2>
            <h3>In This Lab</h3>
            <ul>
                <li>Modify another user's dashboard preferences</li>
                <li>Potentially hide critical low-stock alerts from victims</li>
                <li>Cause confusion and business disruption</li>
            </ul>
            
            <h3>In Real Applications</h3>
            <ul>
                <li><strong>Data Breach:</strong> Access to sensitive personal/financial information</li>
                <li><strong>Data Manipulation:</strong> Modify or delete other users' data</li>
                <li><strong>Account Takeover:</strong> Access another user's account settings</li>
                <li><strong>Financial Loss:</strong> Unauthorized transactions or transfers</li>
                <li><strong>Compliance Violations:</strong> GDPR, HIPAA, PCI-DSS breaches</li>
                <li><strong>Reputation Damage:</strong> Loss of customer trust</li>
            </ul>
        </section>
        
        <nav class="nav-pagination">
            <a href="docs.php">← Documentation Home</a>
            <a href="docs-walkthrough.php">Next: Walkthrough →</a>
        </nav>
    </div>
</body>
</html>
