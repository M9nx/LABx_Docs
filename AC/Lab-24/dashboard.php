<?php
require_once 'config.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's models
$stmt = $pdo->prepare("
    SELECT m.*, p.name as project_name, p.path as project_path
    FROM ml_models m
    JOIN projects p ON m.project_id = p.id
    WHERE m.owner_id = ?
    ORDER BY m.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$userModels = $stmt->fetchAll();

// Get user's projects
$stmt = $pdo->prepare("SELECT * FROM projects WHERE owner_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userProjects = $stmt->fetchAll();

// Get recent activity
$stmt = $pdo->prepare("
    SELECT * FROM activity_log 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recentActivity = $stmt->fetchAll();

// Count total models visible to user (public + own)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM ml_models WHERE visibility = 'public'");
$publicCount = $stmt->fetch()['count'];
$totalAccessible = $publicCount + count($userModels);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ML Model Registry</title>
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
        .welcome-banner {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .welcome-banner h1 {
            font-size: 2rem;
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .welcome-banner p { color: #888; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #fc6d26;
        }
        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #fc6d26;
        }
        .stat-card .label {
            color: #888;
            font-size: 0.9rem;
        }
        .section {
            margin-bottom: 2rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .section-header h2 {
            color: #fc6d26;
            font-size: 1.25rem;
        }
        .section-header a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .section-header a:hover { color: #fc6d26; }
        .model-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        .model-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .model-card:hover {
            border-color: #fc6d26;
            transform: translateY(-3px);
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
            font-size: 1.1rem;
        }
        .model-visibility {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            text-transform: uppercase;
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
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .model-gid {
            font-family: 'Consolas', monospace;
            font-size: 0.7rem;
            color: #666;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            word-break: break-all;
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
        .activity-list { list-style: none; }
        .activity-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-action { color: #ccc; }
        .activity-time { color: #666; font-size: 0.85rem; }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, #fc6d26 0%, #e24329 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
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
                <a href="models.php">My Models</a>
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
        <div class="welcome-banner">
            <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Manage your ML models and explore the Model Registry</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">🤖</div>
                <div class="value"><?php echo count($userModels); ?></div>
                <div class="label">Your Models</div>
            </div>
            <div class="stat-card">
                <div class="icon">📁</div>
                <div class="value"><?php echo count($userProjects); ?></div>
                <div class="label">Your Projects</div>
            </div>
            <div class="stat-card">
                <div class="icon">🌐</div>
                <div class="value"><?php echo $publicCount; ?></div>
                <div class="label">Public Models</div>
            </div>
            <div class="stat-card">
                <div class="icon">🔓</div>
                <div class="value"><?php echo $totalAccessible; ?></div>
                <div class="label">Accessible Models</div>
            </div>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 Attack Hint</h3>
            <p>
                You currently have access to <strong><?php echo $totalAccessible; ?></strong> models through legitimate means.
                However, there are <strong>7 private models</strong> belonging to other users that you shouldn't be able to access...
            </p>
            <p style="margin-top: 0.75rem;">
                <strong>Try this:</strong> Your model has <code>internal_id = 1000500</code>. What happens if you query the GraphQL API 
                with <code>internal_id = 1000501</code>?
            </p>
            <p style="margin-top: 0.5rem;">
                Encode the GID: <code>btoa("gid://gitlab/Ml::Model/1000501")</code>
            </p>
        </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-header">
                <h2>Your ML Models</h2>
                <a href="models.php">View all →</a>
            </div>
            
            <?php if (empty($userModels)): ?>
            <div class="empty-state">
                <p>You don't have any ML models yet.</p>
            </div>
            <?php else: ?>
            <div class="model-grid">
                <?php foreach (array_slice($userModels, 0, 3) as $model): ?>
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
                    <div class="model-gid">
                        GID: <?php echo encodeModelGid($model['internal_id']); ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Recent Activity</h2>
            </div>
            
            <?php if (empty($recentActivity)): ?>
            <div class="empty-state">
                <p>No recent activity</p>
            </div>
            <?php else: ?>
            <ul class="activity-list">
                <?php foreach ($recentActivity as $activity): ?>
                <li class="activity-item">
                    <span class="activity-action"><?php echo htmlspecialchars($activity['action']); ?></span>
                    <span class="activity-time"><?php echo date('M j, H:i', strtotime($activity['created_at'])); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="docs.php" class="btn">📚 Read Documentation</a>
            <a href="success.php" class="btn" style="margin-left: 1rem;">🏆 Submit Solution</a>
        </div>
    </main>
</body>
</html>
