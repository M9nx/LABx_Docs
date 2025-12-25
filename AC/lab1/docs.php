<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Unprotected Admin Functionality</title>
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
            <a href="index.php" class="logo">🔓 Lab 1</a>
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
                <li><a href="robots.txt">🤖 robots.txt</a></li>
                <li><a href="lab-description.php">📋 Lab Description</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <section id="overview" class="doc-section">
                <h1>🔓 Unprotected Admin Functionality</h1>
                <p>
                    This lab demonstrates a critical access control vulnerability where an administrative panel 
                    is left completely unprotected and accessible to anyone who knows the URL. The admin panel 
                    location is inadvertently disclosed in the robots.txt file.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Find the unprotected admin panel and delete the user "carlos" to complete the lab.</p>
                </div>

                <h2>What is Broken Access Control?</h2>
                <p>
                    Broken Access Control occurs when applications fail to properly restrict what authenticated 
                    users are allowed to do. This vulnerability allows attackers to access unauthorized 
                    functionality or data by manipulating URLs, bypassing access control checks, or discovering 
                    hidden endpoints.
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
                
                <h2>Security Through Obscurity</h2>
                <p>
                    This lab relies on "security through obscurity" - the mistaken belief that hiding 
                    something makes it secure. The admin panel URL is simply not linked in the navigation, 
                    but remains fully accessible to anyone who discovers it.
                </p>

                <h2>Information Disclosure via robots.txt</h2>
                <p>
                    The <span class="code-inline">robots.txt</span> file is intended to tell search engine 
                    crawlers which paths they should not index. However, listing sensitive paths in this 
                    file actually discloses them to attackers.
                </p>

                <div class="code-block">
                    <pre># robots.txt contents
User-agent: *
Disallow: /administrator-panel
Disallow: /admin
Disallow: /backup
Disallow: /logs
Disallow: /private</pre>
                </div>

                <div class="alert alert-warning">
                    <h4>⚠️ The Irony</h4>
                    <p>The robots.txt file meant to hide the admin panel actually helps attackers find it!</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Check robots.txt → Find /administrator-panel
2. Access /administrator-panel.php directly
3. No authentication required!
4. Delete user "carlos"
5. Lab completed</pre>
                </div>

                <h2>Discovery Method</h2>
                <p>Simply navigate to the robots.txt file:</p>
                <div class="code-block">
                    <pre>http://localhost/AC/lab1/robots.txt</pre>
                </div>

                <p>Then access the disclosed admin panel:</p>
                <div class="code-block">
                    <pre>http://localhost/AC/lab1/administrator-panel.php</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚫 No Authentication</h4>
                    <p>The admin panel has zero authentication checks. Any visitor can access full admin functionality.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Navigate to the lab homepage</strong></p>
                <p>Access <span class="code-inline">http://localhost/AC/lab1/</span> and explore the application.</p>

                <p><span class="step-number">2</span><strong>Check robots.txt</strong></p>
                <p>Navigate to <span class="code-inline">http://localhost/AC/lab1/robots.txt</span></p>

                <p><span class="step-number">3</span><strong>Identify the admin panel path</strong></p>
                <p>Find the <span class="code-inline">Disallow: /administrator-panel</span> entry.</p>

                <p><span class="step-number">4</span><strong>Access the admin panel</strong></p>
                <p>Navigate to <span class="code-inline">http://localhost/AC/lab1/administrator-panel.php</span></p>

                <p><span class="step-number">5</span><strong>Delete the target user</strong></p>
                <p>Find user "carlos" in the list and click the Delete button.</p>

                <p><span class="step-number">6</span><strong>Verify completion</strong></p>
                <p>The lab is solved when carlos is successfully deleted.</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you successfully delete the user "carlos" from the admin panel.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// administrator-panel.php
// VULNERABLE: No authentication or authorization checks!

require_once 'config.php';

// Directly processes admin functions without any security
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    // User deleted - no verification of requester's identity!
}
?&gt;</pre>
                </div>

                <h2>What's Missing</h2>
                <ul>
                    <li><strong>Session verification</strong> - No check if user is logged in</li>
                    <li><strong>Role verification</strong> - No check if user is an administrator</li>
                    <li><strong>CSRF protection</strong> - No tokens to prevent cross-site attacks</li>
                    <li><strong>Audit logging</strong> - No record of who performed the action</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verify admin role
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("Access denied: Admin privileges required");
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid request");
}

// Now safe to process admin actions
?&gt;</pre>
                </div>

                <h2>Best Practices</h2>
                <ul>
                    <li><strong>Always authenticate</strong> - Verify user identity on every request</li>
                    <li><strong>Always authorize</strong> - Check permissions before allowing actions</li>
                    <li><strong>Don't use robots.txt for security</strong> - It's public information</li>
                    <li><strong>Implement defense in depth</strong> - Multiple security layers</li>
                    <li><strong>Use unpredictable URLs</strong> - But still implement proper authentication</li>
                </ul>

                <div class="alert alert-info">
                    <h4>💡 Remember</h4>
                    <p>Security through obscurity is not security at all. Always implement proper access controls.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" style="color: #ff4444;">OWASP Top 10 - Broken Access Control</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/285.html" style="color: #ff4444;">CWE-285: Improper Authorization</a></li>
                    <li><a href="https://portswigger.net/web-security/access-control" style="color: #ff4444;">PortSwigger - Access Control</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" style="color: #ff4444;">OWASP Authorization Cheat Sheet</a></li>
                    <li><a href="https://www.robotstxt.org/" style="color: #ff4444;">About robots.txt</a></li>
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