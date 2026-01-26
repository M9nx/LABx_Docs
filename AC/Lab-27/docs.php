<?php
/**
 * Lab 27: Documentation - Part 1 (Overview & Walkthrough)
 * IDOR in Stats API Endpoint
 */

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 27: Stats API IDOR</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
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
        .main-content {
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
            margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #ffd700; }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .doc-nav {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .doc-nav a {
            padding: 0.5rem 1rem;
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 6px;
            color: #ffd700;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .doc-nav a:hover, .doc-nav a.active {
            background: rgba(255, 215, 0, 0.2);
        }
        .section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #ffd700;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section h3 {
            color: #fff;
            margin: 1.5rem 0 0.75rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            color: #ccc;
            padding-left: 1.5rem;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .code-block {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre {
            color: #e6edf3;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            margin: 0;
        }
        .code-block .comment { color: #8b949e; }
        .code-block .keyword { color: #ff7b72; }
        .code-block .string { color: #a5d6ff; }
        .code-block .function { color: #d2a8ff; }
        .code-block .variable { color: #ffa657; }
        .code-block .url { color: #7ee787; }
        .highlight-box {
            background: rgba(255, 215, 0, 0.1);
            border-left: 4px solid #ffd700;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .warning-box p { margin: 0; color: #ff9999; }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
            border-radius: 50%;
            font-weight: bold;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn {
            padding: 0.75rem 1.5rem;
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
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
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
                <a href="dashboard.php">Dashboard</a>
                <a href="docs.php" style="color: #ffd700;">Docs</a>
                <?php if (isLoggedIn()): ?>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
                <?php else: ?>
                <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="index.php" class="back-link">← Back to Lab</a>
        
        <div class="page-header">
            <h1>📚 Documentation</h1>
            <p>Complete guide to understanding and exploiting the Stats API IDOR vulnerability</p>
        </div>

        <nav class="doc-nav">
            <a href="docs.php" class="active">Overview & Walkthrough</a>
            <a href="docs-technical.php">Technical Analysis</a>
            <a href="docs-mitigation.php">Mitigation Guide</a>
        </nav>

        <section class="section">
            <h2>📋 1. Lab Overview</h2>
            
            <h3>What is IDOR?</h3>
            <p>
                <strong>Insecure Direct Object Reference (IDOR)</strong> is an access control vulnerability 
                that occurs when an application uses user-controlled input to directly access objects 
                (like database records) without proper authorization checks.
            </p>
            
            <h3>The Vulnerability Scenario</h3>
            <p>
                This lab simulates a forex trading platform's Personal Area (PA) - similar to Exness. 
                Traders can create MetaTrader (MT4/MT5) accounts and view their performance statistics 
                through a dashboard. The Stats API endpoints are responsible for fetching:
            </p>
            <ul>
                <li><strong>Equity:</strong> Current account value over time</li>
                <li><strong>Net Profit:</strong> Daily profit/loss figures</li>
                <li><strong>Orders Number:</strong> Count of executed trades</li>
                <li><strong>Trading Volume:</strong> Total lots traded</li>
            </ul>
            
            <div class="highlight-box">
                <p>
                    <strong>The Problem:</strong> The API accepts an <code>accounts</code> parameter 
                    but does NOT verify if the authenticated user owns that account. Any logged-in 
                    user can view ANY account's statistics by simply changing this parameter.
                </p>
            </div>
        </section>

        <section class="section">
            <h2>🚶 2. Step-by-Step Exploitation</h2>
            
            <h3><span class="step-number">1</span> Login as the Attacker</h3>
            <p>
                Navigate to the login page and authenticate using the attacker credentials:
            </p>
            <div class="code-block">
                <pre>Username: <span class="string">attacker</span>
Password: <span class="string">attacker123</span></pre>
            </div>

            <h3><span class="step-number">2</span> Explore Your Dashboard</h3>
            <p>
                After login, you'll see your dashboard with your trading accounts. Note that 
                you only have modest accounts with small balances (~$1,250 and $5,000).
            </p>

            <h3><span class="step-number">3</span> Navigate to Performance</h3>
            <p>
                Click on "Performance" in the navigation or "View Performance" button. This 
                page displays charts and statistics for your accounts.
            </p>

            <h3><span class="step-number">4</span> Observe the API Requests</h3>
            <p>
                Open your browser's Developer Tools (F12) and go to the Network tab. 
                Watch the requests being made. You'll see calls like:
            </p>
            <div class="code-block">
                <pre><span class="url">GET /api/stats/equity.php?time_range=365&accounts=MT5-100001</span>
<span class="url">GET /api/stats/net_profit.php?time_range=365&accounts=MT5-100001</span>
<span class="url">GET /api/stats/orders_number.php?time_range=365&accounts=MT5-100001</span>
<span class="url">GET /api/stats/trading_volume.php?time_range=365&accounts=MT5-100001</span></pre>
            </div>

            <h3><span class="step-number">5</span> Identify the Vulnerable Parameter</h3>
            <p>
                The <code>accounts</code> parameter controls which account's data is returned. 
                Your account is <code>MT5-100001</code>, but what about other accounts?
            </p>

            <h3><span class="step-number">6</span> Test with Victim's Account</h3>
            <p>
                In the Performance page, there's an input field labeled "Or Enter Account Number (IDOR Test)". 
                Enter a victim's account number:
            </p>
            <div class="code-block">
                <pre><span class="string">MT5-200001</span>  <span class="comment">// Victim's Pro account (~$87,500)</span></pre>
            </div>
            <p>Click "Load Stats" and observe the response. You now see the victim's financial data!</p>

            <h3><span class="step-number">7</span> Enumerate More Accounts</h3>
            <p>Try other account numbers to discover more sensitive data:</p>
            <div class="code-block">
                <pre><span class="string">MT5-200002</span>  <span class="comment">// Victim's Raw Spread (~$125,000)</span>
<span class="string">MT5-300001</span>  <span class="comment">// Whale's Zero account (~$2,500,000)</span>
<span class="string">MT5-300002</span>  <span class="comment">// Whale's Pro account (~$750,000)</span>
<span class="string">MT5-000001</span>  <span class="comment">// Admin's internal account (~$10,000,000)</span></pre>
            </div>

            <h3><span class="step-number">8</span> Direct API Testing (Optional)</h3>
            <p>
                You can also test directly in the browser or with curl:
            </p>
            <div class="code-block">
                <pre><span class="comment"># Direct browser URL</span>
<span class="url">http://localhost/AC/Lab-27/api/stats/equity.php?time_range=365&accounts=MT5-300001</span>

<span class="comment"># Or using curl (with session cookie)</span>
curl -b "PHPSESSID=your_session_id" \
  "<span class="url">http://localhost/AC/Lab-27/api/stats/equity.php?time_range=365&accounts=MT5-300001</span>"</pre>
            </div>
        </section>

        <section class="section">
            <h2>🔍 3. Why The Exploit Works</h2>
            
            <p>The vulnerability exists due to several security failures:</p>
            
            <h3>Missing Authorization Check</h3>
            <p>
                The API only checks if you're logged in (authentication), but NOT if you own 
                the requested account (authorization). These are two different security concerns:
            </p>
            <ul>
                <li><strong>Authentication:</strong> "Are you who you say you are?" ✅ Implemented</li>
                <li><strong>Authorization:</strong> "Are you allowed to access this resource?" ❌ Missing</li>
            </ul>

            <h3>User-Controlled Object Reference</h3>
            <p>
                The application trusts the <code>accounts</code> parameter provided by the user 
                to directly query the database. It assumes users will only request their own data.
            </p>

            <h3>Predictable Identifiers</h3>
            <p>
                Account numbers follow a predictable pattern (MT5-XXXXXX with sequential numbers), 
                making it trivial to enumerate and discover other accounts.
            </p>

            <div class="warning-box">
                <p>
                    <strong>Impact:</strong> An attacker can view equity, profits, trading volume, 
                    and order counts for ANY trading account. This exposes sensitive financial 
                    information that could be used for competitive intelligence, social engineering, 
                    or targeted attacks against high-value traders.
                </p>
            </div>
        </section>

        <div class="nav-buttons">
            <a href="lab-description.php" class="btn btn-secondary">← Lab Info</a>
            <a href="docs-technical.php" class="btn btn-primary">Technical Analysis →</a>
        </div>
    </main>
</body>
</html>
