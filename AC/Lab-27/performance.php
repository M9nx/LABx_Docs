<?php
/**
 * Lab 27: Performance Page
 * IDOR in Stats API Endpoint - Exness-style Trading Platform
 * 
 * This page shows trading statistics and makes API calls to vulnerable stats endpoints
 */

require_once 'config.php';
requireLogin();

$pdo = getDBConnection();
$user = getCurrentUser($pdo);
$accounts = getUserAccounts($pdo, $_SESSION['user_id']);

$selectedAccount = $_GET['account'] ?? ($accounts[0]['account_number'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .nav-links a:hover, .nav-links a.active { color: #ffd700; }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .back-link:hover { color: #ffd700; }
        
        /* Controls */
        .controls {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
        }
        .control-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .control-group label {
            color: #888;
            font-size: 0.85rem;
        }
        .control-group select, .control-group input {
            padding: 0.75rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 0.95rem;
            min-width: 200px;
        }
        .control-group select:focus, .control-group input:focus {
            outline: none;
            border-color: #ffd700;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .stat-card h3 {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ffd700;
        }
        .stat-value.positive { color: #00c853; }
        .stat-value.negative { color: #ff6b6b; }

        /* Chart */
        .chart-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .chart-header h2 {
            color: #fff;
        }
        .chart-tabs {
            display: flex;
            gap: 0.5rem;
        }
        .chart-tab {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 6px;
            color: #888;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        .chart-tab:hover, .chart-tab.active {
            background: rgba(255, 215, 0, 0.1);
            border-color: #ffd700;
            color: #ffd700;
        }
        .chart-container {
            height: 350px;
        }

        /* API Request Box */
        .api-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .api-box h3 {
            color: #ffd700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .api-url {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            color: #00ff88;
            word-break: break-all;
            margin-bottom: 1rem;
        }
        .api-response {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            color: #e0e0e0;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-size: 0.85rem;
        }

        /* Vulnerability Hint */
        .vuln-hint {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .vuln-hint h3 {
            color: #ff6b6b;
            margin-bottom: 0.75rem;
        }
        .vuln-hint p {
            color: #ffaaaa;
            line-height: 1.7;
        }
        .vuln-hint code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #ffd700;
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
                <a href="performance.php" class="active">Performance</a>
                <a href="docs.php">Docs</a>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <div class="page-header">
            <h1>📊 Performance Statistics</h1>
        </div>

        <?php if ($user['username'] === 'attacker'): ?>
        <div class="vuln-hint">
            <h3>🎯 IDOR Vulnerability</h3>
            <p>
                The stats API endpoints below accept an <code>accounts</code> parameter without 
                proper authorization. Try changing the account number to view other users' stats:
            </p>
            <p>
                <strong>Try these target accounts:</strong><br>
                <code>MT5-200001</code> - Victim's Pro account (~$87,500)<br>
                <code>MT5-200002</code> - Victim's Raw Spread (~$125,000)<br>
                <code>MT5-300001</code> - Whale's Zero account (~$2,500,000)<br>
                <code>MT5-000001</code> - Admin's internal account (~$10,000,000)
            </p>
        </div>
        <?php endif; ?>

        <div class="controls">
            <div class="control-group">
                <label>Select Account</label>
                <select id="accountSelect">
                    <?php foreach ($accounts as $account): ?>
                    <option value="<?php echo htmlspecialchars($account['account_number']); ?>"
                            <?php echo $account['account_number'] === $selectedAccount ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($account['account_number']); ?> 
                        (<?php echo htmlspecialchars($account['account_type']); ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="control-group">
                <label>Or Enter Account Number (IDOR Test)</label>
                <input type="text" id="manualAccount" placeholder="e.g., MT5-200001" 
                       value="<?php echo htmlspecialchars($selectedAccount); ?>">
            </div>
            <div class="control-group">
                <label>Time Range</label>
                <select id="timeRange">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365" selected>Last 365 days</option>
                </select>
            </div>
            <div class="control-group" style="justify-content: flex-end;">
                <label>&nbsp;</label>
                <button class="btn btn-primary" onclick="loadStats()">
                    🔄 Load Stats
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>💰 Total Equity</h3>
                <div class="stat-value positive" id="equityValue">Loading...</div>
            </div>
            <div class="stat-card">
                <h3>📈 Net Profit</h3>
                <div class="stat-value" id="profitValue">Loading...</div>
            </div>
            <div class="stat-card">
                <h3>🔢 Orders Count</h3>
                <div class="stat-value" id="ordersValue">Loading...</div>
            </div>
            <div class="stat-card">
                <h3>📊 Trading Volume</h3>
                <div class="stat-value" id="volumeValue">Loading...</div>
            </div>
        </div>

        <div class="chart-section">
            <div class="chart-header">
                <h2>Equity Over Time</h2>
                <div class="chart-tabs">
                    <button class="chart-tab active" data-type="equity">Equity</button>
                    <button class="chart-tab" data-type="net_profit">Net Profit</button>
                    <button class="chart-tab" data-type="trading_volume">Volume</button>
                    <button class="chart-tab" data-type="orders_count">Orders</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="statsChart"></canvas>
            </div>
        </div>

        <div class="api-box">
            <h3>🔌 API Request (for testing)</h3>
            <div class="api-url" id="apiUrl">
                GET /api/stats/equity?time_range=365&accounts=<?php echo htmlspecialchars($selectedAccount); ?>
            </div>
            <h4 style="color: #888; margin-bottom: 0.5rem;">Response:</h4>
            <div class="api-response" id="apiResponse">
                Click "Load Stats" to see the API response...
            </div>
        </div>
    </main>

    <script>
        let chart = null;
        let currentStatType = 'equity';

        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('statsChart').getContext('2d');
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Equity',
                        data: [],
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: '#888' }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: '#888' }
                        }
                    }
                }
            });
        }

        // Load stats from API
        async function loadStats() {
            const account = document.getElementById('manualAccount').value || 
                           document.getElementById('accountSelect').value;
            const timeRange = document.getElementById('timeRange').value;

            // Update API URL display
            document.getElementById('apiUrl').textContent = 
                `GET /api/stats/${currentStatType}?time_range=${timeRange}&accounts=${account}`;

            try {
                // Fetch equity
                const equityRes = await fetch(`api/stats/equity.php?time_range=${timeRange}&accounts=${account}`);
                const equityData = await equityRes.json();
                
                // Fetch net profit
                const profitRes = await fetch(`api/stats/net_profit.php?time_range=${timeRange}&accounts=${account}`);
                const profitData = await profitRes.json();
                
                // Fetch orders count
                const ordersRes = await fetch(`api/stats/orders_number.php?time_range=${timeRange}&accounts=${account}`);
                const ordersData = await ordersRes.json();
                
                // Fetch trading volume
                const volumeRes = await fetch(`api/stats/trading_volume.php?time_range=${timeRange}&accounts=${account}`);
                const volumeData = await volumeRes.json();

                // Update stat cards
                if (equityData.data && equityData.data.length > 0) {
                    const latestEquity = equityData.data[equityData.data.length - 1].value;
                    document.getElementById('equityValue').textContent = '$' + parseFloat(latestEquity).toLocaleString(undefined, {minimumFractionDigits: 2});
                    document.getElementById('equityValue').className = 'stat-value positive';
                }

                if (profitData.data && profitData.data.length > 0) {
                    const totalProfit = profitData.data.reduce((sum, item) => sum + parseFloat(item.value), 0);
                    document.getElementById('profitValue').textContent = '$' + totalProfit.toLocaleString(undefined, {minimumFractionDigits: 2});
                    document.getElementById('profitValue').className = 'stat-value ' + (totalProfit >= 0 ? 'positive' : 'negative');
                }

                if (ordersData.data && ordersData.data.length > 0) {
                    const totalOrders = ordersData.data.reduce((sum, item) => sum + parseInt(item.value), 0);
                    document.getElementById('ordersValue').textContent = totalOrders.toLocaleString();
                }

                if (volumeData.data && volumeData.data.length > 0) {
                    const totalVolume = volumeData.data.reduce((sum, item) => sum + parseFloat(item.value), 0);
                    document.getElementById('volumeValue').textContent = totalVolume.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' lots';
                }

                // Update chart
                updateChart(currentStatType === 'equity' ? equityData : 
                           currentStatType === 'net_profit' ? profitData :
                           currentStatType === 'orders_count' ? ordersData : volumeData);

                // Show API response
                document.getElementById('apiResponse').textContent = JSON.stringify(equityData, null, 2);

            } catch (error) {
                document.getElementById('apiResponse').textContent = 'Error: ' + error.message;
            }
        }

        // Update chart with data
        function updateChart(data) {
            if (!data.data) return;
            
            const labels = data.data.map(item => item.stat_date);
            const values = data.data.map(item => parseFloat(item.value));

            chart.data.labels = labels;
            chart.data.datasets[0].data = values;
            chart.data.datasets[0].label = currentStatType.replace('_', ' ').charAt(0).toUpperCase() + 
                                           currentStatType.replace('_', ' ').slice(1);
            chart.update();
        }

        // Chart tab switching
        document.querySelectorAll('.chart-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentStatType = this.dataset.type;
                loadStats();
            });
        });

        // Sync manual input with dropdown
        document.getElementById('accountSelect').addEventListener('change', function() {
            document.getElementById('manualAccount').value = this.value;
        });

        // Initialize
        initChart();
        loadStats();
    </script>
</body>
</html>
