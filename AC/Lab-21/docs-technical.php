<?php
// Lab 21: Documentation - Why The Exploit Works
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Why The Exploit Works - IDOR Documentation | Lab 21</title>
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
            background: linear-gradient(135deg, #ef4444, #f97316);
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
        .section ul, .section ol {
            color: #94a3b8;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .section li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .code-block {
            background: #0d1117;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block code {
            color: #e2e8f0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            white-space: pre;
        }
        .code-block .comment { color: #64748b; }
        .code-block .danger { color: #ef4444; font-weight: 600; }
        .code-block .keyword { color: #c084fc; }
        .code-block .string { color: #86efac; }
        .code-block .variable { color: #f59e0b; }
        .concept-card {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .concept-card h4 {
            color: #f87171;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .concept-card p {
            color: #fca5a5;
            margin: 0;
        }
        .flow-diagram {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .flow-step {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px dashed rgba(99, 102, 241, 0.2);
        }
        .flow-step:last-child { border-bottom: none; }
        .flow-arrow {
            color: #6366f1;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .flow-content h5 {
            color: #a5b4fc;
            margin-bottom: 0.25rem;
        }
        .flow-content p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
        }
        .problem-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
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
            <h1>⚙️ 3. Why The Exploit Works</h1>
            <p>A technical deep-dive into the root causes of this IDOR vulnerability: missing authorization checks, trust boundary violations, and direct object references.</p>
        </header>
        
        <section class="section">
            <h2>🔴 The Missing Authorization Check</h2>
            <p>The fundamental problem is that the application verifies <strong>authentication</strong> (who you are) but not <strong>authorization</strong> (what you can do).</p>
            
            <div class="concept-card">
                <h4>❌ What's Missing</h4>
                <p>The code never verifies: "Does this user own or have permission to modify settings ID 111111?"</p>
            </div>
            
            <h3>Authentication vs Authorization</h3>
            <ul>
                <li><strong>Authentication:</strong> "Is this a valid logged-in user?" ✅ (Checked)</li>
                <li><strong>Authorization:</strong> "Can this user modify these specific settings?" ❌ (NOT Checked)</li>
            </ul>
            
            <p>The code only checks if the user is logged in:</p>
            <div class="code-block">
                <code><span class="keyword">function</span> <span class="variable">requireLogin</span>() {
    <span class="keyword">if</span> (!<span class="variable">isLoggedIn</span>()) {
        <span class="variable">header</span>(<span class="string">'Location: login.php'</span>);
        <span class="keyword">exit</span>;
    }
    <span class="comment">// ❌ No check for what this user can actually do!</span>
}</code>
            </div>
        </section>
        
        <section class="section">
            <h2>⚠️ Trusting Client-Controlled Input</h2>
            <p>The vulnerability exists because the application trusts the <code>settings_id</code> value that comes from the user's request.</p>
            
            <div class="code-block">
                <code><span class="comment">// The settings_id comes directly from user input</span>
<span class="variable">$settings_id</span> = <span class="danger">$_POST['settings_id']</span>;

<span class="comment">// It's used directly in the database query without ownership verification</span>
<span class="variable">$sql</span> = <span class="string">"UPDATE column_settings SET ... WHERE id = :settings_id"</span>;
<span class="variable">$stmt</span>-><span class="variable">execute</span>([<span class="string">'settings_id'</span> => <span class="danger">$settings_id</span>]);</code>
            </div>
            
            <div class="concept-card">
                <h4>🚨 Trust Boundary Violation</h4>
                <p>User input should never be trusted to determine resource access. The server should independently verify ownership based on the authenticated user's session, not user-supplied IDs.</p>
            </div>
            
            <h3>The Dangerous Assumption</h3>
            <p>Developers often assume:</p>
            <ul>
                <li>"Users won't modify hidden form fields"</li>
                <li>"The form only shows the user's own settings ID"</li>
                <li>"No one will guess other users' IDs"</li>
            </ul>
            <p>All of these assumptions are <span class="problem-badge">WRONG</span> from a security perspective.</p>
        </section>
        
        <section class="section">
            <h2>🔗 Direct Object Reference Flow</h2>
            <p>Here's exactly how the attack works, step by step:</p>
            
            <div class="flow-diagram">
                <div class="flow-step">
                    <span class="flow-arrow">1️⃣</span>
                    <div class="flow-content">
                        <h5>User B (Attacker) Logs In</h5>
                        <p>Server creates session: $_SESSION['user_id'] = 2 (User B's ID)</p>
                    </div>
                </div>
                <div class="flow-step">
                    <span class="flow-arrow">2️⃣</span>
                    <div class="flow-content">
                        <h5>User B Visits Settings Page</h5>
                        <p>Server shows User B their settings ID (111112) in the form</p>
                    </div>
                </div>
                <div class="flow-step">
                    <span class="flow-arrow">3️⃣</span>
                    <div class="flow-content">
                        <h5>User B Modifies the Form</h5>
                        <p>Changes settings_id from 111112 to 111111 (User A's settings)</p>
                    </div>
                </div>
                <div class="flow-step">
                    <span class="flow-arrow">4️⃣</span>
                    <div class="flow-content">
                        <h5>User B Submits the Form</h5>
                        <p>POST request sent with settings_id=111111</p>
                    </div>
                </div>
                <div class="flow-step">
                    <span class="flow-arrow">5️⃣</span>
                    <div class="flow-content">
                        <h5>Server Processes Request <span class="problem-badge">VULNERABLE</span></h5>
                        <p>Server checks: Is user logged in? Yes ✓<br>Server does NOT check: Does user own settings 111111? ❌</p>
                    </div>
                </div>
                <div class="flow-step">
                    <span class="flow-arrow">6️⃣</span>
                    <div class="flow-content">
                        <h5>Database Updated</h5>
                        <p>UPDATE column_settings SET ... WHERE id = 111111<br>User A's settings are modified!</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section">
            <h2>🎯 Why Predictable IDs Make It Worse</h2>
            <p>While not the root cause, sequential/predictable IDs make IDOR exploitation easier:</p>
            
            <ul>
                <li><strong>Sequential IDs:</strong> 111111, 111112, 111113... are easy to guess</li>
                <li><strong>Enumeration:</strong> Attackers can iterate through IDs to find valid targets</li>
                <li><strong>No Security Through Obscurity:</strong> Even random UUIDs don't provide security alone</li>
            </ul>
            
            <div class="concept-card">
                <h4>📌 Key Insight</h4>
                <p>Using UUIDs instead of sequential IDs would only slow down the attacker, not prevent the attack. The real fix is authorization checks, not ID obfuscation.</p>
            </div>
        </section>
        
        <section class="section">
            <h2>📊 The Vulnerable Code Path</h2>
            <div class="code-block">
                <code><span class="comment">// settings.php - Vulnerable Update Handler</span>

<span class="keyword">if</span> ($_SERVER[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="comment">// ❌ VULNERABLE: Takes settings_id directly from user input</span>
    <span class="variable">$settings_id</span> = <span class="danger">$_POST['settings_id']</span> ?? <span class="variable">$settings</span>[<span class="string">'id'</span>];
    
    <span class="comment">// ✅ Good: Sanitizes the column values</span>
    <span class="variable">$columns</span> = [
        <span class="string">'show_grade'</span> => <span class="keyword">isset</span>(<span class="variable">$_POST</span>[<span class="string">'show_grade'</span>]) ? <span class="variable">1</span> : <span class="variable">0</span>,
        <span class="comment">// ... other columns</span>
    ];
    
    <span class="comment">// ❌ VULNERABLE: No ownership verification before update</span>
    <span class="variable">$sql</span> = <span class="string">"UPDATE column_settings SET ... WHERE id = :settings_id"</span>;
    <span class="variable">$stmt</span>-><span class="variable">execute</span>(<span class="variable">array_merge</span>(<span class="variable">$columns</span>, [<span class="string">'settings_id'</span> => <span class="danger">$settings_id</span>]));
    
    <span class="comment">// The update succeeds for ANY valid settings_id, regardless of owner!</span>
}</code>
            </div>
        </section>
        
        <nav class="nav-pagination">
            <a href="docs-walkthrough.php">← Previous: Walkthrough</a>
            <a href="docs-vulnerable-code.php">Next: Vulnerable Code →</a>
        </nav>
    </div>
</body>
</html>
