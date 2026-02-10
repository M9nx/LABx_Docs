<?php
/**
 * LABx_Docs - Global Progress Tracker
 * Comprehensive progress view across ALL categories with advanced analytics
 */

require_once __DIR__ . '/../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

// Sidebar configuration
$basePath = '../';
$activePage = 'progress';

mysqli_report(MYSQLI_REPORT_OFF);

// All Categories with their progress databases and labs
$categories = [
    'AC' => [
        'name' => 'Access Control',
        'color' => '#ef4444',
        'icon' => '🔐',
        'db' => 'ac_progress',
        'total' => 30,
        'link' => '../AC/index.php',
        'description' => 'IDOR, privilege escalation, authorization bypass',
        'labs' => [
            1 => 'Unprotected Admin Functionality',
            2 => 'Unprotected Admin Panel with Unpredictable URL',
            3 => 'Bypassing Admin Panel via User Role Manipulation',
            4 => 'IDOR Leading to Account Takeover',
            5 => 'User ID Controlled by Request Parameter',
            6 => 'User ID Controlled by Request Parameter with Unpredictable IDs',
            7 => 'User ID Controlled by Request Parameter with Data Leakage',
            8 => 'User ID Controlled by Request Parameter with Password Disclosure',
            9 => 'Insecure Direct Object Reference (IDOR)',
            10 => 'URL-Based Access Control Can Be Circumvented',
            11 => 'Method-Based Access Control Can Be Circumvented',
            12 => 'Multi-Step Process with Flawed Access Control',
            13 => 'Referer-Based Access Control',
            14 => 'IDOR via Mass Assignment',
            15 => 'IDOR Leads to Account Takeover via Email Change',
            16 => 'IDOR via Predictable Sequential IDs',
            17 => 'IDOR with Horizontal Privilege Escalation',
            18 => 'IDOR via Parameter Pollution',
            19 => 'IDOR in API Endpoint Leading to Data Breach',
            20 => 'IDOR via Encoded/Hashed IDs',
            21 => 'IDOR with JWT Token Manipulation',
            22 => 'IDOR via Indirect Object Reference',
            23 => 'Privilege Escalation via Role Parameter',
            24 => 'Vertical Privilege Escalation',
            25 => 'Broken Access Control in File Upload',
            26 => 'Access Control Bypass via Path Traversal',
            27 => 'HackerOne Report #1: Improper Access Control Leading to PII Disclosure',
            28 => 'HackerOne Report #2: IDOR Allowing Deletion of Any User Account',
            29 => 'HackerOne Report #3: Mass Assignment Leading to Admin Access',
            30 => 'IDOR via GraphQL Mutation',
        ]
    ],
    'Insecure-Deserialization' => [
        'name' => 'Insecure Deserialization',
        'color' => '#f97316',
        'icon' => '📦',
        'db' => 'id_progress',
        'total' => 10,
        'link' => '../Insecure-Deserialization/index.php',
        'description' => 'Object injection, gadget chains, PHAR exploits',
        'labs' => [
            1 => 'Modifying Serialized Objects',
            2 => 'Modifying Serialized Data Types',
            3 => 'Using Application Functionality to Exploit Deserialization',
            4 => 'Arbitrary Object Injection in PHP',
            5 => 'Exploiting PHP Deserialization with a Pre-Built Gadget Chain',
            6 => 'Exploiting Ruby Deserialization Using a Documented Gadget Chain',
            7 => 'Developing a Custom Gadget Chain for PHP',
            8 => 'Developing a Custom Gadget Chain for Java',
            9 => 'Using PHAR Deserialization to Deploy a Custom Gadget Chain',
            10 => 'Exploiting Deserialization via Cookie Tampering',
        ]
    ],
    'API' => [
        'name' => 'API Security',
        'color' => '#06b6d4',
        'icon' => '🔌',
        'db' => 'api_progress',
        'total' => 0,
        'link' => '../API/index.php',
        'description' => 'Coming soon - API vulnerabilities',
        'labs' => []
    ],
    'Authentication' => [
        'name' => 'Authentication',
        'color' => '#8b5cf6',
        'icon' => '🔑',
        'db' => 'auth_progress',
        'total' => 0,
        'link' => '../Authentication/index.php',
        'description' => 'Coming soon - Auth bypass techniques',
        'labs' => []
    ],
];

// Fetch solved labs from each category
$totalSolved = 0;
$totalLabs = 0;
$recentActivity = [];
$streakDays = 0;
$lastSolveDate = null;

