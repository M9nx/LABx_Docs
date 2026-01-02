<?php
// Lab 30: Stocky - Low Stock Variants Page
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$settings = getUserSettings($pdo, $user['id']);

// Get user's products
$stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY grade, stock ASC");
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll();

// Calculate depletion info (simulated)
function getDaysUntilDepletion($stock, $lostPerDay = 2) {
    return $lostPerDay > 0 ? ceil($stock / $lostPerDay) : 999;
}

function getDepletionDate($days) {
    return date('M j, Y', strtotime("+$days days"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Variants - Stocky</title>
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
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.75rem; color: #333; }
        .header-actions { display: flex; gap: 1rem; }
        .btn { padding: 0.625rem 1.25rem; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s; border: none; cursor: pointer; font-size: 0.9rem; }
        .btn-primary { background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; }
        .btn-secondary { background: #e5e7eb; color: #333; }
        .btn-settings { background: #fbbf24; color: #92400e; }
        .settings-banner {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .settings-banner p { color: #92400e; }
        .settings-banner strong { color: #b45309; }
        .table-container { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb; }
        td { padding: 1rem; border-bottom: 1px solid #f3f4f6; }
        tr:hover td { background: #faf5ff; }
        .grade { display: inline-block; width: 30px; height: 30px; border-radius: 50%; text-align: center; line-height: 30px; font-weight: bold; color: white; }
        .grade-a { background: #059669; }
        .grade-b { background: #fbbf24; color: #92400e; }
        .grade-c { background: #ef4444; }
        .stock-low { color: #ef4444; font-weight: bold; }
        .stock-ok { color: #059669; }
        .sku { font-family: monospace; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .empty-state { text-align: center; padding: 4rem 2rem; color: #666; }
        .empty-state h3 { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <header class="header">
        <a href="dashboard.php" class="logo">📦 Stocky</a>
        <nav class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="settings.php">⚙️ Settings</a>
            <a href="activity.php">Activity</a>
            <a href="index.php">Lab Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>📉 Low Stock Variants</h1>
            <div class="header-actions">
                <a href="settings.php" class="btn btn-settings">⚙️ Column Settings</a>
                <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
            </div>
        </div>

        <div class="settings-banner">
            <p>
                <strong>💡 IDOR Lab Hint:</strong> 
                Your current Settings ID is <strong>#<?= $settings['id'] ?></strong>. 
                Other users have Settings IDs 1-4. Try accessing their settings!
            </p>
            <a href="settings.php" class="btn btn-settings">Configure Columns</a>
        </div>

        <div class="table-container">
            <?php if (empty($products)): ?>
            <div class="empty-state">
                <h3>No products found</h3>
                <p>This store doesn't have any products to track yet.</p>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($settings['show_grade']): ?><th>Grade</th><?php endif; ?>
                        <?php if ($settings['show_product_title']): ?><th>Product</th><?php endif; ?>
                        <?php if ($settings['show_variant_title']): ?><th>Variant</th><?php endif; ?>
                        <?php if ($settings['show_sku']): ?><th>SKU</th><?php endif; ?>
                        <?php if ($settings['show_stock']): ?><th>Stock</th><?php endif; ?>
                        <?php if ($settings['show_reorder_point']): ?><th>Reorder Point</th><?php endif; ?>
                        <?php if ($settings['show_lead_time']): ?><th>Lead Time</th><?php endif; ?>
                        <?php if ($settings['show_need']): ?><th>Need</th><?php endif; ?>
                        <?php if ($settings['show_depletion_days']): ?><th>Depletion Days</th><?php endif; ?>
                        <?php if ($settings['show_depletion_date']): ?><th>Depletion Date</th><?php endif; ?>
                        <?php if ($settings['show_on_po']): ?><th>On PO</th><?php endif; ?>
                        <?php if ($settings['show_on_order']): ?><th>On Order</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): 
                        $depletionDays = getDaysUntilDepletion($product['stock']);
                        $depletionDate = getDepletionDate($depletionDays);
                        $need = max(0, $product['reorder_point'] - $product['stock']);
                    ?>
                    <tr>
                        <?php if ($settings['show_grade']): ?>
                        <td><span class="grade grade-<?= strtolower($product['grade']) ?>"><?= $product['grade'] ?></span></td>
                        <?php endif; ?>
                        <?php if ($settings['show_product_title']): ?>
                        <td><?= htmlspecialchars($product['product_title']) ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_variant_title']): ?>
                        <td><?= htmlspecialchars($product['variant_title']) ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_sku']): ?>
                        <td><span class="sku"><?= htmlspecialchars($product['sku']) ?></span></td>
                        <?php endif; ?>
                        <?php if ($settings['show_stock']): ?>
                        <td class="<?= $product['stock'] < $product['reorder_point'] ? 'stock-low' : 'stock-ok' ?>">
                            <?= $product['stock'] ?>
                        </td>
                        <?php endif; ?>
                        <?php if ($settings['show_reorder_point']): ?>
                        <td><?= $product['reorder_point'] ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_lead_time']): ?>
                        <td><?= $product['lead_time'] ?> days</td>
                        <?php endif; ?>
                        <?php if ($settings['show_need']): ?>
                        <td class="<?= $need > 0 ? 'stock-low' : '' ?>"><?= $need ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_depletion_days']): ?>
                        <td><?= $depletionDays ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_depletion_date']): ?>
                        <td><?= $depletionDate ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_on_po']): ?>
                        <td><?= $product['on_po'] ?></td>
                        <?php endif; ?>
                        <?php if ($settings['show_on_order']): ?>
                        <td><?= $product['on_order'] ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
