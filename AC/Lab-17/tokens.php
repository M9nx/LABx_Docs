<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = '';
$error = '';

// Handle token creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $name = $_POST['name'] ?? '';
        $scopes = $_POST['scopes'] ?? ['api'];
        
        if ($name) {
            // Generate token
            $token = 'glpat-' . bin2hex(random_bytes(20));
            
            $stmt = $pdo->prepare("INSERT INTO personal_access_tokens (user_id, name, token, scopes) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $token, implode(',', $scopes)]);
            
            $message = "Token created successfully! Your new token: <code style='background: rgba(0,0,0,0.4); padding: 0.3rem 0.6rem; border-radius: 4px; color: #88ff88;'>$token</code>";
        } else {
            $error = "Please provide a token name";
        }
    } elseif ($_POST['action'] === 'revoke') {
        $token_id = $_POST['token_id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM personal_access_tokens WHERE id = ? AND user_id = ?");
        $stmt->execute([$token_id, $user_id]);
        $message = "Token revoked successfully";
    }
}

// Get user's tokens
$stmt = $pdo->prepare("SELECT * FROM personal_access_tokens WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Access Tokens - GitLab</title>
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
        .container {
            max-width: 900px;
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
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card-header h2 { color: #fc6d26; font-size: 1.2rem; }
        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .message.success {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
            color: #66ff99;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #fc6d26;
        }
        .scopes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.5rem;
        }
        .scope-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
        }
        .scope-item input { accent-color: #fc6d26; }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: linear-gradient(135deg, #fc6d26, #e24329); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn-danger { background: rgba(255, 68, 68, 0.2); border: 1px solid rgba(255, 68, 68, 0.5); color: #ff8888; }
        .btn:hover { transform: translateY(-2px); }
        .token-list { list-style: none; }
        .token-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }
        .token-info h4 { color: #e0e0e0; margin-bottom: 0.25rem; }
        .token-value {
            font-family: monospace;
            background: rgba(0, 0, 0, 0.4);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
            font-size: 0.8rem;
        }
        .token-meta {
            font-size: 0.75rem;
            color: #888;
            margin-top: 0.25rem;
        }
        .token-scopes {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .scope-badge {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
        }
        .empty-state { text-align: center; padding: 2rem; color: #666; }
        .info-box {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .info-box h4 { color: #fc6d26; margin-bottom: 0.5rem; }
        .info-box p { color: #aaa; font-size: 0.85rem; line-height: 1.6; }
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
        <div class="page-header">
            <h1>🔑 Personal Access Tokens</h1>
            <p>Manage your API access tokens for authentication</p>
        </div>

        <?php if ($message): ?>
        <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Create Token -->
        <div class="card">
            <div class="card-header">
                <h2>➕ Add New Token</h2>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Token Name</label>
                    <input type="text" name="name" placeholder="e.g., API Testing Token" required>
                </div>
                <div class="form-group">
                    <label>Scopes</label>
                    <div class="scopes-grid">
                        <label class="scope-item">
                            <input type="checkbox" name="scopes[]" value="api" checked>
                            <span>api</span>
                        </label>
                        <label class="scope-item">
                            <input type="checkbox" name="scopes[]" value="read_api">
                            <span>read_api</span>
                        </label>
                        <label class="scope-item">
                            <input type="checkbox" name="scopes[]" value="read_repository">
                            <span>read_repository</span>
                        </label>
                        <label class="scope-item">
                            <input type="checkbox" name="scopes[]" value="write_repository">
                            <span>write_repository</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create Token</button>
            </form>
        </div>

        <!-- Active Tokens -->
        <div class="card">
            <div class="card-header">
                <h2>📋 Active Tokens</h2>
            </div>
            <?php if (empty($tokens)): ?>
                <div class="empty-state">No active tokens. Create one above!</div>
            <?php else: ?>
                <ul class="token-list">
                    <?php foreach ($tokens as $token): ?>
                    <li class="token-item">
                        <div class="token-info">
                            <h4><?php echo htmlspecialchars($token['name']); ?></h4>
                            <div class="token-value"><?php echo htmlspecialchars($token['token']); ?></div>
                            <div class="token-meta">
                                Created: <?php echo date('M d, Y H:i', strtotime($token['created_at'])); ?>
                            </div>
                            <div class="token-scopes">
                                <?php foreach (explode(',', $token['scopes']) as $scope): ?>
                                <span class="scope-badge"><?php echo htmlspecialchars(trim($scope)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="revoke">
                            <input type="hidden" name="token_id" value="<?php echo $token['id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Revoke this token?')">Revoke</button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="info-box">
                <h4>💡 Using Tokens with the API</h4>
                <p>
                    Include your token in API requests using the Authorization header:<br>
                    <code>Authorization: Bearer glpat-xxxxx...</code>
                </p>
                <p style="margin-top: 0.5rem;">
                    Test your token in the <a href="api-test.php" style="color: #fc6d26;">API Tester</a>!
                </p>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="api-test.php" class="btn btn-primary" style="margin-left: 1rem;">🧪 API Tester</a>
        </div>
    </div>
</body>
</html>
