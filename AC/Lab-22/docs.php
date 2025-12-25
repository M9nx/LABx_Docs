<?php
// Lab 22: Documentation Hub
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDOR Booking & Bids | Lab 22</title>
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
        .logo { font-size: 1.5rem; font-weight: bold; color: #22d3ee; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #22d3ee;
            text-decoration: none;
            border-radius: 6px;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .hero {
            text-align: center;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .hero h1 { font-size: 2.5rem; color: #22d3ee; margin-bottom: 0.5rem; }
        .hero p { color: #64748b; font-size: 1.1rem; }
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .doc-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        .doc-card:hover {
            border-color: #06b6d4;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.2);
        }
        .doc-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .doc-card h3 {
            color: #22d3ee;
            margin-bottom: 0.75rem;
        }
        .doc-card p {
            color: #94a3b8;
            line-height: 1.6;
            flex: 1;
            margin-bottom: 1rem;
        }
        .doc-card .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .doc-card .tag {
            background: rgba(6, 182, 212, 0.1);
            color: #22d3ee;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .doc-card .btn {
            display: inline-block;
            text-align: center;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .doc-card .btn:hover { transform: scale(1.02); }
        .quick-links {
            background: rgba(30, 41, 59, 0.6);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
        }
        .quick-links h2 {
            color: #22d3ee;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .quick-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 8px;
            color: #e2e8f0;
            text-decoration: none;
            transition: all 0.3s;
        }
        .quick-link:hover {
            background: rgba(6, 182, 212, 0.1);
            border-color: rgba(6, 182, 212, 0.4);
        }
        .quick-link .icon { font-size: 1.25rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="index.php">← Back</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="login.php">🔑 Login</a>
        </nav>
    </header>

    <div class="container">
        <div class="hero">
            <h1>📚 Lab 22 Documentation</h1>
            <p>Comprehensive guides for understanding and exploiting IDOR in Booking & Bids</p>
        </div>

        <div class="docs-grid">
            <div class="doc-card">
                <div class="icon">🎯</div>
                <h3>Vulnerability Analysis</h3>
                <p>Deep dive into the IDOR vulnerability affecting ride-sharing booking systems. Learn how missing authorization checks expose sensitive passenger and driver data.</p>
                <div class="tags">
                    <span class="tag">IDOR</span>
                    <span class="tag">API Security</span>
                    <span class="tag">Authorization</span>
                </div>
                <a href="docs-vulnerability.php" class="btn">Read Analysis →</a>
            </div>

            <div class="doc-card">
                <div class="icon">🔬</div>
                <h3>Technical Deep Dive</h3>
                <p>Examine the vulnerable code patterns, API structure, and database schema. Understand exactly why the vulnerability exists at a technical level.</p>
                <div class="tags">
                    <span class="tag">Code Review</span>
                    <span class="tag">SQL</span>
                    <span class="tag">API Design</span>
                </div>
                <a href="docs-technical.php" class="btn">View Technical Details →</a>
            </div>

            <div class="doc-card">
                <div class="icon">🛡️</div>
                <h3>Remediation Guide</h3>
                <p>Learn how to properly fix IDOR vulnerabilities in booking systems. Includes secure code examples, authorization patterns, and testing strategies.</p>
                <div class="tags">
                    <span class="tag">Security Fix</span>
                    <span class="tag">Best Practices</span>
                    <span class="tag">Testing</span>
                </div>
                <a href="docs-remediation.php" class="btn">View Fixes →</a>
            </div>

            <div class="doc-card">
                <div class="icon">📋</div>
                <h3>HackerOne Report</h3>
                <p>Study the original bug bounty report that inspired this lab. Learn how to write effective vulnerability reports and maximize bounty payouts.</p>
                <div class="tags">
                    <span class="tag">Bug Bounty</span>
                    <span class="tag">Bykea</span>
                    <span class="tag">$500 Bounty</span>
                </div>
                <a href="docs-report.php" class="btn">Read Report →</a>
            </div>

            <div class="doc-card">
                <div class="icon">🧪</div>
                <h3>Exploitation Techniques</h3>
                <p>Master different methods to discover and exploit IDOR vulnerabilities. Covers ID enumeration, API testing with tools like Burp Suite, and automation.</p>
                <div class="tags">
                    <span class="tag">Burp Suite</span>
                    <span class="tag">Automation</span>
                    <span class="tag">Enumeration</span>
                </div>
                <a href="docs-exploitation.php" class="btn">Learn Techniques →</a>
            </div>

            <div class="doc-card">
                <div class="icon">🏗️</div>
                <h3>Architecture Overview</h3>
                <p>Understand the ride-sharing application architecture, data flows, and how booking and bid systems interact. Essential context for security testing.</p>
                <div class="tags">
                    <span class="tag">System Design</span>
                    <span class="tag">Data Flow</span>
                    <span class="tag">Architecture</span>
                </div>
                <a href="docs-architecture.php" class="btn">View Architecture →</a>
            </div>
        </div>

        <div class="quick-links">
            <h2>🔗 Quick Links</h2>
            <div class="links-grid">
                <a href="login.php" class="quick-link">
                    <span class="icon">🔑</span>
                    <span>Login Page</span>
                </a>
                <a href="dashboard.php" class="quick-link">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="my-bookings.php" class="quick-link">
                    <span class="icon">📦</span>
                    <span>My Bookings</span>
                </a>
                <a href="api/bookings.php?booking_id=BKG_65f4e3d2c1b0" class="quick-link" target="_blank">
                    <span class="icon">📡</span>
                    <span>Bookings API</span>
                </a>
                <a href="api/bids.php?booking_id=BKG_90c1d2e3f4g5" class="quick-link" target="_blank">
                    <span class="icon">💰</span>
                    <span>Bids API</span>
                </a>
                <a href="lab-description.php" class="quick-link">
                    <span class="icon">📖</span>
                    <span>Lab Guide</span>
                </a>
                <a href="success.php" class="quick-link">
                    <span class="icon">🏆</span>
                    <span>Success Page</span>
                </a>
                <a href="../" class="quick-link">
                    <span class="icon">🏠</span>
                    <span>All Labs</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
