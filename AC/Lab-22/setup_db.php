<?php
// Lab 22: Database Setup Script

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 22</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
            color: #e2e8f0;
            min-height: 100vh;
            padding: 2rem;
        }
        .container { max-width: 900px; margin: 0 auto; }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #22d3ee;
            font-size: 2rem;
        }
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .success { border-color: #10b981; background: rgba(16, 185, 129, 0.1); }
        .error { border-color: #ef4444; background: rgba(239, 68, 68, 0.1); }
        .info { border-color: #06b6d4; }
        h3 { color: #22d3ee; margin-bottom: 1rem; }
        p { color: #94a3b8; margin-bottom: 0.5rem; line-height: 1.6; }
        .status { font-weight: 600; }
        .status.ok { color: #10b981; }
        .status.fail { color: #ef4444; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
        }
        th { color: #22d3ee; font-weight: 600; }
        td { color: #e2e8f0; }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-passenger { background: rgba(6, 182, 212, 0.2); color: #22d3ee; }
        .badge-driver { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-right: 1rem;
            margin-top: 1rem;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
            color: #22d3ee;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚗 Lab 22: RideKea Database Setup</h1>";

try {
    // Connect without database
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    $pdo->exec($sql);
    
    echo "<div class='card success'>
            <h3>✅ Database Setup Successful</h3>
            <p><span class='status ok'>Database 'ac_lab22' created and populated!</span></p>
          </div>";
    
    // Connect to the new database
    $pdo = new PDO("mysql:host=$host;dbname=ac_lab22", $user, $pass);
    
    // Show users
    $stmt = $pdo->query("SELECT user_id, username, password, phone, full_name, user_type FROM users ORDER BY user_type, id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='card info'>
            <h3>👥 Test Credentials</h3>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Name</th>
                    <th>User ID</th>
                </tr>";
    
    foreach ($users as $u) {
        $badge = $u['user_type'] === 'driver' ? 'badge-driver' : 'badge-passenger';
        echo "<tr>
                <td><code>{$u['username']}</code></td>
                <td><code>{$u['password']}</code></td>
                <td><span class='badge {$badge}'>{$u['user_type']}</span></td>
                <td>{$u['full_name']}</td>
                <td><code style='font-size:0.7rem'>{$u['user_id']}</code></td>
              </tr>";
    }
    
    echo "</table></div>";
    
    // Show bookings
    $stmt = $pdo->query("SELECT booking_id, trip_no, passenger_id, status FROM bookings ORDER BY created_at DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='card info'>
            <h3>📦 Sample Bookings</h3>
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Trip No</th>
                    <th>Passenger</th>
                    <th>Status</th>
                </tr>";
    
    foreach ($bookings as $b) {
        echo "<tr>
                <td><code style='font-size:0.75rem'>{$b['booking_id']}</code></td>
                <td>{$b['trip_no']}</td>
                <td><code style='font-size:0.7rem'>{$b['passenger_id']}</code></td>
                <td>{$b['status']}</td>
              </tr>";
    }
    
    echo "</table></div>";
    
    // Attack scenario
    echo "<div class='card' style='border-color: #f59e0b; background: rgba(245, 158, 11, 0.1);'>
            <h3>🎯 Attack Scenario</h3>
            <p><strong>Victim:</strong> <code>victim_user</code> / <code>victim123</code></p>
            <p><strong>Attacker:</strong> <code>attacker_user</code> / <code>attacker123</code></p>
            <p style='margin-top: 1rem;'><strong>Target Booking IDs (Victim's):</strong></p>
            <ul style='margin-left: 1.5rem; margin-top: 0.5rem;'>
                <li><code>BKG_65f4e3d2c1b0</code> - Completed trip to Airport</li>
                <li><code>BKG_78a9b0c1d2e3</code> - Completed trip to Mall</li>
                <li><code>BKG_90c1d2e3f4g5</code> - Pending trip to Hospital (has active bids!)</li>
            </ul>
          </div>";
    
    echo "<div class='card'>
            <h3>🚀 Next Steps</h3>
            <p>1. Login as <code>attacker_user</code> to exploit the IDOR vulnerability</p>
            <p>2. Access victim's booking details by changing the booking_id parameter</p>
            <p>3. View victim's bids and sensitive location data</p>
            <a href='index.php' class='btn'>🏠 Go to Lab</a>
            <a href='login.php' class='btn'>🔑 Login</a>
            <a href='lab-description.php' class='btn'>📖 Lab Guide</a>
          </div>";
    
} catch (PDOException $e) {
    echo "<div class='card error'>
            <h3>❌ Setup Failed</h3>
            <p><span class='status fail'>Error: " . htmlspecialchars($e->getMessage()) . "</span></p>
            <p>Make sure MySQL is running with credentials: root/root</p>
          </div>";
}

echo "</div></body></html>";
?>
