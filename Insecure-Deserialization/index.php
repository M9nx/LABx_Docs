<?php
/**
 * LABx_Docs - Insecure Deserialization Category
 * Lists all Insecure Deserialization labs with progress tracking
 */

// Use centralized database configuration
require_once __DIR__ . '/../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'id_progress';

mysqli_report(MYSQLI_REPORT_OFF);

// Check DB connection status for sidebar
$dbConnected = false;
$testConn = @new mysqli($db_host, $db_user, $db_pass);
if (!$testConn->connect_error) {
    $dbConnected = true;
    $testConn->close();
}

// Get solved labs
$solvedLabs = [];
$solvedCount = 0;

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if (!$conn->connect_error) {
    $result = $conn->query("SELECT lab_number, solved_at FROM solved_labs ORDER BY lab_number");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $solvedLabs[$row['lab_number']] = $row['solved_at'];
        }
        $solvedCount = count($solvedLabs);
    }
    $conn->close();
}

// Lab definitions - Insecure Deserialization labs
$labs = [
    1 => ['title' => 'Modifying Serialized Objects', 'difficulty' => 'Apprentice', 'type' => 'PHP Serialization'],
    2 => ['title' => 'Modifying Serialized Data Types', 'difficulty' => 'Practitioner', 'type' => 'Type Juggling'],
    3 => ['title' => 'Using Application Functionality to Exploit Deserialization', 'difficulty' => 'Practitioner', 'type' => 'File Deletion'],
    4 => ['title' => 'Arbitrary Object Injection in PHP', 'difficulty' => 'Practitioner', 'type' => 'Magic Methods'],
    5 => ['title' => 'Exploiting PHP Deserialization with a Pre-Built Gadget Chain', 'difficulty' => 'Practitioner', 'type' => 'Gadget Chains'],
    6 => ['title' => 'Exploiting Ruby Deserialization Using a Documented Gadget Chain', 'difficulty' => 'Practitioner', 'type' => 'Ruby Marshal'],
    7 => ['title' => 'Developing a Custom Gadget Chain for PHP', 'difficulty' => 'Expert', 'type' => 'Custom Gadgets'],
    8 => ['title' => 'Developing a Custom Gadget Chain for Java', 'difficulty' => 'Expert', 'type' => 'Java Serialization'],
    9 => ['title' => 'Using PHAR Deserialization to Deploy a Custom Gadget Chain', 'difficulty' => 'Expert', 'type' => 'PHAR Archives'],
    10 => ['title' => 'Exploiting Deserialization via Cookie Tampering', 'difficulty' => 'Practitioner', 'type' => 'Cookie Manipulation'],
];

// Count by difficulty
$apprenticeCount = 0;
$practitionerCount = 0;
$expertCount = 0;
foreach ($labs as $lab) {
    if ($lab['difficulty'] === 'Apprentice') $apprenticeCount++;
    elseif ($lab['difficulty'] === 'Practitioner') $practitionerCount++;
    else $expertCount++;
}

