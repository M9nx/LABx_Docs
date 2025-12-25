<?php
session_start();
require_once 'config.php';

$pdo = getDBConnection();

// Get all blog posts with author info
$posts = $pdo->query("
    SELECT p.*, u.username, u.full_name, u.guid as author_guid 
    FROM blog_posts p 
    JOIN users u ON p.user_guid = u.guid 
    ORDER BY p.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - GUIDLab</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            color: #999;
        }
        .blog-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .blog-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .blog-card:hover {
            border-color: rgba(255, 68, 68, 0.4);
            transform: translateY(-3px);
        }
        .blog-title {
            color: #ff6666;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        .blog-content {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        .blog-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 68, 68, 0.1);
        }
        .blog-author {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .author-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .author-info {
            display: flex;
            flex-direction: column;
        }
        .author-name {
            color: #ff8888;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }
        .author-name:hover {
            color: #ffaaaa;
            text-decoration: underline;
        }
        .author-role {
            color: #666;
            font-size: 0.85rem;
        }
        .blog-date {
            color: #666;
            font-size: 0.9rem;
        }
        .hint-notice {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #ffa500;
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['user_guid']; ?>">My Account</a>
                    <span class="user-status">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">📝 Blog Posts</h1>
            <p class="page-subtitle">Read articles from our community members</p>
        </div>

        <div class="hint-notice">
            💡 <strong>Hint:</strong> Click on author names to view their profiles. Pay attention to the URL structure...
        </div>

        <div class="blog-list">
            <?php foreach ($posts as $post): ?>
            <article class="blog-card">
                <h2 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                <p class="blog-content"><?php echo htmlspecialchars($post['content']); ?></p>
                <div class="blog-footer">
                    <div class="blog-author">
                        <div class="author-avatar">👤</div>
                        <div class="author-info">
                            <a href="profile.php?id=<?php echo $post['author_guid']; ?>" class="author-name">
                                <?php echo htmlspecialchars($post['full_name']); ?>
                            </a>
                            <span class="author-role">@<?php echo htmlspecialchars($post['username']); ?></span>
                        </div>
                    </div>
                    <span class="blog-date"><?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
