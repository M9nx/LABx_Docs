<?php
// Lab 21: Low Stock Variants Page - Stocky Application
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$store = getUserStore($pdo, $user['id']);
$settings = getStoreSettings($pdo, $store['id']);
$lowStockProducts = getLowStockProducts($pdo, $store['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Variants - <?php echo htmlspecialchars($store['store_name']); ?> | Stocky</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }
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
        .header h1 { font-size: 1.75rem; color: #e2e8f0; }
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-settings {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
        }
        .btn-settings:hover {
            background: rgba(99, 102, 241, 0.2);
        }
        .settings-banner {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .settings-banner-text {
            color: #fbbf24;
            font-size: 0.9rem;
        }
        .settings-banner-text strong {
            font-family: 'Consolas', monospace;
        }
        .data-table {
            width: 100%;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            overflow: hidden;
        }
        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: rgba(99, 102, 241, 0.1);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #a5b4fc;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            white-space: nowrap;
        }
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            color: #e2e8f0;
            font-size: 0.9rem;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background: rgba(99, 102, 241, 0.05); }
        .grade-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .grade-badge.critical { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .grade-badge.warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .grade-badge.moderate { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .grade-badge.good { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .sku-code {
            font-family: 'Consolas', monospace;
            background: rgba(99, 102, 241, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .stock-value {
            font-weight: 600;
        }
        .stock-value.low { color: #f87171; }
        .stock-value.ok { color: #34d399; }
        .need-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }
        .empty-state span { font-size: 3rem; }
        .empty-state h3 { color: #94a3b8; margin: 1rem 0 0.5rem; }
        .column-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 4px;
            font-size: 0.75rem;
            color: #94a3b8;
        }
        .legend-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        .legend-dot.on { background: #10b981; }
        .legend-dot.off { background: #64748b; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="index.php" class="logo">
            <div class="logo-icon">📦</div>
            <span>Stocky</span>
        </a>
        <ul class="nav-menu">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><span>📊</span> Dashboard</a></li>
            <li class="nav-item"><a href="low-stock.php" class="nav-link active"><span>⚠️</span> Low Stock Variants</a></li>
            <li class="nav-item"><a href="products.php" class="nav-link"><span>📦</span> Products</a></li>
            <li class="nav-section-title">Settings</li>
            <li class="nav-item"><a href="settings.php" class="nav-link"><span>⚙️</span> Column Settings</a></li>
            <li class="nav-section-title">Lab</li>
            <li class="nav-item"><a href="lab-description.php" class="nav-link"><span>📖</span> Instructions</a></li>
            <li class="nav-item"><a href="docs.php" class="nav-link"><span>📚</span> Documentation</a></li>
            <li class="nav-item"><a href="../index.php" class="nav-link"><span>🏠</span> All Labs</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link"><span>🚪</span> Logout</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>⚠️ Low Stock Variants</h1>
            <div class="header-actions">
                <a href="settings.php" class="btn btn-settings">
                    <span>⚙️</span> Column Settings
                </a>
            </div>
        </div>
        
        <div class="settings-banner">
            <span class="settings-banner-text">
                📋 Using Settings ID: <strong><?php echo $settings['id']; ?></strong> — 
                Showing <?php echo array_sum([$settings['show_grade'], $settings['show_product_title'], $settings['show_variant_title'], $settings['show_sku'], $settings['show_lost_per_day'], $settings['show_reorder_point'], $settings['show_lead_time'], $settings['show_need'], $settings['show_depletion_days'], $settings['show_depletion_date'], $settings['show_next_due_date'], $settings['show_stock'], $settings['show_on_po'], $settings['show_on_order']]); ?>/14 columns
            </span>
            <a href="settings.php" style="color: #fbbf24; font-size: 0.85rem;">Edit Columns →</a>
        </div>
        
        <div class="column-legend">
            <span style="color: #64748b; font-size: 0.75rem; margin-right: 0.5rem;">Visible Columns:</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_grade'] ? 'on' : 'off'; ?>"></span> Grade</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_product_title'] ? 'on' : 'off'; ?>"></span> Product</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_variant_title'] ? 'on' : 'off'; ?>"></span> Variant</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_sku'] ? 'on' : 'off'; ?>"></span> SKU</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_stock'] ? 'on' : 'off'; ?>"></span> Stock</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_reorder_point'] ? 'on' : 'off'; ?>"></span> Reorder</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_need'] ? 'on' : 'off'; ?>"></span> Need</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_lead_time'] ? 'on' : 'off'; ?>"></span> Lead Time</span>
            <span class="legend-item"><span class="legend-dot <?php echo $settings['show_depletion_days'] ? 'on' : 'off'; ?>"></span> Depletion</span>
        </div>
        
        <?php if (empty($lowStockProducts)): ?>
            <div class="data-table">
                <div class="empty-state">
                    <span>✅</span>
                    <h3>All Stocked Up!</h3>
                    <p>No products are currently below their reorder point.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <?php if ($settings['show_grade']): ?><th>Grade</th><?php endif; ?>
                            <?php if ($settings['show_product_title']): ?><th>Product</th><?php endif; ?>
                            <?php if ($settings['show_variant_title']): ?><th>Variant</th><?php endif; ?>
                            <?php if ($settings['show_sku']): ?><th>SKU</th><?php endif; ?>
                            <?php if ($settings['show_stock']): ?><th>Stock</th><?php endif; ?>
                            <?php if ($settings['show_reorder_point']): ?><th>Reorder Point</th><?php endif; ?>
                            <?php if ($settings['show_need']): ?><th>Need</th><?php endif; ?>
                            <?php if ($settings['show_lead_time']): ?><th>Lead Time</th><?php endif; ?>
                            <?php if ($settings['show_depletion_days']): ?><th>Depletion Days</th><?php endif; ?>
                            <?php if ($settings['show_depletion_date']): ?><th>Depletion Date</th><?php endif; ?>
                            <?php if ($settings['show_next_due_date']): ?><th>Next Due</th><?php endif; ?>
                            <?php if ($settings['show_lost_per_day']): ?><th>Lost/Day</th><?php endif; ?>
                            <?php if ($settings['show_on_po']): ?><th>On PO</th><?php endif; ?>
                            <?php if ($settings['show_on_order']): ?><th>On Order</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $product): 
                            $grade = calculateGrade($product['stock_quantity'], $product['reorder_point']);
                            $depletionDays = calculateDepletionDays($product['stock_quantity']);
                            $need = calculateNeed($product['stock_quantity'], $product['reorder_point']);
                        ?>
                        <tr>
                            <?php if ($settings['show_grade']): ?>
                                <td><span class="grade-badge <?php echo $grade['class']; ?>"><?php echo $grade['grade']; ?></span></td>
                            <?php endif; ?>
                            <?php if ($settings['show_product_title']): ?>
                                <td><?php echo htmlspecialchars($product['title']); ?></td>
                            <?php endif; ?>
                            <?php if ($settings['show_variant_title']): ?>
                                <td><?php echo htmlspecialchars($product['variant_title']); ?></td>
                            <?php endif; ?>
                            <?php if ($settings['show_sku']): ?>
                                <td><span class="sku-code"><?php echo htmlspecialchars($product['sku']); ?></span></td>
                            <?php endif; ?>
                            <?php if ($settings['show_stock']): ?>
                                <td><span class="stock-value low"><?php echo $product['stock_quantity']; ?></span></td>
                            <?php endif; ?>
                            <?php if ($settings['show_reorder_point']): ?>
                                <td><?php echo $product['reorder_point']; ?></td>
                            <?php endif; ?>
                            <?php if ($settings['show_need']): ?>
                                <td><span class="need-badge">+<?php echo $need; ?></span></td>
                            <?php endif; ?>
                            <?php if ($settings['show_lead_time']): ?>
                                <td><?php echo $product['lead_time_days']; ?> days</td>
                            <?php endif; ?>
                            <?php if ($settings['show_depletion_days']): ?>
                                <td><?php echo $depletionDays; ?> days</td>
                            <?php endif; ?>
                            <?php if ($settings['show_depletion_date']): ?>
                                <td><?php echo date('M j', strtotime("+$depletionDays days")); ?></td>
                            <?php endif; ?>
                            <?php if ($settings['show_next_due_date']): ?>
                                <td><?php echo date('M j', strtotime("+{$product['lead_time_days']} days")); ?></td>
                            <?php endif; ?>
                            <?php if ($settings['show_lost_per_day']): ?>
                                <td>~2</td>
                            <?php endif; ?>
                            <?php if ($settings['show_on_po']): ?>
                                <td>0</td>
                            <?php endif; ?>
                            <?php if ($settings['show_on_order']): ?>
                                <td>0</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
