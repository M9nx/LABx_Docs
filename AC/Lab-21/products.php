<?php
// Lab 21: Products Page - Stocky Application
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$store = getUserStore($pdo, $user['id']);
$products = getAllProducts($pdo, $store['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - <?php echo htmlspecialchars($store['store_name']); ?> | Stocky</title>
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
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            color: #e2e8f0;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background: rgba(99, 102, 241, 0.05); }
        .stock-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .stock-low {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }
        .stock-ok {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        .sku-code {
            font-family: 'Consolas', monospace;
            background: rgba(99, 102, 241, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
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
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><span>📊</span> Dashboard</a></li>
            <li class="nav-item"><a href="low-stock.php" class="nav-link"><span>⚠️</span> Low Stock Variants</a></li>
            <li class="nav-item"><a href="products.php" class="nav-link active"><span>📦</span> Products</a></li>
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
            <h1>📦 All Products</h1>
        </div>
        
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Cost</th>
                        <th>Stock</th>
                        <th>Reorder Point</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['title']); ?></td>
                        <td><?php echo htmlspecialchars($product['variant_title']); ?></td>
                        <td><span class="sku-code"><?php echo htmlspecialchars($product['sku']); ?></span></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>$<?php echo number_format($product['cost'], 2); ?></td>
                        <td>
                            <span class="stock-badge <?php echo $product['stock_quantity'] <= $product['reorder_point'] ? 'stock-low' : 'stock-ok'; ?>">
                                <?php echo $product['stock_quantity']; ?>
                            </span>
                        </td>
                        <td><?php echo $product['reorder_point']; ?></td>
                        <td><?php echo htmlspecialchars($product['supplier']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
