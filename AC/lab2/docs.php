<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Unpredictable Admin URL</title>
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
            <a href="index.php" class="logo">🔍 Lab 2</a>
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
                <h1>🔍 Unpredictable Admin URL Disclosure</h1>
                <p>
                    This lab demonstrates how "security through obscurity" fails when sensitive URLs 
                    are inadvertently exposed through client-side code. The admin panel uses an 
                    unpredictable URL but the path is leaked in JavaScript.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Find the admin panel URL hidden in client-side code and delete user "carlos".</p>
                </div>

                <h2>Information Disclosure in JavaScript</h2>
                <p>
                    Many developers believe using random or unpredictable URLs provides adequate security. 
                    However, these URLs are often exposed through client-side configuration objects, 
                    JavaScript code, or HTML comments.
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
                            <tr>
                                <td><span class="code-inline">carlos</span></td>
                                <td><span class="code-inline">carlos123</span></td>
                                <td>Regular User (Target)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>Client-Side Information Disclosure</h2>
                <p>
                    The application exposes sensitive endpoint information in client-side JavaScript code. 
                    This is a common mistake when developers hardcode API endpoints or admin URLs in 
                    frontend configuration objects.
                </p>

                <h2>The Leaked Configuration</h2>
                <div class="code-block">
                    <pre>// Found in page source
const config = {
    apiEndpoints: {
        users: '/api/users',
        products: '/api/products',
        admin: '/admin-panel-x7k9p2m5q8w1.php'  // SECRET LEAKED!
    },
    environment: 'production',
    debugMode: false
};</pre>
                </div>

                <h2>Debug Functions Left in Production</h2>
                <div class="code-block">
                    <pre>// Console helper functions (should be removed!)
function quickAdminAccess() {
    window.location.href = '/admin-panel-x7k9p2m5q8w1.php';
}

function getAdminPanelUrl() {
    return '/admin-panel-x7k9p2m5q8w1.php';
}</pre>
                </div>

                <div class="alert alert-warning">
                    <h4>⚠️ Common Mistake</h4>
                    <p>Debug code and configuration objects are often left in production, exposing sensitive information.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. View page source (Ctrl+U)
2. Search for "admin" in the source
3. Find the config object with admin URL
4. Navigate to /admin-panel-x7k9p2m5q8w1.php
5. Delete user "carlos"</pre>
                </div>

                <h2>Discovery Methods</h2>
                
                <h3>Method 1: View Source</h3>
                <p>Right-click → View Page Source → Search for "admin"</p>

                <h3>Method 2: Browser Console</h3>
                <div class="code-block">
                    <pre>// In browser console (F12 → Console)
quickAdminAccess()    // Direct redirect
getAdminPanelUrl()    // Returns the URL</pre>
                </div>

                <h3>Method 3: Developer Tools</h3>
                <p>F12 → Sources → Search through JavaScript files</p>

                <div class="alert alert-danger">
                    <h4>🚫 Still No Authentication</h4>
                    <p>Even though the URL is "unpredictable", the admin panel has no authentication checks.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Access the application</strong></p>
                <p>Navigate to <span class="code-inline">http://localhost/AC/lab2/</span></p>

                <p><span class="step-number">2</span><strong>View the page source</strong></p>
                <p>Right-click and select "View Page Source" or press <span class="code-inline">Ctrl+U</span></p>

                <p><span class="step-number">3</span><strong>Search for admin references</strong></p>
                <p>Use <span class="code-inline">Ctrl+F</span> to search for "admin" in the source code</p>

                <p><span class="step-number">4</span><strong>Find the config object</strong></p>
                <p>Locate the JavaScript config object containing <span class="code-inline">admin-panel-x7k9p2m5q8w1.php</span></p>

                <p><span class="step-number">5</span><strong>Access the admin panel</strong></p>
                <p>Navigate to <span class="code-inline">http://localhost/AC/lab2/admin-panel-x7k9p2m5q8w1.php</span></p>

                <p><span class="step-number">6</span><strong>Delete carlos</strong></p>
                <p>Find and delete the user "carlos" from the admin panel.</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you discover the hidden admin URL and delete "carlos".</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Pattern: Exposed Config</h2>
                <div class="code-block">
                    <pre>&lt;script&gt;
// BAD: Sensitive URLs in client-side code
const config = {
    adminUrl: '/admin-panel-x7k9p2m5q8w1.php',
    apiKey: 'secret-api-key-12345'
};
&lt;/script&gt;</pre>
                </div>

                <h2>Vulnerable Pattern: Debug Functions</h2>
                <div class="code-block">
                    <pre>&lt;script&gt;
// BAD: Debug helpers left in production
function devLogin() {
    // Instant admin access for testing
}
&lt;/script&gt;</pre>
                </div>

                <h2>Information Disclosure Vectors</h2>
                <ul>
                    <li><strong>JavaScript config objects</strong> - API endpoints, admin URLs</li>
                    <li><strong>HTML comments</strong> - Internal notes and paths</li>
                    <li><strong>Source maps</strong> - Original source code structure</li>
                    <li><strong>Error messages</strong> - File paths and stack traces</li>
                    <li><strong>Debug endpoints</strong> - Testing functions left active</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Practices</h2>
                <div class="code-block">
                    <pre>&lt;?php
// 1. ALWAYS authenticate admin panel
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Access denied');
}

// 2. Don't rely on URL obscurity
// Even if URL is random, always check permissions
?&gt;</pre>
                </div>

                <h2>Build Process Best Practices</h2>
                <ul>
                    <li><strong>Remove debug code</strong> - Use build tools to strip development code</li>
                    <li><strong>Environment configs</strong> - Keep sensitive URLs server-side only</li>
                    <li><strong>Code review</strong> - Check for exposed secrets before deployment</li>
                    <li><strong>Source maps</strong> - Disable in production builds</li>
                    <li><strong>Automated scanning</strong> - Use tools to detect leaked secrets</li>
                </ul>

                <div class="alert alert-info">
                    <h4>💡 Defense in Depth</h4>
                    <p>Even if a URL is discovered, proper authentication should prevent unauthorized access.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" style="color: #ff4444;">OWASP Top 10 - Broken Access Control</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/200.html" style="color: #ff4444;">CWE-200: Exposure of Sensitive Information</a></li>
                    <li><a href="https://owasp.org/www-project-web-security-testing-guide/" style="color: #ff4444;">OWASP Testing Guide</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html" style="color: #ff4444;">OWASP Authentication Cheat Sheet</a></li>
                    <li><a href="https://portswigger.net/web-security/information-disclosure" style="color: #ff4444;">PortSwigger - Information Disclosure</a></li>
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