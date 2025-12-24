<?php
session_start();
require_once 'config.php';

// Check authentication
if (!isset($_SESSION['manager_id'])) {
    header("Location: login.php");
    exit;
}

$managerId = $_SESSION['manager_id'];
$isAdmin = $_SESSION['role'] === 'admin';
$csrfToken = $_SESSION['csrf_token'] ?? '';

// Get parameters
$clientId = intval($_GET['clientid'] ?? 0);
$campaignId = intval($_GET['campaignid'] ?? 0);

if (!$clientId || !$campaignId) {
    header("Location: dashboard.php");
    exit;
}

// Verify access to client (this check exists)
if (!$isAdmin) {
    $clientCheck = $conn->prepare("SELECT id FROM clients WHERE id = ? AND manager_id = ?");
    $clientCheck->bind_param("ii", $clientId, $managerId);
    $clientCheck->execute();
    if ($clientCheck->get_result()->num_rows === 0) {
        die("Unauthorized: You don't have access to this client.");
    }
}

// Verify access to campaign (this check exists)
$campaignCheck = $conn->prepare("SELECT * FROM campaigns WHERE id = ? AND client_id = ?");
$campaignCheck->bind_param("ii", $campaignId, $clientId);
$campaignCheck->execute();
$campaign = $campaignCheck->get_result()->fetch_assoc();

if (!$campaign) {
    die("Unauthorized: Campaign not found or doesn't belong to this client.");
}

// Get client info
$clientStmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$clientStmt->bind_param("i", $clientId);
$clientStmt->execute();
$client = $clientStmt->get_result()->fetch_assoc();

