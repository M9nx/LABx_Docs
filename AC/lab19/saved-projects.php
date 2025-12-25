<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['lab19_user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['lab19_user_id'];
$display_name = $_SESSION['lab19_display_name'];
$avatar_color = $_SESSION['lab19_avatar_color'];
$labSolved = isLabSolved(19);

// Handle delete via GET (VULNERABLE!)
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['saved_id'])) {
    // Redirect to API endpoint
    header('Location: api/delete_saved.php?saved_id=' . intval($_GET['saved_id']));
    exit;
}

// Check for success message from API
if (isset($_GET['deleted'])) {
    $message = "Saved project deleted successfully!";
    $messageType = "success";
    if (isset($_GET['lab_solved'])) {
        $message = "🎉 IDOR Exploited! You deleted another user's saved project!";
        $messageType = "solved";
        $labSolved = true;
    }
}

// Get user's saved projects with project details
$stmt = $pdo->prepare("
    SELECT sp.id as saved_id, sp.notes, sp.saved_at, 
           p.id as project_id, p.title, p.category, p.likes_count,
           u.display_name as author_name, u.avatar_color as author_color
    FROM saved_projects sp
    JOIN projects p ON sp.project_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE sp.user_id = ?
    ORDER BY sp.saved_at DESC
");
$stmt->execute([$user_id]);
$saved_projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Projects - ProjectHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #a5b4fc; text-decoration: none; }
        .nav-links a:hover { color: #c7d2fe; }
        .nav-links a.active { color: #818cf8; font-weight: 600; }
        .user-menu { display: flex; align-items: center; gap: 0.75rem; }
        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 { color: #e0e0e0; font-size: 1.75rem; margin-bottom: 0.5rem; }
        .page-header p { color: #64748b; }
        .message {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .message.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        .message.solved {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }
        .hint-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
        }
        .hint-box h3 { color: #a5b4fc; margin-bottom: 0.75rem; }
        .hint-box p { color: #94a3b8; font-size: 0.9rem; line-height: 1.6; }
        .hint-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #fcd34d;
        }
        .vulnerable-url {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            margin-top: 0.75rem;
            overflow-x: auto;
        }
        .vulnerable-url .method { color: #22c55e; }
        .vulnerable-url .url { color: #64748b; }
        .vulnerable-url .param { color: #ef4444; }
        .saved-list { list-style: none; }
        .saved-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        .saved-item:hover {
            border-color: rgba(99, 102, 241, 0.3);
        }
        .saved-info { flex: 1; }
        .saved-info h4 { color: #e0e0e0; margin-bottom: 0.5rem; }
        .saved-info .meta {
            display: flex;
            gap: 1rem;
            color: #64748b;
            font-size: 0.85rem;
        }
        .saved-info .notes {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            font-style: italic;
        }
        .saved-id {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            margin-right: 1rem;
        }
        .btn-delete {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.5);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .target-ids-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .target-ids-box h4 { color: #fcd34d; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .target-ids {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .target-id {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="saved-projects.php" class="active">Saved Projects</a>
                <a href="docs.php">Docs</a>
                <a href="../index.php">All Labs</a>
            </nav>
            <div class="user-menu">
                <span style="color: #94a3b8;"><?php echo htmlspecialchars($display_name); ?></span>
                <div class="avatar" style="background: <?php echo htmlspecialchars($avatar_color); ?>">
                    <?php echo strtoupper(substr($display_name, 0, 1)); ?>
                </div>
                <a href="logout.php" style="color: #ef4444; text-decoration: none; margin-left: 0.5rem;">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>📑 Your Saved Projects</h1>
            <p>Manage your bookmarked projects. Click delete to remove from your collection.</p>
        </div>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="hint-box">
            <h3>🔓 Vulnerability Hint</h3>
            <p>
                Notice the <code>saved_id</code> parameter in the delete URL. The server doesn't verify 
                if you own this saved project before deleting it!
            </p>
            <div class="vulnerable-url">
                <span class="method">GET</span> <span class="url">/api/delete_saved.php?</span><span class="param">saved_id={YOUR_ID}</span>
            </div>
            <div class="target-ids-box">
                <h4>🎯 Victim's Saved Project IDs (victim_designer):</h4>
                <div class="target-ids">
                    <span class="target-id">101</span>
                    <span class="target-id">102</span>
                    <span class="target-id">103</span>
                    <span class="target-id">104</span>
                    <span class="target-id">105</span>
                </div>
            </div>
        </div>

        <?php if (empty($saved_projects)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <h3>No Saved Projects</h3>
            <p>You haven't saved any projects yet.</p>
        </div>
        <?php else: ?>
        <ul class="saved-list">
            <?php foreach ($saved_projects as $saved): ?>
            <li class="saved-item">
                <div class="saved-info">
                    <h4><?php echo htmlspecialchars($saved['title']); ?></h4>
                    <div class="meta">
                        <span>📁 <?php echo htmlspecialchars($saved['category']); ?></span>
                        <span>👤 <?php echo htmlspecialchars($saved['author_name']); ?></span>
                        <span>❤️ <?php echo $saved['likes_count']; ?></span>
                    </div>
                    <?php if ($saved['notes']): ?>
                    <div class="notes">"<?php echo htmlspecialchars($saved['notes']); ?>"</div>
                    <?php endif; ?>
                </div>
                <span class="saved-id">ID: <?php echo $saved['saved_id']; ?></span>
                <a href="api/delete_saved.php?saved_id=<?php echo $saved['saved_id']; ?>" 
                   class="btn-delete"
                   onclick="return confirm('Delete this saved project?');">
                    🗑️ Delete
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <script>
        // Check for API response and redirect back with message
        const urlParams = new URLSearchParams(window.location.search);
        if (window.location.pathname.includes('delete_saved.php')) {
            // Handle API response - this is just for direct API access
        }
    </script>
</body>
</html>
