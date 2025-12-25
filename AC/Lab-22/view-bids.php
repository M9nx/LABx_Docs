<?php
// Lab 22: View Bids Page - VULNERABLE TO IDOR
require_once 'config.php';
requireLogin();

$booking_id = $_GET['booking_id'] ?? '';
$user = getCurrentUser();
$booking = null;
$bids = [];
$error = '';

if (empty($booking_id)) {
    $error = 'No booking ID provided.';
} else {
    try {
        $pdo = getDBConnection();
        
        // ⚠️ VULNERABLE: No ownership check!
        $stmt = $pdo->prepare("SELECT b.*, u.full_name as passenger_name FROM bookings b
            JOIN users u ON b.passenger_id = u.user_id WHERE b.booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if ($booking) {
            // Get bids for this booking - ALSO VULNERABLE
            $stmt = $pdo->prepare("SELECT * FROM bids WHERE booking_id = ? ORDER BY bid_amount ASC");
            $stmt->execute([$booking_id]);
            $bids = $stmt->fetchAll();
        } else {
            $error = 'Booking not found.';
        }
    } catch (PDOException $e) {
        $error = 'Database error.';
    }
}

$isIDOR = $booking && $booking['passenger_id'] !== $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bids - RideKea | Lab 22</title>
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
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 { color: #22d3ee; margin-bottom: 0.5rem; }
        .page-header p { color: #64748b; }
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
        .booking-summary {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .booking-summary .trip-info h3 { color: #22d3ee; margin-bottom: 0.25rem; }
        .booking-summary .trip-info p { color: #64748b; }
        .booking-id {
            font-family: monospace;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }
        .bids-section h2 {
            color: #e2e8f0;
            margin-bottom: 1.5rem;
        }
        .bid-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        .bid-card:hover {
            border-color: #06b6d4;
            transform: translateY(-2px);
        }
        .bid-header {
            background: rgba(6, 182, 212, 0.1);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .driver-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .driver-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .driver-details h4 { color: #e2e8f0; }
        .driver-details p { color: #64748b; font-size: 0.85rem; }
        .driver-details .phone {
            color: #f87171;
            font-family: monospace;
        }
        .bid-amount {
            text-align: right;
        }
        .bid-amount .amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #10b981;
        }
        .bid-amount .label {
            color: #64748b;
            font-size: 0.8rem;
        }
        .bid-body {
            padding: 1rem 1.5rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        .bid-stat {
            text-align: center;
        }
        .bid-stat .value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #22d3ee;
        }
        .bid-stat .label {
            color: #64748b;
            font-size: 0.8rem;
        }
        .vehicle-info {
            background: rgba(245, 158, 11, 0.1);
            padding: 0.75rem 1.5rem;
            display: flex;
            gap: 1.5rem;
            border-top: 1px solid rgba(6, 182, 212, 0.2);
        }
        .vehicle-info span {
            color: #f59e0b;
            font-size: 0.85rem;
        }
        .empty-bids {
            text-align: center;
            padding: 3rem;
            background: rgba(30, 41, 59, 0.6);
            border-radius: 12px;
        }
        .empty-bids .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-bids h3 { color: #94a3b8; margin-bottom: 0.5rem; }
        .empty-bids p { color: #64748b; }
        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
        }
        .error-box h3 { color: #f87171; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="dashboard.php">← Dashboard</a>
            <a href="booking-detail.php?booking_id=<?= urlencode($booking_id) ?>">📦 Booking</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>💰 Driver Bids</h1>
            <p>View bids from available drivers for this booking</p>
        </div>

        <?php if ($error): ?>
            <div class="error-box">
                <h3>❌ <?= e($error) ?></h3>
            </div>
        <?php elseif ($booking): ?>
            <?php if ($isIDOR): ?>
                <div class="idor-alert">
                    <h3>🎯 IDOR Vulnerability Exploited!</h3>
                    <p>You are viewing bids for <strong><?= e($booking['passenger_name']) ?></strong>'s booking!</p>
                    <p>Sensitive driver information exposed: phone numbers, vehicle details, ratings!</p>
                </div>
            <?php endif; ?>

            <div class="booking-summary">
                <div class="trip-info">
                    <h3><?= e($booking['trip_no']) ?></h3>
                    <p><?= e(substr($booking['pickup_address'], 0, 50)) ?>... → <?= e(substr($booking['dropoff_address'], 0, 50)) ?>...</p>
                </div>
                <span class="booking-id"><?= e($booking['booking_id']) ?></span>
            </div>

            <div class="bids-section">
                <h2>📋 Available Bids (<?= count($bids) ?>)</h2>
                
                <?php if (empty($bids)): ?>
                    <div class="empty-bids">
                        <div class="icon">🕐</div>
                        <h3>No bids yet</h3>
                        <p>Drivers are being notified. Bids will appear here shortly.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($bids as $bid): ?>
                        <div class="bid-card">
                            <div class="bid-header">
                                <div class="driver-info">
                                    <div class="driver-avatar">🚗</div>
                                    <div class="driver-details">
                                        <h4><?= e($bid['driver_name']) ?></h4>
                                        <p class="phone">📞 <?= e($bid['driver_phone']) ?></p>
                                        <p>Driver ID: <code style="font-size: 0.7rem;"><?= e($bid['driver_id']) ?></code></p>
                                    </div>
                                </div>
                                <div class="bid-amount">
                                    <div class="amount"><?= formatCurrency($bid['bid_amount']) ?></div>
                                    <div class="label">Bid Amount</div>
                                </div>
                            </div>
                            <div class="bid-body">
                                <div class="bid-stat">
                                    <div class="value"><?= $bid['driver_eta'] ?> min</div>
                                    <div class="label">ETA</div>
                                </div>
                                <div class="bid-stat">
                                    <div class="value"><?= $bid['driver_distance'] ?> km</div>
                                    <div class="label">Distance</div>
                                </div>
                                <div class="bid-stat">
                                    <div class="value">⭐ <?= number_format($bid['driver_rating'], 2) ?></div>
                                    <div class="label">Rating</div>
                                </div>
                                <div class="bid-stat">
                                    <div class="value"><?= ucfirst($bid['status']) ?></div>
                                    <div class="label">Status</div>
                                </div>
                            </div>
                            <div class="vehicle-info">
                                <span>🏍️ <?= e($bid['vehicle_type']) ?></span>
                                <span>🔢 <?= e($bid['vehicle_number']) ?></span>
                                <span>🆔 <?= e($bid['bid_id']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div style="margin-top: 2rem; text-align: center;">
                <a href="api/bids.php?booking_id=<?= urlencode($booking_id) ?>" class="btn btn-primary" target="_blank">📄 View API Response</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
