<?php
// Lab 22: Create New Trip
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_address = $_POST['pickup_address'] ?? '';
    $dropoff_address = $_POST['dropoff_address'] ?? '';
    $customer_bid = floatval($_POST['customer_bid'] ?? 0);
    
    if (empty($pickup_address) || empty($dropoff_address) || $customer_bid <= 0) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $pdo = getDBConnection();
            
            $booking_id = generateBookingId();
            $trip_no = generateTripNo();
            
            // Generate random coordinates (for demo)
            $pickup_lat = 24.8 + (mt_rand(0, 200) / 1000);
            $pickup_lng = 67.0 + (mt_rand(0, 150) / 1000);
            $dropoff_lat = 24.8 + (mt_rand(0, 200) / 1000);
            $dropoff_lng = 67.0 + (mt_rand(0, 150) / 1000);
            
            // Calculate estimates
            $est_distance = mt_rand(2000, 15000);
            $est_time = round($est_distance / 8);
            $est_fare = round($est_distance / 20 + 50);
            
            $stmt = $pdo->prepare("INSERT INTO bookings 
                (booking_id, trip_no, passenger_id, pickup_lat, pickup_lng, pickup_address, 
                dropoff_lat, dropoff_lng, dropoff_address, est_fare, customer_bid, 
                fare_upper, fare_lower, est_distance, est_time, status, tracking_link, session_id, order_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?)");
            
            $stmt->execute([
                $booking_id, $trip_no, $_SESSION['user_id'],
                $pickup_lat, $pickup_lng, $pickup_address,
                $dropoff_lat, $dropoff_lng, $dropoff_address,
                $est_fare, $customer_bid,
                $est_fare * 1.15, $est_fare * 0.85,
                $est_distance, $est_time,
                "https://track.ridekea.net/$trip_no",
                'sess_' . substr(md5(uniqid()), 0, 10),
                'ORD_' . substr(md5(uniqid()), 0, 8)
            ]);
            
            $success = "Trip created successfully! Booking ID: $booking_id";
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Trip - RideKea | Lab 22</title>
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
        .container { max-width: 700px; margin: 0 auto; padding: 2rem; }
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-header h1 { color: #22d3ee; font-size: 2rem; margin-bottom: 0.5rem; }
        .page-header p { color: #64748b; }
        .form-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 16px;
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 1rem;
        }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #06b6d4;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover { transform: translateY(-2px); }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .bid-range {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .bid-btn {
            padding: 0.5rem 1rem;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 6px;
            color: #22d3ee;
            cursor: pointer;
            transition: all 0.3s;
        }
        .bid-btn:hover { background: rgba(6, 182, 212, 0.2); }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="dashboard.php">← Dashboard</a>
            <a href="my-bookings.php">📦 My Bookings</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>➕ Create New Trip</h1>
            <p>Book a ride to your destination</p>
        </div>

        <div class="form-card">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>📍 Pickup Address</label>
                    <textarea name="pickup_address" placeholder="Enter your pickup location..." required></textarea>
                </div>
                <div class="form-group">
                    <label>🏁 Dropoff Address</label>
                    <textarea name="dropoff_address" placeholder="Enter your destination..." required></textarea>
                </div>
                <div class="form-group">
                    <label>💰 Your Bid (Rs.)</label>
                    <input type="number" name="customer_bid" id="customer_bid" placeholder="Enter your bid amount" min="50" max="5000" required>
                    <div class="bid-range">
                        <button type="button" class="bid-btn" onclick="document.getElementById('customer_bid').value=100">Rs. 100</button>
                        <button type="button" class="bid-btn" onclick="document.getElementById('customer_bid').value=200">Rs. 200</button>
                        <button type="button" class="bid-btn" onclick="document.getElementById('customer_bid').value=350">Rs. 350</button>
                        <button type="button" class="bid-btn" onclick="document.getElementById('customer_bid').value=500">Rs. 500</button>
                    </div>
                </div>
                <button type="submit" class="btn-submit">🚗 Create Trip</button>
            </form>
        </div>
    </div>
</body>
</html>
