<?php
/**
 * Lab 27: Lab Description Page
 * IDOR in Stats API Endpoint - Exness-style Trading Platform
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 27: IDOR in Stats API Endpoint</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 2rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: #ffd700; }
        .lab-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 16px;
            overflow: hidden;
        }
        .lab-header {
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            padding: 2rem;
            color: #000;
        }
        .lab-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .lab-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .lab-content {
            padding: 2rem;
        }
        .section {
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #ffd700;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul {
            color: #ccc;
            padding-left: 1.5rem;
            line-height: 1.9;
        }
        .section li { margin-bottom: 0.5rem; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .info-item {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }
        .info-item .label {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        .info-item .value {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .tag {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            background: rgba(255, 215, 0, 0.15);
            color: #ffd700;
            border-radius: 50px;
            font-size: 0.8rem;
            margin: 0.25rem;
        }
        .tag-critical {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6b6b;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #00ff88;
            font-size: 0.9em;
        }
        .highlight-box {
            background: rgba(255, 215, 0, 0.1);
            border-left: 4px solid #ffd700;
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
            margin: 1rem 0;
        }
        .highlight-box p { margin: 0; }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
            margin: 1rem 0;
        }
        .warning-box p { margin: 0; color: #ff9999; }
        .cta-section {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn {
            padding: 0.85rem 1.75rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3); }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }
        .credential-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.25rem;
            margin-top: 1rem;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .credential-item:last-child { border-bottom: none; }
        .credential-item .label { color: #888; }
        .credential-item .value { color: #00ff88; font-family: monospace; }
        .endpoints-box {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .endpoints-box code {
            display: block;
            padding: 0.5rem;
            margin: 0.25rem 0;
            background: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="back-link">← Back to All Labs</a>
        
        <div class="lab-card">
            <div class="lab-header">
                <span class="lab-badge">Lab 27</span>
                <h1>📈 IDOR in Stats API Endpoint</h1>
                <p>Exploit API parameter manipulation to view trading statistics of any account</p>
            </div>
            
            <div class="lab-content">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Difficulty</div>
                        <div class="value" style="color: #ff6b6b;">Expert</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Category</div>
                        <div class="value">Access Control</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Time Required</div>
                        <div class="value">20-30 min</div>
                    </div>
                    <div class="info-item">
                        <div class="label">OWASP API</div>
                        <div class="value">BOLA (API1)</div>
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <span class="tag tag-critical">IDOR</span>
                    <span class="tag tag-critical">Financial Data</span>
                    <span class="tag">API Security</span>
                    <span class="tag">Parameter Tampering</span>
                    <span class="tag">Trading Platform</span>
                </div>

                <section class="section">
                    <h2>🎯 Objective</h2>
                    <p>
                        This lab simulates a forex/CFD trading platform's Personal Area (PA) with 
                        performance statistics APIs. Your goal is to exploit IDOR vulnerabilities 
                        in the Stats API endpoints to access financial data of other traders' 
                        MetaTrader accounts.
                    </p>
                    <div class="highlight-box">
                        <p>
                            <strong>Goal:</strong> Access the equity, net profit, and trading statistics 
                            of the victim's high-value accounts (MT5-200001, MT5-200002) and the 
                            whale's massive accounts (MT5-300001).
                        </p>
                    </div>
                </section>

                <section class="section">
                    <h2>📖 Background</h2>
                    <p>
                        Based on a real HackerOne bug bounty report where a researcher discovered 
                        that the Stats API endpoints on a trading platform's performance dashboard 
                        were vulnerable to IDOR. The <code>accounts</code> parameter accepted any 
                        account number without verifying ownership.
                    </p>
                    <p>
                        The vulnerable endpoints returned sensitive financial data including:
                    </p>
                    <ul>
                        <li>Account equity (current account value)</li>
                        <li>Net profit over time periods</li>
                        <li>Number of closed orders</li>
                        <li>Trading volume history</li>
                    </ul>
                </section>

                <section class="section">
                    <h2>🔓 Vulnerable Endpoints</h2>
                    <div class="endpoints-box">
                        <code>/api/stats/equity.php?time_range=365&accounts={accountNumber}</code>
                        <code>/api/stats/net_profit.php?time_range=365&accounts={accountNumber}</code>
                        <code>/api/stats/orders_number.php?time_range=365&accounts={accountNumber}</code>
                        <code>/api/stats/trading_volume.php?time_range=365&accounts={accountNumber}</code>
                    </div>
                </section>

                <section class="section">
                    <h2>🔐 Test Credentials</h2>
                    <p>Use these credentials to access the lab:</p>
                    <div class="credential-box">
                        <div class="credential-item">
                            <span class="label">Attacker</span>
                            <span class="value">attacker / attacker123</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">Victim (High Value)</span>
                            <span class="value">victim / victim123</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">Whale (Massive)</span>
                            <span class="value">whale / whale123</span>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <h2>🎯 Target Accounts</h2>
                    <div class="credential-box">
                        <div class="credential-item">
                            <span class="label">MT5-200001</span>
                            <span class="value">Victim's Pro (~$87,500)</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">MT5-200002</span>
                            <span class="value">Victim's Raw (~$125,000)</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">MT5-300001</span>
                            <span class="value">Whale's Zero (~$2,500,000)</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">MT5-000001</span>
                            <span class="value">Admin (~$10,000,000)</span>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <h2>💡 Hints</h2>
                    <ul>
                        <li>Log in as the attacker and navigate to the Performance page</li>
                        <li>Observe how the stats API fetches data for your accounts</li>
                        <li>Notice the <code>accounts</code> parameter in the API requests</li>
                        <li>Try changing this parameter to other account numbers</li>
                        <li>Account numbers follow a predictable pattern: MT5-XXXXXX</li>
                    </ul>
                    
                    <div class="warning-box">
                        <p>
                            <strong>⚠️ Note:</strong> This lab intentionally contains vulnerable code 
                            for educational purposes. Never deploy this in a production environment.
                        </p>
                    </div>
                </section>

                <div class="cta-section">
                    <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
                    <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
                    <a href="setup_db.php" class="btn btn-secondary">🔧 Setup Database</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
