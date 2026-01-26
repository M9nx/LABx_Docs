<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(25);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 25 - Notes IDOR on Personal Snippets</title>
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
        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
            text-align: center;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(252, 109, 38, 0.2);
            border: 1px solid rgba(252, 109, 38, 0.3);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #fc6d26;
            margin-bottom: 1rem;
        }
        .hero h1 {
            font-size: 2.75rem;
            color: #fff;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }
        .hero h1 span { color: #fc6d26; }
        .hero p {
            color: #888;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.85rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #fc6d26 0%, #e24329 100%);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            color: #fc6d26;
        }
        .btn-secondary:hover {
            background: rgba(252, 109, 38, 0.2);
        }
        .attack-flow {
            max-width: 1000px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }
        .attack-flow h2 {
            text-align: center;
            color: #fc6d26;
            margin-bottom: 2rem;
        }
        .flow-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .flow-step {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        .flow-step::after {
            content: '→';
            position: absolute;
            right: -1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #fc6d26;
            font-size: 1.5rem;
        }
        .flow-step:last-child::after { display: none; }
        .flow-step .icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        .flow-step h4 {
            color: #fff;
            margin-bottom: 0.25rem;
        }
        .flow-step p {
            color: #888;
            font-size: 0.85rem;
        }
        .credentials-section {
            max-width: 1000px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }
        .credentials-section h2 {
            text-align: center;
            color: #fc6d26;
            margin-bottom: 1.5rem;
        }
        .cred-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .cred-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s;
        }
        .cred-card:hover {
            border-color: #fc6d26;
            transform: translateY(-3px);
        }
        .cred-card.attacker { border-left: 3px solid #ff6666; }
        .cred-card.victim { border-left: 3px solid #ffaa00; }
        .cred-card.user { border-left: 3px solid #00c853; }
        .cred-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .cred-username { font-weight: 600; color: #fff; }
        .cred-role {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            text-transform: uppercase;
        }
        .cred-role.attacker { background: rgba(255, 102, 102, 0.2); color: #ff6666; }
        .cred-role.victim { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .cred-role.user { background: rgba(0, 200, 83, 0.2); color: #00c853; }
        .cred-password {
            font-family: 'Consolas', monospace;
            color: #888;
            font-size: 0.85rem;
        }
        .cred-desc {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.5rem;
        }
        .vuln-section {
            max-width: 1000px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }
        .vuln-section h2 {
            text-align: center;
            color: #fc6d26;
            margin-bottom: 1.5rem;
        }
        .vuln-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .vuln-box h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .vuln-box p {
            color: #ccc;
            line-height: 1.7;
            margin-bottom: 0.75rem;
        }
        .vuln-box code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
        }
        .leaked-data {
            max-width: 1000px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }
        .leaked-data h2 {
            text-align: center;
            color: #fc6d26;
            margin-bottom: 1.5rem;
        }
        .leaked-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }
        .leaked-card {
            background: rgba(255, 68, 68, 0.08);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
        }
        .leaked-card h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .leaked-card p {
            color: #888;
            font-size: 0.85rem;
        }
        .solved-banner {
            background: rgba(0, 200, 83, 0.1);
            border: 1px solid rgba(0, 200, 83, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .solved-banner h3 { color: #00c853; margin-bottom: 0.5rem; }
        .footer {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #666;
        }
        @media (max-width: 768px) {
            .flow-step::after { display: none; }
            .hero h1 { font-size: 2rem; }
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
                SnippetHub
            </a>
            <nav class="nav-links">
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <?php if ($labSolved): ?>
        <div class="solved-banner">
            <h3>✓ Lab Completed!</h3>
            <p>You've successfully exploited the Notes IDOR vulnerability</p>
        </div>
        <?php endif; ?>
        
        <span class="hero-badge">Lab 25 • Access Control</span>
        <h1>Notes IDOR on <span>Personal Snippets</span></h1>
        <p>
            Exploit a broken access control vulnerability that allows attackers to create, edit, 
            and delete notes on private snippets, leaking sensitive titles through the activity feed.
        </p>
        <div class="hero-buttons">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Lab Info</a>
        </div>
    </section>

    <section class="attack-flow">
        <h2>🎯 Attack Flow</h2>
        <div class="flow-steps">
            <div class="flow-step">
                <div class="icon">🔐</div>
                <h4>Login as Attacker</h4>
                <p>Access the platform with attacker account</p>
            </div>
            <div class="flow-step">
                <div class="icon">📝</div>
                <h4>Create Project Issue</h4>
                <p>Post a comment on your own issue</p>
            </div>
            <div class="flow-step">
                <div class="icon">🔄</div>
                <h4>Intercept Request</h4>
                <p>Change noteable_type to personal_snippet</p>
            </div>
            <div class="flow-step">
                <div class="icon">🎯</div>
                <h4>Target Victim Snippet</h4>
                <p>Set noteable_id to victim's snippet ID</p>
            </div>
            <div class="flow-step">
                <div class="icon">📊</div>
                <h4>View Activity</h4>
                <p>Private snippet title leaked!</p>
            </div>
        </div>
    </section>

    <section class="credentials-section">
        <h2>🔑 Test Credentials</h2>
        <div class="cred-grid">
            <div class="cred-card attacker">
                <div class="cred-header">
                    <span class="cred-username">attacker</span>
                    <span class="cred-role attacker">Attacker</span>
                </div>
                <div class="cred-password">attacker123</div>
                <div class="cred-desc">🎯 Use to exploit the vulnerability</div>
            </div>
            <div class="cred-card victim">
                <div class="cred-header">
                    <span class="cred-username">victim</span>
                    <span class="cred-role victim">Victim</span>
                </div>
                <div class="cred-password">victim123</div>
                <div class="cred-desc">🔒 Has 5 private snippets with secrets</div>
            </div>
            <div class="cred-card user">
                <div class="cred-header">
                    <span class="cred-username">alice</span>
                    <span class="cred-role user">User</span>
                </div>
                <div class="cred-password">alice123</div>
                <div class="cred-desc">👤 Regular user with snippets</div>
            </div>
            <div class="cred-card user">
                <div class="cred-header">
                    <span class="cred-username">admin</span>
                    <span class="cred-role user">Admin</span>
                </div>
                <div class="cred-password">admin123</div>
                <div class="cred-desc">⚙️ Platform administrator</div>
            </div>
        </div>
    </section>

    <section class="vuln-section">
        <h2>🔓 The Vulnerability</h2>
        <div class="vuln-box">
            <h3>Missing Authorization Check in Notes Finder</h3>
            <p>
                When creating a note, the server accepts a <code>noteable_type</code> parameter that can be 
                changed from <code>issue</code> to <code>personal_snippet</code>. The vulnerable code path 
                doesn't verify if the user has permission to access the target snippet.
            </p>
            <p>
                The activity log then records the snippet title for the note author, 
                leaking private information to the attacker.
            </p>
            <p>
                <strong>Target Snippet IDs:</strong> <code>1</code> through <code>5</code> (victim's private snippets)
            </p>
        </div>
    </section>

    <section class="leaked-data">
        <h2>🔐 Victim's Private Snippets (Targets)</h2>
        <div class="leaked-grid">
            <div class="leaked-card">
                <h4>📋 Snippet ID: 1</h4>
                <p>Contains API keys and secrets - title reveals sensitive info</p>
            </div>
            <div class="leaked-card">
                <h4>📋 Snippet ID: 2</h4>
                <p>Financial report - confidential business data</p>
            </div>
            <div class="leaked-card">
                <h4>📋 Snippet ID: 3</h4>
                <p>Employee salary database export</p>
            </div>
            <div class="leaked-card">
                <h4>📋 Snippet ID: 4</h4>
                <p>Production database credentials</p>
            </div>
            <div class="leaked-card">
                <h4>📋 Snippet ID: 5</h4>
                <p>Upcoming product launch plans</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>Lab 25: Notes IDOR on Personal Snippets | Based on HackerOne Report</p>
        <p style="margin-top: 0.5rem;">
            <a href="../index.php" style="color: #fc6d26; text-decoration: none;">← Back to All Labs</a>
        </p>
    </footer>
</body>
</html>
