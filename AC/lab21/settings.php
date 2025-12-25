<?php
// Lab 21: Column Settings Page - VULNERABLE ENDPOINT
// This is the vulnerable settings update functionality
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$store = getUserStore($pdo, $user['id']);
$settings = getStoreSettings($pdo, $store['id']);

$success_message = '';
$error_message = '';

// Handle form submission - VULNERABLE CODE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get settings ID from URL or form - THIS IS THE VULNERABILITY
    // The code accepts ANY settings_id without verifying ownership
    $settings_id = $_POST['settings_id'] ?? $settings['id'];
    
    // VULNERABLE: No check if this settings_id belongs to the current user's store!
    // An attacker can modify settings_id to update another user's settings
    
    $columns = [
        'show_grade' => isset($_POST['show_grade']) ? 1 : 0,
        'show_product_title' => isset($_POST['show_product_title']) ? 1 : 0,
        'show_variant_title' => isset($_POST['show_variant_title']) ? 1 : 0,
        'show_sku' => isset($_POST['show_sku']) ? 1 : 0,
        'show_lost_per_day' => isset($_POST['show_lost_per_day']) ? 1 : 0,
        'show_reorder_point' => isset($_POST['show_reorder_point']) ? 1 : 0,
        'show_lead_time' => isset($_POST['show_lead_time']) ? 1 : 0,
        'show_need' => isset($_POST['show_need']) ? 1 : 0,
        'show_depletion_days' => isset($_POST['show_depletion_days']) ? 1 : 0,
        'show_depletion_date' => isset($_POST['show_depletion_date']) ? 1 : 0,
        'show_next_due_date' => isset($_POST['show_next_due_date']) ? 1 : 0,
        'show_stock' => isset($_POST['show_stock']) ? 1 : 0,
        'show_on_po' => isset($_POST['show_on_po']) ? 1 : 0,
        'show_on_order' => isset($_POST['show_on_order']) ? 1 : 0,
        'show_shopify_products_only' => isset($_POST['show_shopify_products_only']) ? 1 : 0,
    ];
    
    // Build the update query
    $sql = "UPDATE column_settings SET 
            show_grade = :show_grade,
            show_product_title = :show_product_title,
            show_variant_title = :show_variant_title,
            show_sku = :show_sku,
            show_lost_per_day = :show_lost_per_day,
            show_reorder_point = :show_reorder_point,
            show_lead_time = :show_lead_time,
            show_need = :show_need,
            show_depletion_days = :show_depletion_days,
            show_depletion_date = :show_depletion_date,
            show_next_due_date = :show_next_due_date,
            show_stock = :show_stock,
            show_on_po = :show_on_po,
            show_on_order = :show_on_order,
            show_shopify_products_only = :show_shopify_products_only
            WHERE id = :settings_id";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($columns, ['settings_id' => $settings_id]));
        
        // Check if we modified another user's settings (for lab success detection)
        if ($settings_id != $settings['id']) {
            // Attacker successfully modified another user's settings!
            header('Location: success.php?attack=idor&target_id=' . $settings_id);
            exit;
        }
        
        $success_message = 'Settings updated successfully!';
        // Refresh settings
        $settings = getStoreSettings($pdo, $store['id']);
    } catch (PDOException $e) {
        $error_message = 'Error updating settings: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Column Settings - <?php echo htmlspecialchars($store['store_name']); ?> | Stocky</title>
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
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .settings-form {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 2rem;
        }
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }
        .settings-id-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .settings-id-box label {
            display: block;
            font-size: 0.75rem;
            color: #f87171;
            margin-bottom: 0.25rem;
        }
        .settings-id-box input {
            background: transparent;
            border: none;
            color: #f87171;
            font-family: 'Consolas', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            width: 100px;
        }
        .settings-id-box input:focus {
            outline: none;
        }
        .vulnerable-hint {
            font-size: 0.8rem;
            color: #f59e0b;
            margin-top: 0.25rem;
        }
        .columns-section h3 {
            color: #a5b4fc;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        .columns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .column-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(99, 102, 241, 0.1);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .column-toggle:hover {
            border-color: rgba(99, 102, 241, 0.3);
            background: rgba(99, 102, 241, 0.05);
        }
        .column-toggle input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #6366f1;
            cursor: pointer;
        }
        .column-toggle .label-text {
            flex: 1;
        }
        .column-toggle .label-text span {
            display: block;
            color: #e2e8f0;
            font-weight: 500;
        }
        .column-toggle .label-text small {
            color: #64748b;
            font-size: 0.8rem;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
        }
        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }
        .btn-secondary {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
        }
        .btn-secondary:hover {
            background: rgba(99, 102, 241, 0.2);
        }
        .request-preview {
            background: #0d1117;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .request-preview h4 {
            color: #f87171;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .request-preview pre {
            color: #94a3b8;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            overflow-x: auto;
        }
        .request-preview .highlight {
            color: #10b981;
            font-weight: 600;
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
            <li class="nav-item"><a href="products.php" class="nav-link"><span>📦</span> Products</a></li>
            <li class="nav-section-title">Settings</li>
            <li class="nav-item"><a href="settings.php" class="nav-link active"><span>⚙️</span> Column Settings</a></li>
            <li class="nav-section-title">Lab</li>
            <li class="nav-item"><a href="lab-description.php" class="nav-link"><span>📖</span> Instructions</a></li>
            <li class="nav-item"><a href="docs.php" class="nav-link"><span>📚</span> Documentation</a></li>
            <li class="nav-item"><a href="../index.php" class="nav-link"><span>🏠</span> All Labs</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link"><span>🚪</span> Logout</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>⚙️ Column Settings</h1>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="settings-form" id="settingsForm">
            <div class="form-header">
                <div>
                    <h2 style="color: #e2e8f0; margin-bottom: 0.25rem;">Low Stock Variants Columns</h2>
                    <p style="color: #64748b; font-size: 0.9rem;">Choose which columns to display in your Low Stock Variants table</p>
                </div>
                <div class="settings-id-box">
                    <label>⚠️ Settings ID</label>
                    <input type="text" name="settings_id" id="settingsId" value="<?php echo $settings['id']; ?>">
                    <div class="vulnerable-hint">Try changing this ID! 🎯</div>
                </div>
            </div>
            
            <div class="columns-section">
                <h3>📊 Data Columns</h3>
                <div class="columns-grid">
                    <label class="column-toggle">
                        <input type="checkbox" name="show_grade" <?php echo $settings['show_grade'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Grade</span>
                            <small>Stock level indicator (A-D)</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_product_title" <?php echo $settings['show_product_title'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Product Title</span>
                            <small>Main product name</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_variant_title" <?php echo $settings['show_variant_title'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Variant Title</span>
                            <small>Product variant details</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_sku" <?php echo $settings['show_sku'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>SKU</span>
                            <small>Stock keeping unit</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_lost_per_day" <?php echo $settings['show_lost_per_day'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Lost Per Day</span>
                            <small>Daily sales rate</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_reorder_point" <?php echo $settings['show_reorder_point'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Reorder Point</span>
                            <small>Min stock threshold</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_lead_time" <?php echo $settings['show_lead_time'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Lead Time</span>
                            <small>Supplier delivery days</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_need" <?php echo $settings['show_need'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Need</span>
                            <small>Quantity to order</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_depletion_days" <?php echo $settings['show_depletion_days'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Depletion Days</span>
                            <small>Days until out of stock</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_depletion_date" <?php echo $settings['show_depletion_date'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Depletion Date</span>
                            <small>Expected stockout date</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_next_due_date" <?php echo $settings['show_next_due_date'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Next Due Date</span>
                            <small>Next reorder date</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_stock" <?php echo $settings['show_stock'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Stock</span>
                            <small>Current inventory</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_on_po" <?php echo $settings['show_on_po'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>On PO</span>
                            <small>On purchase order</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_on_order" <?php echo $settings['show_on_order'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>On Order</span>
                            <small>Customer orders pending</small>
                        </div>
                    </label>
                    <label class="column-toggle">
                        <input type="checkbox" name="show_shopify_products_only" <?php echo $settings['show_shopify_products_only'] ? 'checked' : ''; ?>>
                        <div class="label-text">
                            <span>Shopify Products Only</span>
                            <small>Filter by Shopify products</small>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Update Settings</button>
                <a href="low-stock.php" class="btn btn-secondary">View Low Stock →</a>
            </div>
        </form>
        
        <div class="request-preview">
            <h4>🔍 Request Preview (Interceptable)</h4>
            <pre>POST /settings_for_low_stock_variants/<span class="highlight" id="previewId"><?php echo $settings['id']; ?></span> HTTP/1.1
Host: app.stockyhq.com
Content-Type: application/x-www-form-urlencoded

settings_id=<span class="highlight" id="previewId2"><?php echo $settings['id']; ?></span>&show_grade=1&show_product_title=1&...

<span style="color: #f59e0b;">💡 Hint: Change settings_id to another user's ID (e.g., 111111) to exploit IDOR!</span></pre>
        </div>
    </main>
    
    <script>
        // Update request preview when settings ID changes
        document.getElementById('settingsId').addEventListener('input', function() {
            document.getElementById('previewId').textContent = this.value;
            document.getElementById('previewId2').textContent = this.value;
        });
    </script>
</body>
</html>
