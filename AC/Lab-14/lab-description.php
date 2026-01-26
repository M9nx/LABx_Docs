<?php
session_start();
$isLoggedIn = isset($_SESSION['manager_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDOR Banner Deletion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .nav-links a:hover { color: #ff4444; }
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
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .lab-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2.5rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .lab-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #ff6666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            margin: 1rem 0 1rem 1.5rem;
            color: #ccc;
        }
        .section li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .credentials-box {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .credentials-box h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .credential {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-family: monospace;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .credential:last-child { border-bottom: none; }
        .credential .role {
            color: #888;
        }
        .credential .creds {
            color: #88ff88;
        }
        .hint-box {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .hint-box h3 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            color: #88ff88;
            margin: 1rem 0;
        }
        .step-list {
            counter-reset: step;
        }
        .step-list li {
            counter-increment: step;
            padding-left: 0.5rem;
        }
        .step-list li::marker {
            content: counter(step) ". ";
            color: #ff4444;
            font-weight: bold;
        }
        .impact-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .impact-item {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
        }
        .impact-item h4 {
            color: #ff6666;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .impact-item p {
            font-size: 0.85rem;
            margin: 0;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📢 Revive Adserver</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-header">
            <span class="lab-badge">Lab 14 - Access Control</span>
            <h1>IDOR Banner Deletion</h1>
            <p>Exploit IDOR to delete another manager's banners</p>
        </div>

        <div class="section">
            <h2>🎯 Objective</h2>
            <p>
                This lab simulates a vulnerability found in Revive Adserver's banner deletion endpoint. 
                The <code>/banner-delete.php</code> endpoint validates access to the parent campaign but 
                <strong>fails to verify ownership of the specific banner being deleted</strong>.
            </p>
            <p>
                To solve the lab, log in as <code>manager_a</code> (attacker) and successfully delete 
                a banner belonging to <code>manager_b</code> (victim) by exploiting the IDOR vulnerability.
            </p>
        </div>

        <div class="section">
            <h2>🔑 Credentials</h2>
            <div class="credentials-box">
                <h3>Available Accounts:</h3>
                <div class="credential">
                    <span class="role">Manager A (Attacker) - Clients 1-2, Campaigns 1-3, Banners 1-5</span>
                    <span class="creds">manager_a : attacker123</span>
                </div>
                <div class="credential">
                    <span class="role">Manager B (Victim) - Clients 3-4, Campaigns 4-6, Banners 6-11</span>
                    <span class="creds">manager_b : victim456</span>
                </div>
                <div class="credential">
                    <span class="role">Manager C - Client 5, Campaign 7, Banners 12-13</span>
                    <span class="creds">manager_c : charlie789</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>💡 Solution Walkthrough</h2>
            <ol class="step-list">
                <li>Log in as Manager A (attacker) using <code>manager_a:attacker123</code></li>
                <li>Navigate to your campaign's banner page via Dashboard → View Banners</li>
                <li>Note your CSRF token displayed on the page (or from delete link URLs)</li>
                <li>Identify victim's banner IDs - Manager B's banners are IDs <strong>6-11</strong></li>
                <li>Craft malicious deletion URL combining YOUR clientid/campaignid with VICTIM's bannerid</li>
                <li>Execute the attack by visiting the crafted URL</li>
            </ol>
            
            <div class="hint-box">
                <h3>🔍 Key Insight</h3>
                <p>
                    The endpoint validates that you have access to the client (clientid) and that the 
                    campaign (campaignid) belongs to that client. However, it <strong>never validates 
                    that the banner (bannerid) belongs to the campaign</strong>. You can delete ANY banner 
                    by just knowing its ID!
                </p>
            </div>

            <h3 style="color: #ff6666; margin: 1.5rem 0 0.5rem;">Exploit URL:</h3>
            <div class="code-block">banner-delete.php?token=[YOUR_TOKEN]&clientid=1&campaignid=1&bannerid=6

# token = Your valid CSRF token (from session)
# clientid=1 = YOUR client ID (passes authorization check ✓)
# campaignid=1 = YOUR campaign ID (passes authorization check ✓)  
# bannerid=6 = VICTIM's banner ID (NO check performed! ✓)</div>
        </div>

        <div class="section">
            <h2>💥 Impact</h2>
            <p>
                This vulnerability allows horizontal privilege escalation between managers:
            </p>
            <div class="impact-list">
                <div class="impact-item">
                    <h4>Campaign Sabotage</h4>
                    <p>Delete competitors' banners to disrupt their ad campaigns</p>
                </div>
                <div class="impact-item">
                    <h4>Revenue Loss</h4>
                    <p>Victims lose active advertisements and potential revenue</p>
                </div>
                <div class="impact-item">
                    <h4>Data Integrity</h4>
                    <p>Unauthorized deletion bypasses audit controls</p>
                </div>
                <div class="impact-item">
                    <h4>Cross-Agency Attack</h4>
                    <p>Manager in Agency X can attack Manager in Agency Y</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📚 Learn More</h2>
            <p>
                This vulnerability is an example of Insecure Direct Object Reference (IDOR), which 
                occurs when an application exposes internal implementation objects to users without 
                proper authorization checks.
            </p>
            <ul>
                <li>Read the comprehensive documentation for in-depth analysis</li>
                <li>Understand why the code validates parent objects but not the target object</li>
                <li>Learn how to properly implement object-level authorization</li>
            </ul>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>
</body>
</html>
