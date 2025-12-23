<?php
/**
 * VULNERABLE PROFILE PAGE - User ID (GUID) Controlled by Request Parameter
 * 
 * VULNERABILITY: This page uses $_GET['id'] (GUID) to fetch user data without
 * verifying that the logged-in user is authorized to view that profile.
 * 
 * Although GUIDs are unpredictable, the application leaks them in blog posts,
 * allowing attackers to discover other users' GUIDs and access their profiles.
 */

session_start();
require_once 'config.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// VULNERABLE CODE: Uses URL parameter (GUID) directly without authorization check
// The application trusts the 'id' parameter from the URL
$requestedGuid = $_GET['id'] ?? $_SESSION['user_guid'];

$pdo = getDBConnection();

// VULNERABILITY: Fetches ANY user's data based on the URL parameter (GUID)
// No check is performed to verify if the logged-in user should see this data
$stmt = $pdo->prepare("SELECT * FROM users WHERE guid = ?");
$stmt->execute([$requestedGuid]);
$user = $stmt->fetch();

if (!$user) {
    $error = "User not found.";
}

// Get user's blog posts
$posts = [];
if ($user) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE user_guid = ? ORDER BY created_at DESC");
    $stmt->execute([$user['guid']]);
    $posts = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user ? htmlspecialchars($user['full_name']) : 'User'; ?> - GUIDLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #ff4444;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .user-status {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        .profile-header {
            background: linear-gradient(135deg, rgba(255, 68, 68, 0.2), rgba(204, 0, 0, 0.2));
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }
        .profile-name {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 0.3rem;
        }
        .profile-username {
            color: #ff6666;
            font-size: 1rem;
        }
        .profile-body {
            padding: 2rem;
        }
        .info-section {
            margin-bottom: 2rem;
        }
        .info-section:last-child {
            margin-bottom: 0;
        }
        .info-title {
            color: #ff4444;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 0.8rem;
        }
        .info-label {
            color: #888;
            font-weight: 500;
        }
        .info-value {
            color: #e0e0e0;
        }
        .info-value.guid {
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #ff8888;
        }
        .api-key-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .api-key-label {
            color: #ffa500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .api-key-value {
            font-family: 'Consolas', 'Monaco', monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.8rem;
            border-radius: 6px;
            color: #ffa500;
            word-break: break-all;
            font-size: 0.9rem;
        }
        .error-card {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
        }
        .error-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .error-title {
            color: #ff4444;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .error-message {
            color: #999;
        }
        .viewing-other-notice {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.9rem;
            color: #00ffff;
        }
        .user-posts {
            margin-top: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
        }
        .user-posts h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .post-item {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.8rem;
        }
        .post-item:last-child {
            margin-bottom: 0;
        }
        .post-title {
            color: #ff8888;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .post-date {
            color: #666;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 GUIDLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="blog.php">Blog</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="profile.php?id=<?php echo $_SESSION['user_guid']; ?>">My Account</a>
                <span class="user-status">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-card">
                <div class="error-icon">❌</div>
                <h2 class="error-title">User Not Found</h2>
                <p class="error-message">The requested user profile does not exist.</p>
            </div>
        <?php else: ?>
            <?php if ($requestedGuid !== $_SESSION['user_guid']): ?>
                <div class="viewing-other-notice">
                    ℹ️ You are viewing <strong><?php echo htmlspecialchars($user['username']); ?></strong>'s profile 
                    (You are logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?>)
                </div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">👤</div>
                    <h1 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <p class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></p>
                </div>
                
                <div class="profile-body">
                    <div class="info-section">
                        <h3 class="info-title">📋 Account Information</h3>
                        <div class="info-grid">
                            <span class="info-label">User ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['id']); ?></span>
                            
                            <span class="info-label">GUID:</span>
                            <span class="info-value guid"><?php echo htmlspecialchars($user['guid']); ?></span>
                            
                            <span class="info-label">Username:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                            
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                            
                            <span class="info-label">Department:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['department']); ?></span>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 class="info-title">📞 Contact Details</h3>
                        <div class="info-grid">
                            <span class="info-label">Phone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                            
                            <span class="info-label">Address:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['address']); ?></span>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 class="info-title">🔐 API Access</h3>
                        <div class="api-key-box">
                            <div class="api-key-label">🔑 Your API Key</div>
                            <div class="api-key-value"><?php echo htmlspecialchars($user['api_key']); ?></div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 class="info-title">📝 Notes</h3>
                        <p class="info-value"><?php echo htmlspecialchars($user['notes']); ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($posts)): ?>
            <div class="user-posts">
                <h3>📝 Posts by <?php echo htmlspecialchars($user['username']); ?></h3>
                <?php foreach ($posts as $post): ?>
                <div class="post-item">
                    <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                    <div class="post-date"><?php echo date('F d, Y', strtotime($post['created_at'])); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
