<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Data Leakage in Redirect</title>
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
            <a href="index.php" class="logo">🔄 Lab 7</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">Profile</a>
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
                <h1>🔄 Data Leakage in Redirect Response</h1>
                <p>
                    This lab demonstrates a vulnerability where sensitive data is included in the 
                    response body of a redirect. While browsers automatically follow redirects 
                    and don't display the intermediate response, tools like Burp Suite can capture 
                    and view this leaked data.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Access another user's profile and capture their API key from the redirect response body using Burp Suite.</p>
                </div>

                <h2>Understanding HTTP Redirects</h2>
                <p>
                    When a server sends a 302 redirect response, browsers automatically follow the 
                    <span class="code-inline">Location</span> header to the new URL. Users never see 
                    the body content of the redirect response - but it's still transmitted and can 
                    be intercepted.
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
                                <td>Unknown</td>
                                <td>Target User</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>The Redirect Behavior</h2>
                <p>
                    When you try to access another user's profile without authorization, the server 
                    performs an access check and redirects you to the login page. However, before 
                    the redirect happens, the server has already loaded and rendered the user data.
                </p>

                <div class="code-block">
                    <pre>HTTP/1.1 302 Found
Location: /login.php
Content-Type: text/html

&lt;!-- This body content is sent BEFORE the redirect! --&gt;
&lt;html&gt;
&lt;body&gt;
    &lt;h1&gt;Profile: carlos&lt;/h1&gt;
    &lt;p&gt;API Key: secret-api-key-here&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</pre>
                </div>

                <h2>Why This Happens</h2>
                <ul>
                    <li><strong>Order of operations</strong> - Data is loaded before authorization check</li>
                    <li><strong>Redirect doesn't exit</strong> - Script continues after sending redirect header</li>
                    <li><strong>Browser behavior</strong> - Users don't see the body, creating false sense of security</li>
                    <li><strong>Incomplete access control</strong> - Authorization check doesn't stop data from being sent</li>
                </ul>

                <div class="alert alert-warning">
                    <h4>⚠️ Hidden Data Exposure</h4>
                    <p>The sensitive data is transmitted even though the browser doesn't display it to the user.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener:peter
2. Note your profile URL structure
3. Configure Burp Suite to intercept responses
4. Try to access carlos's profile (change user ID)
5. Browser gets redirected, but...
6. Burp captures the response body with the API key!</pre>
                </div>

                <h2>Burp Suite Setup</h2>
                <div class="code-block">
                    <pre>1. Open Burp Suite
2. Go to Proxy → Options
3. Enable "Intercept Server Responses"
4. Or: Disable interception and check HTTP history
5. Send request to carlos's profile
6. View the response in HTTP history</pre>
                </div>

                <h2>Using cURL to Capture</h2>
                <div class="code-block">
                    <pre># Don't follow redirects (-L is not used)
curl -i "http://localhost/AC/lab7/profile.php?id=carlos" \
  -H "Cookie: PHPSESSID=your-session-cookie"

# Response shows 302 redirect BUT also the body content!</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚫 Invisible Data Leak</h4>
                    <p>The API key is transmitted to the attacker even though they're "blocked" by the redirect.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login to the application</strong></p>
                <p>Use credentials <span class="code-inline">wiener:peter</span></p>

                <p><span class="step-number">2</span><strong>Go to your profile</strong></p>
                <p>Navigate to your profile and note the URL structure (e.g., <span class="code-inline">profile.php?id=3</span>)</p>

                <p><span class="step-number">3</span><strong>Configure Burp Suite</strong></p>
                <p>Enable response interception or prepare to view HTTP history</p>

                <p><span class="step-number">4</span><strong>Access carlos's profile</strong></p>
                <p>Change the ID parameter to access another user's profile</p>

                <p><span class="step-number">5</span><strong>Observe the redirect</strong></p>
                <p>Your browser will be redirected to the login page</p>

                <p><span class="step-number">6</span><strong>Check Burp's HTTP history</strong></p>
                <p>Look at the response for the profile request - the body contains the API key!</p>

                <p><span class="step-number">7</span><strong>Extract the API key</strong></p>
                <p>Copy the API key from the response body</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you capture carlos's API key from the redirect response body.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
session_start();

$userId = $_GET['id'];

// Load user data FIRST (wrong order!)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Check authorization AFTER loading data
if ($userId != $_SESSION['user_id']) {
    header("Location: login.php");
    // VULNERABILITY: No exit! Script continues...
}

// This still gets executed and sent!
?&gt;
&lt;html&gt;
&lt;body&gt;
    &lt;h1&gt;Profile: &lt;?= $user['username'] ?&gt;&lt;/h1&gt;
    &lt;p&gt;API Key: &lt;?= $user['api_key'] ?&gt;&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</pre>
                </div>

                <h2>The Critical Bug</h2>
                <p>
                    The <span class="code-inline">header("Location: ...")</span> function only sends 
                    an HTTP header - it does NOT stop script execution. Without 
                    <span class="code-inline">exit()</span> or <span class="code-inline">die()</span>, 
                    the rest of the page is still rendered and sent.
                </p>

                <div class="alert alert-danger">
                    <h4>🐛 Common Mistake</h4>
                    <p>Many developers assume header() stops execution - it doesn't!</p>
                </div>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Fix 1: Exit After Redirect</h2>
                <div class="code-block">
                    <pre>&lt;?php
// Check authorization FIRST
if ($userId != $_SESSION['user_id']) {
    header("Location: login.php");
    exit();  // CRITICAL: Stop script execution!
}

// Only load data AFTER authorization passes
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
// ... rest of code
?&gt;</pre>
                </div>

                <h2>Fix 2: Check Before Loading Data</h2>
                <div class="code-block">
                    <pre>&lt;?php
session_start();

$userId = $_GET['id'];

// Authorization check BEFORE any data loading
if ($userId != $_SESSION['user_id']) {
    header("Location: login.php");
    exit();
}

// Safe: Only reached if authorized
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
// ... load and display data
?&gt;</pre>
                </div>

                <h2>Fix 3: Don't Send Body with Redirect</h2>
                <div class="code-block">
                    <pre>&lt;?php
function redirect($url) {
    // Send redirect with no body
    header("Location: " . $url);
    header("Content-Length: 0");
    exit();
}

// Usage
if (!authorized()) {
    redirect("login.php");
}
?&gt;</pre>
                </div>

                <h2>Best Practices</h2>
                <ul>
                    <li><strong>Always exit after redirect</strong> - Use <span class="code-inline">exit()</span> immediately after <span class="code-inline">header("Location: ...")</span></li>
                    <li><strong>Check before load</strong> - Perform authorization before loading sensitive data</li>
                    <li><strong>Security testing</strong> - Test redirects by examining raw HTTP responses</li>
                    <li><strong>Code review</strong> - Check all redirects for missing exit statements</li>
                </ul>

                <div class="alert alert-info">
                    <h4>💡 Golden Rule</h4>
                    <p>Every header("Location: ...") should be followed by exit() or die().</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://cwe.mitre.org/data/definitions/200.html" style="color: #ff4444;">CWE-200: Information Exposure</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/698.html" style="color: #ff4444;">CWE-698: Execution After Redirect (EAR)</a></li>
                    <li><a href="https://owasp.org/www-community/vulnerabilities/Execution_After_Redirect_(EAR)" style="color: #ff4444;">OWASP - Execution After Redirect</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://www.php.net/manual/en/function.header.php" style="color: #ff4444;">PHP header() Function Documentation</a></li>
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