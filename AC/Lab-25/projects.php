<?php
require_once 'config.php';
requireLogin();

// Get user's projects
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM issues WHERE project_id = p.id) as issue_count
    FROM projects p 
    WHERE owner_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - SnippetHub</title>
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
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .project-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .project-card:hover {
            border-color: #fc6d26;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .project-name {
            font-weight: 600;
            color: #fff;
            font-size: 1.2rem;
        }
        .project-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
            border-radius: 10px;
            text-transform: uppercase;
        }
        .project-badge.private {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .project-badge.public {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .project-path {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            font-family: 'Consolas', monospace;
        }
        .project-desc {
            color: #aaa;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        .project-stats {
            display: flex;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #666;
            font-size: 0.85rem;
        }
        .project-stats span { display: flex; align-items: center; gap: 0.3rem; }
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
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
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
                SnippetHub
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="projects.php" style="color: #fc6d26;">Projects</a>
                <a href="snippets.php">Snippets</a>
                <a href="activity.php">Activity</a>
                <a href="success.php">Submit Flag</a>
                <div class="user-badge">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <a href="logout.php" style="color: #ff6666;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h1>📁 Your Projects</h1>
            <p>Manage your projects and issues</p>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 Attack Instructions</h3>
            <p>
                Click on a project, then open an issue. In the issue, try adding a <strong>note/comment</strong>.
                Intercept the POST request to <code>/api/notes.php</code> and change:
            </p>
            <p style="margin-top: 0.5rem;">
                • <code>"noteable_type": "issue"</code> → <code>"noteable_type": "personal_snippet"</code><br>
                • <code>"noteable_id": X</code> → <code>"noteable_id": 1</code> (victim's private snippet)
            </p>
        </div>
        <?php endif; ?>

        <?php if (empty($projects)): ?>
        <div class="empty-state">
            <div class="icon">📁</div>
            <p>You don't have any projects yet</p>
        </div>
        <?php else: ?>
        <div class="project-grid">
            <?php foreach ($projects as $project): ?>
            <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="project-card">
                <div class="project-header">
                    <span class="project-name"><?php echo htmlspecialchars($project['name']); ?></span>
                    <span class="project-badge <?php echo $project['visibility']; ?>">
                        <?php echo $project['visibility']; ?>
                    </span>
                </div>
                <div class="project-path"><?php echo htmlspecialchars($project['path']); ?></div>
                <div class="project-desc">
                    <?php echo htmlspecialchars($project['description'] ?? 'No description'); ?>
                </div>
                <div class="project-stats">
                    <span>📋 <?php echo $project['issue_count']; ?> issues</span>
                    <span>📅 <?php echo date('M j, Y', strtotime($project['created_at'])); ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
