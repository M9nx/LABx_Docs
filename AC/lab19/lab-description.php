<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(19);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDOR Delete Saved Projects</title>
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
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #a5b4fc; text-decoration: none; }
        .nav-links a:hover { color: #c7d2fe; }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        .breadcrumb a { color: #818cf8; text-decoration: none; }
        .lab-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .lab-badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2rem;
            color: #c7d2fe;
            margin-bottom: 0.5rem;
        }
        .lab-header p { color: #64748b; }
        .solved-badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-top: 1rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #a5b4fc;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card p, .card li {
            color: #94a3b8;
            line-height: 1.8;
        }
        .objective-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
        }
        .objective-box h3 { color: #a5b4fc; margin-bottom: 0.5rem; }
        .credentials-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .credentials-table th, .credentials-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .credentials-table th { color: #a5b4fc; }
        .credentials-table code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #c7d2fe;
        }
        .role-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .role-badge.victim { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .role-badge.attacker { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .role-badge.admin { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .steps-list {
            counter-reset: step;
            list-style: none;
            padding: 0;
        }
        .steps-list li {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.5rem;
        }
        .steps-list li::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #a5b4fc;
            overflow-x: auto;
            margin: 0.75rem 0;
        }
        .vulnerable { color: #ef4444; }
        .hint-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .hint-box h4 { color: #fcd34d; margin-bottom: 0.5rem; }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); border: 1px solid #444; color: #94a3b8; }
        .btn:hover { transform: translateY(-3px); }
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
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="../index.php">Labs</a> / <a href="index.php">Lab 19</a> / Description
        </div>

        <div class="lab-header">
            <span class="lab-badge">Lab 19 • Practitioner • IDOR</span>
            <h1>IDOR: Delete Users Saved Projects</h1>
            <p>Exploit broken access control to delete other users' bookmarked projects</p>
            <?php if ($labSolved): ?>
            <span class="solved-badge">✓ Solved</span>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>🎯 Lab Objective</h2>
            <div class="objective-box">
                <h3>Your Mission</h3>
                <p>
                    Exploit the IDOR vulnerability in the delete API to remove 
                    <strong>victim_designer's</strong> saved projects. The endpoint trusts the 
                    <code>saved_id</code> parameter without verifying ownership.
                </p>
            </div>
            <p>
                This lab simulates a real bug bounty finding where a project portfolio platform 
                allowed users to delete any saved bookmark by simply knowing its ID.
            </p>
        </div>

        <div class="card">
            <h2>🔑 Test Credentials</h2>
            <table class="credentials-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Saved IDs</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>victim_designer</code></td>
                        <td><code>victim123</code></td>
                        <td><span class="role-badge victim">🎯 Target</span></td>
                        <td><strong>101-105</strong></td>
                    </tr>
                    <tr>
                        <td><code>attacker_user</code></td>
                        <td><code>attacker123</code></td>
                        <td><span class="role-badge attacker">⚔️ Use This</span></td>
                        <td>201-203</td>
                    </tr>
                    <tr>
                        <td><code>admin</code></td>
                        <td><code>admin123</code></td>
                        <td><span class="role-badge admin">Admin</span></td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>📝 Step-by-Step Guide</h2>
            <ol class="steps-list">
                <li>
                    <strong>Login as the attacker</strong>
                    <p>Use <code>attacker_user</code> / <code>attacker123</code></p>
                </li>
                <li>
                    <strong>Navigate to Saved Projects</strong>
                    <p>Go to Dashboard → Saved Projects page</p>
                </li>
                <li>
                    <strong>Observe the Delete URL</strong>
                    <p>Hover over a delete button to see the URL pattern</p>
                </li>
                <li>
                    <strong>Note the vulnerable parameter</strong>
                    <div class="code-block">
/api/delete_saved.php?saved_id=<span class="vulnerable">{ID}</span>
                    </div>
                </li>
                <li>
                    <strong>Modify the saved_id</strong>
                    <p>Change the URL to use victim's ID: <code class="vulnerable">101</code>, <code class="vulnerable">102</code>, etc.</p>
                </li>
                <li>
                    <strong>Execute the attack</strong>
                    <p>Visit the modified URL or use browser console/Burp Suite</p>
                </li>
                <li>
                    <strong>Verify the attack</strong>
                    <p>Check if the victim's saved project was deleted</p>
                </li>
            </ol>
        </div>

        <div class="card">
            <h2>📡 Vulnerable Request</h2>
            <div class="code-block">
GET /AC/lab19/api/delete_saved.php?saved_id=<span class="vulnerable">101</span> HTTP/1.1
Host: localhost
Cookie: PHPSESSID=your_session_cookie
            </div>
            
            <div class="hint-box">
                <h4>💡 Alternative Methods</h4>
                <ul style="margin-top:0.5rem; color: #94a3b8;">
                    <li><strong>URL Bar:</strong> Directly enter the modified URL</li>
                    <li><strong>Browser Console:</strong> Use fetch() to send the request</li>
                    <li><strong>Burp Suite:</strong> Intercept and modify the request</li>
                    <li><strong>cURL:</strong> Command-line with session cookie</li>
                </ul>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="success.php" class="btn btn-secondary">🏆 Check Solution</a>
            <a href="index.php" class="btn btn-secondary">← Back</a>
        </div>
    </div>
</body>
</html>
