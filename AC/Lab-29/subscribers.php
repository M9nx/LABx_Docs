<?php
// Lab 29: LinkedPro Newsletter Platform - Subscribers Page (UI)
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($conn);
$newsletter_id = $_GET['id'] ?? 0;

// Get newsletter - INTENTIONALLY NO OWNERSHIP CHECK FOR IDOR
$stmt = $conn->prepare("
    SELECT n.*, u.full_name as creator_name, u.user_id as creator_id
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

$isOwner = ($newsletter['creator_id'] == $user['user_id']);

// Get subscribers - VULNERABLE: No ownership check!
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.email, u.full_name, u.headline, u.location, u.connections, s.subscribed_at
    FROM subscribers s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.newsletter_id = ?
    ORDER BY s.subscribed_at DESC
");
$stmt->bind_param("i", $newsletter_id);
$stmt->execute();
$subscribers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Log the access - useful for detecting the attack
logActivity($conn, $user['user_id'], 'view_subscribers', 'newsletter', $newsletter_id, 
    ($isOwner ? 'Owner viewing own subscribers' : 'POTENTIAL IDOR: Non-owner accessed subscribers'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers - <?= htmlspecialchars($newsletter['title']) ?> - LinkedPro</title>
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
            max-width: 900px;
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
        .page-header {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .page-title {
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            color: #666;
            font-size: 0.9rem;
        }
        .idor-success {
            background: linear-gradient(135deg, #ff6b6b 0%, #c0392b 100%);
            color: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .idor-success h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .idor-success p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .idor-success .flag {
            font-family: monospace;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            display: inline-block;
        }
        .subscribers-card {
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
        .subscriber-count {
            background: #0a66c2;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
        }
        .subscriber-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        .subscriber-item:last-child {
            border-bottom: none;
        }
        .subscriber-item:hover {
            background: #f8f9fa;
        }
        .subscriber-avatar {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0a66c2, #057642);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .subscriber-info {
            flex: 1;
        }
        .subscriber-name {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }
        .subscriber-headline {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.2rem;
        }
        .subscriber-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #888;
        }
        .subscriber-email {
            color: #0a66c2;
            font-family: monospace;
            font-size: 0.8rem;
        }
        .pii-warning {
            background: #ffebee;
            color: #c62828;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .api-demo {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        .api-demo h4 {
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 0.75rem;
        }
        .api-url {
            font-family: monospace;
            font-size: 0.8rem;
            background: white;
            padding: 0.75rem;
            border-radius: 4px;
            word-break: break-all;
            border: 1px solid #ddd;
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
        }
        .btn-success {
            background: #057642;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="lab-banner">
        🔬 Lab 29: Newsletter Subscriber IDOR | <a href="lab-description.php">Lab Description</a> | <a href="docs.php">Documentation</a> | <a href="index.php">Lab Home</a> | <a href="../index.php">← All Labs</a>
    </div>
    
    <div class="header">
        <a href="dashboard.php" class="logo">Linked<span>Pro</span></a>
        <a href="logout.php" class="btn-logout">Sign Out</a>
    </div>
    
    <div class="container">
        <a href="newsletter.php?id=<?= $newsletter_id ?>" class="back-link">← Back to Newsletter</a>
        
        <?php if (!$isOwner): ?>
            <div class="idor-success">
                <h3>🎉 IDOR Vulnerability Exploited!</h3>
                <p>You successfully accessed the subscriber list of a newsletter you don't own!</p>
                <p>This exposes sensitive PII including emails, locations, and professional information.</p>
                <div class="flag">FLAG{linkedin_idor_newsletter_subscribers_exposed_2024}</div>
                <p style="margin-top: 1rem;"><a href="success.php" class="btn btn-success">🏆 Submit Flag</a></p>
            </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1 class="page-title">👥 Newsletter Subscribers</h1>
            <p class="page-subtitle">
                <strong><?= htmlspecialchars($newsletter['title']) ?></strong> by <?= htmlspecialchars($newsletter['creator_name']) ?>
            </p>
        </div>
        
        <div class="subscribers-card">
            <div class="card-header">
                <h3>Subscriber List</h3>
                <span class="subscriber-count"><?= count($subscribers) ?> subscribers</span>
            </div>
            
            <?php if (empty($subscribers)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    No subscribers yet.
                </div>
            <?php else: ?>
                <?php foreach ($subscribers as $sub): ?>
                    <div class="subscriber-item">
                        <div class="subscriber-avatar"><?= strtoupper(substr($sub['full_name'], 0, 1)) ?></div>
                        <div class="subscriber-info">
                            <div class="subscriber-name">
                                <?= htmlspecialchars($sub['full_name']) ?>
                                <?php if (!$isOwner): ?>
                                    <span class="pii-warning">⚠️ PII EXPOSED</span>
                                <?php endif; ?>
                            </div>
                            <div class="subscriber-headline"><?= htmlspecialchars($sub['headline']) ?></div>
                            <div class="subscriber-meta">
                                <span class="subscriber-email">📧 <?= htmlspecialchars($sub['email']) ?></span>
                                <span>📍 <?= htmlspecialchars($sub['location']) ?></span>
                                <span>🔗 <?= number_format($sub['connections']) ?> connections</span>
                                <span>📅 Subscribed <?= date('M j, Y', strtotime($sub['subscribed_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="api-demo">
            <h4>🔗 API Endpoint (Vulnerable)</h4>
            <div class="api-url">
                GET /api/get_subscribers.php?seriesUrn=urn:li:<?= htmlspecialchars($newsletter['newsletter_urn']) ?>&count=10&start=0
            </div>
            <p style="margin-top: 0.75rem; font-size: 0.85rem; color: #666;">
                This API endpoint returns subscriber data without verifying newsletter ownership.
            </p>
        </div>
    </div>
</body>
</html>
