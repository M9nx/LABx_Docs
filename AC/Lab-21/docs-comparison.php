<?php
// Lab 21: Documentation - Code Comparison
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Comparison - IDOR Documentation | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 1200px; margin: 0 auto; }
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
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
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
        .comparison-section {
            margin-bottom: 3rem;
        }
        .comparison-section h2 {
            color: #a5b4fc;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 900px) {
            .comparison-grid { grid-template-columns: 1fr; }
        }
        .code-panel {
            background: #0d1117;
            border-radius: 12px;
            overflow: hidden;
        }
        .code-panel.vulnerable {
            border: 2px solid rgba(239, 68, 68, 0.5);
        }
        .code-panel.secure {
            border: 2px solid rgba(34, 197, 94, 0.5);
        }
        .panel-header {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .panel-header.vulnerable {
            background: rgba(239, 68, 68, 0.15);
            border-bottom: 1px solid rgba(239, 68, 68, 0.3);
        }
        .panel-header.secure {
            background: rgba(34, 197, 94, 0.15);
            border-bottom: 1px solid rgba(34, 197, 94, 0.3);
        }
        .panel-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }
        .panel-header.vulnerable .panel-title { color: #f87171; }
        .panel-header.secure .panel-title { color: #4ade80; }
        .panel-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .panel-badge.vulnerable {
            background: #ef4444;
            color: white;
        }
        .panel-badge.secure {
            background: #22c55e;
            color: white;
        }
        .code-content {
            padding: 1rem;
            overflow-x: auto;
        }
        .code-content code {
            color: #e2e8f0;
            font-family: 'Consolas', monospace;
            font-size: 0.8rem;
            line-height: 1.6;
            white-space: pre;
        }
        .code-content .comment { color: #64748b; }
        .code-content .keyword { color: #c084fc; }
        .code-content .string { color: #86efac; }
        .code-content .variable { color: #f59e0b; }
        .code-content .danger { color: #ef4444; font-weight: 600; }
        .code-content .secure { color: #4ade80; font-weight: 600; }
        .code-content .highlight-danger {
            background: rgba(239, 68, 68, 0.15);
            margin: 0 -1rem;
            padding: 0 1rem;
            display: block;
        }
        .code-content .highlight-secure {
            background: rgba(34, 197, 94, 0.15);
            margin: 0 -1rem;
            padding: 0 1rem;
            display: block;
        }
        .diff-annotation {
            padding: 1rem;
            border-top: 1px dashed rgba(99, 102, 241, 0.3);
        }
        .diff-annotation.vulnerable {
            background: rgba(239, 68, 68, 0.05);
        }
        .diff-annotation.secure {
            background: rgba(34, 197, 94, 0.05);
        }
        .diff-annotation h4 {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .diff-annotation.vulnerable h4 { color: #f87171; }
        .diff-annotation.secure h4 { color: #4ade80; }
        .diff-annotation p {
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 0;
        }
        .key-differences {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
        }
        .key-differences h3 {
            color: #a5b4fc;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        .diff-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px dashed rgba(99, 102, 241, 0.2);
        }
        .diff-item:last-child { border-bottom: none; }
        .diff-num {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .diff-text h5 {
            color: #e2e8f0;
            margin-bottom: 0.25rem;
        }
        .diff-text p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
        }
        .summary-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .summary-box h4 {
            color: #a5b4fc;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .summary-box p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 0.5rem;
        }
        .summary-box strong { color: #e2e8f0; }
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
            <h1>⚖️ 6. Code Comparison</h1>
            <p>A side-by-side comparison of vulnerable and secure code implementations, highlighting the critical differences that prevent IDOR attacks.</p>
        </header>
        
        <section class="comparison-section">
            <h2>📋 Comparison 1: Settings Update Handler</h2>
            
            <div class="comparison-grid">
                <div class="code-panel vulnerable">
                    <div class="panel-header vulnerable">
                        <span class="panel-title">❌ Vulnerable Code</span>
                        <span class="panel-badge vulnerable">INSECURE</span>
                    </div>
                    <div class="code-content">
                        <code><span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
<span class="highlight-danger">    <span class="comment">// Directly use user-supplied ID</span>
    <span class="variable">$settings_id</span> = <span class="danger">$_POST['settings_id']</span>;</span>
    
    <span class="comment">// Build update data</span>
    <span class="variable">$columns</span> = [
        <span class="string">'show_grade'</span> => <span class="keyword">isset</span>(<span class="variable">$_POST</span>[<span class="string">'show_grade'</span>]) ? <span class="variable">1</span> : <span class="variable">0</span>,
        <span class="comment">// ... other columns</span>
    ];
    
<span class="highlight-danger">    <span class="comment">// No ownership check!</span>
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings 
        SET ... 
        WHERE id = :settings_id"</span>;</span>
    
    <span class="variable">$stmt</span>-><span class="variable">execute</span>([
        <span class="string">'settings_id'</span> => <span class="danger">$settings_id</span>
    ]);
}</code>
                    </div>
                    <div class="diff-annotation vulnerable">
                        <h4>⚠️ Problems</h4>
                        <p>Accepts any settings_id from user input and updates it without verifying ownership.</p>
                    </div>
                </div>
                
                <div class="code-panel secure">
                    <div class="panel-header secure">
                        <span class="panel-title">✅ Secure Code</span>
                        <span class="panel-badge secure">FIXED</span>
                    </div>
                    <div class="code-content">
                        <code><span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
<span class="highlight-secure">    <span class="comment">// Get ID from trusted session</span>
    <span class="variable">$user_id</span> = <span class="secure">$_SESSION['user_id']</span>;</span>
    
    <span class="comment">// Build update data</span>
    <span class="variable">$columns</span> = [
        <span class="string">'show_grade'</span> => <span class="keyword">isset</span>(<span class="variable">$_POST</span>[<span class="string">'show_grade'</span>]) ? <span class="variable">1</span> : <span class="variable">0</span>,
        <span class="comment">// ... other columns</span>
    ];
    
<span class="highlight-secure">    <span class="comment">// Include ownership in query</span>
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings cs
        JOIN stores s ON cs.store_id = s.id
        SET ... 
        WHERE cs.id = :settings_id 
        <span class="secure">AND s.user_id = :user_id</span>"</span>;</span>
    
    <span class="variable">$stmt</span>-><span class="variable">execute</span>([
        <span class="string">'settings_id'</span> => <span class="variable">$settings_id</span>,
<span class="highlight-secure">        <span class="string">'user_id'</span> => <span class="secure">$user_id</span></span>
    ]);
}</code>
                    </div>
                    <div class="diff-annotation secure">
                        <h4>✓ Solution</h4>
                        <p>Uses session-based user_id and adds ownership check in the WHERE clause.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="comparison-section">
            <h2>📋 Comparison 2: Form Implementation</h2>
            
            <div class="comparison-grid">
                <div class="code-panel vulnerable">
                    <div class="panel-header vulnerable">
                        <span class="panel-title">❌ Vulnerable Form</span>
                        <span class="panel-badge vulnerable">INSECURE</span>
                    </div>
                    <div class="code-content">
                        <code><span class="keyword">&lt;form</span> <span class="variable">method</span>=<span class="string">"POST"</span><span class="keyword">&gt;</span>
<span class="highlight-danger">    <span class="comment">&lt;!-- User can modify this! --&gt;</span>
    <span class="keyword">&lt;input</span> <span class="variable">type</span>=<span class="string">"hidden"</span> 
           <span class="variable">name</span>=<span class="string">"settings_id"</span> 
           <span class="variable">value</span>=<span class="string">"<span class="danger">&lt;?= $settings['id'] ?&gt;</span>"</span><span class="keyword">&gt;</span></span>
    
    <span class="keyword">&lt;label&gt;</span>
        <span class="keyword">&lt;input</span> <span class="variable">type</span>=<span class="string">"checkbox"</span> 
               <span class="variable">name</span>=<span class="string">"show_grade"</span><span class="keyword">&gt;</span>
        Show Grade
    <span class="keyword">&lt;/label&gt;</span>
    
    <span class="keyword">&lt;button</span> <span class="variable">type</span>=<span class="string">"submit"</span><span class="keyword">&gt;</span>
        Save
    <span class="keyword">&lt;/button&gt;</span>
<span class="keyword">&lt;/form&gt;</span></code>
                    </div>
                    <div class="diff-annotation vulnerable">
                        <h4>⚠️ Problems</h4>
                        <p>Hidden field with settings_id can be easily modified using browser dev tools.</p>
                    </div>
                </div>
                
                <div class="code-panel secure">
                    <div class="panel-header secure">
                        <span class="panel-title">✅ Secure Form</span>
                        <span class="panel-badge secure">FIXED</span>
                    </div>
                    <div class="code-content">
                        <code><span class="keyword">&lt;form</span> <span class="variable">method</span>=<span class="string">"POST"</span><span class="keyword">&gt;</span>
<span class="highlight-secure">    <span class="comment">&lt;!-- No settings_id needed --&gt;</span>
    <span class="comment">&lt;!-- Server derives it from session --&gt;</span></span>
    
    <span class="keyword">&lt;label&gt;</span>
        <span class="keyword">&lt;input</span> <span class="variable">type</span>=<span class="string">"checkbox"</span> 
               <span class="variable">name</span>=<span class="string">"show_grade"</span><span class="keyword">&gt;</span>
        Show Grade
    <span class="keyword">&lt;/label&gt;</span>
    
<span class="highlight-secure">    <span class="comment">&lt;!-- CSRF token for extra security --&gt;</span>
    <span class="keyword">&lt;input</span> <span class="variable">type</span>=<span class="string">"hidden"</span> 
           <span class="variable">name</span>=<span class="string">"csrf_token"</span> 
           <span class="variable">value</span>=<span class="string">"<span class="secure">&lt;?= $csrf_token ?&gt;</span>"</span><span class="keyword">&gt;</span></span>
    
    <span class="keyword">&lt;button</span> <span class="variable">type</span>=<span class="string">"submit"</span><span class="keyword">&gt;</span>
        Save
    <span class="keyword">&lt;/button&gt;</span>
<span class="keyword">&lt;/form&gt;</span></code>
                    </div>
                    <div class="diff-annotation secure">
                        <h4>✓ Solution</h4>
                        <p>No client-side ID at all. Server looks up settings based on authenticated session.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="comparison-section">
            <h2>📋 Comparison 3: Ownership Verification</h2>
            
            <div class="comparison-grid">
                <div class="code-panel vulnerable">
                    <div class="panel-header vulnerable">
                        <span class="panel-title">❌ No Verification</span>
                        <span class="panel-badge vulnerable">INSECURE</span>
                    </div>
                    <div class="code-content">
                        <code><span class="keyword">function</span> <span class="variable">updateSettings</span>(<span class="variable">$id</span>, <span class="variable">$data</span>) {
<span class="highlight-danger">    <span class="comment">// No ownership check at all!</span></span>
    
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings 
        SET show_grade = :grade
        WHERE id = :id"</span>;
    
    <span class="keyword">return</span> <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$sql</span>)
              -><span class="variable">execute</span>([
        <span class="string">'id'</span> => <span class="danger">$id</span>,
        <span class="string">'grade'</span> => <span class="variable">$data</span>[<span class="string">'grade'</span>]
    ]);
}</code>
                    </div>
                    <div class="diff-annotation vulnerable">
                        <h4>⚠️ Problems</h4>
                        <p>Function accepts any ID and updates it. No user context considered.</p>
                    </div>
                </div>
                
                <div class="code-panel secure">
                    <div class="panel-header secure">
                        <span class="panel-title">✅ With Verification</span>
                        <span class="panel-badge secure">FIXED</span>
                    </div>
                    <div class="code-content">
                        <code><span class="keyword">function</span> <span class="variable">updateSettings</span>(<span class="variable">$id</span>, <span class="variable">$data</span>, <span class="secure">$userId</span>) {
<span class="highlight-secure">    <span class="comment">// First verify ownership</span>
    <span class="keyword">if</span> (!<span class="variable">verifyOwnership</span>(<span class="variable">$id</span>, <span class="secure">$userId</span>)) {
        <span class="keyword">throw new</span> <span class="variable">Exception</span>(<span class="string">'Access denied'</span>);
    }</span>
    
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings cs
        JOIN stores s ON cs.store_id = s.id
        SET cs.show_grade = :grade
        WHERE cs.id = :id
<span class="highlight-secure">        AND s.user_id = :user_id"</span>;</span>
    
    <span class="keyword">return</span> <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$sql</span>)
              -><span class="variable">execute</span>([
        <span class="string">'id'</span> => <span class="variable">$id</span>,
        <span class="string">'grade'</span> => <span class="variable">$data</span>[<span class="string">'grade'</span>],
<span class="highlight-secure">        <span class="string">'user_id'</span> => <span class="secure">$userId</span></span>
    ]);
}</code>
                    </div>
                    <div class="diff-annotation secure">
                        <h4>✓ Solution</h4>
                        <p>Requires userId parameter and includes it in both verification and update queries.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="key-differences">
            <h3>🔑 Key Differences Summary</h3>
            
            <div class="diff-item">
                <span class="diff-num">1</span>
                <div class="diff-text">
                    <h5>Input Source</h5>
                    <p>Vulnerable: Uses $_POST['settings_id'] directly. Secure: Uses $_SESSION['user_id'] and derives settings from database.</p>
                </div>
            </div>
            
            <div class="diff-item">
                <span class="diff-num">2</span>
                <div class="diff-text">
                    <h5>Authorization Check</h5>
                    <p>Vulnerable: No ownership verification. Secure: Explicit check before modification or ownership constraint in query.</p>
                </div>
            </div>
            
            <div class="diff-item">
                <span class="diff-num">3</span>
                <div class="diff-text">
                    <h5>Query Structure</h5>
                    <p>Vulnerable: Simple WHERE id = ?. Secure: JOIN with user table and WHERE ... AND user_id = ?.</p>
                </div>
            </div>
            
            <div class="diff-item">
                <span class="diff-num">4</span>
                <div class="diff-text">
                    <h5>Form Design</h5>
                    <p>Vulnerable: Passes sensitive ID in hidden field. Secure: No client-side ID, server determines from session.</p>
                </div>
            </div>
        </div>
        
        <div class="summary-box">
            <h4>📖 Key Takeaway</h4>
            <p>The difference between vulnerable and secure code is often just a few lines - specifically the <strong>ownership verification</strong>. Always ask: "Does this user have permission to access/modify this specific resource?" and verify the answer on the server side, never trusting client input for authorization decisions.</p>
            <p>Remember: <strong>Authentication</strong> (who you are) is not the same as <strong>Authorization</strong> (what you can do). Both must be checked.</p>
        </div>
        
        <nav class="nav-pagination">
            <a href="docs-mitigation.php">← Previous: Mitigation</a>
            <a href="docs.php">Back to Docs Home →</a>
        </nav>
    </div>
</body>
</html>
