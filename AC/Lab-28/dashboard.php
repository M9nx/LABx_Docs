<?php
/**
 * Lab 28: Dashboard
 * MTN Developers Portal - Team Management
 */

require_once 'config.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser($pdo);
$userTeams = getUserTeams($pdo, $user['user_id']);
$pendingInvitations = getPendingInvitations($pdo, $user['user_id']);
$recentActivity = getUserActivity($pdo, $user['user_id'], 10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MTN Developers Portal</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .welcome-section {
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .welcome-section h1 {
            color: #ffcc00;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .welcome-section p { color: #aaa; }
        .user-info {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .user-info-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
        }
        .user-info-item label {
            font-size: 0.75rem;
            color: #888;
            display: block;
            margin-bottom: 0.25rem;
        }
        .user-info-item code {
            color: #00ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.95rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 900px) {
            .grid-2 { grid-template-columns: 1fr; }
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #ffcc00;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 204, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .team-list { list-style: none; }
        .team-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            margin-bottom: 0.75rem;
            transition: all 0.3s;
        }
        .team-item:hover {
            background: rgba(255, 204, 0, 0.1);
            border-color: rgba(255, 204, 0, 0.3);
        }
        .team-info h3 {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        .team-info p {
            color: #888;
            font-size: 0.85rem;
        }
        .team-meta {
            display: flex;
            gap: 0.5rem;
            align-items: center;
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
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 204, 0, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #ccc;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .activity-list { list-style: none; }
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-item .time {
            color: #666;
            font-size: 0.75rem;
        }
        .activity-item .action {
            color: #ffcc00;
        }
        .hint-box {
            background: rgba(255, 100, 100, 0.1);
            border: 1px solid rgba(255, 100, 100, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .hint-box h4 {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .hint-box p {
            color: #ccc;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .hint-box code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
        .no-items {
            text-align: center;
            padding: 2rem;
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
            <a href="dashboard.php" style="color: #000; font-weight: bold;">Dashboard</a>
            <a href="teams.php">My Teams</a>
            <a href="docs.php">Docs</a>
            <span class="user-badge">👤 <?= htmlspecialchars($user['username']) ?></span>
            <a href="logout.php" style="color: #600;">Logout</a>
        </nav>
    </header>

    <main class="main-content">
        <section class="welcome-section">
            <h1>Welcome back, <?= htmlspecialchars($user['full_name']) ?>! 👋</h1>
            <p>Manage your teams and API integrations from the MTN Developers Portal</p>
            
            <div class="user-info">
                <div class="user-info-item">
                    <label>Your User ID</label>
                    <code><?= htmlspecialchars($user['user_id']) ?></code>
                </div>
                <div class="user-info-item">
                    <label>Username</label>
                    <code><?= htmlspecialchars($user['username']) ?></code>
                </div>
                <div class="user-info-item">
                    <label>Email</label>
                    <code><?= htmlspecialchars($user['email']) ?></code>
                </div>
                <div class="user-info-item">
                    <label>API Key</label>
                    <code><?= htmlspecialchars(substr($user['api_key'], 0, 12)) ?>...</code>
                </div>
            </div>
        </section>

        <div class="grid-2">
            <div>
                <div class="card">
                    <h2>👥 Your Teams</h2>
                    
                    <?php if (empty($userTeams)): ?>
                    <div class="no-items">
                        <p>You're not a member of any teams yet.</p>
                    </div>
                    <?php else: ?>
                    <ul class="team-list">
                        <?php foreach ($userTeams as $team): ?>
                        <li class="team-item">
                            <div class="team-info">
                                <h3><?= htmlspecialchars($team['name']) ?></h3>
                                <p>Team ID: <code><?= htmlspecialchars($team['team_id']) ?></code></p>
                            </div>
                            <div class="team-meta">
                                <span class="role-badge <?= $team['member_role'] ?>">
                                    <?= ucfirst($team['member_role']) ?>
                                </span>
                                <a href="team.php?team_id=<?= urlencode($team['team_id']) ?>" class="btn btn-primary">
                                    Manage →
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <div class="hint-box">
                        <h4>🎯 Attack Hint</h4>
                        <p>
                            Your user_id is <code><?= $user['user_id'] ?></code>. When you remove a member from your team, 
                            observe the <code>team_id</code> and <code>user_id</code> parameters in the request. 
                            What happens if you change them to IDs that don't belong to you?
                        </p>
                    </div>
                </div>

                <?php if (!empty($pendingInvitations)): ?>
                <div class="card">
                    <h2>📩 Pending Invitations</h2>
                    <ul class="team-list">
                        <?php foreach ($pendingInvitations as $invite): ?>
                        <li class="team-item">
                            <div class="team-info">
                                <h3><?= htmlspecialchars($invite['team_name']) ?></h3>
                                <p>Invited by: <?= htmlspecialchars($invite['inviter_name']) ?></p>
                            </div>
                            <div class="team-meta">
                                <button class="btn btn-primary">Accept</button>
                                <button class="btn btn-secondary">Decline</button>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="card">
                    <h2>📋 Recent Activity</h2>
                    
                    <?php if (empty($recentActivity)): ?>
                    <div class="no-items">
                        <p>No recent activity.</p>
                    </div>
                    <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($recentActivity as $activity): ?>
                        <li class="activity-item">
                            <span class="action"><?= htmlspecialchars($activity['action_type']) ?></span>
                            <p><?= htmlspecialchars($activity['details']) ?></p>
                            <span class="time"><?= date('M j, g:i A', strtotime($activity['created_at'])) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>🎯 Target Information</h2>
                    <p style="color: #888; font-size: 0.9rem; margin-bottom: 1rem;">
                        Key IDs to know for the attack:
                    </p>
                    <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                        <p><strong>Team B (Bob's Team):</strong></p>
                        <p>team_id: <code style="color: #ff6b6b;">0002</code></p>
                        <p style="margin-top: 0.5rem;"><strong>Carol (Target User):</strong></p>
                        <p>user_id: <code style="color: #ff6b6b;">1113</code></p>
                        <p style="margin-top: 1rem; color: #ff6b6b;">
                            Goal: Remove Carol from Team B without having permissions!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
