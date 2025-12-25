<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$store_name = $_SESSION['store_name'] ?? 'My Store';

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all users for hint display
$stmt = $pdo->query("SELECT id, username, store_name FROM users ORDER BY id");
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get active sessions count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_sessions WHERE user_id = ? AND is_active = 1");
$stmt->execute([$user_id]);
$sessionCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Shopify Admin</title>
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
            border-bottom: 1px solid rgba(150, 191, 72, 0.3);
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
            color: #96bf48;
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
        .nav-links a:hover { color: #96bf48; }
        .nav-links a.active { color: #96bf48; font-weight: 600; }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            color: #888;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .breadcrumb a { color: #96bf48; text-decoration: none; }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #96bf48;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(150, 191, 72, 0.3);
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
            color: #96bf48;
            font-size: 1.2rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #888; }
        .info-value { color: #e0e0e0; font-weight: 500; }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .warning-box h4 { color: #ff6666; margin-bottom: 0.5rem; }
        .warning-box p { color: #ccc; font-size: 0.9rem; }
        .hint-box {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .hint-box h4 { color: #ffaa00; margin-bottom: 0.5rem; }
        .hint-box p { color: #ccc; font-size: 0.9rem; line-height: 1.6; }
        .hint-box code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .user-list {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 0.75rem;
        }
        .user-list-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-size: 0.85rem;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .form-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-section h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .hidden-field-display {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            margin: 1rem 0;
        }
        .hidden-field-display .highlight { color: #ff6666; }
        .success-message {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            color: #00ff00;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">
                <svg viewBox="0 0 109 124" fill="none">
                    <path d="M74.7 14.8L62.2 55.4H46.7L34.2 14.8C33.1 11 29.5 8.3 25.5 8.3H0L31.5 115.5H40.8L54.5 67.8L68.2 115.5H77.5L109 8.3H83.5C79.5 8.3 75.8 11 74.7 14.8Z" fill="#96bf48"/>
                </svg>
                Shopify Admin
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="account-settings.php" class="active">Settings</a>
                <a href="sessions.php">Sessions</a>
            </nav>
            <div class="user-menu">
                <span><?php echo htmlspecialchars($store_name); ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <a href="logout.php" style="color:#ff6666;">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a> / Account Settings
        </div>

        <div class="page-header">
            <h1>⚙️ Account Settings</h1>
            <p>Manage your account security and session settings</p>
        </div>

        <div id="message-container"></div>

        <div class="card">
            <div class="card-header">
                <h2>👤 Account Information</h2>
            </div>
            <div class="info-row">
                <span class="info-label">Username</span>
                <span class="info-value"><?php echo htmlspecialchars($username); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Store Name</span>
                <span class="info-value"><?php echo htmlspecialchars($store_name); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Account ID</span>
                <span class="info-value" style="color:#96bf48;font-weight:bold;"><?php echo $user_id; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Active Sessions</span>
                <span class="info-value"><?php echo $sessionCount; ?> device(s)</span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>🔐 Security - Session Management</h2>
            </div>
            
            <p style="color:#aaa;margin-bottom:1rem;">
                Expire all active sessions for this account. This will log you out from all devices.
            </p>

            <div class="warning-box">
                <h4>⚠️ Security Action</h4>
                <p>This action will invalidate all active sessions and require re-authentication on all devices.</p>
            </div>

            <div class="form-section">
                <h3>🎯 Expire All Sessions</h3>
                
                <div class="hidden-field-display">
                    <span style="color:#666;">// Hidden form field - visible in DevTools:</span><br>
                    &lt;input type="hidden" name="<span class="highlight">account_id</span>" value="<span class="highlight"><?php echo $user_id; ?></span>"&gt;
                </div>

                <form id="expireForm" onsubmit="return expireSessions(event);">
                    <input type="hidden" name="account_id" id="account_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="action" value="expire_all">
                    <button type="submit" class="btn-danger">🔓 Expire All Sessions</button>
                </form>
            </div>

            <div class="hint-box">
                <h4>💡 Lab Hint</h4>
                <p>
                    The form above contains a hidden <code>account_id</code> field set to your ID (<strong><?php echo $user_id; ?></strong>).
                    Use browser DevTools (F12) or Burp Suite to intercept and modify this value.
                    Try changing it to <strong>2</strong> (victim_store's ID) to expire their sessions!
                </p>
                <div class="user-list">
                    <strong style="color:#96bf48;">All User IDs:</strong>
                    <?php foreach ($allUsers as $u): ?>
                    <div class="user-list-item">
                        <span><?php echo htmlspecialchars($u['username']); ?></span>
                        <span style="color:#88ff88;">ID: <?php echo $u['id']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function expireSessions(e) {
        e.preventDefault();
        
        const form = document.getElementById('expireForm');
        const formData = new FormData(form);
        
        fetch('api/expire_sessions.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('message-container');
            if (data.success) {
                if (data.lab_complete) {
                    container.innerHTML = `
                        <div class="success-message">
                            <h3>🎉 IDOR Exploit Successful!</h3>
                            <p>${data.message}</p>
                            <p style="margin-top:0.5rem;"><a href="success.php" style="color:#00ff00;">View Lab Completion →</a></p>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="success-message">
                            <p>✓ ${data.message}</p>
                        </div>
                    `;
                }
            } else {
                container.innerHTML = `
                    <div class="warning-box">
                        <p>❌ ${data.error || 'An error occurred'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
        
        return false;
    }
    </script>
</body>
</html>
