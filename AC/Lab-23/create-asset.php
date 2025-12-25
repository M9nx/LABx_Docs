<?php
// Lab 23: Create Asset
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assetName = trim($_POST['asset_name'] ?? '');
    $assetType = $_POST['asset_type'] ?? '';
    $assetValue = trim($_POST['asset_value'] ?? '');
    
    if (empty($assetName) || empty($assetType) || empty($assetValue)) {
        $error = 'All fields are required.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Generate asset ID
            $assetId = 'AST_' . strtoupper(substr($_SESSION['user_id'], 0, 1)) . '_' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $stmt = $pdo->prepare("INSERT INTO assets (asset_id, user_id, asset_name, asset_type, asset_value) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$assetId, $_SESSION['user_id'], $assetName, $assetType, $assetValue]);
            
            logActivity($_SESSION['user_id'], 'create_asset', 'asset', $assetId, 'Created asset: ' . $assetName);
            
            $success = "Asset created successfully! ID: $assetId";
            
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Asset - TagScope | Lab 23</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.5rem; font-weight: bold; color: #a78bfa; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a78bfa;
            text-decoration: none;
            border-radius: 6px;
        }
        .container { max-width: 600px; margin: 0 auto; padding: 2rem; }
        .form-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .form-card h1 { color: #a78bfa; margin-bottom: 0.5rem; }
        .form-card .subtitle { color: #64748b; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 1rem;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #6366f1;
        }
        .form-group select option { background: #1e293b; }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-secondary {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="assets.php">📦 Assets</a>
            <a href="tags.php">🏷️ Tags</a>
        </nav>
    </header>

    <div class="container">
        <div class="form-card">
            <h1>📦 Add New Asset</h1>
            <p class="subtitle">Register a new security asset in your scope</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?= e($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= e($success) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="asset_name">Asset Name</label>
                    <input type="text" id="asset_name" name="asset_name" placeholder="e.g., Main Website" required>
                </div>
                
                <div class="form-group">
                    <label for="asset_type">Asset Type</label>
                    <select id="asset_type" name="asset_type" required>
                        <option value="">Select type...</option>
                        <option value="DOMAIN">Domain</option>
                        <option value="IP">IP Address</option>
                        <option value="URL">URL/Endpoint</option>
                        <option value="CIDR">CIDR Range</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="asset_value">Asset Value</label>
                    <input type="text" id="asset_value" name="asset_value" placeholder="e.g., example.com or 192.168.1.1" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Asset</button>
                    <a href="assets.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
