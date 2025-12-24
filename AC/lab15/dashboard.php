<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['email'];

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's notes
$stmt = $pdo->prepare("SELECT * FROM user_notes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's campaigns
$stmt = $pdo->prepare("SELECT * FROM ad_campaigns WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get account settings
$stmt = $pdo->prepare("SELECT * FROM account_settings WHERE user_id = ?");
$stmt->execute([$userId]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MTN MobAd Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 204, 0, 0.3);
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
            color: #ffcc00;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }
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
        .nav-links a:hover { color: #ffcc00; }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 25px;
            font-size: 0.9rem;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .welcome-section {
            margin-bottom: 2rem;
        }
        .welcome-section h1 {
            color: #ffcc00;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .welcome-section p {
            color: #888;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-card .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2rem; color: #ffcc00; font-weight: bold; }
        .stat-card .label { color: #888; font-size: 0.9rem; }
        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 20px;
            padding: 1.5rem;
        }
        .section h2 {
            color: #ffcc00;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .profile-info {
            display: grid;
            gap: 0.75rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }
        .info-row .label { color: #888; }
        .info-row .value { color: #e0e0e0; font-family: monospace; }
        .info-row .value.sensitive { color: #ff8888; }
        .note-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.15);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }
        .note-item h4 {
            color: #ffcc00;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .note-item p {
            color: #aaa;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .note-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #666;
        }
        .note-type {
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.7rem;
            text-transform: uppercase;
        }
        .note-type.personal { background: rgba(0, 150, 255, 0.2); color: #66aaff; }
        .note-type.business { background: rgba(0, 200, 0, 0.2); color: #66ff66; }
        .note-type.confidential { background: rgba(255, 68, 68, 0.2); color: #ff6666; }
        .api-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .api-box h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .api-box p {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .btn-test-api {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        .btn-test-api:hover {
            transform: translateY(-2px);
        }
        .hint-box {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.4);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .hint-box h4 {
            color: #ffcc00;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .hint-box p {
            color: #aaa;
            font-size: 0.85rem;
        }
        .hint-box code {
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
                <span class="logo-icon">MTN</span>
                MobAd Platform
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="docs.php">Documentation</a>
                <span class="user-badge">👤 <?php echo htmlspecialchars($user['full_name']); ?></span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
            <p>Manage your mobile advertising campaigns and account settings</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📝</div>
                <div class="value"><?php echo count($notes); ?></div>
                <div class="label">Notes</div>
            </div>
            <div class="stat-card">
                <div class="icon">📢</div>
                <div class="value"><?php echo count($campaigns); ?></div>
                <div class="label">Campaigns</div>
            </div>
            <div class="stat-card">
                <div class="icon">👁️</div>
                <div class="value"><?php echo number_format(array_sum(array_column($campaigns, 'impressions'))); ?></div>
                <div class="label">Total Impressions</div>
            </div>
            <div class="stat-card">
                <div class="icon">🖱️</div>
                <div class="value"><?php echo number_format(array_sum(array_column($campaigns, 'clicks'))); ?></div>
                <div class="label">Total Clicks</div>
            </div>
        </div>

        <div class="section-grid">
            <div class="section">
                <h2>👤 Your Profile</h2>
                <div class="profile-info">
                    <div class="info-row">
                        <span class="label">Email</span>
                        <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Phone</span>
                        <span class="value sensitive"><?php echo htmlspecialchars($user['phone_number']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Address</span>
                        <span class="value sensitive"><?php echo htmlspecialchars($user['address']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Account Type</span>
                        <span class="value"><?php echo ucfirst($user['account_type']); ?></span>
                    </div>
                    <?php if ($user['company_name']): ?>
                    <div class="info-row">
                        <span class="label">Company</span>
                        <span class="value"><?php echo htmlspecialchars($user['company_name']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($user['tax_id']): ?>
                    <div class="info-row">
                        <span class="label">Tax ID</span>
                        <span class="value sensitive"><?php echo htmlspecialchars($user['tax_id']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="label">API Key</span>
                        <span class="value sensitive"><?php echo htmlspecialchars(substr($user['api_key'], 0, 20) . '...'); ?></span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>📝 Your Notes</h2>
                <?php if (empty($notes)): ?>
                    <p style="color: #888;">No notes yet.</p>
                <?php else: ?>
                    <?php foreach (array_slice($notes, 0, 4) as $note): ?>
                    <div class="note-item">
                        <h4><?php echo htmlspecialchars($note['title']); ?></h4>
                        <p><?php echo htmlspecialchars(substr($note['content'], 0, 150)); ?>...</p>
                        <div class="note-meta">
                            <span class="note-type <?php echo $note['note_type']; ?>"><?php echo $note['note_type']; ?></span>
                            <span><?php echo date('M j, Y', strtotime($note['created_at'])); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="api-box">
            <h3>🔌 API Endpoint - getUserNotes</h3>
            <p>
                Use this endpoint to fetch user notes programmatically. The API returns notes and account 
                information for the specified email address.
            </p>
            <div class="code-block">
POST /AC/lab15/api/getUserNotes.php HTTP/1.1
Content-Type: application/json

{
  "params": {
    "updates": [{
      "param": "user",
      "value": { "userEmail": "<?php echo htmlspecialchars($userEmail); ?>" },
      "op": "a"
    }]
  }
}
            </div>
            <a href="api-test.php" class="btn-test-api">🧪 Test API Endpoint</a>
            
            <div class="hint-box">
                <h4>💡 Hint for Lab Completion</h4>
                <p>
                    Try changing the <code>userEmail</code> parameter to another user's email address. 
                    What happens when you request notes for <code>victim1@mtnbusiness.com</code> or <code>ceo@bigcorp.ng</code>?
                </p>
            </div>
        </div>
    </div>
</body>
</html>