foreach ($categories as $key => &$cat) {
    $cat['solved'] = 0;
    $cat['solvedLabs'] = [];
    $totalLabs += $cat['total'];
    
    $conn = @new mysqli($db_host, $db_user, $db_pass, $cat['db']);
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT lab_number, solved_at FROM solved_labs ORDER BY solved_at DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $cat['solvedLabs'][$row['lab_number']] = $row['solved_at'];
                $cat['solved']++;
                $totalSolved++;
                
                $recentActivity[] = [
                    'category' => $cat['name'],
                    'categoryKey' => $key,
                    'lab' => $row['lab_number'],
                    'labName' => $cat['labs'][$row['lab_number']] ?? "Lab {$row['lab_number']}",
                    'time' => $row['solved_at'],
                    'color' => $cat['color'],
                    'icon' => $cat['icon']
                ];
                
                // Track last solve for streak
                if (!$lastSolveDate) {
                    $lastSolveDate = date('Y-m-d', strtotime($row['solved_at']));
                }
            }
        }
        $conn->close();
    }
}
unset($cat);

// Sort recent activity by time
usort($recentActivity, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));
$recentActivity = array_slice($recentActivity, 0, 8);

$overallPercentage = $totalLabs > 0 ? round(($totalSolved / $totalLabs) * 100) : 0;

// Calculate streak (simplified)
if ($lastSolveDate === date('Y-m-d')) {
    $streakDays = 1; // At least today
}

// Handle reset actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $catKey = $_POST['category'] ?? '';
    $labNum = (int)($_POST['lab'] ?? 0);
    
    if (isset($categories[$catKey]) && $labNum > 0) {
        $conn = @new mysqli($db_host, $db_user, $db_pass, $categories[$catKey]['db']);
        if (!$conn->connect_error) {
            $stmt = $conn->prepare("DELETE FROM solved_labs WHERE lab_number = ?");
            $stmt->bind_param("i", $labNum);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            header("Location: progress.php?reset=1");
            exit;
        }
    }
}

// Handle reset all for a category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_category'])) {
    $catKey = $_POST['category'] ?? '';
    if (isset($categories[$catKey])) {
        $conn = @new mysqli($db_host, $db_user, $db_pass, $categories[$catKey]['db']);
        if (!$conn->connect_error) {
            $conn->query("DELETE FROM solved_labs");
            $conn->close();
            header("Location: progress.php?reset=category");
            exit;
        }
    }
}

