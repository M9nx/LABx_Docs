<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR with GUID Leak</title>
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
            <a href="index.php" class="logo">📰 Lab 6</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="blog.php">Blog</a>
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
                <li><a href="blog.php">📰 Blog Posts</a></li>
                <li><a href="login.php">🔐 Login Page</a></li>
                <li><a href="lab-description.php">📋 Lab Description</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <section id="overview" class="doc-section">
                <h1>📰 IDOR with Unpredictable GUIDs</h1>
                <p>
                    This lab demonstrates that using unpredictable identifiers (GUIDs/UUIDs) alone 
                    doesn't protect against IDOR if those identifiers are leaked elsewhere in the 
                    application - in this case, through blog post author information.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Find carlos's GUID in the blog section and use it to access his profile and retrieve his API key.</p>
                </div>

                <h2>The False Security of GUIDs</h2>
                <p>
                    Many developers believe that using UUIDs/GUIDs instead of sequential IDs prevents 
                    IDOR attacks because the identifiers can't be guessed. However, if these identifiers 
                    are leaked through other parts of the application, they become just as vulnerable.
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
                                <td>Blog Author (Target)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>GUID in Profile URLs</h2>
                <p>
                    The application uses GUIDs for user profile URLs, which appears secure at first:
                </p>

                <div class="code-block">
                    <pre># Profile URL with GUID
profile.php?id=550e8400-e29b-41d4-a716-446655440000

# This GUID cannot be guessed... but can it be found?</pre>
                </div>

                <h2>The Information Leak</h2>
                <p>
                    The blog section reveals author information including their user GUID. When viewing 
                    a blog post, the author's GUID is exposed either in the HTML source or in links.
                </p>

                <div class="code-block">
                    <pre>&lt;!-- Blog post by carlos --&gt;
&lt;article&gt;
    &lt;h2&gt;My Blog Post&lt;/h2&gt;
    &lt;p&gt;By &lt;a href="profile.php?id=carlos-guid-here"&gt;carlos&lt;/a&gt;&lt;/p&gt;
&lt;/article&gt;</pre>
                </div>

                <div class="alert alert-warning">
                    <h4>⚠️ Information Correlation</h4>
                    <p>Even "unpredictable" identifiers become vulnerable when leaked through other features.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener:peter
2. Browse to the blog section
3. Find a post written by carlos
4. Extract carlos's GUID from the author link/source
5. Navigate to profile.php?id=[carlos-guid]
6. Retrieve carlos's API key</pre>
                </div>

                <h2>Finding the GUID</h2>
                
                <h3>Method 1: Inspect Element</h3>
                <div class="code-block">
                    <pre>1. Go to blog section
2. Right-click on carlos's name
3. Select "Inspect Element"
4. Find the href attribute with the GUID</pre>
                </div>

                <h3>Method 2: View Page Source</h3>
                <div class="code-block">
                    <pre>1. Go to blog section
2. View page source (Ctrl+U)
3. Search for "carlos"
4. Find associated GUID in URL</pre>
                </div>

                <h3>Method 3: Network Tab</h3>
                <div class="code-block">
                    <pre>1. Open DevTools (F12)
2. Go to Network tab
3. Click on carlos's profile link
4. Observe the GUID in the request URL</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚫 GUID Protection Bypassed</h4>
                    <p>The "unpredictable" GUID is rendered useless because it's leaked in the blog feature.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login to the application</strong></p>
                <p>Use credentials <span class="code-inline">wiener:peter</span></p>

                <p><span class="step-number">2</span><strong>Navigate to the blog section</strong></p>
                <p>Click on "Blog" in the navigation menu</p>

                <p><span class="step-number">3</span><strong>Find a post by carlos</strong></p>
                <p>Look through the blog posts for one authored by "carlos"</p>

                <p><span class="step-number">4</span><strong>Inspect the author link</strong></p>
                <p>Right-click on carlos's name and inspect the element or hover to see the URL</p>

                <p><span class="step-number">5</span><strong>Extract the GUID</strong></p>
                <p>Copy the GUID from the profile URL in the href attribute</p>

                <p><span class="step-number">6</span><strong>Access carlos's profile</strong></p>
                <p>Navigate to <span class="code-inline">profile.php?id=[carlos-guid]</span></p>

                <p><span class="step-number">7</span><strong>Retrieve the API key</strong></p>
                <p>Copy carlos's API key from his profile page</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you find carlos's GUID through the blog and retrieve his API key.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>The Information Leak</h2>
                <div class="code-block">
                    <pre>&lt;?php
