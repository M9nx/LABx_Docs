<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$project_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get project details with access check
$stmt = $pdo->prepare("
    SELECT p.*, u.username as owner_name,
           CASE WHEN p.owner_id = ? THEN 'Owner' 
                ELSE (SELECT access_level FROM project_members WHERE project_id = p.id AND user_id = ?) 
           END as user_access
    FROM projects p
    JOIN users u ON p.owner_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$user_id, $user_id, $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: dashboard.php");
    exit;
}

// Check if user has access (public project or member)
$hasAccess = $project['visibility'] === 'public' || $project['user_access'];

if (!$hasAccess) {
    header("Location: dashboard.php");
    exit;
}

// Get merge requests
$stmt = $pdo->prepare("
    SELECT mr.*, u.username as author_name
    FROM merge_requests mr
    JOIN users u ON mr.author_id = u.id
    WHERE mr.project_id = ?
    ORDER BY mr.created_at DESC
");
$stmt->execute([$project_id]);
$mergeRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get external status checks
$stmt = $pdo->prepare("
    SELECT esc.*, pb.name as branch_name
    FROM external_status_checks esc
    LEFT JOIN protected_branches pb ON esc.protected_branch_id = pb.id
    WHERE esc.project_id = ?
");
$stmt->execute([$project_id]);
$statusChecks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get project members
$stmt = $pdo->prepare("
    SELECT pm.*, u.username, u.full_name
    FROM project_members pm
    JOIN users u ON pm.user_id = u.id
    WHERE pm.project_id = ?
");
$stmt->execute([$project_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['name']); ?> - GitLab</title>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            color: #888;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .breadcrumb a { color: #fc6d26; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .project-header {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .project-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .project-title h1 { color: #e0e0e0; font-size: 1.8rem; }
        .visibility-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .visibility-badge.private { background: rgba(255, 68, 68, 0.2); color: #ff8888; }
        .visibility-badge.public { background: rgba(0, 200, 0, 0.2); color: #88ff88; }
        .project-meta { color: #888; font-size: 0.9rem; }
        .tabs {
            display: flex;
            gap: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }
        .tab {
            padding: 1rem 1.5rem;
            color: #888;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }
        .tab:hover { color: #e0e0e0; }
        .tab.active {
            color: #fc6d26;
            border-bottom-color: #fc6d26;
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
        .card-header h2 { color: #fc6d26; font-size: 1.2rem; }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #fc6d26, #e24329); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn:hover { transform: translateY(-2px); }
        .mr-list { list-style: none; }
        .mr-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.3s;
        }
        .mr-item:hover { background: rgba(252, 109, 38, 0.05); }
        .mr-item:last-child { border-bottom: none; }
        .mr-info h3 { font-size: 1rem; margin-bottom: 0.25rem; }
        .mr-info h3 a { color: #e0e0e0; text-decoration: none; }
        .mr-info h3 a:hover { color: #fc6d26; }
        .mr-meta { font-size: 0.8rem; color: #888; }
        .mr-status {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .mr-status.open { background: rgba(0, 200, 100, 0.2); color: #66ff99; }
        .mr-status.merged { background: rgba(138, 43, 226, 0.2); color: #bb88ff; }
        .status-check-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.2);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .status-check-item h4 { color: #fc6d26; margin-bottom: 0.5rem; }
        .status-check-item .details { font-size: 0.85rem; color: #888; }
        .status-check-item .id-badge {
            display: inline-block;
            background: rgba(252, 109, 38, 0.3);
            color: #fc6d26;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-family: monospace;
        }
        .member-list { display: flex; flex-wrap: wrap; gap: 1rem; }
        .member-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .member-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #fc6d26, #e24329);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .member-info .name { color: #e0e0e0; font-weight: 500; }
        .member-info .access { font-size: 0.75rem; color: #888; }
        .empty-state { text-align: center; padding: 2rem; color: #666; }
        .info-box {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .info-box p { color: #aaa; font-size: 0.85rem; }
        .info-box code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
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
                <a href="dashboard.php">Dashboard</a>
                <a href="api-test.php">🧪 API Tester</a>
                <a href="docs.php">📚 Docs</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a> / 
            <a href="project.php?id=<?php echo $project_id; ?>"><?php echo htmlspecialchars($project['name']); ?></a>
        </div>

        <div class="project-header">
            <div class="project-title">
                <h1><?php echo htmlspecialchars($project['name']); ?></h1>
                <span class="visibility-badge <?php echo $project['visibility']; ?>">
                    <?php echo ucfirst($project['visibility']); ?>
                </span>
            </div>
            <p class="project-meta">
                Owner: <?php echo htmlspecialchars($project['owner_name']); ?> • 
                Path: <?php echo htmlspecialchars($project['path']); ?> •
                Your access: <?php echo htmlspecialchars($project['user_access'] ?: 'Guest'); ?>
            </p>
        </div>

        <div class="tabs">
            <a href="#overview" class="tab active">Overview</a>
            <a href="#merge-requests" class="tab">Merge Requests (<?php echo count($mergeRequests); ?>)</a>
            <a href="#status-checks" class="tab">Status Checks (<?php echo count($statusChecks); ?>)</a>
            <a href="#members" class="tab">Members (<?php echo count($members); ?>)</a>
        </div>

        <!-- Merge Requests -->
        <div class="card">
            <div class="card-header">
                <h2>🔀 Merge Requests</h2>
            </div>
            <?php if (empty($mergeRequests)): ?>
                <div class="empty-state">No merge requests in this project</div>
            <?php else: ?>
                <ul class="mr-list">
                    <?php foreach ($mergeRequests as $mr): ?>
                    <li class="mr-item">
                        <div class="mr-info">
                            <h3>
                                <a href="merge-request.php?project_id=<?php echo $project_id; ?>&iid=<?php echo $mr['iid']; ?>">
                                    <?php echo htmlspecialchars($mr['title']); ?>
                                </a>
                            </h3>
                            <div class="mr-meta">
                                !<?php echo $mr['iid']; ?> • 
                                <?php echo htmlspecialchars($mr['source_branch']); ?> → <?php echo htmlspecialchars($mr['target_branch']); ?> • 
                                by <?php echo htmlspecialchars($mr['author_name']); ?>
                            </div>
                        </div>
                        <span class="mr-status <?php echo $mr['state']; ?>"><?php echo ucfirst($mr['state']); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- External Status Checks -->
        <div class="card">
            <div class="card-header">
                <h2>✅ External Status Checks</h2>
            </div>
            <?php if (empty($statusChecks)): ?>
                <div class="empty-state">No external status checks configured</div>
            <?php else: ?>
                <?php foreach ($statusChecks as $check): ?>
                <div class="status-check-item">
                    <h4>
                        <?php echo htmlspecialchars($check['name']); ?>
                        <span class="id-badge">ID: <?php echo $check['id']; ?></span>
                    </h4>
                    <div class="details">
                        <p>External URL: <?php echo htmlspecialchars($check['external_url']); ?></p>
                        <?php if ($check['branch_name']): ?>
                        <p>Protected Branch: <?php echo htmlspecialchars($check['branch_name']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="info-box">
                    <p>
                        💡 <strong>Tip:</strong> The Status Check ID (<code>external_status_check_id</code>) can be used 
                        with the API endpoint <code>/api/status_check_responses.php</code>. 
                        Try it in the <a href="api-test.php" style="color: #fc6d26;">API Tester</a>!
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Members -->
        <div class="card">
            <div class="card-header">
                <h2>👥 Project Members</h2>
            </div>
            <?php if (empty($members)): ?>
                <div class="empty-state">No additional members (only owner)</div>
            <?php else: ?>
                <div class="member-list">
                    <?php foreach ($members as $member): ?>
                    <div class="member-item">
                        <div class="member-avatar"><?php echo strtoupper($member['username'][0]); ?></div>
                        <div class="member-info">
                            <div class="name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                            <div class="access"><?php echo htmlspecialchars($member['access_level']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="api-test.php" class="btn btn-primary" style="margin-left: 1rem;">🧪 Test API</a>
        </div>
    </div>
</body>
</html>
