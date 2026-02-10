<?php
/**
 * LABx_Docs - Global Database Setup
 * Initialize ALL lab databases across ALL categories from one place
 */

require_once __DIR__ . '/../db-config.php';

$creds = getDbCredentials();
$dbHost = $creds['host'];
$dbUser = $creds['user'];
$dbPass = $creds['pass'];
$dbConfigured = $creds['configured'];

// Sidebar configuration
$basePath = '../';
$activePage = 'setup';

// All Categories and their labs
$categories = [
    'AC' => [
        'name' => 'Access Control',
        'color' => '#ef4444',
        'progress_db' => 'ac_progress',
        'labs' => [
            1 => ['name' => 'Unprotected Admin', 'db' => 'secureshop_lab1', 'sql' => 'AC/Lab-01/database_setup.sql'],
            2 => ['name' => 'Unpredictable URL', 'db' => 'techcorp_lab2', 'sql' => 'AC/Lab-02/database_setup.sql'],
            3 => ['name' => 'Cookie Manipulation', 'db' => 'lab3_db', 'sql' => 'AC/Lab-03/database_setup.sql'],
            4 => ['name' => 'Mass Assignment', 'db' => 'lab4_rolemod', 'sql' => 'AC/Lab-04/database_setup.sql'],
            5 => ['name' => 'IDOR User ID', 'db' => 'lab5_idor', 'sql' => 'AC/Lab-05/database_setup.sql'],
            6 => ['name' => 'IDOR GUID Leak', 'db' => 'lab6_guid', 'sql' => 'AC/Lab-06/database_setup.sql'],
            7 => ['name' => 'Redirect Data Leak', 'db' => 'lab7_redirect', 'sql' => 'AC/Lab-07/database_setup.sql'],
            8 => ['name' => 'Multi-Step Bypass', 'db' => 'lab8_password', 'sql' => 'AC/Lab-08/database_setup.sql'],
            9 => ['name' => 'Referer Header', 'db' => 'ac_lab9', 'sql' => 'AC/Lab-09/database_setup.sql'],
            10 => ['name' => 'Method Override', 'db' => 'ac_lab10', 'sql' => 'AC/Lab-10/database_setup.sql'],
            11 => ['name' => 'URL Match Discrepancy', 'db' => 'ac_lab11', 'sql' => 'AC/Lab-11/database_setup.sql'],
            12 => ['name' => 'IDOR Chat Messages', 'db' => 'ac_lab12', 'sql' => 'AC/Lab-12/database_setup.sql'],
            13 => ['name' => 'IDOR File Disclosure', 'db' => 'ac_lab13', 'sql' => 'AC/Lab-13/database_setup.sql'],
            14 => ['name' => 'IDOR Subscription', 'db' => 'ac_lab14', 'sql' => 'AC/Lab-14/database_setup.sql'],
            15 => ['name' => 'MTN MobAd IDOR', 'db' => 'ac_lab15', 'sql' => 'AC/Lab-15/database_setup.sql'],
            16 => ['name' => 'IDOR Order Tracking', 'db' => 'ac_lab16', 'sql' => 'AC/Lab-16/database_setup.sql'],
            17 => ['name' => 'GitLab IDOR', 'db' => 'ac_lab17', 'sql' => 'AC/Lab-17/database_setup.sql'],
            18 => ['name' => 'Session Expiry IDOR', 'db' => 'ac_lab18', 'sql' => 'AC/Lab-18/database_setup.sql'],
            19 => ['name' => 'Delete Saved Projects', 'db' => 'ac_lab19', 'sql' => 'AC/Lab-19/database_setup.sql'],
            20 => ['name' => 'API Keys IDOR', 'db' => 'ac_lab20', 'sql' => 'AC/Lab-20/database_setup.sql'],
            21 => ['name' => 'Stocky Settings', 'db' => 'ac_lab21', 'sql' => 'AC/Lab-21/database_setup.sql'],
            22 => ['name' => 'Booking IDOR', 'db' => 'ac_lab22', 'sql' => 'AC/Lab-22/database_setup.sql'],
            23 => ['name' => 'GraphQL Tags IDOR', 'db' => 'ac_lab23', 'sql' => 'AC/Lab-23/database_setup.sql'],
            24 => ['name' => 'ML Models IDOR', 'db' => 'ac_lab24', 'sql' => 'AC/Lab-24/database_setup.sql'],
            25 => ['name' => 'Notes IDOR Snippets', 'db' => 'ac_lab25', 'sql' => 'AC/Lab-25/database_setup.sql'],
            26 => ['name' => 'API IDOR Credential Leak', 'db' => 'ac_lab26', 'sql' => 'AC/Lab-26/database_setup.sql'],
            27 => ['name' => 'Stats API IDOR', 'db' => 'ac_lab27', 'sql' => 'AC/Lab-27/database_setup.sql'],
            28 => ['name' => 'MTN Team IDOR', 'db' => 'ac_lab28', 'sql' => 'AC/Lab-28/database_setup.sql'],
            29 => ['name' => 'Newsletter Subscriber IDOR', 'db' => 'ac_lab29', 'sql' => 'AC/Lab-29/database_setup.sql'],
            30 => ['name' => 'Stocky Settings IDOR', 'db' => 'ac_lab30', 'sql' => 'AC/Lab-30/database_setup.sql'],
        ]
    ],
    'Insecure-Deserialization' => [
        'name' => 'Insecure Deserialization',
        'color' => '#f97316',
        'progress_db' => 'id_progress',
        'labs' => [
            1 => ['name' => 'Modifying Serialized Objects', 'db' => 'deserial_lab1', 'sql' => 'Insecure-Deserialization/Lab-01/database_setup.sql'],
        ]
    ],
];

