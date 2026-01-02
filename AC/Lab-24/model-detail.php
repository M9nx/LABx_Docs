<?php
require_once 'config.php';
requireLogin();

$gid = $_GET['gid'] ?? '';
$model = null;
$versions = [];
$params = [];
$metadata = [];
$metrics = [];
$error = '';

if (empty($gid)) {
    $error = 'No model GID provided';
} else {
    // Decode the GID
    $internalId = decodeModelGid($gid);
    
    if ($internalId === null) {
        $error = 'Invalid model GID format';
    } else {
        // VULNERABLE: No ownership check!
        $stmt = $pdo->prepare("
            SELECT m.*, p.name as project_name, p.path as project_path, p.visibility as project_visibility,
                   u.username as owner_name, u.id as owner_id
            FROM ml_models m
            JOIN projects p ON m.project_id = p.id
            JOIN users u ON m.owner_id = u.id
            WHERE m.internal_id = ?
        ");
        $stmt->execute([$internalId]);
        $model = $stmt->fetch();
        
        if (!$model) {
            $error = 'Model not found';
        } else {
            // Log access (for demonstration)
            logActivity($_SESSION['user_id'], 'view_model', 'ml_models', $model['id'], 
                "Accessed model: {$model['name']} (internal_id: {$internalId})");
            
            // Get versions
            $stmt = $pdo->prepare("SELECT * FROM model_versions WHERE model_id = ? ORDER BY version DESC");
            $stmt->execute([$model['id']]);
            $versions = $stmt->fetchAll();
            
            // Get parameters (hyperparameters)
            $stmt = $pdo->prepare("SELECT * FROM model_params WHERE model_id = ?");
            $stmt->execute([$model['id']]);
            $params = $stmt->fetchAll();
            
            // Get metadata
            $stmt = $pdo->prepare("SELECT * FROM model_metadata WHERE model_id = ?");
            $stmt->execute([$model['id']]);
            $metadata = $stmt->fetchAll();
            
            // Get metrics
            $stmt = $pdo->prepare("SELECT * FROM model_metrics WHERE model_id = ?");
            $stmt->execute([$model['id']]);
            $metrics = $stmt->fetchAll();
        }
    }
}

// Check if this is unauthorized access
$isUnauthorizedAccess = $model && $model['owner_id'] != $_SESSION['user_id'] && $model['visibility'] === 'private';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $model ? htmlspecialchars($model['name']) : 'Model Not Found'; ?> - ML Model Registry</title>
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
        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(252, 109, 38, 0.2);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            color: #fc6d26;
            font-size: 0.9rem;
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .error-container {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 3rem;
            text-align: center;
        }
        .error-container h2 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .error-container a {
            color: #fc6d26;
            text-decoration: none;
        }
        .vulnerability-alert {
            background: rgba(255, 68, 68, 0.15);
            border: 2px solid #ff6666;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .vulnerability-alert h3 {
            color: #ff6666;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vulnerability-alert p {
            color: #ccc;
            line-height: 1.5;
        }
        .vulnerability-alert code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
        }
        .model-header-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .model-title {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .model-title h1 {
            font-size: 2rem;
            color: #fc6d26;
        }
        .visibility-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .visibility-badge.public {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .visibility-badge.private {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .model-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .meta-item {
            color: #888;
            font-size: 0.9rem;
        }
        .meta-item span { color: #ccc; }
        .model-description {
            color: #aaa;
            line-height: 1.7;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gid-display {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-top: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }
        .gid-display .label { color: #666; }
        .gid-display .value { color: #88ff88; word-break: break-all; }
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .tab {
            padding: 0.6rem 1.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #888;
            cursor: pointer;
            transition: all 0.3s;
        }
        .tab:hover { border-color: #fc6d26; color: #fc6d26; }
        .tab.active {
            background: rgba(252, 109, 38, 0.2);
            border-color: #fc6d26;
            color: #fc6d26;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .info-card h3 {
            color: #fc6d26;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        table th {
            color: #888;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        table td {
            color: #ccc;
        }
        .param-value {
            font-family: 'Consolas', monospace;
            color: #88ff88;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .sensitive-value {
            color: #ff6666 !important;
            background: rgba(255, 68, 68, 0.1);
        }
        .version-card {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .version-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .version-number {
            font-weight: 600;
            color: #fc6d26;
        }
        .version-status {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
        }
        .version-status.production {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .version-status.staging {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .version-status.development {
            background: rgba(100, 100, 255, 0.2);
            color: #8888ff;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #fc6d26 0%, #e24329 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #00c853 0%, #00a844 100%);
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
                MLRegistry
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="models.php">My Models</a>
                <a href="docs.php">Docs</a>
                <a href="success.php">Submit Flag</a>
                <div class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php" style="color: #ff6666;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <?php if ($error): ?>
        <div class="error-container">
            <h2>❌ <?php echo htmlspecialchars($error); ?></h2>
            <p><a href="models.php">← Back to Models</a></p>
        </div>
        <?php else: ?>
        
        <?php if ($isUnauthorizedAccess): ?>
        <div class="vulnerability-alert">
            <h3>🚨 IDOR Vulnerability Exploited!</h3>
            <p>
                You are viewing a <strong>PRIVATE</strong> model owned by <strong><?php echo htmlspecialchars($model['owner_name']); ?></strong>.
                You should NOT have access to this model!
            </p>
            <p style="margin-top: 0.5rem;">
                This is the vulnerability: the API returned model data without checking if you're the owner.
                <br>Model: <code><?php echo htmlspecialchars($model['name']); ?></code> | 
                Owner: <code><?php echo htmlspecialchars($model['owner_name']); ?></code> |
                Internal ID: <code><?php echo $model['internal_id']; ?></code>
            </p>
            <p style="margin-top: 0.75rem;">
                <a href="success.php" class="btn btn-success" style="font-size: 0.9rem;">🏆 Submit this model name to complete the lab!</a>
            </p>
        </div>
        <?php endif; ?>

        <div class="model-header-section">
            <div class="model-title">
                <h1>🤖 <?php echo htmlspecialchars($model['name']); ?></h1>
                <span class="visibility-badge <?php echo $model['visibility']; ?>">
                    <?php echo $model['visibility']; ?>
                </span>
            </div>
            <div class="model-meta">
                <div class="meta-item">📁 Project: <span><?php echo htmlspecialchars($model['project_path']); ?></span></div>
                <div class="meta-item">👤 Owner: <span><?php echo htmlspecialchars($model['owner_name']); ?></span></div>
                <div class="meta-item">📅 Created: <span><?php echo date('M j, Y', strtotime($model['created_at'])); ?></span></div>
            </div>
            <?php if ($model['description']): ?>
            <div class="model-description">
                <?php echo nl2br(htmlspecialchars($model['description'])); ?>
            </div>
            <?php endif; ?>
            <div class="gid-display">
                <span class="label">GID: </span>
                <span class="value"><?php echo htmlspecialchars($gid); ?></span>
            </div>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="showTab('versions')">📊 Versions (<?php echo count($versions); ?>)</div>
            <div class="tab" onclick="showTab('params')">⚙️ Hyperparameters (<?php echo count($params); ?>)</div>
            <div class="tab" onclick="showTab('metadata')">📋 Metadata (<?php echo count($metadata); ?>)</div>
            <div class="tab" onclick="showTab('metrics')">📈 Metrics (<?php echo count($metrics); ?>)</div>
        </div>

        <div id="versions" class="tab-content active">
            <div class="info-card">
                <h3>Model Versions</h3>
                <?php if (empty($versions)): ?>
                <div class="empty-state">No versions available</div>
                <?php else: ?>
                <?php foreach ($versions as $version): ?>
                <div class="version-card">
                    <div class="version-header">
                        <span class="version-number">v<?php echo htmlspecialchars($version['version']); ?></span>
                        <span class="version-status <?php echo $version['status']; ?>"><?php echo $version['status']; ?></span>
                    </div>
                    <div style="color: #888; font-size: 0.85rem;">
                        Package: <?php echo htmlspecialchars($version['package_type']); ?> |
                        Artifact: <?php echo htmlspecialchars($version['artifact_path']); ?>
                    </div>
                    <?php if ($version['description']): ?>
                    <div style="color: #aaa; margin-top: 0.5rem; font-size: 0.9rem;">
                        <?php echo htmlspecialchars($version['description']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div id="params" class="tab-content">
            <div class="info-card">
                <h3>Hyperparameters</h3>
                <?php if (empty($params)): ?>
                <div class="empty-state">No hyperparameters defined</div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($params as $param): 
                            $isSensitive = stripos($param['param_name'], 'api_key') !== false || 
                                          stripos($param['param_name'], 'password') !== false ||
                                          stripos($param['param_name'], 'secret') !== false ||
                                          stripos($param['param_name'], 'token') !== false;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($param['param_name']); ?></td>
                            <td>
                                <span class="param-value <?php echo $isSensitive ? 'sensitive-value' : ''; ?>">
                                    <?php echo htmlspecialchars($param['param_value']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="metadata" class="tab-content">
            <div class="info-card">
                <h3>Metadata</h3>
                <?php if (empty($metadata)): ?>
                <div class="empty-state">No metadata available</div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metadata as $meta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($meta['meta_key']); ?></td>
                            <td><span class="param-value"><?php echo htmlspecialchars($meta['meta_value']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="metrics" class="tab-content">
            <div class="info-card">
                <h3>Model Metrics</h3>
                <?php if (empty($metrics)): ?>
                <div class="empty-state">No metrics recorded</div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Step</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metrics as $metric): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($metric['metric_name']); ?></td>
                            <td><span class="param-value"><?php echo htmlspecialchars($metric['metric_value']); ?></span></td>
                            <td><?php echo $metric['step']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        
        <?php endif; ?>
    </main>

    <script>
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
