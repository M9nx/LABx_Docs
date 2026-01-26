<?php
// Lab 21: Documentation - Secure Code & Mitigation
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Code & Mitigation - IDOR Documentation | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .nav-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .nav-top a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-top a:hover { background: rgba(99, 102, 241, 0.2); }
        .doc-header {
            margin-bottom: 3rem;
        }
        .doc-header h1 {
            font-size: 2.25rem;
            background: linear-gradient(135deg, #22c55e, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .doc-header p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        .section {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .section h2 {
            color: #4ade80;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.4rem;
        }
        .section h3 {
            color: #e2e8f0;
            margin: 1.5rem 0 0.75rem;
            font-size: 1.1rem;
        }
        .section p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .code-block {
            background: #0d1117;
            border-radius: 12px;
            margin: 1rem 0;
            overflow: hidden;
        }
        .code-header {
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .code-header.secure {
            background: rgba(34, 197, 94, 0.2);
            border-bottom: 1px solid rgba(34, 197, 94, 0.3);
        }
        .code-header.secure .filename {
            color: #4ade80;
        }
        .code-header.vulnerable {
            background: rgba(239, 68, 68, 0.2);
            border-bottom: 1px solid rgba(239, 68, 68, 0.3);
        }
        .code-header.vulnerable .filename {
            color: #f87171;
        }
        .code-header .filename {
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
        }
        .code-header .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .code-header .badge.secure {
            background: #22c55e;
            color: white;
        }
        .code-header .badge.vulnerable {
            background: #ef4444;
            color: white;
        }
        .code-content {
            padding: 1rem;
            overflow-x: auto;
        }
        .code-content code {
            color: #e2e8f0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.7;
            white-space: pre;
        }
        .code-content .comment { color: #64748b; }
        .code-content .keyword { color: #c084fc; }
        .code-content .string { color: #86efac; }
        .code-content .variable { color: #f59e0b; }
        .code-content .secure { color: #4ade80; font-weight: 600; }
        .code-content .danger { color: #ef4444; font-weight: 600; }
        .code-content .highlight-secure {
            background: rgba(34, 197, 94, 0.15);
            margin: 0 -1rem;
            padding: 0 1rem;
            display: block;
        }
        .mitigation-card {
            background: rgba(34, 197, 94, 0.05);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .mitigation-card h4 {
            color: #4ade80;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .mitigation-card p {
            color: #86efac;
            margin: 0;
        }
        .mitigation-card ul {
            color: #86efac;
            padding-left: 1.5rem;
            margin-top: 0.5rem;
        }
        .mitigation-card li { margin-bottom: 0.25rem; }
        .approach-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .approach-item {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
        }
        .approach-item h5 {
            color: #a5b4fc;
            margin-bottom: 0.5rem;
        }
        .approach-item p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
        }
        .checklist {
            background: rgba(34, 197, 94, 0.05);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .checklist h4 {
            color: #4ade80;
            margin-bottom: 1rem;
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px dashed rgba(34, 197, 94, 0.2);
        }
        .checklist-item:last-child { border-bottom: none; }
        .checklist-item .check {
            color: #22c55e;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .checklist-item p {
            color: #94a3b8;
            margin: 0;
            font-size: 0.95rem;
        }
        .nav-pagination {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
        }
        .nav-pagination a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-pagination a:hover { background: rgba(99, 102, 241, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-top">
            <a href="docs.php">← Back to Docs</a>
            <a href="lab-description.php">📖 Lab Guide</a>
            <a href="login.php">🚀 Start Lab</a>
        </nav>
        
        <header class="doc-header">
            <h1>🛡️ 5. Secure Code & Mitigation</h1>
            <p>Learn how to properly fix IDOR vulnerabilities with secure coding patterns, authorization checks, and defense-in-depth strategies.</p>
        </header>
        
        <section class="section">
            <h2>✅ Fix #1: Server-Side Ownership Verification</h2>
            <p>The primary fix is to verify that the logged-in user owns the settings before allowing modifications:</p>
            
            <div class="code-block">
                <div class="code-header secure">
                    <span class="filename">settings.php (SECURE)</span>
                    <span class="badge secure">FIXED</span>
                </div>
                <div class="code-content">
                    <code><span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="variable">$settings_id</span> = <span class="variable">$_POST</span>[<span class="string">'settings_id'</span>] ?? <span class="variable">null</span>;
    
<span class="highlight-secure">    <span class="comment">// ✅ SECURE: Verify ownership before any modification</span>
    <span class="variable">$verify_sql</span> = <span class="string">"SELECT cs.id FROM column_settings cs
        JOIN stores s ON cs.store_id = s.id
        WHERE cs.id = :settings_id AND s.user_id = :user_id"</span>;
    <span class="variable">$verify_stmt</span> = <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$verify_sql</span>);
    <span class="variable">$verify_stmt</span>-><span class="variable">execute</span>([
        <span class="string">'settings_id'</span> => <span class="variable">$settings_id</span>,
        <span class="string">'user_id'</span> => <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]
    ]);
    
    <span class="keyword">if</span> (!<span class="variable">$verify_stmt</span>-><span class="variable">fetch</span>()) {
        <span class="variable">$error</span> = <span class="string">'Access denied: You do not have permission to modify these settings.'</span>;
    } <span class="keyword">else</span> {</span>
        <span class="comment">// Ownership verified - proceed with update</span>
        <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings SET ... WHERE id = :settings_id"</span>;
        <span class="comment">// ... execute update</span>
    }
}</code>
                </div>
            </div>
            
            <div class="mitigation-card">
                <h4>💡 Why This Works</h4>
                <p>The JOIN clause ensures that only settings belonging to the logged-in user can be selected. If the settings_id doesn't belong to the user, the query returns no results and the update is blocked.</p>
            </div>
        </section>
        
        <section class="section">
            <h2>✅ Fix #2: Don't Use User-Supplied IDs</h2>
            <p>An even better approach is to never accept the settings ID from user input at all:</p>
            
            <div class="code-block">
                <div class="code-header secure">
                    <span class="filename">settings.php (SECURE - No User ID)</span>
                    <span class="badge secure">BEST PRACTICE</span>
                </div>
                <div class="code-content">
                    <code><span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
<span class="highlight-secure">    <span class="comment">// ✅ SECURE: Get settings ID from session, not user input</span>
    <span class="variable">$user_id</span> = <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];
    
    <span class="comment">// Lookup the user's settings directly</span>
    <span class="variable">$settings_sql</span> = <span class="string">"SELECT cs.id FROM column_settings cs
        JOIN stores s ON cs.store_id = s.id
        WHERE s.user_id = :user_id"</span>;
    <span class="variable">$settings_stmt</span> = <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$settings_sql</span>);
    <span class="variable">$settings_stmt</span>-><span class="variable">execute</span>([<span class="string">'user_id'</span> => <span class="variable">$user_id</span>]);
    <span class="variable">$settings</span> = <span class="variable">$settings_stmt</span>-><span class="variable">fetch</span>();
    
    <span class="comment">// Use the server-derived settings ID</span>
    <span class="variable">$settings_id</span> = <span class="variable">$settings</span>[<span class="string">'id'</span>];</span>
    
    <span class="comment">// Now update with guaranteed ownership</span>
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings SET ... WHERE id = :settings_id"</span>;
    <span class="comment">// ...</span>
}</code>
                </div>
            </div>
            
            <div class="mitigation-card">
                <h4>💡 Why This Is Better</h4>
                <p>By deriving the settings ID from the authenticated session rather than user input, there's no opportunity for manipulation. The user can't specify a different ID because it's not accepted as input at all.</p>
            </div>
        </section>
        
        <section class="section">
            <h2>✅ Fix #3: Include Ownership in UPDATE Query</h2>
            <p>Add ownership verification directly in the UPDATE WHERE clause:</p>
            
            <div class="code-block">
                <div class="code-header secure">
                    <span class="filename">settings.php (SECURE - WHERE Clause)</span>
                    <span class="badge secure">ALTERNATIVE</span>
                </div>
                <div class="code-content">
                    <code><span class="comment">// ✅ SECURE: Include ownership check in the UPDATE itself</span>
<span class="highlight-secure"><span class="variable">$sql</span> = <span class="string">"UPDATE column_settings cs
    JOIN stores s ON cs.store_id = s.id
    SET cs.show_grade = :show_grade,
        cs.show_title = :show_title,
        <span class="comment">/* ... other columns */</span>
        cs.updated_at = NOW()
    WHERE cs.id = :settings_id 
    AND s.user_id = :user_id"</span>;</span>

<span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$sql</span>);
<span class="variable">$stmt</span>-><span class="variable">execute</span>(<span class="variable">array_merge</span>(<span class="variable">$columns</span>, [
    <span class="string">'settings_id'</span> => <span class="variable">$settings_id</span>,
<span class="highlight-secure">    <span class="string">'user_id'</span> => <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]</span>
]));

<span class="comment">// Check if any rows were actually updated</span>
<span class="keyword">if</span> (<span class="variable">$stmt</span>-><span class="variable">rowCount</span>() === <span class="variable">0</span>) {
    <span class="variable">$error</span> = <span class="string">'Settings not found or access denied.'</span>;
}</code>
                </div>
            </div>
        </section>
        
        <section class="section">
            <h2>🔐 Defense in Depth Strategies</h2>
            <p>Beyond the primary fix, implement multiple layers of protection:</p>
            
            <div class="approach-grid">
                <div class="approach-item">
                    <h5>🆔 Use UUIDs</h5>
                    <p>Replace sequential IDs with random UUIDs to prevent enumeration (but don't rely on this alone)</p>
                </div>
                <div class="approach-item">
                    <h5>🔒 Rate Limiting</h5>
                    <p>Limit API requests to prevent mass enumeration attempts</p>
                </div>
                <div class="approach-item">
                    <h5>📝 Audit Logging</h5>
                    <p>Log all settings modifications with user IDs for forensic analysis</p>
                </div>
                <div class="approach-item">
                    <h5>🚨 Anomaly Detection</h5>
                    <p>Alert on unusual patterns like rapid settings changes</p>
                </div>
            </div>
        </section>
        
        <section class="section">
            <h2>📋 Security Checklist</h2>
            
            <div class="checklist">
                <h4>Before Deploying Settings Update Functionality:</h4>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Does the code verify the user is authenticated? (Authentication)</p>
                </div>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Does the code verify the user owns the resource they're modifying? (Authorization)</p>
                </div>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Is the ownership check performed server-side, not client-side?</p>
                </div>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Is user input validated and sanitized before database operations?</p>
                </div>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Are database queries parameterized to prevent SQL injection?</p>
                </div>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Are all access attempts logged for audit purposes?</p>
                </div>
                <div class="checklist-item">
                    <span class="check">✓</span>
                    <p>Does the system fail securely if authorization check fails?</p>
                </div>
            </div>
        </section>
        
        <section class="section">
            <h2>🏗️ Architectural Recommendations</h2>
            
            <h3>1. Use an Authorization Layer</h3>
            <p>Implement a centralized authorization service that all endpoints must use:</p>
            
            <div class="code-block">
                <div class="code-header secure">
                    <span class="filename">AuthorizationService.php</span>
                    <span class="badge secure">PATTERN</span>
                </div>
                <div class="code-content">
                    <code><span class="keyword">class</span> <span class="variable">AuthorizationService</span> {
    <span class="keyword">public static function</span> <span class="variable">canModifySettings</span>(<span class="variable">$userId</span>, <span class="variable">$settingsId</span>): <span class="keyword">bool</span> {
        <span class="keyword">global</span> <span class="variable">$pdo</span>;
        
        <span class="variable">$sql</span> = <span class="string">"SELECT 1 FROM column_settings cs
            JOIN stores s ON cs.store_id = s.id
            WHERE cs.id = :settings_id AND s.user_id = :user_id"</span>;
        
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$sql</span>);
        <span class="variable">$stmt</span>-><span class="variable">execute</span>([<span class="string">'settings_id'</span> => <span class="variable">$settingsId</span>, <span class="string">'user_id'</span> => <span class="variable">$userId</span>]);
        
        <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="variable">fetch</span>() !== <span class="keyword">false</span>;
    }
}

<span class="comment">// Usage in settings.php</span>
<span class="keyword">if</span> (!<span class="variable">AuthorizationService</span>::<span class="variable">canModifySettings</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>], <span class="variable">$settings_id</span>)) {
    <span class="variable">http_response_code</span>(<span class="variable">403</span>);
    <span class="variable">exit</span>(<span class="string">'Forbidden'</span>);
}</code>
                </div>
            </div>
            
            <h3>2. Policy-Based Access Control</h3>
            <p>For complex applications, consider implementing RBAC (Role-Based Access Control) or ABAC (Attribute-Based Access Control) frameworks.</p>
        </section>
        
        <nav class="nav-pagination">
            <a href="docs-vulnerable-code.php">← Previous: Vulnerable Code</a>
            <a href="docs-comparison.php">Next: Code Comparison →</a>
        </nav>
    </div>
</body>
</html>
