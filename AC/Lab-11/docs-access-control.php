<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control - MethodLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 68, 68, 0.3); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff4444; }
        .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 68, 68, 0.3); color: #e0e0e0; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.3s; }
        .btn-back:hover { background: rgba(255, 68, 68, 0.2); border-color: #ff4444; color: #ff4444; }
        .docs-container { display: flex; max-width: 1400px; margin: 0 auto; padding: 2rem; gap: 2rem; }
        .sidebar { width: 280px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 15px; padding: 1.5rem; height: fit-content; position: sticky; top: 100px; }
        .sidebar h3 { color: #ff4444; margin-bottom: 1rem; font-size: 1.2rem; }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.5rem; }
        .sidebar-nav a { display: block; color: #ccc; text-decoration: none; padding: 0.7rem 1rem; border-radius: 8px; transition: all 0.3s; }
        .sidebar-nav a:hover { background: rgba(255, 68, 68, 0.2); color: #ff6666; padding-left: 1.5rem; }
        .sidebar-nav a.active { background: rgba(255, 68, 68, 0.3); color: #ff4444; font-weight: 600; }
        .content { flex: 1; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 15px; padding: 2.5rem; }
        .content h1 { color: #ff4444; font-size: 2.5rem; margin-bottom: 1rem; }
        .content h2 { color: #ff6666; font-size: 1.8rem; margin: 2rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(255, 68, 68, 0.3); }
        .content h3 { color: #ff8888; font-size: 1.3rem; margin: 1.5rem 0 1rem 0; }
        .content p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
        .content ul, .content ol { color: #ccc; line-height: 1.8; margin: 1rem 0 1rem 2rem; }
        .content li { margin-bottom: 0.5rem; }
        .code-block { background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 8px; padding: 1rem; margin: 1rem 0; overflow-x: auto; }
        .code-block code { color: #66ff66; font-family: 'Courier New', monospace; font-size: 0.9rem; }
        .info-box { background: rgba(100, 100, 255, 0.1); border-left: 4px solid #6666ff; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .info-box strong { color: #aaaaff; display: block; margin-bottom: 0.5rem; }
        .warning-box { background: rgba(255, 150, 0, 0.1); border-left: 4px solid #ff9600; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .warning-box strong { color: #ffaa66; display: block; margin-bottom: 0.5rem; }
        .success-box { background: rgba(0, 255, 0, 0.1); border-left: 4px solid #00ff00; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .success-box strong { color: #66ff66; display: block; margin-bottom: 0.5rem; }
        table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        th, td { padding: 1rem; text-align: left; border: 1px solid rgba(255, 68, 68, 0.3); }
        th { background: rgba(255, 68, 68, 0.2); color: #ff6666; font-weight: 600; }
        td { background: rgba(0, 0, 0, 0.3); }
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
                <li><a href="docs-http-methods.php">🌐 HTTP Methods</a></li>
                <li><a href="docs-access-control.php" class="active">🔒 Access Control</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-references.php">📚 References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>🔒 Access Control Fundamentals</h1>

            <p>Access control (also known as authorization) is the process of determining whether a user has permission to perform a requested action or access a resource.</p>

            <h2>🎯 Access Control Models</h2>

            <h3>1. Discretionary Access Control (DAC)</h3>
            <p>Resource owners control access permissions. Common in file systems.</p>
            <ul>
                <li>Owner decides who can access their resources</li>
                <li>Flexible but can be inconsistent</li>
                <li>Example: File permissions (chmod)</li>
            </ul>

            <h3>2. Mandatory Access Control (MAC)</h3>
            <p>System-wide policies enforced by administrators. Common in military/government.</p>
            <ul>
                <li>Centralized policy enforcement</li>
                <li>Users cannot override policies</li>
                <li>Example: SELinux, classified document systems</li>
            </ul>

            <h3>3. Role-Based Access Control (RBAC)</h3>
            <p>Permissions assigned to roles, users assigned to roles. <strong>Most common in web applications.</strong></p>
            <ul>
                <li>Users have roles (admin, user, moderator)</li>
                <li>Roles have permissions</li>
                <li>Example: This lab uses RBAC!</li>
            </ul>

            <div class="code-block"><code>User "wiener" → Role: "user" → Permissions: [view_profile]<br>User "administrator" → Role: "admin" → Permissions: [view_profile, promote_users, view_admin_panel]</code></div>

            <h3>4. Attribute-Based Access Control (ABAC)</h3>
            <p>Access based on attributes (user attributes, resource attributes, environmental attributes).</p>
            <ul>
                <li>More granular than RBAC</li>
                <li>Complex but powerful</li>
                <li>Example: "Users from IP 10.0.0.0/8 can access between 9am-5pm"</li>
            </ul>

            <h2>🔑 Key Principles</h2>

            <h3>Principle of Least Privilege</h3>
            <p>Users should have only the minimum permissions necessary to perform their tasks.</p>
            <div class="success-box">
                <strong>✅ Good Example</strong>
                <p>A regular user account that can only view their own profile and cannot access admin functions.</p>
            </div>

            <h3>Defense in Depth</h3>
            <p>Multiple layers of security controls.</p>
            <ul>
                <li>Authentication (Who are you?)</li>
                <li>Authorization (What can you do?)</li>
                <li>Input validation</li>
                <li>Output encoding</li>
                <li>Logging and monitoring</li>
            </ul>

            <h3>Deny by Default</h3>
            <p>Everything is forbidden unless explicitly allowed.</p>
            <div class="code-block"><code>// BAD: Allow everything except blacklist<br>if ($path !== '/admin') {<br>    allow_access();<br>}<br><br>// GOOD: Deny everything except whitelist<br>if ($path === '/public' || $path === '/profile') {<br>    allow_access();<br>} else {<br>    deny_access();<br>}</code></div>

            <h2>⚠️ Common Access Control Vulnerabilities</h2>

            <h3>1. Vertical Privilege Escalation</h3>
            <p>Lower-privileged user gains access to higher-privileged functions.</p>
            <div class="warning-box">
                <strong>Example in this lab:</strong>
                <p>Regular user "wiener" promotes themselves to admin by exploiting method-based bypass.</p>
            </div>

            <h3>2. Horizontal Privilege Escalation</h3>
            <p>User accesses resources belonging to another user at the same privilege level.</p>
            <div class="code-block"><code>// Vulnerable: No check if user owns the resource<br>/profile.php?user_id=123  // User A views their profile<br>/profile.php?user_id=456  // User A views User B's profile!</code></div>

            <h3>3. Insecure Direct Object References (IDOR)</h3>
            <p>Application exposes internal object references without access control checks.</p>
            <div class="code-block"><code>// Vulnerable<br>/api/documents/12345  // Document ID directly in URL<br><br>// Should check: Does current user own document 12345?</code></div>

            <h3>4. Missing Function Level Access Control</h3>
            <p>Application doesn't verify authorization before executing privileged functions.</p>
            <div class="code-block"><code>// Vulnerable: No admin check!<br>function promote_user($username) {<br>    UPDATE users SET role='admin' WHERE username=$username;<br>}<br><br>// Secure: Check authorization first<br>function promote_user($username) {<br>    if (!is_admin()) {<br>        die('Access denied');<br>    }<br>    UPDATE users SET role='admin' WHERE username=$username;<br>}</code></div>

            <h3>5. Method-Based Access Control (This Lab!)</h3>
            <p>Authorization checks only apply to specific HTTP methods.</p>
            <div class="code-block"><code>// VULNERABLE CODE FROM THIS LAB:<br>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    // Only checks admin for POST<br>    if ($_SESSION['role'] !== 'admin') {<br>        die('Access denied');<br>    }<br>    $username = $_POST['username'];<br>} else {<br>    // GET bypasses the check!<br>    $username = $_GET['username'];<br>}<br>// Executes regardless of privileges<br>promote_user($username);</code></div>

            <h2>🔍 Access Control in Web Applications</h2>

            <h3>Where to Implement Access Control</h3>
            <table>
                <thead>
                    <tr>
                        <th>Layer</th>
                        <th>Implementation</th>
                        <th>Pros</th>
                        <th>Cons</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>UI Layer</strong></td>
                        <td>Hide buttons/links</td>
                        <td>Better UX</td>
                        <td>NOT security! Can be bypassed</td>
                    </tr>
                    <tr>
                        <td><strong>Web Server</strong></td>
                        <td>.htaccess, nginx rules</td>
                        <td>Fast, early rejection</td>
                        <td>Limited logic, hard to maintain</td>
                    </tr>
                    <tr>
                        <td><strong>Application</strong></td>
                        <td>Code-level checks</td>
                        <td>Full control, flexible</td>
                        <td>Must be consistent everywhere</td>
                    </tr>
                    <tr>
                        <td><strong>Database</strong></td>
                        <td>Row-level security</td>
                        <td>Defense in depth</td>
                        <td>Performance impact</td>
                    </tr>
                </tbody>
            </table>

            <div class="info-box">
                <strong>🎯 Best Practice</strong>
                <p>Implement access control at the <strong>application layer</strong> as the primary enforcement mechanism, with UI and database layers as additional defenses.</p>
            </div>

            <h2>🛠️ Implementing Proper Access Control</h2>

            <h3>1. Centralized Authorization</h3>
            <div class="code-block"><code>// Create a central authorization function<br>function require_admin() {<br>    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {<br>        http_response_code(403);<br>        die('Access denied');<br>    }<br>}<br><br>// Use it at the start of every privileged endpoint<br>require_admin();<br>// Rest of admin code...</code></div>

            <h3>2. Method-Agnostic Checks</h3>
            <div class="code-block"><code>// ALWAYS check authorization first, regardless of method<br>require_admin();<br><br>// Then handle different methods<br>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    $username = $_POST['username'];<br>} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {<br>    $username = $_GET['username'];<br>}<br><br>promote_user($username);</code></div>

            <h3>3. Resource-Level Checks</h3>
            <div class="code-block"><code>// Check if user owns the resource<br>function can_access_document($doc_id, $user_id) {<br>    $doc = get_document($doc_id);<br>    return $doc->owner_id === $user_id || is_admin();<br>}<br><br>if (!can_access_document($doc_id, $_SESSION['user_id'])) {<br>    die('Access denied');<br>}</code></div>

            <h2>🧪 Testing Access Controls</h2>

            <h3>Manual Testing Checklist</h3>
            <ul>
                <li>✅ Try accessing admin pages as regular user</li>
                <li>✅ Try modifying URLs to access other users' resources</li>
                <li>✅ Test all HTTP methods (GET, POST, PUT, DELETE, PATCH)</li>
                <li>✅ Test with missing/invalid session tokens</li>
                <li>✅ Try parameter tampering (change user IDs, role values)</li>
                <li>✅ Test with multiple simultaneous sessions</li>
            </ul>

            <h3>Automated Testing</h3>
            <p>Tools for testing access controls:</p>
            <ul>
                <li><strong>Burp Suite Professional:</strong> Autorize extension</li>
                <li><strong>OWASP ZAP:</strong> Access Control Testing addon</li>
                <li><strong>Custom Scripts:</strong> Write tests for your specific application</li>
            </ul>

            <h2>📊 Real-World Impact</h2>

            <div class="warning-box">
                <strong>⚠️ OWASP Top 10 #1: Broken Access Control</strong>
                <p>Access control vulnerabilities consistently rank as the #1 web application security risk. They appear in 94% of applications tested.</p>
            </div>

            <h3>Famous Breaches</h3>
            <ul>
                <li><strong>Capital One (2019):</strong> SSRF + broken access control = 100M records exposed</li>
                <li><strong>Facebook (2018):</strong> API access control flaw exposed 50M accounts</li>
                <li><strong>T-Mobile (2021):</strong> API lacked authorization = 40M customer records</li>
            </ul>

            <h2>🎓 Key Takeaways</h2>
            <ul>
                <li>Access control is the #1 web application security risk</li>
                <li>Never trust client-controlled data (including HTTP method)</li>
                <li>Implement authorization checks consistently across all endpoints</li>
                <li>Use centralized authorization functions</li>
                <li>Test access controls thoroughly</li>
                <li>Apply principle of least privilege</li>
                <li>Deny by default</li>
            </ul>
        </main>
    </div>
</body>
</html>
