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
    <?php include __DIR__ . '/../src/sidebar.php'; ?>

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
