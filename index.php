<?php
/**
 * LABx_Docs - Web Security Training Platform
 * Main Home Page - Hub for all vulnerability categories
 * Dynamic integration with all category progress databases
 */

// Include centralized database configuration
require_once __DIR__ . '/db-config.php';

// Sidebar configuration (root level)
$basePath = '';
$activePage = 'home';

// Get database credentials from centralized config
$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_configured = $creds['configured'];

// Disable mysqli exceptions to handle missing databases gracefully
mysqli_report(MYSQLI_REPORT_OFF);

// Initialize stats arrays
$categories = [
    'AC' => [
        'name' => 'Access Control',
        'db' => 'ac_progress',
        'total' => 30,
        'apprentice' => 3,
        'practitioner' => 26,
        'expert' => 1,
        'solved' => 0,
        'status' => 'active',
        'topics' => ['IDOR', 'Privilege Escalation', 'Cookie Manipulation', 'Mass Assignment'],
        'description' => 'Master access control vulnerabilities including IDOR, privilege escalation, broken authorization, and real-world HackerOne case studies.',
        'link' => 'AC/index.php',
        'setup_link' => 'src/setup.php'
    ],
    'API' => [
        'name' => 'API Security',
        'db' => 'api_progress',
        'total' => 0,
        'apprentice' => 0,
        'practitioner' => 0,
        'expert' => 0,
        'solved' => 0,
        'status' => 'coming',
        'topics' => ['BOLA', 'Broken Auth', 'Data Exposure', 'Rate Limiting'],
        'description' => 'Learn to identify and exploit API vulnerabilities including broken authentication, excessive data exposure, and rate limiting issues.',
        'link' => 'API/index.php',
        'setup_link' => '#'
    ],
    'Authentication' => [
        'name' => 'Authentication',
        'db' => 'auth_progress',
        'total' => 0,
        'apprentice' => 0,
        'practitioner' => 0,
        'expert' => 0,
        'solved' => 0,
        'status' => 'coming',
        'topics' => ['Brute Force', 'Password Reset', '2FA Bypass', 'JWT Attacks'],
        'description' => 'Explore authentication flaws including brute force attacks, password reset poisoning, multi-factor bypass, and session vulnerabilities.',
        'link' => 'Authentication/index.php',
        'setup_link' => '#'
    ],
    'Insecure-Deserialization' => [
        'name' => 'Insecure Deserialization',
        'db' => 'id_progress',
        'total' => 10,
        'apprentice' => 4,
        'practitioner' => 4,
        'expert' => 2,
        'solved' => 0,
        'status' => 'active',
        'topics' => ['PHP Serialization', 'Object Injection', 'Magic Methods', 'Gadget Chains'],
        'description' => 'Exploit insecure deserialization vulnerabilities including PHP object injection, magic method abuse, and crafting exploitation gadget chains.',
        'link' => 'Insecure-Deserialization/index.php',
        'setup_link' => 'src/setup.php'
    ]
];

// Activity log for graph
$activity_data = [];
$recent_solves = [];

// Connect to each category database and fetch real stats
foreach ($categories as $key => &$cat) {
    $conn = @new mysqli($db_host, $db_user, $db_pass, $cat['db']);
    
    if (!$conn->connect_error) {
        // Get solved count
        $result = $conn->query("SELECT COUNT(*) as count FROM solved_labs");
        if ($result) {
            $row = $result->fetch_assoc();
            $cat['solved'] = (int)$row['count'];
        }
        
        // Get recent activity for graph (last 84 days - 12 weeks)
        $result = $conn->query("SELECT DATE(solved_at) as date, COUNT(*) as count FROM solved_labs WHERE solved_at >= DATE_SUB(NOW(), INTERVAL 84 DAY) GROUP BY DATE(solved_at)");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $date = $row['date'];
                if (!isset($activity_data[$date])) {
                    $activity_data[$date] = 0;
                }
                $activity_data[$date] += $row['count'];
            }
        }
        
        // Get recent solves with timestamps
        $result = $conn->query("SELECT lab_number, solved_at FROM solved_labs ORDER BY solved_at DESC LIMIT 10");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $recent_solves[] = [
                    'category' => $cat['name'],
                    'lab' => $row['lab_number'],
                    'time' => $row['solved_at']
                ];
            }
        }
        
        $conn->close();
    }
}
unset($cat);

// Sort recent solves by time
usort($recent_solves, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});
$recent_solves = array_slice($recent_solves, 0, 5);

