<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user's personal snippets
$stmt = $pdo->prepare("
    SELECT ps.*, u.username as author_name,
           (SELECT COUNT(*) FROM notes n WHERE n.noteable_type = 'personal_snippet' AND n.noteable_id = ps.id) as notes_count
    FROM personal_snippets ps
    JOIN users u ON ps.author_id = u.id
    WHERE ps.author_id = ?
    ORDER BY ps.updated_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$snippets = $stmt->fetchAll();

// Count by visibility
$publicCount = count(array_filter($snippets, fn($s) => $s['visibility'] === 'public'));
$privateCount = count(array_filter($snippets, fn($s) => $s['visibility'] === 'private'));
$internalCount = count(array_filter($snippets, fn($s) => $s['visibility'] === 'internal'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Snippets - Lab 25</title>
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
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

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #fc6d26;
        }

        .stat-card p {
            color: #808080;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .stat-card.private {
            border-color: rgba(255, 77, 77, 0.3);
        }

        .stat-card.private h3 {
            color: #ff6b6b;
        }

        .stat-card.public {
            border-color: rgba(76, 217, 100, 0.3);
        }

        .stat-card.public h3 {
            color: #4cd964;
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

        /* Snippets List */
        .snippets-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .snippet-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .snippet-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(252, 109, 38, 0.3);
            transform: translateX(5px);
        }

        .snippet-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .snippet-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .snippet-title h3 {
            font-size: 1.1rem;
            color: #fff;
            font-weight: 600;
        }

        .snippet-title h3 a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .snippet-title h3 a:hover {
            color: #fc6d26;
        }

        .snippet-id {
            color: #fc6d26;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .visibility-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .visibility-badge.private {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
        }

        .visibility-badge.public {
            background: rgba(76, 217, 100, 0.2);
            color: #4cd964;
        }

        .visibility-badge.internal {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .snippet-description {
            color: #808080;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .snippet-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
            font-size: 0.85rem;
        }

        .meta-item i {
            color: #fc6d26;
        }

        .notes-badge {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .notes-badge.has-notes {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
        }

        /* Warning Alert */
        .warning-alert {
            background: linear-gradient(135deg, rgba(255, 77, 77, 0.1), rgba(255, 77, 77, 0.05));
            border: 1px solid rgba(255, 77, 77, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .warning-alert h4 {
            color: #ff6b6b;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.75rem;
        }

        .warning-alert p {
            color: #b0b0b0;
            font-size: 0.9rem;
            line-height: 1.6;
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
                <a href="snippets.php" class="active"><i class="fas fa-code"></i> Snippets</a>
                <a href="activity.php"><i class="fas fa-history"></i> Activity</a>
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
                <div class="icon"><i class="fas fa-code"></i></div>
                <h1>My Personal Snippets</h1>
            </div>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <h3><?php echo count($snippets); ?></h3>
                <p>Total Snippets</p>
            </div>
            <div class="stat-card private">
                <h3><?php echo $privateCount; ?></h3>
                <p>Private Snippets</p>
            </div>
            <div class="stat-card public">
                <h3><?php echo $publicCount; ?></h3>
                <p>Public Snippets</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $internalCount; ?></h3>
                <p>Internal Snippets</p>
            </div>
        </div>

        <?php if ($_SESSION['username'] === 'victim'): ?>
        <!-- Warning for Victim User -->
        <div class="warning-alert">
            <h4><i class="fas fa-exclamation-triangle"></i> Security Alert</h4>
            <p>
                Your private snippets may be vulnerable! Due to a bug in the notes system, 
                attackers might be able to add notes to your private snippets without authorization. 
                Check your snippets for any unexpected notes or comments.
            </p>
        </div>
        <?php endif; ?>

        <!-- Info Banner -->
        <div class="info-banner">
            <i class="fas fa-shield-alt"></i>
            <div>
                <h4>Personal Snippets</h4>
                <p>
                    Personal snippets are code blocks or text that only you should be able to access (when private).
                    Private snippets should not be visible or modifiable by other users.
                    <?php if ($_SESSION['username'] === 'victim'): ?>
                    <strong>Note: If you see notes on your private snippets from users other than yourself, the system has been compromised!</strong>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Snippets List -->
        <?php if (empty($snippets)): ?>
        <div class="empty-state">
            <i class="fas fa-code"></i>
            <h3>No Snippets Yet</h3>
            <p>You haven't created any personal snippets.</p>
        </div>
        <?php else: ?>
        <div class="snippets-list">
            <?php foreach ($snippets as $snippet): ?>
            <div class="snippet-card">
                <div class="snippet-header">
                    <div class="snippet-title">
                        <span class="snippet-id">#<?php echo $snippet['id']; ?></span>
                        <h3><a href="snippet-detail.php?id=<?php echo $snippet['id']; ?>"><?php echo htmlspecialchars($snippet['title']); ?></a></h3>
                    </div>
                    <span class="visibility-badge <?php echo $snippet['visibility']; ?>">
                        <i class="fas fa-<?php echo $snippet['visibility'] === 'private' ? 'lock' : ($snippet['visibility'] === 'public' ? 'globe' : 'users'); ?>"></i>
                        <?php echo ucfirst($snippet['visibility']); ?>
                    </span>
                </div>
                <?php if ($snippet['description']): ?>
                <p class="snippet-description"><?php echo htmlspecialchars($snippet['description']); ?></p>
                <?php endif; ?>
                <div class="snippet-meta">
                    <span class="meta-item">
                        <i class="fas fa-file-code"></i>
                        <?php echo htmlspecialchars($snippet['file_name']); ?>
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-clock"></i>
                        <?php echo date('M j, Y', strtotime($snippet['created_at'])); ?>
                    </span>
                    <span class="notes-badge <?php echo $snippet['notes_count'] > 0 ? 'has-notes' : ''; ?>">
                        <i class="fas fa-comment"></i>
                        <?php echo $snippet['notes_count']; ?> note<?php echo $snippet['notes_count'] !== 1 ? 's' : ''; ?>
                        <?php if ($snippet['notes_count'] > 0 && $snippet['visibility'] === 'private'): ?>
                        <i class="fas fa-exclamation-triangle" title="Notes on private snippet - check for unauthorized access!"></i>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
