<?php
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(19);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 19 - IDOR Delete Saved Projects</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
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
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #a5b4fc;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #c7d2fe; }
        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            text-align: center;
        }
        .lab-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            color: #a5b4fc;
            margin-bottom: 1.5rem;
        }
        .solved-badge {
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
        }
        .hero h1 {
            font-size: 3rem;
            background: linear-gradient(135deg, #c7d2fe, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.2rem;
            color: #94a3b8;
            max-width: 700px;
            margin: 0 auto 2rem;
            line-height: 1.7;
        }
        .attack-flow {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin: 3rem 0;
        }
        .flow-step {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            min-width: 180px;
            text-align: center;
            position: relative;
        }
        .flow-step::after {
            content: "→";
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            font-size: 1.5rem;
        }
        .flow-step:last-child::after { content: ""; }
        .flow-step .step-num {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .flow-step h4 { color: #c7d2fe; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .flow-step p { color: #64748b; font-size: 0.8rem; }
        .cards-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s;
        }
        .card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-5px);
        }
        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .card h3 { color: #e0e0e0; margin-bottom: 0.75rem; }
        .card p { color: #64748b; font-size: 0.9rem; line-height: 1.6; }
        .credentials-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .cred-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .cred-item:last-child { border: none; }
        .cred-item code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #a5b4fc;
        }
        .role-badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            margin-left: 0.5rem;
        }
        .role-badge.victim { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .role-badge.attacker { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .role-badge.admin { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .vulnerable-preview {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            overflow-x: auto;
            margin-top: 1rem;
        }
        .vulnerable-preview .method { color: #22c55e; }
        .vulnerable-preview .url { color: #64748b; }
        .vulnerable-preview .param { color: #ef4444; }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn:hover { transform: translateY(-3px); }
        .footer {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="lab-description.php">Instructions</a>
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <span class="lab-badge <?php echo $labSolved ? 'solved-badge' : ''; ?>">
            <?php echo $labSolved ? '✓ Solved' : '🔓 Lab 19'; ?> • IDOR • Practitioner Level
        </span>
        <h1>IDOR: Delete Saved Projects</h1>
        <p>
            Exploit a broken access control vulnerability in a project portfolio platform. 
            The delete endpoint trusts user-supplied IDs without verifying ownership, 
            allowing attackers to delete any user's saved bookmarks.
        </p>

        <div class="attack-flow">
            <div class="flow-step">
                <div class="step-num">1</div>
                <h4>Login as Attacker</h4>
                <p>attacker_user</p>
            </div>
            <div class="flow-step">
                <div class="step-num">2</div>
                <h4>Find Delete URL</h4>
                <p>saved_id parameter</p>
            </div>
            <div class="flow-step">
                <div class="step-num">3</div>
                <h4>Discover Victim IDs</h4>
                <p>IDs 101-105</p>
            </div>
            <div class="flow-step">
                <div class="step-num">4</div>
                <h4>Modify Request</h4>
                <p>Change saved_id</p>
            </div>
            <div class="flow-step">
                <div class="step-num">5</div>
                <h4>Delete Others' Data</h4>
                <p>IDOR Success!</p>
            </div>
        </div>
    </section>

    <section class="cards-section">
        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">🎯</div>
                <h3>Attack Vector</h3>
                <p>
                    The API endpoint accepts a <code>saved_id</code> parameter via GET request 
                    but doesn't verify if the authenticated user owns that saved project.
                </p>
                <div class="vulnerable-preview">
                    <span class="method">GET</span> <span class="url">/api/delete_saved.php?</span><span class="param">saved_id=101</span>
                </div>
            </div>

            <div class="card">
                <div class="card-icon">🔑</div>
                <h3>Test Credentials</h3>
                <p>Use these accounts to test the vulnerability:</p>
                <div class="credentials-box">
                    <div class="cred-item">
                        <span><code>victim_designer</code><span class="role-badge victim">Target</span></span>
                        <code>victim123</code>
                    </div>
                    <div class="cred-item">
                        <span><code>attacker_user</code><span class="role-badge attacker">Use This</span></span>
                        <code>attacker123</code>
                    </div>
                    <div class="cred-item">
                        <span><code>admin</code><span class="role-badge admin">Admin</span></span>
                        <code>admin123</code>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-icon">💡</div>
                <h3>Hint</h3>
                <p>
                    The victim's saved project IDs are: <strong>101, 102, 103, 104, 105</strong>.
                    Login as attacker and try deleting one of these!
                </p>
                <p style="margin-top: 1rem; color: #fcd34d;">
                    ⚠️ Sequential IDs make enumeration attacks trivial.
                </p>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 Instructions</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </section>

    <footer class="footer">
        <p>Lab 19: IDOR Delete Saved Projects | Access Control Vulnerabilities</p>
    </footer>
</body>
</html>
