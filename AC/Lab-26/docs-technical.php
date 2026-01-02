<?php
/**
 * Lab 26: Documentation - Part 2 (Technical Analysis)
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Analysis - Lab 26: API IDOR</title>
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
        .nav-links a { color: #aaa; text-decoration: none; }
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
        .code-block .danger { color: #ff6b6b; background: rgba(255, 68, 68, 0.1); }
        .highlight-box {
            background: rgba(0, 180, 216, 0.1);
            border-left: 4px solid #00b4d8;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .warning-box p { margin: 0; color: #ff9999; }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .file-path {
            color: #ffa657;
            font-family: monospace;
            font-size: 0.9em;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            color: #00b4d8;
            font-weight: 600;
        }
        td { color: #ccc; }
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
        <a href="docs.php" class="back-link">← Back to Overview</a>
        
        <div class="page-header">
            <h1>🔬 Technical Analysis</h1>
            <p>Deep dive into the vulnerable code and exploitation mechanics</p>
        </div>

        <nav class="doc-nav">
            <a href="docs.php">Overview & Walkthrough</a>
            <a href="docs-technical.php" class="active">Technical Analysis</a>
            <a href="docs-mitigation.php">Mitigation Guide</a>
        </nav>

        <section class="section">
            <h2>📂 4. Vulnerable Code Analysis</h2>
            
            <h3>File: <span class="file-path">config.php</span></h3>
            <p>The helper function that fetches applications without ownership verification:</p>
            
            <div class="code-block">
                <pre><span class="comment">/**
 * VULNERABLE FUNCTION - Get application by ID without ownership check
 * This is intentionally vulnerable for the lab!
 */</span>
<span class="keyword">function</span> <span class="function">getApplicationById</span>(<span class="variable">$pdo</span>, <span class="variable">$appId</span>) {
    <span class="comment">// VULNERABILITY: No user ownership verification!</span>
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM api_applications WHERE id = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$appId</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
}
</pre>
            </div>
            
            <div class="warning-box">
                <p>
                    <strong>Problem:</strong> This function accepts ANY application ID and returns the 
                    full record, including <code>client_id</code> and <code>client_secret</code>. 
                    There's no check to verify the requesting user owns this application.
                </p>
            </div>

            <h3>File: <span class="file-path">update-application.php</span></h3>
            <p>The vulnerable update handler that leaks credentials on validation errors:</p>
            
            <div class="code-block">
                <pre><span class="comment">// Handle POST request - THIS IS WHERE THE VULNERABILITY EXISTS</span>
<span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="variable">$applicationId</span> = <span class="variable">$_POST</span>[<span class="string">'application'</span>][<span class="string">'id'</span>] ?? 0;
    <span class="variable">$applicationName</span> = <span class="variable">$_POST</span>[<span class="string">'application'</span>][<span class="string">'name'</span>] ?? <span class="keyword">null</span>;
    
    <span class="comment">// VULNERABILITY: We fetch the application by ID WITHOUT checking ownership!</span>
    <span class="danger">$targetApp = getApplicationById($pdo, $applicationId);</span>
    
    <span class="keyword">if</span> (<span class="function">empty</span>(<span class="variable">$applicationName</span>)) {
        <span class="variable">$message</span> = <span class="string">'Name must be provided'</span>;
        <span class="comment">// CRITICAL LEAK: We set the leaked app to show in the error page!</span>
        <span class="danger">$leakedApp = $targetApp;</span>  <span class="comment">// Contains client_secret!</span>
    }
}</pre>
            </div>

            <h3>The Error Page Rendering</h3>
            <p>When validation fails, the page renders the leaked application data:</p>
            
            <div class="code-block">
                <pre><span class="comment">&lt;!-- VULNERABILITY: This section leaks another user's credentials! --&gt;</span>
