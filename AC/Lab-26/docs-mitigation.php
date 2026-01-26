<?php
/**
 * Lab 26: Documentation - Part 3 (Mitigation Guide)
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitigation Guide - Lab 26: API IDOR</title>
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
        .section h4 {
            color: #ddd;
            margin: 1rem 0 0.5rem;
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
        .code-block .success { color: #7ee787; background: rgba(0, 200, 100, 0.1); }
        .code-header {
            background: #161b22;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #30363d;
            margin: -1rem -1rem 1rem -1rem;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .code-header span {
            color: #8b949e;
            font-size: 0.85rem;
        }
        .label-vulnerable {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6b6b;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .label-secure {
            background: rgba(0, 200, 100, 0.2);
            color: #7ee787;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .highlight-box {
            background: rgba(0, 180, 216, 0.1);
            border-left: 4px solid #00b4d8;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .success-box {
            background: rgba(0, 200, 100, 0.1);
            border-left: 4px solid #7ee787;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .success-box p { margin: 0; color: #7ee787; }
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
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            padding: 0.75rem 1rem;
            margin: 0.5rem 0;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .checklist li::before {
            content: '✓';
            color: #7ee787;
            font-weight: bold;
        }
        .reference-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: rgba(0, 180, 216, 0.1);
            border: 1px solid rgba(0, 180, 216, 0.2);
            border-radius: 8px;
            margin: 0.5rem 0;
            text-decoration: none;
            color: #00b4d8;
            transition: all 0.3s;
        }
        .reference-link:hover {
            background: rgba(0, 180, 216, 0.2);
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
            <h1>🛡️ Mitigation Guide</h1>
            <p>How to properly secure API application management endpoints</p>
        </div>

        <nav class="doc-nav">
            <a href="docs.php">Overview & Walkthrough</a>
            <a href="docs-technical.php">Technical Analysis</a>
            <a href="docs-mitigation.php" class="active">Mitigation Guide</a>
        </nav>

        <section class="section">
            <h2>✅ 8. Secure Code Implementation</h2>
            
            <h3>Fix 1: Authorization Check in Helper Function</h3>
            
            <div class="code-block">
                <div class="code-header">
                    <span class="file-path">config.php</span>
                    <span class="label-secure">SECURE</span>
                </div>
                <pre><span class="comment">/**
 * SECURE: Get application only if user owns it
 */</span>
<span class="keyword">function</span> <span class="function">getApplicationById</span>(<span class="variable">$pdo</span>, <span class="variable">$appId</span>, <span class="variable">$userId</span>) {
    <span class="comment">// FIXED: Always verify ownership!</span>
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"
        SELECT * FROM api_applications 
        <span class="success">WHERE id = ? AND user_id = ?</span>
    "</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$appId</span>, <span class="variable">$userId</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
}
</pre>
            </div>

            <div class="success-box">
                <p>
                    <strong>Key Change:</strong> The SQL query now includes <code>AND user_id = ?</code> 
                    which ensures only the owner can access the record.
                </p>
            </div>

            <h3>Fix 2: Secure Update Handler</h3>
            
            <div class="code-block">
                <div class="code-header">
                    <span class="file-path">update-application.php</span>
                    <span class="label-secure">SECURE</span>
                </div>
                <pre><span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="variable">$applicationId</span> = <span class="variable">$_POST</span>[<span class="string">'application'</span>][<span class="string">'id'</span>] ?? 0;
    <span class="variable">$applicationName</span> = <span class="variable">$_POST</span>[<span class="string">'application'</span>][<span class="string">'name'</span>] ?? <span class="keyword">null</span>;
    <span class="variable">$userId</span> = <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];
    
    <span class="comment">// SECURE: Verify ownership before ANY processing</span>
    <span class="success">$targetApp = getApplicationById($pdo, $applicationId, $userId);</span>
    
    <span class="keyword">if</span> (!<span class="variable">$targetApp</span>) {
        <span class="comment">// SECURE: Generic error message, no data leak</span>
        <span class="variable">$message</span> = <span class="string">'Application not found or access denied'</span>;
        <span class="comment">// Don't set leakedApp - nothing to leak!</span>
    } <span class="keyword">elseif</span> (<span class="function">empty</span>(<span class="variable">$applicationName</span>)) {
        <span class="variable">$message</span> = <span class="string">'Name must be provided'</span>;
        <span class="comment">// SECURE: If validation fails, user already owns this app</span>
        <span class="comment">// Showing their own data is fine</span>
    }
}</pre>
            </div>

            <h3>Fix 3: Never Include Secrets in Error Responses</h3>
            
            <div class="code-block">
                <div class="code-header">
                    <span class="file-path">update-application.php (error handling)</span>
                    <span class="label-secure">SECURE</span>
                </div>
                <pre><span class="comment">// SECURE: Error page should NEVER show sensitive data</span>
<span class="keyword">&lt;?php if</span> (<span class="variable">$message</span>)<span class="keyword">:</span> <span class="keyword">?&gt;</span>
&lt;div class="error-box"&gt;
    &lt;h2&gt;Error&lt;/h2&gt;
    &lt;p&gt;<span class="keyword">&lt;?php echo</span> <span class="function">htmlspecialchars</span>(<span class="variable">$message</span>); <span class="keyword">?&gt;</span>&lt;/p&gt;
    <span class="comment">&lt;!-- NO APPLICATION DATA HERE - EVER! --&gt;</span>
