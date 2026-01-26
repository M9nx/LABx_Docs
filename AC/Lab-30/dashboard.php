<?php
// Lab 30: Stocky Inventory App - Dashboard
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$settings = getUserSettings($pdo, $user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stocky</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; min-height: 100vh; }
        .header {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .logo { font-size: 1.4rem; font-weight: bold; color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .nav-links a { color: rgba(255,255,255,0.9); text-decoration: none; margin-left: 1.5rem; font-size: 0.9rem; }
        .nav-links a:hover { color: white; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .welcome-bar {
            background: white;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .welcome-bar h1 { font-size: 1.5rem; color: #333; }
        .welcome-bar .store { color: #7c3aed; font-weight: 600; }
        .welcome-bar .settings-id { background: #f0e7ff; color: #7c3aed; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card .number { font-size: 2rem; font-weight: bold; color: #333; }
        .stat-card .label { color: #666; font-size: 0.9rem; }
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .action-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .action-card:hover { transform: translateY(-2px); border-color: #7c3aed; box-shadow: 0 4px 12px rgba(124,58,237,0.15); }
        .action-card h3 { color: #333; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .action-card p { color: #666; font-size: 0.9rem; }
        .action-card.highlight { background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; }
        .action-card.highlight h3, .action-card.highlight p { color: white; }
        .lab-banner {
            background: linear-gradient(135deg, rgba(124,58,237,0.1), rgba(91,33,182,0.1));
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .lab-banner .icon { font-size: 1.5rem; }
        .lab-banner .text { flex: 1; }
        .lab-banner .text strong { color: #7c3aed; }
        .lab-banner a { color: #7c3aed; font-weight: 600; }
    </style>
</head>
<body>
    <header class="header">
        <a href="dashboard.php" class="logo">📦 Stocky</a>
        <nav class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="low-stock.php">Low Stock</a>
            <a href="settings.php">⚙️ Settings</a>
            <a href="index.php">Lab Home</a>
            <a href="docs.php">Docs</a>
            <a href="success.php">Submit Flag</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <div class="container">
        <div class="lab-banner">
            <span class="icon">🎯</span>
            <div class="text">
                <strong>Lab 30 Objective:</strong> Exploit the IDOR vulnerability in Settings to modify another store's column preferences.
            </div>
            <a href="docs.php">View Docs →</a>
        </div>
        
        <div class="welcome-bar">
            <div>
                <h1>Welcome back! 👋</h1>
                <p>Store: <span class="store"><?= htmlspecialchars($user['store_name']) ?></span></p>
            </div>
            <span class="settings-id">Settings ID: <?= $settings['id'] ?></span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📦</div>
                <div class="number">247</div>
                <div class="label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="icon">⚠️</div>
                <div class="number">18</div>
                <div class="label">Low Stock Items</div>
            </div>
            <div class="stat-card">
                <div class="icon">🔄</div>
                <div class="number">5</div>
                <div class="label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <div class="icon">📈</div>
                <div class="number">$12.4k</div>
                <div class="label">Monthly Revenue</div>
            </div>
        </div>
        
        <div class="quick-actions">
            <a href="low-stock.php" class="action-card">
                <h3>📊 Low Stock Variants</h3>
                <p>View products that need restocking based on your customized column settings.</p>
            </a>
            
            <a href="settings.php" class="action-card highlight">
                <h3>⚙️ Column Settings</h3>
                <p>Customize which columns appear in your Low Stock dashboard. (Vulnerable to IDOR!)</p>
            </a>
            
            <a href="activity.php" class="action-card">
                <h3>📋 Activity Log</h3>
                <p>View all actions performed in your account including settings changes.</p>
            </a>
        </div>
    </div>
</body>
</html>
