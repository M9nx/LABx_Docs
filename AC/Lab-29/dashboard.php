<?php
// Lab 29: LinkedPro Newsletter Platform - Dashboard
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($conn);

// Get user's newsletters (if creator)
$myNewsletters = [];
if ($user['is_creator']) {
    $stmt = $conn->prepare("SELECT * FROM newsletters WHERE creator_id = ?");
    $stmt->bind_param("i", $user['user_id']);
    $stmt->execute();
    $myNewsletters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get user's subscriptions
$stmt = $conn->prepare("
    SELECT n.*, u.full_name as creator_name, u.headline as creator_headline
    FROM subscribers s
    JOIN newsletters n ON s.newsletter_id = n.id
    JOIN users u ON n.creator_id = u.user_id
    WHERE s.user_id = ?
");
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$subscriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all available newsletters for discovery
$stmt = $conn->prepare("
    SELECT n.*, u.full_name as creator_name, u.headline as creator_headline, u.profile_picture
    FROM newsletters n
    JOIN users u ON n.creator_id = u.user_id
    WHERE n.creator_id != ?
    ORDER BY n.subscriber_count DESC
");
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$discoverNewsletters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LinkedPro</title>
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
            font-size: 0.9rem;
        }
        .user-name {
            font-size: 0.9rem;
            color: #333;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 250px 1fr 300px;
            gap: 1.5rem;
        }
        .sidebar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .profile-card {
            text-align: center;
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }
        .profile-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0a66c2, #057642);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .profile-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        .profile-headline {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .profile-stats {
            display: flex;
            justify-content: space-around;
            padding: 1rem;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0a66c2;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #666;
        }
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h3 {
            font-size: 1rem;
            color: #333;
        }
        .card-body {
            padding: 1rem 1.25rem;
        }
        .newsletter-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .newsletter-item:last-child {
            border-bottom: none;
        }
        .newsletter-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0a66c2, #004182);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .newsletter-info {
            flex: 1;
        }
        .newsletter-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            text-decoration: none;
        }
        .newsletter-title:hover {
            color: #0a66c2;
            text-decoration: underline;
        }
        .newsletter-creator {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .newsletter-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #888;
        }
        .newsletter-actions {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
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
        .btn-subscribers {
            background: #057642;
            color: white;
            border: none;
        }
        .btn-subscribers:hover {
            background: #045432;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        .empty-state p {
            margin-bottom: 1rem;
        }
        .right-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .discover-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        .discover-item:last-child {
            border-bottom: none;
        }
        .discover-avatar {
            width: 40px;
            height: 40px;
            background: #0a66c2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .discover-info {
            flex: 1;
        }
        .discover-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            text-decoration: none;
        }
        .discover-title:hover {
            color: #0a66c2;
        }
        .discover-meta {
            font-size: 0.75rem;
            color: #666;
        }
        .vulnerability-hint {
            background: #fff4e5;
            border: 1px solid #ffa500;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .vulnerability-hint h4 {
            color: #cc7000;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .vulnerability-hint p {
            font-size: 0.85rem;
            color: #666;
        }
        .vulnerability-hint code {
            background: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        .urn-display {
            font-family: monospace;
            font-size: 0.75rem;
            color: #888;
            margin-top: 0.25rem;
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
            <a href="dashboard.php" class="active">🏠 Home</a>
            <a href="newsletters.php">📰 Newsletters</a>
            <?php if ($user['is_creator']): ?>
                <a href="create-newsletter.php">✏️ Create</a>
            <?php endif; ?>
        </div>
        
        <div class="user-menu">
            <div class="user-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
            <span class="user-name"><?= htmlspecialchars($user['full_name']) ?></span>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <div class="profile-card">
                <div class="profile-avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
                <div class="profile-name"><?= htmlspecialchars($user['full_name']) ?></div>
                <div class="profile-headline"><?= htmlspecialchars($user['headline']) ?></div>
            </div>
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($user['connections']) ?></div>
                    <div class="stat-label">Connections</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= count($subscriptions) ?></div>
                    <div class="stat-label">Subscriptions</div>
                </div>
            </div>
        </div>
        
        <div class="main-content">
            <?php if ($user['is_creator'] && !empty($myNewsletters)): ?>
                <div class="vulnerability-hint">
                    <h4>🎯 Lab Objective</h4>
                    <p>As a newsletter creator, you can view your own subscribers. Try to access the subscriber list of <strong>other creators' newsletters</strong> by manipulating the <code>seriesUrn</code> parameter in the API request.</p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>📰 My Newsletters</h3>
                        <a href="create-newsletter.php" class="btn btn-primary">+ Create Newsletter</a>
                    </div>
                    <div class="card-body">
                        <?php foreach ($myNewsletters as $newsletter): ?>
                            <div class="newsletter-item">
                                <div class="newsletter-icon">📰</div>
                                <div class="newsletter-info">
                                    <a href="newsletter.php?id=<?= $newsletter['id'] ?>" class="newsletter-title">
                                        <?= htmlspecialchars($newsletter['title']) ?>
                                    </a>
                                    <div class="newsletter-creator">By you • <?= $newsletter['frequency'] ?></div>
                                    <div class="urn-display">URN: <?= htmlspecialchars($newsletter['newsletter_urn']) ?></div>
                                    <div class="newsletter-meta">
                                        <span>👥 <?= $newsletter['subscriber_count'] ?> subscribers</span>
                                        <span>📅 Created <?= date('M j, Y', strtotime($newsletter['created_at'])) ?></span>
                                    </div>
                                </div>
                                <div class="newsletter-actions">
                                    <a href="subscribers.php?id=<?= $newsletter['id'] ?>" class="btn btn-subscribers">
                                        👥 Subscribers
                                    </a>
                                    <a href="newsletter.php?id=<?= $newsletter['id'] ?>" class="btn btn-secondary">
                                        View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>📚 My Subscriptions</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($subscriptions)): ?>
                        <div class="empty-state">
                            <p>You haven't subscribed to any newsletters yet.</p>
                            <a href="newsletters.php" class="btn btn-primary">Discover Newsletters</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($subscriptions as $sub): ?>
                            <div class="newsletter-item">
                                <div class="newsletter-icon">📰</div>
                                <div class="newsletter-info">
                                    <a href="newsletter.php?id=<?= $sub['id'] ?>" class="newsletter-title">
                                        <?= htmlspecialchars($sub['title']) ?>
                                    </a>
                                    <div class="newsletter-creator">
                                        By <?= htmlspecialchars($sub['creator_name']) ?> • <?= htmlspecialchars($sub['creator_headline']) ?>
                                    </div>
                                    <div class="newsletter-meta">
                                        <span>👥 <?= $sub['subscriber_count'] ?> subscribers</span>
                                        <span>📅 <?= $sub['frequency'] ?></span>
                                    </div>
                                </div>
                                <div class="newsletter-actions">
                                    <a href="newsletter.php?id=<?= $sub['id'] ?>" class="btn btn-secondary">Read</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="right-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>🔍 Discover Newsletters</h3>
                </div>
                <div class="card-body">
                    <?php foreach ($discoverNewsletters as $newsletter): ?>
                        <div class="discover-item">
                            <div class="discover-avatar"><?= strtoupper(substr($newsletter['creator_name'], 0, 1)) ?></div>
                            <div class="discover-info">
                                <a href="newsletter.php?id=<?= $newsletter['id'] ?>" class="discover-title">
                                    <?= htmlspecialchars($newsletter['title']) ?>
                                </a>
                                <div class="discover-meta"><?= htmlspecialchars($newsletter['creator_name']) ?></div>
                                <div class="discover-meta">👥 <?= $newsletter['subscriber_count'] ?> subscribers</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div style="margin-top: 1rem;">
                        <a href="newsletters.php" class="btn btn-secondary" style="width: 100%; justify-content: center;">View All</a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>⚠️ Hint</h3>
                </div>
                <div class="card-body">
                    <p style="font-size: 0.85rem; color: #666; line-height: 1.5;">
                        Each newsletter has a public <code>newsletter_urn</code>. The subscriber list API accepts this URN without verifying ownership. Try viewing subscribers of newsletters you don't own!
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
