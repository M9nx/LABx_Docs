<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Password Disclosure Lab</title>
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
            <a href="index.php" class="logo">🔑 PassLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo htmlspecialchars($_SESSION['username']); ?>">My Account</a>
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
                <h1>🔑 Password Disclosure via IDOR</h1>
                <p>
                    This documentation provides comprehensive information about the password disclosure vulnerability 
                    present in this lab, including technical details, exploitation methods, and prevention techniques.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Retrieve the administrator's password by exploiting the IDOR vulnerability, then delete the user "carlos".</p>
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
                                <td><span class="code-inline">administrator</span></td>
                                <td>??? (to discover)</td>
                                <td>Admin</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>What is Password Disclosure?</h2>
                <p>
                    Password disclosure occurs when an application inadvertently exposes user passwords in a way that 
                    can be accessed by attackers. In this lab, the password is stored in the HTML value attribute of 
                    a password input field.
                </p>

                <h2>The Two Vulnerabilities</h2>
                <p>This lab contains two interconnected vulnerabilities:</p>
                
                <h3>1. IDOR (Insecure Direct Object Reference)</h3>
                <p>
                    The application accepts a user-controllable <span class="code-inline">id</span> parameter to determine 
                    which user's profile to display. It does not verify that the logged-in user should have access to the 
                    requested profile.
                </p>

                <h3>2. Password Exposure in HTML</h3>
                <p>
                    The profile page pre-fills a password change field with the user's current password. While displayed 
                    as masked dots (●●●●●●), the actual password is visible in the HTML source code.
                </p>

                <div class="code-block">
                    <pre>&lt;!-- Vulnerable HTML Output --&gt;
&lt;input type="password" 
       name="current_password" 
       value="x4dm1n_s3cr3t_p@ss!"&gt;

&lt;!-- Password visible in: value="x4dm1n_s3cr3t_p@ss!" --&gt;</pre>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener → Access /profile.php?id=wiener
2. View HTML source → Find password in input value
3. Change URL to /profile.php?id=administrator
4. View HTML source → Extract admin password
5. Login as administrator
6. Access admin panel → Delete carlos</pre>
                </div>

                <h2>URL Manipulation</h2>
                <p>The vulnerable URL structure:</p>
                <div class="code-block">
                    <pre># Original URL (your profile)
http://localhost/AC/lab8/profile.php?id=wiener

# Manipulated URL (admin profile)
http://localhost/AC/lab8/profile.php?id=administrator</pre>
                </div>

                <div class="alert alert-warning">
                    <h4>⚠️ Important</h4>
                    <p>The password appears masked on screen but is fully visible in the page source (Ctrl+U or View Source).</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login with provided credentials</strong></p>
                <p>Navigate to the login page and enter <span class="code-inline">wiener</span> / <span class="code-inline">peter</span></p>

                <p><span class="step-number">2</span><strong>Access your profile page</strong></p>
                <p>Click "My Account" to navigate to your profile. Note the URL contains <span class="code-inline">?id=wiener</span></p>

                <p><span class="step-number">3</span><strong>View page source</strong></p>
                <p>Press <span class="code-inline">Ctrl+U</span> or right-click and select "View Page Source"</p>

                <p><span class="step-number">4</span><strong>Find the password field</strong></p>
                <p>Search for "password" in the source. You'll find your password in an input value attribute.</p>

                <p><span class="step-number">5</span><strong>Modify the id parameter</strong></p>
                <p>Change the URL to: <span class="code-inline">profile.php?id=administrator</span></p>

                <p><span class="step-number">6</span><strong>Extract administrator password</strong></p>
                <p>View the page source again and find the administrator's password in the input field.</p>

                <p><span class="step-number">7</span><strong>Login as administrator</strong></p>
                <p>Log out, then log back in using the administrator credentials you discovered.</p>

                <p><span class="step-number">8</span><strong>Delete carlos</strong></p>
                <p>Access the Admin Panel and delete the user "carlos" to complete the lab.</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is solved when you successfully delete the user "carlos" using the administrator account.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: No authorization check!
$requestedUser = $_GET['id'] ?? $_SESSION['username'];

// Fetches ANY user's data including password
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $requestedUser);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?&gt;

&lt;!-- VULNERABLE: Password exposed in HTML --&gt;
&lt;input type="password" 
       value="&lt;?php echo $user['password']; ?&gt;"&gt;</pre>
                </div>

                <h2>Security Issues</h2>
                <ul>
                    <li><strong>No ownership verification</strong> - Any user can request any other user's profile</li>
                    <li><strong>Password in HTML</strong> - Passwords should never be sent to the browser</li>
                    <li><strong>Plaintext passwords</strong> - Passwords should be hashed, not stored in plaintext</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// SECURE: Always use session user
$userId = $_SESSION['user_id'];  // Never from URL!

// Only fetch current user's data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);

// Never send passwords to browser
// For password changes, verify old password server-side
?&gt;

&lt;!-- SECURE: No password in HTML --&gt;
&lt;input type="password" 
       name="current_password" 
       placeholder="Enter current password"&gt;</pre>
                </div>

                <h2>Best Practices</h2>
                <ul>
                    <li><strong>Hash passwords</strong> - Use bcrypt or Argon2 for password storage</li>
                    <li><strong>Session-based identity</strong> - Never rely on user-supplied ID parameters for sensitive data</li>
                    <li><strong>Authorization checks</strong> - Verify user has permission to access requested resources</li>
                    <li><strong>No passwords in HTML</strong> - Passwords should never be sent to the client</li>
                    <li><strong>Re-authentication</strong> - Require password re-entry for sensitive operations</li>
                </ul>

                <div class="alert alert-danger">
                    <h4>🚫 Never Do This</h4>
                    <p>Never pre-fill password fields with actual passwords, even in masked input fields. The password will always be visible in the HTML source code.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Vulnerabilities</h2>
                <ul>
                    <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" style="color: #ff4444;">OWASP - Insecure Direct Object References</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/639.html" style="color: #ff4444;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/200.html" style="color: #ff4444;">CWE-200: Exposure of Sensitive Information</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html" style="color: #ff4444;">OWASP Authentication Cheat Sheet</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html" style="color: #ff4444;">OWASP Password Storage Cheat Sheet</a></li>
                </ul>
            </section>
        </main>
    </div>

    <script>
        // Smooth scroll for navigation
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                    // Update active state
                    document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Update active nav on scroll
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