<?php
/**
 * Lab 27: Dashboard
 * IDOR in Stats API Endpoint - Exness-style Trading Platform
 */

require_once 'config.php';
requireLogin();

$pdo = getDBConnection();
$user = getCurrentUser($pdo);
$accounts = getUserAccounts($pdo, $_SESSION['user_id']);

// Calculate totals
$totalBalance = 0;
$totalEquity = 0;
foreach ($accounts as $account) {
    $totalBalance += $account['balance'];
    $totalEquity += $account['equity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
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
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
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
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ffd700; }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 8px;
        }
        .user-info .avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
        }
        .user-info .details {
            line-height: 1.3;
        }
        .user-info .name { color: #fff; font-weight: 600; font-size: 0.9rem; }
        .user-info .pa-id { color: #888; font-size: 0.75rem; }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        .page-header p { color: #888; }
        
        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .summary-card:hover {
            border-color: rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }
        .summary-card .icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        .summary-card .label {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        .summary-card .value {
            font-size: 1.75rem;
            font-weight: bold;
            color: #ffd700;
        }
        .summary-card .change {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        .summary-card .change.positive {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .summary-card .change.negative {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6b6b;
        }

        /* Accounts Table */
        .section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .section-header h2 {
            color: #fff;
            font-size: 1.25rem;
        }
        .accounts-table {
            width: 100%;
            border-collapse: collapse;
        }
        .accounts-table th {
            text-align: left;
            padding: 1rem;
            color: #888;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
        }
        .accounts-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .accounts-table tr:hover {
            background: rgba(255, 215, 0, 0.05);
        }
        .account-number {
            font-family: monospace;
            color: #ffd700;
            font-weight: 600;
        }
        .account-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .status-active {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn:hover { transform: translateY(-2px); }

        /* Hint Box */
        .hint-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .hint-box h3 {
            color: #ff6b6b;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .hint-box p {
            color: #ffaaaa;
            line-height: 1.7;
            margin-bottom: 0.5rem;
        }
        .hint-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #ffd700;
            font-family: monospace;
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
                <a href="dashboard.php" style="color: #ffd700;">Dashboard</a>
                <a href="performance.php">Performance</a>
                <a href="docs.php">Docs</a>
                <div class="user-info">
                    <div class="avatar"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>
                    <div class="details">
                        <div class="name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        <div class="pa-id"><?php echo htmlspecialchars($user['pa_id']); ?></div>
                    </div>
                </div>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
            <p>Here's an overview of your trading accounts</p>
        </div>

        <?php if ($user['username'] === 'attacker'): ?>
        <div class="hint-box">
            <h3>🎯 Attack Hint</h3>
            <p>
                You're logged in as the <strong>attacker</strong>. Your goal is to view other users' 
                trading statistics (equity, net profit, trading volume) using the Stats API.
            </p>
            <p>
                Check the <a href="performance.php" style="color: #ffd700;">Performance page</a> and 
                observe how the stats API works. The vulnerable endpoints use an <code>accounts</code> 
                parameter that can be manipulated.
            </p>
            <p>
                <strong>Target accounts to enumerate:</strong><br>
                <code>MT5-200001</code>, <code>MT5-200002</code> (Victim's high-value accounts)<br>
                <code>MT5-300001</code>, <code>MT5-300002</code> (Whale's massive accounts)
            </p>
        </div>
        <?php endif; ?>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="icon">💰</div>
                <div class="label">Total Balance</div>
                <div class="value"><?php echo formatMoney($totalBalance); ?></div>
                <span class="change positive">+2.5% this month</span>
            </div>
            <div class="summary-card">
                <div class="icon">📊</div>
                <div class="label">Total Equity</div>
                <div class="value"><?php echo formatMoney($totalEquity); ?></div>
                <span class="change positive">+3.2% this month</span>
            </div>
            <div class="summary-card">
                <div class="icon">🏦</div>
                <div class="label">Trading Accounts</div>
                <div class="value"><?php echo count($accounts); ?></div>
            </div>
            <div class="summary-card">
                <div class="icon">✅</div>
                <div class="label">Account Status</div>
                <div class="value" style="color: #00c853;">Verified</div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>📈 Your Trading Accounts</h2>
                <a href="performance.php" class="btn btn-primary">View Performance →</a>
            </div>
            
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Platform</th>
                        <th>Balance</th>
                        <th>Equity</th>
                        <th>Leverage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td>
                            <span class="account-number"><?php echo htmlspecialchars($account['account_number']); ?></span>
                        </td>
                        <td>
                            <span class="account-type-badge" style="background: <?php echo getAccountTypeBadge($account['account_type']); ?>20; color: <?php echo getAccountTypeBadge($account['account_type']); ?>;">
                                <?php echo htmlspecialchars($account['account_type']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($account['platform']); ?></td>
                        <td style="font-weight: 600;"><?php echo formatMoney($account['balance'], $account['currency']); ?></td>
                        <td style="color: #00c853; font-weight: 600;"><?php echo formatMoney($account['equity'], $account['currency']); ?></td>
                        <td><?php echo htmlspecialchars($account['leverage']); ?></td>
                        <td>
                            <span class="status-badge status-active">
                                <span style="width: 6px; height: 6px; background: #00c853; border-radius: 50%;"></span>
                                <?php echo htmlspecialchars($account['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="performance.php?account=<?php echo urlencode($account['account_number']); ?>" class="btn btn-secondary">
                                Stats
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
