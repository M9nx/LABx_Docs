<?php
/**
 * Lab 26: View Application Details
 */

require_once 'config.php';
requireLogin();

$appId = $_GET['id'] ?? 0;

// Secure query - only show user's own application
$stmt = $pdo->prepare("SELECT * FROM api_applications WHERE id = ? AND user_id = ?");
$stmt->execute([$appId, $_SESSION['user_id']]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: applications.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($application['name']); ?> - Pressable</title>
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
        .nav-links a {
            color: #aaa;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover { color: #00b4d8; }
        .user-badge {
            padding: 0.4rem 1rem;
            background: rgba(0, 180, 216, 0.2);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 20px;
            color: #00b4d8;
            font-size: 0.9rem;
        }
        .main-content {
            max-width: 900px;
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
            transition: color 0.3s;
        }
        .back-link:hover { color: #00b4d8; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 0.25rem;
        }
        .page-header p { color: #888; }
        .app-status {
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .app-status.active {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .detail-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .detail-card h2 {
            color: #00b4d8;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
        }
        .credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }
        .credential-label {
            color: #888;
            font-size: 0.85rem;
        }
        .credential-value {
            font-family: 'Consolas', monospace;
            color: #00b4d8;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .copy-btn {
            padding: 0.3rem 0.6rem;
            background: rgba(0, 180, 216, 0.2);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 4px;
            color: #00b4d8;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .copy-btn:hover {
            background: rgba(0, 180, 216, 0.3);
        }
        .secret-field {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .secret-value {
            font-family: 'Consolas', monospace;
        }
        .toggle-btn {
            padding: 0.3rem 0.6rem;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 4px;
            color: #888;
            font-size: 0.75rem;
            cursor: pointer;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #888; }
        .info-value { color: #fff; }
        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
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
        .btn-danger {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .actions-row {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .warning-box {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #ffaa00;
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
                <div class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="applications.php" class="back-link">← Back to Applications</a>
        
        <div class="page-header">
            <div>
                <h1><?php echo htmlspecialchars($application['name']); ?></h1>
                <p><?php echo htmlspecialchars($application['description'] ?? 'No description'); ?></p>
            </div>
            <span class="app-status <?php echo $application['status']; ?>">
                <?php echo $application['status']; ?>
            </span>
        </div>

        <div class="detail-card">
            <h2>🔐 API Credentials</h2>
            <div class="credential-row">
                <span class="credential-label">Application ID</span>
                <span class="credential-value">
                    <?php echo $application['id']; ?>
                    <button class="copy-btn" onclick="copyToClipboard('<?php echo $application['id']; ?>')">Copy</button>
                </span>
            </div>
            <div class="credential-row">
                <span class="credential-label">Client ID</span>
                <span class="credential-value">
                    <?php echo htmlspecialchars($application['client_id']); ?>
                    <button class="copy-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($application['client_id']); ?>')">Copy</button>
                </span>
            </div>
            <div class="credential-row">
                <span class="credential-label">Client Secret</span>
                <span class="credential-value">
                    <span class="secret-field">
                        <span class="secret-value" id="secretField">
                            <?php echo maskSecret($application['client_secret']); ?>
                        </span>
                        <button class="toggle-btn" onclick="toggleSecret()">Show</button>
                    </span>
                    <button class="copy-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($application['client_secret']); ?>')">Copy</button>
                </span>
            </div>
            <div class="warning-box">
                ⚠️ Never share your Client Secret. Treat it like a password.
            </div>
        </div>

        <div class="detail-card">
            <h2>📋 Application Details</h2>
            <div class="info-row">
                <span class="info-label">Redirect URI</span>
                <span class="info-value"><?php echo htmlspecialchars($application['redirect_uri'] ?? 'Not set'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Scopes</span>
                <span class="info-value"><?php echo htmlspecialchars($application['scopes'] ?? 'None'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Created</span>
                <span class="info-value"><?php echo formatDate($application['created_at']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Last Updated</span>
                <span class="info-value"><?php echo formatDate($application['updated_at']); ?></span>
            </div>
        </div>

        <div class="actions-row">
            <a href="update-application.php?id=<?php echo $application['id']; ?>" class="btn btn-primary">
                ✏️ Update Application
            </a>
            <a href="applications.php" class="btn btn-secondary">
                Back to List
            </a>
        </div>
    </main>

    <script>
        let secretVisible = false;
        const actualSecret = '<?php echo htmlspecialchars($application['client_secret']); ?>';
        const maskedSecret = '<?php echo maskSecret($application['client_secret']); ?>';
        
        function toggleSecret() {
            const field = document.getElementById('secretField');
            const btn = event.target;
            secretVisible = !secretVisible;
            field.textContent = secretVisible ? actualSecret : maskedSecret;
            btn.textContent = secretVisible ? 'Hide' : 'Show';
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                event.target.textContent = 'Copied!';
                setTimeout(() => event.target.textContent = 'Copy', 1500);
            });
        }
    </script>
</body>
</html>
