<?php
require_once 'config.php';
$session = getSessionFromCookie();
$isLoggedIn = ($session !== null);
$hasAdminPrivs = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Modifying Serialized Objects</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(249,115,22,0.3);
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.8rem; font-weight: bold; color: #f97316; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #f97316; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 1.2rem; background: rgba(255,255,255,0.1);
            border: 1px solid rgba(249,115,22,0.3); color: #e0e0e0;
            text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s;
        }
        .btn-back:hover { background: rgba(249,115,22,0.2); border-color: #f97316; color: #f97316; }
        .container { max-width: 900px; margin: 0 auto; padding: 3rem 2rem; }
        .lab-header {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .difficulty-badge {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-title { font-size: 2rem; color: #f97316; margin-bottom: 1rem; }
        .lab-meta { display: flex; gap: 2rem; color: #999; font-size: 0.9rem; margin-bottom: 1.5rem; }
        .section {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(249,115,22,0.15);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .section h2 { color: #fb923c; margin-bottom: 1.5rem; font-size: 1.3rem; }
        .section p { color: #b0b0b0; line-height: 1.8; margin-bottom: 1rem; }
        .section ul { color: #b0b0b0; margin-left: 1.5rem; line-height: 1.8; }
        .section li { margin-bottom: 0.5rem; }
        .code-box {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-box code {
            color: #fb923c;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
        }
        .objective-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .objective-box h3 { color: #ef4444; margin-bottom: 0.8rem; }
        .objective-box p { color: #fca5a5; margin: 0; }
        .credentials-box {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .credentials-box h3 { color: #22c55e; margin-bottom: 0.8rem; }
        .credentials-box code {
            background: rgba(0,0,0,0.3);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            color: #22c55e;
            font-family: 'Consolas', monospace;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
            margin-top: 0.5rem;
        }
        .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(249,115,22,0.4); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📦 SerialLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isLoggedIn): ?>
                    <a href="my-account.php">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-header">
            <span class="difficulty-badge">APPRENTICE</span>
            <h1 class="lab-title">Lab 1: Modifying Serialized Objects</h1>
            <div class="lab-meta">
                <span>📂 Category: Insecure Deserialization</span>
                <span>⏱️ Estimated Time: 15-30 minutes</span>
                <span>🔧 Type: PHP Serialization</span>
            </div>
        </div>

        <div class="section">
            <h2>📋 Lab Description</h2>
            <p>This lab uses a serialization-based session mechanism and is vulnerable to privilege escalation as a result.</p>
            <p>The application stores session data in a cookie as a serialized PHP object. This object contains an <code>admin</code> attribute that determines whether the user has administrative privileges. The server trusts this client-provided data without proper validation.</p>
            
            <div class="objective-box">
                <h3>🎯 Objective</h3>
                <p>Edit the serialized object in the session cookie to exploit this vulnerability and gain administrative privileges. Then, delete the user <strong>carlos</strong>.</p>
            </div>

            <div class="credentials-box">
                <h3>🔑 Your Credentials</h3>
                <p>Username: <code>wiener</code> | Password: <code>peter</code></p>
            </div>
        </div>

        <div class="section">
            <h2>🧪 Attack Vector</h2>
            <p>The vulnerability exists because:</p>
            <ul>
                <li>Session data is stored client-side in a cookie</li>
                <li>The data is serialized using PHP's <code>serialize()</code> function</li>
                <li>The cookie is only encoded (Base64 + URL), not encrypted or signed</li>
                <li>The server uses <code>unserialize()</code> on the cookie without validation</li>
                <li>Authorization decisions are made based on deserialized cookie data</li>
            </ul>
        </div>

        <div class="section">
            <h2>📚 Background: PHP Serialization</h2>
            <p>PHP serialization converts objects and data structures into a string format that can be stored or transmitted. The format includes type indicators:</p>
            <div class="code-box">
                <code>
                    b:0 → Boolean false<br>
                    b:1 → Boolean true<br>
                    i:42 → Integer 42<br>
                    s:6:"wiener" → String "wiener" (6 chars)<br>
                    O:8:"stdClass" → Object of class "stdClass"
                </code>
            </div>
            <p>When an application deserializes user-controlled data without proper validation, attackers can modify the serialized values to change the application's behavior.</p>
        </div>

        <div class="section">
            <h2>🔍 Solution Approach</h2>
            <ol style="color: #b0b0b0; margin-left: 1.5rem; line-height: 2;">
                <li>Login using the provided credentials</li>
                <li>Capture the session cookie from the browser or proxy</li>
                <li>URL-decode the cookie value</li>
                <li>Base64-decode the result to reveal the serialized PHP object</li>
                <li>Identify the <code>admin</code> attribute (shows <code>b:0</code> for false)</li>
                <li>Change <code>b:0</code> to <code>b:1</code> to enable admin privileges</li>
                <li>Base64-encode and URL-encode the modified object</li>
                <li>Replace the session cookie with the modified value</li>
                <li>Access the admin panel and delete user carlos</li>
            </ol>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="login.php" class="btn btn-primary">Start Lab →</a>
            <a href="docs.php" class="btn btn-primary">View Documentation →</a>
        </div>
    </div>
</body>
</html>