<span class="keyword">&lt;?php if</span> (<span class="variable">$leakedApp</span>)<span class="keyword">:</span> <span class="keyword">?&gt;</span>
&lt;div class="leaked-credentials"&gt;
    &lt;div class="leaked-item"&gt;
        &lt;span&gt;Client ID&lt;/span&gt;
        &lt;span&gt;<span class="keyword">&lt;?php echo</span> <span class="variable">$leakedApp</span>[<span class="string">'client_id'</span>]; <span class="keyword">?&gt;</span>&lt;/span&gt;
    &lt;/div&gt;
    &lt;div class="leaked-item"&gt;
        &lt;span&gt;Client Secret&lt;/span&gt;
        <span class="danger">&lt;span&gt;&lt;?php echo $leakedApp['client_secret']; ?&gt;&lt;/span&gt;</span>
    &lt;/div&gt;
&lt;/div&gt;
<span class="keyword">&lt;?php endif; ?&gt;</span></pre>
            </div>
        </section>

        <section class="section">
            <h2>🔑 5. Data Exposed</h2>
            
            <p>The vulnerability exposes the following sensitive fields:</p>
            
            <table>
                <tr>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Impact</th>
                </tr>
                <tr>
                    <td><code>client_id</code></td>
                    <td>OAuth client identifier</td>
                    <td>Medium - Needed for API authentication</td>
                </tr>
                <tr>
                    <td><code>client_secret</code></td>
                    <td>OAuth client secret</td>
                    <td>Critical - Full API access</td>
                </tr>
                <tr>
                    <td><code>scopes</code></td>
                    <td>Granted API permissions</td>
                    <td>Info - Shows what API access is possible</td>
                </tr>
                <tr>
                    <td><code>redirect_uri</code></td>
                    <td>OAuth callback URL</td>
                    <td>Low - Could enable OAuth flow attacks</td>
                </tr>
            </table>

            <h3>Attack Escalation</h3>
            <p>With the leaked credentials, an attacker can:</p>
            <ul>
                <li>Make authenticated API calls as the victim</li>
                <li>Add themselves as a collaborator to victim's sites</li>
                <li>Read/modify WordPress site content</li>
                <li>Access billing information</li>
                <li>Complete account takeover</li>
            </ul>
        </section>

        <section class="section">
            <h2>🎯 6. Attack Flow Diagram</h2>
            
            <div class="code-block">
                <pre>
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Attacker     │     │     Server      │     │    Database     │
└────────┬────────┘     └────────┬────────┘     └────────┬────────┘
         │                       │                       │
         │  POST /update-application.php                 │
         │  application[id]=2    │                       │
         │  (no name field)      │                       │
         │──────────────────────>│                       │
         │                       │                       │
         │                       │  SELECT * FROM        │
         │                       │  api_applications     │
         │                       │  WHERE id = 2         │
         │                       │──────────────────────>│
         │                       │                       │
         │                       │  {id: 2, name: ...,   │
         │                       │   client_secret: ...} │
         │                       │<──────────────────────│
         │                       │                       │
         │                       │  Validation fails:    │
         │                       │  "Name required"      │
         │                       │                       │
         │  Error Response WITH  │                       │
         │  victim's credentials │                       │
         │<──────────────────────│                       │
         │                       │                       │
    ┌────┴────┐                                          
    │ LEAKED! │                                          
    │ client_ │                                          
    │ secret  │                                          
    └─────────┘                                          
</pre>
            </div>
        </section>

        <section class="section">
            <h2>🔍 7. Root Cause Analysis</h2>
            
            <h3>Multiple Failures</h3>
            <ol>
                <li>
                    <strong>Missing Authorization Check:</strong> The query fetches by ID alone, 
                    never verifying <code>user_id = $_SESSION['user_id']</code>
                </li>
                <li>
                    <strong>Information Disclosure:</strong> Error handlers should never include 
                    sensitive data in responses
                </li>
                <li>
                    <strong>Sequential IDs:</strong> Predictable identifiers make enumeration trivial
                </li>
                <li>
                    <strong>No Rate Limiting:</strong> Attacker can enumerate all IDs without restriction
                </li>
            </ol>

            <div class="highlight-box">
                <p>
                    <strong>Defense in Depth Failure:</strong> Even one of these fixes would 
                    significantly reduce the impact, but proper security requires addressing all of them.
                </p>
            </div>
        </section>

        <div class="nav-buttons">
            <a href="docs.php" class="btn btn-secondary">← Overview</a>
            <a href="docs-mitigation.php" class="btn btn-primary">Mitigation Guide →</a>
        </div>
    </main>
</body>
</html>
