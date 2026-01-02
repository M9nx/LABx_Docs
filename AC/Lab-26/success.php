<?php
/**
 * Lab 26: Success Page
 */

require_once 'config.php';
requireLogin();

// Check if user exploited the vulnerability
$stmt = $pdo->prepare("
    SELECT * FROM activity_log 
    WHERE user_id = ? AND action LIKE '%idor%exploit%'
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$exploit = $stmt->fetch();

$solved = !empty($exploit);

if ($solved) {
    require_once '../progress.php';
    markLabSolved(26);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Complete - Pressable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
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
            color: #00b4d8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #aaa; text-decoration: none; }
        .nav-links a:hover { color: #00b4d8; }
        .main-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 2rem;
            text-align: center;
        }
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #00c853, #00a040);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto 2rem;
            box-shadow: 0 20px 60px rgba(0, 200, 83, 0.3);
        }
        .pending-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #ff9800, #f57c00);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto 2rem;
            box-shadow: 0 20px 60px rgba(255, 152, 0, 0.3);
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #fff;
        }
        h1.success { color: #00c853; }
        h1.pending { color: #ff9800; }
        .subtitle {
            font-size: 1.1rem;
            color: #888;
            margin-bottom: 2rem;
        }
        .flag-box {
            background: rgba(0, 200, 83, 0.1);
            border: 2px solid rgba(0, 200, 83, 0.3);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .flag-label {
            color: #00c853;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }
        .flag-value {
            font-family: 'Consolas', monospace;
            font-size: 1.5rem;
            color: #00ff00;
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            word-break: break-all;
        }
        .exploit-details {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .exploit-details h3 {
            color: #00b4d8;
            margin-bottom: 1rem;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #888; }
        .detail-value { color: #fff; }
        .instructions {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .instructions h3 {
            color: #ffaa00;
            margin-bottom: 1rem;
        }
        .instructions ol {
            color: #ccc;
            padding-left: 1.5rem;
            line-height: 1.8;
        }
        .instructions code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            margin: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">⚡</span>
                Pressable
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="applications.php">API Apps</a>
                <a href="docs.php">Docs</a>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <?php if ($solved): ?>
        <div class="success-icon">🏆</div>
        <h1 class="success">Lab Completed!</h1>
        <p class="subtitle">You successfully exploited the IDOR vulnerability</p>
        
        <div class="flag-box">
            <div class="flag-label">🚩 YOUR FLAG</div>
            <div class="flag-value">FLAG{PRESSABLE_API_IDOR_CREDENTIAL_LEAK_<?php echo strtoupper(substr(md5($_SESSION['username']), 0, 8)); ?>}</div>
        </div>
        
        <div class="exploit-details">
            <h3>📊 Exploit Details</h3>
            <div class="detail-row">
                <span class="detail-label">Exploit Type</span>
                <span class="detail-value">IDOR + Information Disclosure</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Timestamp</span>
                <span class="detail-value"><?php echo formatDate($exploit['created_at']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Target Application ID</span>
                <span class="detail-value"><?php echo $exploit['target_id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Impact</span>
                <span class="detail-value" style="color: #ff6b6b;">Critical - Account Takeover via API Credentials</span>
            </div>
        </div>
        
        <?php else: ?>
        <div class="pending-icon">🔓</div>
        <h1 class="pending">Lab Not Completed Yet</h1>
        <p class="subtitle">Exploit the IDOR vulnerability to get the flag</p>
        
        <div class="instructions">
            <h3>📋 How to Exploit</h3>
            <ol>
                <li>Log in as <code>attacker</code> (password: <code>attacker123</code>)</li>
                <li>Go to <strong>API Apps</strong> → click <strong>Update</strong> on your application</li>
                <li>Open browser DevTools (F12) → Network tab</li>
                <li>Submit the update form</li>
                <li>Find the POST request to <code>update-application.php</code></li>
                <li>Edit and resend the request with:
                    <ul style="margin-top: 0.5rem;">
                        <li>Change <code>application[id]</code> to <code>2</code> (or 3, 4, etc.)</li>
                        <li>Remove <code>application[name]</code> parameter</li>
                        <li>Keep only <code>application[id]</code> and <code>authenticity_token</code></li>
                    </ul>
                </li>
                <li>The error response will leak the victim's <strong>Client ID</strong> and <strong>Client Secret</strong>!</li>
            </ol>
        </div>
        <?php endif; ?>
        
        <div>
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="../index.php" class="btn btn-primary">All Labs →</a>
        </div>
    </main>
</body>
</html>
