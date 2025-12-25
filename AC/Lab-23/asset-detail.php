<?php
// Lab 23: Asset Detail
require_once 'config.php';
requireLogin();

$assetId = $_GET['id'] ?? '';
$asset = null;
$tags = [];

if (!empty($assetId)) {
    try {
        $pdo = getDBConnection();
        
        // Get asset - Only user's own assets
        $stmt = $pdo->prepare("SELECT * FROM assets WHERE asset_id = ? AND user_id = ?");
        $stmt->execute([$assetId, $_SESSION['user_id']]);
        $asset = $stmt->fetch();
        
        if ($asset) {
            // Get tags associated with this asset
            $stmt = $pdo->prepare("
                SELECT t.* FROM tags t
                JOIN asset_tags at ON t.tag_id = at.tag_id
                WHERE at.asset_id = ?
            ");
            $stmt->execute([$assetId]);
            $tags = $stmt->fetchAll();
        }
        
    } catch (PDOException $e) {
        $asset = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Detail - TagScope | Lab 23</title>
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
        .container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .back-link {
            display: inline-flex;
            align-items: center;
            color: #a78bfa;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .detail-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }
        .detail-header h1 { color: #a78bfa; margin-bottom: 0.5rem; }
        .asset-id { color: #64748b; font-size: 0.9rem; }
        .asset-type {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 600;
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
        }
        .detail-section {
            margin-bottom: 1.5rem;
        }
        .detail-section h3 {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .detail-section .value {
            font-family: monospace;
            background: rgba(15, 23, 42, 0.6);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: #f59e0b;
        }
        .tags-section h3 { color: #a78bfa; margin-bottom: 1rem; }
        .tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .tag-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 8px;
        }
        .tag-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }
        .tag-info { display: flex; flex-direction: column; }
        .tag-name { color: #e2e8f0; }
        .tag-encoded { color: #64748b; font-size: 0.75rem; font-family: monospace; }
        .empty-tags {
            color: #64748b;
            padding: 1rem;
            text-align: center;
            background: rgba(15, 23, 42, 0.4);
            border-radius: 8px;
        }
        .not-found {
            text-align: center;
            padding: 4rem;
        }
        .not-found .icon { font-size: 4rem; margin-bottom: 1rem; }
        .not-found h2 { color: #f87171; margin-bottom: 0.5rem; }
        .not-found p { color: #64748b; }
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
        <a href="assets.php" class="back-link">← Back to Assets</a>
        
        <?php if (!$asset): ?>
            <div class="detail-card not-found">
                <div class="icon">🔍</div>
                <h2>Asset Not Found</h2>
                <p>The requested asset doesn't exist or you don't have permission to view it.</p>
            </div>
        <?php else: ?>
            <div class="detail-card">
                <div class="detail-header">
                    <div>
                        <h1><?= e($asset['asset_name']) ?></h1>
                        <span class="asset-id">ID: <?= e($asset['asset_id']) ?></span>
                    </div>
                    <span class="asset-type"><?= e($asset['asset_type']) ?></span>
                </div>
                
                <div class="detail-section">
                    <h3>Asset Value</h3>
                    <div class="value"><?= e($asset['asset_value']) ?></div>
                </div>
                
                <div class="detail-section">
                    <h3>Status</h3>
                    <div class="value"><?= e($asset['status']) ?></div>
                </div>
                
                <div class="detail-section">
                    <h3>Created</h3>
                    <div class="value"><?= date('F d, Y H:i', strtotime($asset['created_at'])) ?></div>
                </div>
                
                <div class="tags-section">
                    <h3>🏷️ Associated Tags</h3>
                    <?php if (empty($tags)): ?>
                        <div class="empty-tags">No tags assigned to this asset</div>
                    <?php else: ?>
                        <div class="tags-list">
                            <?php foreach ($tags as $tag): ?>
                                <div class="tag-badge">
                                    <span class="tag-color" style="background: <?= e($tag['tag_color']) ?>"></span>
                                    <div class="tag-info">
                                        <span class="tag-name"><?= e($tag['tag_name']) ?></span>
                                        <span class="tag-encoded"><?= encodeTagId($tag['internal_id']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
