<?php
/**
 * Lab 27: Documentation - Part 3 (Mitigation Guide)
 * IDOR in Stats API Endpoint
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitigation Guide - Lab 27: Stats API IDOR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
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
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .nav-links a:hover { color: #ffd700; }
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
        .back-link:hover { color: #ffd700; }
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
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 6px;
            color: #ffd700;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .doc-nav a:hover, .doc-nav a.active {
            background: rgba(255, 215, 0, 0.2);
        }
        .section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #ffd700;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
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
            margin: 1.25rem 0 0.5rem;
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
        .code-block .secure { 
            background: rgba(68, 255, 68, 0.15);
            display: inline;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
        }
        .code-block .vulnerable { 
            background: rgba(255, 68, 68, 0.15);
            display: inline;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
        }
        .highlight-box {
            background: rgba(255, 215, 0, 0.1);
            border-left: 4px solid #ffd700;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .success-box {
            background: rgba(68, 255, 68, 0.1);
            border-left: 4px solid #44ff44;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .success-box .label {
            color: #44ff44;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .file-path {
            color: #58a6ff;
            font-family: 'Consolas', monospace;
            font-size: 0.85em;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        .comparison-box {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
        }
        .comparison-box.bad {
            border: 1px solid rgba(255, 68, 68, 0.4);
        }
        .comparison-box.good {
            border: 1px solid rgba(68, 255, 68, 0.4);
        }
        .comparison-box h4 {
            margin: 0 0 0.75rem;
            font-size: 0.9rem;
        }
        .comparison-box.bad h4 { color: #ff6b6b; }
        .comparison-box.good h4 { color: #44ff44; }
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }
        .checklist li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #44ff44;
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
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        @media (max-width: 768px) {
            .comparison-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">📈</span>
                Exness PA
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="docs.php" style="color: #ffd700;">Docs</a>
                <?php if (isLoggedIn()): ?>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
                <?php else: ?>
                <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="docs-technical.php" class="back-link">← Back to Technical Analysis</a>
        
        <div class="page-header">
            <h1>🛡️ Mitigation Guide</h1>
            <p>How to fix IDOR vulnerabilities and implement proper access control</p>
        </div>

        <nav class="doc-nav">
            <a href="docs.php">Overview & Walkthrough</a>
            <a href="docs-technical.php">Technical Analysis</a>
            <a href="docs-mitigation.php" class="active">Mitigation Guide</a>
        </nav>

        <section class="section">
            <h2>🔧 1. Primary Fix: Authorization Check</h2>
            
            <p>
                The most important fix is to verify that the authenticated user owns the 
                requested resource before returning any data.
            </p>

            <h3>Secure API Endpoint</h3>
            <p class="file-path">Fixed: /api/stats/equity.php</p>
            <div class="code-block">
                <pre><span class="keyword">&lt;?php</span>
<span class="keyword">require_once</span> <span class="string">'../../config.php'</span>;
<span class="function">header</span>(<span class="string">'Content-Type: application/json'</span>);

<span class="comment">// Check if user is logged in (Authentication)</span>
<span class="keyword">if</span> (!<span class="function">isLoggedIn</span>()) {
    <span class="function">http_response_code</span>(<span class="variable">401</span>);
    <span class="keyword">echo</span> <span class="function">json_encode</span>([<span class="string">'error'</span> => <span class="string">'Unauthorized'</span>]);
    <span class="keyword">exit</span>;
}

<span class="variable">$accountNumber</span> = <span class="variable">$_GET</span>[<span class="string">'accounts'</span>] ?? <span class="string">''</span>;
<span class="variable">$timeRange</span> = <span class="variable">$_GET</span>[<span class="string">'time_range'</span>] ?? <span class="string">'365'</span>;
<span class="secure"><span class="variable">$userId</span> = <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];</span>

<span class="keyword">if</span> (<span class="keyword">empty</span>(<span class="variable">$accountNumber</span>)) {
    <span class="function">http_response_code</span>(<span class="variable">400</span>);
    <span class="keyword">echo</span> <span class="function">json_encode</span>([<span class="string">'error'</span> => <span class="string">'Account number required'</span>]);
    <span class="keyword">exit</span>;
}

<span class="comment">// ✅ SECURE: Verify ownership before fetching data (Authorization)</span>
<span class="secure"><span class="keyword">if</span> (!<span class="function">userOwnsAccount</span>(<span class="variable">$pdo</span>, <span class="variable">$userId</span>, <span class="variable">$accountNumber</span>)) {
    <span class="function">http_response_code</span>(<span class="variable">403</span>);
    <span class="function">logSecurityEvent</span>(<span class="variable">$pdo</span>, <span class="variable">$userId</span>, <span class="string">'IDOR_ATTEMPT'</span>, <span class="variable">$accountNumber</span>);
    <span class="keyword">echo</span> <span class="function">json_encode</span>([<span class="string">'error'</span> => <span class="string">'Access denied'</span>]);
    <span class="keyword">exit</span>;
}</span>

<span class="comment">// Now safe to fetch stats</span>
<span class="variable">$stats</span> = <span class="function">getAccountStats</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>, <span class="variable">$timeRange</span>, <span class="string">'equity'</span>);
<span class="keyword">echo</span> <span class="function">json_encode</span>(<span class="variable">$stats</span>);</pre>
            </div>

            <h3>Helper Function: userOwnsAccount()</h3>
            <div class="code-block">
                <pre><span class="keyword">function</span> <span class="function">userOwnsAccount</span>(<span class="variable">$pdo</span>, <span class="variable">$userId</span>, <span class="variable">$accountNumber</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"
        SELECT COUNT(*) 
        FROM mt_accounts 
        WHERE account_number = ? AND user_id = ?
    "</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$accountNumber</span>, <span class="variable">$userId</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetchColumn</span>() > <span class="variable">0</span>;
}</pre>
            </div>

            <div class="success-box">
                <div class="label">✅ Key Improvement</div>
                <p>
                    The fix adds a single line that checks <code>userOwnsAccount()</code> 
                    before any data is returned. This is the minimum viable fix.
                </p>
            </div>
        </section>

        <section class="section">
            <h2>📐 2. Alternative: Implicit Authorization</h2>
            
            <p>
                Instead of accepting an account number from the user, only query accounts 
                that belong to the authenticated user.
            </p>

            <h3>Secure Query Pattern</h3>
            <div class="code-block">
                <pre><span class="keyword">function</span> <span class="function">getAccountStatsSecure</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>, <span class="variable">$userId</span>, <span class="variable">$timeRange</span>, <span class="variable">$statType</span>) {
    <span class="comment">// ✅ SECURE: Join with user_id in the query itself</span>
    <span class="variable">$sql</span> = <span class="string">"
        SELECT ts.stat_date, ts.$statType
        FROM trading_stats ts
        INNER JOIN mt_accounts ma ON ts.account_id = ma.id
        WHERE ma.account_number = :account_number
        <span class="secure">AND ma.user_id = :user_id</span>
        AND ts.stat_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ORDER BY ts.stat_date ASC
    "</span>;
    
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="variable">$sql</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([
        <span class="string">':account_number'</span> => <span class="variable">$accountNumber</span>,
        <span class="secure"><span class="string">':user_id'</span> => <span class="variable">$userId</span></span>,
        <span class="string">':days'</span> => (<span class="keyword">int</span>)<span class="variable">$timeRange</span>
    ]);
    
    <span class="comment">// Returns empty array if user doesn't own the account</span>
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetchAll</span>(<span class="variable">PDO::FETCH_ASSOC</span>);
}</pre>
            </div>

            <div class="highlight-box">
                <p>
                    <strong>Benefit:</strong> Authorization is enforced at the database level. 
                    Even if code changes are made elsewhere, the query itself won't return 
                    unauthorized data.
                </p>
            </div>
        </section>

        <section class="section">
            <h2>🏗️ 3. Architectural Solutions</h2>
            
            <h3>Authorization Middleware</h3>
            <p>Implement a centralized authorization layer that all API endpoints must pass through:</p>
            <div class="code-block">
                <pre><span class="keyword">class</span> <span class="function">AuthorizationMiddleware</span> {
    <span class="keyword">public static function</span> <span class="function">checkResourceAccess</span>(<span class="variable">$resourceType</span>, <span class="variable">$resourceId</span>, <span class="variable">$userId</span>) {
        <span class="variable">$accessRules</span> = [
            <span class="string">'mt_account'</span> => <span class="keyword">fn</span>(<span class="variable">$id</span>, <span class="variable">$user</span>) => 
                <span class="function">self::userOwnsAccount</span>(<span class="variable">$id</span>, <span class="variable">$user</span>),
            <span class="string">'order'</span> => <span class="keyword">fn</span>(<span class="variable">$id</span>, <span class="variable">$user</span>) => 
                <span class="function">self::userOwnsOrder</span>(<span class="variable">$id</span>, <span class="variable">$user</span>),
            <span class="string">'document'</span> => <span class="keyword">fn</span>(<span class="variable">$id</span>, <span class="variable">$user</span>) => 
                <span class="function">self::userOwnsDocument</span>(<span class="variable">$id</span>, <span class="variable">$user</span>),
        ];
        
        <span class="keyword">if</span> (!<span class="keyword">isset</span>(<span class="variable">$accessRules</span>[<span class="variable">$resourceType</span>])) {
            <span class="keyword">throw new</span> <span class="function">Exception</span>(<span class="string">'Unknown resource type'</span>);
        }
        
        <span class="keyword">return</span> <span class="variable">$accessRules</span>[<span class="variable">$resourceType</span>](<span class="variable">$resourceId</span>, <span class="variable">$userId</span>);
    }
}

<span class="comment">// Usage in API endpoint:</span>
<span class="keyword">if</span> (!<span class="function">AuthorizationMiddleware::checkResourceAccess</span>(<span class="string">'mt_account'</span>, <span class="variable">$accountNumber</span>, <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
    <span class="function">http_response_code</span>(<span class="variable">403</span>);
    <span class="keyword">exit</span>;
}</pre>
            </div>

            <h3>Object-Level Access Control (OLAC)</h3>
            <p>Implement access control at the model/ORM level:</p>
            <div class="code-block">
                <pre><span class="keyword">class</span> <span class="function">Account</span> {
    <span class="keyword">public static function</span> <span class="function">findForUser</span>(<span class="variable">$accountNumber</span>, <span class="variable">$userId</span>) {
        <span class="comment">// Always scoped to user</span>
        <span class="keyword">return</span> <span class="function">self::where</span>(<span class="string">'account_number'</span>, <span class="variable">$accountNumber</span>)
            -><span class="function">where</span>(<span class="string">'user_id'</span>, <span class="variable">$userId</span>)
            -><span class="function">first</span>();
    }
    
    <span class="keyword">public function</span> <span class="function">getStats</span>(<span class="variable">$userId</span>, <span class="variable">$timeRange</span>, <span class="variable">$statType</span>) {
        <span class="comment">// Verify ownership before returning stats</span>
        <span class="keyword">if</span> (<span class="variable">$this</span>->user_id !== <span class="variable">$userId</span>) {
            <span class="keyword">throw new</span> <span class="function">UnauthorizedAccessException</span>();
        }
        <span class="keyword">return</span> <span class="variable">$this</span>-><span class="function">stats</span>()-><span class="function">forTimeRange</span>(<span class="variable">$timeRange</span>);
    }
}</pre>
            </div>
        </section>

        <section class="section">
            <h2>🔄 4. Code Comparison</h2>
            
            <div class="comparison-grid">
                <div class="comparison-box bad">
                    <h4>❌ Vulnerable Code</h4>
                    <div class="code-block">
                        <pre><span class="comment">// Only checks authentication</span>
<span class="keyword">if</span> (!<span class="function">isLoggedIn</span>()) {
    <span class="keyword">exit</span>;
}

<span class="comment">// Trusts user input directly</span>
<span class="variable">$account</span> = <span class="variable">$_GET</span>[<span class="string">'accounts'</span>];

<span class="comment">// No ownership check</span>
<span class="variable">$data</span> = <span class="function">fetchStats</span>(<span class="variable">$account</span>);</pre>
                    </div>
                </div>
                <div class="comparison-box good">
                    <h4>✅ Secure Code</h4>
                    <div class="code-block">
                        <pre><span class="comment">// Check authentication</span>
<span class="keyword">if</span> (!<span class="function">isLoggedIn</span>()) {
    <span class="keyword">exit</span>;
}

<span class="comment">// Get user from session</span>
<span class="variable">$userId</span> = <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];

<span class="comment">// Verify ownership (authorization)</span>
<span class="keyword">if</span> (!<span class="function">userOwns</span>(<span class="variable">$account</span>, <span class="variable">$userId</span>)) {
    <span class="function">http_response_code</span>(<span class="variable">403</span>);
    <span class="keyword">exit</span>;
}

<span class="variable">$data</span> = <span class="function">fetchStats</span>(<span class="variable">$account</span>);</pre>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <h2>🔐 5. Additional Security Measures</h2>
            
            <h3>Rate Limiting</h3>
            <p>Implement rate limiting to prevent automated enumeration:</p>
            <div class="code-block">
                <pre><span class="comment">// Redis-based rate limiter</span>
<span class="variable">$key</span> = <span class="string">"stats_api:"</span> . <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];
<span class="variable">$requests</span> = <span class="variable">$redis</span>-><span class="function">incr</span>(<span class="variable">$key</span>);

<span class="keyword">if</span> (<span class="variable">$requests</span> === <span class="variable">1</span>) {
    <span class="variable">$redis</span>-><span class="function">expire</span>(<span class="variable">$key</span>, <span class="variable">60</span>); <span class="comment">// 60 second window</span>
}

<span class="keyword">if</span> (<span class="variable">$requests</span> > <span class="variable">30</span>) { <span class="comment">// Max 30 requests per minute</span>
    <span class="function">http_response_code</span>(<span class="variable">429</span>);
    <span class="keyword">echo</span> <span class="function">json_encode</span>([<span class="string">'error'</span> => <span class="string">'Rate limit exceeded'</span>]);
    <span class="keyword">exit</span>;
}</pre>
            </div>

            <h3>Security Logging</h3>
            <p>Log access patterns to detect attack attempts:</p>
            <div class="code-block">
                <pre><span class="keyword">function</span> <span class="function">logSecurityEvent</span>(<span class="variable">$pdo</span>, <span class="variable">$userId</span>, <span class="variable">$eventType</span>, <span class="variable">$targetResource</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"
        INSERT INTO security_logs 
        (user_id, event_type, target_resource, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    "</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([
        <span class="variable">$userId</span>,
        <span class="variable">$eventType</span>,
        <span class="variable">$targetResource</span>,
        <span class="variable">$_SERVER</span>[<span class="string">'REMOTE_ADDR'</span>],
        <span class="variable">$_SERVER</span>[<span class="string">'HTTP_USER_AGENT'</span>]
    ]);
}

<span class="comment">// Trigger alert if many IDOR attempts detected</span>
<span class="keyword">if</span> (<span class="function">countRecentIdorAttempts</span>(<span class="variable">$userId</span>) > <span class="variable">5</span>) {
    <span class="function">triggerSecurityAlert</span>(<span class="variable">$userId</span>, <span class="string">'IDOR_ATTACK_PATTERN'</span>);
}</pre>
            </div>

            <h3>Use UUIDs (Secondary Measure)</h3>
            <p>While not a fix on its own, UUIDs make enumeration harder:</p>
            <div class="code-block">
                <pre><span class="comment">// Instead of: MT5-100001, MT5-100002, ...</span>
<span class="comment">// Use: acc_f47ac10b-58cc-4372-a567-0e02b2c3d479</span>

<span class="variable">$accountNumber</span> = <span class="string">'acc_'</span> . <span class="function">bin2hex</span>(<span class="function">random_bytes</span>(<span class="variable">16</span>));

<span class="comment">// ⚠️ IMPORTANT: UUIDs alone do NOT fix IDOR!</span>
<span class="comment">// Still need authorization checks - UUIDs just add obscurity</span></pre>
            </div>
        </section>

        <section class="section">
            <h2>✅ 6. Security Checklist</h2>
            
            <ul class="checklist">
                <li>Implement authorization checks for ALL resource access endpoints</li>
                <li>Use session-based user ID, never trust client-provided user identifiers</li>
                <li>Include user_id in database queries when fetching user-owned resources</li>
                <li>Log all authorization failures for security monitoring</li>
                <li>Implement rate limiting on sensitive API endpoints</li>
                <li>Consider using UUIDs instead of sequential IDs (as defense-in-depth)</li>
                <li>Conduct regular security audits focusing on IDOR vulnerabilities</li>
                <li>Write unit tests that specifically test authorization boundaries</li>
                <li>Use authorization middleware/decorators for consistent enforcement</li>
                <li>Document access control requirements for each API endpoint</li>
            </ul>
        </section>

        <section class="section">
            <h2>📚 7. References & Further Reading</h2>
            
            <ul>
                <li><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" target="_blank" style="color: #ffd700;">OWASP - Testing for IDOR</a></li>
                <li><a href="https://portswigger.net/web-security/access-control/idor" target="_blank" style="color: #ffd700;">PortSwigger - IDOR Vulnerabilities</a></li>
                <li><a href="https://cwe.mitre.org/data/definitions/639.html" target="_blank" style="color: #ffd700;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                <li><a href="https://hackerone.com/reports/993722" target="_blank" style="color: #ffd700;">HackerOne - Original Exness Report (Inspiration)</a></li>
            </ul>
        </section>

        <div class="nav-buttons">
            <a href="docs-technical.php" class="btn btn-secondary">← Technical Analysis</a>
            <a href="login.php" class="btn btn-primary">Try the Lab →</a>
        </div>
    </main>
</body>
</html>
