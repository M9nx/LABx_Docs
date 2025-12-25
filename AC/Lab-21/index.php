<?php
// Lab 21: Stocky Application - Landing Page
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocky - Inventory Management | Lab 21</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar-content {
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
            font-size: 1.5rem;
            font-weight: 700;
            color: #6366f1;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .nav-links a:hover {
            color: #e2e8f0;
            background: rgba(99, 102, 241, 0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white !important;
            font-weight: 600;
        }
        .hero {
            padding: 10rem 2rem 6rem;
            text-align: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        .hero-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 50px;
            color: #ef4444;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #a78bfa, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        .hero p {
            font-size: 1.25rem;
            color: #94a3b8;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.4);
        }
        .btn-secondary {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
        }
        .btn-secondary:hover {
            background: rgba(99, 102, 241, 0.2);
        }
        .features {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .feature-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.1);
        }
        .feature-icon {
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
        .feature-card h3 {
            color: #e2e8f0;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        .feature-card p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .attack-flow {
            padding: 4rem 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        .attack-flow h2 {
            text-align: center;
            color: #ef4444;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        .flow-container {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 16px;
            padding: 2rem;
        }
        .flow-step {
            display: flex;
            gap: 1.5rem;
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(239, 68, 68, 0.1);
        }
        .flow-step:last-child {
            border-bottom: none;
        }
        .step-number {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #ef4444, #f97316);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .step-content h4 {
            color: #f87171;
            margin-bottom: 0.25rem;
        }
        .step-content p {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .code-example {
            background: #0d1117;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.75rem;
            overflow-x: auto;
        }
        .code-example code {
            color: #f97316;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }
        .nav-bottom {
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 2rem;
            flex-wrap: wrap;
        }
        .nav-bottom a {
            padding: 0.75rem 1.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-bottom a:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }
        footer {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: 0.9rem;
            border-top: 1px solid rgba(99, 102, 241, 0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📦</div>
                <span>Stocky</span>
            </a>
            <div class="nav-links">
                <a href="docs.php">Documentation</a>
                <a href="lab-description.php">Lab Guide</a>
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="hero">
        <span class="hero-badge">🔓 Lab 21 - Access Control Vulnerability</span>
        <h1>Inventory Management Made Simple</h1>
        <p>Stocky helps e-commerce businesses manage their inventory, track low stock variants, and customize their dashboard columns. Discover how IDOR vulnerabilities can allow users to modify other users' settings.</p>
        <div class="hero-buttons">
            <a href="login.php" class="btn btn-primary">
                <span>🚀</span> Start Lab
            </a>
            <a href="lab-description.php" class="btn btn-secondary">
                <span>📖</span> View Instructions
            </a>
            <a href="docs.php" class="btn btn-secondary">
                <span>📚</span> Documentation
            </a>
        </div>
    </section>

    <section class="features">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Low Stock Tracking</h3>
                <p>Monitor variants with low inventory levels and get alerts before stockouts occur.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚙️</div>
                <h3>Custom Columns</h3>
                <p>Personalize your dashboard by showing or hiding specific data columns based on your needs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏪</div>
                <h3>Multi-Store Support</h3>
                <p>Manage multiple stores from a single dashboard with separate settings for each.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3>Reorder Management</h3>
                <p>Set reorder points and lead times to optimize your inventory replenishment cycle.</p>
            </div>
        </div>
    </section>

    <section class="attack-flow">
        <h2>⚠️ IDOR Attack Flow</h2>
        <div class="flow-container">
            <div class="flow-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Setup: Create Two User Accounts</h4>
                    <p>User A (victim) with store test.myshopify.com and User B (attacker) with store test1.myshopify.com</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Login as Attacker (User B)</h4>
                    <p>Navigate to Low Stock Variants → Settings → Columns and change column visibility settings</p>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Intercept the Update Request</h4>
                    <p>Capture the POST request when clicking "Update" button</p>
                    <div class="code-example">
                        <code>POST /settings_for_low_stock_variants/<span style="color:#10b981">111112</span> HTTP/1.1</code>
                    </div>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>Modify the Settings ID</h4>
                    <p>Change the ID from User B's settings (111112) to User A's settings (111111)</p>
                    <div class="code-example">
                        <code>POST /settings_for_low_stock_variants/<span style="color:#ef4444">111111</span> HTTP/1.1</code>
                    </div>
                </div>
            </div>
            <div class="flow-step">
                <div class="step-number">5</div>
                <div class="step-content">
                    <h4>Exploit Success</h4>
                    <p>User A's column settings are now modified by User B without authorization!</p>
                </div>
            </div>
        </div>
    </section>

    <div class="nav-bottom">
        <a href="../index.php">← Back to Labs</a>
        <a href="lab-description.php">📖 Lab Instructions</a>
        <a href="docs.php">📚 Documentation</a>
        <a href="setup_db.php">🔧 Setup Database</a>
    </div>

    <footer>
        <p>Lab 21: IDOR on Column Settings | Access Control Vulnerability Training</p>
    </footer>
</body>
</html>
