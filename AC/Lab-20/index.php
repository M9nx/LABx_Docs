<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(20);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 20 - IDOR API Key Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a {
            color: #5eead4;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #99f6e4; }
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 3rem;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #14b8a6, #2dd4bf);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            font-size: 1.2rem;
            color: #94a3b8;
            max-width: 700px;
            margin: 0 auto;
        }
        .lab-badge {
            display: inline-block;
            background: <?php echo $labSolved ? 'rgba(16, 185, 129, 0.2)' : 'rgba(20, 184, 166, 0.2)'; ?>;
            border: 1px solid <?php echo $labSolved ? '#10b981' : '#14b8a6'; ?>;
            color: <?php echo $labSolved ? '#6ee7b7' : '#5eead4'; ?>;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(20, 184, 166, 0.3);
            transform: translateY(-5px);
        }
        .card-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.2), rgba(20, 184, 166, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        .card h3 { color: #5eead4; margin-bottom: 0.75rem; }
        .card p { color: #94a3b8; font-size: 0.95rem; line-height: 1.6; }
        .attack-flow {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(20, 184, 166, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem 0;
        }
        .attack-flow h2 {
            color: #14b8a6;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .flow-steps {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .flow-step {
            flex: 1;
            min-width: 150px;
            text-align: center;
            padding: 1rem;
            background: rgba(20, 184, 166, 0.1);
            border-radius: 10px;
            position: relative;
        }
        .flow-step::after {
            content: '→';
            position: absolute;
            right: -1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #14b8a6;
            font-size: 1.5rem;
        }
        .flow-step:last-child::after { display: none; }
        .flow-step .step-num {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
        }
        .flow-step span { color: #94a3b8; font-size: 0.85rem; }
        .credentials-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .credentials-box h3 { color: #fca5a5; margin-bottom: 1rem; }
        .cred-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .cred-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 8px;
        }
        .cred-item .role {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        .cred-item.attacker {
            border: 1px solid #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }
        .cred-item code {
            color: #fca5a5;
            background: rgba(0,0,0,0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #94a3b8;
        }
        .btn:hover { transform: translateY(-3px); }
        .vulnerability-highlight {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .vulnerability-highlight h4 { color: #fca5a5; margin-bottom: 0.5rem; }
        .vulnerability-highlight p { color: #94a3b8; margin: 0; }
        .footer {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 3rem;
            color: #64748b;
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
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
            <nav class="nav-links">
                <a href="lab-description.php">Instructions</a>
                <a href="docs.php">Documentation</a>
                <a href="login.php">Login</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main-container">
        <section class="hero">
            <span class="lab-badge"><?php echo $labSolved ? '✓ Solved' : '🔓 Lab 20'; ?> • IDOR • Practitioner Level</span>
            <h1>IDOR: API Key Management</h1>
            <p>
                Exploit broken access control in organization API key management.
                Members with limited permissions can view, create, and delete sensitive API keys!
            </p>
        </section>

        <div class="vulnerability-highlight">
            <h4>🎯 Vulnerability: IDOR in API Key Operations</h4>
            <p>
                The application checks if a user is a member of an organization, but fails to verify 
                if the user has sufficient permissions (admin/owner) to manage API keys. Regular members 
                can access sensitive API endpoints and perform unauthorized operations.
            </p>
        </div>

        <div class="attack-flow">
            <h2>📊 Attack Flow</h2>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="step-num">1</div>
                    <span>Join org as Member</span>
                </div>
                <div class="flow-step">
                    <div class="step-num">2</div>
                    <span>Access /apiKeys endpoint</span>
                </div>
                <div class="flow-step">
                    <div class="step-num">3</div>
                    <span>VIEW all API keys</span>
                </div>
                <div class="flow-step">
                    <div class="step-num">4</div>
                    <span>CREATE new keys</span>
                </div>
                <div class="flow-step">
                    <div class="step-num">5</div>
                    <span>DELETE existing keys</span>
                </div>
            </div>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">🏢</div>
                <h3>Organization Platform</h3>
                <p>
                    KeyVault is an organization management platform where teams can collaborate 
                    and manage API keys for their services. Role-based access should restrict 
                    key management to admins and owners only.
                </p>
            </div>
            <div class="card">
                <div class="card-icon">🔐</div>
                <h3>API Key Management</h3>
                <p>
                    Organizations store sensitive API keys for production services, databases, 
                    payment gateways, and CI/CD pipelines. These should be protected from 
                    unauthorized access.
                </p>
            </div>
            <div class="card">
                <div class="card-icon">⚠️</div>
                <h3>Broken Access Control</h3>
                <p>
                    The API endpoints only check organization membership, not the user's role. 
                    This allows regular members to perform admin-only operations like viewing, 
                    creating, and deleting API keys.
                </p>
            </div>
        </div>

        <div class="credentials-box">
            <h3>🔑 Test Credentials (TechCorp Inc Organization)</h3>
            <div class="cred-grid">
                <div class="cred-item">
                    <div class="role">Owner (Victim)</div>
                    <code>victim_owner</code> / <code>victim123</code>
                </div>
                <div class="cred-item attacker">
                    <div class="role">Member (Attacker) ⚔️</div>
                    <code>attacker_member</code> / <code>attacker123</code>
                </div>
                <div class="cred-item">
                    <div class="role">Admin</div>
                    <code>alice_admin</code> / <code>alice123</code>
                </div>
                <div class="cred-item">
                    <div class="role">Member</div>
                    <code>bob_member</code> / <code>bob123</code>
                </div>
            </div>
        </div>

        <div class="btn-group">
            <a href="lab-description.php" class="btn btn-primary">📋 View Instructions</a>
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn btn-secondary">📖 Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← Back to Labs</a>
        </div>
    </main>

    <footer class="footer">
        <p>Lab 20: IDOR API Key Management | Access Control Vulnerabilities</p>
    </footer>
</body>
</html>
