<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get all public polls
$stmt = $pdo->prepare("
    SELECT s.*, u.username as creator_name, u.full_name as creator_full_name,
           (SELECT COUNT(*) FROM poll_votes WHERE poll_id = s.id) as total_votes
    FROM slowvotes s
    JOIN users u ON s.creator_id = u.id
    WHERE s.visibility = 'everyone'
    ORDER BY s.created_at DESC
");
$stmt->execute();
$publicPolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get polls user has explicit permission to view (for specific visibility)
$stmt = $pdo->prepare("
    SELECT s.*, u.username as creator_name, u.full_name as creator_full_name,
           (SELECT COUNT(*) FROM poll_votes WHERE poll_id = s.id) as total_votes
    FROM slowvotes s
    JOIN users u ON s.creator_id = u.id
    JOIN poll_permissions pp ON s.id = pp.poll_id
    WHERE s.visibility = 'specific' AND pp.user_id = ? AND pp.can_view = TRUE
    ORDER BY s.created_at DESC
");
$stmt->execute([$userId]);
$permittedPolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's own polls (including private ones)
$stmt = $pdo->prepare("
    SELECT s.*, u.username as creator_name, u.full_name as creator_full_name,
           (SELECT COUNT(*) FROM poll_votes WHERE poll_id = s.id) as total_votes
    FROM slowvotes s
    JOIN users u ON s.creator_id = u.id
    WHERE s.creator_id = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$userId]);
$myPolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total hidden polls (for demonstration)
$stmt = $pdo->query("SELECT COUNT(*) FROM slowvotes WHERE visibility != 'everyone'");
$hiddenCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Phabricator Slowvote</title>
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
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
            padding: 1rem 2rem;
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
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #9370DB; }
        .user-info {
            color: #888;
            font-size: 0.9rem;
        }
        .user-info strong { color: #9370DB; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #9370DB;
            font-size: 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-2px); }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #9370DB;
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
            color: #9370DB;
            font-size: 1.3rem;
        }
        .polls-list {
            display: grid;
            gap: 1rem;
        }
        .poll-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .poll-card:hover {
            border-color: #9370DB;
            transform: translateY(-2px);
        }
        .poll-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        .poll-title {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .poll-title a {
            color: inherit;
            text-decoration: none;
        }
        .poll-title a:hover { color: #9370DB; }
        .visibility-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .visibility-badge.public {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
        }
        .visibility-badge.specific {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .visibility-badge.private {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
        }
        .poll-meta {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .poll-description {
            color: #aaa;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .poll-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(106, 90, 205, 0.2);
        }
        .poll-stat {
            color: #888;
            font-size: 0.85rem;
        }
        .poll-stat strong { color: #9370DB; }
        .api-hint {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .api-hint h3 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .api-hint p {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .api-hint code {
            display: block;
            background: #0d0d0d;
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            color: #88ff88;
            font-size: 0.85rem;
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
                <span class="logo-icon">P</span>
                Phabricator
            </a>
            <nav class="nav-links">
                <span class="user-info">Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="../index.php">← All Labs</a>
                <a href="create-poll.php">Create Poll</a>
                <a href="api-test.php">API Tester</a>
                <a href="docs.php">Docs</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>🗳️ Slowvote Dashboard</h1>
            <a href="create-poll.php" class="btn btn-primary">+ Create New Poll</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="value"><?php echo count($publicPolls); ?></div>
                <div class="label">Public Polls</div>
            </div>
            <div class="stat-card">
                <div class="icon">🔒</div>
                <div class="value"><?php echo $hiddenCount; ?></div>
                <div class="label">Hidden Polls</div>
            </div>
            <div class="stat-card">
                <div class="icon">📝</div>
                <div class="value"><?php echo count($myPolls); ?></div>
                <div class="label">My Polls</div>
            </div>
            <div class="stat-card">
                <div class="icon">🔓</div>
                <div class="value"><?php echo count($permittedPolls); ?></div>
                <div class="label">Shared With Me</div>
            </div>
        </div>

        <!-- Public Polls -->
        <div class="section">
            <div class="section-header">
                <h2>📊 Public Polls</h2>
            </div>
            <div class="polls-list">
                <?php if (empty($publicPolls)): ?>
                    <div class="empty-state">No public polls available</div>
                <?php else: ?>
                    <?php foreach ($publicPolls as $poll): ?>
                    <div class="poll-card">
                        <div class="poll-header">
                            <h3 class="poll-title">
                                <a href="view-poll.php?id=<?php echo $poll['id']; ?>">
                                    <?php echo htmlspecialchars($poll['title']); ?>
                                </a>
                            </h3>
                            <span class="visibility-badge public">Public</span>
                        </div>
                        <div class="poll-meta">
                            Created by <?php echo htmlspecialchars($poll['creator_full_name']); ?> 
                            (@<?php echo htmlspecialchars($poll['creator_name']); ?>)
                        </div>
                        <p class="poll-description">
                            <?php echo htmlspecialchars(substr($poll['description'], 0, 150)); ?>
                            <?php if (strlen($poll['description']) > 150) echo '...'; ?>
                        </p>
                        <div class="poll-stats">
                            <span class="poll-stat"><strong><?php echo $poll['total_votes']; ?></strong> votes</span>
                            <span class="poll-stat">Poll #V<?php echo $poll['id']; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Polls Shared With Me -->
        <?php if (!empty($permittedPolls)): ?>
        <div class="section">
            <div class="section-header">
                <h2>🔓 Shared With Me</h2>
            </div>
            <div class="polls-list">
                <?php foreach ($permittedPolls as $poll): ?>
                <div class="poll-card">
                    <div class="poll-header">
                        <h3 class="poll-title">
                            <a href="view-poll.php?id=<?php echo $poll['id']; ?>">
                                <?php echo htmlspecialchars($poll['title']); ?>
                            </a>
                        </h3>
                        <span class="visibility-badge specific">Restricted</span>
                    </div>
                    <div class="poll-meta">
                        Created by <?php echo htmlspecialchars($poll['creator_full_name']); ?>
                    </div>
                    <p class="poll-description">
                        <?php echo htmlspecialchars(substr($poll['description'], 0, 150)); ?>...
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- My Polls -->
        <?php if (!empty($myPolls)): ?>
        <div class="section">
            <div class="section-header">
                <h2>📝 My Polls</h2>
            </div>
            <div class="polls-list">
                <?php foreach ($myPolls as $poll): ?>
                <div class="poll-card">
                    <div class="poll-header">
                        <h3 class="poll-title">
                            <a href="view-poll.php?id=<?php echo $poll['id']; ?>">
                                <?php echo htmlspecialchars($poll['title']); ?>
                            </a>
                        </h3>
                        <span class="visibility-badge <?php 
                            echo $poll['visibility'] === 'everyone' ? 'public' : 
                                ($poll['visibility'] === 'specific' ? 'specific' : 'private'); 
                        ?>">
                            <?php echo ucfirst($poll['visibility']); ?>
                        </span>
                    </div>
                    <p class="poll-description">
                        <?php echo htmlspecialchars(substr($poll['description'], 0, 100)); ?>...
                    </p>
                    <div class="poll-stats">
                        <span class="poll-stat"><strong><?php echo $poll['total_votes']; ?></strong> votes</span>
                        <span class="poll-stat">Poll #V<?php echo $poll['id']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- API Hint -->
        <div class="api-hint">
            <h3>🔍 Developer Note: API Endpoint Available</h3>
            <p>
                The Slowvote API is available for programmatic access. Try the endpoint:
            </p>
            <code>POST /api/slowvote.php?action=info&poll_id=1</code>
            <p style="margin-top: 1rem;">
                <a href="api-test.php" class="btn btn-secondary" style="display: inline-block;">
                    Open API Tester →
                </a>
            </p>
        </div>
    </div>
</body>
</html>
