<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3 Documentation - User Role Controlled by Request Parameter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #e0e0e0;
            line-height: 1.7;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 60px;
            padding: 40px 0;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            color: #ff4444;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 8px rgba(255, 68, 68, 0.3);
        }

        .header .subtitle {
            color: #999;
            font-size: 1.3rem;
            font-weight: 300;
            font-style: italic;
        }

        /* Blog-style content */
        .blog-content {
            max-width: 100%;
        }

        .blog-content h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin: 50px 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 3px solid #ff4444;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .blog-content h2 {
            color: #ff5555;
            font-size: 2rem;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #444;
        }

        .blog-content h3 {
            color: #ff6666;
            font-size: 1.4rem;
            margin: 30px 0 15px 0;
            font-weight: 600;
        }

        .blog-content h4 {
            color: #ff8888;
            font-size: 1.2rem;
            margin: 25px 0 10px 0;
            font-weight: 500;
        }

        .blog-content p {
            margin: 15px 0;
            text-align: justify;
            color: #ccc;
        }

        .blog-content ul, .blog-content ol {
            margin: 15px 0;
            padding-left: 25px;
        }

        .blog-content li {
            margin: 8px 0;
            color: #ccc;
        }

        .blog-content strong {
            color: #fff;
            font-weight: 600;
        }

        /* Code blocks */
        .code-block {
            background: #111;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #f0f0f0;
            overflow-x: auto;
            white-space: pre-wrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .inline-code {
            background: #222;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            color: #ff6666;
            border: 1px solid #444;
        }

        /* Alert boxes */
        .alert {
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            border-left: 5px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-left-color: #ff4444;
            color: #ffcccc;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border-left-color: #ffc107;
            color: #fff3cd;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .alert-info {
            background: rgba(0, 123, 255, 0.1);
            border-left-color: #007bff;
            color: #cce7ff;
            border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: #28a745;
            color: #d4edda;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        /* Navigation */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin: 50px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background: #111;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background: #222;
            color: #ff4444;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }

        /* Blockquotes */
        blockquote {
            border-left: 4px solid #ff4444;
            padding: 20px 25px;
            margin: 25px 0;
            background: rgba(255, 68, 68, 0.05);
            font-style: italic;
            color: #ddd;
            border-radius: 0 8px 8px 0;
        }

        /* Section dividers */
        hr {
            border: none;
            border-top: 2px solid #333;
            margin: 40px 0;
        }

        /* Steps styling */
        .step {
            background: rgba(255, 68, 68, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .step-number {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }

        /* Attack chain styling */
        .attack-chain {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Lab 3 Documentation</h1>
            <p class="subtitle">User Role Controlled by Request Parameter Vulnerability</p>
        </div>

        <div class="blog-content">

            <h1>Introduction to Client-Side Role Manipulation</h1>

            <p>Welcome to Lab 3, which demonstrates one of the most dangerous access control vulnerabilities: <strong>User Role Controlled by Request Parameter</strong>.</p>

            <p>This vulnerability occurs when applications trust client-side data (such as cookies, hidden form fields, or URL parameters) to make authorization decisions. Instead of verifying a user's role on the server side, the application blindly trusts whatever the client sends.</p>

<div class="alert alert-danger">
<strong>⚠️ Critical Vulnerability Alert</strong><br>
This vulnerability allows any authenticated user to escalate their privileges to administrator level simply by modifying a browser cookie.
</div>

            <h2>What is Client-Side Role Manipulation?</h2>

            <p>Client-side role manipulation happens when web applications store user privileges or roles in locations that can be modified by the user:</p>

<ul>
<li><strong>Cookies</strong>: Storing admin status in browser cookies</li>
<li><strong>Hidden Form Fields</strong>: Using hidden inputs to pass role information</li>
<li><strong>URL Parameters</strong>: Including role data in query strings</li>
<li><strong>Local Storage</strong>: Saving privilege levels in browser storage</li>
<li><strong>JWT Tokens</strong>: Using unsigned or weakly signed tokens for authorization</li>
</ul>

            <h3>Why This Is Critically Dangerous</h3>

            <p>This vulnerability is particularly severe because:</p>

<ol>
<li><strong>Trivial to Exploit</strong>: Requires only browser developer tools to modify cookies</li>
<li><strong>Complete Privilege Escalation</strong>: Normal users can instantly become administrators</li>
<li><strong>No Technical Expertise Required</strong>: Any user can learn to modify cookies in minutes</li>
<li><strong>Bypasses Authentication</strong>: Even proper login doesn't protect against this</li>
<li><strong>Silent Attack</strong>: No logs or alerts are typically generated</li>
</ol>

            <hr>

            <h1>Step-by-Step Exploitation Walkthrough</h1>

            <h2>Prerequisites</h2>

            <p>Before starting this lab, ensure you have:</p>

<ul>
<li>XAMPP running with Apache and MySQL services</li>
<li>Lab deployed to <span class="inline-code">http://localhost/AC/lab3/</span></li>
<li>Web browser with Developer Tools (Chrome, Firefox, or Edge)</li>
<li>Basic understanding of browser cookies</li>
</ul>

            <h2>Discovery Phase</h2>

<div class="step">
<span class="step-number">1</span>
<strong>Access the Application</strong>

<p>Navigate to the lab URL:</p>

<div class="code-block">http://localhost/AC/lab3/</div>

<p>You'll see a simple interface with a login option. Notice that there's no visible admin panel link.</p>
</div>

<div class="step">
<span class="step-number">2</span>
<strong>Login with Provided Credentials</strong>

<p>Login using any of the available user accounts:</p>

<div class="code-block">Username: wiener
Password: password

Username: carlos
Password: password

Username: alice
Password: password</div>

<p>After logging in, you'll be redirected to your profile page.</p>
</div>

<div class="step">
<span class="step-number">3</span>
<strong>Examine Your Browser Cookies</strong>

<p>Open Developer Tools by pressing <span class="inline-code">F12</span>.</p>

<p>Navigate to:</p>
<ul>
<li><strong>Chrome</strong>: Application tab → Storage → Cookies</li>
<li><strong>Firefox</strong>: Storage tab → Cookies</li>
<li><strong>Edge</strong>: Application tab → Storage → Cookies</li>
</ul>

<p>Look for a cookie named <span class="inline-code">Admin</span> with value <span class="inline-code">false</span>.</p>
</div>

<div class="alert alert-info">
<strong>💡 Pro Tip</strong><br>
You can also view cookies by typing <span class="inline-code">document.cookie</span> in the browser console.
</div>

            <h2>Exploitation Phase</h2>

<div class="step">
<span class="step-number">4</span>
<strong>Modify the Admin Cookie</strong>

<p>In the Developer Tools:</p>

<ol>
<li>Find the <span class="inline-code">Admin</span> cookie</li>
<li>Double-click on its value (<span class="inline-code">false</span>)</li>
<li>Change it to <span class="inline-code">true</span></li>
<li>Press Enter to save the change</li>
</ol>

<div class="code-block">// Before modification:
Admin = false

// After modification:
Admin = true</div>
</div>

<div class="step">
<span class="step-number">5</span>
<strong>Access the Admin Panel</strong>

<p>Now navigate to the admin panel:</p>

<div class="code-block">http://localhost/AC/lab3/admin.php</div>

<p>You now have full administrative access! The application checks only the cookie value, not your actual role in the database.</p>
</div>

<div class="step">
<span class="step-number">6</span>
<strong>Complete the Lab Objective</strong>

<p>In the admin panel:</p>

<ol>
<li>Locate the user <strong>carlos</strong> in the user management table</li>
<li>Click the <span class="inline-code">Delete</span> button next to carlos</li>
<li>Confirm the deletion</li>
</ol>

<p>Congratulations! You've successfully exploited the vulnerability and deleted the target user.</p>
</div>

            <hr>

            <h1>Vulnerable Code Analysis</h1>

            <p>Let's examine what makes this application vulnerable:</p>

            <h2>Login.php - Setting the Vulnerable Cookie</h2>

<div class="code-block">&lt;?php
// VULNERABILITY: Setting admin status in a client-controllable cookie!
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    
    // DANGEROUS: Admin status stored in cookie that user can modify!
    $admin_status = ($user['role'] === 'admin') ? 'true' : 'false';
    setcookie('Admin', $admin_status, time() + 3600, '/');
    
    header('Location: profile.php');
    exit;
}
?&gt;</div>

            <h2>Admin.php - Trusting the Cookie</h2>

<div class="code-block">&lt;?php
// VULNERABILITY: Checking cookie instead of server-side session/database!
$is_admin = isset($_COOKIE['Admin']) && $_COOKIE['Admin'] === 'true';

if (!$is_admin) {
    $error_message = 'Access Denied: Admin privileges required.';
}

// Admin operations proceed based on cookie value alone
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user']) && $is_admin) {
    // Delete user - this should NEVER rely on cookie-based authorization!
    $user_id = $_POST['user_id'];
    $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->execute([$user_id]);
}
?&gt;</div>

            <h2>Critical Security Flaws Identified</h2>

<div class="alert alert-danger">
<strong>🚨 Critical Security Flaws</strong>

<ol>
<li><strong>Client-Side Trust</strong>: Application trusts cookie value set by user's browser</li>
<li><strong>No Server-Side Verification</strong>: Role is not verified against database</li>
<li><strong>Cookie Manipulation</strong>: Cookies can be easily modified with browser tools</li>
<li><strong>Session Mismatch</strong>: Session has role, but cookie is used for authorization</li>
<li><strong>No Cookie Signing</strong>: Cookie value is plain text without cryptographic protection</li>
<li><strong>No Integrity Check</strong>: No way to detect if cookie was tampered with</li>
</ol>
</div>

            <hr>

            <h1>Comprehensive Security Implementation</h1>

            <p>Here's how to properly secure this application:</p>

            <h2>Secure Authorization Implementation</h2>

<div class="code-block">&lt;?php
// SECURITY FIX #1: Use session-based authorization only
session_start();

// SECURITY FIX #2: Verify authentication first
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=authentication_required');
    exit;
}

// SECURITY FIX #3: Verify role from database, never from client input!
function isUserAdmin($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['role'] === 'admin';
}

// SECURITY FIX #4: Check authorization server-side
$is_admin = isUserAdmin($pdo, $_SESSION['user_id']);

if (!$is_admin) {
    error_log("Unauthorized admin access attempt by user ID: " . $_SESSION['user_id']);
    http_response_code(403);
    header('Location: unauthorized.php');
    exit;
}

// SECURITY FIX #5: Re-verify permissions for sensitive operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    // Double-check admin status before any destructive operation
    if (!isUserAdmin($pdo, $_SESSION['user_id'])) {
        die('Unauthorized operation');
    }
    
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF validation failed');
    }
    
    // Now safe to proceed with deletion
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($user_id) {
        $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->execute([$user_id]);
    }
}
?&gt;</div>

            <h2>Secure Cookie Handling (If Cookies Must Be Used)</h2>

