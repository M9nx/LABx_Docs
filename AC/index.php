<?php
/**
 * LABx_Docs - Access Control Category
 * Lists all 30 Access Control labs with progress tracking
 */

// Use centralized database configuration
require_once __DIR__ . '/../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'ac_progress';

mysqli_report(MYSQLI_REPORT_OFF);

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

// Lab definitions
$labs = [
    1 => ['title' => 'Unprotected Admin Functionality', 'difficulty' => 'Apprentice', 'type' => 'Robots File Disclosure'],
    2 => ['title' => 'Unprotected Admin Panel with Unpredictable URL', 'difficulty' => 'Apprentice', 'type' => 'JS Source Disclosure'],
    3 => ['title' => 'Bypassing Admin Panel via User Role Manipulation', 'difficulty' => 'Apprentice', 'type' => 'Cookie Manipulation'],
    4 => ['title' => 'IDOR Leading to Account Takeover', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    5 => ['title' => 'User ID Controlled by Request Parameter', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    6 => ['title' => 'User ID Controlled by Request Parameter with Unpredictable IDs', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    7 => ['title' => 'User ID Controlled by Request Parameter with Data Leakage', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    8 => ['title' => 'User ID Controlled by Request Parameter with Password Disclosure', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    9 => ['title' => 'Insecure Direct Object Reference (IDOR)', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    10 => ['title' => 'URL-Based Access Control Can Be Circumvented', 'difficulty' => 'Practitioner', 'type' => 'Header Bypass'],
    11 => ['title' => 'Method-Based Access Control Can Be Circumvented', 'difficulty' => 'Practitioner', 'type' => 'HTTP Method'],
    12 => ['title' => 'Multi-Step Process with Flawed Access Control', 'difficulty' => 'Practitioner', 'type' => 'Multi-Step Bypass'],
    13 => ['title' => 'Referer-Based Access Control', 'difficulty' => 'Practitioner', 'type' => 'Header Bypass'],
    14 => ['title' => 'IDOR via Mass Assignment', 'difficulty' => 'Practitioner', 'type' => 'Mass Assignment'],
    15 => ['title' => 'IDOR Leads to Account Takeover via Email Change', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    16 => ['title' => 'IDOR via Predictable Sequential IDs', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    17 => ['title' => 'IDOR with Horizontal Privilege Escalation', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    18 => ['title' => 'IDOR via Parameter Pollution', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    19 => ['title' => 'IDOR in API Endpoint Leading to Data Breach', 'difficulty' => 'Practitioner', 'type' => 'API IDOR'],
    20 => ['title' => 'IDOR via Encoded/Hashed IDs', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    21 => ['title' => 'IDOR with JWT Token Manipulation', 'difficulty' => 'Practitioner', 'type' => 'JWT IDOR'],
    22 => ['title' => 'IDOR via Indirect Object Reference', 'difficulty' => 'Practitioner', 'type' => 'IDOR'],
    23 => ['title' => 'Privilege Escalation via Role Parameter', 'difficulty' => 'Practitioner', 'type' => 'Privilege Escalation'],
    24 => ['title' => 'Vertical Privilege Escalation', 'difficulty' => 'Practitioner', 'type' => 'Privilege Escalation'],
    25 => ['title' => 'Broken Access Control in File Upload', 'difficulty' => 'Practitioner', 'type' => 'File Upload'],
    26 => ['title' => 'Access Control Bypass via Path Traversal', 'difficulty' => 'Practitioner', 'type' => 'Path Traversal'],
    27 => ['title' => 'HackerOne Report #1: Improper Access Control Leading to PII Disclosure', 'difficulty' => 'Practitioner', 'type' => 'Real Case'],
    28 => ['title' => 'HackerOne Report #2: IDOR Allowing Deletion of Any User Account', 'difficulty' => 'Practitioner', 'type' => 'Real Case'],
    29 => ['title' => 'HackerOne Report #3: Mass Assignment Leading to Admin Access', 'difficulty' => 'Practitioner', 'type' => 'Real Case'],
    30 => ['title' => 'IDOR via GraphQL Mutation', 'difficulty' => 'Expert', 'type' => 'GraphQL IDOR'],
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

$completionPercentage = round(($solvedCount / 30) * 100);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control Labs - LABx_Docs</title>
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
            --accent: #ef4444;
            --accent-muted: #dc2626;
            --accent-bg: rgba(239, 68, 68, 0.1);
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
            --accent: #dc2626;
            --accent-muted: #b91c1c;
            --accent-bg: rgba(220, 38, 38, 0.1);
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
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
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
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-primary);
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
        .nav-item.active { background: var(--bg-tertiary); color: var(--text-primary); }
        
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
        
        /* Mobile */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1100;
            width: 44px;
            height: 44px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        
        .mobile-toggle svg { width: 22px; height: 22px; stroke: var(--text-primary); stroke-width: 2; fill: none; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 999; }
        
        /* Main content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem; }
        
        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.2s ease;
        }
        
        .breadcrumb a:hover { color: var(--accent); }
        .breadcrumb span { color: var(--text-muted); }
        .breadcrumb .current { color: var(--text-primary); font-weight: 500; }
        
        /* Hero */
        .hero {
            margin-bottom: 2.5rem;
            padding: 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }
        
        .hero h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .hero p { color: var(--text-muted); font-size: 1rem; margin-bottom: 1.5rem; }
        
        .hero-stats { display: flex; gap: 2rem; flex-wrap: wrap; }
        
        .hero-stat {
            text-align: left;
            padding: 1rem 1.5rem;
            background: var(--bg-tertiary);
            border-radius: 10px;
            min-width: 100px;
        }
        
        .hero-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .hero-stat-value.accent { color: var(--accent); }
        .hero-stat-label { color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Filter */
        .filter-bar {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 0.5rem 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .filter-btn:hover { border-color: var(--border-hover); color: var(--text-primary); }
        .filter-btn.active { background: var(--accent-bg); border-color: var(--accent); color: var(--accent); }
        
        /* Labs Table */
        .labs-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .labs-table th {
            text-align: left;
            padding: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background: var(--bg-tertiary);
            border-bottom: 1px solid var(--border-color);
        }
        
        .labs-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }
        
        .labs-table tr:last-child td { border-bottom: none; }
        .labs-table tr:hover td { background: var(--bg-card-hover); }
        
        .lab-number {
            width: 50px;
            font-weight: 600;
            color: var(--text-muted);
        }
        
        .lab-title a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .lab-title a:hover { color: var(--accent); }
        
        .lab-type {
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        
        .difficulty-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .difficulty-badge.apprentice { background: var(--success-bg); color: var(--success); }
        .difficulty-badge.practitioner { background: var(--warning-bg); color: var(--warning); }
        .difficulty-badge.expert { background: var(--danger-bg); color: var(--danger); }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.solved { background: var(--success-bg); color: var(--success); }
        .status-badge.unsolved { background: var(--bg-tertiary); color: var(--text-muted); }
        
        /* Footer */
        .footer {
            padding: 2rem 0;
            border-top: 1px solid var(--border-color);
            margin-top: 2rem;
            text-align: center;
        }
        
        .footer p { color: var(--text-muted); font-size: 0.85rem; }
        .footer a { color: var(--accent); text-decoration: none; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .mobile-toggle { display: flex; }
            .main-content { margin-left: 0; }
            .container { padding: 4rem 1rem 1rem; }
            .hero h1 { font-size: 1.5rem; }
            .hero-stats { gap: 1rem; }
            .labs-table th:nth-child(3), .labs-table td:nth-child(3),
            .labs-table th:nth-child(4), .labs-table td:nth-child(4) { display: none; }
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>
    
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
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
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                    Home
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Categories</div>
                <a href="index.php" class="nav-item active">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    Access Control
                    <span class="nav-badge"><?php echo $solvedCount; ?>/30</span>
                </a>
                <a href="../API/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></span>
                    API Security
                    <span class="nav-badge coming">Soon</span>
                </a>
                <a href="../Authentication/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg></span>
                    Authentication
                    <span class="nav-badge coming">Soon</span>
                </a>
                <a href="../Insecure-Deserialization/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
                    Insecure Deserialization
                    <span class="nav-badge">0/10</span>
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Quick Actions</div>
                <a href="../src/setup.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg></span>
                    Setup All Databases
                </a>
                <a href="../src/progress.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
                    View Progress
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Resources</div>
                <a href="https://github.com/M9nx/LABx_Docs" target="_blank" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg></span>
                    GitHub
                </a>
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <div class="theme-toggle" onclick="toggleTheme()">
                <span class="theme-toggle-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    Dark Mode
                </span>
                <span class="theme-toggle-switch"></span>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="../index.php">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Home
                </a>
                <span>/</span>
                <span class="current">Access Control</span>
            </nav>
            
            <!-- Hero -->
            <div class="hero">
                <h1>Access Control Labs</h1>
                <p>Master IDOR, privilege escalation, broken authorization, and real-world HackerOne case studies</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value accent"><?php echo $solvedCount; ?>/30</div>
                        <div class="hero-stat-label">Solved</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $completionPercentage; ?>%</div>
                        <div class="hero-stat-label">Complete</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $apprenticeCount; ?></div>
                        <div class="hero-stat-label">Apprentice</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $practitionerCount; ?></div>
                        <div class="hero-stat-label">Practitioner</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value"><?php echo $expertCount; ?></div>
                        <div class="hero-stat-label">Expert</div>
                    </div>
                </div>
            </div>
            
            <!-- Filter -->
            <div class="filter-bar">
                <button class="filter-btn active" data-filter="all">All Labs</button>
                <button class="filter-btn" data-filter="apprentice">Apprentice</button>
                <button class="filter-btn" data-filter="practitioner">Practitioner</button>
                <button class="filter-btn" data-filter="expert">Expert</button>
                <button class="filter-btn" data-filter="solved">Solved</button>
                <button class="filter-btn" data-filter="unsolved">Unsolved</button>
            </div>
            
            <!-- Labs Table -->
            <table class="labs-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lab Title</th>
                        <th>Type</th>
                        <th>Difficulty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($labs as $num => $lab): 
                        $isSolved = isset($solvedLabs[$num]);
                        $diffClass = strtolower($lab['difficulty']);
                    ?>
                    <tr data-difficulty="<?php echo $diffClass; ?>" data-status="<?php echo $isSolved ? 'solved' : 'unsolved'; ?>">
                        <td class="lab-number"><?php echo str_pad($num, 2, '0', STR_PAD_LEFT); ?></td>
                        <td class="lab-title"><a href="Lab-<?php echo str_pad($num, 2, '0', STR_PAD_LEFT); ?>/index.php"><?php echo htmlspecialchars($lab['title']); ?></a></td>
                        <td class="lab-type"><?php echo htmlspecialchars($lab['type']); ?></td>
                        <td><span class="difficulty-badge <?php echo $diffClass; ?>"><?php echo $lab['difficulty']; ?></span></td>
                        <td>
                            <?php if ($isSolved): ?>
                            <span class="status-badge solved">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Solved
                            </span>
                            <?php else: ?>
                            <span class="status-badge unsolved">Unsolved</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <footer class="footer">
                <p>LABx_Docs — <a href="../index.php">Back to Home</a></p>
            </footer>
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
        
        // Filter
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const filter = btn.dataset.filter;
                document.querySelectorAll('.labs-table tbody tr').forEach(row => {
                    const difficulty = row.dataset.difficulty;
                    const status = row.dataset.status;
                    
                    if (filter === 'all') {
                        row.style.display = '';
                    } else if (filter === 'solved' || filter === 'unsolved') {
                        row.style.display = status === filter ? '' : 'none';
                    } else {
                        row.style.display = difficulty === filter ? '' : 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
