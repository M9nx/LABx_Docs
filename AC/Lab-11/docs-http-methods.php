<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTTP Methods - MethodLab</title>
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
                <li><a href="docs.php">📖 Overview</a></li>
                <li><a href="docs-http-methods.php" class="active">🌐 HTTP Methods</a></li>
                <li><a href="docs-access-control.php">🔒 Access Control</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-references.php">📚 References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>🌐 HTTP Methods Deep Dive</h1>

            <p>HTTP (Hypertext Transfer Protocol) defines several request methods to indicate the desired action to be performed on a resource. Understanding these methods is crucial for exploiting method-based access control vulnerabilities.</p>

            <h2>📋 Standard HTTP Methods</h2>

            <table>
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Purpose</th>
                        <th>Safe</th>
                        <th>Idempotent</th>
                        <th>Cacheable</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>GET</strong></td>
                        <td>Retrieve resource</td>
                        <td>✅ Yes</td>
                        <td>✅ Yes</td>
                        <td>✅ Yes</td>
                    </tr>
                    <tr>
                        <td><strong>POST</strong></td>
                        <td>Submit/Create data</td>
                        <td>❌ No</td>
                        <td>❌ No</td>
                        <td>⚠️ Rarely</td>
                    </tr>
                    <tr>
                        <td><strong>PUT</strong></td>
                        <td>Update/Replace resource</td>
                        <td>❌ No</td>
                        <td>✅ Yes</td>
                        <td>❌ No</td>
                    </tr>
                    <tr>
                        <td><strong>DELETE</strong></td>
                        <td>Remove resource</td>
                        <td>❌ No</td>
                        <td>✅ Yes</td>
                        <td>❌ No</td>
                    </tr>
                    <tr>
                        <td><strong>PATCH</strong></td>
                        <td>Partial modification</td>
                        <td>❌ No</td>
                        <td>❌ No</td>
                        <td>❌ No</td>
                    </tr>
                    <tr>
                        <td><strong>HEAD</strong></td>
                        <td>Get headers only</td>
                        <td>✅ Yes</td>
                        <td>✅ Yes</td>
                        <td>✅ Yes</td>
                    </tr>
                    <tr>
                        <td><strong>OPTIONS</strong></td>
                        <td>Describe options</td>
                        <td>✅ Yes</td>
                        <td>✅ Yes</td>
                        <td>❌ No</td>
                    </tr>
                </tbody>
            </table>

            <h2>🔑 Key Concepts</h2>

            <h3>1. Safe Methods</h3>
            <p>A method is <strong>safe</strong> if it doesn't modify server state. Safe methods should only retrieve data, never alter it.</p>
            <div class="code-block"><code>Safe Methods: GET, HEAD, OPTIONS<br>Unsafe Methods: POST, PUT, DELETE, PATCH</code></div>
            
            <div class="warning-box">
                <strong>⚠️ Common Mistake</strong>
                <p>Developers often assume GET is always safe, but nothing prevents using GET to modify state. This assumption leads to vulnerabilities!</p>
            </div>

            <h3>2. Idempotent Methods</h3>
            <p>A method is <strong>idempotent</strong> if making the same request multiple times has the same effect as making it once.</p>
            <ul>
                <li><strong>GET:</strong> Retrieving data 10 times = retrieving it once</li>
                <li><strong>PUT:</strong> Setting value to X ten times = setting it to X once</li>
                <li><strong>DELETE:</strong> Deleting a resource twice = deleting it once (after first delete, it's gone)</li>
                <li><strong>POST:</strong> Creating 10 times = 10 resources created (NOT idempotent)</li>
            </ul>

            <h3>3. Cacheable Methods</h3>
            <p>Cacheable methods can have their responses stored and reused. Typically only GET and HEAD are cacheable.</p>

            <h2>🔍 GET vs POST: The Critical Difference</h2>

            <table>
                <thead>
                    <tr>
                        <th>Aspect</th>
                        <th>GET</th>
                        <th>POST</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Data Location</strong></td>
                        <td>URL query string</td>
                        <td>Request body</td>
                    </tr>
                    <tr>
                        <td><strong>Visibility</strong></td>
                        <td>Visible in URL</td>
                        <td>Hidden in body</td>
                    </tr>
                    <tr>
                        <td><strong>Browser History</strong></td>
                        <td>Stored</td>
                        <td>Not stored</td>
                    </tr>
                    <tr>
                        <td><strong>Bookmarkable</strong></td>
                        <td>Yes</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td><strong>Data Length</strong></td>
                        <td>Limited (~2000 chars)</td>
                        <td>Unlimited</td>
                    </tr>
                    <tr>
                        <td><strong>Intended Use</strong></td>
                        <td>Retrieve data</td>
                        <td>Submit data</td>
                    </tr>
                    <tr>
                        <td><strong>Security</strong></td>
                        <td>Less secure</td>
                        <td>More secure</td>
                    </tr>
                </tbody>
            </table>

            <h2>💥 The Vulnerability: Method Conversion</h2>

            <p>In this lab, the vulnerable endpoint accepts both GET and POST but only validates authorization for POST:</p>

            <h3>POST Request (Requires Admin):</h3>
            <div class="code-block"><code>POST /admin-upgrade.php HTTP/1.1<br>Host: localhost<br>Cookie: PHPSESSID=abc123<br>Content-Type: application/x-www-form-urlencoded<br><br>username=carlos</code></div>

            <h3>GET Request (No Authorization Check!):</h3>
            <div class="code-block"><code>GET /admin-upgrade.php?username=wiener HTTP/1.1<br>Host: localhost<br>Cookie: PHPSESSID=abc123</code></div>

            <div class="info-box">
                <strong>🎯 Exploitation Path</strong>
                <p>By converting the POST request to GET, you bypass the admin privilege check and can promote yourself to admin!</p>
            </div>

            <h2>🛠️ Converting POST to GET</h2>

            <h3>Method 1: Browser URL Bar</h3>
            <p>Simply type the URL with query parameters:</p>
            <div class="code-block"><code>http://localhost/AC/lab11/admin-upgrade.php?username=wiener</code></div>

            <h3>Method 2: Using curl</h3>
            <div class="code-block"><code>curl "http://localhost/AC/lab11/admin-upgrade.php?username=wiener" \<br>  -b "PHPSESSID=your_session_id_here"</code></div>

            <h3>Method 3: Using Burp Suite</h3>
            <ol>
                <li>Capture the POST request in Burp Proxy</li>
                <li>Right-click → "Change request method"</li>
                <li>Burp automatically converts POST body to GET parameters</li>
                <li>Forward the modified request</li>
            </ol>

            <h3>Method 4: Using Browser DevTools</h3>
            <ol>
                <li>Open DevTools (F12) → Network tab</li>
                <li>Perform the action and find the POST request</li>
                <li>Right-click → Copy as cURL</li>
                <li>Modify the curl command to use GET</li>
                <li>Run in terminal</li>
            </ol>

            <h2>🔐 Why Method-based Security Fails</h2>

            <p>Relying on HTTP methods for security has fundamental flaws:</p>
            <ol>
                <li><strong>Client-Side Control:</strong> HTTP method is controlled by the client, not the server</li>
                <li><strong>Easy to Manipulate:</strong> Changing methods requires no special tools or skills</li>
                <li><strong>Framework Quirks:</strong> Different frameworks handle methods differently</li>
                <li><strong>Legacy Assumptions:</strong> Old code assumed browsers only sent GET/POST</li>
            </ol>

            <div class="warning-box">
                <strong>⚠️ Security Principle Violation</strong>
                <p>HTTP methods are part of the request, which is fully controlled by the attacker. Never trust client-controlled data for security decisions!</p>
            </div>

            <h2>📊 Real-World Examples</h2>

            <h3>Example 1: REST API Bypass</h3>
            <div class="code-block"><code>// Vulnerable API endpoint<br>DELETE /api/users/123  // Requires admin<br>GET /api/users/123     // Read-only, anyone can access<br><br>// What if the DELETE logic is also accessible via GET?<br>GET /api/users/123?_method=DELETE  // BYPASSED!</code></div>

            <h3>Example 2: Form Submission Override</h3>
            <div class="code-block"><code>&lt;!-- Expected: POST form --&gt;<br>&lt;form method="POST" action="/admin/delete-user"&gt;<br>    &lt;input name="user_id" value="123"&gt;<br>&lt;/form&gt;<br><br>&lt;!-- Attacker converts to GET: --&gt;<br>/admin/delete-user?user_id=123</code></div>

            <h2>🎓 Key Takeaways</h2>
            <ul>
                <li>HTTP methods indicate <strong>intent</strong>, not security boundaries</li>
                <li>Any method can be converted to another by the client</li>
                <li>Authorization must be method-agnostic</li>
                <li>GET requests should NEVER modify server state</li>
                <li>Use proper access control checks regardless of HTTP method</li>
            </ul>
        </main>
    </div>
</body>
</html>