<div class="code-block">&lt;?php
// SECURITY FIX #6: If you must use cookies for any purpose, sign them!
function createSecureCookie($name, $value, $secret_key) {
    $signature = hash_hmac('sha256', $value, $secret_key);
    $secure_value = base64_encode(json_encode([
        'value' => $value,
        'signature' => $signature,
        'timestamp' => time()
    ]));
    
    setcookie($name, $secure_value, [
        'expires' => time() + 3600,
        'path' => '/',
        'secure' => true,      // HTTPS only
        'httponly' => true,    // No JavaScript access
        'samesite' => 'Strict' // CSRF protection
    ]);
}

function verifySecureCookie($name, $secret_key) {
    if (!isset($_COOKIE[$name])) {
        return null;
    }
    
    $data = json_decode(base64_decode($_COOKIE[$name]), true);
    if (!$data || !isset($data['value']) || !isset($data['signature'])) {
        return null;
    }
    
    $expected_signature = hash_hmac('sha256', $data['value'], $secret_key);
    if (!hash_equals($expected_signature, $data['signature'])) {
        // Tampering detected!
        error_log("Cookie tampering detected for: " . $name);
        return null;
    }
    
    return $data['value'];
}

// IMPORTANT: Even with signed cookies, NEVER use them for authorization!
// Always verify roles against the database!
?&gt;</div>

            <hr>

            <h1>Security Best Practices Summary</h1>