// blog.php - Leaks user GUIDs
foreach ($posts as $post) {
    echo "&lt;article&gt;";
    echo "&lt;h2&gt;" . $post['title'] . "&lt;/h2&gt;";
    // VULNERABILITY: Exposes author's GUID!
    echo "&lt;p&gt;By &lt;a href='profile.php?id=" . $post['author_guid'] . "'&gt;";
    echo $post['author_name'] . "&lt;/a&gt;&lt;/p&gt;";
    echo "&lt;/article&gt;";
}
?&gt;</pre>
                </div>

                <h2>Profile Page (Still Vulnerable)</h2>
                <div class="code-block">
                    <pre>&lt;?php
// profile.php - No authorization check
$guid = $_GET['id'];

// Even with GUID, there's no ownership verification!
$stmt = $conn->prepare("SELECT * FROM users WHERE guid = ?");
$stmt->bind_param("s", $guid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

echo "API Key: " . $user['api_key'];  // Sensitive data exposed!
?&gt;</pre>
                </div>

                <h2>The Two Problems</h2>
                <ul>
                    <li><strong>Information leak</strong> - GUIDs exposed in blog section</li>
                    <li><strong>No authorization</strong> - Profile page doesn't verify access rights</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Fix 1: Don't Leak Identifiers</h2>
                <div class="code-block">
                    <pre>&lt;?php
// Use username for display, not GUID
echo "&lt;p&gt;By &lt;a href='author.php?name=" . urlencode($post['author_name']) . "'&gt;";
echo htmlspecialchars($post['author_name']) . "&lt;/a&gt;&lt;/p&gt;";

// Or don't link at all for public profiles
echo "&lt;p&gt;By " . htmlspecialchars($post['author_name']) . "&lt;/p&gt;";
?&gt;</pre>
                </div>

                <h2>Fix 2: Authorization Check</h2>
                <div class="code-block">
                    <pre>&lt;?php
// Always verify authorization regardless of identifier type
$requestedGuid = $_GET['id'];
$currentUserGuid = $_SESSION['user_guid'];

// Only allow viewing own profile
if ($requestedGuid !== $currentUserGuid) {
    http_response_code(403);
    die("You can only view your own profile");
}

// Or implement proper permission system
if (!canViewProfile($currentUserGuid, $requestedGuid)) {
    http_response_code(403);
    die("Access denied");
}
?&gt;</pre>
                </div>

                <h2>Defense in Depth</h2>
                <ul>
                    <li><strong>Don't expose identifiers</strong> - Avoid leaking any user identifiers</li>
                    <li><strong>Authorization checks</strong> - Always verify permission regardless of ID type</li>
                    <li><strong>Sensitive data protection</strong> - API keys shouldn't be visible to others</li>
                    <li><strong>Audit identifier usage</strong> - Review where user IDs appear in the application</li>
                </ul>

                <div class="alert alert-info">
                    <h4>💡 Key Lesson</h4>
                    <p>GUIDs prevent enumeration but don't provide authorization. Always implement proper access controls.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" style="color: #ff4444;">OWASP Testing for IDOR</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/639.html" style="color: #ff4444;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/200.html" style="color: #ff4444;">CWE-200: Exposure of Sensitive Information</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://portswigger.net/web-security/access-control/idor" style="color: #ff4444;">PortSwigger - IDOR Vulnerabilities</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" style="color: #ff4444;">OWASP IDOR Prevention</a></li>
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