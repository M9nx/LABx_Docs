<?php
/**
 * Lab 27: Landing Page
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
        .header {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
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
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .nav-links a:hover { color: #ffd700; }
        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            text-align: center;
        }
        .hero-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 50px;
            color: #ffd700;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: 3rem;
            color: #fff;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .hero h1 span {
            color: #ffd700;
        }
        .hero p {
            font-size: 1.2rem;
            color: #888;
            max-width: 700px;
            margin: 0 auto 2rem;
            line-height: 1.7;
        }
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        }
        .features {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .feature-card:hover {
            border-color: rgba(255, 215, 0, 0.3);
            transform: translateY(-5px);
        }
        .feature-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .feature-card h3 {
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            color: #888;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .vuln-info {
            max-width: 900px;
            margin: 0 auto 4rem;
            padding: 2rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 16px;
        }
        .vuln-info h2 {
            color: #ff6b6b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vuln-info p {
            color: #ffaaaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .vuln-info code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 8px;
            color: #ffd700;
            font-family: monospace;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .endpoints-list {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
        }
        .endpoints-list li {
            color: #ffd700;
            font-family: monospace;
            padding: 0.5rem 0;
            list-style: none;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">📈</span>
                Exness PA
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <span class="hero-badge">Lab 27 - Access Control</span>
            <h1>IDOR in <span>Stats API</span> Endpoint</h1>
            <p>
                Exploit a vulnerable trading platform's Stats API to view equity, 
                net profit, and trading volume of ANY MetaTrader account - not just your own.
            </p>
            <div class="cta-buttons">
                <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
                <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
                <a href="lab-description.php" class="btn btn-secondary">ℹ️ Lab Info</a>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="icon">🎯</div>
                <h3>Real-World Vulnerability</h3>
                <p>Based on an actual HackerOne bug bounty report where IDOR in stats API exposed trading account data.</p>
            </div>
            <div class="feature-card">
                <div class="icon">💰</div>
                <h3>Sensitive Data Exposure</h3>
                <p>Access equity figures, net profit, trading volumes, and order counts of high-value trading accounts.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🔌</div>
                <h3>API Testing Focus</h3>
                <p>Practice API parameter manipulation and IDOR exploitation through GET request parameters.</p>
            </div>
            <div class="feature-card">
                <div class="icon">📊</div>
                <h3>Interactive Charts</h3>
                <p>Visual representation of trading statistics makes the data exposure impact more tangible.</p>
            </div>
        </section>

        <section class="vuln-info">
            <h2>🔓 Vulnerability Details</h2>
            <p>
                The Stats API endpoints accept an <code>accounts</code> parameter that specifies which 
                trading account to retrieve statistics for. The vulnerability exists because the backend 
                does NOT verify if the authenticated user owns the requested account.
            </p>
            
            <h3 style="color: #ffd700; margin: 1.5rem 0 0.75rem;">Vulnerable Endpoints:</h3>
            <ul class="endpoints-list">
                <li>/api/stats/equity.php?time_range=365&accounts={accountNumber}</li>
                <li>/api/stats/net_profit.php?time_range=365&accounts={accountNumber}</li>
                <li>/api/stats/orders_number.php?time_range=365&accounts={accountNumber}</li>
                <li>/api/stats/trading_volume.php?time_range=365&accounts={accountNumber}</li>
            </ul>
            
            <p style="margin-top: 1rem;">
                <strong>Impact:</strong> Disclosure of any MT trading account's equity, net profit, 
                closed order counts, and trading volumes. This financial information could be used 
                for competitive intelligence, social engineering, or targeted attacks.
            </p>
        </section>
    </main>
</body>
</html>
