<?php
// Lab 22: Success Page
require_once 'config.php';
require_once '../progress.php';
requireLogin();

$booking_id = $_GET['booking_id'] ?? '';
$user = getCurrentUser();
$booking = null;
$victim = null;

if ($booking_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT b.*, u.full_name as victim_name FROM bookings b
            JOIN users u ON b.passenger_id = u.user_id WHERE b.booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        // Mark lab as solved if viewing another user's booking
        if ($booking && $booking['passenger_id'] != $user['user_id']) {
            markLabSolved(22);
        }
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏆 Lab Completed! - Lab 22</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-card {
            background: rgba(30, 41, 59, 0.9);
            border: 2px solid #10b981;
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            max-width: 700px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #22d3ee, #f59e0b, #ef4444);
        }
        .trophy { font-size: 5rem; margin-bottom: 1rem; animation: bounce 1s infinite; }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .success-card h1 {
            color: #10b981;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .success-card .subtitle {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .badge {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            margin-bottom: 2rem;
        }
        .details-box {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .details-box h3 {
            color: #22d3ee;
            margin-bottom: 1rem;
            text-align: center;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed rgba(6, 182, 212, 0.2);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; }
        .detail-value { color: #e2e8f0; font-family: monospace; }
        .exposed-data {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .exposed-data h4 {
            color: #f87171;
            margin-bottom: 1rem;
        }
        .exposed-data ul {
            list-style: none;
            text-align: left;
        }
        .exposed-data li {
            padding: 0.4rem 0;
            color: #fca5a5;
        }
        .exposed-data li::before {
            content: '⚠️ ';
        }
        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
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
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        /* Confetti Animation */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            animation: confetti-fall 3s ease-out forwards;
            pointer-events: none;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🚗 RideKea</div>
        <nav class="nav-links">
            <a href="index.php">← Home</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="docs.php">📚 Docs</a>
        </nav>
    </header>

    <div class="container">
        <div class="success-card">
            <div class="trophy">🏆</div>
            <h1>Lab 22 Completed!</h1>
            <p class="subtitle">IDOR on Booking Detail & Bids - Information Disclosure</p>
            
            <span class="badge">🎯 Vulnerability Exploited Successfully</span>
            
            <div class="details-box">
                <h3>📊 Attack Summary</h3>
                <div class="detail-row">
                    <span class="detail-label">Attacker</span>
                    <span class="detail-value"><?= e($user['full_name']) ?> (<?= e($user['username']) ?>)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Attacker ID</span>
                    <span class="detail-value"><?= e($_SESSION['user_id']) ?></span>
                </div>
                <?php if ($booking): ?>
                <div class="detail-row">
                    <span class="detail-label">Victim</span>
                    <span class="detail-value"><?= e($booking['victim_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Booking Accessed</span>
                    <span class="detail-value"><?= e($booking_id) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Victim's User ID</span>
                    <span class="detail-value"><?= e($booking['passenger_id']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="exposed-data">
                <h4>⚠️ Sensitive Data Exposed</h4>
                <ul>
                    <li>Victim's full name and contact details</li>
                    <li>Home address / pickup location</li>
                    <li>GPS coordinates (exact location)</li>
                    <li>Travel destinations and patterns</li>
                    <li>Driver phone numbers and vehicle info</li>
                    <li>Booking preferences and bid settings</li>
                </ul>
            </div>
            
            <div class="details-box">
                <h3>🛡️ Real-World Impact</h3>
                <p style="color: #94a3b8; text-align: center;">
                    In the original Bykea report, this vulnerability was rated as <strong style="color: #f59e0b;">Medium Severity</strong> 
                    and awarded a <strong style="color: #10b981;">$500 bounty</strong>. The exposed data could enable:
                </p>
                <ul style="color: #94a3b8; margin-top: 1rem; margin-left: 1.5rem;">
                    <li>Stalking victims by knowing their travel patterns</li>
                    <li>Impersonating drivers to victims</li>
                    <li>Social engineering attacks using personal details</li>
                    <li>Physical security risks from exposed home addresses</li>
                </ul>
            </div>
            
            <div class="buttons">
                <a href="lab-description.php" class="btn btn-secondary">📖 Review Guide</a>
                <a href="docs.php" class="btn btn-primary">📚 Full Documentation</a>
                <a href="../" class="btn btn-success">🏠 All Labs</a>
            </div>
        </div>
    </div>

    <script>
        // Confetti celebration
        const colors = ['#10b981', '#22d3ee', '#f59e0b', '#ef4444', '#a855f7'];
        for (let i = 0; i < 100; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 2 + 's';
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 4000);
            }, i * 30);
        }
    </script>
</body>
</html>