// Calculate totals
$total_labs = 0;
$total_solved = 0;
$total_apprentice = 0;
$total_practitioner = 0;
$total_expert = 0;
$active_categories = 0;

foreach ($categories as $cat) {
    $total_labs += $cat['total'];
    $total_solved += $cat['solved'];
    $total_apprentice += $cat['apprentice'];
    $total_practitioner += $cat['practitioner'];
    $total_expert += $cat['expert'];
    if ($cat['status'] === 'active') {
        $active_categories++;
    }
}

// Calculate completion percentage
$completion_percentage = $total_labs > 0 ? round(($total_solved / $total_labs) * 100) : 0;

// Generate activity data for 84 days
$activity_levels = [];
$today = new DateTime();
for ($i = 83; $i >= 0; $i--) {
    $date = (clone $today)->modify("-$i days")->format('Y-m-d');
    $count = isset($activity_data[$date]) ? $activity_data[$date] : 0;
    
    // Determine level (0-4)
    if ($count === 0) $level = 0;
    elseif ($count === 1) $level = 1;
    elseif ($count <= 3) $level = 2;
    elseif ($count <= 5) $level = 3;
    else $level = 4;
    
    $activity_levels[] = ['date' => $date, 'count' => $count, 'level' => $level];
}

// Calculate vulnerability coverage (based on solved labs by type for AC)
$vulnerability_coverage = [
    'IDOR' => ['total' => 12, 'covered' => 0],
    'Privilege Escalation' => ['total' => 8, 'covered' => 0],
    'Auth Bypass' => ['total' => 6, 'covered' => 0],
    'Session Management' => ['total' => 4, 'covered' => 0]
];

// Map labs to vulnerability types for AC (simplified mapping)
$lab_vuln_map = [
    'IDOR' => [5, 6, 9, 14, 15, 16, 17, 18, 19, 20, 21, 22],
    'Privilege Escalation' => [4, 10, 11, 12, 13, 23, 24, 25],
    'Auth Bypass' => [1, 2, 3, 26, 27, 28],
    'Session Management' => [7, 8, 29, 30]
];

// Check which vulnerability labs are solved
$ac_conn = @new mysqli($db_host, $db_user, $db_pass, 'ac_progress');
if (!$ac_conn->connect_error) {
    $result = $ac_conn->query("SELECT lab_number FROM solved_labs");
    $solved_labs = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $solved_labs[] = (int)$row['lab_number'];
        }
    }
    
    foreach ($lab_vuln_map as $vuln => $labs) {
        $covered = 0;
        foreach ($labs as $lab) {
            if (in_array($lab, $solved_labs)) {
                $covered++;
            }
        }
        $vulnerability_coverage[$vuln]['covered'] = $covered;
    }
    $ac_conn->close();
}

// Calculate difficulty breakdown for donut chart (circumference = 2 * PI * 40 = 251.2)
$circumference = 251.2;
$ac = $categories['AC'];
$total_for_chart = $ac['total'];
if ($total_for_chart > 0) {
    $apprentice_dash = ($ac['apprentice'] / $total_for_chart) * $circumference;
    $practitioner_dash = ($ac['practitioner'] / $total_for_chart) * $circumference;
    $expert_dash = ($ac['expert'] / $total_for_chart) * $circumference;
} else {
    $apprentice_dash = $practitioner_dash = $expert_dash = 0;
}