&lt;/div&gt;
<span class="keyword">&lt;?php endif; ?&gt;</span></pre>
            </div>
        </section>

        <section class="section">
            <h2>🏗️ 9. Additional Security Measures</h2>
            
            <h3>Use UUIDs Instead of Sequential IDs</h3>
            
            <div class="code-block">
                <pre><span class="comment">-- SECURE: Use UUIDs to prevent enumeration</span>
<span class="keyword">CREATE TABLE</span> api_applications (
    id <span class="keyword">CHAR(36) PRIMARY KEY DEFAULT</span> (<span class="function">UUID</span>()),
    user_id <span class="keyword">INT NOT NULL</span>,
    name <span class="keyword">VARCHAR(255) NOT NULL</span>,
    client_id <span class="keyword">CHAR(36) DEFAULT</span> (<span class="function">UUID</span>()),
    client_secret <span class="keyword">CHAR(64)</span>,
    ...
);</pre>
            </div>

            <h3>Implement Rate Limiting</h3>
            
            <div class="code-block">
                <pre><span class="comment">// SECURE: Rate limit API requests</span>
<span class="keyword">function</span> <span class="function">checkRateLimit</span>(<span class="variable">$pdo</span>, <span class="variable">$userId</span>, <span class="variable">$action</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"
        SELECT COUNT(*) FROM api_logs 
        WHERE user_id = ? 
        AND action = ?
        AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
    "</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$userId</span>, <span class="variable">$action</span>]);
    
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetchColumn</span>() < <span class="variable">$limit</span>;
}</pre>
            </div>

            <h3>Audit Logging</h3>
            
            <div class="code-block">
                <pre><span class="comment">// SECURE: Log all access attempts for monitoring</span>
<span class="keyword">function</span> <span class="function">logAccessAttempt</span>(<span class="variable">$pdo</span>, <span class="variable">$userId</span>, <span class="variable">$resourceId</span>, <span class="variable">$resourceType</span>, <span class="variable">$success</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"
        INSERT INTO audit_log (user_id, resource_id, resource_type, success, ip_address, timestamp)
        VALUES (?, ?, ?, ?, ?, NOW())
    "</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([
        <span class="variable">$userId</span>,
        <span class="variable">$resourceId</span>,
        <span class="variable">$resourceType</span>,
        <span class="variable">$success</span>,
        <span class="variable">$_SERVER</span>[<span class="string">'REMOTE_ADDR'</span>]
    ]);
}</pre>
            </div>
        </section>

        <section class="section">
            <h2>📋 10. Security Checklist</h2>
            
            <ul class="checklist">
                <li>
                    <div>
                        <strong>Authorization on Every Request</strong><br>
                        <span style="color: #888;">Always verify user ownership before returning or modifying resources</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Generic Error Messages</strong><br>
                        <span style="color: #888;">Never include sensitive data in error responses</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Unpredictable Identifiers</strong><br>
                        <span style="color: #888;">Use UUIDs instead of sequential IDs for security-sensitive resources</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Rate Limiting</strong><br>
                        <span style="color: #888;">Implement rate limits to prevent brute force enumeration</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Comprehensive Logging</strong><br>
                        <span style="color: #888;">Log all access attempts for security monitoring</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Automated Security Testing</strong><br>
                        <span style="color: #888;">Include IDOR checks in your CI/CD pipeline</span>
                    </div>
                </li>
                <li>
                    <div>
                        <strong>Secrets Management</strong><br>
                        <span style="color: #888;">Never expose secrets in any response - use separate secure endpoints</span>
                    </div>
                </li>
            </ul>
        </section>

        <section class="section">
            <h2>📚 11. References</h2>
            
            <a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" class="reference-link" target="_blank">
                📖 OWASP API Security Top 10 - Broken Object Level Authorization
            </a>
            
            <a href="https://cwe.mitre.org/data/definitions/639.html" class="reference-link" target="_blank">
                📖 CWE-639: Authorization Bypass Through User-Controlled Key
            </a>
            
            <a href="https://portswigger.net/web-security/access-control/idor" class="reference-link" target="_blank">
                📖 PortSwigger - Insecure Direct Object References (IDOR)
            </a>
            
            <a href="https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html" class="reference-link" target="_blank">
                📖 OWASP Access Control Cheat Sheet
            </a>

            <div class="highlight-box" style="margin-top: 1.5rem;">
                <p>
                    <strong>💡 Pro Tip:</strong> IDOR vulnerabilities are consistently among the most 
                    common and impactful bugs found through bug bounty programs. Always implement 
                    authorization checks at the data access layer, not just the UI layer.
                </p>
            </div>
        </section>

        <div class="nav-buttons">
            <a href="docs-technical.php" class="btn btn-secondary">← Technical Analysis</a>
            <a href="dashboard.php" class="btn btn-primary">Try the Lab →</a>
        </div>
    </main>
</body>
</html>