$resetSuccess = isset($_GET['reset']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracker - LABx_Docs</title>
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
        
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        /* Hero Stats Section */
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-hover);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent);
        }
        
        .stat-card.success::before { background: var(--success); }
        .stat-card.warning::before { background: var(--warning); }
        .stat-card.danger::before { background: var(--danger); }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-card.success .stat-icon { background: var(--success-bg); }
        .stat-card.warning .stat-icon { background: var(--warning-bg); }
        .stat-card.danger .stat-icon { background: var(--danger-bg); }
        .stat-card .stat-icon { background: var(--accent-bg); }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-change {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .stat-change.up { background: var(--success-bg); color: var(--success); }
        
        /* Progress Ring */
        .progress-ring-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 3rem;
        }
        
        .progress-ring-container {
            position: relative;
            width: 180px;
            height: 180px;
            flex-shrink: 0;
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring circle {
            fill: none;
            stroke-width: 12;
        }
        
        .progress-ring .bg { stroke: var(--bg-tertiary); }
        .progress-ring .progress {
            stroke: var(--success);
            stroke-linecap: round;
            transition: stroke-dashoffset 1s ease;
        }
        
        .progress-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .progress-percent {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .progress-label { font-size: 0.85rem; color: var(--text-muted); }
        
        .progress-info { flex: 1; }
        .progress-info h2 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .progress-info p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .progress-bars { display: flex; flex-direction: column; gap: 1rem; }
        
        .progress-item { display: flex; align-items: center; gap: 1rem; }
        
        .progress-item-label {
            width: 180px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .progress-item-bar {
            flex: 1;
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-item-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .progress-item-value {
            width: 60px;
            text-align: right;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.85rem;
        }
        
        /* Grid Layout */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        /* Category Cards */
        .category-list {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .category-list-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .category-list-header h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .category-card {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .category-card:hover { background: var(--bg-card-hover); }
        .category-card:last-child { border-bottom: none; }
        
        .category-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        .category-details { flex: 1; min-width: 0; }
        
        .category-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .category-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        
        .category-progress-bar {
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
        
        .category-stats {
            text-align: right;
            flex-shrink: 0;
        }
        
        .category-count {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .category-percent {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        /* Activity Feed */
        .activity-feed {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .activity-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .activity-header h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .activity-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transition: background 0.2s;
        }
        
        .activity-item:hover { background: var(--bg-card-hover); }
        .activity-item:last-child { border-bottom: none; }
        
        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .activity-content { flex: 1; min-width: 0; }
        
        .activity-lab {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.15rem;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .activity-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .activity-badge {
            background: var(--success-bg);
            color: var(--success);
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--text-muted);
        }
        
        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Solved Labs Section */
        .solved-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .solved-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .solved-header:hover { background: var(--bg-card-hover); }
        
        .solved-header h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .solved-header .toggle-icon {
            transition: transform 0.2s;
            color: var(--text-muted);
        }
        
        .solved-header.collapsed .toggle-icon { transform: rotate(-90deg); }
        
        .solved-content { padding: 1rem 1.5rem; }
        .solved-content.hidden { display: none; }
        
        .solved-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 0.75rem;
        }
        
        .solved-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem;
            background: var(--success-bg);
            border-radius: 10px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        
        .solved-item:hover {
            border-color: var(--success);
            transform: translateX(4px);
        }
        
        .solved-check {
            width: 28px;
            height: 28px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        
        .solved-info { flex: 1; min-width: 0; }
        
        .solved-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .solved-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .btn-reset {
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            opacity: 0;
        }
        
        .solved-item:hover .btn-reset { opacity: 1; }
        .btn-reset:hover { background: var(--danger); color: white; }
        
        /* Alert */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--success-bg);
            border: 1px solid var(--success);
            color: var(--success);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .hero-stats { grid-template-columns: repeat(2, 1fr); }
            .content-grid { grid-template-columns: 1fr; }
            .progress-ring-section { flex-direction: column; text-align: center; }
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .mobile-toggle { display: flex; }
            .main-content { margin-left: 0; }
            .container { padding: 4rem 1rem 1rem; }
            .hero-stats { grid-template-columns: 1fr 1fr; gap: 1rem; }
            .stat-card { padding: 1rem; }
            .stat-value { font-size: 1.75rem; }
            .solved-grid { grid-template-columns: 1fr; }
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
            <?php if ($resetSuccess): ?>
            <div class="alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Progress has been reset successfully.
            </div>
            <?php endif; ?>
            
            <!-- Hero Stats -->
            <div class="hero-stats">
                <div class="stat-card success">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-value"><?php echo $totalSolved; ?></div>
                    <div class="stat-label">Labs Solved</div>
                    <?php if ($totalSolved > 0): ?>
                    <div class="stat-change up">+<?php echo $totalSolved; ?></div>
                    <?php endif; ?>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?php echo $totalLabs; ?></div>
                    <div class="stat-label">Total Labs</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">📊</div>
                    <div class="stat-value"><?php echo $overallPercentage; ?>%</div>
                    <div class="stat-label">Completion</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📁</div>
                    <div class="stat-value"><?php echo count(array_filter($categories, fn($c) => $c['total'] > 0)); ?></div>
                    <div class="stat-label">Categories</div>
                </div>
            </div>
            
            <!-- Progress Ring Section -->
            <div class="progress-ring-section">
                <div class="progress-ring-container">
                    <svg class="progress-ring" width="180" height="180">
                        <circle class="bg" cx="90" cy="90" r="78"/>
                        <circle class="progress" cx="90" cy="90" r="78" 
                                stroke-dasharray="490" 
                                stroke-dashoffset="<?php echo 490 - (490 * $overallPercentage / 100); ?>"/>
                    </svg>
                    <div class="progress-center">
                        <div class="progress-percent"><?php echo $overallPercentage; ?>%</div>
                        <div class="progress-label">Complete</div>
                    </div>
                </div>
                <div class="progress-info">
                    <h2>Overall Progress</h2>
                    <p>Track your journey through all security lab categories. Each solved lab brings you closer to mastering web application security vulnerabilities.</p>
                    <div class="progress-bars">
                        <?php foreach ($categories as $key => $cat): 
                            if ($cat['total'] === 0) continue;
                            $percentage = round(($cat['solved'] / $cat['total']) * 100);
                        ?>
                        <div class="progress-item">
                            <div class="progress-item-label">
                                <span><?php echo $cat['icon']; ?></span>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </div>
                            <div class="progress-item-bar">
                                <div class="progress-item-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $cat['color']; ?>;"></div>
                            </div>
                            <div class="progress-item-value"><?php echo $cat['solved']; ?>/<?php echo $cat['total']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Category List -->
                <div class="category-list">
                    <div class="category-list-header">
                        <h3>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                            </svg>
                            Categories
                        </h3>
                    </div>
                    <?php foreach ($categories as $key => $cat): 
                        $percentage = $cat['total'] > 0 ? round(($cat['solved'] / $cat['total']) * 100) : 0;
                    ?>
                    <a href="<?php echo $cat['link']; ?>" class="category-card">
                        <div class="category-icon" style="background: <?php echo $cat['color']; ?>20; color: <?php echo $cat['color']; ?>;">
                            <?php echo $cat['icon']; ?>
                        </div>
                        <div class="category-details">
                            <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                            <div class="category-desc"><?php echo htmlspecialchars($cat['description']); ?></div>
                            <div class="category-progress-bar">
                                <div class="category-progress-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $cat['color']; ?>;"></div>
                            </div>
                        </div>
                        <div class="category-stats">
                            <div class="category-count"><?php echo $cat['solved']; ?>/<?php echo $cat['total']; ?></div>
                            <div class="category-percent"><?php echo $percentage; ?>%</div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Activity Feed -->
                <div class="activity-feed">
                    <div class="activity-header">
                        <h3>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                            Recent Activity
                        </h3>
                    </div>
                    <?php if (empty($recentActivity)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🎯</div>
                        <p>No labs solved yet.<br>Start your journey!</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: <?php echo $activity['color']; ?>20; color: <?php echo $activity['color']; ?>;">
                            <?php echo $activity['icon']; ?>
                        </div>
                        <div class="activity-content">
                            <div class="activity-lab">Lab <?php echo $activity['lab']; ?>: <?php echo htmlspecialchars($activity['labName']); ?></div>
                            <div class="activity-meta">
                                <?php echo htmlspecialchars($activity['category']); ?> • <?php echo date('M j, g:i A', strtotime($activity['time'])); ?>
                            </div>
                        </div>
                        <span class="activity-badge">Solved</span>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Solved Labs per Category -->
            <?php foreach ($categories as $key => $cat): ?>
            <?php if ($cat['solved'] > 0): ?>
            <div class="solved-section">
                <div class="solved-header" onclick="toggleSection(this)">
                    <h3>
                        <span style="color: <?php echo $cat['color']; ?>;"><?php echo $cat['icon']; ?></span>
                        <?php echo htmlspecialchars($cat['name']); ?> — <?php echo $cat['solved']; ?> Solved
                    </h3>
                    <svg class="toggle-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
                <div class="solved-content">
                    <div class="solved-grid">
                        <?php foreach ($cat['solvedLabs'] as $labNum => $solvedAt): ?>
                        <div class="solved-item">
                            <div class="solved-check">✓</div>
                            <div class="solved-info">
                                <div class="solved-name">Lab <?php echo $labNum; ?>: <?php echo htmlspecialchars($cat['labs'][$labNum] ?? "Lab $labNum"); ?></div>
                                <div class="solved-time"><?php echo date('M j, Y \a\t g:i A', strtotime($solvedAt)); ?></div>
                            </div>
                            <form method="POST" style="margin: 0;" onsubmit="return confirm('Reset progress for this lab?');">
                                <input type="hidden" name="category" value="<?php echo $key; ?>">
                                <input type="hidden" name="lab" value="<?php echo $labNum; ?>">
                                <button type="submit" name="reset" class="btn-reset">Reset</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
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
        
        // Toggle solved sections
        function toggleSection(header) {
            header.classList.toggle('collapsed');
            header.nextElementSibling.classList.toggle('hidden');
        }
        
        // Animate progress ring on load
        document.addEventListener('DOMContentLoaded', () => {
            const ring = document.querySelector('.progress-ring .progress');
            if (ring) {
                const offset = ring.getAttribute('stroke-dashoffset');
                ring.style.strokeDashoffset = 490;
                setTimeout(() => {
                    ring.style.strokeDashoffset = offset;
                }, 100);
            }
        });
    </script>
</body>
</html>
