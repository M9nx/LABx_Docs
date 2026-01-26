<?php
/**
 * Lab 27: Success Page
 * IDOR in Stats API Endpoint
 */

require_once 'config.php';
require_once '../progress.php';
requireLogin();

$pdo = getDBConnection();
$user = getCurrentUser($pdo);
$message = '';
$success = false;

// Check for successful exploit by looking at activity log
$stmt = $pdo->prepare("
    SELECT COUNT(*) as exploit_count 
    FROM activity_log 
    WHERE user_id = ? AND action = 'idor_exploit'
");
$stmt->execute([$_SESSION['user_id']]);
$exploitCount = $stmt->fetch()['exploit_count'];

// Flag submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedFlag = trim($_POST['flag'] ?? '');
    $discoveredData = trim($_POST['discovered_data'] ?? '');
    
    if ($submittedFlag === LAB_FLAG) {
        $success = true;
        markLabSolved(27);
        $message = "🎉 Congratulations! You've successfully exploited the IDOR vulnerability!";
        logActivity($pdo, $_SESSION['user_id'], 'flag_submitted', 'Correct flag submitted');
    } else {
        $message = "❌ Incorrect flag. Keep exploring the API endpoints!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Flag - <?php echo APP_NAME; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
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
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .nav-links a:hover { color: #ffd700; }
        .main-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #ffd700; }
        .success-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
        }
        .success-card h1 {
            color: #ffd700;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .stats-box {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .stats-box h3 {
            color: #ffd700;
            margin-bottom: 1rem;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .stat-item:last-child { border-bottom: none; }
        .stat-label { color: #888; }
        .stat-value { color: #00c853; font-weight: 600; }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #ffd700;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border: none;
            border-radius: 8px;
            color: #000;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .message-success {
            background: rgba(0, 200, 83, 0.2);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: #00c853;
        }
        .message-error {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .flag-reveal {
            background: linear-gradient(135deg, rgba(0, 200, 83, 0.2), rgba(0, 150, 60, 0.2));
            border: 2px solid #00c853;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 1.5rem;
        }
        .flag-reveal h2 {
            color: #00c853;
            margin-bottom: 1rem;
        }
        .flag-reveal code {
            display: block;
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 8px;
            color: #ffd700;
            font-family: monospace;
            font-size: 1.1rem;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">
                <span class="logo-icon">📈</span>
                Exness PA
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="performance.php">Performance</a>
                <a href="docs.php">Docs</a>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <div class="success-card">
            <h1>🏆 Submit Your Flag</h1>
            <p>Successfully exploited the IDOR vulnerability? Submit the flag below!</p>

            <?php if ($message): ?>
            <div class="message <?php echo $success ? 'message-success' : 'message-error'; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <div class="stats-box">
                <h3>📊 Your Progress</h3>
                <div class="stat-item">
                    <span class="stat-label">IDOR Exploits Detected</span>
                    <span class="stat-value"><?php echo $exploitCount; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Accounts Accessed</span>
                    <span class="stat-value">
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT requested_account) FROM api_logs WHERE user_id = ? AND is_idor_attempt = 1");
                        $stmt->execute([$_SESSION['user_id']]);
                        echo $stmt->fetchColumn();
                        ?>
                    </span>
                </div>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="discovered_data">What sensitive data did you discover? (Optional)</label>
                    <textarea id="discovered_data" name="discovered_data" rows="3" 
                              placeholder="e.g., Victim's account equity is $92,750..."></textarea>
                </div>
                <div class="form-group">
                    <label for="flag">Enter the Flag</label>
                    <input type="text" id="flag" name="flag" 
                           placeholder="FLAG{...}" required>
                </div>
                <button type="submit" class="btn-submit">Submit Flag 🚀</button>
            </form>

            <?php if ($success): ?>
            <div class="flag-reveal">
                <h2>🎉 Lab Completed!</h2>
                <p style="color: #ccc; margin-bottom: 1rem;">
                    You've successfully demonstrated the IDOR vulnerability by accessing 
                    other users' trading statistics through the Stats API endpoints.
                </p>
                <code><?php echo LAB_FLAG; ?></code>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
