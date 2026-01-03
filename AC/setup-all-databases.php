<?php
/**
 * Master Database Setup Page
 * Initialize all lab databases from one place
 */

// Database connection settings
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = 'root';

// Lab configurations - add new labs here
$labs = [
    1 => ['name' => 'Unprotected Admin', 'db' => 'secureshop_lab1', 'sql' => 'Lab-01/database_setup.sql'],
    2 => ['name' => 'Unpredictable URL', 'db' => 'techcorp_lab2', 'sql' => 'Lab-02/database_setup.sql'],
    3 => ['name' => 'Cookie Manipulation', 'db' => 'lab3_db', 'sql' => 'Lab-03/database_setup.sql'],
    4 => ['name' => 'Mass Assignment', 'db' => 'lab4_rolemod', 'sql' => 'Lab-04/database_setup.sql'],
    5 => ['name' => 'IDOR User ID', 'db' => 'lab5_idor', 'sql' => 'Lab-05/database_setup.sql'],
    6 => ['name' => 'IDOR GUID Leak', 'db' => 'lab6_guid', 'sql' => 'Lab-06/database_setup.sql'],
    7 => ['name' => 'Redirect Data Leak', 'db' => 'lab7_redirect', 'sql' => 'Lab-07/database_setup.sql'],
    8 => ['name' => 'Multi-Step Bypass', 'db' => 'lab8_password', 'sql' => 'Lab-08/database_setup.sql'],
    9 => ['name' => 'Referer Header', 'db' => 'ac_lab9', 'sql' => 'Lab-09/database_setup.sql'],
    10 => ['name' => 'Method Override', 'db' => 'ac_lab10', 'sql' => 'Lab-10/database_setup.sql'],
    11 => ['name' => 'URL Match Discrepancy', 'db' => 'ac_lab11', 'sql' => 'Lab-11/database_setup.sql'],
    12 => ['name' => 'IDOR Chat Messages', 'db' => 'ac_lab12', 'sql' => 'Lab-12/database_setup.sql'],
    13 => ['name' => 'IDOR File Disclosure', 'db' => 'ac_lab13', 'sql' => 'Lab-13/database_setup.sql'],
    14 => ['name' => 'IDOR Subscription', 'db' => 'ac_lab14', 'sql' => 'Lab-14/database_setup.sql'],
    15 => ['name' => 'MTN MobAd IDOR', 'db' => 'ac_lab15', 'sql' => 'Lab-15/database_setup.sql'],
    16 => ['name' => 'IDOR Order Tracking', 'db' => 'ac_lab16', 'sql' => 'Lab-16/database_setup.sql'],
    17 => ['name' => 'GitLab IDOR', 'db' => 'ac_lab17', 'sql' => 'Lab-17/database_setup.sql'],
    18 => ['name' => 'Session Expiry IDOR', 'db' => 'ac_lab18', 'sql' => 'Lab-18/database_setup.sql'],
    19 => ['name' => 'Delete Saved Projects', 'db' => 'ac_lab19', 'sql' => 'Lab-19/database_setup.sql'],
    20 => ['name' => 'API Keys IDOR', 'db' => 'ac_lab20', 'sql' => 'Lab-20/database_setup.sql'],
    21 => ['name' => 'Stocky Settings', 'db' => 'ac_lab21', 'sql' => 'Lab-21/database_setup.sql'],
    22 => ['name' => 'Booking IDOR', 'db' => 'ac_lab22', 'sql' => 'Lab-22/database_setup.sql'],
    23 => ['name' => 'GraphQL Tags IDOR', 'db' => 'ac_lab23', 'sql' => 'Lab-23/database_setup.sql'],
    24 => ['name' => 'ML Models IDOR', 'db' => 'ac_lab24', 'sql' => 'Lab-24/database_setup.sql'],
    25 => ['name' => 'Notes IDOR Snippets', 'db' => 'ac_lab25', 'sql' => 'Lab-25/database_setup.sql'],
    26 => ['name' => 'API IDOR Credential Leak', 'db' => 'ac_lab26', 'sql' => 'Lab-26/database_setup.sql'],
    27 => ['name' => 'Stats API IDOR', 'db' => 'ac_lab27', 'sql' => 'Lab-27/database_setup.sql'],
    28 => ['name' => 'MTN Team IDOR', 'db' => 'ac_lab28', 'sql' => 'Lab-28/database_setup.sql'],
    29 => ['name' => 'Newsletter Subscriber IDOR', 'db' => 'ac_lab29', 'sql' => 'Lab-29/database_setup.sql'],
    30 => ['name' => 'Stocky Settings IDOR', 'db' => 'ac_lab30', 'sql' => 'Lab-30/database_setup.sql'],
];