$message = '';
$messageType = '';
$results = [];

// Check if credentials are configured
if (!$dbConfigured) {
    $message = "Database credentials not configured. Please configure them on the <a href='../index.php'>main page</a> first.";
    $messageType = 'error';
    $pdo = null;
} else {
    try {
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        $message = "Database connection failed: " . $e->getMessage();
        $messageType = 'error';
        $pdo = null;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $selectedLabs = $_POST['labs'] ?? [];
    $setupAll = isset($_POST['setup_all']);
    $setupCategory = $_POST['setup_category'] ?? '';
    
    foreach ($categories as $catKey => $category) {
        // Skip if not setting up all and not this category
        if (!$setupAll && $setupCategory !== $catKey && empty(array_filter($selectedLabs, fn($l) => strpos($l, $catKey . '_') === 0))) {
            continue;
        }
        
        // Setup progress database for this category
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$category['progress_db']}");
            $pdo->exec("USE {$category['progress_db']}");
            $pdo->exec("CREATE TABLE IF NOT EXISTS solved_labs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lab_number INT NOT NULL UNIQUE,
                solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $results[$catKey . '_progress'] = ['status' => 'success', 'message' => 'Progress DB ready'];
        } catch (PDOException $e) {
            $results[$catKey . '_progress'] = ['status' => 'error', 'message' => $e->getMessage()];
        }
        
        // Setup individual labs
        foreach ($category['labs'] as $labNum => $lab) {
            $labKey = $catKey . '_' . $labNum;
            
            // Skip if not selected and not setting up all/category
            if (!$setupAll && $setupCategory !== $catKey && !in_array($labKey, $selectedLabs)) {
                continue;
            }
            
            $sqlFile = __DIR__ . '/../' . $lab['sql'];
            
            if (!file_exists($sqlFile)) {
                $results[$labKey] = ['status' => 'skip', 'message' => 'SQL file not found'];
                continue;
            }
            
            $sql = file_get_contents($sqlFile);
            
            try {
                $pdo->exec($sql);
                $results[$labKey] = ['status' => 'success', 'message' => 'Database initialized'];
            } catch (PDOException $e) {
                // Try line by line
                try {
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    foreach ($statements as $stmt) {
                        if (!empty($stmt)) {
                            $pdo->exec($stmt);
                        }
                    }
                    $results[$labKey] = ['status' => 'success', 'message' => 'Database initialized'];
                } catch (PDOException $e2) {
                    $results[$labKey] = ['status' => 'error', 'message' => $e2->getMessage()];
                }
            }
        }
    }
    
    $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
    $errorCount = count(array_filter($results, fn($r) => $r['status'] === 'error'));
    
    if ($errorCount === 0 && $successCount > 0) {
        $message = "Successfully initialized $successCount database(s)!";
        $messageType = 'success';
    } elseif ($successCount > 0) {
        $message = "Initialized $successCount database(s) with $errorCount error(s).";
        $messageType = 'warning';
    } else {
        $message = "Failed to initialize databases.";
        $messageType = 'error';
    }
}

