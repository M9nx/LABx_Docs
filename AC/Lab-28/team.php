<?php
/**
 * Lab 28: Team Management Page
 * MTN Developers Portal - Contains the VULNERABLE remove member functionality
 */

require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser($pdo);
$teamId = $_GET['team_id'] ?? '';

if (empty($teamId)) {
    header('Location: teams.php');
    exit;
}

$team = getTeamByTeamId($pdo, $teamId);
if (!$team) {
    header('Location: teams.php');
    exit;
}

$members = getTeamMembers($pdo, $teamId);
$userRole = getUserTeamRole($pdo, $teamId, $user['user_id']);
$isOwnerOrAdmin = in_array($userRole, ['owner', 'admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($team['name']) ?> - MTN Developers Portal</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .back-link:hover { color: #ffcc00; }
        .team-header {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .team-header h1 {
            color: #ffcc00;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .team-header p {
            color: #888;
            margin-bottom: 1rem;
        }
        .team-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .meta-item {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }
        .meta-item label {
            font-size: 0.75rem;
            color: #888;
            display: block;
        }
        .meta-item code {
            color: #00ff88;
            font-family: 'Consolas', monospace;
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
        }
        .members-table {
            width: 100%;
            border-collapse: collapse;
        }
        .members-table th {
            text-align: left;
            padding: 0.75rem;
            color: #888;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .members-table td {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .members-table tr:last-child td { border-bottom: none; }
        .members-table tr:hover td {
            background: rgba(255, 204, 0, 0.05);
        }
        .member-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .member-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
        }
        .member-details h4 {
            color: #fff;
            font-size: 0.95rem;
            margin-bottom: 0.15rem;
        }
        .member-details p {
            color: #888;
            font-size: 0.8rem;
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
        .btn-remove {
            padding: 0.4rem 0.8rem;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.4);
            color: #ff6b6b;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-remove:hover {
            background: rgba(255, 68, 68, 0.3);
        }
        .btn-remove:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
            color: #44ff44;
        }
        .alert-error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
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
        .hint-box p, .hint-box li {
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
        .hint-box ul {
            margin-left: 1.5rem;
            margin-top: 0.5rem;
        }
        #response-output {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #e6edf3;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
            display: none;
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
            <a href="teams.php">My Teams</a>
            <a href="docs.php">Docs</a>
            <span class="user-badge">👤 <?= htmlspecialchars($user['username']) ?></span>
            <a href="logout.php" style="color: #600;">Logout</a>
        </nav>
    </header>

    <main class="main-content">
        <a href="teams.php" class="back-link">← Back to My Teams</a>

        <div class="team-header">
            <h1>👥 <?= htmlspecialchars($team['name']) ?></h1>
            <p><?= htmlspecialchars($team['description'] ?? 'No description provided') ?></p>
            <div class="team-meta">
                <div class="meta-item">
                    <label>Team ID</label>
                    <code><?= htmlspecialchars($team['team_id']) ?></code>
                </div>
                <div class="meta-item">
                    <label>Your Role</label>
                    <code><?= $userRole ? ucfirst($userRole) : 'Not a member' ?></code>
                </div>
                <div class="meta-item">
                    <label>Members</label>
                    <code><?= count($members) ?></code>
                </div>
            </div>
        </div>

        <div id="alert-container"></div>

        <div class="card">
            <h2>Team Members</h2>
            
            <table class="members-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>User ID</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                    <tr>
                        <td>
                            <div class="member-info">
                                <div class="member-avatar">
                                    <?= strtoupper(substr($member['username'], 0, 1)) ?>
                                </div>
                                <div class="member-details">
                                    <h4><?= htmlspecialchars($member['full_name']) ?></h4>
                                    <p>@<?= htmlspecialchars($member['username']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td><code style="color: #00ff88;"><?= htmlspecialchars($member['user_id']) ?></code></td>
                        <td><span class="role-badge <?= $member['team_role'] ?>"><?= ucfirst($member['team_role']) ?></span></td>
                        <td style="color: #888; font-size: 0.85rem;">
                            <?= date('M j, Y', strtotime($member['joined_at'])) ?>
                        </td>
                        <td>
                            <?php if ($member['team_role'] !== 'owner'): ?>
                            <button class="btn-remove" 
                                    onclick="removeMember('<?= $team['team_id'] ?>', '<?= $member['user_id'] ?>')"
                                    <?= !$isOwnerOrAdmin ? 'title="You need owner/admin role to remove members"' : '' ?>>
                                Remove
                            </button>
                            <?php else: ?>
                            <span style="color: #666; font-size: 0.8rem;">Owner</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="hint-box">
                <h4>🎯 IDOR Attack Instructions</h4>
                <p>The "Remove" button sends a POST request to <code>/api/remove_member.php</code> with:</p>
                <ul>
                    <li><code>team_id</code> - The team to remove from</li>
                    <li><code>user_id</code> - The user to remove</li>
                </ul>
                <p style="margin-top: 0.5rem;">
                    <strong>Vulnerability:</strong> The API does NOT verify if you have permission to remove users from the specified team!
                </p>
                <p style="margin-top: 0.5rem;">
                    <strong>Try this:</strong> Use Burp Suite to intercept a remove request, then change 
                    <code>team_id</code> to <code>0002</code> and <code>user_id</code> to <code>1113</code> 
                    to remove Carol from Bob's team without permission!
                </p>
            </div>

            <div id="response-output"></div>
        </div>
    </main>

    <script>
        function removeMember(teamId, userId) {
            if (!confirm(`Are you sure you want to remove user ${userId} from team ${teamId}?`)) {
                return;
            }

            const responseOutput = document.getElementById('response-output');
            responseOutput.style.display = 'block';
            responseOutput.textContent = 'Sending request...';

            fetch('api/remove_member.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `team_id=${encodeURIComponent(teamId)}&user_id=${encodeURIComponent(userId)}`
            })
            .then(response => response.json())
            .then(data => {
                responseOutput.textContent = JSON.stringify(data, null, 2);
                
                const alertContainer = document.getElementById('alert-container');
                if (data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success">
                            ✅ ${data.message}<br>
                            <strong>Removed:</strong> ${data.removed_user.full_name} (${data.removed_user.username})<br>
                            <strong>From Team:</strong> ${data.from_team.name}
                        </div>
                    `;
                    // Reload page after 2 seconds to show updated member list
                    setTimeout(() => location.reload(), 2000);
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-error">
                            ❌ ${data.error}
                            ${data.user_name ? `<br><strong>User:</strong> ${data.user_name}` : ''}
                            ${data.team_name ? `<br><strong>Team:</strong> ${data.team_name}` : ''}
                        </div>
                    `;
                }
            })
            .catch(error => {
                responseOutput.textContent = 'Error: ' + error.message;
            });
        }
    </script>
</body>
</html>
