<?php
// Lab 22: Lab Description & Exploitation Guide
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Guide - IDOR Booking & Bids | Lab 22</title>
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
        .container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        .hero {
            text-align: center;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .hero h1 { font-size: 2.5rem; color: #22d3ee; margin-bottom: 0.5rem; }
        .hero .subtitle { color: #64748b; font-size: 1.1rem; }
        .lab-badge {
            display: inline-block;
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card h2 {
            color: #22d3ee;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card h3 {
            color: #f59e0b;
            margin: 1.5rem 0 1rem;
        }
        .card p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .cred-box {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }
        .cred-box h4 {
            color: #22d3ee;
            margin-bottom: 0.75rem;
        }
        .cred-box.victim { border: 1px solid rgba(239, 68, 68, 0.4); }
        .cred-box.attacker { border: 1px solid rgba(16, 185, 129, 0.4); }
        .cred-box .role {
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-bottom: 0.75rem;
            display: inline-block;
        }
        .cred-box.victim .role { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .cred-box.attacker .role { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .cred-detail { margin: 0.5rem 0; color: #94a3b8; }
        .cred-detail span { color: #e2e8f0; font-family: monospace; }
        .step {
            background: rgba(15, 23, 42, 0.6);
            border-left: 4px solid #06b6d4;
            padding: 1.25rem;
            margin: 1rem 0;
            border-radius: 0 12px 12px 0;
        }
        .step h4 {
            color: #22d3ee;
            margin-bottom: 0.5rem;
        }
        .step p { color: #94a3b8; margin: 0; }
        .step code {
            background: rgba(0, 0, 0, 0.4);
            color: #f59e0b;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
        }
        .endpoint-list {
            list-style: none;
            margin: 1rem 0;
        }
        .endpoint-list li {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.75rem 1rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            border-left: 3px solid #f59e0b;
        }
        .endpoint-list code {
            color: #22d3ee;
            font-family: monospace;
        }
        .target-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .target-box h4 { color: #f87171; margin-bottom: 1rem; }
        .target-box ul { list-style: none; }
        .target-box li {
            padding: 0.5rem 0;
            color: #fca5a5;
            font-family: monospace;
        }
        .api-example {
            background: #0d1117;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .api-example pre {
            color: #e2e8f0;
            font-family: monospace;
            font-size: 0.85rem;
            white-space: pre-wrap;
        }
        .warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
        }
        .warning .icon { font-size: 1.5rem; }
        .warning p { color: #f59e0b; margin: 0; }
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
        }
        th { color: #22d3ee; background: rgba(6, 182, 212, 0.1); }
        td { color: #e2e8f0; }
        td code { color: #f59e0b; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="index.php">← Back</a>
            <a href="login.php">🔑 Login</a>
            <a href="docs.php">📚 Docs</a>
            <a href="dashboard.php">📊 Dashboard</a>
        </nav>
    </header>

    <div class="container">
        <div class="hero">
            <h1>🎯 Lab 22: IDOR on Booking & Bids</h1>
            <p class="subtitle">Information Disclosure in Ride-Sharing Application</p>
            <span class="lab-badge">⚠️ MEDIUM SEVERITY - $500 BOUNTY</span>
        </div>

        <div class="card">
            <h2>📖 Vulnerability Overview</h2>
            <p>This lab is based on a real HackerOne report from the Bykea Bug Bounty Program. The vulnerability allows any authenticated user to view another user's booking details, driver bids, and bid configurations by simply changing the booking ID parameter in API requests.</p>
            
            <h3>🔍 What's Exposed?</h3>
            <p>The IDOR vulnerability exposes highly sensitive information:</p>
            <ul style="color: #94a3b8; margin-left: 1.5rem; margin-top: 0.5rem;">
                <li><strong>Booking Details:</strong> Passenger name, phone, email, full addresses</li>
                <li><strong>Location Data:</strong> GPS coordinates of pickup/dropoff locations (could be home address!)</li>
                <li><strong>Driver Bids:</strong> Driver names, phone numbers, vehicle details, ratings</li>
                <li><strong>Bid Config:</strong> Pricing algorithms, bid thresholds, auto-accept settings</li>
            </ul>
        </div>

        <div class="card">
            <h2>🔑 Test Credentials</h2>
            <div class="credentials-grid">
                <div class="cred-box victim">
                    <span class="role">👤 VICTIM</span>
                    <h4>victim_user</h4>
                    <div class="cred-detail">Password: <span>victim123</span></div>
                    <div class="cred-detail">User ID: <span>USR_P_65a1b2c3d4e5</span></div>
                </div>
                <div class="cred-box attacker">
                    <span class="role">☠️ ATTACKER</span>
                    <h4>attacker_user</h4>
                    <div class="cred-detail">Password: <span>attacker123</span></div>
                    <div class="cred-detail">User ID: <span>USR_P_78f9g0h1i2j3</span></div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>🎯 Vulnerable Endpoints</h2>
            <ul class="endpoint-list">
                <li><code>GET /api/bookings.php?booking_id={ID}</code> - Returns full booking details</li>
                <li><code>GET /api/bids.php?booking_id={ID}</code> - Returns driver bids with contact info</li>
                <li><code>GET /api/bids_config.php?trip_id={ID}</code> - Returns bid configuration</li>
            </ul>
            
            <div class="warning">
                <span class="icon">⚠️</span>
                <p>All three endpoints lack ownership verification - any authenticated user can access any booking!</p>
            </div>
        </div>

        <div class="card">
            <h2>🚀 Step-by-Step Exploitation</h2>
            
            <div class="step">
                <h4>Step 1: Login as Attacker</h4>
                <p>Go to <a href="login.php" style="color: #22d3ee;">Login Page</a> and sign in with <code>attacker_user / attacker123</code></p>
            </div>
            
            <div class="step">
                <h4>Step 2: Note Your Booking ID</h4>
                <p>Go to "My Bookings" and observe your booking ID format: <code>BKG_12e3f4g5h6i7</code></p>
            </div>
            
            <div class="step">
                <h4>Step 3: Discover Target Booking IDs</h4>
                <p>Target booking IDs can be enumerated or found in various ways. Here are victim's bookings:</p>
            </div>

            <div class="target-box">
                <h4>🎯 Target Booking IDs (Victim's)</h4>
                <ul>
                    <li>BKG_65f4e3d2c1b0 - Completed trip to Airport (contains HOME ADDRESS)</li>
                    <li>BKG_78a9b0c1d2e3 - Completed trip to Mall</li>
                    <li>BKG_90c1d2e3f4g5 - PENDING trip with 3 ACTIVE BIDS (driver info!)</li>
                </ul>
            </div>
            
            <div class="step">
                <h4>Step 4: Access Victim's Booking</h4>
                <p>Navigate to: <code>booking-detail.php?booking_id=BKG_65f4e3d2c1b0</code></p>
            </div>
            
            <div class="step">
                <h4>Step 5: View Driver Bids</h4>
                <p>Navigate to: <code>view-bids.php?booking_id=BKG_90c1d2e3f4g5</code></p>
            </div>
            
            <div class="step">
                <h4>Step 6: Test API Directly</h4>
                <p>Call the vulnerable API endpoints directly to see raw JSON response</p>
            </div>
        </div>

        <div class="card">
            <h2>📡 API Response Example</h2>
            <p>When accessing a victim's booking via the API:</p>
            <div class="api-example">
<pre>{
  "success": true,
  "data": {
    "booking_id": "BKG_65f4e3d2c1b0",
    "passenger": {
      "name": "Alice Victim",
      "phone": "+1-555-0123",
      "email": "alice@victim.com"
    },
    "pickup": {
      "address": "123 Home Street, Apartment 4B, Residential Area",
      "lat": 24.8607,
      "lng": 67.0011
    },
    "dropoff": {
      "address": "International Airport, Terminal 2"
    }
  }
}</pre>
            </div>
        </div>

        <div class="card">
            <h2>🛡️ Proper Fix</h2>
            <p>The secure implementation should verify ownership before returning data:</p>
            <div class="api-example">
<pre>// ✅ SECURE: Add ownership check
$stmt = $pdo->prepare("
    SELECT * FROM bookings 
    WHERE booking_id = ? AND passenger_id = ?
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    // Return 403 Forbidden or 404 Not Found
    jsonResponse(['error' => 'Access denied'], 403);
}</pre>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="login.php" class="btn btn-primary">🔑 Start Hacking</a>
            <a href="docs.php" class="btn btn-secondary">📚 Full Documentation</a>
            <a href="index.php" class="btn btn-secondary">← Back to Home</a>
        </div>
    </div>
</body>
</html>
