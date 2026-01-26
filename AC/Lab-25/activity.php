<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user's activity - THIS IS WHERE THE INFORMATION LEAK IS VISIBLE!
$stmt = $pdo->prepare("
    SELECT al.*, u.username as actor_name
    FROM activity_log al
    JOIN users u ON al.user_id = u.id
    WHERE al.user_id = ?
    ORDER BY al.created_at DESC
    LIMIT 100
");
$stmt->execute([$_SESSION['user_id']]);
$activities = $stmt->fetchAll();

// Check if any activity shows personal_snippet targets (attack evidence)
$hasSnippetActivity = false;
foreach ($activities as $activity) {
    if ($activity['target_type'] === 'personal_snippet') {
        $hasSnippetActivity = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity - Lab 25</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }

        /* Navigation */
        .navbar {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fc6d26;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .nav-brand svg {
            width: 32px;
            height: 32px;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            color: #b0b0b0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #fc6d26;
        }

        .user-badge {
            background: linear-gradient(135deg, rgba(252, 109, 38, 0.2), rgba(252, 109, 38, 0.1));
            border: 1px solid rgba(252, 109, 38, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            color: #fc6d26;
            font-weight: 600;
        }

        /* Main Container */
        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
        }

        .page-title .icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Success Banner */
        .success-banner {
            background: linear-gradient(135deg, rgba(76, 217, 100, 0.15), rgba(76, 217, 100, 0.05));
            border: 2px solid rgba(76, 217, 100, 0.5);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .success-banner h3 {
            color: #4cd964;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }

        .success-banner p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        .success-banner .highlight {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            border-left: 4px solid #4cd964;
        }

        .success-banner .highlight code {
            color: #4cd964;
            font-family: 'Fira Code', monospace;
            font-weight: 600;
        }

        .success-banner a {
            color: #fc6d26;
            text-decoration: none;
            font-weight: 600;
        }

        .success-banner a:hover {
            text-decoration: underline;
        }

        /* Info Banner */
        .info-banner {
            background: linear-gradient(135deg, rgba(252, 109, 38, 0.1), rgba(252, 109, 38, 0.05));
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .info-banner i {
            color: #fc6d26;
            font-size: 1.5rem;
        }

        .info-banner h4 {
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }

        .info-banner p {
            color: #b0b0b0;
            font-size: 0.9rem;
        }

        /* Activity Timeline */
        .activity-timeline {
            position: relative;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(252, 109, 38, 0.2);
        }

        .activity-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 1.5rem;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: 13px;
            top: 8px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fc6d26;
            border: 3px solid #1a1a2e;
        }

        .activity-item.snippet-leak::before {
            background: #ff6b6b;
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
        }

        .activity-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s;
        }

        .activity-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(252, 109, 38, 0.3);
        }

        .activity-item.snippet-leak .activity-card {
            border-color: rgba(255, 107, 107, 0.5);
            background: rgba(255, 107, 107, 0.05);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .activity-action {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .action-icon.note {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
        }

        .action-icon.snippet-leak {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }

        .action-text {
            font-weight: 600;
            color: #fff;
        }

        .activity-time {
            color: #666;
            font-size: 0.85rem;
        }

        .activity-details {
            color: #b0b0b0;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .target-info {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-top: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .target-row {
            display: flex;
            gap: 1rem;
        }

        .target-label {
            color: #808080;
            min-width: 80px;
            font-size: 0.85rem;
        }

        .target-value {
            color: #e0e0e0;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
        }

        .target-value.leaked {
            color: #ff6b6b;
            font-weight: 600;
            background: rgba(255, 107, 107, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .leak-badge {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #404040;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #808080;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #606060;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <svg viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 0L0 9.14v13.72L16 32l16-9.14V9.14L16 0zm0 4.57l10.29 5.86L16 16.29 5.71 10.43 16 4.57zM3.43 12.57l11.14 6.29v9.71L3.43 22.29v-9.72zm15.14 16v-9.71l11.14-6.29v9.72l-11.14 6.28z"/>
                </svg>
                Lab 25 - Notes IDOR
            </a>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="projects.php"><i class="fas fa-folder"></i> Projects</a>
                <a href="snippets.php"><i class="fas fa-code"></i> Snippets</a>
                <a href="activity.php" class="active"><i class="fas fa-history"></i> Activity</a>
                <div class="user-badge">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="page-header">
            <div class="page-title">
                <div class="icon"><i class="fas fa-history"></i></div>
                <h1>My Activity</h1>
            </div>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($hasSnippetActivity && $_SESSION['username'] === 'attacker'): ?>
        <!-- Success Banner - Shows leaked snippet titles -->
        <div class="success-banner">
            <h3><i class="fas fa-flag"></i> Information Leak Detected!</h3>
            <p>
                Your activity log reveals the <strong>PRIVATE snippet titles</strong> from your notes attack!
                This is the information leak component of the vulnerability - even though you can't directly view 
                the private snippets, the activity log exposes their titles.
            </p>
            <div class="highlight">
                <strong>Look for entries with target type <code>personal_snippet</code></strong> - 
                the "Target Title" field contains the leaked private information!
                <br><br>
                <a href="success.php"><i class="fas fa-trophy"></i> Submit the leaked snippet title to complete the lab!</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Info Banner -->
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <div>
                <h4>Activity Feed</h4>
                <p>
                    This page shows your recent activity including notes you've created.
                    <?php if ($_SESSION['username'] === 'attacker'): ?>
                    <strong>Notice:</strong> When you create a note on someone's private snippet via IDOR, 
                    the snippet's title gets logged here - leaking private information!
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Activity Timeline -->
        <?php if (empty($activities)): ?>
        <div class="empty-state">
            <i class="fas fa-clock"></i>
            <h3>No Activity Yet</h3>
            <p>Your activity will appear here once you start interacting with the system.</p>
        </div>
        <?php else: ?>
        <div class="activity-timeline">
            <?php foreach ($activities as $activity): 
                $isSnippetLeak = $activity['target_type'] === 'personal_snippet';
            ?>
            <div class="activity-item <?php echo $isSnippetLeak ? 'snippet-leak' : ''; ?>">
                <div class="activity-card">
                    <div class="activity-header">
                        <div class="activity-action">
                            <span class="action-icon <?php echo $isSnippetLeak ? 'snippet-leak' : 'note'; ?>">
                                <i class="fas fa-<?php echo $isSnippetLeak ? 'exclamation-triangle' : 'comment'; ?>"></i>
                            </span>
                            <span class="action-text">
                                <?php 
                                $actionLabels = [
                                    'created_note' => 'Created Note',
                                    'updated_note' => 'Updated Note',
                                    'deleted_note' => 'Deleted Note'
                                ];
                                echo $actionLabels[$activity['action']] ?? ucwords(str_replace('_', ' ', $activity['action']));
                                ?>
                            </span>
                            <?php if ($isSnippetLeak): ?>
                            <span class="leak-badge">
                                <i class="fas fa-key"></i> PRIVATE INFO LEAKED
                            </span>
                            <?php endif; ?>
                        </div>
                        <span class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></span>
                    </div>
                    <p class="activity-details"><?php echo htmlspecialchars($activity['details']); ?></p>
                    <div class="target-info">
                        <div class="target-row">
                            <span class="target-label">Target Type:</span>
                            <span class="target-value"><?php echo htmlspecialchars($activity['target_type']); ?></span>
                        </div>
                        <div class="target-row">
                            <span class="target-label">Target ID:</span>
                            <span class="target-value">#<?php echo htmlspecialchars($activity['target_id']); ?></span>
                        </div>
                        <div class="target-row">
                            <span class="target-label">Target Title:</span>
                            <span class="target-value <?php echo $isSnippetLeak ? 'leaked' : ''; ?>">
                                <?php echo htmlspecialchars($activity['target_title']); ?>
                                <?php if ($isSnippetLeak): ?>
                                <i class="fas fa-eye" title="This title should be private!"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