// Get current database status
$dbStatus = [];
if ($pdo) {
    foreach ($categories as $catKey => $category) {
        $dbStatus[$catKey] = [];
        foreach ($category['labs'] as $labNum => $lab) {
            try {
                $pdo->exec("USE {$lab['db']}");
                $dbStatus[$catKey][$labNum] = true;
            } catch (PDOException $e) {
                $dbStatus[$catKey][$labNum] = false;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup All Databases - LABx_Docs</title>
    <link rel="stylesheet" href="sidebar.css">
    <style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #111111;
            --bg-tertiary: #1a1a1a;
            --bg-card: rgba(255, 255, 255, 0.02);
            --bg-card-hover: rgba(255, 255, 255, 0.05);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.15);
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --text-muted: #666666;
            --accent: #3b82f6;
            --accent-muted: #2563eb;
            --accent-bg: rgba(59, 130, 246, 0.1);
            --success: #22c55e;
            --success-bg: rgba(34, 197, 94, 0.1);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.1);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.1);
            --sidebar-width: 280px;
        }
        
        [data-theme="light"] {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #f0f1f3;
            --bg-card: rgba(0, 0, 0, 0.02);
            --bg-card-hover: rgba(0, 0, 0, 0.04);
            --border-color: rgba(0, 0, 0, 0.08);
            --border-hover: rgba(0, 0, 0, 0.15);
            --text-primary: #0a0a0a;
            --text-secondary: #555555;
            --text-muted: #888888;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-secondary);
            min-height: 100vh;
            display: flex;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem; }
        
        .page-header {
            margin-bottom: 2rem;
            padding: 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }
        
        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .page-header p { color: var(--text-muted); }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success { background: var(--success-bg); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); }
        .alert-warning { background: var(--warning-bg); border: 1px solid var(--warning); color: var(--warning); }
        .alert a { color: inherit; text-decoration: underline; }
        
        .category-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .category-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
        }
        
        .category-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .category-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        
        .category-title h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-muted); }
        .btn-success { background: var(--success); color: white; }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        .btn-outline:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
        }
        
        .labs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 0.75rem;
            padding: 1rem 1.5rem;
        }
        
        .lab-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .lab-item:hover { background: var(--bg-card-hover); }
        
        .lab-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
        }
        
        .lab-item label {
            flex: 1;
            cursor: pointer;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        
        .lab-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .lab-status.ready { background: var(--success); }
        .lab-status.missing { background: var(--danger); }
        
        .actions-bar {
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 1rem;
            justify-content: center;
            position: sticky;
            bottom: 0;
        }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .labs-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>⚙️ Setup All Databases</h1>
                <p>Initialize lab databases for all categories. Select individual labs or setup entire categories at once.</p>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" id="setupForm">
                <?php foreach ($categories as $catKey => $category): ?>
                <div class="category-section">
                    <div class="category-header">
                        <div class="category-title">
                            <div class="category-icon" style="background: <?php echo $category['color']; ?>20; color: <?php echo $category['color']; ?>;">
                                <?php echo $catKey === 'AC' ? '🔐' : '📦'; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">(<?php echo count($category['labs']); ?> labs)</span>
                        </div>
                        <button type="submit" name="setup_category" value="<?php echo $catKey; ?>" class="btn btn-outline">
                            Setup Category
                        </button>
                    </div>
                    <div class="labs-grid">
                        <?php foreach ($category['labs'] as $labNum => $lab): 
                            $labKey = $catKey . '_' . $labNum;
                            $isReady = isset($dbStatus[$catKey][$labNum]) && $dbStatus[$catKey][$labNum];
                            $hasResult = isset($results[$labKey]);
                        ?>
                        <div class="lab-item">
                            <input type="checkbox" name="labs[]" value="<?php echo $labKey; ?>" id="<?php echo $labKey; ?>">
                            <label for="<?php echo $labKey; ?>">
                                <strong>Lab <?php echo $labNum; ?>:</strong> <?php echo htmlspecialchars($lab['name']); ?>
                            </label>
                            <div class="lab-status <?php echo $isReady ? 'ready' : 'missing'; ?>" 
                                 title="<?php echo $isReady ? 'Database ready' : 'Database not initialized'; ?>"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="actions-bar">
                    <button type="submit" name="setup_selected" class="btn btn-primary">
                        Setup Selected Labs
                    </button>
                    <button type="submit" name="setup_all" class="btn btn-success">
                        🚀 Setup All Databases
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
