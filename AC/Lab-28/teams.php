<?php
/**
 * Lab 28: Teams List Page
 * MTN Developers Portal
 */

require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser($pdo);
$userTeams = getUserTeams($pdo, $user['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams - MTN Developers Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 45px;
            height: 45px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            color: #ffcc00;
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #000;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
            opacity: 0.8;
        }
        .nav-links a:hover { opacity: 1; }
        .user-badge {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #000;
        }
        .main-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #ffcc00;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .back-link:hover { color: #ffcc00; }
        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .team-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .team-card:hover {
            border-color: rgba(255, 204, 0, 0.4);
            transform: translateY(-2px);
        }
        .team-card h3 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .team-card .description {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .team-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .team-id {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #00ff88;
            font-size: 0.85rem;
        }
        .role-badge {
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-badge.owner {
            background: rgba(255, 204, 0, 0.2);
            color: #ffcc00;
        }
        .role-badge.admin {
            background: rgba(100, 200, 255, 0.2);
            color: #64c8ff;
        }
        .role-badge.member {
            background: rgba(100, 255, 100, 0.2);
            color: #64ff64;
        }
        .btn {
            display: inline-block;
            padding: 0.6rem 1.25rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            width: 100%;
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 204, 0, 0.3);
        }
        .no-teams {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">
            <div class="logo-icon">MTN</div>
            <div class="logo-text">Developers <span>Portal</span></div>
        </a>
        <nav class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="teams.php" style="color: #000; font-weight: bold;">My Teams</a>
            <a href="docs.php">Docs</a>
            <span class="user-badge">👤 <?= htmlspecialchars($user['username']) ?></span>
            <a href="logout.php" style="color: #600;">Logout</a>
        </nav>
    </header>

    <main class="main-content">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <div class="page-header">
            <h1>👥 My Teams</h1>
            <p>Teams you own or are a member of</p>
        </div>

        <?php if (empty($userTeams)): ?>
        <div class="no-teams">
            <h3>No teams yet</h3>
            <p>You're not a member of any teams.</p>
        </div>
        <?php else: ?>
        <div class="teams-grid">
            <?php foreach ($userTeams as $team): ?>
            <div class="team-card">
                <h3><?= htmlspecialchars($team['name']) ?></h3>
                <p class="description"><?= htmlspecialchars($team['description'] ?? 'No description') ?></p>
                <div class="team-meta">
                    <span class="team-id">ID: <?= htmlspecialchars($team['team_id']) ?></span>
                    <span class="role-badge <?= $team['member_role'] ?>">
                        <?= ucfirst($team['member_role']) ?>
                    </span>
                </div>
                <a href="team.php?team_id=<?= urlencode($team['team_id']) ?>" class="btn btn-primary">
                    Manage Team →
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
