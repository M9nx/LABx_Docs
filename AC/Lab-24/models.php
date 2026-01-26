<?php
require_once 'config.php';
requireLogin();

// Get user's models
$stmt = $pdo->prepare("
    SELECT m.*, p.name as project_name, p.path as project_path,
           (SELECT COUNT(*) FROM model_versions WHERE model_id = m.id) as version_count
    FROM ml_models m
    JOIN projects p ON m.project_id = p.id
    WHERE m.owner_id = ?
    ORDER BY m.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$userModels = $stmt->fetchAll();

// Get public models from other users
$stmt = $pdo->prepare("
    SELECT m.*, p.name as project_name, p.path as project_path, u.username as owner_name,
           (SELECT COUNT(*) FROM model_versions WHERE model_id = m.id) as version_count
    FROM ml_models m
    JOIN projects p ON m.project_id = p.id
    JOIN users u ON m.owner_id = u.id
    WHERE m.visibility = 'public' AND m.owner_id != ?
    ORDER BY m.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$publicModels = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Models - ML Model Registry</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(252, 109, 38, 0.2);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            color: #fc6d26;
            font-size: 0.9rem;
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .section {
            margin-bottom: 2.5rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(252, 109, 38, 0.2);
        }
        .section-header h2 {
            color: #fc6d26;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .model-count {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.85rem;
        }
        .model-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.25rem;
        }
        .model-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .model-card:hover {
            border-color: #fc6d26;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .model-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }
        .model-name {
            font-weight: 600;
            color: #fff;
            font-size: 1.15rem;
        }
        .model-visibility {
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
            border-radius: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .model-visibility.public {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .model-visibility.private {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .model-project {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .model-description {
            color: #999;
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .model-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .model-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #666;
        }
        .model-gid {
            font-family: 'Consolas', monospace;
            font-size: 0.65rem;
            color: #555;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .owner-badge {
            font-size: 0.75rem;
            color: #888;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.2rem 0.5rem;
            border-radius: 5px;
            margin-left: auto;
        }
        .attack-hint {
            background: rgba(255, 102, 102, 0.1);
            border: 1px solid rgba(255, 102, 102, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .attack-hint h3 {
            color: #ff6666;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .attack-hint p {
            color: #ccc;
            line-height: 1.6;
        }
        .attack-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                    <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                    <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                    <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
                </svg>
                MLRegistry
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="models.php" style="color: #fc6d26;">My Models</a>
                <a href="docs.php">Docs</a>
                <a href="success.php">Submit Flag</a>
                <div class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php" style="color: #ff6666;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h1>🤖 ML Models</h1>
            <p>View and manage your machine learning models</p>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 IDOR Attack Hint</h3>
            <p>
                Notice that your model has <code>internal_id = 1000500</code> in its GID. 
                There are 7 private models with IDs <code>1000501</code> through <code>1000507</code> 
                that belong to other users.
            </p>
            <p style="margin-top: 0.75rem;">
                <strong>Try:</strong> Open browser console (F12) and run:
            </p>
            <p style="margin-top: 0.5rem;">
                <code>
                fetch('/Lab-24/api/graphql.php', {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({operationName:'getModel',variables:{id:btoa('gid://gitlab/Ml::Model/1000501')}})}).then(r=>r.json()).then(console.log)
                </code>
            </p>
        </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-header">
                <h2>📁 Your Models <span class="model-count"><?php echo count($userModels); ?></span></h2>
            </div>
            
            <?php if (empty($userModels)): ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>You don't have any ML models yet</p>
            </div>
            <?php else: ?>
            <div class="model-grid">
                <?php foreach ($userModels as $model): ?>
                <a href="model-detail.php?gid=<?php echo urlencode(encodeModelGid($model['internal_id'])); ?>" class="model-card">
                    <div class="model-header">
                        <span class="model-name"><?php echo htmlspecialchars($model['name']); ?></span>
                        <span class="model-visibility <?php echo $model['visibility']; ?>">
                            <?php echo $model['visibility']; ?>
                        </span>
                    </div>
                    <div class="model-project">
                        📁 <?php echo htmlspecialchars($model['project_path']); ?>
                    </div>
                    <div class="model-description">
                        <?php echo htmlspecialchars(substr($model['description'] ?? 'No description', 0, 100)); ?>
                    </div>
                    <div class="model-meta">
                        <div class="model-stats">
                            <span>📊 <?php echo $model['version_count']; ?> versions</span>
                        </div>
                        <div class="model-gid" title="<?php echo encodeModelGid($model['internal_id']); ?>">
                            <?php echo encodeModelGid($model['internal_id']); ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>🌐 Public Models <span class="model-count"><?php echo count($publicModels); ?></span></h2>
            </div>
            
            <?php if (empty($publicModels)): ?>
            <div class="empty-state">
                <div class="icon">🔒</div>
                <p>No public models available</p>
            </div>
            <?php else: ?>
            <div class="model-grid">
                <?php foreach ($publicModels as $model): ?>
                <a href="model-detail.php?gid=<?php echo urlencode(encodeModelGid($model['internal_id'])); ?>" class="model-card">
                    <div class="model-header">
                        <span class="model-name"><?php echo htmlspecialchars($model['name']); ?></span>
                        <span class="model-visibility <?php echo $model['visibility']; ?>">
                            <?php echo $model['visibility']; ?>
                        </span>
                    </div>
                    <div class="model-project">
                        📁 <?php echo htmlspecialchars($model['project_path']); ?>
                    </div>
                    <div class="model-description">
                        <?php echo htmlspecialchars(substr($model['description'] ?? 'No description', 0, 100)); ?>
                    </div>
                    <div class="model-meta">
                        <div class="model-stats">
                            <span>📊 <?php echo $model['version_count']; ?> versions</span>
                        </div>
                        <span class="owner-badge">by <?php echo htmlspecialchars($model['owner_name']); ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
