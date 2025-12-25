<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HackerOne Report Analysis - Lab 22</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
            color: #e2e8f0;
            min-height: 100vh;
            line-height: 1.7;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(6, 182, 212, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
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
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        h1 { color: #22d3ee; font-size: 2rem; margin-bottom: 0.5rem; }
        .subtitle { color: #64748b; margin-bottom: 2rem; }
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card h2 { color: #22d3ee; margin-bottom: 1rem; }
        .card h3 { color: #f59e0b; margin: 1.5rem 0 1rem; }
        .card p { color: #94a3b8; margin-bottom: 1rem; }
        .report-header {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(239, 68, 68, 0.1));
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .report-header h2 { color: #f59e0b; margin-bottom: 1rem; }
        .report-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .meta-item {
            background: rgba(15, 23, 42, 0.5);
            padding: 0.75rem;
            border-radius: 8px;
        }
        .meta-item .label { color: #64748b; font-size: 0.8rem; }
        .meta-item .value { color: #e2e8f0; font-weight: 600; }
        .bounty-badge {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
        }
        .timeline {
            position: relative;
            padding-left: 2rem;
            margin: 1rem 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(6, 182, 212, 0.3);
        }
        .timeline-item {
            position: relative;
            padding: 1rem;
            margin-bottom: 1rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 8px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 1.25rem;
            width: 10px;
            height: 10px;
            background: #22d3ee;
            border-radius: 50%;
            transform: translateX(-4px);
        }
        .timeline-item .date { color: #64748b; font-size: 0.8rem; }
        .timeline-item .event { color: #e2e8f0; }
        .quote-block {
            background: rgba(15, 23, 42, 0.7);
            border-left: 4px solid #f59e0b;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
            font-style: italic;
            color: #94a3b8;
        }
        .code-block {
            background: #0d1117;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block pre { color: #e2e8f0; font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 0.25rem;
        }
        .btn-primary { background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; }
        .btn-secondary { background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); color: #22d3ee; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="docs.php">← Docs</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="login.php">🔑 Login</a>
        </nav>
    </header>

    <div class="container">
        <h1>📋 HackerOne Report Analysis</h1>
        <p class="subtitle">Study of the original Bykea bug bounty report</p>

        <div class="report-header">
            <h2>Report: IDOR in Booking Detail and Bids</h2>
            <div class="report-meta">
                <div class="meta-item">
                    <div class="label">Program</div>
                    <div class="value">Bykea Bug Bounty</div>
                </div>
                <div class="meta-item">
                    <div class="label">Severity</div>
                    <div class="value">Medium</div>
                </div>
                <div class="meta-item">
                    <div class="label">Vulnerability Type</div>
                    <div class="value">IDOR - Information Disclosure</div>
                </div>
                <div class="meta-item">
                    <div class="label">Platform</div>
                    <div class="value">Mobile API (Android/iOS)</div>
                </div>
            </div>
            <span class="bounty-badge">💰 $500 Bounty Awarded</span>
        </div>

        <div class="card">
            <h2>📝 Report Summary</h2>
            <p>The researcher discovered that authenticated users could access other users' booking details, bid information, and bid configurations by manipulating the <code>booking_id</code> (also called <code>trip_id</code>) parameter in API requests.</p>
            
            <div class="quote-block">
                "The only information user needed was Booking id(trip_id), Authentication token. The response contains name,phone number, location, gps coordinates, all bids information of other users."
            </div>
        </div>

        <div class="card">
            <h2>🎯 Vulnerable Endpoints Identified</h2>
            
            <h3>1. Booking Details Endpoint</h3>
            <div class="code-block">
<pre>GET /partner/booking_detail?booking_id=1234567
Host: api.bykea.cash
Authorization: Bearer {attacker_token}

Response: Full booking details including passenger PII</pre>
            </div>
            
            <h3>2. Bids Information Endpoint</h3>
            <div class="code-block">
<pre>GET /partner/get_bids?booking_id=1234567
Host: api.bykea.cash
Authorization: Bearer {attacker_token}

Response: All driver bids with contact information</pre>
            </div>
            
            <h3>3. Bids Configuration Endpoint</h3>
            <div class="code-block">
<pre>GET /get_bids_config?trip_id=1234567
Host: api.bykea.cash
Authorization: Bearer {attacker_token}

Response: Bid settings and thresholds</pre>
            </div>
        </div>

        <div class="card">
            <h2>⏱️ Report Timeline</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="date">Day 0</div>
                    <div class="event">Report submitted to HackerOne</div>
                </div>
                <div class="timeline-item">
                    <div class="date">Day 1</div>
                    <div class="event">Triaged by Bykea security team</div>
                </div>
                <div class="timeline-item">
                    <div class="date">Day 7</div>
                    <div class="event">Vulnerability confirmed</div>
                </div>
                <div class="timeline-item">
                    <div class="date">Day 14</div>
                    <div class="event">Fix deployed to production</div>
                </div>
                <div class="timeline-item">
                    <div class="date">Day 21</div>
                    <div class="event">$500 bounty awarded</div>
                </div>
                <div class="timeline-item">
                    <div class="date">Day 30</div>
                    <div class="event">Report disclosed publicly</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>✍️ Writing an Effective Report</h2>
            <p>Key elements that made this report successful:</p>
            
            <h3>1. Clear Title</h3>
            <p>"IDOR in Booking Detail and Bids Could Lead to Sensitive Information Disclosure" - Specific and describes the impact</p>
            
            <h3>2. Detailed Steps to Reproduce</h3>
            <p>The researcher provided exact API endpoints, request formats, and expected responses</p>
            
            <h3>3. Impact Statement</h3>
            <p>Clearly explained what data was exposed and potential real-world consequences</p>
            
            <h3>4. Multiple Endpoints</h3>
            <p>Researcher tested related endpoints, finding the same vulnerability in three places</p>
            
            <h3>5. Proof of Concept</h3>
            <p>Included screenshots and sample API responses showing actual data exposure</p>
        </div>

        <div class="card">
            <h2>💡 Key Takeaways</h2>
            <ul style="color: #94a3b8; margin-left: 1.5rem;">
                <li><strong>Test Related Endpoints:</strong> If one API has IDOR, check all similar APIs</li>
                <li><strong>ID Enumeration:</strong> Simple sequential IDs make exploitation trivial</li>
                <li><strong>Authentication ≠ Authorization:</strong> Being logged in doesn't mean access to all data</li>
                <li><strong>Mobile APIs:</strong> Often have weaker access controls than web applications</li>
                <li><strong>PII Sensitivity:</strong> Location data + contact info is high-impact</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="docs-exploitation.php" class="btn btn-primary">Next: Exploitation Techniques →</a>
            <a href="docs-remediation.php" class="btn btn-secondary">← Previous</a>
        </div>
    </div>
</body>
</html>
