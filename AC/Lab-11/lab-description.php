<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - MethodLab</title>
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
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .lab-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .lab-header h1 {
            font-size: 2.5rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .lab-header p {
            font-size: 1.2rem;
            color: #999;
        }
        .content-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-section h2 {
            color: #ff4444;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .content-section h3 {
            color: #ff6666;
            margin: 1.5rem 0 1rem 0;
            font-size: 1.3rem;
        }
        .content-section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block code {
            color: #66ff66;
            font-family: 'Courier New', monospace;
        }
        .vulnerability-badge {
            display: inline-block;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.5);
            color: #ff6666;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .step-list {
            list-style: none;
            counter-reset: step-counter;
        }
        .step-list li {
            counter-increment: step-counter;
            background: rgba(0, 0, 0, 0.3);
            border-left: 3px solid #ff4444;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            position: relative;
            padding-left: 3rem;
        }
        .step-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 1rem;
            top: 1rem;
            background: #ff4444;
            color: white;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .info-box {
            background: rgba(100, 100, 255, 0.1);
            border: 1px solid rgba(100, 100, 255, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .info-box strong {
            color: #aaaaff;
        }
        .warning-box {
            background: rgba(255, 150, 0, 0.1);
            border: 1px solid rgba(255, 150, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .warning-box strong {
            color: #ffaa66;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
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
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">⚙️ MethodLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-header">
            <h1>⚙️ Lab 11: Method-based Access Control</h1>
            <p>Circumvent HTTP method-based access control</p>
        </div>

        <div class="content-section">
            <h2>🎯 Lab Objective</h2>
            <span class="vulnerability-badge">Method-based Access Control Bypass</span>
            <p>This lab implements access controls based partly on the <strong>HTTP method</strong> of requests. You can familiarize yourself with the admin panel by logging in using the credentials <code>administrator:admin</code>.</p>
            <p>To solve the lab, log in using the credentials <code>wiener:peter</code> and exploit the flawed access controls to <strong>promote yourself to become an administrator</strong>.</p>
        </div>

        <div class="content-section">
            <h2>🔍 Understanding the Vulnerability</h2>
            <p>Many web applications implement access control by restricting access based on the HTTP request method. For example:</p>
            <ul style="list-style-position: inside; color: #ccc; margin-left: 1rem;">
                <li>POST requests might require admin privileges</li>
                <li>GET requests might be considered "read-only" and less restricted</li>
            </ul>
            <p style="margin-top: 1rem;">However, if the application doesn't properly validate authorization for <strong>all</strong> HTTP methods, an attacker can bypass the access control by simply changing the request method.</p>

            <div class="warning-box">
                <strong>⚠️ The Flaw:</strong>
                <p>The admin-upgrade.php endpoint checks for admin privileges only when the request method is POST. GET requests bypass this check entirely!</p>
            </div>
        </div>

        <div class="content-section">
            <h2>📋 Solution Walkthrough</h2>
            <ol class="step-list">
                <li>
                    <strong>Explore as Admin</strong><br>
                    Login with <code>administrator:admin</code> and visit the Admin Panel. Try promoting user "carlos" and observe the POST request to admin-upgrade.php.
                </li>
                <li>
                    <strong>Capture the Request</strong><br>
                    Use browser DevTools (Network tab) or a proxy like Burp Suite to capture the POST request when promoting carlos.
                    <div class="code-block"><code>POST /admin-upgrade.php<br>username=carlos</code></div>
                </li>
                <li>
                    <strong>Login as Regular User</strong><br>
                    Logout and login with <code>wiener:peter</code>. You'll see you don't have access to the Admin Panel.
                </li>
                <li>
                    <strong>Convert POST to GET</strong><br>
                    Manually craft a GET request with your username as a parameter:
                    <div class="code-block"><code>GET /admin-upgrade.php?username=wiener</code></div>
                    You can do this by typing the URL directly in your browser or using curl:
                    <div class="code-block"><code>curl "http://localhost/AC/lab11/admin-upgrade.php?username=wiener" \<br>  -b "PHPSESSID=your_session_id"</code></div>
                </li>
                <li>
                    <strong>Check Your Profile</strong><br>
                    Visit your profile page. You should now see your role changed to "ADMIN"!
                </li>
                <li>
                    <strong>Lab Solved!</strong><br>
                    The success page will automatically load, confirming you've bypassed the access control.
                </li>
            </ol>
        </div>

        <div class="content-section">
            <h2>💡 Why This Works</h2>
            <p>The vulnerable code in admin-upgrade.php looks like this:</p>
            <div class="code-block"><code>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    // Only checks admin privileges for POST<br>    if ($_SESSION['role'] !== 'admin') {<br>        die('Access denied');<br>    }<br>    $username = $_POST['username'];<br>} else {<br>    // GET method - NO ADMIN CHECK!<br>    $username = $_GET['username'];<br>}<br><br>// Promotes user to admin<br>UPDATE users SET role = 'admin' WHERE username = ?</code></div>
            
            <div class="info-box">
                <strong>🔑 Key Insight:</strong>
                <p>The developer assumed that only POST requests would be used for privileged operations, but forgot to validate authorization for other HTTP methods. This allows any authenticated user to perform admin actions via GET.</p>
            </div>
        </div>

        <div class="content-section">
            <h2>🛡️ How to Prevent This</h2>
            <p><strong>1. Validate Authorization for ALL HTTP Methods:</strong></p>
            <div class="code-block"><code>// Check admin privileges regardless of method<br>if ($_SESSION['role'] !== 'admin') {<br>    http_response_code(403);<br>    die('Access denied');<br>}<br><br>// Then handle the request<br>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    $username = $_POST['username'];<br>} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {<br>    $username = $_GET['username'];<br>}</code></div>

            <p><strong>2. Use a Consistent Authorization Layer:</strong></p>
            <ul style="list-style-position: inside; color: #ccc; margin-left: 1rem; margin-top: 0.5rem;">
                <li>Implement centralized access control checks</li>
                <li>Apply authorization checks before processing any request</li>
                <li>Don't rely on HTTP method as a security control</li>
            </ul>

            <p style="margin-top: 1rem;"><strong>3. Use POST for State-Changing Operations:</strong></p>
            <ul style="list-style-position: inside; color: #ccc; margin-left: 1rem; margin-top: 0.5rem;">
                <li>Restrict privileged operations to POST/PUT/DELETE only</li>
                <li>Implement CSRF protection for state-changing requests</li>
                <li>Never allow GET requests to modify server state</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="index.php" class="btn btn-secondary">← Back to Home</a>
        </div>
    </div>
</body>
</html>
