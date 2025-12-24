<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$pollId = $_GET['id'] ?? 0;
$error = null;
$poll = null;
$options = [];
$canView = false;

if ($pollId) {
    // Get poll details
    $stmt = $pdo->prepare("
        SELECT s.*, u.username as creator_name, u.full_name as creator_full_name
        FROM slowvotes s
        JOIN users u ON s.creator_id = u.id
        WHERE s.id = ?
    ");
    $stmt->execute([$pollId]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$poll) {
        $error = "Poll not found";
    } else {
        // ✅ PROPER ACCESS CONTROL CHECK (UI does this correctly)
        if ($poll['visibility'] === 'everyone') {
            $canView = true;
        } elseif ($poll['creator_id'] == $userId) {
            $canView = true;
        } elseif ($poll['visibility'] === 'specific') {
            // Check if user has explicit permission
            $stmt = $pdo->prepare("SELECT can_view FROM poll_permissions WHERE poll_id = ? AND user_id = ?");
            $stmt->execute([$pollId, $userId]);
            $permission = $stmt->fetch();
            $canView = $permission && $permission['can_view'];
        } elseif ($poll['visibility'] === 'nobody') {
            // Only creator can view
            $canView = ($poll['creator_id'] == $userId);
        }
        
        if ($canView) {
            // Get poll options
            $stmt = $pdo->prepare("SELECT * FROM poll_options WHERE poll_id = ? ORDER BY id");
            $stmt->execute([$pollId]);
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $poll ? htmlspecialchars($poll['title']) : 'View Poll'; ?> - Phabricator</title>
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
        }
        .nav-links a:hover { color: #9370DB; }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .access-denied {
            background: rgba(255, 68, 68, 0.1);
            border: 2px solid rgba(255, 68, 68, 0.4);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
        }
        .access-denied .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .access-denied h1 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .access-denied p {
            color: #aaa;
            margin-bottom: 2rem;
        }
        .poll-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }
        .poll-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(106, 90, 205, 0.2);
        }
        .poll-title {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .poll-meta {
            color: #888;
            font-size: 0.9rem;
        }
        .visibility-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .visibility-badge.public { background: rgba(0,255,0,0.2); color: #00ff00; }
        .visibility-badge.specific { background: rgba(255,170,0,0.2); color: #ffaa00; }
        .visibility-badge.private { background: rgba(255,68,68,0.2); color: #ff6666; }
        .poll-description {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 2rem;
            white-space: pre-wrap;
        }
        .poll-options {
            display: grid;
            gap: 1rem;
        }
        .poll-option {
            background: rgba(106, 90, 205, 0.1);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .option-text { color: #e0e0e0; }
        .option-votes {
            background: #9370DB;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
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
        .hint-box {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.4);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .hint-box h3 {
            color: #ffaa00;
            margin-bottom: 0.5rem;
        }
        .hint-box p {
            color: #aaa;
            font-size: 0.9rem;
        }
        .hint-box code {
            background: rgba(0,0,0,0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
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
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="api-test.php">API Tester</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($error): ?>
            <div class="access-denied">
                <div class="icon">❌</div>
                <h1>Poll Not Found</h1>
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
            </div>
        <?php elseif (!$canView): ?>
            <div class="access-denied">
                <div class="icon">🔒</div>
                <h1>Access Denied</h1>
                <p>
                    You don't have permission to view poll <strong>V<?php echo $pollId; ?></strong>.<br>
                    This poll's visibility is set to "<?php echo ucfirst($poll['visibility']); ?>".
                </p>
                <p style="color: #666; font-size: 0.9rem;">
                    Only the poll creator or explicitly permitted users can view this content.
                </p>
                <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
                
                <div class="hint-box">
                    <h3>💡 Hint</h3>
                    <p>
                        The web UI properly checks your permissions. But what about the API?
                        Try using <code>POST /api/slowvote.php?action=info&poll_id=<?php echo $pollId; ?></code>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="poll-container">
                <div class="poll-header">
                    <h1 class="poll-title">
                        <?php echo htmlspecialchars($poll['title']); ?>
                        <span class="visibility-badge <?php 
                            echo $poll['visibility'] === 'everyone' ? 'public' : 
                                ($poll['visibility'] === 'specific' ? 'specific' : 'private'); 
                        ?>">
                            <?php echo ucfirst($poll['visibility']); ?>
                        </span>
                    </h1>
                    <p class="poll-meta">
                        Poll V<?php echo $poll['id']; ?> • 
                        Created by <?php echo htmlspecialchars($poll['creator_full_name']); ?> 
                        (@<?php echo htmlspecialchars($poll['creator_name']); ?>)
                    </p>
                </div>
                
                <div class="poll-description"><?php echo htmlspecialchars($poll['description']); ?></div>
                
                <h3 style="color: #9370DB; margin-bottom: 1rem;">Poll Options</h3>
                <div class="poll-options">
                    <?php foreach ($options as $option): ?>
                    <div class="poll-option">
                        <span class="option-text"><?php echo htmlspecialchars($option['option_text']); ?></span>
                        <span class="option-votes"><?php echo $option['vote_count']; ?> votes</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="actions">
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
