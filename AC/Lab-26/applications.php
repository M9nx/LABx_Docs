<?php
/**
 * Lab 26: API Applications List
 */

require_once 'config.php';
requireLogin();

$applications = getUserApplications($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Applications - Pressable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #00b4d8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #aaa;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #00b4d8;
            background: rgba(0, 180, 216, 0.1);
        }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(0, 180, 216, 0.2);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 20px;
            color: #00b4d8;
            font-size: 0.9rem;
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
        }
        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 180, 216, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .apps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.25rem;
        }
        .app-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .app-card:hover {
            border-color: rgba(0, 180, 216, 0.5);
            transform: translateY(-3px);
        }
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .app-name {
            font-size: 1.15rem;
            font-weight: 600;
            color: #fff;
        }
        .app-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .app-status.active {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .app-description {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .app-meta {
            display: grid;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }
        .meta-item .label { color: #666; }
        .meta-item .value {
            color: #00b4d8;
            font-family: monospace;
        }
        .app-actions {
            display: flex;
            gap: 0.5rem;
        }
        .empty-state {
            text-align: center;
            padding: 4rem;
            color: #666;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }
        .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: #00b4d8; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">⚡</span>
                Pressable
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="applications.php" class="active">API Apps</a>
                <a href="docs.php">Docs</a>
                <a href="success.php">🏁 Flag</a>
                <div class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <div class="page-header">
            <h1>API Applications</h1>
            <a href="create-application.php" class="btn btn-primary">+ New Application</a>
        </div>

        <?php if (empty($applications)): ?>
        <div class="empty-state">
            <div class="icon">📱</div>
            <h2>No API Applications Yet</h2>
            <p style="margin-top: 0.5rem;">Create your first API application to get started</p>
            <a href="create-application.php" class="btn btn-primary" style="margin-top: 1.5rem;">
                Create Application
            </a>
        </div>
        <?php else: ?>
        <div class="apps-grid">
            <?php foreach ($applications as $app): ?>
            <div class="app-card">
                <div class="app-header">
                    <span class="app-name"><?php echo htmlspecialchars($app['name']); ?></span>
                    <span class="app-status <?php echo $app['status']; ?>"><?php echo $app['status']; ?></span>
                </div>
                <p class="app-description">
                    <?php echo htmlspecialchars($app['description'] ?? 'No description provided'); ?>
                </p>
                <div class="app-meta">
                    <div class="meta-item">
                        <span class="label">Application ID</span>
                        <span class="value"><?php echo $app['id']; ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="label">Created</span>
                        <span class="value"><?php echo date('M j, Y', strtotime($app['created_at'])); ?></span>
                    </div>
                </div>
                <div class="app-actions">
                    <a href="view-application.php?id=<?php echo $app['id']; ?>" class="btn btn-secondary">View Details</a>
                    <a href="update-application.php?id=<?php echo $app['id']; ?>" class="btn btn-primary">Update</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
