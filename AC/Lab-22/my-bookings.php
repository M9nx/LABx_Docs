<?php
// Lab 22: My Bookings Page
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
$bookings = getUserBookings($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - RideKea | Lab 22</title>
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
            transition: all 0.3s;
        }
        .nav-links a:hover { background: rgba(6, 182, 212, 0.2); }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 { color: #22d3ee; font-size: 1.75rem; }
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
        .booking-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .booking-header {
            background: rgba(6, 182, 212, 0.1);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(6, 182, 212, 0.2);
        }
        .booking-id {
            font-family: monospace;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .trip-no { color: #22d3ee; font-weight: 600; }
        .status-badge {
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-completed { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .booking-body {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .route-info h4 {
            color: #94a3b8;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .location {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .location-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .pickup-icon { background: rgba(16, 185, 129, 0.2); }
        .dropoff-icon { background: rgba(239, 68, 68, 0.2); }
        .location-text {
            color: #e2e8f0;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .fare-info {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            padding: 1rem;
        }
        .fare-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed rgba(6, 182, 212, 0.2);
        }
        .fare-row:last-child { border-bottom: none; }
        .fare-label { color: #64748b; }
        .fare-value { color: #22d3ee; font-weight: 600; }
        .booking-footer {
            background: rgba(15, 23, 42, 0.5);
            padding: 1rem 1.5rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .action-btn-view {
            background: rgba(6, 182, 212, 0.2);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #22d3ee;
        }
        .action-btn-bids {
            background: rgba(139, 92, 246, 0.2);
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: #a78bfa;
        }
        .action-btn:hover { transform: translateY(-2px); }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(30, 41, 59, 0.6);
            border-radius: 16px;
        }
        .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; }
        .empty-state h3 { color: #94a3b8; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="index.php">🏠 Home</a>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="create-trip.php">➕ New Trip</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>📦 My Bookings</h1>
            <a href="create-trip.php" class="btn btn-primary">➕ Create New Trip</a>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h3>No bookings found</h3>
                <p style="color: #64748b;">Create your first trip to get started!</p>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <span class="trip-no"><?= e($booking['trip_no']) ?></span>
                            <span class="booking-id"><?= e($booking['booking_id']) ?></span>
                        </div>
                        <span class="status-badge status-<?= $booking['status'] ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </div>
                    <div class="booking-body">
                        <div class="route-info">
                            <h4>Route</h4>
                            <div class="location">
                                <div class="location-icon pickup-icon">📍</div>
                                <div class="location-text"><?= e($booking['pickup_address']) ?></div>
                            </div>
                            <div class="location">
                                <div class="location-icon dropoff-icon">🏁</div>
                                <div class="location-text"><?= e($booking['dropoff_address']) ?></div>
                            </div>
                        </div>
                        <div class="fare-info">
                            <div class="fare-row">
                                <span class="fare-label">Estimated Fare</span>
                                <span class="fare-value"><?= formatCurrency($booking['est_fare']) ?></span>
                            </div>
                            <div class="fare-row">
                                <span class="fare-label">Your Bid</span>
                                <span class="fare-value"><?= formatCurrency($booking['customer_bid']) ?></span>
                            </div>
                            <div class="fare-row">
                                <span class="fare-label">Distance</span>
                                <span class="fare-value"><?= formatDistance($booking['est_distance']) ?></span>
                            </div>
                            <div class="fare-row">
                                <span class="fare-label">Est. Time</span>
                                <span class="fare-value"><?= formatTime($booking['est_time']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="booking-footer">
                        <a href="booking-detail.php?booking_id=<?= urlencode($booking['booking_id']) ?>" class="action-btn action-btn-view">👁️ View Details</a>
                        <a href="view-bids.php?booking_id=<?= urlencode($booking['booking_id']) ?>" class="action-btn action-btn-bids">💰 View Bids</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
