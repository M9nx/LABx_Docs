<?php
// Lab 21: Dashboard - Stocky Application
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$store = getUserStore($pdo, $user['id']);

if (!$store) {
    die("No store found for this user. Please contact support.");
}

$settings = getStoreSettings($pdo, $store['id']);
$products = getAllProducts($pdo, $store['id']);
$lowStockCount = count(getLowStockProducts($pdo, $store['id']));
$totalStock = array_sum(array_column($products, 'stock_quantity'));
$totalValue = array_sum(array_map(function($p) { return $p['stock_quantity'] * $p['price']; }, $products));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($store['store_name']); ?> | Stocky</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: rgba(15, 23, 42, 0.98);
            border-right: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1.5rem;
            overflow-y: auto;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.4rem;
            font-weight: 700;
            color: #6366f1;
            margin-bottom: 2rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-menu {
            list-style: none;
        }
        .nav-item {
            margin-bottom: 0.5rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.15));
            border: 1px solid rgba(99, 102, 241, 0.3);
        }
        .nav-link span {
            font-size: 1.1rem;
        }
        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 1rem 1rem 0.5rem;
        }
        .main-content {
            margin-left: 260px;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 1.75rem;
            color: #e2e8f0;
        }
        .header-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .store-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #a5b4fc;
            font-size: 0.9rem;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .user-info span {
            display: block;
            font-size: 0.9rem;
        }
        .user-info small {
            color: #64748b;
            font-size: 0.75rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.4);
        }
        .stat-card.warning {
            border-color: rgba(245, 158, 11, 0.4);
            background: rgba(245, 158, 11, 0.05);
        }
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .stat-icon.purple { background: rgba(99, 102, 241, 0.2); }
        .stat-icon.orange { background: rgba(245, 158, 11, 0.2); }
        .stat-icon.green { background: rgba(16, 185, 129, 0.2); }
        .stat-icon.blue { background: rgba(59, 130, 246, 0.2); }
        .stat-label {
            color: #94a3b8;
            font-size: 0.875rem;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #e2e8f0;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .action-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }
        .action-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.5);
            background: rgba(99, 102, 241, 0.1);
        }
        .action-card h3 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #a5b4fc;
            margin-bottom: 0.5rem;
        }
        .action-card p {
            color: #64748b;
            font-size: 0.85rem;
        }
        .section-title {
            font-size: 1.25rem;
            color: #e2e8f0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .settings-preview {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .settings-id {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            color: #f87171;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .columns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
        }
        .column-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 6px;
            font-size: 0.85rem;
        }
        .column-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .column-status.on { background: #10b981; }
        .column-status.off { background: #64748b; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="index.php" class="logo">
            <div class="logo-icon">📦</div>
            <span>Stocky</span>
        </a>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <span>📊</span> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="low-stock.php" class="nav-link">
                    <span>⚠️</span> Low Stock Variants
                </a>
            </li>
            <li class="nav-item">
                <a href="products.php" class="nav-link">
                    <span>📦</span> Products
                </a>
            </li>
            
            <li class="nav-section-title">Settings</li>
            <li class="nav-item">
                <a href="settings.php" class="nav-link">
                    <span>⚙️</span> Column Settings
                </a>
            </li>
            
            <li class="nav-section-title">Lab</li>
            <li class="nav-item">
                <a href="lab-description.php" class="nav-link">
                    <span>📖</span> Instructions
                </a>
            </li>
            <li class="nav-item">
                <a href="docs.php" class="nav-link">
                    <span>📚</span> Documentation
                </a>
            </li>
            <li class="nav-item">
                <a href="../index.php" class="nav-link">
                    <span>🏠</span> All Labs
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">
                    <span>🚪</span> Logout
                </a>
            </li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <div class="header-info">
                <div class="store-badge">
                    <span>🏪</span>
                    <?php echo htmlspecialchars($store['domain']); ?>
                </div>
                <div class="user-menu">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                        <small><?php echo htmlspecialchars($store['plan_type']); ?> plan</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon purple">📦</div>
                    <span class="stat-label">Total Products</span>
                </div>
                <div class="stat-value"><?php echo count($products); ?></div>
            </div>
            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-icon orange">⚠️</div>
                    <span class="stat-label">Low Stock Items</span>
                </div>
                <div class="stat-value"><?php echo $lowStockCount; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon green">📈</div>
                    <span class="stat-label">Total Stock</span>
                </div>
                <div class="stat-value"><?php echo number_format($totalStock); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blue">💰</div>
                    <span class="stat-label">Stock Value</span>
                </div>
                <div class="stat-value">$<?php echo number_format($totalValue, 0); ?></div>
            </div>
        </div>
        
        <div class="quick-actions">
            <a href="low-stock.php" class="action-card">
                <h3><span>⚠️</span> Low Stock Variants</h3>
                <p>View products that need reordering</p>
            </a>
            <a href="settings.php" class="action-card">
                <h3><span>⚙️</span> Column Settings</h3>
                <p>Customize visible columns</p>
            </a>
            <a href="products.php" class="action-card">
                <h3><span>📦</span> All Products</h3>
                <p>Manage your product inventory</p>
            </a>
        </div>
        
        <h2 class="section-title">⚙️ Current Column Settings</h2>
        <div class="settings-preview">
            <div class="settings-id">
                ⚠️ Settings ID: <strong><?php echo $settings['id']; ?></strong>
            </div>
            
            <div class="columns-grid">
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_grade'] ? 'on' : 'off'; ?>"></span>
                    Grade
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_product_title'] ? 'on' : 'off'; ?>"></span>
                    Product Title
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_variant_title'] ? 'on' : 'off'; ?>"></span>
                    Variant Title
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_sku'] ? 'on' : 'off'; ?>"></span>
                    SKU
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_lost_per_day'] ? 'on' : 'off'; ?>"></span>
                    Lost Per Day
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_reorder_point'] ? 'on' : 'off'; ?>"></span>
                    Reorder Point
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_lead_time'] ? 'on' : 'off'; ?>"></span>
                    Lead Time
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_need'] ? 'on' : 'off'; ?>"></span>
                    Need
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_depletion_days'] ? 'on' : 'off'; ?>"></span>
                    Depletion Days
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_depletion_date'] ? 'on' : 'off'; ?>"></span>
                    Depletion Date
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_next_due_date'] ? 'on' : 'off'; ?>"></span>
                    Next Due Date
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_stock'] ? 'on' : 'off'; ?>"></span>
                    Stock
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_on_po'] ? 'on' : 'off'; ?>"></span>
                    On PO
                </div>
                <div class="column-item">
                    <span class="column-status <?php echo $settings['show_on_order'] ? 'on' : 'off'; ?>"></span>
                    On Order
                </div>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <a href="settings.php" class="btn btn-primary">
                    <span>⚙️</span> Edit Settings
                </a>
            </div>
        </div>
    </main>
</body>
</html>
