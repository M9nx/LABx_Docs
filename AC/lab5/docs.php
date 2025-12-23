<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR Vulnerability</title>
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
            <a href="index.php" class="logo">🔑 Lab 5</a>
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
                <h1>🔑 Horizontal Privilege Escalation via IDOR</h1>
                <p>
                    This lab demonstrates an Insecure Direct Object Reference (IDOR) vulnerability 
                    where user profiles are accessed via a predictable <span class="code-inline">id</span> 
                    parameter in the URL, without proper authorization checks.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Access carlos's profile by changing the id parameter and retrieve his API key.</p>
                </div>

                <h2>What is IDOR?</h2>
                <p>
                    Insecure Direct Object Reference (IDOR) occurs when an application uses user-supplied 
                    input to directly access objects (like database records, files, or resources) without 
                    verifying that the user is authorized to access them.
                </p>

                <h2>Types of Privilege Escalation</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Horizontal</strong></td>
                                <td>Access another user's resources at same privilege level</td>
                                <td>User A accesses User B's profile</td>
                            </tr>
                            <tr>
                                <td><strong>Vertical</strong></td>
                                <td>Access higher privilege functionality</td>
                                <td>Regular user accesses admin panel</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>Lab Credentials</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>User ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="code-inline">wiener</span></td>
                                <td><span class="code-inline">peter</span></td>
                                <td>Your assigned ID</td>
                            </tr>
                            <tr>
                                <td><span class="code-inline">carlos</span></td>
                                <td>Unknown</td>
                                <td>Different ID (target)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>The Vulnerable URL Pattern</h2>
                <p>
                    The application uses sequential numeric IDs in the URL to identify user profiles:
                </p>

                <div class="code-block">
                    <pre># Your profile URL
http://localhost/AC/lab5/profile.php?id=3

# Another user's profile (just change the number!)
http://localhost/AC/lab5/profile.php?id=1
http://localhost/AC/lab5/profile.php?id=2</pre>
                </div>

                <h2>Why This Is Vulnerable</h2>
                <ul>
                    <li><strong>Predictable IDs</strong> - Sequential numbers are easy to enumerate</li>
                    <li><strong>No ownership check</strong> - Server doesn't verify if user owns the profile</li>
                    <li><strong>Sensitive data exposed</strong> - API keys visible on profile page</li>
                    <li><strong>Direct database reference</strong> - URL parameter maps directly to database ID</li>
                </ul>

                <div class="alert alert-warning">
                    <h4>⚠️ Enumeration Risk</h4>
                    <p>With sequential IDs, attackers can easily iterate through all user profiles (id=1, id=2, id=3...)</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener:peter
2. Go to your profile, note URL: profile.php?id=X
3. Change id parameter to other numbers
4. Find carlos's profile
5. Copy his API key
6. Submit the API key to solve the lab</pre>
                </div>

                <h2>Manual Enumeration</h2>
                <div class="code-block">
                    <pre># Try different IDs
profile.php?id=1  → User 1's profile
profile.php?id=2  → User 2's profile  
profile.php?id=3  → User 3's profile (maybe carlos?)
...</pre>
                </div>

                <h2>Automated Enumeration with Burp Intruder</h2>
                <div class="code-block">
                    <pre>1. Capture request in Burp
2. Send to Intruder
3. Mark id parameter: profile.php?id=§1§
4. Payload: Numbers 1-100
5. Start attack
6. Look for carlos in responses</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚫 Data Breach</h4>
                    <p>IDOR allows access to any user's sensitive data just by changing a number in the URL.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login to the application</strong></p>
                <p>Use credentials <span class="code-inline">wiener:peter</span></p>

                <p><span class="step-number">2</span><strong>Navigate to your profile</strong></p>
                <p>Click "My Account" or "Profile" in the navigation</p>

                <p><span class="step-number">3</span><strong>Observe the URL</strong></p>
                <p>Note that the URL contains <span class="code-inline">?id=X</span> where X is your user ID</p>

                <p><span class="step-number">4</span><strong>Modify the ID parameter</strong></p>
                <p>Change the ID to other numbers (1, 2, 3, etc.) to find carlos</p>

                <p><span class="step-number">5</span><strong>Find carlos's profile</strong></p>
                <p>When you find a profile belonging to "carlos", you've found the target</p>

                <p><span class="step-number">6</span><strong>Retrieve the API key</strong></p>
                <p>Copy carlos's API key from his profile page</p>

                <p><span class="step-number">7</span><strong>Submit the solution</strong></p>
                <p>Submit the API key to complete the lab</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you successfully retrieve and submit carlos's API key.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: No authorization check!
$userId = $_GET['id'];  // Directly from URL

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Display user's sensitive data
echo "API Key: " . $user['api_key'];
?&gt;</pre>
                </div>

                <h2>What's Missing</h2>
                <ul>
                    <li><strong>Authorization check</strong> - No verification that logged-in user owns this profile</li>
                    <li><strong>Session comparison</strong> - Should compare requested ID with session user ID</li>
                    <li><strong>Access control</strong> - No role-based or resource-based authorization</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
session_start();

// SECURE: Always use session for user's own profile
$userId = $_SESSION['user_id'];  // From session, not URL!

// Or if viewing others is allowed, verify authorization
$requestedId = $_GET['id'];
if ($requestedId != $_SESSION['user_id']) {
    // Check if current user has permission to view this profile
    if (!canViewProfile($_SESSION['user_id'], $requestedId)) {
        http_response_code(403);
        die("Access denied");
    }
}
?&gt;</pre>
                </div>

                <h2>Defense Strategies</h2>
                <ul>
                    <li><strong>Use session data</strong> - For own resources, use session user ID instead of URL parameter</li>
                    <li><strong>Authorization checks</strong> - Always verify user has permission to access resource</li>
                    <li><strong>Indirect references</strong> - Use random tokens instead of sequential IDs</li>
                    <li><strong>UUIDs</strong> - Use unpredictable identifiers that can't be enumerated</li>
                    <li><strong>Access Control Lists</strong> - Implement proper permission systems</li>
                </ul>

                <h2>Using Indirect References</h2>
                <div class="code-block">
                    <pre>&lt;?php
// Generate random token for each user
$profileToken = bin2hex(random_bytes(16));
// Store mapping: token -> user_id in session

// URL becomes: profile.php?token=a7f3b2c1d4e5f6...
// Attacker can't enumerate other users!
?&gt;</pre>
                </div>

                <div class="alert alert-info">
                    <h4>💡 Best Practice</h4>
                    <p>For user's own profile, don't use URL parameter at all - just use the session user ID.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" style="color: #ff4444;">OWASP Testing for IDOR</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/639.html" style="color: #ff4444;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                    <li><a href="https://portswigger.net/web-security/access-control/idor" style="color: #ff4444;">PortSwigger - IDOR</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" style="color: #ff4444;">OWASP IDOR Prevention Cheat Sheet</a></li>
                    <li><a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" style="color: #ff4444;">OWASP API1 - Broken Object Level Authorization</a></li>
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