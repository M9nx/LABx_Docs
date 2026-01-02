<?php
// Lab 30: Stocky Inventory App - Settings Page (VULNERABLE TO IDOR)
require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$settings = getUserSettings($pdo, $user['id']);
$message = '';
$messageType = '';
$exploitDetected = false;

// Handle form submission - THIS IS THE VULNERABLE PART
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update';
    
    if ($action === 'import') {
        // VULNERABILITY: Import settings from another user without ownership check!
        $importFromId = $_POST['import_from_id'] ?? 0;
        
        // Fetch source settings - NO OWNERSHIP CHECK (IDOR VULNERABILITY)
        $stmt = $pdo->prepare("SELECT * FROM settings_for_low_stock_variants WHERE id = ?");
        $stmt->execute([$importFromId]);
        $sourceSettings = $stmt->fetch();
        
        if ($sourceSettings) {
            // Check if this is an IDOR exploit (importing from another user)
            if ($sourceSettings['user_id'] != $user['id']) {
                $exploitDetected = true;
            }
            
            // Get source store info
            $stmt = $pdo->prepare("SELECT store_name FROM users WHERE id = ?");
            $stmt->execute([$sourceSettings['user_id']]);
            $sourceUser = $stmt->fetch();
            
            // Copy settings
            $columns = ['show_grade', 'show_product_title', 'show_variant_title', 'show_sku',
                'show_lost_per_day', 'show_reorder_point', 'show_lead_time', 'show_need',
                'show_depletion_days', 'show_depletion_date', 'show_next_due_date',
                'show_stock', 'show_on_po', 'show_on_order', 'show_shopify_products_only'];
            
            $updates = [];
            $params = [];
            foreach ($columns as $col) {
                $updates[] = "$col = ?";
                $params[] = $sourceSettings[$col];
            }
            $params[] = $settings['id'];
            
            $sql = "UPDATE settings_for_low_stock_variants SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            logActivity($pdo, $user['id'], 'settings_import', "Imported settings from ID: $importFromId (Store: " . ($sourceUser['store_name'] ?? 'Unknown') . ")");
            
            $message = "Settings imported from " . htmlspecialchars($sourceUser['store_name'] ?? "ID #$importFromId") . "!";
            $messageType = 'success';
        } else {
            $message = "Settings ID #$importFromId not found!";
            $messageType = 'error';
        }
        
        $settings = getUserSettings($pdo, $user['id']);
        
    } else {
        // Regular update - ALSO VULNERABLE
        $settingsId = $_POST['settings_id'] ?? $settings['id'];
        
        // Check if this is an IDOR exploit (modifying another user's settings)
        $stmt = $pdo->prepare("SELECT user_id FROM settings_for_low_stock_variants WHERE id = ?");
        $stmt->execute([$settingsId]);
        $targetSettings = $stmt->fetch();
        
        if ($targetSettings && $targetSettings['user_id'] != $user['id']) {
            $exploitDetected = true;
        }
        
        $columns = ['show_grade', 'show_product_title', 'show_variant_title', 'show_sku',
            'show_lost_per_day', 'show_reorder_point', 'show_lead_time', 'show_need',
            'show_depletion_days', 'show_depletion_date', 'show_next_due_date',
            'show_stock', 'show_on_po', 'show_on_order', 'show_shopify_products_only'];
        
        $updates = [];
        $params = [];
        foreach ($columns as $col) {
            $updates[] = "$col = ?";
            $params[] = isset($_POST[$col]) ? 1 : 0;
        }
        
        // VULNERABLE: No check if $settingsId belongs to the current user!
        $params[] = $settingsId;
        
        $sql = "UPDATE settings_for_low_stock_variants SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        logActivity($pdo, $user['id'], 'settings_update', "Updated settings ID: $settingsId");
        
        $message = "Settings updated successfully! (ID: $settingsId)";
        $messageType = 'success';
        
        $settings = getUserSettings($pdo, $user['id']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Column Settings - Stocky</title>
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
        .logo { font-size: 1.4rem; font-weight: bold; color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .nav-links a { color: rgba(255,255,255,0.9); text-decoration: none; margin-left: 1.5rem; font-size: 0.9rem; }
        .nav-links a:hover { color: white; }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.75rem; color: #333; }
        .btn { padding: 0.625rem 1.25rem; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s; border: none; cursor: pointer; font-size: 0.9rem; }
        .btn-primary { background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; }
        .btn-secondary { background: #e5e7eb; color: #333; }
        .btn:hover { transform: translateY(-1px); }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .message.success { background: #d1fae5; border: 1px solid #34d399; color: #065f46; }
        .message.error { background: #fee2e2; border: 1px solid #f87171; color: #991b1b; }
        .exploit-success {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
        }
        .exploit-success h2 { font-size: 1.5rem; margin-bottom: 1rem; }
        .exploit-success .flag { background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 1.1rem; margin: 1rem 0; }
        .exploit-success a { color: #fbbf24; }
        .card { background: white; border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h2 { color: #333; margin-bottom: 1rem; font-size: 1.25rem; }
        .card p { color: #666; margin-bottom: 1.5rem; }
        .settings-id-display { background: #f4f5f7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .settings-id-display label { color: #666; }
        .settings-id-display input { background: white; border: 2px solid #7c3aed; border-radius: 6px; padding: 0.5rem 1rem; color: #7c3aed; font-family: monospace; font-size: 1rem; width: 100px; text-align: center; }
        .settings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .setting-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
        .setting-item:hover { background: #f0e7ff; }
        .setting-item input[type="checkbox"] { width: 20px; height: 20px; accent-color: #7c3aed; cursor: pointer; }
        .setting-item label { color: #333; cursor: pointer; flex: 1; }
        .form-actions { margin-top: 2rem; display: flex; gap: 1rem; }
        .import-section { margin-bottom: 1rem; }
        .import-row { display: flex; gap: 1rem; align-items: flex-end; }
        .import-input-group { flex: 1; }
        .import-input-group label { display: block; color: #666; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .import-input-group input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; }
        .import-input-group input:focus { outline: none; border-color: #7c3aed; }
        .hint-box { background: #fffbeb; border: 1px solid #fbbf24; border-radius: 8px; padding: 1rem; margin-top: 1rem; }
        .hint-box h4 { color: #b45309; margin-bottom: 0.5rem; }
        .hint-box p { color: #92400e; font-size: 0.9rem; margin: 0; }
        .hint-box code { background: rgba(180, 83, 9, 0.1); color: #b45309; padding: 0.1rem 0.3rem; border-radius: 3px; }
        .hint-box ul { margin: 0.5rem 0 0 1.5rem; color: #92400e; font-size: 0.85rem; }
    </style>
</head>
<body>
    <header class="header">
        <a href="dashboard.php" class="logo">📦 Stocky</a>
        <nav class="nav-links">
            <a href="dashboard.php">← Dashboard</a>
            <a href="low-stock.php">Low Stock</a>
            <a href="index.php">Lab Home</a>
            <a href="docs.php">Docs</a>
            <a href="success.php">Submit Flag</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>⚙️ Column Settings</h1>
            <a href="low-stock.php" class="btn btn-secondary">← Back to Low Stock</a>
        </div>

        <?php if ($exploitDetected): ?>
        <div class="exploit-success">
            <h2>🎉 IDOR Exploit Successful!</h2>
            <p>You successfully accessed or modified another user's settings without authorization!</p>
            <div class="flag">FLAG{IDOR_STOCKY_SETTINGS_PWNED_2024}</div>
            <p><a href="success.php">Submit this flag to complete the lab →</a></p>
        </div>
        <?php endif; ?>

        <?php if ($message): ?>
        <div class="message <?= $messageType ?>">
            <?= $messageType === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- IMPORT SETTINGS FEATURE - VULNERABLE TO IDOR -->
        <div class="card">
            <h2>📥 Import Settings from Another Store</h2>
            <p>Copy column preferences from another store. Just enter their Settings ID!</p>
            
            <form method="POST" class="import-section">
                <input type="hidden" name="action" value="import">
                <div class="import-row">
                    <div class="import-input-group">
                        <label for="import_from_id">Import from Settings ID:</label>
                        <input type="number" name="import_from_id" id="import_from_id" placeholder="e.g., 2, 3, 4..." min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">📥 Import Settings</button>
                </div>
            </form>
            
            <div class="hint-box">
                <h4>🎯 IDOR Attack Vector</h4>
                <p>This import feature doesn't verify ownership! Try importing settings from other users:</p>
                <ul>
                    <li>Settings ID <code>1</code> → Alice's Fashion (Your store)</li>
                    <li>Settings ID <code>2</code> → Bob's Tech</li>
                    <li>Settings ID <code>3</code> → Carol's Home</li>
                    <li>Settings ID <code>4</code> → David's Sports</li>
                </ul>
            </div>
        </div>

        <form method="POST" id="settingsForm">
            <input type="hidden" name="action" value="update">
            
            <div class="card">
                <h2>🎯 Settings Target</h2>
                <p>Configure which columns are displayed in the Low Stock Variants view.</p>
                
                <div class="settings-id-display">
                    <label>Settings ID (editable - try changing it!):</label>
                    <input type="number" name="settings_id" value="<?= $settings['id'] ?>" id="settingsIdInput">
                </div>

                <div class="hint-box">
                    <h4>💡 Lab Hint</h4>
                    <p>Change the Settings ID to another user's ID (e.g., <code>2</code>, <code>3</code>, <code>4</code>) and submit to modify their column preferences!</p>
                </div>
            </div>

            <div class="card">
                <h2>📊 Display Columns</h2>
                <p>Select which columns to show in the Low Stock Variants table.</p>
                
                <div class="settings-grid">
                    <div class="setting-item">
                        <input type="checkbox" name="show_grade" id="show_grade" <?= $settings['show_grade'] ? 'checked' : '' ?>>
                        <label for="show_grade">Grade</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_product_title" id="show_product_title" <?= $settings['show_product_title'] ? 'checked' : '' ?>>
                        <label for="show_product_title">Product Title</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_variant_title" id="show_variant_title" <?= $settings['show_variant_title'] ? 'checked' : '' ?>>
                        <label for="show_variant_title">Variant Title</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_sku" id="show_sku" <?= $settings['show_sku'] ? 'checked' : '' ?>>
                        <label for="show_sku">SKU</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_lost_per_day" id="show_lost_per_day" <?= $settings['show_lost_per_day'] ? 'checked' : '' ?>>
                        <label for="show_lost_per_day">Lost Per Day</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_reorder_point" id="show_reorder_point" <?= $settings['show_reorder_point'] ? 'checked' : '' ?>>
                        <label for="show_reorder_point">Reorder Point</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_lead_time" id="show_lead_time" <?= $settings['show_lead_time'] ? 'checked' : '' ?>>
                        <label for="show_lead_time">Lead Time</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_need" id="show_need" <?= $settings['show_need'] ? 'checked' : '' ?>>
                        <label for="show_need">Need</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_depletion_days" id="show_depletion_days" <?= $settings['show_depletion_days'] ? 'checked' : '' ?>>
                        <label for="show_depletion_days">Depletion Days</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_depletion_date" id="show_depletion_date" <?= $settings['show_depletion_date'] ? 'checked' : '' ?>>
                        <label for="show_depletion_date">Depletion Date</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_next_due_date" id="show_next_due_date" <?= $settings['show_next_due_date'] ? 'checked' : '' ?>>
                        <label for="show_next_due_date">Next Due Date</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_stock" id="show_stock" <?= $settings['show_stock'] ? 'checked' : '' ?>>
                        <label for="show_stock">Stock</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_on_po" id="show_on_po" <?= $settings['show_on_po'] ? 'checked' : '' ?>>
                        <label for="show_on_po">On PO</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_on_order" id="show_on_order" <?= $settings['show_on_order'] ? 'checked' : '' ?>>
                        <label for="show_on_order">On Order</label>
                    </div>
                    <div class="setting-item">
                        <input type="checkbox" name="show_shopify_products_only" id="show_shopify_products_only" <?= $settings['show_shopify_products_only'] ? 'checked' : '' ?>>
                        <label for="show_shopify_products_only">Shopify Only</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Save Settings</button>
                    <a href="low-stock.php" class="btn btn-secondary">View Low Stock</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
