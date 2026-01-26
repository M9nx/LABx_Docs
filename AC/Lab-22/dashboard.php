<?php
// Lab 22: User Dashboard
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
    <title>Dashboard - RideKea | Lab 22</title>
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
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #22d3ee;
        }
        .nav-links { display: flex; gap: 1rem; align-items: center; }
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
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
        }
        .user-info .avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .user-info .name { color: #10b981; font-weight: 600; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            color: #22d3ee;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #64748b; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 1.75rem; font-weight: bold; color: #22d3ee; }
        .stat-card .label { color: #64748b; font-size: 0.9rem; }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .section-header h2 { color: #e2e8f0; }
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
        .btn:hover { transform: translateY(-2px); }
        .bookings-table {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(6, 182, 212, 0.1);
        }
        th {
            background: rgba(6, 182, 212, 0.1);
            color: #22d3ee;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        td { color: #e2e8f0; }
        tr:hover { background: rgba(6, 182, 212, 0.05); }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-completed { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .status-accepted { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .booking-id {
            font-family: monospace;
            font-size: 0.8rem;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
        .action-btn {
            padding: 0.4rem 0.8rem;
            background: rgba(6, 182, 212, 0.2);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #22d3ee;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            margin-right: 0.5rem;
            transition: all 0.3s;
        }
        .action-btn:hover { background: rgba(6, 182, 212, 0.3); }
        .api-hint {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .api-hint h4 { color: #f87171; margin-bottom: 0.75rem; }
        .api-hint p { color: #fca5a5; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .api-hint code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #f59e0b;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="index.php">🏠 Home</a>
            <a href="my-bookings.php">📦 My Bookings</a>
            <a href="create-trip.php">➕ New Trip</a>
            <a href="lab-description.php">📖 Lab Guide</a>
            <div class="user-info">
                <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                <span class="name"><?= e($user['full_name']) ?></span>
            </div>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>👋 Welcome, <?= e($user['full_name']) ?></h1>
            <p>User ID: <code style="color: #f59e0b;"><?= e($user['user_id']) ?></code> | Token: <code style="color: #f59e0b;"><?= e(substr($user['access_token'], 0, 20)) ?>...</code></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📦</div>
                <div class="value"><?= count($bookings) ?></div>
                <div class="label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="value"><?= count(array_filter($bookings, fn($b) => $b['status'] === 'completed')) ?></div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="icon">⏳</div>
                <div class="value"><?= count(array_filter($bookings, fn($b) => $b['status'] === 'pending')) ?></div>
                <div class="label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="icon">⭐</div>
                <div class="value"><?= number_format($user['profile_rating'], 1) ?></div>
                <div class="label">Rating</div>
            </div>
        </div>

        <div class="section-header">
            <h2>📦 Recent Bookings</h2>
            <a href="create-trip.php" class="btn btn-primary">➕ Create New Trip</a>
        </div>

        <div class="bookings-table">
            <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <p>No bookings yet. Create your first trip!</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Trip No</th>
                            <th>Route</th>
                            <th>Fare</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><span class="booking-id"><?= e($booking['booking_id']) ?></span></td>
                                <td><?= e($booking['trip_no']) ?></td>
                                <td>
                                    <small style="color: #64748b;">
                                        📍 <?= e(substr($booking['pickup_address'], 0, 30)) ?>...<br>
                                        📍 <?= e(substr($booking['dropoff_address'], 0, 30)) ?>...
                                    </small>
                                </td>
                                <td><?= formatCurrency($booking['est_fare']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $booking['status'] ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="booking-detail.php?booking_id=<?= urlencode($booking['booking_id']) ?>" class="action-btn">👁️ View</a>
                                    <a href="view-bids.php?booking_id=<?= urlencode($booking['booking_id']) ?>" class="action-btn">💰 Bids</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="api-hint">
            <h4>🎯 IDOR Hint - API Endpoints</h4>
            <p>Notice each booking has a unique <code>booking_id</code>. Try accessing these API endpoints:</p>
            <p>• <code>api/bookings.php?booking_id=BKG_XXXXXXXX</code></p>
            <p>• <code>api/bids.php?booking_id=BKG_XXXXXXXX</code></p>
            <p>What happens if you change the booking_id to another user's booking? 🤔</p>
        </div>
    </div>
</body>
</html>
