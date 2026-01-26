<?php
// Lab 23: Dashboard
require_once 'config.php';
requireLogin();

$user = getCurrentUser();

try {
    $pdo = getDBConnection();
    
    // Get user's assets count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assets WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $assetCount = $stmt->fetch()['count'];
    
    // Get user's tags count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tags WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $tagCount = $stmt->fetch()['count'];
    
    // Get user's recent assets
    $stmt = $pdo->prepare("SELECT * FROM assets WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $recentAssets = $stmt->fetchAll();
    
    // Get user's tags with encoded IDs
    $stmt = $pdo->prepare("SELECT * FROM tags WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $userTags = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $assetCount = 0;
    $tagCount = 0;
    $recentAssets = [];
    $userTags = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TagScope | Lab 23</title>
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
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-badge {
            padding: 0.4rem 0.8rem;
            background: rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            font-size: 0.85rem;
            color: #a78bfa;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .welcome {
            margin-bottom: 2rem;
        }
        .welcome h1 { color: #a78bfa; margin-bottom: 0.5rem; }
        .welcome p { color: #64748b; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2rem; font-weight: bold; color: #a78bfa; }
        .stat-card .label { color: #64748b; font-size: 0.9rem; }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .card h3 {
            color: #a78bfa;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .asset-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .asset-item .info h4 { color: #e2e8f0; font-size: 0.95rem; }
        .asset-item .info p { color: #64748b; font-size: 0.8rem; }
        .asset-type {
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            text-transform: uppercase;
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
        }
        .tag-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .tag-item .tag-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tag-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }
        .tag-id {
            font-family: monospace;
            font-size: 0.7rem;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
        }
        .btn-sm {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .hint-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .hint-box h4 { color: #f59e0b; margin-bottom: 0.5rem; }
        .hint-box p { color: #fbbf24; font-size: 0.85rem; line-height: 1.5; }
        .hint-box code { color: #10b981; font-size: 0.8rem; }
        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        @media (max-width: 900px) {
            .content-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="index.php">← Home</a>
            <a href="assets.php">📦 Assets</a>
            <a href="tags.php">🏷️ Tags</a>
            <a href="lab-description.php">📖 Guide</a>
        </nav>
        <div class="user-info">
            <span class="user-badge">👤 <?= e($user['username']) ?></span>
            <a href="logout.php" class="nav-links"><span style="padding: 0.5rem 1rem; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 6px;">Logout</span></a>
        </div>
    </header>

    <div class="container">
        <div class="welcome">
            <h1>Welcome, <?= e($user['full_name']) ?>!</h1>
            <p>Manage your security assets and organize them with custom tags</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📦</div>
                <div class="value"><?= $assetCount ?></div>
                <div class="label">Total Assets</div>
            </div>
            <div class="stat-card">
                <div class="icon">🏷️</div>
                <div class="value"><?= $tagCount ?></div>
                <div class="label">Custom Tags</div>
            </div>
            <div class="stat-card">
                <div class="icon">🏢</div>
                <div class="value"><?= e($user['organization']) ?></div>
                <div class="label">Organization</div>
            </div>
            <div class="stat-card">
                <div class="icon">🔑</div>
                <div class="value" style="font-size: 0.8rem;"><?= e(substr($user['api_token'], 0, 12)) ?>...</div>
                <div class="label">API Token</div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <h3>📦 Recent Assets</h3>
                <?php if (empty($recentAssets)): ?>
                    <p style="color: #64748b;">No assets yet. Create your first asset!</p>
                <?php else: ?>
                    <?php foreach ($recentAssets as $asset): ?>
                        <div class="asset-item">
                            <div class="info">
                                <h4><?= e($asset['asset_name']) ?></h4>
                                <p><?= e($asset['asset_value']) ?></p>
                            </div>
                            <span class="asset-type"><?= e($asset['asset_type']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="quick-actions">
                    <a href="assets.php" class="btn btn-sm">View All Assets</a>
                    <a href="create-asset.php" class="btn btn-sm">+ Add Asset</a>
                </div>
            </div>

            <div class="card">
                <h3>🏷️ Your Tags</h3>
                <?php if (empty($userTags)): ?>
                    <p style="color: #64748b;">No tags created yet.</p>
                <?php else: ?>
                    <?php foreach ($userTags as $tag): ?>
                        <div class="tag-item">
                            <div class="tag-name">
                                <span class="tag-color" style="background: <?= e($tag['tag_color']) ?>"></span>
                                <span><?= e($tag['tag_name']) ?></span>
                            </div>
                            <span class="tag-id" title="Internal ID: <?= $tag['internal_id'] ?>"><?= encodeTagId($tag['internal_id']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="quick-actions">
                    <a href="tags.php" class="btn btn-sm">Manage Tags</a>
                    <a href="create-tag.php" class="btn btn-sm">+ New Tag</a>
                </div>
                
                <div class="hint-box">
                    <h4>💡 IDOR Hint</h4>
                    <p>Notice the encoded tag IDs? They're base64 encoded internal IDs!</p>
                    <p>Decode: <code><?= encodeTagId(49790100) ?></code></p>
                    <p>→ <code>gid://tagscope/AsmTag/49790100</code></p>
                    <p>Try IDs: 49790001 - 49790007 to find victim's tags!</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