$message = '';
$messageType = '';
$results = [];

// Connect to MySQL
try {
    $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    $message = "Database connection failed: " . $e->getMessage();
    $messageType = 'error';
    $pdo = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $labsToSetup = $_POST['labs'] ?? [];
    
    if (isset($_POST['setup_all'])) {
        $labsToSetup = array_keys($labs);
    }
    
    foreach ($labsToSetup as $labNum) {
        $labNum = (int)$labNum;
        if (!isset($labs[$labNum])) continue;
        
        $lab = $labs[$labNum];
        $sqlFile = __DIR__ . '/' . $lab['sql'];
        
        if (!file_exists($sqlFile)) {
            $results[$labNum] = ['status' => 'error', 'message' => 'SQL file not found'];
            continue;
        }
        
        $sql = file_get_contents($sqlFile);
        
        try {
            // Execute multi-query SQL
            $pdo->exec($sql);
            $results[$labNum] = ['status' => 'success', 'message' => 'Database initialized'];
        } catch (PDOException $e) {
            // Try line by line if multi-query fails
            try {
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($statements as $stmt) {
                    if (!empty($stmt)) {
                        $pdo->exec($stmt);
                    }
                }
                $results[$labNum] = ['status' => 'success', 'message' => 'Database initialized'];
            } catch (PDOException $e2) {
                $results[$labNum] = ['status' => 'error', 'message' => substr($e2->getMessage(), 0, 100)];
            }
        }
    }
    
    if (!empty($results)) {
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
        $message = "$successCount of " . count($results) . " databases initialized successfully!";
        $messageType = $successCount === count($results) ? 'success' : 'warning';
    }
}

