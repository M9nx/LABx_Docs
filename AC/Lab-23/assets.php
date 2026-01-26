<?php
// Lab 23: Assets Management
require_once 'config.php';
requireLogin();

$user = getCurrentUser();

try {
    $pdo = getDBConnection();
    
    // Get user's assets with associated tags
    $stmt = $pdo->prepare("
        SELECT a.*, GROUP_CONCAT(t.tag_name) as tags, GROUP_CONCAT(t.tag_color) as tag_colors
        FROM assets a
        LEFT JOIN asset_tags at ON a.asset_id = at.asset_id
        LEFT JOIN tags t ON at.tag_id = t.tag_id
        WHERE a.user_id = ?
        GROUP BY a.asset_id
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $assets = $stmt->fetchAll();
    
    // Get available tags for user
    $stmt = $pdo->prepare("SELECT * FROM tags WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userTags = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $assets = [];
    $userTags = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets - TagScope | Lab 23</title>
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
        .nav-links a.active { background: rgba(99, 102, 241, 0.3); }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 { color: #a78bfa; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); }
        .assets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .asset-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .asset-card:hover {
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-3px);
        }
        .asset-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .asset-header h3 { color: #e2e8f0; font-size: 1.1rem; }
        .asset-type {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        .asset-type.domain { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .asset-type.ip { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .asset-type.url { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .asset-type.cidr { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
        .asset-value {
            font-family: monospace;
            background: rgba(15, 23, 42, 0.6);
            padding: 0.5rem;
            border-radius: 6px;
            color: #f59e0b;
            margin-bottom: 1rem;
            word-break: break-all;
        }
        .asset-id {
            color: #64748b;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        .asset-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .tag {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            background: rgba(99, 102, 241, 0.15);
            color: #a78bfa;
        }
        .tag-color {
            width: 8px;
            height: 8px;
            border-radius: 2px;
        }
        .asset-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-tag {
            background: rgba(99, 102, 241, 0.2);
            color: #a78bfa;
        }
        .btn-view {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 16px;
            border: 2px dashed rgba(99, 102, 241, 0.3);
        }
        .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; }
        .empty-state h3 { color: #a78bfa; margin-bottom: 0.5rem; }
        .empty-state p { color: #64748b; margin-bottom: 1.5rem; }
        
        /* Tag Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        .modal h3 { color: #a78bfa; margin-bottom: 1.5rem; }
        .modal-tags {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 300px;
            overflow-y: auto;
        }
        .modal-tag-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .modal-tag-item:hover { background: rgba(99, 102, 241, 0.2); }
        .modal-tag-item input { cursor: pointer; }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .status-msg {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .status-msg.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
        .status-msg.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="assets.php" class="active">📦 Assets</a>
            <a href="tags.php">🏷️ Tags</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="logout.php" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>📦 Your Assets</h1>
            <a href="create-asset.php" class="btn btn-primary">+ Add Asset</a>
        </div>

        <div id="statusMsg"></div>

        <?php if (empty($assets)): ?>
            <div class="empty-state">
                <div class="icon">📦</div>
                <h3>No Assets Yet</h3>
                <p>Add your first security asset to get started</p>
                <a href="create-asset.php" class="btn btn-primary">+ Add Your First Asset</a>
            </div>
        <?php else: ?>
            <div class="assets-grid">
                <?php foreach ($assets as $asset): ?>
                    <div class="asset-card" data-asset-id="<?= e($asset['asset_id']) ?>">
                        <div class="asset-header">
                            <div>
                                <h3><?= e($asset['asset_name']) ?></h3>
                                <span class="asset-id">ID: <?= e($asset['asset_id']) ?></span>
                            </div>
                            <span class="asset-type <?= strtolower($asset['asset_type']) ?>"><?= e($asset['asset_type']) ?></span>
                        </div>
                        <div class="asset-value"><?= e($asset['asset_value']) ?></div>
                        
                        <div class="asset-tags">
                            <?php 
                            if ($asset['tags']) {
                                $tagNames = explode(',', $asset['tags']);
                                $tagColors = explode(',', $asset['tag_colors']);
                                foreach ($tagNames as $idx => $tagName): 
                            ?>
                                <span class="tag">
                                    <span class="tag-color" style="background: <?= e($tagColors[$idx] ?? '#6366f1') ?>"></span>
                                    <?= e($tagName) ?>
                                </span>
                            <?php endforeach; } else { ?>
                                <span style="color: #64748b; font-size: 0.8rem;">No tags assigned</span>
                            <?php } ?>
                        </div>
                        
                        <div class="asset-actions">
                            <button class="btn-sm btn-tag" onclick="openTagModal('<?= e($asset['asset_id']) ?>')">🏷️ Add Tag</button>
                            <a href="asset-detail.php?id=<?= e($asset['asset_id']) ?>" class="btn-sm btn-view">👁️ View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tag Modal -->
    <div class="modal-overlay" id="tagModal">
        <div class="modal">
            <h3>🏷️ Add Tag to Asset</h3>
            <p style="color: #64748b; margin-bottom: 1rem;">Asset ID: <span id="modalAssetId" style="color: #f59e0b;"></span></p>
            
            <div class="modal-tags">
                <?php foreach ($userTags as $tag): ?>
                    <label class="modal-tag-item">
                        <input type="checkbox" name="tag" value="<?= encodeTagId($tag['internal_id']) ?>" data-internal="<?= $tag['internal_id'] ?>">
                        <span class="tag-color" style="background: <?= e($tag['tag_color']) ?>; width: 12px; height: 12px;"></span>
                        <span><?= e($tag['tag_name']) ?></span>
                        <span style="margin-left: auto; color: #64748b; font-size: 0.75rem;"><?= encodeTagId($tag['internal_id']) ?></span>
                    </label>
                <?php endforeach; ?>
                
                <?php if (empty($userTags)): ?>
                    <p style="color: #64748b; text-align: center; padding: 1rem;">No tags available. <a href="create-tag.php" style="color: #a78bfa;">Create one first</a></p>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 1rem; padding: 1rem; background: rgba(245, 158, 11, 0.1); border-radius: 8px;">
                <p style="color: #f59e0b; font-size: 0.85rem;"><strong>💡 IDOR Tip:</strong> Try using a different tag ID! Encode: <code>gid://tagscope/AsmTag/49790001</code> to base64</p>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="addTagToAsset()">Add Selected Tags</button>
                <button class="btn" style="background: rgba(100, 116, 139, 0.2); color: #94a3b8;" onclick="closeTagModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let currentAssetId = null;
        
        function openTagModal(assetId) {
            currentAssetId = assetId;
            document.getElementById('modalAssetId').textContent = assetId;
            document.getElementById('tagModal').classList.add('active');
        }
        
        function closeTagModal() {
            document.getElementById('tagModal').classList.remove('active');
            currentAssetId = null;
            // Uncheck all
            document.querySelectorAll('.modal-tag-item input').forEach(cb => cb.checked = false);
        }
        
        async function addTagToAsset() {
            const selectedTags = Array.from(document.querySelectorAll('.modal-tag-item input:checked'))
                .map(cb => cb.value);
            
            if (selectedTags.length === 0) {
                alert('Please select at least one tag');
                return;
            }
            
            try {
                const response = await fetch('api/add-tag-to-asset.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operationName: 'AddTagToAssets',
                        variables: {
                            tagId: selectedTags[0], // Just use first tag for demo
                            assetIds: [currentAssetId]
                        }
                    })
                });
                
                const data = await response.json();
                
                const statusDiv = document.getElementById('statusMsg');
                if (data.success) {
                    statusDiv.innerHTML = `<div class="status-msg success">✅ ${data.message}<br>Tag Name: <strong>${data.tag_name}</strong><br>Tag Owner: ${data.tag_owner}</div>`;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    statusDiv.innerHTML = `<div class="status-msg error">❌ ${data.error}</div>`;
                }
                
                closeTagModal();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }
        
        // Close modal on outside click
        document.getElementById('tagModal').addEventListener('click', function(e) {
            if (e.target === this) closeTagModal();
        });
    </script>
</body>
</html>
