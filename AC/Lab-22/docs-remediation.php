<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remediation Guide - Lab 22</title>
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
        .card ul { color: #94a3b8; margin-left: 1.5rem; margin-bottom: 1rem; }
        .card li { margin-bottom: 0.5rem; }
        .code-block {
            background: #0d1117;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
            border-left: 3px solid #10b981;
        }
        .code-block pre { color: #e2e8f0; font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; }
        .fix-step {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            padding: 1.25rem;
            margin: 1rem 0;
            border-radius: 0 12px 12px 0;
        }
        .fix-step h4 { color: #10b981; margin-bottom: 0.5rem; }
        .fix-step p { color: #94a3b8; margin: 0; }
        .checklist {
            list-style: none;
            margin: 1rem 0;
        }
        .checklist li {
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.5);
            margin: 0.5rem 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .checklist li::before { content: '☐'; color: #22d3ee; }
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
        <h1>🛡️ Remediation Guide</h1>
        <p class="subtitle">How to properly fix IDOR vulnerabilities in booking systems</p>

        <div class="card">
            <h2>✅ Fix 1: Add Ownership Verification</h2>
            <p>The primary fix is to always verify resource ownership before returning data:</p>
            
            <div class="code-block">
<pre>// api/bookings.php - SECURE VERSION

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

// Option 1: Include ownership in query
$stmt = $pdo->prepare("
    SELECT * FROM bookings 
    WHERE booking_id = ? 
    AND (passenger_id = ? OR driver_id = ?)
");
$stmt->execute([$booking_id, $user_id, $user_id]);
$booking = $stmt->fetch();

// Option 2: Separate authorization check
$booking = getBooking($booking_id);
if (!canUserAccessBooking($user_id, $booking)) {
    jsonResponse(['error' => 'Access denied'], 403);
}</pre>
            </div>
        </div>

        <div class="card">
            <h2>✅ Fix 2: Implement Role-Based Access</h2>
            <p>Different users should have different access levels:</p>
            
            <div class="code-block">
<pre>function canUserAccessBooking($user_id, $booking) {
    // Passenger can view their own bookings
    if ($booking['passenger_id'] === $user_id) {
        return true;
    }
    
    // Assigned driver can view the booking
    if ($booking['driver_id'] === $user_id) {
        return true;
    }
    
    // Bidding drivers can see limited info
    if (userHasBidOnBooking($user_id, $booking['booking_id'])) {
        return 'limited'; // Return subset of data
    }
    
    // Admin can see all
    if (isAdmin($user_id)) {
        return true;
    }
    
    return false;
}</pre>
            </div>
        </div>

        <div class="card">
            <h2>✅ Fix 3: Use Unpredictable IDs</h2>
            <p>While not a complete fix, using unpredictable IDs makes enumeration harder:</p>
            
            <div class="code-block">
<pre>// Instead of sequential IDs:
// BKG_000001, BKG_000002, BKG_000003

// Use UUIDs or cryptographically random IDs:
function generateBookingId() {
    return 'BKG_' . bin2hex(random_bytes(16));
    // Result: BKG_a7f3e9c1b2d4f6a8c0e2d4f6a8b0c2e4
}

// Important: This is defense-in-depth, NOT a replacement
// for proper authorization checks!</pre>
            </div>
        </div>

        <div class="card">
            <h2>✅ Fix 4: Implement Indirect References</h2>
            <p>Map user-specific references to actual database IDs:</p>
            
            <div class="code-block">
<pre>// Create a session-based mapping
$_SESSION['booking_refs'] = [
    'booking_1' => 'BKG_65f4e3d2c1b0',
    'booking_2' => 'BKG_78a9b0c1d2e3',
];

// API accepts indirect reference
// GET /api/bookings.php?ref=booking_1

$ref = $_GET['ref'];
if (!isset($_SESSION['booking_refs'][$ref])) {
    jsonResponse(['error' => 'Invalid reference'], 404);
}

$booking_id = $_SESSION['booking_refs'][$ref];
// Now fetch the booking - user can only reference their own</pre>
            </div>
        </div>

        <div class="card">
            <h2>🧪 Testing Checklist</h2>
            <ul class="checklist">
                <li>Test accessing own resources with valid IDs</li>
                <li>Test accessing other users' resources</li>
                <li>Test with admin vs regular user accounts</li>
                <li>Test enumeration of sequential IDs</li>
                <li>Test authorization after session changes</li>
                <li>Test for information leakage in error messages</li>
                <li>Test bulk access attempts (rate limiting)</li>
                <li>Test with Burp Suite intruder for automation</li>
            </ul>
        </div>

        <div class="card">
            <h2>📋 Security Best Practices</h2>
            <div class="fix-step">
                <h4>1. Principle of Least Privilege</h4>
                <p>Only return the minimum data necessary for the operation</p>
            </div>
            <div class="fix-step">
                <h4>2. Defense in Depth</h4>
                <p>Layer multiple security controls - don't rely on obscurity alone</p>
            </div>
            <div class="fix-step">
                <h4>3. Fail Securely</h4>
                <p>When authorization fails, return 404 (not 403) to prevent enumeration</p>
            </div>
            <div class="fix-step">
                <h4>4. Log Access Attempts</h4>
                <p>Log all access attempts for auditing and detecting attacks</p>
            </div>
            <div class="fix-step">
                <h4>5. Regular Security Testing</h4>
                <p>Include IDOR testing in your CI/CD pipeline and penetration tests</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="docs-report.php" class="btn btn-primary">Next: HackerOne Report →</a>
            <a href="docs-technical.php" class="btn btn-secondary">← Previous</a>
        </div>
    </div>
</body>
</html>
