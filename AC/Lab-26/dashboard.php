<?php
/**
 * Lab 26: Dashboard
 * Pressable-style API Applications Platform
 */

require_once 'config.php';
requireLogin();

$user = getCurrentUser($pdo);
$applications = getUserApplications($pdo, $_SESSION['user_id']);

// Get site count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sites WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$siteCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pressable API Platform</title>
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
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .stat-card h3 {
            color: #888;
            font-size: 0.85rem;
            font-weight: normal;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #00b4d8;
        }
        .section {
            margin-bottom: 2rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
        }
        .section-header h2 {
            color: #fff;
            font-size: 1.25rem;
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
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border: none;
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
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .app-list {
            display: grid;
            gap: 1rem;
        }
        .app-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        .app-card:hover {
            border-color: rgba(0, 180, 216, 0.5);
        }
        .app-info h3 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }
        .app-info p {
            color: #666;
            font-size: 0.85rem;
        }
        .app-id {
            font-family: monospace;
            font-size: 0.75rem;
            color: #888;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
        }
        .app-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .app-status.active {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .app-status.inactive {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .app-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .attack-hint {
            background: rgba(255, 102, 102, 0.1);
            border: 1px solid rgba(255, 102, 102, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .attack-hint h3 {
            color: #ff6b6b;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .attack-hint p {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }
        .attack-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
        }
        .quick-links {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .quick-link {
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #e0e0e0;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quick-link:hover {
            border-color: #00b4d8;
            background: rgba(0, 180, 216, 0.1);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
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
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="applications.php">API Apps</a>
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
        <div class="page-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? $_SESSION['username']); ?></h1>
            <p>Manage your API applications and integrations</p>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 IDOR Vulnerability Hint</h3>
            <p>
                You have 1 API application with ID <code>1</code>. There are more applications (IDs <code>2-9</code>) 
                belonging to other users. These contain <strong>sensitive Client Secrets</strong>.
            </p>
            <p>
                <strong>Attack Vector:</strong> Go to the "Update Application" page and intercept the request. 
                Try changing the <code>application[id]</code> parameter while removing other fields. 
                The error response will leak the victim's credentials!
            </p>
            <p style="margin-top: 0.5rem;">
                💡 Check the <a href="docs.php" style="color: #00b4d8;">Documentation</a> for exploitation steps.
            </p>
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>API Applications</h3>
                <div class="stat-value"><?php echo count($applications); ?></div>
            </div>
            <div class="stat-card">
                <h3>Managed Sites</h3>
                <div class="stat-value"><?php echo $siteCount; ?></div>
            </div>
            <div class="stat-card">
                <h3>Current Plan</h3>
                <div class="stat-value" style="font-size: 1.25rem; text-transform: capitalize;">
                    <?php echo htmlspecialchars($user['plan_type']); ?>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Your API Applications</h2>
                <a href="create-application.php" class="btn btn-primary">+ New Application</a>
            </div>

            <?php if (empty($applications)): ?>
            <div class="empty-state">
                <div class="icon">📱</div>
                <p>You haven't created any API applications yet</p>
            </div>
            <?php else: ?>
            <div class="app-list">
                <?php foreach ($applications as $app): ?>
                <div class="app-card">
                    <div class="app-info">
                        <h3><?php echo htmlspecialchars($app['name']); ?></h3>
                        <p><?php echo htmlspecialchars($app['description'] ?? 'No description'); ?></p>
                        <div class="app-id">Application ID: <?php echo $app['id']; ?></div>
                    </div>
                    <div class="app-actions">
                        <span class="app-status <?php echo $app['status']; ?>"><?php echo $app['status']; ?></span>
                        <a href="view-application.php?id=<?php echo $app['id']; ?>" class="btn btn-secondary">View</a>
                        <a href="update-application.php?id=<?php echo $app['id']; ?>" class="btn btn-secondary">Update</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Quick Links</h2>
            </div>
            <div class="quick-links">
                <a href="applications.php" class="quick-link">📱 All Applications</a>
                <a href="create-application.php" class="quick-link">➕ Create New App</a>
                <a href="docs.php" class="quick-link">📚 API Documentation</a>
                <a href="success.php" class="quick-link">🏁 Submit Flag</a>
            </div>
        </div>
    </main>
</body>
</html>
