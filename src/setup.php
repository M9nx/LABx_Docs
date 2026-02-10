<?php
/**
 * LABx_Docs - Global Database Setup
 * Powerful setup interface with real-time status, batch operations, and keyboard shortcuts
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
        'icon' => '🔐',
        'progress_db' => 'ac_progress',
        'description' => 'IDOR, privilege escalation, authorization bypass',
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
        'icon' => '📦',
        'progress_db' => 'id_progress',
        'description' => 'Object injection, gadget chains, PHAR exploits',
        'labs' => [
            1 => ['name' => 'Modifying Serialized Objects', 'db' => 'deserial_lab1', 'sql' => 'Insecure-Deserialization/Lab-01/database_setup.sql'],
            2 => ['name' => 'Modifying Serialized Data Types', 'db' => 'deserial_lab2', 'sql' => 'Insecure-Deserialization/Lab-02/database_setup.sql'],
            3 => ['name' => 'Using Application Functionality', 'db' => 'deserial_lab3', 'sql' => 'Insecure-Deserialization/Lab-03/database_setup.sql'],
            4 => ['name' => 'Arbitrary Object Injection PHP', 'db' => 'deserial_lab4', 'sql' => 'Insecure-Deserialization/Lab-04/database_setup.sql'],
            5 => ['name' => 'PHP Pre-Built Gadget Chain', 'db' => 'deserial_lab5', 'sql' => 'Insecure-Deserialization/Lab-05/database_setup.sql'],
            6 => ['name' => 'Ruby Documented Gadget Chain', 'db' => 'deserial_lab6', 'sql' => 'Insecure-Deserialization/Lab-06/database_setup.sql'],
            7 => ['name' => 'Custom PHP Gadget Chain', 'db' => 'deserial_lab7', 'sql' => 'Insecure-Deserialization/Lab-07/database_setup.sql'],
            8 => ['name' => 'Custom Java Gadget Chain', 'db' => 'deserial_lab8', 'sql' => 'Insecure-Deserialization/Lab-08/database_setup.sql'],
            9 => ['name' => 'PHAR Deserialization', 'db' => 'deserial_lab9', 'sql' => 'Insecure-Deserialization/Lab-09/database_setup.sql'],
            10 => ['name' => 'Cookie Tampering Exploit', 'db' => 'deserial_lab10', 'sql' => 'Insecure-Deserialization/Lab-10/database_setup.sql'],
        ]
    ],
    'API' => [
        'name' => 'API Security',
        'color' => '#06b6d4',
        'icon' => '🔌',
        'progress_db' => 'api_progress',
        'description' => 'Coming soon - API vulnerability labs',
        'labs' => []
    ],
    'Authentication' => [
        'name' => 'Authentication',
        'color' => '#8b5cf6',
        'icon' => '🔑',
        'progress_db' => 'auth_progress',
        'description' => 'Coming soon - Auth bypass techniques',
        'labs' => []
    ],
];

$message = '';
$messageType = '';
$results = [];

// Check if credentials are configured
if (!$dbConfigured) {
    $message = "Database credentials not configured. <a href='../index.php'>Configure them here</a> first.";
    $messageType = 'error';
    $pdo = null;
} else {
    try {
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        $message = "Connection failed: " . $e->getMessage();
        $messageType = 'error';
        $pdo = null;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $selectedLabs = $_POST['labs'] ?? [];
    $setupAll = isset($_POST['setup_all']);
    $setupCategory = $_POST['setup_category'] ?? '';
    $resetAll = isset($_POST['reset_all']);
    $resetCategory = $_POST['reset_category'] ?? '';
    
    // Handle reset
    if ($resetAll || $resetCategory) {
        foreach ($categories as $catKey => $category) {
            if (!$resetAll && $resetCategory !== $catKey) continue;
            if (empty($category['labs'])) continue;
            
            try {
                $pdo->exec("DROP DATABASE IF EXISTS {$category['progress_db']}");
                foreach ($category['labs'] as $lab) {
                    $pdo->exec("DROP DATABASE IF EXISTS {$lab['db']}");
                }
                $results[$catKey . '_reset'] = ['status' => 'success', 'message' => 'Reset complete'];
            } catch (PDOException $e) {
                $results[$catKey . '_reset'] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }
        
        $message = "Databases have been reset.";
        $messageType = 'success';
    } else {
        // Handle setup
        foreach ($categories as $catKey => $category) {
            if (!$setupAll && $setupCategory !== $catKey && empty(array_filter($selectedLabs, fn($l) => strpos($l, $catKey . '_') === 0))) {
                continue;
            }
            
            if (empty($category['labs'])) continue;
            
            // Setup progress database
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
                    $results[$labKey] = ['status' => 'success', 'message' => 'Initialized'];
                } catch (PDOException $e) {
                    try {
                        $statements = array_filter(array_map('trim', explode(';', $sql)));
                        foreach ($statements as $stmt) {
                            if (!empty($stmt)) $pdo->exec($stmt);
                        }
                        $results[$labKey] = ['status' => 'success', 'message' => 'Initialized'];
                    } catch (PDOException $e2) {
                        $results[$labKey] = ['status' => 'error', 'message' => $e2->getMessage()];
                    }
                }
            }
        }
        
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
        $errorCount = count(array_filter($results, fn($r) => $r['status'] === 'error'));
        $skipCount = count(array_filter($results, fn($r) => $r['status'] === 'skip'));
        
        if ($errorCount === 0 && $successCount > 0) {
            $message = "✓ Successfully initialized $successCount database(s)!";
            $messageType = 'success';
        } elseif ($successCount > 0) {
            $message = "Initialized $successCount database(s) with $errorCount error(s).";
            $messageType = 'warning';
        } else {
            $message = "Failed to initialize databases.";
            $messageType = 'error';
        }
    }
}

// Get database status
$dbStatus = [];
$stats = ['ready' => 0, 'missing' => 0, 'nofile' => 0, 'total' => 0];
if ($pdo) {
    foreach ($categories as $catKey => $category) {
        $dbStatus[$catKey] = ['progress' => false, 'labs' => []];
        
        // Check progress DB
        try {
            $pdo->exec("USE {$category['progress_db']}");
            $dbStatus[$catKey]['progress'] = true;
        } catch (PDOException $e) {
            $dbStatus[$catKey]['progress'] = false;
        }
        
        foreach ($category['labs'] as $labNum => $lab) {
            $sqlFile = __DIR__ . '/../' . $lab['sql'];
            $hasFile = file_exists($sqlFile);
            
            try {
                $pdo->exec("USE {$lab['db']}");
                $dbStatus[$catKey]['labs'][$labNum] = ['exists' => true, 'hasFile' => $hasFile];
                $stats['ready']++;
            } catch (PDOException $e) {
                $dbStatus[$catKey]['labs'][$labNum] = ['exists' => false, 'hasFile' => $hasFile];
                $stats[$hasFile ? 'missing' : 'nofile']++;
            }
            $stats['total']++;
        }
    }
}

$readyPercent = $stats['total'] > 0 ? round(($stats['ready'] / $stats['total']) * 100) : 0;
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
            transition: margin-left 0.3s ease;
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-bottom: 6rem; }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }
        
        .stat-card:hover { transform: translateY(-2px); border-color: var(--border-hover); }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        
        .stat-card.success::before { background: var(--success); }
        .stat-card.warning::before { background: var(--warning); }
        .stat-card.danger::before { background: var(--danger); }
        .stat-card.primary::before { background: var(--accent); }
        
        .stat-icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.25rem;
        }
        
        /* Alert */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success { background: var(--success-bg); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); }
        .alert-warning { background: var(--warning-bg); border: 1px solid var(--warning); color: var(--warning); }
        .alert a { color: inherit; text-decoration: underline; }
        
        /* Category Section */
        .category-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .category-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .category-header:hover { background: var(--bg-card-hover); }
        
        .category-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .category-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        
        .category-info h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.15rem;
        }
        
        .category-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .category-stats {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .category-progress {
            width: 120px;
            height: 6px;
            background: var(--bg-tertiary);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .category-progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }
        
        .category-count {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            min-width: 50px;
            text-align: right;
        }
        
        .toggle-icon {
            transition: transform 0.2s;
            color: var(--text-muted);
        }
        
        .category-header.collapsed .toggle-icon { transform: rotate(-90deg); }
        
        .category-content { padding: 1.25rem 1.5rem; }
        .category-content.hidden { display: none; }
        
        .category-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.8rem; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-muted); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #1db954; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        .btn-outline:hover { border-color: var(--border-hover); color: var(--text-primary); }
        .btn-ghost { background: transparent; color: var(--text-secondary); }
        .btn-ghost:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .labs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 0.75rem;
        }
        
        .lab-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: var(--bg-tertiary);
            border-radius: 10px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .lab-item:hover { border-color: var(--border-hover); transform: translateX(4px); }
        .lab-item.selected { background: var(--accent-bg); border-color: var(--accent); }
        .lab-item.ready { border-left: 3px solid var(--success); }
        .lab-item.missing { border-left: 3px solid var(--warning); }
        .lab-item.nofile { border-left: 3px solid var(--danger); opacity: 0.6; }
        
        .lab-checkbox-wrapper { position: relative; display: flex; align-items: center; }
        
        .lab-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        
        .lab-info { flex: 1; min-width: 0; }
        
        .lab-name {
            font-size: 0.85rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .lab-name strong { color: var(--text-primary); margin-right: 0.25rem; }
        
        .lab-db {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-family: monospace;
        }
        
        .lab-badges { display: flex; align-items: center; gap: 0.5rem; }
        
        .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        
        .result-icon { font-size: 1rem; }
        
        /* Floating Action Bar */
        .action-bar {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-width);
            right: 0;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            backdrop-filter: blur(12px);
            transition: left 0.3s ease;
        }
        
        .action-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .selection-count {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        .selection-count strong { color: var(--text-primary); }
        
        .action-buttons { display: flex; gap: 0.75rem; }
        
        .shortcuts {
            display: flex;
            gap: 1rem;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .shortcuts kbd {
            background: var(--bg-tertiary);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-family: inherit;
            font-weight: 600;
        }
        
        /* Empty State */
        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--text-muted);
        }
        
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .shortcuts { display: none; }
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .mobile-toggle { display: flex; }
            .main-content { margin-left: 0; }
            .action-bar { left: 0; flex-direction: column; gap: 0.75rem; }
            .container { padding: 4rem 1rem 8rem; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat-value { font-size: 1.5rem; }
            .labs-grid { grid-template-columns: 1fr; }
            .category-stats { display: none; }
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>
    
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card success">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value"><?php echo $stats['ready']; ?></div>
                    <div class="stat-label">Ready</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-value"><?php echo $stats['missing']; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-value"><?php echo $stats['nofile']; ?></div>
                    <div class="stat-label">No SQL File</div>
                </div>
                <div class="stat-card primary">
                    <div class="stat-icon">📊</div>
                    <div class="stat-value"><?php echo $readyPercent; ?>%</div>
                    <div class="stat-label">Complete</div>
                </div>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <?php if ($messageType === 'success'): ?>
                    <polyline points="20 6 9 17 4 12"/>
                    <?php else: ?>
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    <?php endif; ?>
                </svg>
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" id="setupForm">
                <?php foreach ($categories as $catKey => $category):
                    $catReady = count(array_filter($dbStatus[$catKey]['labs'] ?? [], fn($l) => $l['exists']));
                    $catTotal = count($category['labs']);
                    $catPercent = $catTotal > 0 ? round(($catReady / $catTotal) * 100) : 0;
                ?>
                <div class="category-section" data-category="<?php echo $catKey; ?>">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-title">
                            <div class="category-icon" style="background: <?php echo $category['color']; ?>20; color: <?php echo $category['color']; ?>;">
                                <?php echo $category['icon']; ?>
                            </div>
                            <div class="category-info">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p><?php echo htmlspecialchars($category['description']); ?></p>
                            </div>
                        </div>
                        <div class="category-stats">
                            <div class="category-progress">
                                <div class="category-progress-fill" style="width: <?php echo $catPercent; ?>%; background: <?php echo $category['color']; ?>;"></div>
                            </div>
                            <div class="category-count"><?php echo $catReady; ?>/<?php echo $catTotal; ?></div>
                            <svg class="toggle-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                    
                    <?php if (!empty($category['labs'])): ?>
                    <div class="category-content">
                        <div class="category-actions">
                            <button type="button" class="btn btn-sm btn-ghost" onclick="selectAll('<?php echo $catKey; ?>')">Select All</button>
                            <button type="button" class="btn btn-sm btn-ghost" onclick="selectNone('<?php echo $catKey; ?>')">Clear</button>
                            <button type="button" class="btn btn-sm btn-ghost" onclick="selectMissing('<?php echo $catKey; ?>')">Select Missing</button>
                            <div style="flex: 1;"></div>
                            <button type="submit" name="setup_category" value="<?php echo $catKey; ?>" class="btn btn-sm btn-primary">
                                Setup Category
                            </button>
                            <button type="submit" name="reset_category" value="<?php echo $catKey; ?>" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: var(--danger);" onclick="return confirm('Reset all databases for <?php echo htmlspecialchars($category['name']); ?>?');">
                                Reset
                            </button>
                        </div>
                        
                        <div class="labs-grid">
                            <?php foreach ($category['labs'] as $labNum => $lab):
                                $labKey = $catKey . '_' . $labNum;
                                $labStatus = $dbStatus[$catKey]['labs'][$labNum] ?? ['exists' => false, 'hasFile' => false];
                                $statusClass = $labStatus['exists'] ? 'ready' : ($labStatus['hasFile'] ? 'missing' : 'nofile');
                                $hasResult = isset($results[$labKey]);
                            ?>
                            <div class="lab-item <?php echo $statusClass; ?>" onclick="toggleLabCheckbox(this)">
                                <div class="lab-checkbox-wrapper">
                                    <input type="checkbox" name="labs[]" value="<?php echo $labKey; ?>" id="<?php echo $labKey; ?>" onclick="event.stopPropagation();" <?php echo $statusClass === 'nofile' ? 'disabled' : ''; ?>>
                                </div>
                                <div class="lab-info">
                                    <div class="lab-name">
                                        <strong>Lab <?php echo $labNum; ?>:</strong> <?php echo htmlspecialchars($lab['name']); ?>
                                    </div>
                                    <div class="lab-db"><?php echo $lab['db']; ?></div>
                                </div>
                                <div class="lab-badges">
                                    <?php if ($hasResult): ?>
                                    <span class="result-icon"><?php echo $results[$labKey]['status'] === 'success' ? '✓' : '✕'; ?></span>
                                    <?php else: ?>
                                    <span class="badge badge-<?php echo $statusClass === 'ready' ? 'success' : ($statusClass === 'missing' ? 'warning' : 'danger'); ?>">
                                        <?php echo $statusClass === 'ready' ? 'Ready' : ($statusClass === 'missing' ? 'Pending' : 'No SQL'); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🚧</div>
                        <p>Labs coming soon...</p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <!-- Floating Action Bar -->
                <div class="action-bar">
                    <div class="action-info">
                        <div class="selection-count">
                            <strong id="selectedCount">0</strong> labs selected
                        </div>
                        <div class="shortcuts">
                            <span><kbd>A</kbd> Select All</span>
                            <span><kbd>Esc</kbd> Clear</span>
                            <span><kbd>Enter</kbd> Setup</span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline" onclick="clearSelection()">Clear Selection</button>
                        <button type="submit" name="setup_selected" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Setup Selected
                        </button>
                        <button type="submit" name="setup_all" class="btn btn-success">
                            🚀 Setup All
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Theme
        function toggleTheme() {
            const html = document.documentElement;
            const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Sidebar
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }
        
        // Toggle category collapse
        function toggleCategory(header) {
            header.classList.toggle('collapsed');
            const content = header.nextElementSibling;
            if (content) content.classList.toggle('hidden');
        }
        
        // Toggle individual lab checkbox
        function toggleLabCheckbox(item) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = !checkbox.checked;
                item.classList.toggle('selected', checkbox.checked);
                updateSelectedCount();
            }
        }
        
        // Selection helpers
        function selectAll(category) {
            document.querySelectorAll(`[data-category="${category}"] input[type="checkbox"]:not(:disabled)`).forEach(cb => {
                cb.checked = true;
                cb.closest('.lab-item').classList.add('selected');
            });
            updateSelectedCount();
        }
        
        function selectNone(category) {
            document.querySelectorAll(`[data-category="${category}"] input[type="checkbox"]`).forEach(cb => {
                cb.checked = false;
                cb.closest('.lab-item').classList.remove('selected');
            });
            updateSelectedCount();
        }
        
        function selectMissing(category) {
            document.querySelectorAll(`[data-category="${category}"] .lab-item.missing input[type="checkbox"]`).forEach(cb => {
                cb.checked = true;
                cb.closest('.lab-item').classList.add('selected');
            });
            updateSelectedCount();
        }
        
        function clearSelection() {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
                cb.closest('.lab-item')?.classList.remove('selected');
            });
            updateSelectedCount();
        }
        
        function updateSelectedCount() {
            const count = document.querySelectorAll('input[name="labs[]"]:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            
            if (e.key === 'a' || e.key === 'A') {
                e.preventDefault();
                document.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cb => {
                    cb.checked = true;
                    cb.closest('.lab-item').classList.add('selected');
                });
                updateSelectedCount();
            }
            
            if (e.key === 'Escape') {
                clearSelection();
            }
            
            if (e.key === 'Enter') {
                const count = document.querySelectorAll('input[name="labs[]"]:checked').length;
                if (count > 0) {
                    document.querySelector('[name="setup_selected"]').click();
                }
            }
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            updateSelectedCount();
            
            // Sync selected state with checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    this.closest('.lab-item').classList.toggle('selected', this.checked);
                    updateSelectedCount();
                });
            });
        });
    </script>
</body>
</html>