// Get banners for this campaign
$bannersStmt = $conn->prepare("SELECT * FROM banners WHERE campaign_id = ? ORDER BY id");
$bannersStmt->bind_param("i", $campaignId);
$bannersStmt->execute();
$banners = $bannersStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Check for success/error messages
$message = '';
$messageType = '';
if (isset($_GET['deleted'])) {
    $message = "Banner '{$_GET['deleted']}' has been successfully deleted.";
    $messageType = 'success';
}
if (isset($_GET['error'])) {
    $message = htmlspecialchars($_GET['error']);
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Banners - Revive Adserver</title>
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
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 2rem;
            color: #888;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: #ff6666;
            text-decoration: none;
        }
        .breadcrumb a:hover { text-decoration: underline; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
        }
        .page-header h1 {
            color: #ff4444;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .page-header p {
            color: #888;
        }
        .campaign-meta {
            text-align: right;
        }
        .campaign-meta .id-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            border-radius: 5px;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .message {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .message.success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #66ff66;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
        }
        .info-banner {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-banner h4 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .info-banner p {
            color: #aaa;
            font-size: 0.9rem;
        }
        .info-banner code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
        }
        .banners-table {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: rgba(255, 68, 68, 0.2);
        }
        th, td {
            padding: 1rem 1.2rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 68, 68, 0.15);
        }
        th {
            color: #ff6666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        tbody tr {
            transition: background 0.3s;
        }
        tbody tr:hover {
            background: rgba(255, 68, 68, 0.08);
        }
        .banner-id {
            font-family: monospace;
            color: #ff6666;
            font-weight: 600;
        }
        .banner-type {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 15px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .type-image { background: rgba(0, 150, 255, 0.2); color: #66aaff; }
        .type-html { background: rgba(255, 100, 0, 0.2); color: #ffaa66; }
        .type-video { background: rgba(150, 0, 255, 0.2); color: #cc88ff; }
        .type-native { background: rgba(0, 200, 100, 0.2); color: #66cc88; }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 15px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .status-active { background: rgba(0, 200, 0, 0.2); color: #00cc00; }
        .status-paused { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .status-pending { background: rgba(100, 100, 100, 0.3); color: #888; }
        .stats-cell {
            font-size: 0.85rem;
            color: #aaa;
        }
        .stats-cell strong {
            color: #fff;
        }
        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }
        .btn-action {
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-delete {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.4);
            color: #ff6666;
        }
        .btn-delete:hover {
            background: rgba(255, 68, 68, 0.3);
        }
        .btn-deactivate {
            background: rgba(255, 170, 0, 0.2);
            border: 1px solid rgba(255, 170, 0, 0.4);
            color: #ffaa00;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        .url-hint {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
        }
        .url-hint h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .url-hint code {
            display: block;
            padding: 1rem;
            background: #0d0d0d;
            border-radius: 6px;
            color: #88ff88;
            font-family: monospace;
            font-size: 0.85rem;
            overflow-x: auto;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📢 Revive Adserver</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="docs.php">Documentation</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a>
            <span>›</span>
            <span><?php echo htmlspecialchars($client['client_name']); ?></span>
            <span>›</span>
            <span><?php echo htmlspecialchars($campaign['campaign_name']); ?></span>
            <span>›</span>
            <span>Banners</span>
        </div>

        <div class="page-header">
            <div>
                <h1>🖼️ <?php echo htmlspecialchars($campaign['campaign_name']); ?></h1>
                <p>Manage banners for this campaign</p>
            </div>
            <div class="campaign-meta">
                <div class="id-badge">Client ID: <?php echo $clientId; ?></div>
                <div class="id-badge">Campaign ID: <?php echo $campaignId; ?></div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $messageType === 'success' ? '✅' : '⚠️'; ?> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="info-banner">
            <h4>⚠️ Security Notice</h4>
            <p>
                The delete links contain a CSRF token: <code>token=<?php echo htmlspecialchars($csrfToken); ?></code>
                <br>In Burp Suite, observe the request parameters when clicking Delete. Note how the URL contains 
                <code>clientid</code>, <code>campaignid</code>, and <code>bannerid</code>.
            </p>
        </div>

        <div class="banners-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Banner Name</th>
                        <th>Type</th>
                        <th>Dimensions</th>
                        <th>Performance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($banners)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">No banners found for this campaign</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td class="banner-id">#<?php echo $banner['id']; ?></td>
                        <td><?php echo htmlspecialchars($banner['banner_name']); ?></td>
                        <td>
                            <span class="banner-type type-<?php echo $banner['banner_type']; ?>">
                                <?php echo $banner['banner_type']; ?>
                            </span>
                        </td>
                        <td><?php echo $banner['width']; ?>x<?php echo $banner['height']; ?></td>
                        <td class="stats-cell">
                            <strong><?php echo number_format($banner['impressions']); ?></strong> imp / 
                            <strong><?php echo number_format($banner['clicks']); ?></strong> clicks
                            (<?php echo $banner['ctr']; ?>% CTR)
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $banner['status']; ?>">
                                <?php echo $banner['status']; ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="banner-delete.php?token=<?php echo urlencode($csrfToken); ?>&clientid=<?php echo $clientId; ?>&campaignid=<?php echo $campaignId; ?>&bannerid=<?php echo $banner['id']; ?>" 
                               class="btn-action btn-delete"
                               onclick="return confirm('Are you sure you want to delete this banner?');">
                                🗑️ Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="url-hint">
            <h4>🔓 Exploit Hint: IDOR Attack URL Structure</h4>
            <p style="color: #aaa; font-size: 0.9rem; margin-bottom: 0.5rem;">
                To exploit the IDOR, craft a URL with YOUR valid clientid and campaignid, but ANOTHER manager's bannerid:
            </p>
            <code>banner-delete.php?token=<?php echo htmlspecialchars($csrfToken); ?>&clientid=<?php echo $clientId; ?>&campaignid=<?php echo $campaignId; ?>&bannerid=<span style="color: #ff6666;">[VICTIM_BANNER_ID]</span></code>
            <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem;">
                Manager B's banner IDs are: <strong style="color: #ff6666;">6, 7, 8, 9, 10, 11</strong>
            </p>
        </div>
    </div>
</body>
</html>
