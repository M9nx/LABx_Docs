<?php
// Lab 29: LinkedPro Newsletter Platform - Newsletters Listing
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($conn);

// Get all newsletters
$stmt = $conn->prepare("
    SELECT n.*, u.full_name as creator_name, u.headline as creator_headline, u.profile_picture,
           (SELECT COUNT(*) FROM subscribers s WHERE s.newsletter_id = n.id AND s.user_id = ?) as is_subscribed
    FROM newsletters n
    JOIN users u ON n.creator_id = u.user_id
    ORDER BY n.subscriber_count DESC
");
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$newsletters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover Newsletters - LinkedPro</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
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
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .header-nav a:hover {
            background: #f3f2ef;
            color: #0a66c2;
        }
        .header-nav a.active {
            color: #0a66c2;
            font-weight: 600;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            background: #0a66c2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        .btn-logout {
            background: none;
            border: 1px solid #0a66c2;
            color: #0a66c2;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .btn-logout:hover {
            background: #0a66c2;
            color: white;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 1.5rem;
        }
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .page-header p {
            color: #666;
        }
        .newsletters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        .newsletter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .newsletter-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .newsletter-cover {
            height: 100px;
            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .newsletter-body {
            padding: 1.25rem;
        }
        .creator-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .creator-avatar {
            width: 48px;
            height: 48px;
            background: #0a66c2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-top: -36px;
            border: 3px solid white;
        }
        .creator-details h4 {
            font-size: 0.9rem;
            color: #333;
        }
        .creator-details p {
            font-size: 0.8rem;
            color: #666;
        }
        .newsletter-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            text-decoration: none;
            display: block;
        }
        .newsletter-title:hover {
            color: #0a66c2;
        }
        .newsletter-desc {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .newsletter-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 1rem;
        }
        .newsletter-urn {
            font-family: monospace;
            font-size: 0.7rem;
            color: #999;
            background: #f5f5f5;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            margin-bottom: 1rem;
            word-break: break-all;
        }
        .newsletter-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #0a66c2;
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background: #004182;
        }
        .btn-secondary {
            background: white;
            color: #0a66c2;
            border: 1px solid #0a66c2;
        }
        .btn-secondary:hover {
            background: #f3f2ef;
        }
        .btn-subscribed {
            background: #057642;
            color: white;
            border: none;
        }
        .vulnerability-note {
            background: #fff4e5;
            border: 1px solid #ffa500;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .vulnerability-note strong {
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
            <a href="newsletters.php" class="active">📰 Newsletters</a>
            <?php if ($user['is_creator']): ?>
                <a href="create-newsletter.php">✏️ Create</a>
            <?php endif; ?>
        </div>
        
        <div class="user-menu">
            <div class="user-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h1>📰 Discover Newsletters</h1>
            <p>Subscribe to newsletters from industry thought leaders</p>
        </div>
        
        <div class="vulnerability-note">
            <strong>🎯 Lab Hint:</strong> Each newsletter displays its public URN. The subscriber list API endpoint does not verify if you own the newsletter before returning subscriber data. Try accessing subscribers of newsletters you don't own!
        </div>
        
        <div class="newsletters-grid">
            <?php foreach ($newsletters as $newsletter): ?>
                <div class="newsletter-card">
                    <div class="newsletter-cover">📰</div>
                    <div class="newsletter-body">
                        <div class="creator-info">
                            <div class="creator-avatar"><?= strtoupper(substr($newsletter['creator_name'], 0, 1)) ?></div>
                            <div class="creator-details">
                                <h4><?= htmlspecialchars($newsletter['creator_name']) ?></h4>
                                <p><?= htmlspecialchars($newsletter['creator_headline']) ?></p>
                            </div>
                        </div>
                        
                        <a href="newsletter.php?id=<?= $newsletter['id'] ?>" class="newsletter-title">
                            <?= htmlspecialchars($newsletter['title']) ?>
                        </a>
                        <p class="newsletter-desc"><?= htmlspecialchars($newsletter['description']) ?></p>
                        
                        <div class="newsletter-urn">URN: <?= htmlspecialchars($newsletter['newsletter_urn']) ?></div>
                        
                        <div class="newsletter-meta">
                            <span>👥 <?= $newsletter['subscriber_count'] ?> subscribers</span>
                            <span>📅 <?= $newsletter['frequency'] ?></span>
                        </div>
                        
                        <div class="newsletter-actions">
                            <?php if ($newsletter['is_subscribed']): ?>
                                <span class="btn btn-subscribed">✓ Subscribed</span>
                            <?php else: ?>
                                <a href="subscribe.php?id=<?= $newsletter['id'] ?>" class="btn btn-primary">Subscribe</a>
                            <?php endif; ?>
                            <a href="newsletter.php?id=<?= $newsletter['id'] ?>" class="btn btn-secondary">View</a>
                            <?php if ($newsletter['creator_id'] == $user['user_id']): ?>
                                <a href="subscribers.php?id=<?= $newsletter['id'] ?>" class="btn btn-secondary">👥 Subscribers</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
