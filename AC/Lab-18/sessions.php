<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$store_name = $_SESSION['store_name'] ?? 'My Store';

// Get all sessions for this user
$stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE user_id = ? ORDER BY last_activity DESC");
$stmt->execute([$user_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Sessions - Shopify Admin</title>
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
        .nav-links a.active { color: #96bf48; font-weight: 600; }
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            color: #888;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .breadcrumb a { color: #96bf48; text-decoration: none; }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #96bf48;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
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
        .session-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }
        .session-info {
            flex: 1;
        }
        .session-info h4 {
            color: #e0e0e0;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .session-info p {
            color: #888;
            font-size: 0.85rem;
        }
        .session-status {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .session-status.active {
            background: rgba(0, 200, 100, 0.2);
            color: #00ff88;
        }
        .session-status.expired {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
        }
        .current-badge {
            background: rgba(150, 191, 72, 0.3);
            color: #96bf48;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .no-sessions {
            text-align: center;
            color: #888;
            padding: 2rem;
        }
        .actions {
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
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
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
                <a href="sessions.php" class="active">Sessions</a>
            </nav>
            <div class="user-menu">
                <span><?php echo htmlspecialchars($store_name); ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <a href="logout.php" style="color:#ff6666;">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a> / <a href="account-settings.php">Settings</a> / Sessions
        </div>

        <div class="page-header">
            <h1>🖥️ Active Sessions</h1>
            <p>View and manage your login sessions across devices</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Your Sessions</h2>
                <span style="color:#888;font-size:0.9rem;"><?php echo count($sessions); ?> total</span>
            </div>

            <?php if (empty($sessions)): ?>
                <div class="no-sessions">
                    <p>No session records found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($sessions as $session): ?>
                <div class="session-item">
                    <div class="session-info">
                        <h4>
                            <?php echo $session['is_active'] ? '🟢' : '🔴'; ?>
                            <?php echo htmlspecialchars(substr($session['user_agent'], 0, 50)); ?>...
                            <?php if ($session['session_token'] === ($_SESSION['session_token'] ?? '')): ?>
                                <span class="current-badge">Current</span>
                            <?php endif; ?>
                        </h4>
                        <p>
                            IP: <?php echo htmlspecialchars($session['ip_address']); ?> •
                            Last active: <?php echo date('M j, Y H:i', strtotime($session['last_activity'])); ?>
                        </p>
                    </div>
                    <span class="session-status <?php echo $session['is_active'] ? 'active' : 'expired'; ?>">
                        <?php echo $session['is_active'] ? 'Active' : 'Expired'; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="actions">
                <a href="account-settings.php" class="btn btn-primary">⚙️ Manage Security</a>
                <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
