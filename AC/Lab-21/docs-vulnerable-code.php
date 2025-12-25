<?php
// Lab 21: Documentation - Vulnerable Code Analysis
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Code Analysis - IDOR Documentation | Lab 21</title>
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
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
            color: #f87171;
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
            background: rgba(239, 68, 68, 0.2);
            border-bottom: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .code-header .filename {
            color: #f87171;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
        }
        .code-header .line-info {
            color: #64748b;
            font-size: 0.8rem;
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
        .code-content .danger { color: #ef4444; font-weight: 600; }
        .code-content .highlight-line {
            background: rgba(239, 68, 68, 0.15);
            margin: 0 -1rem;
            padding: 0 1rem;
            display: block;
        }
        .annotation {
            background: rgba(239, 68, 68, 0.1);
            border-left: 3px solid #ef4444;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .annotation h4 {
            color: #f87171;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .annotation p {
            color: #fca5a5;
            margin: 0;
            font-size: 0.95rem;
        }
        .file-structure {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .file-structure h4 {
            color: #a5b4fc;
            margin-bottom: 1rem;
        }
        .file-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            color: #94a3b8;
        }
        .file-item.vulnerable {
            color: #f87171;
            font-weight: 600;
        }
        .vulnerability-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .warning-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #f87171;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .warning-box ul {
            color: #fca5a5;
            padding-left: 1.5rem;
        }
        .warning-box li { margin-bottom: 0.5rem; }
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
            <h1>🔍 4. Vulnerable Code Analysis</h1>
            <p>A detailed line-by-line breakdown of the vulnerable settings.php file, highlighting exactly where and why the IDOR vulnerability exists.</p>
        </header>
        
        <section class="section">
            <h2>📁 Application Structure</h2>
            <p>The vulnerability exists in the settings update functionality. Here's the relevant file structure:</p>
            
            <div class="file-structure">
                <h4>📂 lab21/</h4>
                <div class="file-item">📄 config.php - Database connection & helpers</div>
                <div class="file-item">📄 login.php - Authentication handling</div>
                <div class="file-item">📄 dashboard.php - User dashboard</div>
                <div class="file-item vulnerable">📄 settings.php - <span class="vulnerability-badge">VULNERABLE</span></div>
                <div class="file-item">📄 low-stock.php - Displays filtered columns</div>
            </div>
        </section>
        
        <section class="section">
            <h2>📄 Full Vulnerable Code: settings.php</h2>
            <p>Below is the complete vulnerable code with annotations:</p>
            
            <div class="code-block">
                <div class="code-header">
                    <span class="filename">settings.php</span>
                    <span class="line-info">POST Handler Section</span>
                </div>
                <div class="code-content">
                    <code><span class="keyword">if</span> (<span class="variable">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="comment">// Get settings ID from POST data</span>
<span class="highlight-line">    <span class="variable">$settings_id</span> = <span class="danger">$_POST['settings_id']</span> ?? <span class="variable">$settings</span>[<span class="string">'id'</span>];</span>
    
    <span class="comment">// Build column visibility array</span>
    <span class="variable">$columns</span> = [
        <span class="string">'show_grade'</span> => <span class="keyword">isset</span>(<span class="variable">$_POST</span>[<span class="string">'show_grade'</span>]) ? <span class="variable">1</span> : <span class="variable">0</span>,
        <span class="string">'show_title'</span> => <span class="keyword">isset</span>(<span class="variable">$_POST</span>[<span class="string">'show_title'</span>]) ? <span class="variable">1</span> : <span class="variable">0</span>,
        <span class="comment">// ... more column settings</span>
    ];
    
    <span class="comment">// Build and execute UPDATE query</span>
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings SET 
        show_grade = :show_grade,
        show_title = :show_title,
        <span class="comment">/* ... */</span>
        updated_at = NOW()
<span class="highlight-line">        WHERE id = <span class="danger">:settings_id</span>"</span>;</span>
    
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$sql</span>);
    <span class="variable">$stmt</span>-><span class="variable">execute</span>(<span class="variable">array_merge</span>(<span class="variable">$columns</span>, [
<span class="highlight-line">        <span class="string">'settings_id'</span> => <span class="danger">$settings_id</span></span>
    ]));
}</code>
                </div>
            </div>
            
            <div class="annotation">
                <h4>🚨 Line 3: User-Controlled Input</h4>
                <p><code>$settings_id = $_POST['settings_id']</code> - This line directly accepts a settings ID from user input without any validation that the user owns this settings record.</p>
            </div>
            
            <div class="annotation">
                <h4>🚨 Lines 15-16: No Ownership Check</h4>
                <p>The WHERE clause uses the user-supplied settings_id directly. There is no JOIN with the users/stores table to verify that the logged-in user has permission to modify these settings.</p>
            </div>
        </section>
        
        <section class="section">
            <h2>🧬 The Hidden Form Field</h2>
            <p>The settings ID is passed via a hidden form field, which is easily modifiable:</p>
            
            <div class="code-block">
                <div class="code-header">
                    <span class="filename">settings.php</span>
                    <span class="line-info">HTML Form Section</span>
                </div>
                <div class="code-content">
                    <code><span class="keyword">&lt;form</span> <span class="variable">method</span>=<span class="string">"POST"</span><span class="keyword">&gt;</span>
    <span class="comment">&lt;!-- This hidden field is the vulnerability vector --&gt;</span>
