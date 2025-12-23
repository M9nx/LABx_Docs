<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Chat Transcript IDOR</title>
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
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin: 1.5rem 0;
        }
        .comparison-box {
            padding: 1.5rem;
            border-radius: 10px;
        }
        .comparison-box.vulnerable {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .comparison-box.secure {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
        }
        .comparison-box h4 {
            margin-bottom: 1rem;
        }
        .comparison-box.vulnerable h4 { color: #ff4444; }
        .comparison-box.secure h4 { color: #44ff44; }
        @media (max-width: 1024px) {
            .comparison-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">💬 Lab 9</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="chat.php">Live Chat</a>
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
                <li><a href="#comparison">Code Comparison</a></li>
                <li><a href="#references">References</a></li>
            </ul>
            <h3 style="margin-top: 2rem;">Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="index.php">🏠 Lab Home</a></li>
                <li><a href="chat.php">💬 Live Chat</a></li>
                <li><a href="login.php">🔐 Login Page</a></li>
                <li><a href="lab-description.php">📋 Lab Description</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <section id="overview" class="doc-section">
                <h1>💬 Insecure Direct Object References (IDOR)</h1>
                <p>
                    This lab demonstrates an IDOR vulnerability where chat transcripts are stored with 
                    predictable filenames and served to any user without authentication or authorization checks.
                </p>

                <div class="alert alert-info">
                    <h4>💡 Lab Objective</h4>
                    <p>Find carlos's password in a chat transcript and use it to login to their account.</p>
                </div>

                <h2>What is IDOR?</h2>
                <p>
                    Insecure Direct Object Reference (IDOR) is a type of access control vulnerability that 
                    occurs when an application uses user-controllable input to directly access objects 
                    such as files, database records, or other resources without proper authorization checks.
                </p>

                <h2>Why Does IDOR Happen?</h2>
                <ul>
                    <li><strong>Predictable identifiers</strong> - Using sequential numbers or simple patterns for resource IDs</li>
                    <li><strong>Missing authorization</strong> - Not checking if the user owns or has access to the resource</li>
                    <li><strong>Trust in client input</strong> - Assuming users won't modify URL parameters or request data</li>
                    <li><strong>Direct file access</strong> - Serving files based on user-provided filenames without validation</li>
                </ul>

                <h2>Lab Credentials</h2>
                <div class="table-container">
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="code-inline">wiener</span></td>
                                <td><span class="code-inline">peter</span></td>
                                <td>Your test account</td>
                            </tr>
                            <tr>
                                <td><span class="code-inline">carlos</span></td>
                                <td>Hidden in transcript</td>
                                <td>Target account</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="vulnerability" class="doc-section">
                <h1>🔍 Vulnerability Details</h1>
                
                <h2>The Vulnerable Pattern</h2>
                <p>
                    The application stores chat transcripts as text files with incrementing numeric filenames:
                </p>

                <div class="code-block">
                    <pre>/transcripts/1.txt  → First chat transcript (carlos's)
/transcripts/2.txt  → Second chat transcript (wiener's)
/transcripts/3.txt  → Third chat transcript
...</pre>
                </div>

                <h2>The Access Pattern</h2>
                <p>
                    Transcripts are retrieved via a URL parameter with no authentication check:
                </p>

                <div class="code-block">
                    <pre>download-transcript.php?file=2.txt   ← Your transcript
download-transcript.php?file=1.txt   ← Anyone's transcript! No check!</pre>
                </div>

                <h2>Why This Is Vulnerable</h2>
                <ul>
                    <li><strong>No authentication</strong> - Anyone can access transcripts without logging in</li>
                    <li><strong>No authorization</strong> - No check verifies if the transcript belongs to the requesting user</li>
                    <li><strong>Predictable filenames</strong> - Sequential numbers make enumeration trivial</li>
                    <li><strong>Sensitive data exposure</strong> - Chat logs may contain passwords and private information</li>
                </ul>

                <div class="alert alert-danger">
                    <h4>🚫 Critical Flaw</h4>
                    <p>The transcript download endpoint trusts the user-provided filename completely without any ownership verification.</p>
                </div>
            </section>

            <section id="exploitation" class="doc-section">
                <h1>⚡ Exploitation</h1>

                <h2>Attack Flow</h2>
                <div class="code-block">
                    <pre>1. Login as wiener:peter
2. Use Live Chat feature - send a message
3. Click "View Transcript"
4. Note URL: download-transcript.php?file=5.txt
5. Change file=5.txt to file=1.txt
6. Read carlos's chat transcript
7. Find password: h5a2xfj8k3
8. Login as carlos with stolen password</pre>
                </div>

                <h2>Enumeration Technique</h2>
                <div class="code-block">
                    <pre># Manual enumeration
download-transcript.php?file=1.txt
download-transcript.php?file=2.txt
download-transcript.php?file=3.txt

# Using curl
for i in {1..10}; do
    curl "http://target/download-transcript.php?file=$i.txt"
done

# Using Burp Intruder
Position: file=§1§.txt
Payload: Numbers 1-100</pre>
                </div>

                <h2>What You'll Find</h2>
                <p>
                    In <span class="code-inline">1.txt</span>, carlos's chat with support reveals their password 
                    in the conversation where they requested a password reset.
                </p>

                <div class="code-block">
                    <pre>[14:34:15] Support Agent: Your new password has been set to: h5a2xfj8k3</pre>
                </div>

                <div class="alert alert-warning">
                    <h4>⚠️ Real World Impact</h4>
                    <p>Chat logs often contain sensitive information: passwords, personal details, financial data, API keys, and more.</p>
                </div>
            </section>

            <section id="step-by-step" class="doc-section">
                <h1>📖 Step-by-Step Guide</h1>

                <p><span class="step-number">1</span><strong>Login to the application</strong></p>
                <p>Use credentials <span class="code-inline">wiener:peter</span></p>

                <p><span class="step-number">2</span><strong>Navigate to Live Chat</strong></p>
                <p>Click "Live Chat" in the navigation menu</p>

                <p><span class="step-number">3</span><strong>Start a chat session</strong></p>
                <p>Send any message to the support agent</p>

                <p><span class="step-number">4</span><strong>View your transcript</strong></p>
                <p>Click the "View Transcript" button after the chat</p>

                <p><span class="step-number">5</span><strong>Analyze the URL</strong></p>
                <p>Note the URL pattern: <span class="code-inline">download-transcript.php?file=X.txt</span></p>

                <p><span class="step-number">6</span><strong>Enumerate other transcripts</strong></p>
                <p>Change the filename to <span class="code-inline">1.txt</span> in the URL</p>

                <p><span class="step-number">7</span><strong>Find the password</strong></p>
                <p>Read carlos's chat transcript and locate the password: <span class="code-inline">h5a2xfj8k3</span></p>

                <p><span class="step-number">8</span><strong>Login as carlos</strong></p>
                <p>Use credentials <span class="code-inline">carlos:h5a2xfj8k3</span> to complete the lab</p>

                <div class="alert alert-success">
                    <h4>✅ Success Criteria</h4>
                    <p>The lab is solved when you successfully login as carlos using the password found in the chat transcript.</p>
                </div>
            </section>

            <section id="code-analysis" class="doc-section">
                <h1>🔬 Code Analysis</h1>

                <h2>Vulnerable Code - download-transcript.php</h2>
                <div class="code-block">
                    <pre>&lt;?php
// VULNERABLE: No authentication or authorization check!

// Get the requested file from URL parameter
$file = $_GET['file'] ?? '';

// Basic path traversal prevention (but still vulnerable to IDOR!)
$file = basename($file);

$transcriptDir = __DIR__ . '/transcripts';
$filePath = $transcriptDir . '/' . $file;

// Check if file exists - but NOT if user owns it!
if (empty($file) || !file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

// VULNERABILITY: Serve ANY transcript to ANY user!
$content = file_get_contents($filePath);
echo $content;
?&gt;</pre>
                </div>

                <h2>What's Missing</h2>
                <ul>
                    <li><strong>Authentication check</strong> - No verification that user is logged in</li>
                    <li><strong>Authorization check</strong> - No verification that user owns this transcript</li>
                    <li><strong>Session validation</strong> - No correlation between session and requested resource</li>
                    <li><strong>Access logging</strong> - No audit trail of who accessed what</li>
                </ul>

                <h2>The Problem Flow</h2>
                <div class="code-block">
                    <pre>User Request                    Server Response
─────────────────────────────   ─────────────────────────────
GET ?file=1.txt                 → Read transcripts/1.txt
                                → Return content (NO AUTH CHECK!)

Expected:
GET ?file=1.txt                 → Check: Is user logged in?
                                → Check: Does user own file 1.txt?
                                → If both yes: Return content
                                → If no: Return 403 Forbidden</pre>
                </div>

                <div class="alert alert-danger">
                    <h4>🐛 Root Cause</h4>
                    <p>The endpoint trusts the user-provided filename and serves files directly without verifying ownership or even authentication.</p>
                </div>
            </section>

            <section id="prevention" class="doc-section">
                <h1>🛡️ Prevention</h1>

                <h2>Secure Code Implementation</h2>
                <div class="code-block">
                    <pre>&lt;?php
session_start();
require_once 'config.php';

// SECURE: Check authentication first
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.0 401 Unauthorized");
    exit("Please login to access transcripts");
}

$requestedFile = basename($_GET['file'] ?? '');
$userId = $_SESSION['user_id'];

// SECURE: Query database to verify ownership
$stmt = $conn->prepare("SELECT filename FROM chat_logs 
                        WHERE filename = ? AND user_id = ?");
$stmt->bind_param("si", $requestedFile, $userId);
$stmt->execute();
$result = $stmt->get_result();

// SECURE: Only allow access if user owns the transcript
if ($result->num_rows === 0) {
    header("HTTP/1.0 403 Forbidden");
    exit("Access denied: This transcript does not belong to you");
}

// Safe to serve the file now
$filePath = __DIR__ . '/transcripts/' . $requestedFile;
if (file_exists($filePath)) {
    echo file_get_contents($filePath);
} else {
    header("HTTP/1.0 404 Not Found");
    exit("Transcript not found");
}
?&gt;</pre>
                </div>

                <h2>Defense Strategies</h2>
                <ul>
                    <li><strong>Authentication first</strong> - Always verify the user is logged in before serving protected resources</li>
                    <li><strong>Authorization check</strong> - Verify the user has permission to access the specific resource</li>
                    <li><strong>Indirect references</strong> - Use random tokens instead of predictable IDs</li>
                    <li><strong>Database lookup</strong> - Query database to confirm ownership rather than trusting client input</li>
                    <li><strong>Access logging</strong> - Log all access attempts for audit and detection</li>
                </ul>

                <h2>Using Indirect References</h2>
                <div class="code-block">
                    <pre>&lt;?php
// Generate unique token for each transcript
$token = bin2hex(random_bytes(32));

// Store mapping: token -> actual file, user_id
// URL becomes: download-transcript.php?token=a7b3c2d1...

// On access: lookup token, verify user_id matches session
$stmt = $conn->prepare("SELECT filename, user_id FROM transcript_tokens 
                        WHERE token = ?");
// This prevents enumeration entirely!
?&gt;</pre>
                </div>

                <div class="alert alert-info">
                    <h4>💡 Best Practice</h4>
                    <p>Never expose internal identifiers directly. Use indirect references and always verify authorization server-side.</p>
                </div>
            </section>

            <section id="comparison" class="doc-section">
                <h1>⚖️ Code Comparison</h1>

                <div class="comparison-grid">
                    <div class="comparison-box vulnerable">
                        <h4>❌ Vulnerable Code</h4>
                        <div class="code-block">
                            <pre>$file = $_GET['file'];
$file = basename($file);

// NO AUTH CHECK!
// NO OWNERSHIP CHECK!

$content = file_get_contents(
    "transcripts/" . $file
);
echo $content;</pre>
                        </div>
                    </div>
                    <div class="comparison-box secure">
                        <h4>✅ Secure Code</h4>
                        <div class="code-block">
                            <pre>// Check authentication
if (!$_SESSION['user_id']) {
    die("Unauthorized");
}

// Verify ownership
$stmt = $conn->prepare(
    "SELECT * FROM chat_logs 
     WHERE filename=? AND user_id=?"
);
if ($result->num_rows === 0) {
    die("Access denied");
}</pre>
                        </div>
                    </div>
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
                                <td>Authentication</td>
                                <td>❌ None</td>
                                <td>✅ Session check</td>
                            </tr>
                            <tr>
                                <td>Authorization</td>
                                <td>❌ None</td>
                                <td>✅ Ownership verification</td>
                            </tr>
                            <tr>
                                <td>Input Trust</td>
                                <td>❌ Trusts client</td>
                                <td>✅ Server-side validation</td>
                            </tr>
                            <tr>
                                <td>Access Control</td>
                                <td>❌ Anyone can access</td>
                                <td>✅ Owner only</td>
                            </tr>
                            <tr>
                                <td>Error Messages</td>
                                <td>❌ File not found only</td>
                                <td>✅ Proper 401/403 responses</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>Why The Fix Works</h2>
                <ol>
                    <li><strong>Session check</strong> - Ensures only authenticated users can attempt access</li>
                    <li><strong>Database verification</strong> - Confirms the transcript belongs to the requesting user</li>
                    <li><strong>Proper error handling</strong> - Returns appropriate HTTP status codes</li>
                    <li><strong>Defense in depth</strong> - Multiple layers of protection</li>
                </ol>
            </section>

            <section id="references" class="doc-section">
                <h1>📚 References</h1>

                <h2>OWASP Resources</h2>
                <ul>
                    <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" style="color: #ff4444;">OWASP Testing for IDOR</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" style="color: #ff4444;">OWASP IDOR Prevention Cheat Sheet</a></li>
                    <li><a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" style="color: #ff4444;">OWASP API1 - Broken Object Level Authorization</a></li>
                </ul>

                <h2>CWE References</h2>
                <ul>
                    <li><a href="https://cwe.mitre.org/data/definitions/639.html" style="color: #ff4444;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/284.html" style="color: #ff4444;">CWE-284: Improper Access Control</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/200.html" style="color: #ff4444;">CWE-200: Exposure of Sensitive Information</a></li>
                </ul>

                <h2>Further Reading</h2>
                <ul>
                    <li><a href="https://portswigger.net/web-security/access-control/idor" style="color: #ff4444;">PortSwigger - IDOR</a></li>
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