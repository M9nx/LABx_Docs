<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['lab19_user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['lab19_user_id'];
$username = $_SESSION['lab19_username'];
$display_name = $_SESSION['lab19_display_name'];
$avatar_color = $_SESSION['lab19_avatar_color'];
$labSolved = isLabSolved(19);

// Get user's saved projects count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM saved_projects WHERE user_id = ?");
$stmt->execute([$user_id]);
$saved_count = $stmt->fetch()['count'];

// Get some featured projects for the feed
$stmt = $pdo->query("SELECT p.*, u.display_name, u.avatar_color FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.likes_count DESC LIMIT 6");
$featured_projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ProjectHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #a5b4fc; text-decoration: none; transition: color 0.3s; }
        .nav-links a:hover { color: #c7d2fe; }
        .nav-links a.active { color: #818cf8; font-weight: 600; }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 0.9rem;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .welcome-section h1 { color: #e0e0e0; font-size: 1.75rem; }
        .welcome-section p { color: #64748b; margin-top: 0.25rem; }
        <?php if ($labSolved): ?>
        .solved-banner {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #6ee7b7;
        }
        <?php endif; ?>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .stat-card .icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .stat-card h3 { color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem; }
        .stat-card .value { color: #e0e0e0; font-size: 1.75rem; font-weight: bold; }
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }
        @media (max-width: 1000px) {
            .main-grid { grid-template-columns: 1fr; }
        }
        .section-title {
            color: #e0e0e0;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .project-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .project-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-5px);
        }
        .project-thumbnail {
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .project-info { padding: 1rem; }
        .project-info h4 { color: #e0e0e0; margin-bottom: 0.5rem; }
        .project-info p { color: #64748b; font-size: 0.85rem; }
        .project-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .project-author {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .project-author .mini-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            color: white;
        }
        .project-author span { color: #94a3b8; font-size: 0.8rem; }
        .project-stats { display: flex; gap: 1rem; color: #64748b; font-size: 0.8rem; }
        .sidebar-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .sidebar-card h3 {
            color: #a5b4fc;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .quick-links { list-style: none; }
        .quick-links li { margin-bottom: 0.5rem; }
        .quick-links a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .quick-links a:hover {
            background: rgba(99, 102, 241, 0.1);
            color: #c7d2fe;
        }
        .highlight-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.1));
            border-color: rgba(99, 102, 241, 0.3);
        }
        .highlight-card h3 { color: #c7d2fe; border-color: rgba(99, 102, 241, 0.3); }
        .highlight-card p { color: #a5b4fc; font-size: 0.9rem; line-height: 1.6; }
        .highlight-card code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #fcd34d;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
            </a>
            <nav class="nav-links">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="saved-projects.php">Saved Projects</a>
                <a href="docs.php">Docs</a>
                <a href="../index.php">All Labs</a>
            </nav>
            <div class="user-menu">
                <span style="color: #94a3b8;"><?php echo htmlspecialchars($display_name); ?></span>
                <div class="avatar" style="background: <?php echo htmlspecialchars($avatar_color); ?>">
                    <?php echo strtoupper(substr($display_name, 0, 1)); ?>
                </div>
                <a href="logout.php" style="color: #ef4444; text-decoration: none; margin-left: 0.5rem;">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <div>
                <h1>Welcome back, <?php echo htmlspecialchars($display_name); ?>! 👋</h1>
                <p>Here's what's happening with your projects today.</p>
            </div>
            <?php if ($labSolved): ?>
            <div class="solved-banner">
                <span style="font-size: 1.5rem;">🏆</span>
                <span>Lab 19 Solved! IDOR vulnerability successfully exploited!</span>
            </div>
            <?php endif; ?>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon" style="background: rgba(99, 102, 241, 0.2);">📁</div>
                <h3>Saved Projects</h3>
                <div class="value"><?php echo $saved_count; ?></div>
            </div>
            <div class="stat-card">
                <div class="icon" style="background: rgba(236, 72, 153, 0.2);">❤️</div>
                <h3>Total Likes</h3>
                <div class="value">1.2K</div>
            </div>
            <div class="stat-card">
                <div class="icon" style="background: rgba(34, 197, 94, 0.2);">👁️</div>
                <h3>Profile Views</h3>
                <div class="value">3.4K</div>
            </div>
            <div class="stat-card">
                <div class="icon" style="background: rgba(245, 158, 11, 0.2);">⭐</div>
                <h3>Featured</h3>
                <div class="value">2</div>
            </div>
        </div>

        <div class="main-grid">
            <div>
                <h2 class="section-title">🔥 Trending Projects</h2>
                <div class="projects-grid">
                    <?php 
                    $colors = ['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#3b82f6'];
                    foreach ($featured_projects as $i => $project): 
                    ?>
                    <div class="project-card">
                        <div class="project-thumbnail" style="background: <?php echo $colors[$i % count($colors)]; ?>20;">
                            <?php echo ['🎨', '📱', '💼', '🖼️', '🎬', '🎮'][$i % 6]; ?>
                        </div>
                        <div class="project-info">
                            <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($project['description'] ?? '', 0, 60)); ?>...</p>
                            <div class="project-meta">
                                <div class="project-author">
                                    <div class="mini-avatar" style="background: <?php echo htmlspecialchars($project['avatar_color']); ?>">
                                        <?php echo strtoupper(substr($project['display_name'], 0, 1)); ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($project['display_name']); ?></span>
                                </div>
                                <div class="project-stats">
                                    <span>❤️ <?php echo $project['likes_count']; ?></span>
                                    <span>👁️ <?php echo $project['views_count']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="sidebar-card highlight-card">
                    <h3>🎯 Lab Objective</h3>
                    <p>
                        Delete another user's saved project by exploiting the IDOR vulnerability 
                        in the delete endpoint. Target: <code>victim_designer</code>'s saved 
                        projects (IDs: 101-105).
                    </p>
                    <a href="saved-projects.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        Go to Saved Projects →
                    </a>
                </div>

                <div class="sidebar-card">
                    <h3>⚡ Quick Actions</h3>
                    <ul class="quick-links">
                        <li><a href="saved-projects.php">📑 Manage Saved Projects</a></li>
                        <li><a href="lab-description.php">📋 Lab Instructions</a></li>
                        <li><a href="docs.php">📚 Documentation</a></li>
                        <li><a href="success.php">🏆 Check Progress</a></li>
                    </ul>
                </div>

                <div class="sidebar-card">
                    <h3>💡 Hint</h3>
                    <p style="color: #94a3b8; font-size: 0.9rem;">
                        Check the URL when deleting your own saved project. What parameter controls 
                        which item gets deleted? Can you change it?
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
