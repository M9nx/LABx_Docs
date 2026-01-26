<?php
require_once 'config.php';
requireLogin();

$user = getCurrentUser();

// Get user's projects
$stmt = $pdo->prepare("SELECT * FROM projects WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();

// Get user's snippets
$stmt = $pdo->prepare("SELECT * FROM personal_snippets WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$snippets = $stmt->fetchAll();

// Get recent activity
$stmt = $pdo->prepare("
    SELECT * FROM activity_log 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$activities = $stmt->fetchAll();

// Count stats
$projectCount = count($projects);
$snippetCount = count($snippets);
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notes WHERE author_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$noteCount = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SnippetHub</title>
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
        .stat-card .icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #fc6d26;
        }
        .stat-card .label { color: #888; font-size: 0.9rem; }
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
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        .item-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .item-card:hover {
            border-color: #fc6d26;
            transform: translateY(-3px);
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }
        .item-name {
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
        }
        .item-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            text-transform: uppercase;
        }
        .item-badge.private {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .item-badge.public {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .item-desc {
            color: #888;
            font-size: 0.85rem;
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
        .activity-target { color: #fc6d26; font-weight: 500; }
        .activity-time { color: #666; font-size: 0.85rem; }
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
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
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
                SnippetHub
            </a>
            <nav class="nav-links">
                <a href="dashboard.php" style="color: #fc6d26;">Dashboard</a>
                <a href="projects.php">Projects</a>
                <a href="snippets.php">Snippets</a>
                <a href="activity.php">Activity</a>
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
            <p>Manage your projects, snippets, and collaborate with others</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📁</div>
                <div class="value"><?php echo $projectCount; ?></div>
                <div class="label">Projects</div>
            </div>
            <div class="stat-card">
                <div class="icon">📝</div>
                <div class="value"><?php echo $snippetCount; ?></div>
                <div class="label">Snippets</div>
            </div>
            <div class="stat-card">
                <div class="icon">💬</div>
                <div class="value"><?php echo $noteCount; ?></div>
                <div class="label">Notes Created</div>
            </div>
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="value"><?php echo count($activities); ?></div>
                <div class="label">Recent Activities</div>
            </div>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 Attack Hint</h3>
            <p>
                You have a project with an issue. Try creating a <strong>note/comment</strong> on your issue, 
                then intercept the request and change:
            </p>
            <p style="margin-top: 0.75rem;">
                <code>noteable_type: "issue"</code> → <code>noteable_type: "personal_snippet"</code><br>
                <code>noteable_id: [your_issue_id]</code> → <code>noteable_id: 1-5</code> (victim's private snippets)
            </p>
            <p style="margin-top: 0.75rem;">
                After creating the note, check your <a href="activity.php" style="color: #fc6d26;">Activity page</a> 
                to see the leaked snippet title!
            </p>
        </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-header">
                <h2>📁 Your Projects</h2>
                <a href="projects.php">View all →</a>
            </div>
            
            <?php if (empty($projects)): ?>
            <div class="empty-state">No projects yet</div>
            <?php else: ?>
            <div class="item-grid">
                <?php foreach (array_slice($projects, 0, 3) as $project): ?>
                <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="item-card">
                    <div class="item-header">
                        <span class="item-name"><?php echo htmlspecialchars($project['name']); ?></span>
                        <span class="item-badge <?php echo $project['visibility']; ?>">
                            <?php echo $project['visibility']; ?>
                        </span>
                    </div>
                    <div class="item-desc"><?php echo htmlspecialchars($project['path']); ?></div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>📝 Your Snippets</h2>
                <a href="snippets.php">View all →</a>
            </div>
            
            <?php if (empty($snippets)): ?>
            <div class="empty-state">No snippets yet</div>
            <?php else: ?>
            <div class="item-grid">
                <?php foreach (array_slice($snippets, 0, 3) as $snippet): ?>
                <a href="snippet-detail.php?id=<?php echo $snippet['id']; ?>" class="item-card">
                    <div class="item-header">
                        <span class="item-name"><?php echo htmlspecialchars($snippet['title']); ?></span>
                        <span class="item-badge <?php echo $snippet['visibility']; ?>">
                            <?php echo $snippet['visibility']; ?>
                        </span>
                    </div>
                    <div class="item-desc"><?php echo htmlspecialchars($snippet['filename']); ?></div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>📊 Recent Activity</h2>
                <a href="activity.php">View all →</a>
            </div>
            
            <?php if (empty($activities)): ?>
            <div class="empty-state">No recent activity</div>
            <?php else: ?>
            <ul class="activity-list">
                <?php foreach (array_slice($activities, 0, 5) as $activity): ?>
                <li class="activity-item">
                    <span class="activity-action">
                        <?php echo htmlspecialchars($activity['action']); ?> on 
                        <span class="activity-target"><?php echo htmlspecialchars($activity['target_title']); ?></span>
                    </span>
                    <span class="activity-time"><?php echo formatDate($activity['created_at']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="docs.php" class="btn">📚 Documentation</a>
            <a href="success.php" class="btn" style="margin-left: 1rem;">🏆 Submit Flag</a>
        </div>
    </main>
</body>
</html>