<div class="alert alert-success">
<strong>✅ Authorization Security Checklist</strong>

<ul>
<li><strong>Never Trust Client Data</strong>: Cookies, headers, and form fields can all be manipulated</li>
<li><strong>Server-Side Authorization</strong>: Always verify permissions on the server</li>
<li><strong>Database Verification</strong>: Check user roles against authoritative data source</li>
<li><strong>Session-Based Control</strong>: Use secure server-side sessions for authorization</li>
<li><strong>Defense in Depth</strong>: Re-verify permissions before sensitive operations</li>
<li><strong>Audit Logging</strong>: Log all authorization decisions and failures</li>
</ul>
</div>

            <h2>Implementation Comparison</h2>

<table>
<tr>
<th>Approach</th>
<th>Security Level</th>
<th>Recommendation</th>
</tr>
<tr>
<td>Cookie-based role</td>
<td>❌ None</td>
<td>Never use for authorization</td>
</tr>
<tr>
<td>Hidden form field</td>
<td>❌ None</td>
<td>Never use for authorization</td>
</tr>
<tr>
<td>URL parameter</td>
<td>❌ None</td>
<td>Never use for authorization</td>
</tr>
<tr>
<td>Unsigned JWT</td>
<td>⚠️ Low</td>
<td>Must be properly signed</td>
</tr>
<tr>
<td>Session variable only</td>
<td>⚠️ Medium</td>
<td>Can get stale</td>
</tr>
<tr>
<td>Database verification</td>
<td>✅ High</td>
<td>Recommended approach</td>
</tr>
<tr>
<td>Combined session + DB</td>
<td>✅ Highest</td>
<td>Best practice</td>
</tr>
</table>

            <hr>

            <h1>Conclusion</h1>

            <p>This lab demonstrates the critical importance of <strong>never trusting client-side data for authorization decisions</strong>.</p>

            <p>The vulnerability exploited here is surprisingly common in real-world applications, where developers sometimes take shortcuts by storing role information in easily accessible locations.</p>

            <h2>Key Takeaways</h2>

<ol>
<li><strong>Client data is untrusted</strong>: Anything the browser sends can be modified</li>
<li><strong>Authorization must be server-side</strong>: Database or secure session only</li>
<li><strong>Cookies are not secure</strong>: Never use them for authorization decisions</li>
<li><strong>Defense in depth</strong>: Multiple checks for sensitive operations</li>
<li><strong>Regular security audits</strong>: Review authorization logic frequently</li>
</ol>

<blockquote>
"The fundamental rule of web security: never trust user input. This includes cookies, which are just user input wearing a disguise."
<br><em>- Security Best Practices</em>
</blockquote>

        </div>

        <div class="nav-buttons">
            <a href="lab-description.php" class="btn btn-secondary">← Lab Description</a>
            <a href="index.php" class="btn btn-secondary">Lab Home →</a>
            <a href="../index.php" class="btn btn-primary">← Back to Labs</a>
        </div>
    </div>
</body>
</html>