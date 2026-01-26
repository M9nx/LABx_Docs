<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - MethodLab</title>
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
        .docs-container {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            gap: 2rem;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .sidebar h3 {
            color: #ff4444;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .sidebar-nav {
            list-style: none;
        }
        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }
        .sidebar-nav a {
            display: block;
            color: #ccc;
            text-decoration: none;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            padding-left: 1.5rem;
        }
        .sidebar-nav a.active {
            background: rgba(255, 68, 68, 0.3);
            color: #ff4444;
            font-weight: 600;
        }
        .content {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2.5rem;
        }
        .content h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .content h2 {
            color: #ff6666;
            font-size: 1.8rem;
            margin: 2rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(255, 68, 68, 0.3);
        }
        .content h3 {
            color: #ff8888;
            font-size: 1.3rem;
            margin: 1.5rem 0 1rem 0;
        }
        .content p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content ul, .content ol {
            color: #ccc;
            line-height: 1.8;
            margin: 1rem 0 1rem 2rem;
        }
        .content li {
            margin-bottom: 0.5rem;
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
            font-size: 0.9rem;
        }
        .info-box {
            background: rgba(100, 100, 255, 0.1);
            border-left: 4px solid #6666ff;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .info-box strong {
            color: #aaaaff;
            display: block;
            margin-bottom: 0.5rem;
        }
        .warning-box {
            background: rgba(255, 150, 0, 0.1);
            border-left: 4px solid #ff9600;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning-box strong {
            color: #ffaa66;
            display: block;
            margin-bottom: 0.5rem;
        }
        .success-box {
            background: rgba(0, 255, 0, 0.1);
            border-left: 4px solid #00ff00;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .success-box strong {
            color: #66ff66;
            display: block;
            margin-bottom: 0.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        th {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            font-weight: 600;
        }
        td {
            background: rgba(0, 0, 0, 0.3);
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

    <div class="docs-container">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php" class="active">📖 Overview</a></li>
                <li><a href="docs-http-methods.php">🌐 HTTP Methods</a></li>
                <li><a href="docs-access-control.php">🔒 Access Control</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-references.php">📚 References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>📖 Method-based Access Control Documentation</h1>
            
            <div class="info-box">
                <strong>🎯 Lab Overview</strong>
                <p>This lab demonstrates a critical vulnerability where access controls are implemented based on HTTP request methods, allowing attackers to bypass security checks by simply changing the request method.</p>
            </div>

            <h2>🔍 What is Method-based Access Control?</h2>
            <p>Method-based access control is a security mechanism where different HTTP methods (GET, POST, PUT, DELETE, etc.) are treated differently in terms of authorization. The assumption is that certain methods are "safer" or require different privilege levels.</p>

            <h3>Common Assumptions (Often Wrong):</h3>
            <ul>
                <li><strong>GET requests</strong> are read-only and don't need strict authorization</li>
                <li><strong>POST requests</strong> modify data and require authentication</li>
                <li><strong>PUT/DELETE</strong> are privileged operations</li>
            </ul>

            <div class="warning-box">
                <strong>⚠️ The Critical Flaw</strong>
                <p>If an application only validates authorization for specific HTTP methods but not others, attackers can bypass access controls by converting requests to unprotected methods.</p>
            </div>

            <h2>🎯 Lab Scenario</h2>
            <p>In this lab, the admin panel allows administrators to promote users to admin status. The endpoint <code>admin-upgrade.php</code> has the following flawed implementation:</p>

            <div class="code-block"><code>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    // Check admin privileges only for POST<br>    if ($_SESSION['role'] !== 'admin') {<br>        die('Access denied');<br>    }<br>    $username = $_POST['username'];<br>} else {<br>    // GET method bypasses the check!<br>    $username = $_GET['username'];<br>}<br><br>// Promote user to admin (executed regardless of privileges)<br>$stmt->execute();</code></div>

            <h2>💡 Why This Vulnerability Exists</h2>
            <p>Developers often make these mistakes:</p>
            <ol>
                <li><strong>False Sense of Security:</strong> Assuming users will only interact through the UI</li>
                <li><strong>Incomplete Validation:</strong> Only checking authorization for expected methods</li>
                <li><strong>Framework Defaults:</strong> Relying on default behaviors without explicit validation</li>
                <li><strong>Legacy Code:</strong> Old codebases that assumed GET was always safe</li>
            </ol>

            <h2>🔑 Key Concepts</h2>
            
            <h3>1. HTTP Methods</h3>
            <p>HTTP defines several request methods, each with semantic meaning:</p>
            <table>
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Intended Purpose</th>
                        <th>Idempotent</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>GET</strong></td>
                        <td>Retrieve data</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td><strong>POST</strong></td>
                        <td>Submit data / Create resource</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td><strong>PUT</strong></td>
                        <td>Update/Replace resource</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td><strong>DELETE</strong></td>
                        <td>Delete resource</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td><strong>PATCH</strong></td>
                        <td>Partial update</td>
                        <td>No</td>
                    </tr>
                </tbody>
            </table>

            <h3>2. Access Control Models</h3>
            <p>Proper access control should be:</p>
            <ul>
                <li><strong>Method-agnostic:</strong> Authorization checks apply regardless of HTTP method</li>
                <li><strong>Centralized:</strong> Single point of enforcement</li>
                <li><strong>Explicit:</strong> Deny by default, allow explicitly</li>
                <li><strong>Consistent:</strong> Same rules across all entry points</li>
            </ul>

            <div class="success-box">
                <strong>✅ Best Practice</strong>
                <p>Always validate authorization before processing ANY request, regardless of the HTTP method used. The method should never be used as a security boundary.</p>
            </div>

            <h2>🎓 Learning Objectives</h2>
            <p>By completing this lab, you will:</p>
            <ol>
                <li>Understand how HTTP methods work</li>
                <li>Recognize method-based access control vulnerabilities</li>
                <li>Learn to intercept and modify HTTP requests</li>
                <li>Practice converting POST to GET requests</li>
                <li>Understand proper access control implementation</li>
            </ol>

            <h2>🚀 Getting Started</h2>
            <p>To begin this lab:</p>
            <ol>
                <li>Click <strong>"Setup Database"</strong> to initialize the lab environment</li>
                <li>Login as <code>administrator:admin</code> to see how promotion works</li>
                <li>Observe the POST request when promoting a user</li>
                <li>Login as <code>wiener:peter</code> and craft a GET request to promote yourself</li>
                <li>Navigate through the sidebar topics to learn more about each aspect</li>
            </ol>

            <div class="info-box">
                <strong>📌 Pro Tip</strong>
                <p>Use browser DevTools (Network tab) or a proxy like Burp Suite to intercept and modify requests. You can also craft requests manually using curl or directly in the browser URL bar.</p>
            </div>

            <h2>📋 Lab Credentials</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>administrator</td>
                        <td>admin</td>
                        <td>Admin</td>
                    </tr>
                    <tr>
                        <td>wiener</td>
                        <td>peter</td>
                        <td>User</td>
                    </tr>
                    <tr>
                        <td>carlos</td>
                        <td>montoya</td>
                        <td>User</td>
                    </tr>
                </tbody>
            </table>

            <h2>🗺️ Navigation</h2>
            <p>Use the sidebar to explore different topics:</p>
            <ul>
                <li><strong>HTTP Methods:</strong> Deep dive into HTTP request methods</li>
                <li><strong>Access Control:</strong> Understanding access control mechanisms</li>
                <li><strong>Exploitation:</strong> Step-by-step exploitation guide</li>
                <li><strong>Prevention:</strong> How to prevent this vulnerability</li>
                <li><strong>References:</strong> Additional resources and links</li>
            </ul>
        </main>
    </div>
</body>
</html>
