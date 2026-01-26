<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user's organizations with role
$stmt = $pdo->prepare("
    SELECT o.*, om.role, om.joined_at,
           (SELECT COUNT(*) FROM org_members WHERE org_id = o.id) as member_count,
           (SELECT COUNT(*) FROM api_keys WHERE org_id = o.id) as key_count
    FROM organizations o
    JOIN org_members om ON o.id = om.org_id
    WHERE om.user_id = ?
    ORDER BY o.name
");
$stmt->execute([$_SESSION['user_id']]);
$organizations = $stmt->fetchAll();

// Get recent activity (API keys created/viewed)
$stmt = $pdo->prepare("
    SELECT ak.*, o.name as org_name
    FROM api_keys ak
    JOIN organizations o ON ak.org_id = o.id
    JOIN org_members om ON o.id = om.org_id
    WHERE om.user_id = ?
    ORDER BY ak.created_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recentKeys = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KeyVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
            padding: 1rem 2rem;
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-info {
            text-align: right;
        }
        .user-name { color: #5eead4; font-weight: 600; }
        .user-role { color: #64748b; font-size: 0.85rem; }
        .btn-logout {
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .welcome-section {
            margin-bottom: 2rem;
        }
        .welcome-section h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #f8fafc;
        }
        .welcome-section p {
            color: #94a3b8;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .card h2 {
            color: #5eead4;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .org-list { list-style: none; }
        .org-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .org-item:hover {
            background: rgba(20, 184, 166, 0.1);
            border-left: 3px solid #14b8a6;
        }
        .org-info h3 { color: #f8fafc; margin-bottom: 0.25rem; }
        .org-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #64748b;
        }
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        .role-owner {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
        }
        .role-admin {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: #fff;
        }
        .role-member {
            background: rgba(100, 116, 139, 0.3);
            color: #94a3b8;
        }
        .btn-view {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border: none;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .stats-row {
            display: flex;
            gap: 0.5rem;
        }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .recent-key {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }
        .recent-key .key-name {
            color: #f8fafc;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .recent-key .key-org {
            color: #64748b;
            font-size: 0.85rem;
        }
        .recent-key .key-time {
            color: #475569;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        .vuln-warning {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        .vuln-warning h4 {
            color: #fca5a5;
            margin-bottom: 0.5rem;
        }
        .vuln-warning p {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .vuln-warning code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.1rem 0.3rem;
            border-radius: 4px;
            color: #5eead4;
            font-size: 0.85rem;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        .empty-state i { font-size: 3rem; margin-bottom: 1rem; }
        .nav-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-btn.primary {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
        }
        .nav-btn.secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        @media (max-width: 900px) {
            .grid-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                    <div class="user-role">@<?php echo htmlspecialchars($_SESSION['username']); ?></div>
                </div>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="welcome-section">
            <h1>👋 Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
            <p>Manage your organizations and API keys securely</p>
        </div>

        <div class="grid-container">
            <div class="card">
                <h2>🏢 Your Organizations</h2>
                
                <?php if (empty($organizations)): ?>
                    <div class="empty-state">
                        <div>🏢</div>
                        <p>You're not a member of any organizations yet.</p>
                    </div>
                <?php else: ?>
                    <ul class="org-list">
                        <?php foreach ($organizations as $org): ?>
                            <li class="org-item">
                                <div class="org-info">
                                    <h3><?php echo htmlspecialchars($org['name']); ?></h3>
                                    <div class="org-meta">
                                        <span class="stat-item">👥 <?php echo $org['member_count']; ?> members</span>
                                        <span class="stat-item">🔐 <?php echo $org['key_count']; ?> API keys</span>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <span class="role-badge role-<?php echo $org['role']; ?>">
                                        <?php echo ucfirst($org['role']); ?>
                                    </span>
                                    <a href="organization.php?uuid=<?php echo urlencode($org['uuid']); ?>" class="btn-view">
                                        View →
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="nav-buttons">
                    <a href="lab-description.php" class="nav-btn secondary">📖 Lab Instructions</a>
                    <a href="docs.php" class="nav-btn secondary">📚 Documentation</a>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2>🔐 Recent API Keys</h2>
                    
                    <?php if (empty($recentKeys)): ?>
                        <div class="empty-state">
                            <p>No API keys found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentKeys as $key): ?>
                            <div class="recent-key">
                                <div class="key-name"><?php echo htmlspecialchars($key['name']); ?></div>
                                <div class="key-org"><?php echo htmlspecialchars($key['org_name']); ?></div>
                                <div class="key-time"><?php echo date('M j, Y', strtotime($key['created_at'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="vuln-warning">
                    <h4>⚠️ Vulnerability Hint</h4>
                    <p>
                        The API key management checks if you're a <code>member</code> of an organization,
                        but does it properly verify your <code>role</code> permissions?
                    </p>
                    <p style="margin-top: 0.5rem;">
                        Try accessing <code>api/keys.php</code> with different <code>org_uuid</code> values.
                    </p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
