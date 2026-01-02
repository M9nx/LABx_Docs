<?php
// Lab 30: Stocky Inventory App - Mitigation Guide Documentation
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitigation Guide - Lab 30: Low Stock Settings IDOR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #12081a 50%, #0a0a0f 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .nav-bar {
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(124, 58, 237, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-logo { font-size: 1.4rem; font-weight: bold; color: #a78bfa; text-decoration: none; }
        .nav-logo span { color: #7c3aed; }
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #888; text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: #a78bfa; }
        .docs-layout { display: flex; min-height: calc(100vh - 60px); }
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(124, 58, 237, 0.2);
            padding: 2rem 0;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        .sidebar-header { padding: 0 1.5rem 1.5rem; border-bottom: 1px solid rgba(124, 58, 237, 0.1); margin-bottom: 1rem; }
        .sidebar-header h3 { color: #a78bfa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem;
            color: #888; text-decoration: none; font-size: 0.9rem; transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover { background: rgba(124, 58, 237, 0.1); color: #a78bfa; }
        .sidebar-nav a.active { background: rgba(124, 58, 237, 0.15); color: #a78bfa; border-left-color: #7c3aed; }
        .sidebar-nav .icon { width: 20px; text-align: center; }
        .sidebar-section { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(124, 58, 237, 0.1); }
        .sidebar-section-title { color: #666; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 0 1.5rem; margin-bottom: 0.75rem; }
        .main-content { flex: 1; padding: 2rem 3rem; max-width: 900px; }
        .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: #888; text-decoration: none; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .back-link:hover { color: #a78bfa; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 2rem; background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.75rem; }
        .page-header p { color: #888; font-size: 1.1rem; }
        .section { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; }
        .section h2 { color: #a78bfa; font-size: 1.25rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .section h3 { color: #7c3aed; font-size: 1rem; margin: 1.5rem 0 0.75rem 0; }
        .section p { color: #aaa; line-height: 1.7; margin-bottom: 1rem; }
        .section ul, .section ol { color: #aaa; margin-left: 1.5rem; line-height: 1.8; margin-bottom: 1rem; }
        .section li { margin-bottom: 0.5rem; }
        .section code { background: rgba(124, 58, 237, 0.15); color: #c4b5fd; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.85rem; }
        .code-block { background: #0d1117; border: 1px solid rgba(124, 58, 237, 0.2); border-radius: 8px; padding: 1.25rem; margin: 1rem 0; overflow-x: auto; }
        .code-block pre { margin: 0; color: #e0e0e0; font-size: 0.85rem; line-height: 1.6; white-space: pre; }
        .code-block .comment { color: #6a9955; }
        .code-block .keyword { color: #c586c0; }
        .code-block .function { color: #dcdcaa; }
        .code-block .string { color: #ce9178; }
        .code-block .variable { color: #9cdcfe; }
        .code-block .number { color: #b5cea8; }
        .code-block .success { color: #22c55e; }
        .code-title { background: rgba(124, 58, 237, 0.2); color: #c4b5fd; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-size: 0.8rem; font-weight: 600; margin-top: 1rem; }
        .code-title.secure { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .code-title.vulnerable { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .code-block.with-title { border-radius: 0 0 8px 8px; margin-top: 0; }
        .warning-box { background: rgba(255, 0, 0, 0.08); border-left: 4px solid #ff4444; border-radius: 0 8px 8px 0; padding: 1rem 1rem 1rem 1.25rem; margin: 1rem 0; }
        .warning-box strong { color: #ff6b6b; }
        .success-box { background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e; border-radius: 0 8px 8px 0; padding: 1rem 1rem 1rem 1.25rem; margin: 1rem 0; }
        .success-box strong { color: #22c55e; }
        .info-box { background: rgba(124, 58, 237, 0.1); border-left: 4px solid #7c3aed; border-radius: 0 8px 8px 0; padding: 1rem 1rem 1rem 1.25rem; margin: 1rem 0; }
        .info-box strong { color: #a78bfa; }
        .comparison-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1rem 0; }
        @media (max-width: 768px) { .comparison-grid { grid-template-columns: 1fr; } }
        .comparison-item { background: rgba(0, 0, 0, 0.2); border-radius: 8px; padding: 1rem; }
        .comparison-item.vulnerable { border: 1px solid rgba(239, 68, 68, 0.3); }
        .comparison-item.secure { border: 1px solid rgba(34, 197, 94, 0.3); }
        .comparison-item h4 { margin-bottom: 0.75rem; font-size: 0.9rem; }
        .comparison-item.vulnerable h4 { color: #ef4444; }
        .comparison-item.secure h4 { color: #22c55e; }
        .checklist { list-style: none; margin-left: 0; }
        .checklist li { padding: 0.5rem 0; padding-left: 2rem; position: relative; }
        .checklist li::before { content: '☐'; position: absolute; left: 0; color: #7c3aed; }
        .nav-buttons { display: flex; gap: 1rem; justify-content: space-between; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(124, 58, 237, 0.1); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4); }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); color: #a78bfa; border: 1px solid rgba(124, 58, 237, 0.3); }
        .btn-secondary:hover { background: rgba(124, 58, 237, 0.1); }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">📦 <span>Stocky</span></a>
        <div class="nav-links">
            <a href="index.php">🏠 Lab Home</a>
            <a href="lab-description.php">📋 Description</a>
            <a href="docs.php" class="active">📚 Documentation</a>
            <a href="login.php">🔐 Login</a>
        </div>
    </nav>
    
    <div class="docs-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>📚 Documentation</h3>
            </div>
            <ul class="sidebar-nav">
                <li><a href="docs.php"><span class="icon">📖</span> Overview</a></li>
                <li><a href="docs.php#what-is-idor"><span class="icon">❓</span> What is IDOR?</a></li>
                <li><a href="docs.php#vulnerability"><span class="icon">🔓</span> The Vulnerability</a></li>
                <li><a href="docs.php#impact"><span class="icon">⚠️</span> Security Impact</a></li>
            </ul>
            <div class="sidebar-section">
                <div class="sidebar-section-title">Exploitation</div>
                <ul class="sidebar-nav">
                    <li><a href="docs-technical.php"><span class="icon">🔍</span> Technical Deep Dive</a></li>
                    <li><a href="docs-technical.php#walkthrough"><span class="icon">👣</span> Step-by-Step</a></li>
                    <li><a href="docs-technical.php#code-analysis"><span class="icon">💻</span> Code Analysis</a></li>
                    <li><a href="docs-technical.php#attack-vectors"><span class="icon">⚔️</span> Attack Vectors</a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-section-title">Defense</div>
                <ul class="sidebar-nav">
                    <li><a href="docs-mitigation.php" class="active"><span class="icon">🛡️</span> Mitigation Guide</a></li>
                    <li><a href="#secure-code"><span class="icon">✅</span> Secure Code</a></li>
                    <li><a href="#best-practices"><span class="icon">📋</span> Best Practices</a></li>
                    <li><a href="#testing"><span class="icon">🧪</span> Testing Guide</a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-section-title">Resources</div>
                <ul class="sidebar-nav">
                    <li><a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" target="_blank"><span class="icon">🌐</span> OWASP BOLA</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" target="_blank"><span class="icon">📚</span> Auth Cheatsheet</a></li>
                </ul>
            </div>
        </aside>
        
        <main class="main-content">
            <a href="docs-technical.php" class="back-link">← Back to Technical Deep Dive</a>
            
            <div class="page-header">
                <h1>🛡️ Mitigation Guide</h1>
                <p>How to fix IDOR vulnerabilities with secure code examples and best practices</p>
            </div>
            
            <!-- Secure Code Section -->
            <div class="section" id="secure-code">
                <h2>✅ Secure Code Implementation</h2>
                <p>The fix is straightforward: always verify that the requesting user owns the resource they're trying to access or modify.</p>
                
                <div class="comparison-grid">
                    <div class="comparison-item vulnerable">
                        <h4>❌ Vulnerable Code</h4>
                        <p style="font-size: 0.85rem; color: #888;">Uses user-supplied ID directly</p>
                    </div>
                    <div class="comparison-item secure">
                        <h4>✓ Secure Code</h4>
                        <p style="font-size: 0.85rem; color: #888;">Verifies ownership before access</p>
                    </div>
                </div>
                
                <div class="code-title vulnerable">❌ Vulnerable: Direct ID Usage</div>
                <div class="code-block with-title">
                    <pre><span class="comment">// VULNERABLE: Uses settings_id from POST directly</span>
<span class="variable">$settingsId</span> = <span class="variable">$_POST</span>[<span class="string">'settings_id'</span>];

<span class="variable">$sql</span> = <span class="string">"UPDATE settings_for_low_stock_variants 
       SET show_product_title = ? 
       WHERE id = ?"</span>;
<span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$value</span>, <span class="variable">$settingsId</span>]);</pre>
                </div>
                
                <div class="code-title secure">✓ Secure: Ownership Verification</div>
                <div class="code-block with-title">
                    <pre><span class="comment">// SECURE: Only update settings that belong to the current user</span>
<span class="variable">$userId</span> = <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];

<span class="comment">// Method 1: Include user_id in WHERE clause</span>
<span class="variable">$sql</span> = <span class="string">"UPDATE settings_for_low_stock_variants 
       SET show_product_title = ? 
       WHERE id = ? <span class="success">AND user_id = ?</span>"</span>;
<span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$value</span>, <span class="variable">$settingsId</span>, <span class="success">$userId</span>]);

<span class="comment">// Check if update actually happened</span>
<span class="keyword">if</span> (<span class="variable">$stmt</span>-><span class="function">rowCount</span>() === <span class="number">0</span>) {
    <span class="keyword">throw new</span> <span class="function">Exception</span>(<span class="string">'Access denied'</span>);
}</pre>
                </div>
                
                <div class="code-title secure">✓ Secure: Pre-fetch and Verify</div>
                <div class="code-block with-title">
                    <pre><span class="comment">// SECURE: Verify ownership before any operation</span>
<span class="keyword">function</span> <span class="function">verifySettingsOwnership</span>(<span class="variable">$pdo</span>, <span class="variable">$settingsId</span>, <span class="variable">$userId</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(
        <span class="string">"SELECT id FROM settings_for_low_stock_variants 
         WHERE id = ? AND user_id = ?"</span>
    );
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$settingsId</span>, <span class="variable">$userId</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>() !== <span class="keyword">false</span>;
}

<span class="comment">// Usage</span>
<span class="keyword">if</span> (!<span class="function">verifySettingsOwnership</span>(<span class="variable">$pdo</span>, <span class="variable">$settingsId</span>, <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
    <span class="function">http_response_code</span>(<span class="number">403</span>);
    <span class="keyword">die</span>(<span class="string">'Access denied: You do not own this resource'</span>);
}

<span class="comment">// Safe to proceed with update</span></pre>
                </div>
                
                <div class="success-box">
                    <strong>✓ Key Principle:</strong> Never trust user input for resource identification without verifying ownership against the authenticated user's session.
                </div>
            </div>
            
            <!-- Best Practices Section -->
            <div class="section" id="best-practices">
                <h2>📋 Best Practices for Preventing IDOR</h2>
                
                <h3>1. Always Verify Ownership</h3>
                <p>Every request to access or modify a resource must verify that the authenticated user has permission to perform that action on that specific resource.</p>
                
                <h3>2. Use Indirect References</h3>
                <p>Instead of exposing database IDs, use indirect references that are mapped per-user session:</p>
                <div class="code-block">
                    <pre><span class="comment">// Instead of: settings.php?id=123</span>
<span class="comment">// Use session-mapped references</span>
<span class="variable">$_SESSION</span>[<span class="string">'settings_map'</span>] = [
    <span class="string">'my-settings'</span> => <span class="number">123</span>  <span class="comment">// Only accessible to this user</span>
];</pre>
                </div>
                
                <h3>3. Implement Access Control Lists (ACL)</h3>
                <p>Use a centralized authorization system that checks permissions for every resource access:</p>
                <div class="code-block">
                    <pre><span class="keyword">class</span> <span class="function">Authorization</span> {
    <span class="keyword">public static function</span> <span class="function">canAccess</span>(<span class="variable">$user</span>, <span class="variable">$resource</span>, <span class="variable">$action</span>) {
        <span class="comment">// Check if user has permission for this resource/action</span>
        <span class="keyword">return</span> <span class="variable">$resource</span>-><span class="function">owner_id</span> === <span class="variable">$user</span>-><span class="function">id</span>
            || <span class="variable">$user</span>-><span class="function">hasRole</span>(<span class="string">'admin'</span>);
    }
}</pre>
                </div>
                
                <h3>4. Use UUIDs Instead of Sequential IDs</h3>
                <p>UUIDs make enumeration attacks impractical:</p>
                <div class="code-block">
                    <pre><span class="comment">// Hard to guess: 550e8400-e29b-41d4-a716-446655440000</span>
<span class="comment">// vs Easy to enumerate: 1, 2, 3, 4...</span></pre>
                </div>
                
                <h3>5. Log Access Attempts</h3>
                <p>Log all access attempts, especially failures, to detect potential IDOR exploitation:</p>
                <div class="code-block">
                    <pre><span class="keyword">if</span> (!<span class="function">verifyOwnership</span>(<span class="variable">$resourceId</span>, <span class="variable">$userId</span>)) {
    <span class="function">logSecurityEvent</span>(<span class="string">'IDOR_ATTEMPT'</span>, [
        <span class="string">'user_id'</span> => <span class="variable">$userId</span>,
        <span class="string">'resource_id'</span> => <span class="variable">$resourceId</span>,
        <span class="string">'ip'</span> => <span class="variable">$_SERVER</span>[<span class="string">'REMOTE_ADDR'</span>]
    ]);
}</pre>
                </div>
            </div>
            
            <!-- Testing Section -->
            <div class="section" id="testing">
                <h2>🧪 Testing Guide</h2>
                <p>Use this checklist to test for IDOR vulnerabilities in your applications:</p>
                
                <ul class="checklist">
                    <li>Create two test accounts with different resources</li>
                    <li>Login as User A and capture requests that access resources</li>
                    <li>Identify parameters containing resource IDs (id, user_id, settings_id, etc.)</li>
                    <li>Replace User A's resource ID with User B's resource ID</li>
                    <li>Send the modified request - does it succeed?</li>
                    <li>Test all CRUD operations (Create, Read, Update, Delete)</li>
                    <li>Test both GET and POST parameters</li>
                    <li>Test API endpoints and direct URL access</li>
                    <li>Try ID enumeration (1, 2, 3...) to discover resources</li>
                    <li>Check if error messages leak information about existence</li>
                </ul>
                
                <div class="info-box">
                    <strong>💡 Automated Testing:</strong> Tools like Burp Suite's Autorize extension can help automate IDOR testing by replaying requests with different user sessions.
                </div>
            </div>
            
            <div class="nav-buttons">
                <a href="docs-technical.php" class="btn btn-secondary">← Technical Deep Dive</a>
                <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            </div>
        </main>
    </div>
</body>
</html>
