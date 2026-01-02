<?php
// Lab 30: Stocky - Activity Log
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);

// Get all activity for current user
$stmt = $pdo->prepare("SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$user['id']]);
$activities = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - Stocky</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; min-height: 100vh; }
        .header {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.4rem; font-weight: bold; color: white; text-decoration: none; }
        .nav-links a { color: rgba(255,255,255,0.9); text-decoration: none; margin-left: 1.5rem; font-size: 0.9rem; }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.75rem; color: #333; }
        .btn { padding: 0.625rem 1.25rem; border-radius: 8px; text-decoration: none; font-weight: 500; }
        .btn-secondary { background: #e5e7eb; color: #333; }
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 40px;
            height: 40px;
            background: #f3e8ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .activity-content { flex: 1; }
        .activity-action { font-weight: 600; color: #333; }
        .activity-details { color: #666; font-size: 0.9rem; margin-top: 0.25rem; }
        .activity-time { color: #999; font-size: 0.8rem; margin-top: 0.25rem; }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }
        .action-settings_update { background: #fef3c7; }
        .action-settings_import { background: #dbeafe; }
        .action-login { background: #d1fae5; }
    </style>
</head>
<body>
    <header class="header">
        <a href="dashboard.php" class="logo">📦 Stocky</a>
        <nav class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="low-stock.php">Low Stock</a>
            <a href="settings.php">Settings</a>
            <a href="index.php">Lab Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>📋 Activity Log</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <div class="card">
            <?php if (empty($activities)): ?>
            <div class="empty-state">
                <h3>No activity yet</h3>
                <p>Your activity will appear here after you make changes to settings.</p>
            </div>
            <?php else: ?>
                <?php foreach ($activities as $activity): 
                    $icon = match($activity['action']) {
                        'settings_update' => '⚙️',
                        'settings_import' => '📥',
                        'login' => '🔐',
                        default => '📝'
                    };
                ?>
                <div class="activity-item">
                    <div class="activity-icon action-<?= $activity['action'] ?>">
                        <?= $icon ?>
                    </div>
                    <div class="activity-content">
                        <div class="activity-action">
                            <?= match($activity['action']) {
                                'settings_update' => 'Settings Updated',
                                'settings_import' => 'Settings Imported',
                                'login' => 'Login',
                                default => ucfirst($activity['action'])
                            } ?>
                        </div>
                        <div class="activity-details"><?= htmlspecialchars($activity['details']) ?></div>
                        <div class="activity-time"><?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
