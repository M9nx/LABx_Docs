<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$store_name = $_SESSION['store_name'] ?? 'My Store';

// Get user's active sessions count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_sessions WHERE user_id = ? AND is_active = 1");
$stmt->execute([$user_id]);
$activeSessions = $stmt->fetchColumn();

// Get recent activity
$stmt = $pdo->prepare("SELECT * FROM session_activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shopify Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(150, 191, 72, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #96bf48;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #96bf48; }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #96bf48;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 1000px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(150, 191, 72, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card-header h2 {
            color: #96bf48;
            font-size: 1.2rem;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .stat-box {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #96bf48;
        }
        .stat-label {
            color: #888;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
            text-decoration: none;
            color: #e0e0e0;
            transition: all 0.3s;
        }
        .menu-item:hover {
            background: rgba(150, 191, 72, 0.1);
            border-left: 3px solid #96bf48;
        }
        .menu-icon {
            font-size: 1.5rem;
        }
        .menu-text h4 { color: #96bf48; margin-bottom: 0.25rem; }
        .menu-text p { color: #888; font-size: 0.85rem; }
        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 35px;
            height: 35px;
            background: rgba(150, 191, 72, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .activity-text { flex: 1; }
        .activity-text h4 { color: #e0e0e0; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .activity-text p { color: #666; font-size: 0.8rem; }
        .highlight-card {
            background: linear-gradient(135deg, rgba(150, 191, 72, 0.2), rgba(92, 106, 196, 0.2));
            border: 1px solid rgba(150, 191, 72, 0.4);
        }
        .highlight-card h3 { color: #96bf48; margin-bottom: 0.5rem; }
        .highlight-card p { color: #aaa; font-size: 0.9rem; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">
                <svg viewBox="0 0 109 124" fill="none">
                    <path d="M74.7 14.8L62.2 55.4H46.7L34.2 14.8C33.1 11 29.5 8.3 25.5 8.3H0L31.5 115.5H40.8L54.5 67.8L68.2 115.5H77.5L109 8.3H83.5C79.5 8.3 75.8 11 74.7 14.8Z" fill="#96bf48"/>
                </svg>
                Shopify Admin
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="account-settings.php">Settings</a>
                <a href="sessions.php">Sessions</a>
            </nav>
            <div class="user-menu">
                <span><?php echo htmlspecialchars($store_name); ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <a href="logout.php" style="color:#ff6666;">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
            <p>Manage your store and account settings</p>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <div class="card highlight-card">
                    <h3>🔐 Security Lab - IDOR Vulnerability</h3>
                    <p>
                        This admin panel contains an IDOR vulnerability in the session management feature.
                        Navigate to <strong>Account Settings → Security</strong> to find and exploit it.
                    </p>
                    <a href="account-settings.php" class="btn">Go to Account Settings →</a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>📊 Quick Stats</h2>
                    </div>
                    <div class="stat-grid">
                        <div class="stat-box">
                            <div class="stat-value"><?php echo $activeSessions; ?></div>
                            <div class="stat-label">Active Sessions</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">ID: <?php echo $user_id; ?></div>
                            <div class="stat-label">Your Account ID</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value">🟢</div>
                            <div class="stat-label">Account Status</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>🚀 Quick Actions</h2>
                    </div>
                    <a href="account-settings.php" class="menu-item">
                        <div class="menu-icon">⚙️</div>
                        <div class="menu-text">
                            <h4>Account Settings</h4>
                            <p>Manage security and session settings</p>
                        </div>
                    </a>
                    <a href="sessions.php" class="menu-item">
                        <div class="menu-icon">🖥️</div>
                        <div class="menu-text">
                            <h4>Active Sessions</h4>
                            <p>View and manage your login sessions</p>
                        </div>
                    </a>
                    <a href="docs.php" class="menu-item">
                        <div class="menu-icon">📚</div>
                        <div class="menu-text">
                            <h4>Documentation</h4>
                            <p>Learn about this vulnerability</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="sidebar-content">
                <div class="card">
                    <div class="card-header">
                        <h2>📋 Recent Activity</h2>
                    </div>
                    <?php if (empty($recentActivity)): ?>
                        <p style="color:#888;text-align:center;padding:1rem;">No recent activity</p>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">📝</div>
                            <div class="activity-text">
                                <h4><?php echo htmlspecialchars($activity['action_type']); ?></h4>
                                <p><?php echo date('M j, H:i', strtotime($activity['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>💡 Hint</h2>
                    </div>
                    <p style="color:#aaa;font-size:0.9rem;line-height:1.6;">
                        Check the hidden <code style="background:#333;padding:0.2rem 0.4rem;border-radius:4px;color:#88ff88;">account_id</code> 
                        parameter in the session expiration form. What happens if you change it?
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
