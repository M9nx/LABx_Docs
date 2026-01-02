<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(24);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 24 - IDOR Exposes All ML Models</title>
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
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 36px; height: 36px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #fc6d26; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }
        .hero h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #fc6d26, #fca326);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .hero p {
            color: #888;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        .lab-badge {
            display: inline-block;
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .solved-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
        .scenario-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .scenario-card h2 {
            color: #fc6d26;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .scenario-card p {
            color: #aaa;
            line-height: 1.8;
        }
        .attack-flow {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .attack-step {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        .attack-step::after {
            content: '→';
            position: absolute;
            right: -1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #fc6d26;
            font-size: 1.5rem;
        }
        .attack-step:last-child::after { display: none; }
        .step-num {
            background: linear-gradient(135deg, #fc6d26, #e24329);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-weight: bold;
        }
        .attack-step h4 { color: #fc6d26; margin-bottom: 0.5rem; }
        .attack-step p { color: #888; font-size: 0.85rem; }
        .credential-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .credential-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .credential-card h4 {
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .credential-card .role {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 5px;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .role.victim { background: rgba(255, 68, 68, 0.2); color: #ff6666; }
        .role.attacker { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .role.admin { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
        .credential-card code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 0.5rem;
            border-radius: 5px;
            color: #88ff88;
            margin-top: 0.5rem;
        }
        .vulnerable-request {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .vulnerable-request .comment { color: #666; }
        .vulnerable-request .param { color: #ff6666; }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #fc6d26, #e24329);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-3px); }
        .leaked-data {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .leaked-data h4 { color: #ff6666; margin-bottom: 0.5rem; }
        .leaked-data ul {
            list-style: none;
            padding: 0;
        }
        .leaked-data li {
            padding: 0.3rem 0;
            color: #ccc;
        }
        .leaked-data li::before {
            content: '🔓 ';
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
                <a href="../index.php">← All Labs</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="hero">
            <span class="lab-badge">Lab 24 • Practitioner</span>
            <h1>🔓 IDOR Exposes All ML Models</h1>
            <p>Exploit a GraphQL API vulnerability to access private machine learning models and their sensitive metadata across the entire MLRegistry instance.</p>
        </div>

        <?php if ($labSolved): ?>
        <div class="solved-banner">
            <h3>🎉 Lab Solved!</h3>
            <p>You've successfully exploited the IDOR vulnerability and discovered all private models!</p>
        </div>
        <?php endif; ?>

        <div class="scenario-card">
            <h2>🎯 Lab Scenario</h2>
            <p>
                This lab simulates a GitLab-style <strong>ML Model Registry</strong> platform where data scientists store and version their machine learning models.
                The GraphQL API endpoint <code>/api/graphql.php</code> contains an IDOR vulnerability - the 
                <code>internal_id</code> parameter in model GIDs is not restricted to models the user has access to,
                allowing attackers to enumerate and access ANY model on the instance by manipulating sequential IDs.
            </p>
            
            <div class="leaked-data">
                <h4>Sensitive Data That Can Be Leaked:</h4>
                <ul>
                    <li>Private model names and descriptions</li>
                    <li>API keys and secrets in model metadata</li>
                    <li>Training hyperparameters with credentials</li>
                    <li>Model owner information and emails</li>
                    <li>Project structures and internal paths</li>
                </ul>
            </div>
        </div>

        <div class="scenario-card">
            <h2>🔥 Attack Flow</h2>
            <div class="attack-flow">
                <div class="attack-step">
                    <div class="step-num">1</div>
                    <h4>Login</h4>
                    <p>Authenticate as attacker user</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">2</div>
                    <h4>Find GID</h4>
                    <p>View your model's GID format</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">3</div>
                    <h4>Decode</h4>
                    <p>Extract internal_id from GID</p>
                </div>
                <div class="attack-step">
                    <div class="step-num">4</div>
                    <h4>Enumerate</h4>
                    <p>Try sequential IDs via API</p>
                </div>
            </div>

            <h3 style="color: #fc6d26; margin: 1.5rem 0 1rem;">Vulnerable API Request:</h3>
            <div class="vulnerable-request">
<span class="comment">// The attacker changes internal_id from their own (1000500) to victim's (1000501)</span>
POST /api/graphql.php HTTP/1.1
Content-Type: application/json

{
  "operationName": "getModel",
  "variables": {
    "id": "<span class="param">Z2lkOi8vZ2l0bGFiL01sOjpNb2RlbC8xMDAwNTAx</span>"  <span class="comment">// base64(gid://gitlab/Ml::Model/1000501)</span>
  }
}

<span class="comment">// API returns victim's private model with all sensitive data!</span>
            </div>
        </div>

        <div class="scenario-card">
            <h2>🔐 Test Credentials</h2>
            <div class="credential-grid">
                <div class="credential-card">
                    <span class="role attacker">Attacker ⭐</span>
                    <h4>attacker</h4>
                    <code>attacker / attacker123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        Use this account to exploit the vulnerability
                    </p>
                </div>
                <div class="credential-card">
                    <span class="role victim">Victim</span>
                    <h4>victim_corp</h4>
                    <code>victim_corp / victim123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        Owns 4 private ML models with secrets
                    </p>
                </div>
                <div class="credential-card">
                    <span class="role victim">Victim</span>
                    <h4>data_scientist</h4>
                    <code>data_scientist / scientist123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        Owns 3 private ML models with credentials
                    </p>
                </div>
                <div class="credential-card">
                    <span class="role admin">Admin</span>
                    <h4>admin</h4>
                    <code>admin / admin123</code>
                    <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                        System administrator
                    </p>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Description</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>
</body>
</html>
