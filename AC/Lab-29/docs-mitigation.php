<?php
// Lab 29: LinkedPro Newsletter Platform - Mitigation Guide
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mitigation Guide - Lab 29: Newsletter Subscriber IDOR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #0a0a0f;
            color: #e0e0e0;
            min-height: 100vh;
        }
        .top-nav {
            background: rgba(10, 10, 15, 0.98);
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 56px;
        }
        .nav-logo {
            font-size: 1.3rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .nav-logo span {
            color: #057642;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #0a66c2;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(10, 102, 194, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(10, 102, 194, 0.2);
            border-color: #0a66c2;
            color: #0a66c2;
        }
        .layout {
            display: flex;
            margin-top: 56px;
            min-height: calc(100vh - 56px);
        }
        .sidebar {
            width: 280px;
            background: rgba(15, 15, 20, 0.95);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1.5rem 0;
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 0 1.25rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 1rem;
        }
        .sidebar-header h2 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #057642;
            margin-bottom: 0.25rem;
        }
        .sidebar-header p {
            font-size: 1rem;
            color: #fff;
        }
        .sidebar-nav {
            list-style: none;
        }
        .sidebar-nav li a {
            display: block;
            padding: 0.6rem 1.25rem;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar-nav li a:hover {
            background: rgba(10, 102, 194, 0.1);
            color: #7fc4fd;
        }
        .sidebar-nav li a.active {
            background: rgba(10, 102, 194, 0.15);
            color: #0a66c2;
            border-left-color: #0a66c2;
        }
        .sidebar-nav li.section-title {
            padding: 1rem 1.25rem 0.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
        }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 1rem;
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #0a66c2;
            text-decoration: none;
            font-size: 0.85rem;
            padding: 0.5rem 0;
        }
        .sidebar-footer a:hover {
            color: #7fc4fd;
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .page-header h1 {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .page-header p {
            color: #888;
            font-size: 1.05rem;
        }
        .section {
            margin-bottom: 3rem;
            scroll-margin-top: 80px;
        }
        .section h2 {
            color: #0a66c2;
            font-size: 1.4rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
        }
        .section h3 {
            color: #057642;
            font-size: 1.1rem;
            margin: 1.5rem 0 0.75rem 0;
        }
        .section p {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #aaa;
            margin-left: 1.5rem;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section li {
            margin-bottom: 0.5rem;
        }
        .section code {
            background: rgba(10, 102, 194, 0.15);
            color: #7fc4fd;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-family: 'Consolas', 'Monaco', monospace;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .comparison-card {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            padding: 1rem;
        }
        .comparison-card.bad {
            border: 1px solid rgba(255, 0, 0, 0.2);
        }
        .comparison-card.good {
            border: 1px solid rgba(5, 118, 66, 0.3);
        }
        .comparison-card h4 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .comparison-card.bad h4 { color: #ff6b6b; }
        .comparison-card.good h4 { color: #20c997; }
        .comparison-card p { font-size: 0.9rem; color: #aaa; margin: 0; }
        .code-example {
            margin: 1.5rem 0;
            border-radius: 8px;
            overflow: hidden;
        }
        .code-header {
            padding: 0.6rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .code-header.vulnerable {
            background: rgba(255, 0, 0, 0.15);
            color: #ff6b6b;
        }
        .code-header.secure {
            background: rgba(5, 118, 66, 0.2);
            color: #20c997;
        }
        .code-header.neutral {
            background: rgba(10, 102, 194, 0.15);
            color: #7fc4fd;
        }
        .code-body {
            background: #1a1a2e;
            padding: 1rem;
            overflow-x: auto;
        }
        .code-body pre {
            margin: 0;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            color: #e0e0e0;
        }
        .code-body .cm { color: #6a9955; }
        .code-body .kw { color: #c586c0; }
        .code-body .fn { color: #dcdcaa; }
        .code-body .st { color: #ce9178; }
        .code-body .vr { color: #9cdcfe; }
        .code-body .nm { color: #b5cea8; }
        .hl {
            display: block;
            background: rgba(255, 0, 0, 0.1);
            margin: 0 -1rem;
            padding: 0 1rem;
        }
        .hl.good {
            background: rgba(5, 118, 66, 0.15);
        }
        .alert-box {
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
        }
        .alert-box.warning {
            background: rgba(255, 165, 0, 0.1);
            border-left: 4px solid #ffa500;
        }
        .alert-box.warning strong { color: #ffa500; }
        .alert-box.info {
            background: rgba(10, 102, 194, 0.1);
            border-left: 4px solid #0a66c2;
        }
        .alert-box.info strong { color: #0a66c2; }
        .alert-box.success {
            background: rgba(5, 118, 66, 0.1);
            border-left: 4px solid #057642;
        }
        .alert-box.success strong { color: #20c997; }
        .principle-card {
            background: rgba(10, 102, 194, 0.05);
            border: 1px solid rgba(10, 102, 194, 0.15);
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }
        .principle-card h4 {
            color: #0a66c2;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        .principle-card p {
            margin: 0;
            font-size: 0.9rem;
        }
        .checklist {
            list-style: none;
            margin-left: 0;
        }
        .checklist li {
            padding: 0.5rem 0 0.5rem 2rem;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }
        .checklist li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #057642;
            font-weight: bold;
            width: 1.5rem;
            height: 1.5rem;
            background: rgba(5, 118, 66, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        .nav-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-btn {
            flex: 1;
            padding: 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-btn.prev {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            text-align: left;
        }
        .nav-btn.next {
            background: linear-gradient(135deg, #0a66c2, #004182);
            text-align: right;
        }
        .nav-btn:hover {
            transform: translateY(-2px);
        }
        .nav-btn .label {
            font-size: 0.75rem;
            color: #888;
            display: block;
            margin-bottom: 0.25rem;
        }
        .nav-btn.next .label { color: rgba(255,255,255,0.7); }
        .nav-btn .title {
            color: #fff;
            font-weight: 600;
        }
        @media (max-width: 900px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .comparison-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <a href="index.php" class="nav-logo">Linked<span>Pro</span></a>
        <div class="nav-links">
            <a href="../index.php" class="btn-back">← All Labs</a>
            <a href="index.php">Home</a>
            <a href="lab-description.php">Lab Info</a>
            <a href="docs.php" class="active">Documentation</a>
            <a href="login.php">Login</a>
        </div>
    </nav>
    
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Documentation</h2>
                <p>Mitigation Guide</p>
            </div>
            <ul class="sidebar-nav">
                <li class="section-title">On This Page</li>
                <li><a href="#root-cause" class="active">Root Cause Analysis</a></li>
                <li><a href="#fix-comparison">Vulnerable vs. Secure Code</a></li>
                <li><a href="#implementation">Implementing the Fix</a></li>
                <li><a href="#best-practices">Best Practices</a></li>
                <li><a href="#testing">Testing Your Fix</a></li>
                <li><a href="#checklist">Security Checklist</a></li>
                <li class="section-title">Related Docs</li>
                <li><a href="docs.php">Documentation Hub</a></li>
                <li><a href="docs-technical.php">Technical Deep Dive</a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="login.php">🚀 Try the Lab</a>
                <a href="../index.php">← Back to All Labs</a>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="page-header">
                <h1>🛡️ Mitigation Guide</h1>
                <p>How to properly fix the IDOR vulnerability and prevent similar issues</p>
            </div>
            
            <!-- Root Cause -->
            <section class="section" id="root-cause">
                <h2>1. Root Cause Analysis</h2>
                <p>The vulnerability exists due to a fundamental misunderstanding of access control:</p>
                
                <div class="comparison-grid">
                    <div class="comparison-card bad">
                        <h4>❌ What the Code Does</h4>
                        <p>Checks if the user is <strong>authenticated</strong> (logged in)</p>
                    </div>
                    <div class="comparison-card good">
                        <h4>✓ What It Should Do</h4>
                        <p>Check if the user is <strong>authorized</strong> (has permission to access this specific resource)</p>
                    </div>
                </div>
                
                <h3>Authentication ≠ Authorization</h3>
                <ul>
                    <li><strong>Authentication:</strong> "Who are you?" - Verifying identity</li>
                    <li><strong>Authorization:</strong> "What can you do?" - Verifying permissions</li>
                </ul>
                
                <div class="alert-box warning">
                    <strong>⚠️ Key Insight:</strong> Just because a user is logged in doesn't mean they should have access to all resources. Each resource access must verify ownership or appropriate permissions.
                </div>
            </section>
            
            <!-- Fix Comparison -->
            <section class="section" id="fix-comparison">
                <h2>2. Vulnerable vs. Secure Code</h2>
                
                <h3>API Endpoint: api/get_subscribers.php</h3>
                
                <div class="code-example">
                    <div class="code-header vulnerable">❌ Vulnerable Implementation</div>
                    <div class="code-body">
<pre><span class="cm">// Check authentication only</span>
<span class="kw">if</span> (!<span class="fn">isset</span>($_SESSION[<span class="st">'user_id'</span>])) {
    <span class="fn">http_response_code</span>(<span class="nm">401</span>);
    <span class="kw">echo</span> <span class="fn">json_encode</span>([<span class="st">'error'</span> =&gt; <span class="st">'Unauthorized'</span>]);
    <span class="kw">exit</span>;
}

<span class="vr">$seriesUrn</span> = $_GET[<span class="st">'seriesUrn'</span>] ?? <span class="st">''</span>;
<span class="vr">$newsletter_id</span> = <span class="fn">preg_replace</span>(<span class="st">'/[^0-9]/'</span>, <span class="st">''</span>, <span class="vr">$seriesUrn</span>);

<span class="hl"><span class="cm">// ❌ NO OWNERSHIP CHECK - VULNERABLE!</span></span>

<span class="cm">// Directly query subscribers</span>
<span class="vr">$query</span> = <span class="st">"SELECT * FROM subscribers WHERE newsletter_id = ?"</span>;
<span class="vr">$stmt</span> = <span class="vr">$conn</span>-&gt;<span class="fn">prepare</span>(<span class="vr">$query</span>);
<span class="vr">$stmt</span>-&gt;<span class="fn">bind_param</span>(<span class="st">"i"</span>, <span class="vr">$newsletter_id</span>);
<span class="vr">$stmt</span>-&gt;<span class="fn">execute</span>();</pre>
                    </div>
                </div>
                
                <div class="code-example">
                    <div class="code-header secure">✓ Secure Implementation</div>
                    <div class="code-body">
<pre><span class="cm">// Check authentication</span>
<span class="kw">if</span> (!<span class="fn">isset</span>($_SESSION[<span class="st">'user_id'</span>])) {
    <span class="fn">http_response_code</span>(<span class="nm">401</span>);
    <span class="kw">echo</span> <span class="fn">json_encode</span>([<span class="st">'error'</span> =&gt; <span class="st">'Unauthorized'</span>]);
    <span class="kw">exit</span>;
}

<span class="vr">$seriesUrn</span> = $_GET[<span class="st">'seriesUrn'</span>] ?? <span class="st">''</span>;
<span class="vr">$newsletter_id</span> = <span class="fn">preg_replace</span>(<span class="st">'/[^0-9]/'</span>, <span class="st">''</span>, <span class="vr">$seriesUrn</span>);
<span class="vr">$user_id</span> = $_SESSION[<span class="st">'user_id'</span>];

<span class="hl good"><span class="cm">// ✓ VERIFY OWNERSHIP</span></span>
<span class="hl good"><span class="vr">$check</span> = <span class="vr">$conn</span>-&gt;<span class="fn">prepare</span>(<span class="st">"SELECT creator_id FROM newsletters WHERE id = ?"</span>);</span>
<span class="hl good"><span class="vr">$check</span>-&gt;<span class="fn">bind_param</span>(<span class="st">"i"</span>, <span class="vr">$newsletter_id</span>);</span>
<span class="hl good"><span class="vr">$check</span>-&gt;<span class="fn">execute</span>();</span>
<span class="hl good"><span class="vr">$newsletter</span> = <span class="vr">$check</span>-&gt;<span class="fn">get_result</span>()-&gt;<span class="fn">fetch_assoc</span>();</span>
<span class="hl good"></span>
<span class="hl good"><span class="kw">if</span> (!<span class="vr">$newsletter</span> || <span class="vr">$newsletter</span>[<span class="st">'creator_id'</span>] !== <span class="vr">$user_id</span>) {</span>
<span class="hl good">    <span class="fn">http_response_code</span>(<span class="nm">403</span>);</span>
<span class="hl good">    <span class="kw">echo</span> <span class="fn">json_encode</span>([<span class="st">'error'</span> =&gt; <span class="st">'Forbidden: Access denied'</span>]);</span>
<span class="hl good">    <span class="kw">exit</span>;</span>
<span class="hl good">}</span>

<span class="cm">// Now safe to query subscribers</span>
<span class="vr">$query</span> = <span class="st">"SELECT * FROM subscribers WHERE newsletter_id = ?"</span>;
<span class="vr">$stmt</span> = <span class="vr">$conn</span>-&gt;<span class="fn">prepare</span>(<span class="vr">$query</span>);
<span class="vr">$stmt</span>-&gt;<span class="fn">bind_param</span>(<span class="st">"i"</span>, <span class="vr">$newsletter_id</span>);
<span class="vr">$stmt</span>-&gt;<span class="fn">execute</span>();</pre>
                    </div>
                </div>
            </section>
            
            <!-- Implementation -->
            <section class="section" id="implementation">
                <h2>3. Implementing the Fix</h2>
                
                <h3>Step 1: Create an Authorization Helper</h3>
                <p>Add a reusable function in your config or helpers file:</p>
                
                <div class="code-example">
                    <div class="code-header neutral">📄 Helper Function</div>
                    <div class="code-body">
<pre><span class="cm">/**
 * Check if user owns a newsletter
 * @param mysqli $conn Database connection
 * @param int $newsletter_id Newsletter ID to check
 * @param int $user_id User ID to verify
 * @return bool True if user owns the newsletter
 */</span>
<span class="kw">function</span> <span class="fn">userOwnsNewsletter</span>(<span class="vr">$conn</span>, <span class="vr">$newsletter_id</span>, <span class="vr">$user_id</span>) {
    <span class="vr">$stmt</span> = <span class="vr">$conn</span>-&gt;<span class="fn">prepare</span>(
        <span class="st">"SELECT 1 FROM newsletters WHERE id = ? AND creator_id = ?"</span>
    );
    <span class="vr">$stmt</span>-&gt;<span class="fn">bind_param</span>(<span class="st">"ii"</span>, <span class="vr">$newsletter_id</span>, <span class="vr">$user_id</span>);
    <span class="vr">$stmt</span>-&gt;<span class="fn">execute</span>();
    <span class="kw">return</span> <span class="vr">$stmt</span>-&gt;<span class="fn">get_result</span>()-&gt;num_rows &gt; <span class="nm">0</span>;
}</pre>
                    </div>
                </div>
                
                <h3>Step 2: Use Consistent Authorization Checks</h3>
                <p>Apply the check in all relevant endpoints:</p>
                
                <div class="code-example">
                    <div class="code-header neutral">📄 Usage Example</div>
                    <div class="code-body">
<pre><span class="cm">// In api/get_subscribers.php, subscribers.php, etc.</span>
<span class="kw">if</span> (!<span class="fn">userOwnsNewsletter</span>(<span class="vr">$conn</span>, <span class="vr">$newsletter_id</span>, $_SESSION[<span class="st">'user_id'</span>])) {
    <span class="cm">// For API:</span>
    <span class="fn">http_response_code</span>(<span class="nm">403</span>);
    <span class="kw">echo</span> <span class="fn">json_encode</span>([<span class="st">'error'</span> =&gt; <span class="st">'Forbidden'</span>]);
    <span class="kw">exit</span>;
}</pre>
                    </div>
                </div>
                
                <h3>Step 3: Audit All Endpoints</h3>
                <p>Review all endpoints that access subscriber data:</p>
                <ul>
                    <li><code>/api/get_subscribers.php</code> - API endpoint</li>
                    <li><code>/subscribers.php</code> - Web page</li>
                    <li><code>/api/export_subscribers.php</code> - Export functionality (if exists)</li>
                    <li>Any analytics or reporting endpoints</li>
                </ul>
            </section>
            
            <!-- Best Practices -->
            <section class="section" id="best-practices">
                <h2>4. Best Practices</h2>
                
                <div class="principle-card">
                    <h4>1. Defense in Depth</h4>
                    <p>Apply authorization checks at multiple layers - controller, service, and database query levels.</p>
                </div>
                
                <div class="principle-card">
                    <h4>2. Principle of Least Privilege</h4>
                    <p>Only return the minimum data necessary. Don't include sensitive fields like email by default.</p>
                </div>
                
                <div class="principle-card">
                    <h4>3. Consistent Error Handling</h4>
                    <p>Return the same error message for "not found" and "not authorized" to prevent enumeration attacks.</p>
                </div>
                
                <div class="principle-card">
                    <h4>4. Query-Level Security</h4>
                    <p>Include ownership in your database queries rather than checking separately:</p>
                </div>
                
                <div class="code-example">
                    <div class="code-header secure">✓ Query with Built-in Authorization</div>
                    <div class="code-body">
<pre><span class="cm">// Better: Include ownership in the query itself</span>
<span class="vr">$query</span> = <span class="st">"SELECT s.* FROM subscribers s 
          INNER JOIN newsletters n ON s.newsletter_id = n.id
          WHERE n.id = ? AND n.creator_id = ?"</span>;
<span class="vr">$stmt</span>-&gt;<span class="fn">bind_param</span>(<span class="st">"ii"</span>, <span class="vr">$newsletter_id</span>, <span class="vr">$user_id</span>);</pre>
                    </div>
                </div>
                
                <div class="principle-card">
                    <h4>5. Use UUIDs (But Don't Rely on Them)</h4>
                    <p>UUIDs make enumeration harder but don't replace authorization. Still verify permissions!</p>
                </div>
                
                <div class="principle-card">
                    <h4>6. Audit Logging</h4>
                    <p>Log access attempts to sensitive resources for security monitoring.</p>
                </div>
            </section>
            
            <!-- Testing -->
            <section class="section" id="testing">
                <h2>5. Testing Your Fix</h2>
                
                <h3>Manual Testing</h3>
                <ol>
                    <li>Login as User A who owns Newsletter 1</li>
                    <li>Verify you CAN access subscribers of Newsletter 1</li>
                    <li>Try to access subscribers of Newsletter 2 (owned by User B)</li>
                    <li>Verify you get a 403 Forbidden response</li>
                </ol>
                
                <h3>Automated Test Cases</h3>
                <div class="code-example">
                    <div class="code-header neutral">📄 Test Cases</div>
                    <div class="code-body">
<pre><span class="cm">// Test: Owner can access their subscribers</span>
<span class="kw">function</span> <span class="fn">test_owner_can_access_subscribers</span>() {
    <span class="vr">$response</span> = <span class="fn">apiRequest</span>(<span class="st">'GET'</span>, <span class="st">'/api/get_subscribers.php?seriesUrn=...'</span>,
        [<span class="st">'user_id'</span> =&gt; <span class="nm">2001</span>]  <span class="cm">// Owner of newsletter 1</span>
    );
    <span class="fn">assertEquals</span>(<span class="nm">200</span>, <span class="vr">$response</span>-&gt;status);
}

<span class="cm">// Test: Non-owner cannot access subscribers</span>
<span class="kw">function</span> <span class="fn">test_non_owner_denied_access</span>() {
    <span class="vr">$response</span> = <span class="fn">apiRequest</span>(<span class="st">'GET'</span>, <span class="st">'/api/get_subscribers.php?seriesUrn=...'</span>,
        [<span class="st">'user_id'</span> =&gt; <span class="nm">1001</span>]  <span class="cm">// NOT owner</span>
    );
    <span class="fn">assertEquals</span>(<span class="nm">403</span>, <span class="vr">$response</span>-&gt;status);
}

<span class="cm">// Test: Unauthenticated user denied</span>
<span class="kw">function</span> <span class="fn">test_unauthenticated_denied</span>() {
    <span class="vr">$response</span> = <span class="fn">apiRequest</span>(<span class="st">'GET'</span>, <span class="st">'/api/get_subscribers.php?seriesUrn=...'</span>,
        []  <span class="cm">// No session</span>
    );
    <span class="fn">assertEquals</span>(<span class="nm">401</span>, <span class="vr">$response</span>-&gt;status);
}</pre>
                    </div>
                </div>
            </section>
            
            <!-- Checklist -->
            <section class="section" id="checklist">
                <h2>6. Security Checklist</h2>
                
                <p>Use this checklist when reviewing code for IDOR vulnerabilities:</p>
                
                <ul class="checklist">
                    <li>Every endpoint accessing user-specific resources has authorization checks</li>
                    <li>Authorization checks verify ownership, not just authentication</li>
                    <li>Error messages don't reveal whether resources exist</li>
                    <li>Database queries include ownership conditions where appropriate</li>
                    <li>API rate limiting prevents enumeration attacks</li>
                    <li>Access attempts are logged for security monitoring</li>
                    <li>Sensitive data has additional permission layers</li>
                    <li>Authorization logic is centralized (not duplicated)</li>
                    <li>Tests exist for both authorized and unauthorized access</li>
                    <li>Code reviews specifically check for IDOR vulnerabilities</li>
                </ul>
                
                <div class="alert-box success">
                    <strong>✓ Remember:</strong> Never trust client-supplied identifiers. Always verify that the authenticated user has permission to access the specific resource being requested.
                </div>
            </section>
            
            <div class="nav-buttons">
                <a href="docs-technical.php" class="nav-btn prev">
                    <span class="label">← Previous</span>
                    <span class="title">Technical Deep Dive</span>
                </a>
                <a href="login.php" class="nav-btn next">
                    <span class="label">Next →</span>
                    <span class="title">Try the Lab</span>
                </a>
            </div>
        </main>
    </div>
    
    <script>
        // Highlight active section in sidebar based on scroll
        const sections = document.querySelectorAll('.section');
        const navLinks = document.querySelectorAll('.sidebar-nav a[href^="#"]');
        
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
