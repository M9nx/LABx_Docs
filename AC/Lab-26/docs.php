<?php
/**
 * Lab 26: Documentation - Part 1 (Overview & Walkthrough)
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 26: API IDOR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #00b4d8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #aaa;
            text-decoration: none;
        }
        .nav-links a:hover { color: #00b4d8; }
        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #00b4d8; }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .doc-nav {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .doc-nav a {
            padding: 0.5rem 1rem;
            background: rgba(0, 180, 216, 0.1);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 6px;
            color: #00b4d8;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .doc-nav a:hover, .doc-nav a.active {
            background: rgba(0, 180, 216, 0.2);
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #00b4d8;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section h3 {
            color: #fff;
            margin: 1.5rem 0 0.75rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #ccc;
            padding-left: 1.5rem;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section li { margin-bottom: 0.5rem; }
        .code-block {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            color: #e6edf3;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            margin: 0;
        }
        .code-block .comment { color: #8b949e; }
        .code-block .keyword { color: #ff7b72; }
        .code-block .string { color: #a5d6ff; }
        .code-block .function { color: #d2a8ff; }
        .code-block .variable { color: #ffa657; }
        .highlight-box {
            background: rgba(0, 180, 216, 0.1);
            border-left: 4px solid #00b4d8;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight-box p { margin: 0; color: #ccc; }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .warning-box p { margin: 0; color: #ff9999; }
        .success-box {
            background: rgba(0, 200, 83, 0.1);
            border-left: 4px solid #00c853;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .success-box p { margin: 0; color: #88ff88; }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .step-number {
            display: inline-flex;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .http-request {
            background: #1a1a2e;
            border: 1px solid #333;
            border-radius: 8px;
            overflow: hidden;
            margin: 1rem 0;
        }
        .http-header {
            background: rgba(0, 180, 216, 0.2);
            padding: 0.5rem 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #00b4d8;
        }
        .http-body {
            padding: 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #88ff88;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">⚡</span>
                Pressable
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="applications.php">API Apps</a>
                <a href="docs.php">Docs</a>
                <?php if (isLoggedIn()): ?>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
                <?php else: ?>
                <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="<?php echo isLoggedIn() ? 'dashboard.php' : 'index.php'; ?>" class="back-link">← Back</a>
        
        <div class="page-header">
            <h1>📚 Lab Documentation</h1>
            <p>Complete guide to understanding and exploiting the IDOR vulnerability</p>
        </div>

        <nav class="doc-nav">
            <a href="docs.php" class="active">Overview & Walkthrough</a>
            <a href="docs-technical.php">Technical Analysis</a>
            <a href="docs-mitigation.php">Mitigation Guide</a>
        </nav>

        <section class="section" id="overview">
            <h2>🎯 1. Lab Overview</h2>
            
            <h3>What is IDOR?</h3>
            <p>
                <strong>Insecure Direct Object Reference (IDOR)</strong> is an access control vulnerability 
                that occurs when an application uses user-controlled input to directly access objects 
                (like database records, files, or resources) without proper authorization checks.
            </p>
            
            <h3>This Lab's Scenario</h3>
            <p>
                This lab simulates a managed WordPress hosting platform (like Pressable) where users can 
                create API applications to access the platform's API. Each application has sensitive 
                credentials: a <strong>Client ID</strong> and <strong>Client Secret</strong>.
            </p>
            
            <div class="highlight-box">
                <p>
                    <strong>The Vulnerability:</strong> The application update endpoint accepts an 
                    <code>application[id]</code> parameter that references ANY application in the database, 
                    not just the current user's applications. When validation fails, the error response 
                    leaks the target application's credentials.
                </p>
            </div>

            <h3>Real-World Impact</h3>
            <p>
                This vulnerability is based on an actual HackerOne report on Pressable. The impact is 
                <strong>critical</strong> because:
            </p>
            <ul>
                <li>Attacker can access any user's API credentials</li>
                <li>API credentials enable full account access (read/write sites, add collaborators, access billing)</li>
                <li>Sequential IDs make enumeration trivial</li>
                <li>Can lead to complete account takeover</li>
            </ul>
        </section>

        <section class="section" id="walkthrough">
            <h2>🚶 2. Step-by-Step Exploitation</h2>
            
            <h3><span class="step-number">1</span> Log In as Attacker</h3>
            <p>
                Navigate to the login page and use the attacker credentials:
            </p>
            <ul>
                <li>Username: <code>attacker</code></li>
                <li>Password: <code>attacker123</code></li>
            </ul>

            <h3><span class="step-number">2</span> Navigate to Your Application</h3>
            <p>
                Go to <strong>API Apps</strong> and click <strong>Update</strong> on your application 
                (Application ID: 1). Note that the victim has applications with IDs 2, 3, and 4.
            </p>

            <h3><span class="step-number">3</span> Open Browser Developer Tools</h3>
            <p>
                Press <code>F12</code> to open DevTools and go to the <strong>Network</strong> tab. 
                Make sure "Preserve log" is enabled.
            </p>

            <h3><span class="step-number">4</span> Submit the Update Form</h3>
            <p>
                Click the "Update Application" button. You'll see a POST request to 
                <code>update-application.php</code> in the Network tab.
            </p>

            <h3><span class="step-number">5</span> Examine the Request</h3>
            <p>Click on the request to see the form data being sent:</p>
            
            <div class="http-request">
                <div class="http-header">POST /AC/Lab-26/update-application.php HTTP/1.1</div>
                <div class="http-body">
authenticity_token=abc123...
application[id]=1
application[name]=My Test App
application[description]=Testing the API
application[redirect_uri]=http://localhost:8080/callback
                </div>
            </div>

            <h3><span class="step-number">6</span> Craft the Exploit Request</h3>
            <p>
                Right-click the request and select "Edit and Resend" (Firefox) or copy as cURL.
                Modify the request:
            </p>
            <ol>
                <li>Change <code>application[id]=1</code> to <code>application[id]=2</code></li>
                <li><strong>Remove</strong> the <code>application[name]</code> parameter entirely</li>
                <li>Remove other optional fields (description, redirect_uri)</li>
                <li>Keep only <code>authenticity_token</code> and <code>application[id]</code></li>
            </ol>

            <div class="http-request">
                <div class="http-header">POST /AC/Lab-26/update-application.php HTTP/1.1</div>
                <div class="http-body">
authenticity_token=abc123...
application[id]=2
                </div>
            </div>

            <h3><span class="step-number">7</span> Send and Observe the Leak</h3>
            <p>
                Send the modified request. The response will show an error message 
                <code>"Name must be provided"</code> BUT it will also display the victim's 
                application details including:
            </p>
            
            <div class="warning-box">
                <p>
                    <strong>⚠️ LEAKED CREDENTIALS:</strong><br>
                    • Client ID: <code>cli_bigco_prod_8x7k9m2p4q</code><br>
                    • Client Secret: <code>sec_SUPER_SECRET_bigco_DO_NOT_SHARE_abc123xyz</code>
                </p>
            </div>

            <h3><span class="step-number">8</span> Enumerate Other Applications</h3>
            <p>
                Repeat with <code>application[id]=3</code>, <code>4</code>, <code>5</code>, etc. 
                to access all users' API credentials. IDs are sequential, so enumeration is trivial.
            </p>

            <div class="success-box">
                <p>
                    <strong>✅ Lab Complete!</strong> You've successfully exploited the IDOR 
                    vulnerability to leak another user's API credentials.
                </p>
            </div>
        </section>

        <section class="section" id="why">
            <h2>🔍 3. Why The Exploit Works</h2>
            
            <h3>Missing Authorization Check</h3>
            <p>
                The vulnerable code fetches the application by ID without verifying that the 
                requesting user owns that application:
            </p>
            
            <div class="code-block">
                <pre><span class="comment">// VULNERABLE: No ownership verification!</span>
<span class="keyword">function</span> <span class="function">getApplicationById</span>(<span class="variable">$pdo</span>, <span class="variable">$appId</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM api_applications WHERE id = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$appId</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
}</pre>
            </div>

            <h3>Information Disclosure in Error Handling</h3>
            <p>
                When validation fails, the error handler includes the full application object 
                in the response, including sensitive fields that should never be exposed:
            </p>
            
            <div class="code-block">
                <pre><span class="keyword">if</span> (<span class="function">empty</span>(<span class="variable">$applicationName</span>)) {
    <span class="variable">$message</span> = <span class="string">'Name must be provided'</span>;
    <span class="comment">// BUG: We expose the target app, including secrets!</span>
    <span class="variable">$leakedApp</span> = <span class="variable">$targetApp</span>;
}</pre>
            </div>

            <h3>Trusting Client Input</h3>
            <p>
                The server trusts the <code>application[id]</code> parameter from the client 
                without any validation that the user should have access to that resource.
            </p>
            
            <div class="highlight-box">
                <p>
                    <strong>Key Lesson:</strong> Never trust client-controlled identifiers. 
                    Always verify authorization before accessing or modifying resources.
                </p>
            </div>
        </section>

        <div class="nav-buttons">
            <a href="<?php echo isLoggedIn() ? 'dashboard.php' : 'index.php'; ?>" class="btn btn-secondary">← Back</a>
            <a href="docs-technical.php" class="btn btn-primary">Technical Analysis →</a>
        </div>
    </main>
</body>
</html>
