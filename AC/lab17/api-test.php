<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's tokens for the dropdown
$stmt = $pdo->prepare("SELECT * FROM personal_access_tokens WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's projects
$stmt = $pdo->prepare("
    SELECT DISTINCT p.* 
    FROM projects p
    LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = ?
    WHERE p.owner_id = ? OR pm.user_id = ?
    ORDER BY p.name
");
$stmt->execute([$user_id, $user_id, $user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get ALL status checks (for enumeration reference)
$stmt = $pdo->query("SELECT esc.*, p.name as project_name, p.visibility FROM external_status_checks esc JOIN projects p ON esc.project_id = p.id ORDER BY esc.id");
$allStatusChecks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester - GitLab</title>
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
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 1000px) {
            .grid { grid-template-columns: 1fr; }
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
        }
        .card-header {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card-header h2 { color: #fc6d26; font-size: 1.2rem; }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.6rem 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 6px;
            color: #e0e0e0;
            font-size: 0.9rem;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #fc6d26;
        }
        .vulnerable-param {
            border-color: #ff6666 !important;
            background: rgba(255, 68, 68, 0.1) !important;
        }
        .param-hint {
            font-size: 0.75rem;
            color: #ff8888;
            margin-top: 0.25rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-primary { background: linear-gradient(135deg, #fc6d26, #e24329); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn:hover { transform: translateY(-2px); }
        .response-area {
            margin-top: 1.5rem;
        }
        .response-area h3 { color: #fc6d26; margin-bottom: 0.75rem; }
        .response-box {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.8rem;
            color: #88ff88;
            max-height: 400px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .response-box.error { color: #ff8888; }
        .status-checks-ref {
            max-height: 300px;
            overflow-y: auto;
        }
        .status-check-ref {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .status-check-ref:hover { background: rgba(252, 109, 38, 0.1); }
        .status-check-ref.private { border-left: 3px solid #ff6666; }
        .status-check-ref.public { border-left: 3px solid #66ff99; }
        .check-id {
            background: rgba(252, 109, 38, 0.3);
            color: #fc6d26;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.8rem;
        }
        .check-name { color: #e0e0e0; }
        .check-project { font-size: 0.75rem; color: #888; }
        .visibility-badge {
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.65rem;
        }
        .visibility-badge.private { background: rgba(255, 68, 68, 0.2); color: #ff8888; }
        .visibility-badge.public { background: rgba(0, 200, 0, 0.2); color: #88ff88; }
        .attack-hint {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .attack-hint h4 { color: #ff6666; margin-bottom: 0.5rem; }
        .attack-hint p { color: #aaa; font-size: 0.85rem; line-height: 1.6; }
        .attack-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
            color: #fc6d26;
        }
        .solve-btn {
            margin-top: 1rem;
            background: linear-gradient(135deg, #00c853, #00e676);
        }
        .solve-btn:disabled {
            background: #444;
            cursor: not-allowed;
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
                <a href="tokens.php">🔑 Tokens</a>
                <a href="lab-description.php">📋 Lab Info</a>
                <a href="docs.php">📚 Docs</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>🧪 API Tester</h1>
            <p>Test the status_check_responses API endpoint and exploit the IDOR vulnerability</p>
        </div>

        <div class="attack-hint">
            <h4>🎯 Attack Objective</h4>
            <p>
                The <code>external_status_check_id</code> parameter is vulnerable to IDOR. 
                Use YOUR project's credentials but change the status check ID to access 
                <strong>private project configurations</strong>. Look for status checks marked as 
                <span style="color: #ff8888;">🔒 PRIVATE</span> in the reference below.
            </p>
        </div>

        <div class="grid">
            <div>
                <div class="card">
                    <div class="card-header">
                        <h2>📤 API Request Builder</h2>
                    </div>
                    
                    <form id="apiForm">
                        <div class="form-group">
                            <label>Authorization Token</label>
                            <select name="token" id="token" required>
                                <option value="">Select a token...</option>
                                <?php foreach ($tokens as $token): ?>
                                <option value="<?php echo htmlspecialchars($token['token']); ?>">
                                    <?php echo htmlspecialchars($token['name']); ?> (<?php echo substr($token['token'], 0, 15); ?>...)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($tokens)): ?>
                            <p class="param-hint">No tokens! <a href="tokens.php" style="color: #fc6d26;">Create one first</a></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Project ID (Your project)</label>
                            <select name="project_id" id="project_id" required>
                                <option value="">Select your project...</option>
                                <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>">
                                    <?php echo htmlspecialchars($project['name']); ?> (ID: <?php echo $project['id']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Merge Request IID</label>
                            <input type="number" name="merge_request_iid" id="merge_request_iid" value="1" required>
                        </div>

                        <div class="form-group">
                            <label>SHA (commit hash)</label>
                            <input type="text" name="sha" id="sha" value="abc123def456" required>
                        </div>

                        <div class="form-group">
                            <label>⚠️ External Status Check ID <span style="color: #ff6666;">(VULNERABLE!)</span></label>
                            <input type="number" name="external_status_check_id" id="external_status_check_id" 
                                   class="vulnerable-param" value="1" required>
                            <p class="param-hint">Change this to access status checks from OTHER projects!</p>
                        </div>

                        <button type="submit" class="btn btn-primary">🚀 Send Request</button>
                    </form>

                    <div class="response-area">
                        <h3>📥 Response</h3>
                        <div class="loading">Sending request...</div>
                        <div id="responseBox" class="response-box">Response will appear here...</div>
                        <button id="solveBtn" class="btn solve-btn" style="display: none;" onclick="window.location.href='success.php'">
                            🎉 Lab Solved! Click to Complete
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <div class="card-header">
                        <h2>📋 Status Check Reference</h2>
                    </div>
                    <p style="color: #888; font-size: 0.85rem; margin-bottom: 1rem;">
                        Click on a status check to copy its ID. Try accessing <span style="color: #ff8888;">PRIVATE</span> ones!
                    </p>
                    <div class="status-checks-ref">
                        <?php foreach ($allStatusChecks as $check): ?>
                        <div class="status-check-ref <?php echo $check['visibility']; ?>" 
                             onclick="document.getElementById('external_status_check_id').value='<?php echo $check['id']; ?>'">
                            <div>
                                <span class="check-id">ID: <?php echo $check['id']; ?></span>
                                <span class="check-name"><?php echo htmlspecialchars($check['name']); ?></span>
                                <span class="visibility-badge <?php echo $check['visibility']; ?>">
                                    <?php echo $check['visibility'] === 'private' ? '🔒 PRIVATE' : '🌐 PUBLIC'; ?>
                                </span>
                                <div class="check-project"><?php echo htmlspecialchars($check['project_name']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card" style="margin-top: 1.5rem;">
                    <div class="card-header">
                        <h2>💡 Exploitation Tips</h2>
                    </div>
                    <ol style="color: #aaa; font-size: 0.85rem; line-height: 1.8; padding-left: 1.5rem;">
                        <li>Login as <code style="background: rgba(0,0,0,0.4); padding: 0.1rem 0.3rem; border-radius: 3px;">attacker01</code></li>
                        <li>Create an API token in the <a href="tokens.php" style="color: #fc6d26;">Tokens</a> page</li>
                        <li>Use YOUR project ID (ID: 3 or 4) in the request</li>
                        <li>Change <code style="background: rgba(0,0,0,0.4); padding: 0.1rem 0.3rem; border-radius: 3px;">external_status_check_id</code> to a PRIVATE project's check</li>
                        <li>Look for <code style="color: #ff8888;">"cross_project_access": "YES"</code> in the response</li>
                        <li>You've exploited IDOR when you see private project info!</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary" style="display: inline-block; width: auto;">← Back to Dashboard</a>
        </div>
    </div>

    <script>
        document.getElementById('apiForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const responseBox = document.getElementById('responseBox');
            const loading = document.querySelector('.loading');
            const solveBtn = document.getElementById('solveBtn');
            
            loading.style.display = 'block';
            responseBox.textContent = '';
            solveBtn.style.display = 'none';
            
            const token = document.getElementById('token').value;
            const formData = new FormData();
            formData.append('project_id', document.getElementById('project_id').value);
            formData.append('merge_request_iid', document.getElementById('merge_request_iid').value);
            formData.append('sha', document.getElementById('sha').value);
            formData.append('external_status_check_id', document.getElementById('external_status_check_id').value);
            
            try {
                const response = await fetch('api/status_check_responses.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    body: formData
                });
                
                const data = await response.json();
                loading.style.display = 'none';
                
                responseBox.className = 'response-box' + (response.ok ? '' : ' error');
                responseBox.textContent = JSON.stringify(data, null, 2);
                
                // Check if exploit was successful
                if (data._exploit_success || (data._debug && data._debug.cross_project_access === 'YES - IDOR DETECTED!')) {
                    solveBtn.style.display = 'block';
                }
            } catch (error) {
                loading.style.display = 'none';
                responseBox.className = 'response-box error';
                responseBox.textContent = 'Error: ' + error.message;
            }
        });
    </script>
</body>
</html>
