<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$project_id = $_GET['project_id'] ?? 0;
$mr_iid = $_GET['iid'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: dashboard.php");
    exit;
}

// Get merge request
$stmt = $pdo->prepare("
    SELECT mr.*, u.username as author_name
    FROM merge_requests mr
    JOIN users u ON mr.author_id = u.id
    WHERE mr.project_id = ? AND mr.iid = ?
");
$stmt->execute([$project_id, $mr_iid]);
$mergeRequest = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mergeRequest) {
    header("Location: project.php?id=" . $project_id);
    exit;
}

// Get status checks for this project
$stmt = $pdo->prepare("
    SELECT esc.*, pb.name as branch_name
    FROM external_status_checks esc
    LEFT JOIN protected_branches pb ON esc.protected_branch_id = pb.id
    WHERE esc.project_id = ?
");
$stmt->execute([$project_id]);
$statusChecks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get status check responses for this MR
$stmt = $pdo->prepare("
    SELECT scr.*, esc.name as check_name
    FROM status_check_responses scr
    JOIN external_status_checks esc ON scr.external_status_check_id = esc.id
    WHERE scr.merge_request_id = ?
");
$stmt->execute([$mergeRequest['id']]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>!<?php echo $mr_iid; ?> - <?php echo htmlspecialchars($mergeRequest['title']); ?> - GitLab</title>
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
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .container {
            max-width: 1000px;
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
        .mr-header {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .mr-title {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .mr-title h1 {
            color: #e0e0e0;
            font-size: 1.5rem;
            flex: 1;
        }
        .mr-status {
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .mr-status.open { background: rgba(0, 200, 100, 0.2); color: #66ff99; }
        .mr-status.merged { background: rgba(138, 43, 226, 0.2); color: #bb88ff; }
        .mr-meta {
            display: flex;
            gap: 2rem;
            color: #888;
            font-size: 0.9rem;
        }
        .branch-flow {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .branch {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.85rem;
        }
        .arrow { color: #666; }
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
        .status-check-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }
        .status-check-item:last-child { margin-bottom: 0; }
        .check-info h4 { color: #e0e0e0; margin-bottom: 0.25rem; }
        .check-info .id-badge {
            display: inline-block;
            background: rgba(252, 109, 38, 0.3);
            color: #fc6d26;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-family: monospace;
            margin-left: 0.5rem;
        }
        .check-info .url { font-size: 0.8rem; color: #888; font-family: monospace; }
        .check-status {
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .check-status.pending { background: rgba(255, 170, 0, 0.2); color: #ffcc00; }
        .check-status.passed { background: rgba(0, 200, 100, 0.2); color: #66ff99; }
        .check-status.failed { background: rgba(255, 68, 68, 0.2); color: #ff8888; }
        .response-item {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .response-item h5 { color: #fc6d26; margin-bottom: 0.5rem; }
        .response-item pre {
            background: #0d0d0d;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .api-hint {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .api-hint h4 { color: #ff6666; margin-bottom: 0.5rem; }
        .api-hint p { color: #aaa; font-size: 0.85rem; line-height: 1.6; }
        .api-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
        }
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
        .empty-state { text-align: center; padding: 2rem; color: #666; }
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
            <a href="project.php?id=<?php echo $project_id; ?>"><?php echo htmlspecialchars($project['name']); ?></a> /
            Merge Request !<?php echo $mr_iid; ?>
        </div>

        <div class="mr-header">
            <div class="mr-title">
                <h1><?php echo htmlspecialchars($mergeRequest['title']); ?></h1>
                <span class="mr-status <?php echo $mergeRequest['state']; ?>"><?php echo ucfirst($mergeRequest['state']); ?></span>
            </div>
            <div class="mr-meta">
                <span>📝 !<?php echo $mr_iid; ?></span>
                <span>👤 <?php echo htmlspecialchars($mergeRequest['author_name']); ?></span>
                <span>📅 <?php echo date('M d, Y', strtotime($mergeRequest['created_at'])); ?></span>
            </div>
            <div class="branch-flow">
                <span class="branch"><?php echo htmlspecialchars($mergeRequest['source_branch']); ?></span>
                <span class="arrow">→</span>
                <span class="branch"><?php echo htmlspecialchars($mergeRequest['target_branch']); ?></span>
            </div>
        </div>

        <!-- Status Checks -->
        <div class="card">
            <div class="card-header">
                <h2>✅ External Status Checks</h2>
                <a href="api-test.php" class="btn btn-primary">🧪 Test API</a>
            </div>
            
            <?php if (empty($statusChecks)): ?>
                <div class="empty-state">No external status checks configured for this project</div>
            <?php else: ?>
                <?php foreach ($statusChecks as $check): ?>
                <?php 
                    $response = array_filter($responses, fn($r) => $r['external_status_check_id'] == $check['id']);
                    $response = reset($response);
                    $status = $response ? $response['status'] : 'pending';
                ?>
                <div class="status-check-item">
                    <div class="check-info">
                        <h4>
                            <?php echo htmlspecialchars($check['name']); ?>
                            <span class="id-badge">ID: <?php echo $check['id']; ?></span>
                        </h4>
                        <div class="url"><?php echo htmlspecialchars($check['external_url']); ?></div>
                    </div>
                    <span class="check-status <?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="api-hint">
                    <h4>🔓 Vulnerability Hint</h4>
                    <p>
                        Use the API endpoint to update status check responses. The <code>external_status_check_id</code> parameter 
                        is vulnerable to IDOR - try using status check IDs from <strong>other projects</strong> 
                        to leak sensitive information!
                    </p>
                    <p style="margin-top: 0.5rem;">
                        Current MR Info: <code>project_id=<?php echo $project_id; ?></code>, 
                        <code>merge_request_iid=<?php echo $mr_iid; ?></code>,
                        <code>sha=<?php echo htmlspecialchars($mergeRequest['sha']); ?></code>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($responses)): ?>
        <!-- Status Check Responses -->
        <div class="card">
            <div class="card-header">
                <h2>📋 Status Check Responses</h2>
            </div>
            <?php foreach ($responses as $response): ?>
            <div class="response-item">
                <h5><?php echo htmlspecialchars($response['check_name']); ?></h5>
                <pre><?php echo htmlspecialchars(json_encode(json_decode($response['response_data'] ?: '{}'), JSON_PRETTY_PRINT)); ?></pre>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div style="margin-top: 2rem;">
            <a href="project.php?id=<?php echo $project_id; ?>" class="btn btn-secondary">← Back to Project</a>
            <a href="api-test.php" class="btn btn-primary" style="margin-left: 1rem;">🧪 Exploit with API Tester</a>
        </div>
    </div>
</body>
</html>