// Check database status
function checkDatabaseExists($pdo, $dbName) {
    if (!$pdo) return false;
    try {
        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
        $stmt->execute([$dbName]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        return false;
    }
}

function checkSqlFileExists($sqlPath) {
    return file_exists(__DIR__ . '/' . $sqlPath);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Access Control Labs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            padding: 1.5rem 2rem;
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link {
            color: #e0e0e0;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background: rgba(255, 68, 68, 0.1);
            color: #ff4444;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #ff4444, #ff6666);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #888;
            font-size: 1.1rem;
        }
        
        .message {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .message.success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #81c784;
        }
        
        .message.error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #e57373;
        }
        
        .message.warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffd54f;
        }
        
        .actions-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.3);
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff4444;
        }
        
        .stat-label {
            color: #888;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .stat-card.ready .stat-value { color: #4caf50; }
        .stat-card.missing .stat-value { color: #ff9800; }
        
        .labs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1rem;
        }
        
        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .lab-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 68, 68, 0.3);
        }
        
        .lab-card.db-exists {
            border-left: 4px solid #4caf50;
        }
        
        .lab-card.db-missing {
            border-left: 4px solid #ff9800;
        }
        
        .lab-card.sql-missing {
            border-left: 4px solid #f44336;
            opacity: 0.6;
        }
        
        .lab-checkbox {
            width: 22px;
            height: 22px;
            accent-color: #ff4444;
            cursor: pointer;
        }
        
        .lab-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }
        
        .lab-info {
            flex: 1;
            min-width: 0;
        }
        
        .lab-name {
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .lab-db {
            font-size: 0.8rem;
            color: #888;
            font-family: monospace;
        }
        
        .lab-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.exists {
            background: rgba(76, 175, 80, 0.2);
            color: #81c784;
        }
        
        .status-badge.missing {
            background: rgba(255, 152, 0, 0.2);
            color: #ffb74d;
        }
        
        .status-badge.no-sql {
            background: rgba(244, 67, 54, 0.2);
            color: #e57373;
        }
        
        .status-badge.success {
            background: rgba(76, 175, 80, 0.3);
            color: #a5d6a7;
        }
        
        .status-badge.error {
            background: rgba(244, 67, 54, 0.3);
            color: #ef9a9a;
        }
        
        .result-message {
            font-size: 0.75rem;
            color: #888;
            max-width: 120px;
            text-align: right;
        }
        
        .select-actions {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .select-btn {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: #888;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .select-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .footer-note code {
            background: rgba(255, 68, 68, 0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #ff6666;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 AC Labs Setup</a>
            <a href="index.php" class="nav-link">← Back to Labs</a>
        </div>
    </header>
    
    <div class="container">
        <div class="page-header">
            <h1>🗄️ Database Setup</h1>
            <p>Initialize databases for all Access Control labs from one place</p>
        </div>
        
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php if ($messageType === 'success'): ?>✅<?php elseif ($messageType === 'error'): ?>❌<?php else: ?>⚠️<?php endif; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($pdo): ?>
        <?php
        // Calculate stats
        $totalLabs = count($labs);
        $dbExists = 0;
        $sqlExists = 0;
        foreach ($labs as $lab) {
            if (checkDatabaseExists($pdo, $lab['db'])) $dbExists++;
            if (checkSqlFileExists($lab['sql'])) $sqlExists++;
        }
        ?>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalLabs; ?></div>
                <div class="stat-label">Total Labs</div>
            </div>
            <div class="stat-card ready">
                <div class="stat-value"><?php echo $dbExists; ?></div>
                <div class="stat-label">DBs Ready</div>
            </div>
            <div class="stat-card missing">
                <div class="stat-value"><?php echo $totalLabs - $dbExists; ?></div>
                <div class="stat-label">DBs Missing</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $sqlExists; ?></div>
                <div class="stat-label">SQL Files Found</div>
            </div>
        </div>
        
        <form method="POST">
            <div class="actions-bar">
                <button type="submit" name="setup_all" class="btn btn-primary">
                    🚀 Initialize ALL Databases
                </button>
                <button type="submit" class="btn btn-success">
                    ✅ Initialize Selected
                </button>
                <button type="button" class="btn btn-secondary" onclick="location.reload()">
                    🔄 Refresh Status
                </button>
            </div>
            
            <div class="select-actions">
                <button type="button" class="select-btn" onclick="selectAll()">Select All</button>
                <button type="button" class="select-btn" onclick="selectNone()">Select None</button>
                <button type="button" class="select-btn" onclick="selectMissing()">Select Missing DBs</button>
            </div>
            
            <div class="labs-grid">
                <?php foreach ($labs as $num => $lab): 
                    $dbExistsFlag = checkDatabaseExists($pdo, $lab['db']);
                    $sqlExistsFlag = checkSqlFileExists($lab['sql']);
                    $cardClass = !$sqlExistsFlag ? 'sql-missing' : ($dbExistsFlag ? 'db-exists' : 'db-missing');
                    $result = $results[$num] ?? null;
                ?>
                <div class="lab-card <?php echo $cardClass; ?>">
                    <input type="checkbox" name="labs[]" value="<?php echo $num; ?>" 
                           class="lab-checkbox" <?php echo !$sqlExistsFlag ? 'disabled' : ''; ?>
                           data-db-exists="<?php echo $dbExistsFlag ? '1' : '0'; ?>">
                    <div class="lab-number"><?php echo $num; ?></div>
                    <div class="lab-info">
                        <div class="lab-name"><?php echo htmlspecialchars($lab['name']); ?></div>
                        <div class="lab-db"><?php echo htmlspecialchars($lab['db']); ?></div>
                    </div>
                    <div class="lab-status">
                        <?php if ($result): ?>
                            <span class="status-badge <?php echo $result['status']; ?>">
                                <?php echo $result['status'] === 'success' ? '✓ Done' : '✗ Failed'; ?>
                            </span>
                            <?php if ($result['status'] === 'error'): ?>
                            <span class="result-message"><?php echo htmlspecialchars(substr($result['message'], 0, 50)); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (!$sqlExistsFlag): ?>
                                <span class="status-badge no-sql">No SQL</span>
                            <?php elseif ($dbExistsFlag): ?>
                                <span class="status-badge exists">Ready</span>
                            <?php else: ?>
                                <span class="status-badge missing">Missing</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </form>
        
        <?php else: ?>
        <div class="message error">
            ❌ Cannot connect to MySQL. Please check your database credentials.
        </div>
        <?php endif; ?>
        
        <div class="footer-note">
            <p>
                <strong>Note:</strong> This page uses credentials <code>root:root</code> by default. 
                If your MySQL uses different credentials, update them in each lab's <code>config.php</code> 
                and in this file.
            </p>
        </div>
    </div>
    
    <script>
        function selectAll() {
            document.querySelectorAll('.lab-checkbox:not([disabled])').forEach(cb => cb.checked = true);
        }
        
        function selectNone() {
            document.querySelectorAll('.lab-checkbox').forEach(cb => cb.checked = false);
        }
        
        function selectMissing() {
            document.querySelectorAll('.lab-checkbox').forEach(cb => {
                cb.checked = cb.dataset.dbExists === '0' && !cb.disabled;
            });
        }
    </script>
</body>
</html>
