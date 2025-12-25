<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Cookie Role Manipulation</title>
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
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-nav:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .layout {
            display: flex;
            margin-top: 60px;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.02);
            border-right: 1px solid rgba(255, 68, 68, 0.1);
            padding: 2rem 1rem;
            position: fixed;
            top: 60px;
            bottom: 0;
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #ff4444;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        .sidebar-nav {
            list-style: none;
        }
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
            background: rgba(255, 68, 68, 0.1);
            color: #ff4444;
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 3rem;
            max-width: calc(100% - 280px);
        }
        .doc-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.1);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .doc-section h1 {
            color: #ff4444;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .doc-section h2 {
            color: #ff6666;
            font-size: 1.5rem;
            margin: 2rem 0 1rem 0;
        }
        .doc-section h3 {
            color: #ff8888;
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
        .doc-section li {
            margin-bottom: 0.5rem;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            color: #ff6666;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }
        .code-inline {
            background: rgba(255, 68, 68, 0.1);
            color: #ff6666;
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
            background: rgba(68, 68, 255, 0.1);
            border: 1px solid rgba(68, 68, 255, 0.3);
        }
        .alert-info h4 { color: #6666ff; margin-bottom: 0.5rem; }
        .alert-info p { color: #a0a0ff; margin: 0; }
        .alert-warning {
            background: rgba(255, 200, 68, 0.1);
            border: 1px solid rgba(255, 200, 68, 0.3);
        }
        .alert-warning h4 { color: #ffcc44; margin-bottom: 0.5rem; }
        .alert-warning p { color: #ffdd88; margin: 0; }
        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .alert-danger h4 { color: #ff4444; margin-bottom: 0.5rem; }
        .alert-danger p { color: #ff8888; margin: 0; }
        .alert-success {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
        }
        .alert-success h4 { color: #44ff44; margin-bottom: 0.5rem; }
        .alert-success p { color: #88ff88; margin: 0; }
        .table-container {
            overflow-x: auto;
            margin: 1rem 0;
        }
        .doc-table {
            width: 100%;
            border-collapse: collapse;
        }
        .doc-table th, .doc-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 68, 68, 0.1);
        }
        .doc-table th {
            background: rgba(255, 68, 68, 0.1);
            color: #ff6666;
            font-weight: 600;
        }
        .doc-table td {
            color: #b0b0b0;
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.8rem;
            height: 1.8rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🍪 Lab 3</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
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
                <li><a href="#overview" class="active">Overview</a></li>
                <li><a href="#vulnerability">Vulnerability</a></li>
                <li><a href="#exploitation">Exploitation</a></li>
                <li><a href="#step-by-step">Step-by-Step Guide</a></li>
                <li><a href="#code-analysis">Code Analysis</a></li>
                <li><a href="#prevention">Prevention</a></li>
                <li><a href="#references">References</a></li>
            </ul>
            <h3 style="margin-top: 2rem;">Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="index.php">🏠 Lab Home</a></li>
                <li><a href="login.php">🔐 Login Page</a></li>
                <li><a href="lab-description.php">📋 Lab Description</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <section id="overview" class="doc-section">
                <h1>🍪 User Role Controlled by Cookie</h1>
                <p>
                    This lab demonstrates a critical vulnerability where user roles are stored in a 
                    client-side cookie that can be easily manipulated. By changing the cookie value, 
                    a regular user can escalate their privileges to admin.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Modify the Admin cookie to gain administrative access and delete user "carlos".</p>
                </div>

                <h2>Cookie-Based Access Control</h2>
                <p>
                    Some applications store authorization data in cookies on the client-side. This is 
                    fundamentally insecure because users can modify their own cookies. Sensitive data 
                    like roles, permissions, or user IDs should always be stored server-side.
                </p>

                <h2>Lab Credentials</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="code-inline">wiener</span></td>
                                <td><span class="code-inline">peter</span></td>
                                <td>Regular User</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>Client-Side Role Storage</h2>
                <p>
                    The application stores the user's administrative status in a cookie called 
                    <span class="code-inline">Admin</span>. After logging in as a regular user, 
                    this cookie is set to <span class="code-inline">false</span>.
                </p>

                <h2>The Vulnerable Cookie</h2>
                <div class="code-block">
                    <pre># Cookie set after login
Admin=false

# What the application checks
if ($_COOKIE['Admin'] === 'true') {
    // Grant admin access
}</pre>
                </div>

                <h2>Why This Is Dangerous</h2>
                <ul>
                    <li><strong>Client-side storage</strong> - Users can modify their own cookies</li>
                    <li><strong>No cryptographic protection</strong> - Cookie value is plain text</li>
                    <li><strong>No server-side verification</strong> - Application trusts the cookie blindly</li>
                    <li><strong>Boolean flag</strong> - Simple true/false makes it easy to exploit</li>
                </ul>

                <div class="alert alert-warning">
                    <h4>⚠️ Never Trust Client Data</h4>
                    <p>Authorization decisions should ALWAYS be made server-side based on session data.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener:peter
2. Observe Admin=false cookie
3. Change cookie value to Admin=true
4. Refresh the page
5. Access admin panel
6. Delete user "carlos"</pre>
                </div>

                <h2>Cookie Modification Methods</h2>
                
                <h3>Method 1: Browser Developer Tools</h3>
                <div class="code-block">
                    <pre>1. Press F12 to open DevTools
2. Go to Application → Cookies
3. Find the "Admin" cookie
4. Double-click the value "false"
5. Change it to "true"
6. Refresh the page</pre>
                </div>

                <h3>Method 2: Browser Console</h3>
                <div class="code-block">
                    <pre>// In browser console (F12 → Console)
document.cookie = "Admin=true; path=/";</pre>
                </div>

                <h3>Method 3: Using Burp Suite</h3>
                <div class="code-block">
                    <pre>1. Intercept the request
2. Find: Cookie: Admin=false
3. Change to: Cookie: Admin=true
4. Forward the request</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚫 Instant Privilege Escalation</h4>
                    <p>A single cookie change grants full administrative access to the application.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login to the application</strong></p>
                <p>Use credentials <span class="code-inline">wiener:peter</span> to login.</p>

                <p><span class="step-number">2</span><strong>Open Developer Tools</strong></p>
                <p>Press <span class="code-inline">F12</span> or right-click → Inspect Element</p>

                <p><span class="step-number">3</span><strong>Navigate to Cookies</strong></p>
                <p>Go to <span class="code-inline">Application</span> tab → <span class="code-inline">Cookies</span> → Select the domain</p>

                <p><span class="step-number">4</span><strong>Find the Admin cookie</strong></p>
                <p>Locate the cookie named <span class="code-inline">Admin</span> with value <span class="code-inline">false</span></p>

                <p><span class="step-number">5</span><strong>Modify the cookie</strong></p>
                <p>Double-click the value and change it from <span class="code-inline">false</span> to <span class="code-inline">true</span></p>

                <p><span class="step-number">6</span><strong>Refresh and access admin</strong></p>
                <p>Refresh the page. You should now see the Admin Panel link in the navigation.</p>

                <p><span class="step-number">7</span><strong>Delete carlos</strong></p>
                <p>Go to the Admin Panel and delete user "carlos".</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you escalate to admin via cookie manipulation and delete "carlos".</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: Trusting client-side cookie for authorization
$isAdmin = isset($_COOKIE['Admin']) && $_COOKIE['Admin'] === 'true';

if ($isAdmin) {
    // Show admin panel link
    echo '&lt;a href="admin.php"&gt;Admin Panel&lt;/a&gt;';
}
?&gt;</pre>
                </div>

                <h2>Vulnerable Login Code</h2>
                <div class="code-block">
                    <pre>&lt;?php
// On successful login
setcookie('Admin', 'false', time() + 3600, '/');  // BAD!

// Should be:
$_SESSION['role'] = $user['role'];  // Store in session instead
?&gt;</pre>
                </div>

                <h2>Security Issues</h2>
                <ul>
                    <li><strong>Client-side authorization</strong> - Cookies are user-controllable</li>
                    <li><strong>No encryption</strong> - Plain text cookie values</li>
                    <li><strong>No signature</strong> - No way to detect tampering</li>
                    <li><strong>No server verification</strong> - Cookie is trusted implicitly</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
session_start();

// SECURE: Store role in server-side session
$_SESSION['role'] = $user['role'];  // From database

// Check authorization server-side
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Use in code
if (isAdmin()) {
    echo '&lt;a href="admin.php"&gt;Admin Panel&lt;/a&gt;';
}
?&gt;</pre>
                </div>

                <h2>Best Practices</h2>
                <ul>
                    <li><strong>Server-side sessions</strong> - Store authorization data in sessions</li>
                    <li><strong>Database verification</strong> - Always verify roles from database</li>
                    <li><strong>Signed cookies</strong> - If using cookies, sign them cryptographically</li>
                    <li><strong>HttpOnly flag</strong> - Prevent JavaScript access to sensitive cookies</li>
                    <li><strong>Secure flag</strong> - Only send cookies over HTTPS</li>
                </ul>

                <h2>If You Must Use Cookies</h2>
                <div class="code-block">
                    <pre>&lt;?php
// Use signed/encrypted cookies if absolutely necessary
$secretKey = 'your-secret-key';
$role = 'admin';
$signature = hash_hmac('sha256', $role, $secretKey);
$cookieValue = base64_encode($role . '.' . $signature);

setcookie('role', $cookieValue, [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
?&gt;</pre>
                </div>

                <div class="alert alert-info">
                    <h4>💡 Golden Rule</h4>
                    <p>Never trust client-supplied data for authorization decisions. Always verify server-side.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" style="color: #ff4444;">OWASP Top 10 - Broken Access Control</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/565.html" style="color: #ff4444;">CWE-565: Reliance on Cookies without Validation</a></li>
                    <li><a href="https://owasp.org/www-community/controls/SecureCookieAttribute" style="color: #ff4444;">OWASP Secure Cookie Attributes</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html" style="color: #ff4444;">OWASP Session Management Cheat Sheet</a></li>
                    <li><a href="https://portswigger.net/web-security/access-control" style="color: #ff4444;">PortSwigger - Access Control Vulnerabilities</a></li>
                </ul>
            </section>
        </main>
    </div>

    <script>
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                    document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('.doc-section');
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>