<span class="highlight-line">    <span class="keyword">&lt;input</span> <span class="variable">type</span>=<span class="string">"hidden"</span> <span class="variable">name</span>=<span class="string">"settings_id"</span> <span class="variable">value</span>=<span class="string">"<span class="danger">&lt;?= $settings['id'] ?&gt;</span>"</span><span class="keyword">&gt;</span></span>
    
    <span class="keyword">&lt;label&gt;</span>
        <span class="keyword">&lt;input</span> <span class="variable">type</span>=<span class="string">"checkbox"</span> <span class="variable">name</span>=<span class="string">"show_grade"</span><span class="keyword">&gt;</span>
        Show Grade Column
    <span class="keyword">&lt;/label&gt;</span>
    <span class="comment">&lt;!-- More checkboxes... --&gt;</span>
    
    <span class="keyword">&lt;button</span> <span class="variable">type</span>=<span class="string">"submit"</span><span class="keyword">&gt;</span>Save Settings<span class="keyword">&lt;/button&gt;</span>
<span class="keyword">&lt;/form&gt;</span></code>
                </div>
            </div>
            
            <div class="warning-box">
                <h4>⚠️ Security Anti-Pattern: Hidden Fields</h4>
                <ul>
                    <li>Hidden fields are NOT hidden from users - they're in the HTML source</li>
                    <li>Any user can modify hidden field values using browser dev tools</li>
                    <li>Never use hidden fields for authorization-critical data</li>
                    <li>Always verify ownership on the server side regardless of input source</li>
                </ul>
            </div>
        </section>
        
        <section class="section">
            <h2>🔐 What Security Checks Are Missing</h2>
            
            <h3>Missing Check #1: Ownership Verification</h3>
            <div class="code-block">
                <div class="code-header">
                    <span class="filename">Missing Code</span>
                    <span class="line-info">Should exist before UPDATE</span>
                </div>
                <div class="code-content">
                    <code><span class="comment">// This check should exist but doesn't:</span>
<span class="variable">$verify_sql</span> = <span class="string">"SELECT cs.id FROM column_settings cs
    JOIN stores s ON cs.store_id = s.id
    WHERE cs.id = :settings_id AND s.user_id = :user_id"</span>;
<span class="variable">$verify_stmt</span> = <span class="variable">$pdo</span>-><span class="variable">prepare</span>(<span class="variable">$verify_sql</span>);
<span class="variable">$verify_stmt</span>-><span class="variable">execute</span>([
    <span class="string">'settings_id'</span> => <span class="variable">$settings_id</span>,
    <span class="string">'user_id'</span> => <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>]
]);

<span class="keyword">if</span> (!<span class="variable">$verify_stmt</span>-><span class="variable">fetch</span>()) {
    <span class="variable">die</span>(<span class="string">'Access denied: You do not own these settings'</span>);
}</code>
                </div>
            </div>
            
            <h3>Missing Check #2: Update with Ownership Constraint</h3>
            <div class="code-block">
                <div class="code-header">
                    <span class="filename">Missing Code</span>
                    <span class="line-info">Alternative approach</span>
                </div>
                <div class="code-content">
                    <code><span class="comment">// The UPDATE should include an ownership check:</span>
<span class="variable">$sql</span> = <span class="string">"UPDATE column_settings cs
    JOIN stores s ON cs.store_id = s.id
    SET cs.show_grade = :show_grade, ...
    WHERE cs.id = :settings_id AND s.user_id = :user_id"</span>;</code>
                </div>
            </div>
        </section>
        
        <section class="section">
            <h2>📊 Data Flow Visualization</h2>
            <p>The vulnerability exists because there's no validation step between input and database:</p>
            
            <div class="file-structure">
                <h4>Current (Vulnerable) Flow:</h4>
                <div class="file-item">1️⃣ User submits form with settings_id=111111</div>
                <div class="file-item">2️⃣ PHP receives $_POST['settings_id'] = 111111</div>
                <div class="file-item vulnerable">3️⃣ ❌ No ownership check performed</div>
                <div class="file-item">4️⃣ Database UPDATE executed on settings 111111</div>
                <div class="file-item danger">5️⃣ ⚠️ Victim's settings modified!</div>
            </div>
            
            <div class="file-structure" style="margin-top: 1rem;">
                <h4>Expected (Secure) Flow:</h4>
                <div class="file-item">1️⃣ User submits form with settings_id=111111</div>
                <div class="file-item">2️⃣ PHP receives $_POST['settings_id'] = 111111</div>
                <div class="file-item">3️⃣ ✅ Server checks: Does user own settings 111111?</div>
                <div class="file-item">4️⃣ ❌ User doesn't own it → Access Denied</div>
                <div class="file-item">5️⃣ ✅ Victim's settings remain unchanged</div>
            </div>
        </section>
        
        <nav class="nav-pagination">
            <a href="docs-technical.php">← Previous: Technical</a>
            <a href="docs-mitigation.php">Next: Mitigation →</a>
        </nav>
    </div>
</body>
</html>
