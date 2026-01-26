<?php
// Lab 22: Booking Detail Page - VULNERABLE TO IDOR
require_once 'config.php';
requireLogin();

$booking_id = $_GET['booking_id'] ?? '';
$user = getCurrentUser();
$booking = null;
$error = '';

if (empty($booking_id)) {
    $error = 'No booking ID provided.';
} else {
    try {
        $pdo = getDBConnection();
        
        // ⚠️ VULNERABLE: No ownership check! Any user can view any booking
        $stmt = $pdo->prepare("SELECT b.*, u.full_name as passenger_name, u.phone as passenger_phone, u.email as passenger_email
            FROM bookings b
            JOIN users u ON b.passenger_id = u.user_id
            WHERE b.booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            $error = 'Booking not found.';
        }
    } catch (PDOException $e) {
        $error = 'Database error.';
    }
}

// Check if attacker is viewing victim's booking (for success detection)
$isIDOR = false;
if ($booking && $booking['passenger_id'] !== $_SESSION['user_id']) {
    $isIDOR = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail - RideKea | Lab 22</title>
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
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .idor-alert {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(245, 158, 11, 0.2));
            border: 2px solid #ef4444;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .idor-alert h3 { color: #f87171; margin-bottom: 0.5rem; }
        .idor-alert p { color: #fca5a5; }
        .idor-alert .btn-success {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
        }
        .error-box h3 { color: #f87171; margin-bottom: 0.5rem; }
        .booking-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 16px;
            overflow: hidden;
        }
        .booking-header {
            background: rgba(6, 182, 212, 0.15);
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(6, 182, 212, 0.3);
        }
        .booking-header h2 { color: #22d3ee; }
        .booking-id {
            font-family: monospace;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-completed { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .booking-body { padding: 1.5rem; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .info-section {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            padding: 1.25rem;
        }
        .info-section h4 {
            color: #22d3ee;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed rgba(6, 182, 212, 0.2);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748b; }
        .info-value { color: #e2e8f0; font-weight: 500; }
        .info-value.sensitive {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
        .location-card {
            grid-column: span 2;
        }
        .location {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
        }
        .location:last-child { border-bottom: none; }
        .location-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .pickup-icon { background: rgba(16, 185, 129, 0.2); }
        .dropoff-icon { background: rgba(239, 68, 68, 0.2); }
        .location-details h5 { color: #94a3b8; font-size: 0.8rem; margin-bottom: 0.25rem; }
        .location-details p { color: #e2e8f0; }
        .location-details .coords {
            color: #f59e0b;
            font-family: monospace;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .api-response {
            margin-top: 2rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 12px;
            overflow: hidden;
        }
        .api-response-header {
            background: rgba(6, 182, 212, 0.1);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .api-response-header h4 { color: #22d3ee; }
        .api-response-body {
            padding: 1rem;
            background: #0d1117;
            max-height: 400px;
            overflow-y: auto;
        }
        .api-response-body pre {
            color: #e2e8f0;
            font-family: monospace;
            font-size: 0.85rem;
            white-space: pre-wrap;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }
        .btn-secondary {
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #22d3ee;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="dashboard.php">← Dashboard</a>
            <a href="my-bookings.php">📦 My Bookings</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </header>

    <div class="container">
        <?php if ($error): ?>
            <div class="error-box">
                <h3>❌ Error</h3>
                <p><?= e($error) ?></p>
            </div>
        <?php elseif ($booking): ?>
            <?php if ($isIDOR): ?>
                <div class="idor-alert">
                    <h3>🎯 IDOR Vulnerability Exploited!</h3>
                    <p>You are viewing <strong><?= e($booking['passenger_name']) ?></strong>'s booking as <strong><?= e($user['full_name']) ?></strong>!</p>
                    <p>This booking belongs to passenger ID: <code><?= e($booking['passenger_id']) ?></code></p>
                    <p>Your user ID: <code><?= e($_SESSION['user_id']) ?></code></p>
                    <a href="success.php?booking_id=<?= urlencode($booking_id) ?>" class="btn-success">🏆 Complete Lab</a>
                </div>
            <?php endif; ?>

            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <h2>📦 Booking Details</h2>
                        <span class="booking-id"><?= e($booking['booking_id']) ?></span>
                    </div>
                    <span class="status-badge status-<?= $booking['status'] ?>">
                        <?= ucfirst($booking['status']) ?>
                    </span>
                </div>
                <div class="booking-body">
                    <div class="info-grid">
                        <div class="info-section">
                            <h4>👤 Passenger Info</h4>
                            <div class="info-row">
                                <span class="info-label">Name</span>
                                <span class="info-value sensitive"><?= e($booking['passenger_name']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Phone</span>
                                <span class="info-value sensitive"><?= e($booking['passenger_phone']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value sensitive"><?= e($booking['passenger_email']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Passenger ID</span>
                                <span class="info-value" style="font-family: monospace; font-size: 0.75rem;"><?= e($booking['passenger_id']) ?></span>
                            </div>
                        </div>
                        <div class="info-section">
                            <h4>💰 Fare Details</h4>
                            <div class="info-row">
                                <span class="info-label">Estimated Fare</span>
                                <span class="info-value"><?= formatCurrency($booking['est_fare']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Customer Bid</span>
                                <span class="info-value"><?= formatCurrency($booking['customer_bid']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Fare Range</span>
                                <span class="info-value"><?= formatCurrency($booking['fare_lower']) ?> - <?= formatCurrency($booking['fare_upper']) ?></span>
                            </div>
                            <?php if ($booking['actual_fare']): ?>
                            <div class="info-row">
                                <span class="info-label">Actual Fare</span>
                                <span class="info-value"><?= formatCurrency($booking['actual_fare']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="info-section location-card">
                            <h4>📍 Route Details</h4>
                            <div class="location">
                                <div class="location-icon pickup-icon">📍</div>
                                <div class="location-details">
                                    <h5>Pickup Location</h5>
                                    <p class="sensitive"><?= e($booking['pickup_address']) ?></p>
                                    <p class="coords">Lat: <?= $booking['pickup_lat'] ?>, Lng: <?= $booking['pickup_lng'] ?></p>
                                </div>
                            </div>
                            <div class="location">
                                <div class="location-icon dropoff-icon">🏁</div>
                                <div class="location-details">
                                    <h5>Dropoff Location</h5>
                                    <p class="sensitive"><?= e($booking['dropoff_address']) ?></p>
                                    <p class="coords">Lat: <?= $booking['dropoff_lat'] ?>, Lng: <?= $booking['dropoff_lng'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="info-section">
                            <h4>📊 Trip Info</h4>
                            <div class="info-row">
                                <span class="info-label">Trip No</span>
                                <span class="info-value"><?= e($booking['trip_no']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Distance</span>
                                <span class="info-value"><?= formatDistance($booking['est_distance']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Est. Time</span>
                                <span class="info-value"><?= formatTime($booking['est_time']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Trip Type</span>
                                <span class="info-value"><?= e($booking['trip_type']) ?></span>
                            </div>
                        </div>
                        <div class="info-section">
                            <h4>🔗 Tracking & Session</h4>
                            <div class="info-row">
                                <span class="info-label">Tracking Link</span>
                                <span class="info-value sensitive" style="font-size: 0.75rem;"><?= e($booking['tracking_link']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Session ID</span>
                                <span class="info-value" style="font-family: monospace; font-size: 0.75rem;"><?= e($booking['session_id']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Order ID</span>
                                <span class="info-value" style="font-family: monospace; font-size: 0.75rem;"><?= e($booking['order_id']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="view-bids.php?booking_id=<?= urlencode($booking_id) ?>" class="btn btn-primary">💰 View Bids</a>
                        <a href="api/bookings.php?booking_id=<?= urlencode($booking_id) ?>" class="btn btn-secondary" target="_blank">📄 API Response</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
