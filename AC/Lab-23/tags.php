<?php
// Lab 23: Tags Management
require_once 'config.php';
requireLogin();

$user = getCurrentUser();

try {
    $pdo = getDBConnection();
    
    // Get user's tags with asset count
    $stmt = $pdo->prepare("
        SELECT t.*, COUNT(at.asset_id) as asset_count
        FROM tags t
        LEFT JOIN asset_tags at ON t.tag_id = at.tag_id
        WHERE t.user_id = ?
        GROUP BY t.tag_id
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $tags = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $tags = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tags - TagScope | Lab 23</title>
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
        .tags-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .tag-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .tag-card:hover { border-color: rgba(99, 102, 241, 0.5); }
        .tag-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .tag-color-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }
        .tag-header h3 { color: #e2e8f0; }
        .tag-header .internal-id { color: #64748b; font-size: 0.8rem; }
        .tag-desc {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .tag-ids {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .tag-ids h4 { color: #818cf8; font-size: 0.85rem; margin-bottom: 0.5rem; }
        .tag-ids code {
            display: block;
            color: #f59e0b;
            font-size: 0.8rem;
            word-break: break-all;
            margin: 0.25rem 0;
        }
        .tag-ids code.gid { color: #10b981; }
        .tag-stats {
            display: flex;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid rgba(99, 102, 241, 0.1);
        }
        .tag-stats span {
            color: #64748b;
            font-size: 0.85rem;
        }
        .tag-stats .count {
            color: #a78bfa;
            font-weight: 600;
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
        .hint-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .hint-box h4 { color: #f59e0b; margin-bottom: 0.75rem; }
        .hint-box p { color: #fbbf24; font-size: 0.9rem; line-height: 1.6; }
        .hint-box code { color: #10b981; background: rgba(0,0,0,0.3); padding: 0.1rem 0.3rem; border-radius: 3px; }
        .copy-btn {
            background: rgba(99, 102, 241, 0.2);
            border: none;
            color: #a78bfa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }
        .copy-btn:hover { background: rgba(99, 102, 241, 0.4); }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="assets.php">📦 Assets</a>
            <a href="tags.php" class="active">🏷️ Tags</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="logout.php" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>🏷️ Your Custom Tags</h1>
            <a href="create-tag.php" class="btn btn-primary">+ Create Tag</a>
        </div>

        <div class="hint-box">
            <h4>🔐 Tag ID Encoding Explained</h4>
            <p>Each tag has a <strong>sequential internal ID</strong> that is encoded as a GraphQL-style ID:</p>
            <p>1. Internal ID: <code>49790001</code></p>
            <p>2. GID format: <code>gid://tagscope/AsmTag/49790001</code></p>
            <p>3. Base64 encoded: <code><?= encodeTagId(49790001) ?></code></p>
            <p style="margin-top: 0.75rem;"><strong>🎯 IDOR Attack:</strong> Try decrementing/incrementing the internal ID to discover other users' tags!</p>
        </div>

        <?php if (empty($tags)): ?>
            <div class="empty-state">
                <div class="icon">🏷️</div>
                <h3>No Tags Created</h3>
                <p>Create your first custom tag to categorize your assets</p>
                <a href="create-tag.php" class="btn btn-primary">+ Create Your First Tag</a>
            </div>
        <?php else: ?>
            <div class="tags-grid">
                <?php foreach ($tags as $tag): ?>
                    <div class="tag-card">
                        <div class="tag-header">
                            <div class="tag-color-preview" style="background: <?= e($tag['tag_color']) ?>"></div>
                            <div>
                                <h3><?= e($tag['tag_name']) ?></h3>
                                <span class="internal-id">Internal ID: <?= e($tag['internal_id']) ?></span>
                            </div>
                        </div>
                        
                        <p class="tag-desc"><?= e($tag['description']) ?></p>
                        
                        <div class="tag-ids">
                            <h4>🔑 Tag Identifiers</h4>
                            <code class="gid">gid://tagscope/AsmTag/<?= e($tag['internal_id']) ?></code>
                            <code>
                                <?= encodeTagId($tag['internal_id']) ?>
                                <button class="copy-btn" onclick="copyToClipboard('<?= encodeTagId($tag['internal_id']) ?>')">📋 Copy</button>
                            </code>
                        </div>
                        
                        <div class="tag-stats">
                            <span>Assets Tagged: <span class="count"><?= $tag['asset_count'] ?></span></span>
                            <span>Created: <?= date('M d, Y', strtotime($tag['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied: ' + text);
            });
        }
    </script>
</body>
</html>
