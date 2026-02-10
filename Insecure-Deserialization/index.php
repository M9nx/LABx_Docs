<?php
/**
 * LABx_Docs - Insecure Deserialization Category
 * Lists all Insecure Deserialization labs with progress tracking
 */

// Use centralized database configuration
require_once __DIR__ . '/../db-config.php';

// Sidebar configuration
$basePath = '../';
$activePage = 'id';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'id_progress';

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
    <link rel="stylesheet" href="../src/sidebar.css">
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
    
    <?php include __DIR__ . '/../src/sidebar.php'; ?>
    
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
                <span class="current">Insecure Deserialization</span>
            </nav>
            
            <!-- Hero -->
            <div class="hero">
                <h1>Insecure Deserialization Labs</h1>
                <p>Master PHP object injection, cookie tampering, magic methods, gadget chains, and PHAR deserialization attacks</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value accent"><?php echo $solvedCount; ?>/<?php echo $totalLabs; ?></div>
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
                        $labFolder = "Lab-" . str_pad($num, 2, '0', STR_PAD_LEFT);
                        $labExists = is_dir(__DIR__ . '/' . $labFolder);
                    ?>
                    <tr data-difficulty="<?php echo $diffClass; ?>" data-status="<?php echo $isSolved ? 'solved' : 'unsolved'; ?>">
                        <td class="lab-number"><?php echo str_pad($num, 2, '0', STR_PAD_LEFT); ?></td>
                        <td class="lab-title">
                            <?php if ($labExists): ?>
                            <a href="<?php echo $labFolder; ?>/index.php"><?php echo htmlspecialchars($lab['title']); ?></a>
                            <?php else: ?>
                            <span style="color: var(--text-muted)"><?php echo htmlspecialchars($lab['title']); ?></span>
                            <?php endif; ?>
                        </td>
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