// Time of day greeting
$hour = date('H');
if ($hour < 12) $greeting = 'Good morning';
elseif ($hour < 18) $greeting = 'Good afternoon';
else $greeting = 'Good evening';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LABx_Docs - Web Security Training Platform</title>
    <link rel="stylesheet" href="src/sidebar.css">
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
            --accent: #ffffff;
            --accent-muted: #888888;
            --success: #22c55e;
            --success-bg: rgba(34, 197, 94, 0.1);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.1);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.1);
            --info: #3b82f6;
            --info-bg: rgba(59, 130, 246, 0.1);
            --sidebar-width: 280px;
            --sidebar-collapsed: 70px;
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
            --accent: #0a0a0a;
            --accent-muted: #666666;
            --success: #16a34a;
            --success-bg: rgba(22, 163, 74, 0.1);
            --warning: #d97706;
            --warning-bg: rgba(217, 119, 6, 0.1);
            --danger: #dc2626;
            --danger-bg: rgba(220, 38, 38, 0.1);
            --info: #2563eb;
            --info-bg: rgba(37, 99, 235, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--bg-primary);
            color: var(--text-secondary);
            min-height: 100vh;
            line-height: 1.6;
            display: flex;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        /* Main content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        
        /* Hero Section */
        .hero {
            margin-bottom: 4rem;
            padding: 2rem 0;
        }
        
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 1rem;
            color: var(--text-primary);
            line-height: 1.2;
        }
        
        .hero p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin-bottom: 2rem;
        }
        
        .hero-stats {
            display: flex;
            gap: 3rem;
            padding: 1.5rem 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .hero-stat {
            text-align: left;
        }
        
        .hero-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }
        
        .hero-stat-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.35rem;
        }
        
        /* Section Headers */
        .section-header {
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        
        .section-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        /* Categories Grid */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.25rem;
            margin-bottom: 4rem;
        }
        
        .category-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            transform: translateY(-2px);
        }
        
        .category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .category-title h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .category-title h3 a {
            color: inherit;
            text-decoration: none;
        }
        
        .category-badge {
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .category-badge.active {
            background: var(--success-bg);
            color: var(--success);
        }
        
        .category-badge.coming {
            background: var(--bg-tertiary);
            color: var(--text-muted);
        }
        
        .category-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .category-meta {
            display: flex;
            gap: 1.25rem;
            margin-bottom: 1rem;
            padding: 0.75rem 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .category-meta-item {
            font-size: 0.8rem;
        }
        
        .category-meta-item strong {
            color: var(--text-primary);
        }
        
        .category-meta-item span {
            color: var(--text-muted);
        }
        
        .category-topics {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }
        
        .topic-tag {
            padding: 0.2rem 0.5rem;
            background: var(--bg-tertiary);
            border-radius: 4px;
            font-size: 0.7rem;
            color: var(--text-muted);
        }
        
        .category-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--text-primary);
            color: var(--bg-primary);
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .btn-secondary:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
        }
        
        .btn-disabled {
            background: var(--bg-tertiary);
            color: var(--text-muted);
            cursor: not-allowed;
        }
        
        /* Flow Section */
        .flow-section {
            margin-bottom: 4rem;
        }
        
        .flow-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
        }
        
        .flow-steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        
        .flow-step {
            text-align: center;
        }
        
        .flow-step-number {
            width: 48px;
            height: 48px;
            margin: 0 auto 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .flow-step h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }
        
        .flow-step p {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        
        /* Features Section */
        .features-section {
            margin-bottom: 3rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.25rem;
        }
        
        .feature-card h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }
        
        .feature-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        
        /* Stats Visual Section */
        .stats-section {
            margin-bottom: 4rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .stats-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .stats-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        
        .stats-card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .stats-card-value {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        /* Progress Bars */
        .progress-bars {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .progress-item {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
        }
        
        .progress-label span:first-child {
            color: var(--text-secondary);
        }
        
        .progress-label span:last-child {
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .progress-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease-out;
            position: relative;
        }
        
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .progress-fill.green { background: var(--success); }
        .progress-fill.blue { background: var(--info); }
        .progress-fill.orange { background: var(--warning); }
        .progress-fill.red { background: var(--danger); }
        
        /* Donut Chart */
        .chart-container {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .donut-chart {
            position: relative;
            width: 140px;
            height: 140px;
            flex-shrink: 0;
        }
        
        .donut-chart svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }
        
        .donut-chart circle {
            fill: none;
            stroke-width: 12;
            stroke-linecap: round;
        }
        
        .donut-bg {
            stroke: var(--bg-tertiary);
        }
        
        .donut-segment {
            transition: stroke-dashoffset 1s ease-out;
        }
        
        .donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .donut-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }
        
        .donut-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.25rem;
        }
        
        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
        }
        
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .legend-dot.green { background: var(--success); }
        .legend-dot.blue { background: var(--info); }
        .legend-dot.orange { background: var(--warning); }
        .legend-dot.red { background: var(--danger); }
        
        .legend-text {
            color: var(--text-secondary);
        }
        
        .legend-value {
            color: var(--text-primary);
            font-weight: 600;
            margin-left: auto;
        }
        
        /* Activity Graph */
        .activity-graph {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
            padding: 0.5rem 0;
        }
        
        .activity-cell {
            width: 14px;
            height: 14px;
            background: var(--bg-tertiary);
            border-radius: 3px;
            transition: all 0.2s ease;
        }
        
        .activity-cell:hover {
            transform: scale(1.2);
        }
        
        .activity-cell.level-1 { background: rgba(34, 197, 94, 0.2); }
        .activity-cell.level-2 { background: rgba(34, 197, 94, 0.4); }
        .activity-cell.level-3 { background: rgba(34, 197, 94, 0.6); }
        .activity-cell.level-4 { background: var(--success); }
        
        [data-theme="light"] .activity-cell.level-1 { background: rgba(22, 163, 74, 0.2); }
        [data-theme="light"] .activity-cell.level-2 { background: rgba(22, 163, 74, 0.4); }
        [data-theme="light"] .activity-cell.level-3 { background: rgba(22, 163, 74, 0.6); }
        
        .activity-legend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.75rem;
            font-size: 0.7rem;
            color: var(--text-muted);
        }
        
        .activity-legend-cells {
            display: flex;
            gap: 2px;
        }
        
        .activity-legend-cell {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }
        
        /* Skill Bars */
        .skill-bars {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .skill-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .skill-name {
            width: 120px;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .skill-bar {
            flex: 1;
            height: 6px;
            background: var(--bg-tertiary);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .skill-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--success), var(--info));
            border-radius: 3px;
            transition: width 1.2s ease-out;
        }
        
        .skill-value {
            width: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
        }
        
        /* Recent Activity Timeline */
        .recent-activity-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 1px solid var(--border-color);
        }
        
        .recent-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }
        
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        
        .timeline-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            position: relative;
        }
        
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 28px;
            bottom: -8px;
            width: 2px;
            background: var(--border-color);
        }
        
        .timeline-dot {
            width: 14px;
            height: 14px;
            min-width: 14px;
            background: var(--success);
            border-radius: 50%;
            margin-top: 2px;
            box-shadow: 0 0 0 4px var(--bg-secondary);
            position: relative;
            z-index: 1;
        }
        
        .timeline-content {
            flex: 1;
        }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .timeline-category {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .timeline-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .timeline-body {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        /* Database Config Section */
        .db-config-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .db-config-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .db-config-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .db-config-title h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        
        .db-led {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #666;
            box-shadow: 0 0 6px rgba(102, 102, 102, 0.5);
            transition: all 0.3s ease;
        }
        
        .db-led.connected {
            background: #22c55e;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.6), 0 0 20px rgba(34, 197, 94, 0.3);
            animation: pulse-green 2s infinite;
        }
        
        .db-led.error {
            background: #ef4444;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.6);
        }
        
        .db-led.testing {
            background: #f59e0b;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.6);
            animation: pulse-orange 1s infinite;
        }
        
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 10px rgba(34, 197, 94, 0.6), 0 0 20px rgba(34, 197, 94, 0.3); }
            50% { box-shadow: 0 0 15px rgba(34, 197, 94, 0.8), 0 0 30px rgba(34, 197, 94, 0.4); }
        }
        
        @keyframes pulse-orange {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .db-status-text {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .db-status-text.connected { color: var(--success); }
        .db-status-text.error { color: var(--danger); }
        
        .db-config-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 0.75rem;
            align-items: end;
        }
        
        .db-form-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        
        .db-form-group label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .db-form-group input {
            padding: 0.6rem 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 0.85rem;
            transition: border-color 0.2s ease;
        }
        
        .db-form-group input:focus {
            outline: none;
            border-color: var(--text-muted);
        }
        
        .db-form-group input::placeholder {
            color: var(--text-muted);
        }
        
        .db-test-btn {
            padding: 0.6rem 1.25rem;
            background: var(--text-primary);
            color: var(--bg-primary);
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s ease;
            white-space: nowrap;
        }
        
        .db-test-btn:hover {
            opacity: 0.9;
        }
        
        .db-test-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .db-config-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border-color);
        }
        
        .db-last-check {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        .db-clear-btn {
            font-size: 0.75rem;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: underline;
        }
        
        .db-clear-btn:hover {
            color: var(--danger);
        }
        
        @media (max-width: 768px) {
            .db-config-form {
                grid-template-columns: 1fr;
            }
            
            .db-test-btn {
                width: 100%;
            }
        }
        
        /* Decorative Background */
        .hero::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--border-color) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.5;
            pointer-events: none;
        }
        
        .hero {
            position: relative;
            overflow: hidden;
        }
        
        /* Footer */
        .footer {
            padding: 2rem 0;
            border-top: 1px solid var(--border-color);
            margin-top: 2rem;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-left p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .footer-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }
        
        .footer-links a:hover {
            color: var(--text-primary);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .flow-steps {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 900px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .hero h1 {
                font-size: 2.25rem;
            }
        }
        
        @media (max-width: 768px) {
            .mobile-toggle {
                display: flex;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .sidebar-overlay.open {
                display: block;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .container {
                padding: 4rem 1.5rem 2rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero-stats {
                flex-wrap: wrap;
                gap: 1.5rem;
            }
            
            .hero-stat {
                flex: 1;
                min-width: 80px;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .flow-steps {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .flow-container {
                padding: 1.5rem;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 4rem 1rem 1.5rem;
            }
            
            .hero h1 {
                font-size: 1.75rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .category-card {
                padding: 1.25rem;
            }
            
            .category-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <?php include __DIR__ . '/src/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="hero">
                <h1><?php echo $greeting; ?>!<br>Master Web Security</h1>
                <p>Hands-on vulnerable labs designed to teach real-world security flaws. You've solved <strong><?php echo $total_solved; ?></strong> of <strong><?php echo $total_labs; ?></strong> labs (<?php echo $completion_percentage; ?>% complete).</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo count($categories); ?></div>
                        <div class="hero-stat-label">Categories</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $total_labs; ?></div>
                        <div class="hero-stat-label">Total Labs</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $total_solved; ?></div>
                        <div class="hero-stat-label">Solved</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $completion_percentage; ?>%</div>
                        <div class="hero-stat-label">Complete</div>
                    </div>
                </div>
            </div>

            <!-- Database Configuration Section -->
            <div class="db-config-section" id="dbConfigSection">
                <div class="db-config-header">
                    <div class="db-config-title">
                        <div class="db-led" id="dbLed"></div>
                        <h3>Database Connection</h3>
                    </div>
                    <span class="db-status-text" id="dbStatusText">Checking...</span>
                </div>
                <form class="db-config-form" id="dbConfigForm" onsubmit="testConnection(event)">
                    <div class="db-form-group">
                        <label for="dbHost">Host</label>
                        <input type="text" id="dbHost" name="host" value="<?php echo htmlspecialchars($db_host); ?>" placeholder="localhost">
                    </div>
                    <div class="db-form-group">
                        <label for="dbUser">Username</label>
                        <input type="text" id="dbUser" name="user" value="<?php echo htmlspecialchars($db_user); ?>" placeholder="root">
                    </div>
                    <div class="db-form-group">
                        <label for="dbPass">Password</label>
                        <input type="password" id="dbPass" name="pass" placeholder="••••••••">
                    </div>
                    <button type="submit" class="db-test-btn" id="dbTestBtn">Test & Save</button>
                </form>
                <div class="db-config-footer">
                    <span class="db-last-check" id="dbLastCheck">Auto-test every 5 minutes</span>
                    <button class="db-clear-btn" onclick="clearCredentials()">Clear Credentials</button>
                </div>
            </div>

            <div class="section-header">
                <h2>Vulnerability Categories</h2>
                <p>Choose a category to start your security training</p>
            </div>

            <div class="categories-grid">
                <?php foreach ($categories as $key => $cat): ?>
                <div class="category-card">
                    <div class="category-header">
                        <div class="category-title">
                            <h3><a href="<?php echo $cat['link']; ?>"><?php echo $cat['name']; ?></a></h3>
                        </div>
                        <?php if ($cat['status'] === 'active'): ?>
                        <span class="category-badge active"><?php echo $cat['solved']; ?>/<?php echo $cat['total']; ?> Solved</span>
                        <?php else: ?>
                        <span class="category-badge coming">Coming Soon</span>
                        <?php endif; ?>
                    </div>
                    <p class="category-description"><?php echo $cat['description']; ?></p>
                    <div class="category-meta">
                        <div class="category-meta-item"><strong><?php echo $cat['apprentice'] ?: '-'; ?></strong> <span>Apprentice</span></div>
                        <div class="category-meta-item"><strong><?php echo $cat['practitioner'] ?: '-'; ?></strong> <span>Practitioner</span></div>
                        <div class="category-meta-item"><strong><?php echo $cat['expert'] ?: '-'; ?></strong> <span>Expert</span></div>
                    </div>
                    <div class="category-topics">
                        <?php foreach ($cat['topics'] as $topic): ?>
                        <span class="topic-tag"><?php echo $topic; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="category-actions">
                        <a href="<?php echo $cat['link']; ?>" class="btn btn-primary"><?php echo $cat['status'] === 'active' ? 'Explore Labs' : 'View Category'; ?></a>
                        <?php if ($cat['status'] === 'active'): ?>
                        <a href="<?php echo $cat['setup_link']; ?>" class="btn btn-secondary">Setup</a>
                        <?php else: ?>
                        <a href="#" class="btn btn-disabled">Setup</a>
                        <?php endif; ?>
                    </div>
                    <?php if ($cat['status'] === 'active' && $cat['total'] > 0): ?>
                    <div class="category-progress" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--text-muted);">Progress</span>
                            <span style="color: var(--text-primary); font-weight: 600;"><?php echo round(($cat['solved'] / $cat['total']) * 100); ?>%</span>
                        </div>
                        <div class="progress-bar" style="height: 6px;">
                            <div class="progress-fill green" style="width: <?php echo ($cat['solved'] / $cat['total']) * 100; ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Statistics Visual Section -->
            <div class="stats-section">
                <div class="section-header">
                    <h2>Platform Statistics</h2>
                    <p>Real-time overview of your progress and lab activity</p>
                </div>
                
                <div class="stats-grid">
                    <!-- Category Progress -->
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <span class="stats-card-title">Your Progress</span>
                            <span class="stats-card-value"><?php echo $total_solved; ?> / <?php echo $total_labs; ?> Labs</span>
                        </div>
                        <div class="progress-bars">
                            <?php foreach ($categories as $key => $cat): ?>
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span><?php echo $cat['name']; ?></span>
                                    <span><?php echo $cat['status'] === 'active' ? $cat['solved'] . '/' . $cat['total'] . ' solved' : 'Coming soon'; ?></span>
                                </div>
                                <div class="progress-bar">
                                    <?php 
                                    $progress = $cat['total'] > 0 ? ($cat['solved'] / $cat['total']) * 100 : 0;
                                    $color = $key === 'AC' ? 'green' : ($key === 'API' ? 'blue' : 'orange');
                                    ?>
                                    <div class="progress-fill <?php echo $color; ?>" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Difficulty Donut Chart -->
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <span class="stats-card-title">Difficulty Breakdown</span>
                            <span class="stats-card-value">All Categories</span>
                        </div>
                        <div class="chart-container">
                            <div class="donut-chart">
                                <svg viewBox="0 0 100 100">
                                    <circle class="donut-bg" cx="50" cy="50" r="40"/>
                                    <?php if ($total_labs > 0): ?>
                                    <circle class="donut-segment" cx="50" cy="50" r="40" 
                                        stroke="var(--success)" 
                                        stroke-dasharray="<?php echo ($total_apprentice / $total_labs) * $circumference; ?> <?php echo $circumference; ?>" 
                                        stroke-dashoffset="0"/>
                                    <circle class="donut-segment" cx="50" cy="50" r="40" 
                                        stroke="var(--warning)" 
                                        stroke-dasharray="<?php echo ($total_practitioner / $total_labs) * $circumference; ?> <?php echo $circumference; ?>" 
                                        stroke-dashoffset="-<?php echo ($total_apprentice / $total_labs) * $circumference; ?>"/>
                                    <circle class="donut-segment" cx="50" cy="50" r="40" 
                                        stroke="var(--danger)" 
                                        stroke-dasharray="<?php echo ($total_expert / $total_labs) * $circumference; ?> <?php echo $circumference; ?>" 
                                        stroke-dashoffset="-<?php echo (($total_apprentice + $total_practitioner) / $total_labs) * $circumference; ?>"/>
                                    <?php endif; ?>
                                </svg>
                                <div class="donut-center">
                                    <div class="donut-value"><?php echo $total_labs; ?></div>
                                    <div class="donut-label">Total</div>
                                </div>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <span class="legend-dot green"></span>
                                    <span class="legend-text">Apprentice</span>
                                    <span class="legend-value"><?php echo $total_apprentice; ?></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-dot orange"></span>
                                    <span class="legend-text">Practitioner</span>
                                    <span class="legend-value"><?php echo $total_practitioner; ?></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-dot red"></span>
                                    <span class="legend-text">Expert</span>
                                    <span class="legend-value"><?php echo $total_expert; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity Graph -->
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <span class="stats-card-title">Lab Activity</span>
                            <span class="stats-card-value"><?php echo array_sum(array_column($activity_levels, 'count')); ?> solves (12 weeks)</span>
                        </div>
                        <div class="activity-graph" id="activityGraph">
                            <?php foreach ($activity_levels as $day): ?>
                            <div class="activity-cell level-<?php echo $day['level']; ?>" title="<?php echo $day['date']; ?>: <?php echo $day['count']; ?> solve(s)"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="activity-legend">
                            <span>Less</span>
                            <div class="activity-legend-cells">
                                <div class="activity-legend-cell" style="background: var(--bg-tertiary)"></div>
                                <div class="activity-legend-cell level-1" style="background: rgba(34, 197, 94, 0.2)"></div>
                                <div class="activity-legend-cell level-2" style="background: rgba(34, 197, 94, 0.4)"></div>
                                <div class="activity-legend-cell level-3" style="background: rgba(34, 197, 94, 0.6)"></div>
                                <div class="activity-legend-cell level-4" style="background: var(--success)"></div>
                            </div>
                            <span>More</span>
                        </div>
                    </div>
                    
                    <!-- Skill Bars -->
                    <div class="stats-card">
                        <div class="stats-card-header">
                            <span class="stats-card-title">Vulnerability Coverage</span>
                            <span class="stats-card-value"><?php echo count($vulnerability_coverage); ?> types</span>
                        </div>
                        <div class="skill-bars">
                            <?php 
                            $skill_colors = ['var(--primary)', 'var(--success)', 'var(--warning)', 'var(--danger)'];
                            $i = 0;
                            foreach ($vulnerability_coverage as $vuln_name => $data): 
                                $color = $skill_colors[$i % count($skill_colors)];
                                $percentage = $data['total'] > 0 ? round(($data['covered'] / $data['total']) * 100) : 0;
                            ?>
                            <div class="skill-item">
                                <span class="skill-name"><?php echo htmlspecialchars($vuln_name); ?></span>
                                <div class="skill-bar">
                                    <div class="skill-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $color; ?>"></div>
                                </div>
                                <span class="skill-value"><?php echo $data['covered']; ?>/<?php echo $data['total']; ?></span>
                            </div>
                            <?php $i++; endforeach; ?>
                            <?php if (empty($vulnerability_coverage)): ?>
                            <div class="skill-item" style="opacity: 0.5; text-align: center;">
                                <span class="skill-name">No data yet - solve some labs!</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity Timeline -->
                <?php if (!empty($recent_solves)): ?>
                <div class="recent-activity-section">
                    <h3 class="recent-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        Recent Activity
                    </h3>
                    <div class="timeline">
                        <?php foreach ($recent_solves as $solve): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <span class="timeline-category"><?php echo htmlspecialchars($solve['category']); ?></span>
                                    <span class="timeline-time"><?php echo date('M j, g:i A', strtotime($solve['solved_at'])); ?></span>
                                </div>
                                <div class="timeline-body">
                                    Completed Lab <?php echo $solve['lab_number']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Lab Flow Section -->
            <div class="flow-section">
                <div class="section-header">
                    <h2>How It Works</h2>
                    <p>Follow this simple flow to complete each lab</p>
                </div>
                
                <div class="flow-container">
                    <div class="flow-steps">
                        <div class="flow-step">
                            <div class="flow-step-number">1</div>
                            <h4>Setup Database</h4>
                            <p>Initialize the lab database with sample data and vulnerable configurations</p>
                        </div>
                        <div class="flow-step">
                            <div class="flow-step-number">2</div>
                            <h4>Read Documentation</h4>
                            <p>Understand the vulnerability type and review hints before attempting</p>
                        </div>
                        <div class="flow-step">
                            <div class="flow-step-number">3</div>
                            <h4>Exploit & Solve</h4>
                            <p>Find and exploit the vulnerability to complete the lab objective</p>
                        </div>
                        <div class="flow-step">
                            <div class="flow-step-number">4</div>
                            <h4>Track Progress</h4>
                            <p>Lab is marked as solved automatically. Reset anytime to retry</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="features-section">
                <div class="section-header">
                    <h2>Platform Features</h2>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>Learn by Doing</h4>
                        <p>Each lab contains a real vulnerability to exploit. Practice in a safe environment before testing in the real world.</p>
                    </div>
                    <div class="feature-card">
                        <h4>Full Documentation</h4>
                        <p>Every lab includes detailed documentation explaining the vulnerability, attack vectors, and remediation steps.</p>
                    </div>
                    <div class="feature-card">
                        <h4>Progress Tracking</h4>
                        <p>Monitor your learning journey with built-in progress tracking. Mark labs as solved and see your completion rate.</p>
                    </div>
                    <div class="feature-card">
                        <h4>Reset Anytime</h4>
                        <p>Made a mistake? Reset any lab's database to start fresh without affecting other labs or your progress.</p>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-left">
                        <p>LABx_Docs — Web Security Training Platform</p>
                        <p style="margin-top: 0.25rem; opacity: 0.6;">Built for educational purposes only</p>
                    </div>
                    <div class="footer-links">
                        <a href="https://github.com/M9nx/LABx_Docs" target="_blank">Repository</a>
                        <a href="https://github.com/M9nx" target="_blank">Author</a>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <script>
        // Theme toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Sidebar toggle for mobile
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }
        
        // Close sidebar on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                document.querySelector('.sidebar').classList.remove('open');
                document.querySelector('.sidebar-overlay').classList.remove('open');
            }
        });
        
        // Animate progress bars on scroll
        function animateOnScroll() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.width = entry.target.dataset.width || entry.target.style.width;
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.progress-fill, .skill-fill').forEach(el => {
                observer.observe(el);
            });
        }
        
        // Database connection management
        let dbCheckInterval = null;
        
        function updateDbStatus(status, message) {
            const led = document.getElementById('dbLed');
            const statusText = document.getElementById('dbStatusText');
            const lastCheck = document.getElementById('dbLastCheck');
            const sidebarLed = document.getElementById('sidebarDbLed');
            const sidebarText = document.getElementById('sidebarDbText');
            
            led.className = 'db-led';
            statusText.className = 'db-status-text';
            sidebarLed.className = 'sidebar-db-led';
            
            if (status === 'connected') {
                led.classList.add('connected');
                statusText.classList.add('connected');
                statusText.textContent = message || 'Connected';
                sidebarLed.classList.add('connected');
                sidebarText.textContent = 'Connected';
            } else if (status === 'error') {
                led.classList.add('error');
                statusText.classList.add('error');
                statusText.textContent = message || 'Connection failed';
                sidebarLed.classList.add('error');
                sidebarText.textContent = 'Error';
            } else if (status === 'testing') {
                led.classList.add('testing');
                statusText.textContent = 'Testing...';
                sidebarLed.classList.add('testing');
                sidebarText.textContent = 'Testing...';
            } else {
                statusText.textContent = message || 'Not configured';
                sidebarText.textContent = 'Not configured';
            }
            
            lastCheck.textContent = 'Last check: ' + new Date().toLocaleTimeString() + ' • Auto-test every 5 min';
        }
        
        function testConnection(event) {
            if (event) event.preventDefault();
            
            const host = document.getElementById('dbHost').value || 'localhost';
            const user = document.getElementById('dbUser').value;
            const pass = document.getElementById('dbPass').value;
            const btn = document.getElementById('dbTestBtn');
            
            btn.disabled = true;
            btn.textContent = 'Testing...';
            updateDbStatus('testing');
            
            const formData = new FormData();
            formData.append('host', host);
            formData.append('user', user);
            formData.append('pass', pass);
            
            fetch('db-config.php?action=test', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDbStatus('connected', data.message);
                } else if (!data.configured) {
                    updateDbStatus('unconfigured', data.message);
                } else {
                    updateDbStatus('error', data.message);
                }
            })
            .catch(error => {
                updateDbStatus('error', 'Network error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Test & Save';
            });
        }
        
        function checkConnectionStatus() {
            fetch('db-config.php?action=status')
            .then(response => response.json())
            .then(data => {
                updateDbStatus(data.status, data.message);
            })
            .catch(error => {
                updateDbStatus('error', 'Network error');
            });
        }
        
        function clearCredentials() {
            if (!confirm('Clear saved database credentials?')) return;
            
            fetch('db-config.php?action=clear')
            .then(response => response.json())
            .then(data => {
                document.getElementById('dbUser').value = '';
                document.getElementById('dbPass').value = '';
                updateDbStatus('unconfigured', 'Credentials cleared');
            });
        }
        
        function startAutoCheck() {
            // Check immediately on load
            checkConnectionStatus();
            
            // Then check every 5 minutes (300000ms)
            dbCheckInterval = setInterval(checkConnectionStatus, 300000);
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            animateOnScroll();
            startAutoCheck();
        });
    </script>
</body>
</html>
