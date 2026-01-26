<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(17);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDOR External Status Check Disclosure</title>
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
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .breadcrumb a { color: #fc6d26; text-decoration: none; }
        .lab-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .lab-badge {
            display: inline-block;
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2rem;
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .lab-header p { color: #888; }
        .solved-badge {
            display: inline-block;
            background: rgba(0, 200, 100, 0.2);
            color: #66ff99;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-top: 1rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #fc6d26;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card p, .card li {
            color: #aaa;
            line-height: 1.8;
        }
        .objective-box {
            background: rgba(252, 109, 38, 0.1);
            border-left: 4px solid #fc6d26;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
        }
        .objective-box h3 { color: #fc6d26; margin-bottom: 0.5rem; }
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
        .credentials-table th { color: #fc6d26; }
        .credentials-table code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .role-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .role-badge.victim { background: rgba(255, 68, 68, 0.2); color: #ff8888; }
        .role-badge.attacker { background: rgba(255, 170, 0, 0.2); color: #ffcc00; }
        .role-badge.admin { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
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
            background: linear-gradient(135deg, #fc6d26, #e24329);
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
            color: #88ff88;
            overflow-x: auto;
            margin: 0.75rem 0;
        }
        .vulnerable { color: #ff6666; }
        .hint-box {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .hint-box h4 { color: #ffaa00; margin-bottom: 0.5rem; }
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
        .btn-primary { background: linear-gradient(135deg, #fc6d26, #e24329); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn:hover { transform: translateY(-3px); }
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
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="../index.php">Labs</a> / <a href="index.php">Lab 17</a> / Description
        </div>

        <div class="lab-header">
            <span class="lab-badge">Lab 17 • Practitioner • IDOR</span>
            <h1>IDOR External Status Check Information Disclosure</h1>
            <p>Exploit an API endpoint vulnerability to leak sensitive project configurations</p>
            <?php if ($labSolved): ?>
            <span class="solved-badge">✓ Solved</span>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>🎯 Lab Objective</h2>
            <div class="objective-box">
                <h3>Your Mission</h3>
                <p>
                    Exploit the IDOR vulnerability in the status check responses API to access 
                    sensitive configuration data from <strong>victim01's private project</strong>.
                    Successfully leak the external status check URL and API keys.
                </p>
            </div>
            <p>
                This lab simulates a real GitLab vulnerability where the API endpoint for external 
                status check responses did not validate that the requested status check belongs to 
                the project specified in the request.
            </p>
        </div>

        <div class="card">
            <h2>🔐 Test Credentials</h2>
            <table class="credentials-table">
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td><code>victim01</code></td>
                    <td><code>victim123</code></td>
                    <td><span class="role-badge victim">Victim</span></td>
                    <td>Owns private project with sensitive status checks</td>
                </tr>
                <tr>
                    <td><code>attacker01</code></td>
                    <td><code>attacker123</code></td>
                    <td><span class="role-badge attacker">Attacker ⭐</span></td>
                    <td>Use this account to exploit the vulnerability</td>
                </tr>
                <tr>
                    <td><code>admin</code></td>
                    <td><code>admin123</code></td>
                    <td><span class="role-badge admin">Admin</span></td>
                    <td>System administrator</td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>📝 Step-by-Step Guide</h2>
            <ol class="steps-list">
                <li>
                    <strong>Login as attacker01</strong>
                    <p>Use the credentials <code>attacker01 / attacker123</code> to login.</p>
                </li>
                <li>
                    <strong>Create a Personal Access Token</strong>
                    <p>Go to <a href="tokens.php" style="color: #fc6d26;">Access Tokens</a> and create a new API token. You'll need this for API authentication.</p>
                </li>
                <li>
                    <strong>Open the API Tester</strong>
                    <p>Navigate to the <a href="api-test.php" style="color: #fc6d26;">API Tester</a> page to test the vulnerable endpoint.</p>
                </li>
                <li>
                    <strong>Configure Your Request</strong>
                    <p>Set up the request with:</p>
                    <ul style="margin-top: 0.5rem;">
                        <li>Your API token for authorization</li>
                        <li>YOUR project ID (e.g., 3 or 4 - your own projects)</li>
                        <li>Any merge request IID and SHA values</li>
                    </ul>
                </li>
                <li>
                    <strong>Change the Status Check ID</strong>
                    <p>The key vulnerability! Change <code>external_status_check_id</code> from your project's status check to one belonging to a <span style="color: #ff8888;">PRIVATE project</span> (e.g., ID 1 or 2).</p>
                </li>
                <li>
                    <strong>Send the Request and Observe</strong>
                    <p>The API will return sensitive information about the OTHER project's status check, including:</p>
                    <div class="code-block">
{
  "id": 1,
  "name": "AWS Deployment Validator",
  "project": {
    "id": 1,
    "name": "<span class="vulnerable">Confidential Infrastructure</span>",
    "visibility": "<span class="vulnerable">private</span>"
  },
  "external_url": "<span class="vulnerable">https://aws-validator.internal.corp.local/api/validate?key=secret_aws_key_12345</span>",
  "_debug": {
    "cross_project_access": "<span class="vulnerable">YES - IDOR DETECTED!</span>"
  }
}
                    </div>
                </li>
            </ol>
        </div>

        <div class="card">
            <h2>💡 Hints</h2>
            <div class="hint-box">
                <h4>Hint 1: Status Check IDs</h4>
                <p>Status checks with IDs 1-2 belong to victim01's private projects. IDs 3-5 are from other projects.</p>
            </div>
            <div class="hint-box">
                <h4>Hint 2: Cross-Project Access</h4>
                <p>The API validates that YOU have access to the project_id you specify, but NOT that the status check belongs to that project!</p>
            </div>
            <div class="hint-box">
                <h4>Hint 3: Success Indicator</h4>
                <p>Look for <code>"cross_project_access": "YES - IDOR DETECTED!"</code> in the response to confirm successful exploitation.</p>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="index.php" class="btn btn-secondary">← Lab Home</a>
            <a href="../index.php" class="btn btn-secondary">All Labs</a>
        </div>
    </div>
</body>
</html>
