<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$snippetId = intval($_GET['id'] ?? 0);

if (!$snippetId) {
    header('Location: snippets.php');
    exit;
}

// Get snippet details
$stmt = $pdo->prepare("
    SELECT ps.*, u.username as author_name
    FROM personal_snippets ps
    JOIN users u ON ps.author_id = u.id
    WHERE ps.id = ?
");
$stmt->execute([$snippetId]);
$snippet = $stmt->fetch();

if (!$snippet) {
    header('Location: snippets.php');
    exit;
}

// Check access - only owner can view private snippets (CORRECT behavior)
$canView = false;
if ($snippet['visibility'] === 'public') {
    $canView = true;
} elseif ($snippet['visibility'] === 'internal' && isLoggedIn()) {
    $canView = true;
} elseif ($snippet['author_id'] == $_SESSION['user_id']) {
    $canView = true;
}

if (!$canView) {
    // Access denied - but notes could still be created via IDOR!
    header('Location: snippets.php?error=access_denied');
    exit;
}

// Get notes on this snippet (this is where the attack evidence shows!)
$stmt = $pdo->prepare("
    SELECT n.*, u.username as author_name
    FROM notes n
    JOIN users u ON n.author_id = u.id
    WHERE n.noteable_type = 'personal_snippet' AND n.noteable_id = ?
    ORDER BY n.created_at DESC
");
$stmt->execute([$snippetId]);
$notes = $stmt->fetchAll();

$isOwner = $snippet['author_id'] == $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($snippet['title']); ?> - Lab 25</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
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

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: #808080;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #fc6d26;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Snippet Header */
        .snippet-header {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .snippet-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .snippet-title-area h1 {
            font-size: 1.75rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .snippet-id {
            color: #fc6d26;
            font-size: 1rem;
        }

        .visibility-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .visibility-badge.private {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .visibility-badge.public {
            background: rgba(76, 217, 100, 0.2);
            color: #4cd964;
            border: 1px solid rgba(76, 217, 100, 0.3);
        }

        .visibility-badge.internal {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .snippet-description {
            color: #b0b0b0;
            margin-bottom: 1rem;
        }

        .snippet-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #808080;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: #fc6d26;
        }

        /* Code Block */
        .code-block {
            background: #0d1117;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .code-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .code-header .file-name {
            color: #fc6d26;
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
        }

        .code-content {
            padding: 1.5rem;
            overflow-x: auto;
        }

        .code-content pre {
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            color: #e0e0e0;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Notes Section */
        .notes-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .notes-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notes-header h2 {
            font-size: 1.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notes-count {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .notes-list {
            padding: 1.5rem;
        }

        .note-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .note-item:last-child {
            margin-bottom: 0;
        }

        .note-item.unauthorized {
            border-color: rgba(255, 77, 77, 0.5);
            background: rgba(255, 77, 77, 0.05);
        }

        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .note-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .note-author-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .note-author-avatar.unauthorized {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
        }

        .note-author-name {
            font-weight: 600;
            color: #fff;
        }

        .note-author-name.unauthorized {
            color: #ff6b6b;
        }

        .unauthorized-badge {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .note-time {
            color: #666;
            font-size: 0.85rem;
        }

        .note-content {
            color: #c0c0c0;
            line-height: 1.6;
        }

        .note-content.attack-note {
            background: rgba(255, 77, 77, 0.1);
            border: 1px dashed rgba(255, 77, 77, 0.3);
            padding: 1rem;
            border-radius: 6px;
            color: #ff9999;
        }

        /* Empty Notes */
        .empty-notes {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .empty-notes i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Alert Box */
        .alert-box {
            background: linear-gradient(135deg, rgba(255, 77, 77, 0.1), rgba(255, 77, 77, 0.05));
            border: 1px solid rgba(255, 77, 77, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .alert-box h3 {
            color: #ff6b6b;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.75rem;
        }

        .alert-box p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        /* Button */
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

        /* Success Evidence Box */
        .evidence-box {
            background: linear-gradient(135deg, rgba(76, 217, 100, 0.1), rgba(76, 217, 100, 0.05));
            border: 1px solid rgba(76, 217, 100, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .evidence-box h3 {
            color: #4cd964;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.75rem;
        }

        .evidence-box p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        .evidence-box a {
            color: #fc6d26;
            text-decoration: none;
        }

        .evidence-box a:hover {
            text-decoration: underline;
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
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a>
            <i class="fas fa-chevron-right"></i>
            <a href="snippets.php">Snippets</a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($snippet['title']); ?></span>
        </div>

        <?php 
        // Check for unauthorized notes
        $hasUnauthorizedNotes = false;
        foreach ($notes as $note) {
            if ($note['author_id'] != $snippet['author_id']) {
                $hasUnauthorizedNotes = true;
                break;
            }
        }
        ?>

        <?php if ($hasUnauthorizedNotes && $isOwner): ?>
        <!-- Security Alert -->
        <div class="alert-box">
            <h3><i class="fas fa-exclamation-triangle"></i> Security Breach Detected!</h3>
            <p>
                <strong>WARNING:</strong> This private snippet has notes from users other than yourself!
                This indicates that someone has exploited the Notes IDOR vulnerability to access your private content.
                Check the notes below - unauthorized notes are highlighted in red.
            </p>
        </div>
        <?php endif; ?>

        <?php if ($hasUnauthorizedNotes && $_SESSION['username'] === 'attacker'): ?>
        <!-- Success Evidence for Attacker -->
        <div class="evidence-box">
            <h3><i class="fas fa-check-circle"></i> Attack Successful!</h3>
            <p>
                You have successfully exploited the Notes IDOR vulnerability! Your note appears on victim's private snippet.
                Now check your <a href="activity.php">Activity Feed</a> to see the leaked snippet title.
                Submit the snippet title on the <a href="success.php">Success Page</a> to complete the lab.
            </p>
        </div>
        <?php endif; ?>

        <!-- Snippet Header -->
        <div class="snippet-header">
            <div class="snippet-top">
                <div class="snippet-title-area">
                    <h1>
                        <span class="snippet-id">#<?php echo $snippet['id']; ?></span>
                        <?php echo htmlspecialchars($snippet['title']); ?>
                    </h1>
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
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($snippet['author_name']); ?>
                </span>
                <span class="meta-item">
                    <i class="fas fa-file-code"></i>
                    <?php echo htmlspecialchars($snippet['file_name']); ?>
                </span>
                <span class="meta-item">
                    <i class="fas fa-calendar"></i>
                    Created <?php echo date('M j, Y', strtotime($snippet['created_at'])); ?>
                </span>
            </div>
        </div>

        <!-- Code Block -->
        <div class="code-block">
            <div class="code-header">
                <span class="file-name"><?php echo htmlspecialchars($snippet['file_name']); ?></span>
                <a href="snippets.php" class="btn btn-back" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-arrow-left"></i> Back to Snippets
                </a>
            </div>
            <div class="code-content">
                <pre><?php echo htmlspecialchars($snippet['content']); ?></pre>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <div class="notes-header">
                <h2>
                    <i class="fas fa-comments"></i>
                    Notes
                </h2>
                <span class="notes-count"><?php echo count($notes); ?> note<?php echo count($notes) !== 1 ? 's' : ''; ?></span>
            </div>
            <div class="notes-list">
                <?php if (empty($notes)): ?>
                <div class="empty-notes">
                    <i class="fas fa-comment-slash"></i>
                    <p>No notes on this snippet yet.</p>
                </div>
                <?php else: ?>
                    <?php foreach ($notes as $note): 
                        $isUnauthorized = $note['author_id'] != $snippet['author_id'];
                    ?>
                    <div class="note-item <?php echo $isUnauthorized ? 'unauthorized' : ''; ?>">
                        <div class="note-header">
                            <div class="note-author">
                                <span class="note-author-avatar <?php echo $isUnauthorized ? 'unauthorized' : ''; ?>">
                                    <?php echo strtoupper(substr($note['author_name'], 0, 1)); ?>
                                </span>
                                <span class="note-author-name <?php echo $isUnauthorized ? 'unauthorized' : ''; ?>">
                                    <?php echo htmlspecialchars($note['author_name']); ?>
                                    <?php if ($isUnauthorized): ?>
                                    <span class="unauthorized-badge">
                                        <i class="fas fa-exclamation-triangle"></i> UNAUTHORIZED
                                    </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <span class="note-time"><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></span>
                        </div>
                        <div class="note-content <?php echo $isUnauthorized ? 'attack-note' : ''; ?>">
                            <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                            <?php if ($isUnauthorized): ?>
                            <p style="margin-top: 1rem; font-style: italic; color: #ff6b6b; font-size: 0.85rem;">
                                ⚠️ This note was created by a user who should not have access to this private snippet!
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
