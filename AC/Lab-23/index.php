<?php
// Lab 23: Landing Page - TagScope Asset Management
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 23 - IDOR AddTagToAssets | TagScope</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #a78bfa;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a78bfa;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover { background: rgba(99, 102, 241, 0.2); }
        .container { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; }
        .hero {
            text-align: center;
            margin-bottom: 4rem;
        }
        .hero h1 {
            font-size: 3rem;
            background: linear-gradient(135deg, #a78bfa, #818cf8);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .hero p { color: #64748b; font-size: 1.2rem; max-width: 700px; margin: 0 auto; }
        .lab-badge {
            display: inline-block;
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .feature-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .feature-card:hover {
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-3px);
        }
        .feature-card .icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .feature-card h3 { color: #a78bfa; margin-bottom: 0.75rem; }
        .feature-card p { color: #94a3b8; line-height: 1.6; }
        .attack-flow {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
        }
        .attack-flow h2 { color: #a78bfa; margin-bottom: 1.5rem; text-align: center; }
        .flow-diagram {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
        .flow-step {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            min-width: 150px;
        }
        .flow-step .num {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-weight: bold;
        }
        .flow-step h4 { color: #e2e8f0; font-size: 0.9rem; }
        .flow-arrow { color: #6366f1; font-size: 1.5rem; }
        .vuln-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .vuln-box h4 { color: #f87171; margin-bottom: 1rem; }
        .vuln-box code {
            display: block;
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            color: #e2e8f0;
            font-family: monospace;
            font-size: 0.85rem;
            overflow-x: auto;
        }
        .cta-section {
            text-align: center;
            padding: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 0.5rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4); }
        .btn-secondary {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a78bfa;
        }
        .endpoints {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .endpoints h4 { color: #818cf8; margin-bottom: 1rem; }
        .endpoint {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            margin: 0.5rem 0;
        }
        .method {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .method.post { background: #10b981; color: white; }
        .method.get { background: #3b82f6; color: white; }
        .endpoint code { color: #f59e0b; font-size: 0.85rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="../">← All Labs</a>
            <a href="setup_db.php">⚙️ Setup</a>
            <a href="login.php">🔑 Login</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="docs.php">📚 Docs</a>
        </nav>
    </header>

    <div class="container">
        <div class="hero">
            <h1>🎯 Lab 23: IDOR AddTagToAssets</h1>
            <p>Discover and exploit an Insecure Direct Object Reference vulnerability in the asset tagging system that allows enumeration of other users' private custom tags</p>
            <span class="lab-badge">⚠️ MEDIUM SEVERITY - INFORMATION DISCLOSURE</span>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="icon">🏷️</div>
                <h3>Custom Tags System</h3>
                <p>Create private custom tags to categorize and organize your security assets. Tags reveal your security strategy and asset classification.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🔍</div>
                <h3>Enumerable Tag IDs</h3>
                <p>Tag IDs are base64-encoded sequential identifiers. Decode and bruteforce to discover other users' private tags.</p>
            </div>
            <div class="feature-card">
                <div class="icon">⚡</div>
                <h3>AddTagToAssets API</h3>
                <p>The vulnerable endpoint accepts any tag ID without ownership verification, leaking tag existence and names.</p>
            </div>
        </div>

        <div class="attack-flow">
            <h2>🔄 Attack Flow</h2>
            <div class="flow-diagram">
                <div class="flow-step">
                    <div class="num">1</div>
                    <h4>Login as<br>Attacker</h4>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="num">2</div>
                    <h4>Get Own<br>Tag ID</h4>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="num">3</div>
                    <h4>Decode<br>Base64</h4>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="num">4</div>
                    <h4>Bruteforce<br>IDs</h4>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="num">5</div>
                    <h4>Discover<br>Tags!</h4>
                </div>
            </div>

            <div class="vuln-box">
                <h4>🎯 Vulnerable Endpoint</h4>
                <code>POST /api/add-tag-to-asset.php
{
  "operationName": "AddTagToAssets",
  "variables": {
    "tagId": "Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDAx",  // base64 encoded
    "assetIds": ["AST_A_001"]
  }
}</code>
            </div>

            <div class="endpoints">
                <h4>📡 API Endpoints</h4>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <code>/api/add-tag-to-asset.php</code>
                    <span style="color: #f87171; margin-left: auto;">⚠️ VULNERABLE</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <code>/api/tags.php?user_id=...</code>
                    <span style="color: #64748b; margin-left: auto;">List user's tags</span>
                </div>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <code>/api/assets.php?user_id=...</code>
                    <span style="color: #64748b; margin-left: auto;">List user's assets</span>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <a href="login.php" class="btn btn-primary">🚀 Start Hacking</a>
            <a href="lab-description.php" class="btn btn-secondary">📖 Step-by-Step Guide</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
        </div>
    </div>
</body>
</html>