$totalLabs = count($labs);
$completionPercentage = round(($solvedCount / $totalLabs) * 100);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insecure Deserialization Labs - LABx_Docs</title>
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
            --accent: #f97316;
            --accent-muted: #ea580c;
            --accent-bg: rgba(249, 115, 22, 0.1);
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
            --accent: #ea580c;
            --accent-muted: #c2410c;
            --accent-bg: rgba(234, 88, 12, 0.1);
            --success: #16a34a;
            --success-bg: rgba(22, 163, 74, 0.1);
            --warning: #d97706;
            --warning-bg: rgba(217, 119, 6, 0.1);
            --danger: #dc2626;
            --danger-bg: rgba(220, 38, 38, 0.1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--bg-primary);
            color: var(--text-secondary);
            min-height: 100vh;
            line-height: 1.6;
            display: flex;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid var(--border-color); }
        
        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logo span { color: var(--text-muted); font-weight: 400; }
        
        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--accent-bg);
            border: 1px solid var(--accent);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--accent);
        }
        
        .sidebar-nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
        .nav-section { padding: 0 1rem; margin-bottom: 1.5rem; }
        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }
        
        .nav-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .nav-item.active { background: var(--accent-bg); color: var(--accent); }
        
        .nav-item-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
        }
        
        .nav-item-icon svg { width: 18px; height: 18px; stroke: currentColor; stroke-width: 2; fill: none; }
        
        .nav-badge {
            margin-left: auto;
            padding: 0.15rem 0.5rem;
            background: var(--success-bg);
            color: var(--success);
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .nav-badge.coming { background: var(--bg-tertiary); color: var(--text-muted); }
        
        .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); }
        
        /* Sidebar DB Status Indicator */
        .sidebar-db-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border-radius: 8px;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--text-secondary);
        }
        .sidebar-db-status:hover { background: var(--bg-card-hover); }
        .sidebar-db-info { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; }
        .sidebar-db-led {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #666;
            box-shadow: 0 0 0 2px rgba(102, 102, 102, 0.2);
            transition: all 0.3s ease;
        }
        .sidebar-db-led.connected {
            background: #10b981;
            box-shadow: 0 0 8px #10b981, 0 0 0 2px rgba(16, 185, 129, 0.2);
            animation: sidebarPulse 2s infinite;
        }
        .sidebar-db-led.error {
            background: #ef4444;
            box-shadow: 0 0 8px #ef4444, 0 0 0 2px rgba(239, 68, 68, 0.2);
        }
        @keyframes sidebarPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        
        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .theme-toggle:hover { background: var(--bg-card-hover); }
        .theme-toggle-label { font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem; }
        
        .theme-toggle-switch {
            width: 44px;
            height: 24px;
            background: var(--border-color);
            border-radius: 12px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .theme-toggle-switch::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            background: var(--text-primary);
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: all 0.3s ease;
        }
        
        [data-theme="light"] .theme-toggle-switch::after { left: 23px; }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .top-bar {
            height: 60px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb a:hover { color: var(--accent); }
        .breadcrumb span { color: var(--text-muted); }
        .breadcrumb-current { color: var(--text-primary); font-weight: 500; }
        
        .top-bar-actions { display: flex; align-items: center; gap: 1rem; }
        
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .theme-toggle:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            color: var(--text-primary);
        }
        
        .content-area { padding: 2rem; }
        
        /* Category Header */
        .category-header {
            background: linear-gradient(135deg, var(--accent-bg), transparent);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .category-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .category-icon {
            width: 56px;
            height: 56px;
            background: var(--accent);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .category-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .category-description {
            color: var(--text-secondary);
            max-width: 800px;
            margin-bottom: 1.5rem;
        }
        
        /* Stats */
        .stats-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        /* Progress Bar */
        .progress-section { margin-bottom: 2rem; }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .progress-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .progress-percentage {
            font-size: 0.85rem;
            color: var(--accent);
            font-weight: 600;
        }
        
        .progress-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent-muted));
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        /* Labs Grid */
        .labs-grid {
            display: grid;
            gap: 1rem;
        }
        
        .lab-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .lab-card:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            transform: translateX(4px);
        }
        
        .lab-card.solved {
            border-left: 3px solid var(--success);
        }
        
        .lab-number {
            width: 40px;
            height: 40px;
            background: var(--bg-tertiary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .lab-card.solved .lab-number {
            background: var(--success-bg);
            color: var(--success);
        }
        
        .lab-info { flex: 1; min-width: 0; }
        
        .lab-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .lab-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.8rem;
        }
        
        .lab-difficulty {
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 0.5px;
        }
        
        .difficulty-apprentice {
            background: var(--success-bg);
            color: var(--success);
        }
        
        .difficulty-practitioner {
            background: var(--warning-bg);
            color: var(--warning);
        }
        
        .difficulty-expert {
            background: var(--danger-bg);
            color: var(--danger);
        }
        
        .lab-type { color: var(--text-muted); }
        
        .lab-status {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            flex-shrink: 0;
        }
        
        .lab-card.solved .lab-status { color: var(--success); }
        
        /* Setup Button */
        .setup-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-setup {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-setup:hover {
            background: var(--accent-muted);
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="../index.php" class="logo">
                <span class="logo-icon">L</span>
                LABx<span>_Docs</span>
            </a>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Overview</div>
                <a href="../index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                    Home
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Categories</div>
                <a href="../AC/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    Access Control
                    <span class="nav-badge">0/30</span>
                </a>
                <a href="../API/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></span>
                    API Security
                    <span class="nav-badge coming">Soon</span>
                </a>
                <a href="../Authentication/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg></span>
                    Authentication
                    <span class="nav-badge coming">Soon</span>
                </a>
                <a href="index.php" class="nav-item active">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
                    Insecure Deserialization
                    <span class="nav-badge"><?php echo $solvedCount; ?>/<?php echo $totalLabs; ?></span>
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Quick Actions</div>
                <a href="../src/setup.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg></span>
                    Setup All Databases
                </a>
                <a href="../src/progress.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
                    View Progress
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Resources</div>
                <a href="https://github.com/M9nx/LABx_Docs" target="_blank" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg></span>
                    GitHub
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <a href="../src/setup.php" class="sidebar-db-status" title="<?php echo $dbConnected ? 'Database Connected' : 'Click to configure database'; ?>">
                <div class="sidebar-db-info">
                    <div class="sidebar-db-led <?php echo $dbConnected ? 'connected' : 'error'; ?>"></div>
                    <span><?php echo $dbConnected ? 'Connected' : 'Not Connected'; ?></span>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.5;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </a>
            <div class="theme-toggle" onclick="toggleTheme()">
                <span class="theme-toggle-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    Dark Mode
                </span>
                <span class="theme-toggle-switch"></span>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="breadcrumb">
                <a href="../index.php">Home</a>
                <span>/</span>
                <span class="breadcrumb-current">Insecure Deserialization</span>
            </div>
            <div class="top-bar-actions">
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                    🌙
                </button>
            </div>
        </div>

        <div class="content-area">
            <div class="category-header">
                <div class="category-title">
                    <div class="category-icon">📦</div>
                    <h1>Insecure Deserialization</h1>
                </div>
                <p class="category-description">
                    Master insecure deserialization vulnerabilities including PHP object injection, 
                    cookie tampering, magic method exploitation, gadget chains, and PHAR deserialization attacks.
                </p>
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $totalLabs; ?></span>
                        <span class="stat-label">Total Labs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: var(--success)"><?php echo $apprenticeCount; ?></span>
                        <span class="stat-label">Apprentice</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: var(--warning)"><?php echo $practitionerCount; ?></span>
                        <span class="stat-label">Practitioner</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" style="color: var(--danger)"><?php echo $expertCount; ?></span>
                        <span class="stat-label">Expert</span>
                    </div>
                </div>
            </div>

            <div class="progress-section">
                <div class="progress-header">
                    <span class="progress-title">Overall Progress</span>
                    <span class="progress-percentage"><?php echo $completionPercentage; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $completionPercentage; ?>%"></div>
                </div>
            </div>

            <div class="labs-grid">
                <?php foreach ($labs as $num => $lab): 
                    $isSolved = isset($solvedLabs[$num]);
                    $diffClass = strtolower($lab['difficulty']);
                    $labFolder = "Lab-" . str_pad($num, 2, '0', STR_PAD_LEFT);
                    $labExists = is_dir(__DIR__ . '/' . $labFolder);
                ?>
                <a href="<?php echo $labExists ? $labFolder . '/index.php' : '#'; ?>" 
                   class="lab-card <?php echo $isSolved ? 'solved' : ''; ?> <?php echo !$labExists ? 'disabled' : ''; ?>"
                   <?php echo !$labExists ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>>
                    <div class="lab-number"><?php echo $num; ?></div>
                    <div class="lab-info">
                        <div class="lab-title"><?php echo htmlspecialchars($lab['title']); ?></div>
                        <div class="lab-meta">
                            <span class="lab-difficulty difficulty-<?php echo $diffClass; ?>">
                                <?php echo $lab['difficulty']; ?>
                            </span>
                            <span class="lab-type"><?php echo htmlspecialchars($lab['type']); ?></span>
                        </div>
                    </div>
                    <div class="lab-status">
                        <?php echo $isSolved ? '✓' : '→'; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="setup-section">
                <a href="../src/setup.php" class="btn-setup">
                    ⚙️ Setup All Databases
                </a>
            </div>
        </div>
    </main>

    <script>
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
    </script>
</body>
</html>
