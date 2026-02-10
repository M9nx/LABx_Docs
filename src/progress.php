<?php
/**
 * LABx_Docs - Global Progress Tracker
 * View progress across ALL categories
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
        'labs' => []
    ],
    'Authentication' => [
        'name' => 'Authentication',
        'color' => '#8b5cf6',
        'icon' => '🔑',
        'db' => 'auth_progress',
        'total' => 0,
        'link' => '../Authentication/index.php',
        'labs' => []
    ],
];

// Fetch solved labs from each category
$progressData = [];
$totalSolved = 0;
$totalLabs = 0;
$recentActivity = [];

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
            }
        }
        $conn->close();
    }
}
unset($cat);

// Sort recent activity by time
usort($recentActivity, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));
$recentActivity = array_slice($recentActivity, 0, 10);

$overallPercentage = $totalLabs > 0 ? round(($totalSolved / $totalLabs) * 100) : 0;

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
            margin-bottom: 1rem;
        }
        
        .overall-stats {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .overall-stat { text-align: center; }
        .overall-stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .overall-stat-value.accent { color: var(--success); }
        .overall-stat-label { font-size: 0.85rem; color: var(--text-muted); }
        
        .progress-bar-large {
            height: 12px;
            background: var(--bg-tertiary);
            border-radius: 6px;
            overflow: hidden;
            margin-top: 1.5rem;
        }
        
        .progress-fill-large {
            height: 100%;
            background: linear-gradient(90deg, var(--success), #16a34a);
            border-radius: 6px;
            transition: width 0.5s ease;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            background: var(--success-bg);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .section-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .category-progress {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .category-item {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: background 0.2s;
        }
        
        .category-item:hover { background: var(--bg-card-hover); }
        .category-item:last-child { border-bottom: none; }
        
        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .category-info { flex: 1; }
        .category-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .category-progress-bar {
            height: 6px;
            background: var(--bg-tertiary);
            border-radius: 3px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .category-progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        
        .category-stats { text-align: right; }
        .category-count { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); }
        .category-percent { font-size: 0.8rem; color: var(--text-muted); }
        
        .recent-activity {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .recent-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .activity-item:last-child { border-bottom: none; }
        
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        
        .activity-info { flex: 1; min-width: 0; }
        .activity-lab {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .activity-time { font-size: 0.75rem; color: var(--text-muted); }
        
        .solved-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .solved-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
        }
        
        .solved-header h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .solved-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 0.75rem;
            padding: 1rem 1.5rem;
        }
        
        .solved-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--success-bg);
            border-radius: 8px;
        }
        
        .solved-check {
            width: 24px;
            height: 24px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
        }
        
        .solved-info { flex: 1; min-width: 0; }
        .solved-name {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .solved-time { font-size: 0.75rem; color: var(--text-muted); }
        
        .btn-reset {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            cursor: pointer;
        }
        .btn-reset:hover { background: var(--danger); color: white; }
        
        .empty-state { padding: 3rem; text-align: center; color: var(--text-muted); }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .section-grid { grid-template-columns: 1fr; }
            .solved-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>📊 Progress Tracker</h1>
                <div class="overall-stats">
                    <div class="overall-stat">
                        <div class="overall-stat-value accent"><?php echo $totalSolved; ?></div>
                        <div class="overall-stat-label">Labs Solved</div>
                    </div>
                    <div class="overall-stat">
                        <div class="overall-stat-value"><?php echo $totalLabs; ?></div>
                        <div class="overall-stat-label">Total Labs</div>
                    </div>
                    <div class="overall-stat">
                        <div class="overall-stat-value"><?php echo $overallPercentage; ?>%</div>
                        <div class="overall-stat-label">Complete</div>
                    </div>
                    <div class="overall-stat">
                        <div class="overall-stat-value"><?php echo count(array_filter($categories, fn($c) => $c['solved'] > 0)); ?></div>
                        <div class="overall-stat-label">Active Categories</div>
                    </div>
                </div>
                <div class="progress-bar-large">
                    <div class="progress-fill-large" style="width: <?php echo $overallPercentage; ?>%"></div>
                </div>
            </div>
            
            <?php if ($resetSuccess): ?>
            <div class="alert">Lab progress has been reset successfully.</div>
            <?php endif; ?>
            
            <div class="section-grid">
                <div class="category-progress">
                    <?php foreach ($categories as $key => $cat): 
                        $percentage = $cat['total'] > 0 ? round(($cat['solved'] / $cat['total']) * 100) : 0;
                    ?>
                    <a href="<?php echo $cat['link']; ?>" class="category-item" style="text-decoration: none;">
                        <div class="category-icon" style="background: <?php echo $cat['color']; ?>20; color: <?php echo $cat['color']; ?>;">
                            <?php echo $cat['icon']; ?>
                        </div>
                        <div class="category-info">
                            <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
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
                
                <div class="recent-activity">
                    <div class="recent-title">Recent Activity</div>
                    <?php if (empty($recentActivity)): ?>
                    <div class="empty-state">No labs solved yet</div>
                    <?php else: ?>
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: <?php echo $activity['color']; ?>20; color: <?php echo $activity['color']; ?>;">
                            <?php echo $activity['icon']; ?>
                        </div>
                        <div class="activity-info">
                            <div class="activity-lab">Lab <?php echo $activity['lab']; ?>: <?php echo htmlspecialchars($activity['labName']); ?></div>
                            <div class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['time'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php foreach ($categories as $key => $cat): ?>
            <?php if ($cat['solved'] > 0): ?>
            <div class="solved-section">
                <div class="solved-header">
                    <h3>
                        <span style="color: <?php echo $cat['color']; ?>;"><?php echo $cat['icon']; ?></span>
                        <?php echo htmlspecialchars($cat['name']); ?> - Solved Labs (<?php echo $cat['solved']; ?>)
                    </h3>
                </div>
                <div class="solved-grid">
                    <?php foreach ($cat['solvedLabs'] as $labNum => $solvedAt): ?>
                    <div class="solved-item">
                        <div class="solved-check">✓</div>
                        <div class="solved-info">
                            <div class="solved-name">Lab <?php echo $labNum; ?>: <?php echo htmlspecialchars($cat['labs'][$labNum] ?? "Lab $labNum"); ?></div>
                            <div class="solved-time"><?php echo date('M j, Y', strtotime($solvedAt)); ?></div>
                        </div>
                        <form method="POST" style="margin: 0;" onsubmit="return confirm('Reset this lab progress?');">
                            <input type="hidden" name="category" value="<?php echo $key; ?>">
                            <input type="hidden" name="lab" value="<?php echo $labNum; ?>">
                            <button type="submit" name="reset" class="btn-reset">Reset</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
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
