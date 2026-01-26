<?php
require_once 'config.php';
requireLogin();

$projectId = $_GET['id'] ?? 0;

// Get project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$projectId]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: projects.php');
    exit();
}

// Check access (owner only for private)
if ($project['visibility'] === 'private' && $project['owner_id'] != $_SESSION['user_id']) {
    header('Location: projects.php');
    exit();
}

// Get issues
$stmt = $pdo->prepare("
    SELECT i.*, u.username as author_name,
           (SELECT COUNT(*) FROM notes WHERE noteable_type = 'issue' AND noteable_id = i.id) as note_count
    FROM issues i
    JOIN users u ON i.author_id = u.id
    WHERE i.project_id = ?
    ORDER BY i.created_at DESC
");
$stmt->execute([$projectId]);
$issues = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['name']); ?> - SnippetHub</title>
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
        .breadcrumb {
            margin-bottom: 1.5rem;
            color: #888;
        }
        .breadcrumb a { color: #fc6d26; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .project-header {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .project-title {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .project-title h1 {
            font-size: 2rem;
            color: #fc6d26;
        }
        .project-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
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
            font-family: 'Consolas', monospace;
            color: #888;
            margin-bottom: 0.75rem;
        }
        .project-desc { color: #aaa; line-height: 1.6; }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .section-header h2 {
            color: #fc6d26;
            font-size: 1.5rem;
        }
        .issue-list { list-style: none; }
        .issue-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .issue-item:hover {
            border-color: #fc6d26;
            transform: translateX(5px);
        }
        .issue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .issue-title {
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
        }
        .issue-status {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            text-transform: uppercase;
        }
        .issue-status.open {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .issue-status.closed {
            background: rgba(136, 136, 136, 0.2);
            color: #888;
        }
        .issue-meta {
            color: #888;
            font-size: 0.85rem;
        }
        .issue-meta span {
            margin-right: 1rem;
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
        .empty-state {
            text-align: center;
            padding: 3rem;
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
                <a href="dashboard.php">Dashboard</a>
                <a href="projects.php">Projects</a>
                <a href="snippets.php">Snippets</a>
                <a href="activity.php">Activity</a>
                <a href="success.php">Submit Flag</a>
                <div class="user-badge">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <a href="logout.php" style="color: #ff6666;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="breadcrumb">
            <a href="projects.php">Projects</a> / <?php echo htmlspecialchars($project['name']); ?>
        </div>

        <div class="project-header">
            <div class="project-title">
                <h1>📁 <?php echo htmlspecialchars($project['name']); ?></h1>
                <span class="project-badge <?php echo $project['visibility']; ?>">
                    <?php echo $project['visibility']; ?>
                </span>
            </div>
            <div class="project-path"><?php echo htmlspecialchars($project['path']); ?></div>
            <div class="project-desc">
                <?php echo htmlspecialchars($project['description'] ?? 'No description'); ?>
            </div>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 Next Step</h3>
            <p>
                Click on an issue below. Then add a note/comment and intercept the request to exploit the vulnerability.
            </p>
        </div>
        <?php endif; ?>

        <div class="section-header">
            <h2>📋 Issues</h2>
        </div>

        <?php if (empty($issues)): ?>
        <div class="empty-state">
            <p>No issues in this project</p>
        </div>
        <?php else: ?>
        <ul class="issue-list">
            <?php foreach ($issues as $issue): ?>
            <a href="issue-detail.php?id=<?php echo $issue['id']; ?>" class="issue-item">
                <div class="issue-header">
                    <span class="issue-title">#<?php echo $issue['id']; ?> - <?php echo htmlspecialchars($issue['title']); ?></span>
                    <span class="issue-status <?php echo $issue['status']; ?>"><?php echo $issue['status']; ?></span>
                </div>
                <div class="issue-meta">
                    <span>👤 <?php echo htmlspecialchars($issue['author_name']); ?></span>
                    <span>💬 <?php echo $issue['note_count']; ?> notes</span>
                    <span>📅 <?php echo date('M j, Y', strtotime($issue['created_at'])); ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </main>
</body>
</html>
