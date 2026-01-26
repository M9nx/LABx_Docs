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

// Fetch manager's data
$stmt = $conn->prepare("SELECT * FROM managers WHERE id = ?");
$stmt->bind_param("i", $managerId);
$stmt->execute();
$manager = $stmt->get_result()->fetch_assoc();

// Fetch clients (for admin: all clients, for manager: own clients only)
if ($isAdmin) {
    $clientsQuery = "SELECT c.*, m.username as manager_name FROM clients c 
                     JOIN managers m ON c.manager_id = m.id 
                     ORDER BY c.id";
    $clientsResult = $conn->query($clientsQuery);
} else {
    $clientsStmt = $conn->prepare("SELECT * FROM clients WHERE manager_id = ?");
    $clientsStmt->bind_param("i", $managerId);
    $clientsStmt->execute();
    $clientsResult = $clientsStmt->get_result();
}
$clients = $clientsResult->fetch_all(MYSQLI_ASSOC);

// Fetch campaigns for accessible clients
$clientIds = array_column($clients, 'id');
$campaignsData = [];
if (!empty($clientIds)) {
    $placeholders = implode(',', array_fill(0, count($clientIds), '?'));
    $types = str_repeat('i', count($clientIds));
    
    $campaignsStmt = $conn->prepare("SELECT * FROM campaigns WHERE client_id IN ($placeholders)");
    $campaignsStmt->bind_param($types, ...$clientIds);
    $campaignsStmt->execute();
    $campaignsData = $campaignsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Count banners per campaign
$bannerCounts = [];
foreach ($campaignsData as $camp) {
    $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM banners WHERE campaign_id = ?");
    $countStmt->bind_param("i", $camp['id']);
    $countStmt->execute();
    $bannerCounts[$camp['id']] = $countStmt->get_result()->fetch_assoc()['count'];
}

// Stats
$totalClients = count($clients);
$totalCampaigns = count($campaignsData);
$totalBanners = array_sum($bannerCounts);

// Get CSRF token
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Revive Adserver</title>
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
        .welcome-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
        }
        .welcome-bar h1 {
            color: #ff4444;
            font-size: 1.5rem;
        }
        .welcome-bar p {
            color: #888;
            margin-top: 0.3rem;
        }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .user-info span {
            display: block;
            color: #fff;
            font-weight: 600;
        }
        .user-info small {
            color: #888;
            font-size: 0.8rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        .stat-card:hover {
            border-color: #ff4444;
            transform: translateY(-3px);
        }
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff4444;
        }
        .stat-label {
            color: #888;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .section-title {
            color: #ff6666;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .clients-section {
            margin-bottom: 3rem;
        }
        .client-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .client-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: rgba(255, 68, 68, 0.1);
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .client-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
        }
        .client-meta {
            color: #888;
            font-size: 0.85rem;
        }
        .client-budget {
            color: #88ff88;
            font-weight: 600;
        }
        .campaigns-list {
            padding: 1rem;
        }
        .campaign-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }
        .campaign-item:hover {
            background: rgba(255, 68, 68, 0.1);
        }
        .campaign-info h4 {
            color: #e0e0e0;
            margin-bottom: 0.3rem;
        }
        .campaign-info small {
            color: #666;
        }
        .campaign-stats {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .campaign-stat {
            text-align: center;
        }
        .campaign-stat-value {
            display: block;
            color: #ff6666;
            font-weight: 600;
        }
        .campaign-stat-label {
            font-size: 0.7rem;
            color: #666;
            text-transform: uppercase;
        }
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active {
            background: rgba(0, 200, 0, 0.2);
            color: #00cc00;
        }
        .status-paused {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
        }
        .status-draft {
            background: rgba(100, 100, 100, 0.3);
            color: #888;
        }
        .btn-view {
            padding: 0.5rem 1rem;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.4);
            color: #ff6666;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-view:hover {
            background: rgba(255, 68, 68, 0.3);
        }
        .token-display {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.3);
            border-radius: 10px;
        }
        .token-display h4 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .token-display code {
            display: block;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 6px;
            color: #88ff88;
            font-family: monospace;
            margin-top: 0.5rem;
            word-break: break-all;
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
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="welcome-bar">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($manager['full_name']); ?>!</h1>
                <p>Agency: <?php echo htmlspecialchars($manager['agency']); ?> | Role: <?php echo ucfirst($manager['role']); ?></p>
            </div>
            <div class="user-badge">
                <div class="user-info">
                    <span>@<?php echo htmlspecialchars($manager['username']); ?></span>
                    <small>Manager ID: <?php echo $managerId; ?></small>
                </div>
                <div class="user-avatar">
                    <?php echo $isAdmin ? '👑' : '👤'; ?>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-value"><?php echo $totalClients; ?></div>
                <div class="stat-label">Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo $totalCampaigns; ?></div>
                <div class="stat-label">Campaigns</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🖼️</div>
                <div class="stat-value"><?php echo $totalBanners; ?></div>
                <div class="stat-label">Banners</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔑</div>
                <div class="stat-value" style="font-size: 1rem;"><?php echo substr($csrfToken, 0, 8); ?>...</div>
                <div class="stat-label">CSRF Token</div>
            </div>
        </div>

        <div class="clients-section">
            <h2 class="section-title">📋 Your Clients & Campaigns</h2>
            
            <?php foreach ($clients as $client): ?>
            <div class="client-card">
                <div class="client-header">
                    <div>
                        <div class="client-name"><?php echo htmlspecialchars($client['client_name']); ?></div>
                        <div class="client-meta">
                            Client ID: <?php echo $client['id']; ?> | 
                            <?php echo htmlspecialchars($client['company']); ?>
                            <?php if ($isAdmin): ?>
                                | Manager: <?php echo htmlspecialchars($client['manager_name'] ?? 'N/A'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="client-budget">
                        Budget: $<?php echo number_format($client['budget'], 2); ?>
                    </div>
                </div>
                <div class="campaigns-list">
                    <?php 
                    $clientCampaigns = array_filter($campaignsData, fn($c) => $c['client_id'] == $client['id']);
                    foreach ($clientCampaigns as $campaign): 
                    ?>
                    <div class="campaign-item">
                        <div class="campaign-info">
                            <h4><?php echo htmlspecialchars($campaign['campaign_name']); ?></h4>
                            <small>Campaign ID: <?php echo $campaign['id']; ?> | 
                                   <?php echo $campaign['start_date']; ?> to <?php echo $campaign['end_date']; ?></small>
                        </div>
                        <div class="campaign-stats">
                            <div class="campaign-stat">
                                <span class="campaign-stat-value"><?php echo $bannerCounts[$campaign['id']] ?? 0; ?></span>
                                <span class="campaign-stat-label">Banners</span>
                            </div>
                            <div class="campaign-stat">
                                <span class="campaign-stat-value"><?php echo number_format($campaign['total_impressions']); ?></span>
                                <span class="campaign-stat-label">Impressions</span>
                            </div>
                            <div class="campaign-stat">
                                <span class="campaign-stat-value"><?php echo number_format($campaign['total_clicks']); ?></span>
                                <span class="campaign-stat-label">Clicks</span>
                            </div>
                            <span class="status-badge status-<?php echo $campaign['status']; ?>">
                                <?php echo $campaign['status']; ?>
                            </span>
                            <a href="campaign-banners.php?clientid=<?php echo $client['id']; ?>&campaignid=<?php echo $campaign['id']; ?>" 
                               class="btn-view">View Banners →</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($clientCampaigns)): ?>
                    <div class="campaign-item" style="justify-content: center; color: #666;">
                        No campaigns for this client
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="token-display">
            <h4>🔐 Your Current CSRF Token</h4>
            <p style="color: #aaa; font-size: 0.9rem;">
                This token is used for delete operations. In a real attack, you would capture this from legitimate requests.
            </p>
            <code><?php echo htmlspecialchars($csrfToken); ?></code>
        </div>
    </div>
</body>
</html>
