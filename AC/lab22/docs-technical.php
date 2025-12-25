<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Deep Dive - Lab 22</title>
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
        .code-block {
            background: #0d1117;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
            border-left: 3px solid #f59e0b;
        }
        .code-block.vulnerable { border-left-color: #ef4444; }
        .code-block.secure { border-left-color: #10b981; }
        .code-block pre { color: #e2e8f0; font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; }
        .code-block .comment { color: #6a737d; }
        .code-block .keyword { color: #ff7b72; }
        .code-block .string { color: #a5d6ff; }
        .code-block .variable { color: #ffa657; }
        .label {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        .label-vuln { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .label-secure { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .schema-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.9rem;
        }
        .schema-table th, .schema-table td {
            padding: 0.5rem;
            text-align: left;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }
        .schema-table th { background: rgba(6, 182, 212, 0.1); color: #22d3ee; }
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
        <h1>🔬 Technical Deep Dive</h1>
        <p class="subtitle">Code-level analysis of the IDOR vulnerability</p>

        <div class="card">
            <h2>📊 Database Schema</h2>
            <p>Understanding the data model is crucial for exploiting IDOR vulnerabilities:</p>
            
            <h3>bookings Table</h3>
            <table class="schema-table">
                <tr><th>Column</th><th>Type</th><th>Description</th></tr>
                <tr><td>booking_id</td><td>VARCHAR(50)</td><td>Primary key, format: BKG_xxxxxxxxxxxx</td></tr>
                <tr><td>passenger_id</td><td>VARCHAR(50)</td><td>FK to users - Should be used for authorization!</td></tr>
                <tr><td>pickup_address</td><td>TEXT</td><td>⚠️ Sensitive - Home address</td></tr>
                <tr><td>dropoff_address</td><td>TEXT</td><td>⚠️ Sensitive - Destination</td></tr>
                <tr><td>pickup_lat/lng</td><td>DECIMAL</td><td>⚠️ Sensitive - Exact GPS coordinates</td></tr>
            </table>
            
            <h3>bids Table</h3>
            <table class="schema-table">
                <tr><th>Column</th><th>Type</th><th>Description</th></tr>
                <tr><td>driver_id</td><td>VARCHAR(50)</td><td>Driver's user ID</td></tr>
                <tr><td>driver_name</td><td>VARCHAR(100)</td><td>⚠️ Sensitive - Driver PII</td></tr>
                <tr><td>driver_phone</td><td>VARCHAR(20)</td><td>⚠️ Sensitive - Contact info</td></tr>
                <tr><td>vehicle_number</td><td>VARCHAR(20)</td><td>⚠️ Sensitive - Can identify vehicle</td></tr>
            </table>
        </div>

        <div class="card">
            <h2>🔴 Vulnerable Code</h2>
            
            <h3>api/bookings.php</h3>
            <span class="label label-vuln">⚠️ VULNERABLE</span>
            <div class="code-block vulnerable">
<pre><span class="comment">// ⚠️ VULNERABLE: No ownership check!</span>
<span class="variable">$booking_id</span> = <span class="variable">$_GET</span>[<span class="string">'booking_id'</span>];

<span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="keyword">prepare</span>(<span class="string">"
    SELECT * FROM bookings 
    WHERE booking_id = ?
"</span>);
<span class="variable">$stmt</span>-><span class="keyword">execute</span>([<span class="variable">$booking_id</span>]);
<span class="variable">$booking</span> = <span class="variable">$stmt</span>-><span class="keyword">fetch</span>();

<span class="comment">// Returns ANY booking, regardless of who owns it!</span>
<span class="keyword">jsonResponse</span>([<span class="string">'data'</span> => <span class="variable">$booking</span>]);</pre>
            </div>

            <h3>The Missing Authorization Check</h3>
            <p>The code only verifies that the user is <em>authenticated</em> (logged in), but doesn't verify <em>authorization</em> (permission to access this specific resource).</p>
            
            <div class="code-block">
<pre><span class="comment">// Authentication ✅ - Checks if user is logged in</span>
<span class="keyword">if</span> (!<span class="keyword">isset</span>(<span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>])) {
    <span class="keyword">jsonResponse</span>([<span class="string">'error'</span> => <span class="string">'Auth required'</span>], 401);
}

<span class="comment">// Authorization ❌ - Missing! Should check:</span>
<span class="comment">// if ($booking['passenger_id'] !== $_SESSION['user_id'])</span></pre>
            </div>
        </div>

        <div class="card">
            <h2>🟢 Secure Implementation</h2>
            <span class="label label-secure">✅ SECURE</span>
            <div class="code-block secure">
<pre><span class="variable">$booking_id</span> = <span class="variable">$_GET</span>[<span class="string">'booking_id'</span>];
<span class="variable">$user_id</span> = <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>];

<span class="comment">// ✅ SECURE: Include ownership check in query</span>
<span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="keyword">prepare</span>(<span class="string">"
    SELECT * FROM bookings 
    WHERE booking_id = ? AND passenger_id = ?
"</span>);
<span class="variable">$stmt</span>-><span class="keyword">execute</span>([<span class="variable">$booking_id</span>, <span class="variable">$user_id</span>]);
<span class="variable">$booking</span> = <span class="variable">$stmt</span>-><span class="keyword">fetch</span>();

<span class="keyword">if</span> (!<span class="variable">$booking</span>) {
    <span class="comment">// Return 404 (not 403) to avoid enumeration</span>
    <span class="keyword">jsonResponse</span>([<span class="string">'error'</span> => <span class="string">'Booking not found'</span>], 404);
}</pre>
            </div>
        </div>

        <div class="card">
            <h2>📡 API Request Flow</h2>
            <div class="code-block">
<pre>┌──────────────┐     GET /api/bookings.php?booking_id=BKG_xxx     ┌──────────────┐
│              │ ──────────────────────────────────────────────▶  │              │
│   Attacker   │                                                   │    Server    │
│              │ ◀──────────────────────────────────────────────  │              │
└──────────────┘     { "data": { "passenger_name": "Alice",       └──────────────┘
                              "phone": "+1-555-0123",
                              "address": "123 Home St" } }

<span class="comment">The server should check:</span>
1. Is the user authenticated? ✅ (Currently done)
2. Does booking exist? ✅ (Currently done)
3. Does user own this booking? ❌ (MISSING!)
4. Is user a driver for this booking? ❌ (MISSING!)</pre>
            </div>
        </div>

        <div class="card">
            <h2>🔑 ID Format Analysis</h2>
            <p>Understanding ID formats helps in enumeration:</p>
            <div class="code-block">
<pre><span class="comment">// Booking ID format: BKG_xxxxxxxxxxxx (12 hex chars)</span>
BKG_65f4e3d2c1b0  <span class="comment">// Victim's booking</span>
BKG_78a9b0c1d2e3  <span class="comment">// Victim's booking</span>
BKG_12e3f4g5h6i7  <span class="comment">// Attacker's booking</span>

<span class="comment">// The format is predictable - sequential or timestamp-based</span>
<span class="comment">// Enumeration possible through:</span>
<span class="comment">// 1. Incrementing/decrementing values</span>
<span class="comment">// 2. Dictionary attacks on hex patterns</span>
<span class="comment">// 3. Observing patterns in own bookings</span></pre>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="docs-remediation.php" class="btn btn-primary">Next: Remediation Guide →</a>
            <a href="docs-vulnerability.php" class="btn btn-secondary">← Previous</a>
        </div>
    </div>
</body>
</html>
