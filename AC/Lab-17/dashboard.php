<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's projects (owned + member)
$stmt = $pdo->prepare("
    SELECT DISTINCT p.*, 
           (SELECT COUNT(*) FROM merge_requests WHERE project_id = p.id) as mr_count,
           CASE WHEN p.owner_id = ? THEN 'Owner' ELSE pm.access_level END as user_access
    FROM projects p
    LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = ?
    WHERE p.owner_id = ? OR pm.user_id = ?
    ORDER BY p.updated_at DESC
");
$stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent merge requests for user's projects
$stmt = $pdo->prepare("
    SELECT mr.*, p.name as project_name, p.path as project_path, u.username as author_name
    FROM merge_requests mr
    JOIN projects p ON mr.project_id = p.id
    JOIN users u ON mr.author_id = u.id
    LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = ?
    WHERE p.owner_id = ? OR pm.user_id = ?
    ORDER BY mr.created_at DESC
    LIMIT 10
");
$stmt->execute([$user_id, $user_id, $user_id]);
$mergeRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get personal access tokens
$stmt = $pdo->prepare("SELECT * FROM personal_access_tokens WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GitLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
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
            font-size: 1.3rem;
            font-weight: bold;
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #fc6d26; }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #fc6d26, #e24329);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #fc6d26;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 1000px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card-header h2 {
            color: #fc6d26;
            font-size: 1.2rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #fc6d26, #e24329);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-2px); }
        .project-list {
            list-style: none;
        }
        .project-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.3s;
        }
        .project-item:hover { background: rgba(252, 109, 38, 0.05); }
        .project-item:last-child { border-bottom: none; }
        .project-info h3 {
            color: #e0e0e0;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        .project-info h3 a {
            color: inherit;
            text-decoration: none;
        }
        .project-info h3 a:hover { color: #fc6d26; }
        .project-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: #888;
        }
        .visibility-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }
        .visibility-badge.private {
            background: rgba(255, 68, 68, 0.2);
            color: #ff8888;
        }
        .visibility-badge.public {
            background: rgba(0, 200, 0, 0.2);
            color: #88ff88;
        }
        .access-badge {
            padding: 0.25rem 0.75rem;
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .mr-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .mr-item:last-child { border-bottom: none; }
        .mr-item a {
            color: #e0e0e0;
            text-decoration: none;
        }
        .mr-item a:hover { color: #fc6d26; }
        .mr-status {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }
        .mr-status.open { background: rgba(0, 200, 100, 0.2); color: #66ff99; }
        .mr-status.merged { background: rgba(138, 43, 226, 0.2); color: #bb88ff; }
        .mr-meta { font-size: 0.75rem; color: #666; margin-top: 0.25rem; }
        .token-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .token-item:last-child { border-bottom: none; }
        .token-name { color: #e0e0e0; }
        .token-value {
            font-family: monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
            font-size: 0.8rem;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .quick-action {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .quick-action:hover {
            background: rgba(252, 109, 38, 0.1);
            border-color: #fc6d26;
            color: #fc6d26;
        }
        .quick-action-icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                    <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                    <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                    <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
                </svg>
                GitLab
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="api-test.php">🧪 API Tester</a>
                <a href="tokens.php">🔑 Access Tokens</a>
                <a href="lab-description.php">📋 Lab Info</a>
                <a href="docs.php">📚 Docs</a>
            </nav>
            <div class="user-menu">
                <span><?php echo htmlspecialchars($username); ?></span>
                <div class="user-avatar"><?php echo strtoupper($username[0]); ?></div>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
            <p>Manage your projects, merge requests, and access tokens</p>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <!-- Projects -->
                <div class="card">
                    <div class="card-header">
                        <h2>📁 Your Projects</h2>
                    </div>
                    <?php if (empty($projects)): ?>
                        <div class="empty-state">No projects found</div>
                    <?php else: ?>
                        <ul class="project-list">
                            <?php foreach ($projects as $project): ?>
                            <li class="project-item">
                                <div class="project-info">
                                    <h3>
                                        <a href="project.php?id=<?php echo $project['id']; ?>">
                                            <?php echo htmlspecialchars($project['name']); ?>
                                        </a>
                                        <span class="visibility-badge <?php echo $project['visibility']; ?>">
                                            <?php echo ucfirst($project['visibility']); ?>
                                        </span>
                                    </h3>
                                    <div class="project-meta">
                                        <span>📂 <?php echo htmlspecialchars($project['path']); ?></span>
                                        <span>🔀 <?php echo $project['mr_count']; ?> MRs</span>
                                    </div>
                                </div>
                                <span class="access-badge"><?php echo htmlspecialchars($project['user_access']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Recent Merge Requests -->
                <div class="card">
                    <div class="card-header">
                        <h2>🔀 Recent Merge Requests</h2>
                    </div>
                    <?php if (empty($mergeRequests)): ?>
                        <div class="empty-state">No merge requests found</div>
                    <?php else: ?>
                        <?php foreach ($mergeRequests as $mr): ?>
                        <div class="mr-item">
                            <a href="merge-request.php?project_id=<?php echo $mr['project_id']; ?>&iid=<?php echo $mr['iid']; ?>">
                                <?php echo htmlspecialchars($mr['title']); ?>
                                <span class="mr-status <?php echo $mr['state']; ?>"><?php echo ucfirst($mr['state']); ?></span>
                            </a>
                            <div class="mr-meta">
                                !<?php echo $mr['iid']; ?> in <?php echo htmlspecialchars($mr['project_name']); ?> 
                                • by <?php echo htmlspecialchars($mr['author_name']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sidebar">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h2>⚡ Quick Actions</h2>
                    </div>
                    <div class="quick-actions">
                        <a href="api-test.php" class="quick-action">
                            <div class="quick-action-icon">🧪</div>
                            API Tester
                        </a>
                        <a href="tokens.php" class="quick-action">
                            <div class="quick-action-icon">🔑</div>
                            Tokens
                        </a>
                        <a href="lab-description.php" class="quick-action">
                            <div class="quick-action-icon">📋</div>
                            Lab Info
                        </a>
                        <a href="docs.php" class="quick-action">
                            <div class="quick-action-icon">📚</div>
                            Docs
                        </a>
                    </div>
                </div>

                <!-- Access Tokens -->
                <div class="card">
                    <div class="card-header">
                        <h2>🔑 Your API Tokens</h2>
                        <a href="tokens.php" class="btn btn-primary">Manage</a>
                    </div>
                    <?php if (empty($tokens)): ?>
                        <div class="empty-state">No tokens. <a href="tokens.php" style="color: #fc6d26;">Create one</a></div>
                    <?php else: ?>
                        <?php foreach (array_slice($tokens, 0, 3) as $token): ?>
                        <div class="token-item">
                            <span class="token-name"><?php echo htmlspecialchars($token['name']); ?></span>
                            <span class="token-value"><?php echo substr($token['token'], 0, 20); ?>...</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Vulnerability Hint -->
                <div class="card" style="border-color: rgba(255, 68, 68, 0.5);">
                    <div class="card-header">
                        <h2 style="color: #ff6666;">🔓 Vulnerability Hint</h2>
                    </div>
                    <p style="color: #aaa; font-size: 0.9rem; line-height: 1.6;">
                        The <code style="background: rgba(0,0,0,0.4); padding: 0.2rem 0.4rem; border-radius: 4px;">external_status_check_id</code> 
                        parameter in the API is not properly validated. Try accessing status checks from other projects!
                    </p>
                    <a href="api-test.php" class="btn btn-primary" style="display: block; text-align: center; margin-top: 1rem;">
                        🧪 Test API
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
