<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$response = null;
$pollId = $_POST['poll_id'] ?? '';
$action = $_POST['action'] ?? 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pollId) {
    // Make internal request to API
    $url = "http://localhost/AC/lab16/api/slowvote.php?action={$action}&poll_id={$pollId}&output=json";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Cookie: PHPSESSID=" . session_id()
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    if ($result) {
        $response = json_decode($result, true);
    }
}

// Get list of polls for reference
$stmt = $pdo->query("SELECT id, title, visibility FROM slowvotes ORDER BY id");
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Tester - Phabricator Slowvote</title>
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
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
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
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
        }
        .nav-links a:hover { color: #9370DB; }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #9370DB;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .page-title p { color: #888; }
        .api-form {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .api-form h2 {
            color: #9370DB;
            margin-bottom: 1.5rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #9370DB;
        }
        .quick-polls {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .quick-poll {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .quick-poll.public {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }
        .quick-poll.private {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
        }
        .quick-poll.specific {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            color: #ffaa00;
        }
        .quick-poll:hover { transform: scale(1.05); }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
        }
        .btn:hover { transform: translateY(-2px); }
        .request-preview {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
        }
        .response-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }
        .response-section h2 {
            color: #9370DB;
            margin-bottom: 1rem;
        }
        .response-json {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
        }
        .exploit-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .exploit-banner h3 {
            color: #00ff00;
            margin-bottom: 0.5rem;
        }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">P</span>
                Phabricator
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="docs.php">Docs</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>🧪 Slowvote API Tester</h1>
            <p>Test the API endpoint to exploit the IDOR vulnerability. Try accessing private polls!</p>
        </div>

        <?php if ($response && isset($response['_security_note']['exploit_detected'])): ?>
        <div class="exploit-banner">
            <h3>🎉 Lab Solved!</h3>
            <p>You successfully accessed a restricted poll via the API!</p>
            <a href="success.php?poll_id=<?php echo $pollId; ?>" class="btn btn-secondary" style="margin-top: 1rem;">View Success Page →</a>
        </div>
        <?php endif; ?>

        <div class="api-form">
            <h2>📤 API Request</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Action</label>
                        <select name="action">
                            <option value="info" <?php echo $action === 'info' ? 'selected' : ''; ?>>info - Get poll details</option>
                            <option value="list" <?php echo $action === 'list' ? 'selected' : ''; ?>>list - List all polls</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Poll ID</label>
                        <input type="number" name="poll_id" value="<?php echo htmlspecialchars($pollId); ?>" placeholder="Enter poll ID">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Quick Select Poll:</label>
                    <div class="quick-polls">
                        <?php foreach ($polls as $poll): ?>
                        <span class="quick-poll <?php echo $poll['visibility'] === 'everyone' ? 'public' : ($poll['visibility'] === 'specific' ? 'specific' : 'private'); ?>"
                              onclick="document.querySelector('input[name=poll_id]').value='<?php echo $poll['id']; ?>'">
                            V<?php echo $poll['id']; ?>: <?php echo htmlspecialchars(substr($poll['title'], 0, 25)); ?>...
                            (<?php echo $poll['visibility']; ?>)
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="request-preview">
                    <strong>Request Preview:</strong><br><br>
                    POST /api/slowvote.php HTTP/1.1<br>
                    Host: localhost<br>
                    Cookie: PHPSESSID=<?php echo session_id(); ?><br><br>
                    action=<span id="preview-action"><?php echo $action; ?></span>&poll_id=<span id="preview-poll"><?php echo $pollId ?: '?'; ?></span>
                </div>
                
                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">🚀 Send Request</button>
            </form>
        </div>

        <?php if ($response): ?>
        <div class="response-section">
            <h2>📥 API Response</h2>
            <div class="response-json">
                <pre><?php echo htmlspecialchars(json_encode($response, JSON_PRETTY_PRINT)); ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
        </div>
    </div>

    <script>
        document.querySelector('select[name=action]').addEventListener('change', function() {
            document.getElementById('preview-action').textContent = this.value;
        });
        document.querySelector('input[name=poll_id]').addEventListener('input', function() {
            document.getElementById('preview-poll').textContent = this.value || '?';
        });
    </script>
</body>
</html>
