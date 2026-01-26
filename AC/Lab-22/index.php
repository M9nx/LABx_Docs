<?php
// Lab 22: Landing Page - RideKea Booking App
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 22 - IDOR in Booking Detail & Bids | RideKea</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(6, 182, 212, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #22d3ee;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #22d3ee;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover { background: rgba(6, 182, 212, 0.2); }
        .container { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; }
        .hero {
            text-align: center;
            margin-bottom: 4rem;
        }
        .hero h1 {
            font-size: 3rem;
            background: linear-gradient(135deg, #22d3ee, #06b6d4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .hero .subtitle {
            font-size: 1.3rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .lab-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(245, 158, 11, 0.2));
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 30px;
            color: #fca5a5;
            font-weight: 600;
            margin-top: 1rem;
        }
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .info-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 16px;
            padding: 2rem;
        }
        .info-card h3 {
            color: #22d3ee;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-card p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 0.5rem;
        }
        .info-card code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #f59e0b;
            font-size: 0.85rem;
        }
        .attack-flow {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
        }
        .attack-flow h2 {
            color: #f87171;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .flow-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .flow-step {
            flex: 1;
            min-width: 150px;
            text-align: center;
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 12px;
            position: relative;
        }
        .flow-step .step-num {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ef4444, #f97316);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 0.75rem;
        }
        .flow-step h4 { color: #fca5a5; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .flow-step p { color: #64748b; font-size: 0.8rem; }
        .flow-arrow {
            color: #ef4444;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .endpoints-box {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 3rem;
        }
        .endpoints-box h3 {
            color: #22d3ee;
            margin-bottom: 1rem;
        }
        .endpoint {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            font-family: monospace;
            font-size: 0.85rem;
        }
        .endpoint .method {
            color: #10b981;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        .endpoint .url { color: #f59e0b; }
        .endpoint .vuln {
            float: right;
            color: #ef4444;
            font-size: 0.75rem;
        }
        .cta-section {
            text-align: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }
        .btn-secondary {
            background: rgba(6, 182, 212, 0.1);
            border: 2px solid #06b6d4;
            color: #22d3ee;
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3); }
        .credentials-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .credentials-box h4 {
            color: #10b981;
            margin-bottom: 1rem;
        }
        .cred-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .cred-item {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 8px;
        }
        .cred-item .role {
            color: #10b981;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .cred-item .user { color: #e2e8f0; font-weight: 600; }
        .cred-item .pass { color: #94a3b8; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="../index.php">🏠 Lab Home</a>
            <a href="lab-description.php">📖 Lab Guide</a>
            <a href="docs.php">📚 Documentation</a>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php">📊 Dashboard</a>
                <a href="logout.php">🚪 Logout</a>
            <?php else: ?>
                <a href="login.php">🔑 Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <section class="hero">
            <h1>🚗 RideKea Booking Platform</h1>
            <p class="subtitle">IDOR in Booking Detail & Bids - Information Disclosure</p>
            <span class="lab-badge">⚠️ Lab 22 - Practitioner Level</span>
        </section>

        <div class="info-cards">
            <div class="info-card">
                <h3>🎯 Vulnerability</h3>
                <p>The booking detail and bids API endpoints accept a <code>booking_id</code> parameter without verifying ownership.</p>
                <p>Any authenticated user can view ANY booking's sensitive details including pickup/dropoff locations, driver info, and active bids.</p>
            </div>
            <div class="info-card">
                <h3>💥 Impact</h3>
                <p>• View other users' home/work addresses</p>
                <p>• Access ride history and travel patterns</p>
                <p>• See driver phone numbers & vehicle details</p>
                <p>• Access bidding information and pricing</p>
            </div>
        </div>

        <div class="attack-flow">
            <h2>🔥 Attack Flow</h2>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="step-num">1</div>
                    <h4>Login as Attacker</h4>
                    <p>Get valid access token</p>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="step-num">2</div>
                    <h4>Find Victim's Booking ID</h4>
                    <p>Enumerate or discover IDs</p>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="step-num">3</div>
                    <h4>Request Booking Details</h4>
                    <p>Use victim's booking_id</p>
                </div>
                <span class="flow-arrow">→</span>
                <div class="flow-step">
                    <div class="step-num">4</div>
                    <h4>Access Sensitive Data</h4>
                    <p>View locations, bids, etc.</p>
                </div>
            </div>
        </div>

        <div class="endpoints-box">
            <h3>🔗 Vulnerable API Endpoints</h3>
            <div class="endpoint">
                <span class="method">GET</span>
                <span class="url">api/bookings.php?booking_id={BOOKING_ID}</span>
                <span class="vuln">IDOR ⚠️</span>
            </div>
            <div class="endpoint">
                <span class="method">GET</span>
                <span class="url">api/bids.php?booking_id={BOOKING_ID}</span>
                <span class="vuln">IDOR ⚠️</span>
            </div>
            <div class="endpoint">
                <span class="method">GET</span>
                <span class="url">api/bids_config.php?trip_id={BOOKING_ID}</span>
                <span class="vuln">IDOR ⚠️</span>
            </div>
        </div>

        <div class="cta-section">
            <a href="lab-description.php" class="btn btn-primary">📖 Start Lab Guide</a>
            <a href="login.php" class="btn btn-secondary">🔑 Login to Exploit</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            
            <div class="credentials-box">
                <h4>🔐 Test Credentials</h4>
                <div class="cred-grid">
                    <div class="cred-item">
                        <div class="role">👤 Victim (Target)</div>
                        <div class="user">victim_user</div>
                        <div class="pass">victim123</div>
                    </div>
                    <div class="cred-item">
                        <div class="role">😈 Attacker</div>
                        <div class="user">attacker_user</div>
                        <div class="pass">attacker123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
