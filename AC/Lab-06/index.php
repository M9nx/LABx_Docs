<?php
session_start();
require_once '../progress.php';
$isSolved = isLabSolved(6);
require_once 'config.php';

// Check if carlos's API key has been submitted
$labSolved = false;
$carlosApiKey = 'sk-carlos-x7y8z9a0b1c2d3e4-targetkey';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_key'])) {
    $submittedKey = trim($_POST['api_key'] ?? '');
    if ($submittedKey === $carlosApiKey) {
        $labSolved = true;
        $_SESSION['lab_solved'] = true;
        markLabSolved(6);
    }
}

if (isset($_SESSION['lab_solved']) && $_SESSION['lab_solved']) {
    $labSolved = true;
}

// Get blog posts for display
$pdo = getDBConnection();
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
    <title>GUIDLab - Secure User System</title>
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
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .lab-description {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        .credentials-box {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .credentials-box h3 {
            color: #00ff00;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .credentials-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            color: #00ff00;
            font-family: 'Consolas', monospace;
        }
        .hint-box {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .hint-box h3 {
            color: #00ffff;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .hint-box p {
            color: #aadddd;
            font-size: 0.95rem;
        }
        .submit-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .submit-box h3 {
            color: #ffa500;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .submit-box form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .submit-box input[type="text"] {
            flex: 1;
            min-width: 250px;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            color: #fff;
            font-family: 'Consolas', monospace;
        }
        .submit-box input[type="text"]:focus {
            outline: none;
            border-color: #ffa500;
        }
        .submit-box button {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #ffa500, #cc8400);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .submit-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.4);
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
            margin-top: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
        .status-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .status-box.solved {
            background: rgba(0, 255, 0, 0.1);
            border-color: rgba(0, 255, 0, 0.3);
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ff4444;
        }
        .status-box.solved .status-indicator {
            background: #00ff00;
        }
        .user-status {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        /* Blog Section */
        .blog-section {
            margin-top: 2rem;
        }
        .blog-section h2 {
            color: #ff6666;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .blog-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .blog-card:hover {
            border-color: rgba(255, 68, 68, 0.4);
            transform: translateY(-3px);
        }
        .blog-title {
            color: #ff8888;
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
        }
        .blog-excerpt {
            color: #999;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .blog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 68, 68, 0.1);
        }
        .blog-author {
            color: #ff6666;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .blog-author:hover {
            color: #ff8888;
            text-decoration: underline;
        }
        .blog-date {
            color: #666;
            font-size: 0.85rem;
        }
    .solved-banner { background: rgba(0, 255, 0, 0.1); border: 1px solid rgba(0, 255, 0, 0.3); border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; text-align: center; } .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
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
        <div class="lab-card">
            <span class="lab-badge">ACCESS CONTROL LAB</span>
            <h1 class="lab-title">Lab 6: User ID Controlled by Request Parameter with Unpredictable User IDs</h1>
            
            <p class="lab-description">
                This lab has a <strong>horizontal privilege escalation vulnerability</strong> on the user account page.
                Unlike simple sequential IDs, this application uses <strong>GUIDs (Globally Unique Identifiers)</strong> 
                to identify users, making the IDs unpredictable. However, the application leaks user GUIDs in other 
                places, allowing attackers to discover them.
            </p>

            <div class="credentials-box">
                <h3>🔑 Your Credentials</h3>
                <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
            </div>

            <div class="hint-box">
                <h3>💡 Goal</h3>
                <p>
                    Find the <strong>GUID</strong> for user <strong>carlos</strong>, then access his profile to obtain 
                    his <strong>API key</strong>. Submit the API key below to solve the lab.
                    <br><br>
                    <em>Hint: Check the blog posts to find information about carlos...</em>
                </p>
            </div>

            <div class="submit-box">
                <h3>🏁 Submit Carlos's API Key</h3>
                <form method="POST">
                    <input type="text" name="api_key" placeholder="Enter carlos's API key here..." 
                           value="<?php echo $labSolved ? $carlosApiKey : ''; ?>" <?php echo $labSolved ? 'readonly' : ''; ?>>
                    <button type="submit" name="submit_key" <?php echo $labSolved ? 'disabled' : ''; ?>>
                        <?php echo $labSolved ? '✓ Verified' : 'Submit Key'; ?>
                    </button>
                </form>
            </div>

            <div class="status-box <?php echo $labSolved ? 'solved' : ''; ?>">
                <div class="status-indicator"></div>
                <span>
                    <?php if ($labSolved): ?>
                        🎉 <strong>Congratulations!</strong> Lab solved - You obtained carlos's API key!
                    <?php else: ?>
                        Lab Status: <strong>Unsolved</strong> - Find carlos's GUID and retrieve his API key
                    <?php endif; ?>
                </span>
            </div>

            <div style="margin-top: 2rem;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['user_guid']; ?>" class="btn btn-primary">Go to My Account</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login to Start</a>
                <?php endif; ?>
                <a href="blog.php" class="btn btn-secondary">Browse Blog</a>
                <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            </div>
        </div>

        <!-- Blog Preview Section -->
        <div class="lab-card blog-section">
            <h2>📝 Recent Blog Posts</h2>
            <div class="blog-grid">
                <?php foreach (array_slice($posts, 0, 4) as $post): ?>
                <div class="blog-card">
                    <h3 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="blog-excerpt"><?php echo htmlspecialchars(substr($post['content'], 0, 120)) . '...'; ?></p>
                    <div class="blog-meta">
                        <a href="profile.php?id=<?php echo $post['author_guid']; ?>" class="blog-author">
                            👤 <?php echo htmlspecialchars($post['username']); ?>
                        </a>
                        <span class="blog-date"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="blog.php" class="btn btn-secondary">View All Posts →</a>
            </div>
        </div>
    </div>
</body>
</html>



