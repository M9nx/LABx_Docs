<?php
// Lab 29: LinkedPro Newsletter Platform - Newsletter Detail Page
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($conn);
$newsletter_id = $_GET['id'] ?? 0;

// Get newsletter details
$stmt = $conn->prepare("
    SELECT n.*, u.full_name as creator_name, u.headline as creator_headline, u.user_id as creator_id
    FROM newsletters n
    JOIN users u ON n.creator_id = u.user_id
    WHERE n.id = ?
");
$stmt->bind_param("i", $newsletter_id);
$stmt->execute();
$newsletter = $stmt->get_result()->fetch_assoc();

if (!$newsletter) {
    header('Location: newsletters.php');
    exit();
}

// Check if user is subscribed
$stmt = $conn->prepare("SELECT * FROM subscribers WHERE newsletter_id = ? AND user_id = ?");
$stmt->bind_param("ii", $newsletter_id, $user['user_id']);
$stmt->execute();
$isSubscribed = $stmt->get_result()->num_rows > 0;

// Get articles
$stmt = $conn->prepare("SELECT * FROM articles WHERE newsletter_id = ? ORDER BY published_at DESC");
$stmt->bind_param("i", $newsletter_id);
$stmt->execute();
$articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$isOwner = ($newsletter['creator_id'] == $user['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($newsletter['title']) ?> - LinkedPro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f3f2ef;
            min-height: 100vh;
        }
        .lab-banner {
            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
            color: white;
            padding: 0.5rem 1rem;
            text-align: center;
            font-size: 0.85rem;
        }
        .lab-banner a {
            color: #7fc4fd;
            text-decoration: none;
        }
        .header {
            background: white;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .logo span {
            color: #057642;
        }
        .header-nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .header-nav a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .btn-logout {
            background: none;
            border: 1px solid #0a66c2;
            color: #0a66c2;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1.5rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #0a66c2;
            text-decoration: none;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .newsletter-header {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .newsletter-cover {
            height: 150px;
            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }
        .newsletter-info {
            padding: 1.5rem;
        }
        .creator-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .creator-avatar {
            width: 56px;
            height: 56px;
            background: #0a66c2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            margin-top: -40px;
            border: 3px solid white;
        }
        .creator-details h4 {
            font-size: 1rem;
            color: #333;
        }
        .creator-details p {
            font-size: 0.85rem;
            color: #666;
        }
        .newsletter-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
        }
        .newsletter-desc {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .newsletter-urn {
            font-family: monospace;
            font-size: 0.8rem;
            color: #888;
            background: #f5f5f5;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .newsletter-stats {
            display: flex;
            gap: 2rem;
            padding: 1rem 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin-bottom: 1rem;
        }
        .stat {
            text-align: center;
        }
        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0a66c2;
        }
        .stat-label {
            font-size: 0.8rem;
            color: #666;
        }
        .newsletter-actions {
            display: flex;
            gap: 0.75rem;
        }
        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            border: none;
        }
        .btn-primary {
            background: #0a66c2;
            color: white;
        }
        .btn-primary:hover {
            background: #004182;
        }
        .btn-secondary {
            background: white;
            color: #0a66c2;
            border: 1px solid #0a66c2;
        }
        .btn-subscribers {
            background: #057642;
            color: white;
        }
        .btn-subscribers:hover {
            background: #045432;
        }
        .btn-subscribed {
            background: #057642;
            color: white;
        }
        .articles-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .section-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eee;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }
        .article-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .article-item:last-child {
            border-bottom: none;
        }
        .article-title {
            font-size: 1rem;
            color: #333;
            text-decoration: none;
        }
        .article-title:hover {
            color: #0a66c2;
        }
        .article-meta {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .article-views {
            font-size: 0.85rem;
            color: #888;
        }
        .owner-hint {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #2e7d32;
        }
        .attacker-hint {
            background: #fff4e5;
            border: 1px solid #ffa500;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #cc7000;
        }
    </style>
</head>
<body>
    <div class="lab-banner">
        🔬 Lab 29: Newsletter Subscriber IDOR | <a href="lab-description.php">Lab Description</a> | <a href="docs.php">Documentation</a> | <a href="index.php">Lab Home</a> | <a href="../index.php">← All Labs</a>
    </div>
    
    <div class="header">
        <a href="dashboard.php" class="logo">Linked<span>Pro</span></a>
        
        <div class="header-nav">
            <a href="dashboard.php">🏠 Home</a>
            <a href="newsletters.php">📰 Newsletters</a>
        </div>
        
        <div class="user-menu">
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
    
    <div class="container">
        <a href="newsletters.php" class="back-link">← Back to Newsletters</a>
        
        <?php if ($isOwner): ?>
            <div class="owner-hint">
                <strong>✓ You own this newsletter.</strong> You can legitimately view your subscriber list using the "View Subscribers" button below.
            </div>
        <?php else: ?>
            <div class="attacker-hint">
                <strong>🎯 Attack Opportunity:</strong> You don't own this newsletter, but you can see its URN. Try accessing the subscriber API with this URN to view private subscriber data!
            </div>
        <?php endif; ?>
        
        <div class="newsletter-header">
            <div class="newsletter-cover">📰</div>
            <div class="newsletter-info">
                <div class="creator-row">
                    <div class="creator-avatar"><?= strtoupper(substr($newsletter['creator_name'], 0, 1)) ?></div>
                    <div class="creator-details">
                        <h4><?= htmlspecialchars($newsletter['creator_name']) ?></h4>
                        <p><?= htmlspecialchars($newsletter['creator_headline']) ?></p>
                    </div>
                </div>
                
                <h1 class="newsletter-title"><?= htmlspecialchars($newsletter['title']) ?></h1>
                <p class="newsletter-desc"><?= htmlspecialchars($newsletter['description']) ?></p>
                
                <div class="newsletter-urn">
                    <strong>Newsletter URN:</strong> <?= htmlspecialchars($newsletter['newsletter_urn']) ?>
                </div>
                
                <div class="newsletter-stats">
                    <div class="stat">
                        <div class="stat-value"><?= $newsletter['subscriber_count'] ?></div>
                        <div class="stat-label">Subscribers</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= count($articles) ?></div>
                        <div class="stat-label">Articles</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= $newsletter['frequency'] ?></div>
                        <div class="stat-label">Frequency</div>
                    </div>
                </div>
                
                <div class="newsletter-actions">
                    <?php if ($isOwner): ?>
                        <a href="subscribers.php?id=<?= $newsletter['id'] ?>" class="btn btn-subscribers">👥 View Subscribers</a>
                    <?php elseif ($isSubscribed): ?>
                        <span class="btn btn-subscribed">✓ Subscribed</span>
                    <?php else: ?>
                        <a href="subscribe.php?id=<?= $newsletter['id'] ?>" class="btn btn-primary">+ Subscribe</a>
                    <?php endif; ?>
                    
                    <a href="api/get_subscribers.php?seriesUrn=urn:li:<?= urlencode($newsletter['newsletter_urn']) ?>" 
                       class="btn btn-secondary" target="_blank" title="Direct API Link (for testing)">
                        🔗 API Link
                    </a>
                </div>
            </div>
        </div>
        
        <div class="articles-section">
            <div class="section-header">📄 Recent Articles</div>
            <?php if (empty($articles)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    No articles published yet.
                </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article-item">
                        <div>
                            <a href="#" class="article-title"><?= htmlspecialchars($article['title']) ?></a>
                            <div class="article-meta">
                                Published <?= date('M j, Y', strtotime($article['published_at'])) ?>
                            </div>
                        </div>
                        <div class="article-views">
                            👁️ <?= number_format($article['views']) ?> views
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
