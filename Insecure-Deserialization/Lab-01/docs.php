<?php
require_once 'config.php';
$session = getSessionFromCookie();
$isLoggedIn = ($session !== null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Modifying Serialized Objects</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(249, 115, 22, 0.3);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.5rem; font-weight: bold; color: #f97316; text-decoration: none; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #f97316; }
        .btn-nav {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(249, 115, 22, 0.3); color: #e0e0e0;
            text-decoration: none; border-radius: 6px; transition: all 0.3s;
        }
        .btn-nav:hover { background: rgba(249, 115, 22, 0.2); border-color: #f97316; color: #f97316; }
        .layout { display: flex; margin-top: 60px; }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.02);
            border-right: 1px solid rgba(249, 115, 22, 0.1);
            padding: 2rem 1rem;
            position: fixed;
            top: 60px;
            bottom: 0;
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #f97316;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li a {
            display: block;
            padding: 0.7rem 1rem;
            color: #888;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .sidebar-nav li a:hover,
        .sidebar-nav li a.active {
            background: rgba(249, 115, 22, 0.1);
            color: #f97316;
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 3rem;
            max-width: calc(100% - 280px);
        }
        .doc-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(249, 115, 22, 0.1);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .doc-section h1 {
            color: #f97316;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(249, 115, 22, 0.2);
        }
        .doc-section h2 {
            color: #fb923c;
            font-size: 1.5rem;
            margin: 2rem 0 1rem 0;
        }
        .doc-section h3 {
            color: #fdba74;
            font-size: 1.2rem;
            margin: 1.5rem 0 1rem 0;
        }
        .doc-section p {
            color: #b0b0b0;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .doc-section ul, .doc-section ol {
            color: #b0b0b0;
            line-height: 1.8;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .doc-section li { margin-bottom: 0.5rem; }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(249, 115, 22, 0.2);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            color: #fb923c;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }
        .code-inline {
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .alert {
            padding: 1.2rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .alert-info h4 { color: #60a5fa; margin-bottom: 0.5rem; }
        .alert-info p { color: #93c5fd; margin: 0; }
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .alert-warning h4 { color: #fbbf24; margin-bottom: 0.5rem; }
        .alert-warning p { color: #fcd34d; margin: 0; }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .alert-danger h4 { color: #f87171; margin-bottom: 0.5rem; }
        .alert-danger p { color: #fca5a5; margin: 0; }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .alert-success h4 { color: #4ade80; margin-bottom: 0.5rem; }
        .alert-success p { color: #86efac; margin: 0; }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .comparison-table th, .comparison-table td {
            padding: 1rem;
            border: 1px solid rgba(249, 115, 22, 0.2);
            text-align: left;
        }
        .comparison-table th {
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
        }
        .comparison-table td { color: #b0b0b0; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📦 SerialLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
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

    <div class="layout">
        <aside class="sidebar">
            <h3>Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="#overview" class="active">Lab Overview</a></li>
                <li><a href="#walkthrough">Step-by-Step Walkthrough</a></li>
                <li><a href="#why-works">Why The Exploit Works</a></li>
                <li><a href="#vulnerable-code">Vulnerable Code Analysis</a></li>
                <li><a href="#secure-code">Secure Implementation</a></li>
                <li><a href="#comparison">Code Comparison</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <section id="overview" class="doc-section">
                <h1>Lab 1: Modifying Serialized Objects</h1>
                
                <h2>Lab Overview</h2>
                <p>This lab demonstrates an <strong>insecure deserialization vulnerability</strong> that allows privilege escalation through cookie manipulation. The application uses PHP serialization to store session data in a client-side cookie, which can be modified by an attacker.</p>

                <div class="alert alert-info">
                    <h4>💡 What is Insecure Deserialization?</h4>
                    <p>Insecure deserialization occurs when an application deserializes data from an untrusted source without proper validation. Attackers can manipulate the serialized data to achieve malicious goals like privilege escalation, remote code execution, or data tampering.</p>
                </div>

                <h3>Attack Surface</h3>
                <ul>
                    <li><strong>Entry Point:</strong> Session cookie containing serialized PHP object</li>
                    <li><strong>Vulnerability:</strong> Unvalidated deserialization of user-controlled data</li>
                    <li><strong>Impact:</strong> Privilege escalation from regular user to administrator</li>
                    <li><strong>Exploitation:</strong> Modify the <code>admin</code> attribute in the serialized object</li>
                </ul>

                <h3>Backend Logic</h3>
                <p>The application works as follows:</p>
                <ol>
                    <li>User logs in with valid credentials</li>
                    <li>Server creates a PHP object with user data including an <code>admin</code> flag</li>
                    <li>Object is serialized, Base64-encoded, and URL-encoded</li>
                    <li>Encoded data is stored in a <code>session</code> cookie</li>
                    <li>On each request, the cookie is decoded and deserialized</li>
                    <li>Authorization decisions are made based on the deserialized <code>admin</code> property</li>
                </ol>
            </section>

            <section id="walkthrough" class="doc-section">
                <h2>Step-by-Step Walkthrough</h2>

                <h3>Step 1: Login and Capture the Cookie</h3>
                <p>Login using the provided credentials: <code class="code-inline">wiener:peter</code></p>
                <p>After login, examine the cookies in your browser's Developer Tools or intercept the request with Burp Suite.</p>
                <div class="code-block">
                    <pre>Cookie: session=TzozMDoic3RkQ2xhc3MiOjM6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjU6ImFkbWluIjtiOjA7czo3OiJ1c2VyX2lkIjtpOjM7fQ%3D%3D</pre>
                </div>

                <h3>Step 2: Decode the Cookie Value</h3>
                <p>First, URL-decode the cookie value:</p>
                <div class="code-block">
                    <pre>TzozMDoic3RkQ2xhc3MiOjM6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjU6ImFkbWluIjtiOjA7czo3OiJ1c2VyX2lkIjtpOjM7fQ==</pre>
                </div>
                <p>Then, Base64-decode to reveal the serialized PHP object:</p>
                <div class="code-block">
                    <pre>O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:0;s:7:"user_id";i:3;}</pre>
                </div>

                <h3>Step 3: Analyze the Serialized Object</h3>
                <p>Understanding the format:</p>
                <ul>
                    <li><code>O:8:"stdClass"</code> - Object of class "stdClass" (8 characters)</li>
                    <li><code>:3:</code> - The object has 3 properties</li>
                    <li><code>s:8:"username"</code> - String property named "username" (8 chars)</li>
                    <li><code>s:6:"wiener"</code> - String value "wiener" (6 chars)</li>
                    <li><code>s:5:"admin"</code> - String property named "admin" (5 chars)</li>
                    <li><code>b:0</code> - Boolean value false (0 = false, 1 = true)</li>
                    <li><code>s:7:"user_id"</code> - String property named "user_id"</li>
                    <li><code>i:3</code> - Integer value 3</li>
                </ul>

                <h3>Step 4: Modify the Admin Attribute</h3>
                <p>Change <code>b:0</code> to <code>b:1</code> to set admin to true:</p>
                <div class="code-block">
                    <pre>O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:1;s:7:"user_id";i:3;}</pre>
                </div>

                <h3>Step 5: Re-encode the Modified Object</h3>
                <p>Base64-encode the modified object:</p>
                <div class="code-block">
                    <pre>Tzo4OiJzdGRDbGFzcyI6Mzp7czo4OiJ1c2VybmFtZSI7czo2OiJ3aWVuZXIiO3M6NToiYWRtaW4iO2I6MTtzOjc6InVzZXJfaWQiO2k6Mzt9</pre>
                </div>
                <p>URL-encode if needed (usually automatic).</p>

                <h3>Step 6: Replace the Cookie and Access Admin Panel</h3>
                <p>Replace the session cookie with the modified value. Navigate to <code>/my-account.php</code> - you should now see the Admin Panel link. Access <code>/admin.php</code> and delete user carlos.</p>

                <div class="alert alert-success">
                    <h4>✅ Lab Completed!</h4>
                    <p>You have successfully exploited the insecure deserialization vulnerability to gain admin access and delete the target user.</p>
                </div>
            </section>

            <section id="why-works" class="doc-section">
                <h2>Why The Exploit Works</h2>

                <h3>Root Cause Analysis</h3>
                <p>The vulnerability exists because of several critical security flaws:</p>

                <ol>
                    <li><strong>Client-Side Session Storage:</strong> Session data containing authorization flags is stored in a cookie that the user can modify.</li>
                    <li><strong>Lack of Integrity Protection:</strong> The serialized data is only encoded (Base64), not signed or encrypted. There's no HMAC or digital signature to detect tampering.</li>
                    <li><strong>Trusting Deserialized Data:</strong> The server directly trusts the <code>admin</code> property from the deserialized object without verifying it against the database.</li>
                    <li><strong>No Server-Side Validation:</strong> Authorization checks use the cookie data instead of querying the user's actual role from the database.</li>
                </ol>

                <div class="alert alert-danger">
                    <h4>⚠️ Security Anti-Pattern</h4>
                    <p>Never store authorization data in client-side storage without cryptographic integrity protection. Client-provided data should always be verified against a trusted server-side source.</p>
                </div>
            </section>

            <section id="vulnerable-code" class="doc-section">
                <h2>Vulnerable Code Analysis</h2>

                <h3>Creating the Vulnerable Session Cookie</h3>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: Session data stored in client-side cookie
function createSerializedSession($user) {
    $sessionData = new stdClass();
    $sessionData->username = $user['username'];
    
    // The admin flag is set based on database role
    // but stored in client-controllable cookie
    $sessionData->admin = ($user['role'] === 'admin') ? true : false;
    $sessionData->user_id = $user['id'];
    
    // Serialize and encode - NO SIGNING OR ENCRYPTION
    $serialized = serialize($sessionData);
    $encoded = base64_encode($serialized);
    return urlencode($encoded);
}</pre>
                </div>
                <p><strong>Problem:</strong> The session data including authorization flags is stored client-side without any integrity protection.</p>

                <h3>Deserializing Without Validation</h3>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: Directly deserializes user-controlled data
function getSessionFromCookie() {
    if (!isset($_COOKIE['session'])) {
        return null;
    }
    
    $decoded = urldecode($_COOKIE['session']);
    $unserialized = base64_decode($decoded);
    
    // DANGEROUS: unserialize() on user-controlled data
    $sessionData = @unserialize($unserialized);
    
    return $sessionData;
}</pre>
                </div>
                <p><strong>Problem:</strong> The cookie value is deserialized directly without any validation or integrity checking.</p>

                <h3>Trusting Deserialized Admin Flag</h3>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: Trusts the 'admin' property from cookie
function isAdmin() {
    $session = getSessionFromCookie();
    if (!$session) {
        return false;
    }
    
    // DANGEROUS: Directly trusts client-provided data
    return isset($session->admin) && $session->admin === true;
}</pre>
                </div>
                <p><strong>Problem:</strong> Authorization is based entirely on the deserialized cookie data, which the attacker controls.</p>
            </section>

            <section id="secure-code" class="doc-section">
                <h2>Secure Implementation</h2>

                <h3>Option 1: Server-Side Sessions (Recommended)</h3>
                <div class="code-block">
                    <pre>&lt;?php
// SECURE: Use PHP's built-in session handling
session_start();

function secureLogin($user) {
    // Store only the session ID client-side
    // Actual data is stored server-side
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    // DO NOT store role in session - always query from DB
}

function isAdmin() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // ALWAYS check the database for current role
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    return $user && $user['role'] === 'admin';
}</pre>
                </div>

                <h3>Option 2: Signed Tokens (JWT-style)</h3>
                <div class="code-block">
                    <pre>&lt;?php
// SECURE: Use HMAC to sign the session data
define('SECRET_KEY', 'your-secret-key-here'); // Store securely!

function createSecureSession($user) {
    $data = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'exp' => time() + 3600 // Expiration time
    ];
    
    $payload = base64_encode(json_encode($data));
    $signature = hash_hmac('sha256', $payload, SECRET_KEY);
    
    return $payload . '.' . $signature;
}

function verifySecureSession($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 2) return null;
    
    list($payload, $signature) = $parts;
    
    // Verify signature
    $expectedSignature = hash_hmac('sha256', $payload, SECRET_KEY);
    if (!hash_equals($expectedSignature, $signature)) {
        return null; // Tampered!
    }
    
    $data = json_decode(base64_decode($payload), true);
    
    // Check expiration
    if ($data['exp'] < time()) {
        return null;
    }
    
    return $data;
}

function isAdmin() {
    $session = verifySecureSession($_COOKIE['session'] ?? '');
    if (!$session) return false;
    
    // STILL query DB for role - never trust token for authorization
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$session['user_id']]);
    $user = $stmt->fetch();
    
    return $user && $user['role'] === 'admin';
}</pre>
                </div>
            </section>

            <section id="comparison" class="doc-section">
                <h2>Code Comparison</h2>

                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Aspect</th>
                            <th>Vulnerable Code</th>
                            <th>Secure Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Session Storage</td>
                            <td>Client-side cookie with serialized PHP object</td>
                            <td>Server-side session or signed JWT</td>
                        </tr>
                        <tr>
                            <td>Data Integrity</td>
                            <td>No protection (Base64 only)</td>
                            <td>HMAC signature or server-side storage</td>
                        </tr>
                        <tr>
                            <td>Authorization Check</td>
                            <td>Trusts cookie's <code>admin</code> flag</td>
                            <td>Always queries database for current role</td>
                        </tr>
                        <tr>
                            <td>Deserialization</td>
                            <td>Direct <code>unserialize()</code> on user input</td>
                            <td>JSON decode or avoid serialization entirely</td>
                        </tr>
                        <tr>
                            <td>Attack Surface</td>
                            <td>User can modify any session attribute</td>
                            <td>Modifications are detected and rejected</td>
                        </tr>
                    </tbody>
                </table>

                <div class="alert alert-warning">
                    <h4>🔑 Key Takeaways</h4>
                    <p>1. Never store authorization data in client-side storage without cryptographic protection.<br>
                    2. Always verify authorization claims against a trusted server-side source.<br>
                    3. Use PHP's built-in session handling instead of custom cookie-based solutions.<br>
                    4. If using tokens, ensure they are properly signed and validated.</p>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Smooth scroll for sidebar links
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
                document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
