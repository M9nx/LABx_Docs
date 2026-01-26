<?php
session_start();
$currentPage = $_GET['page'] ?? 'overview';
$pages = [
    'overview' => 'Overview',
    'vulnerability' => 'The Vulnerability',
    'exploitation' => 'Exploitation',
    'prevention' => 'Prevention',
    'examples' => 'Code Examples',
    'references' => 'References'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Multi-Step Access Control</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            padding: 1rem 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
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
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .layout {
            display: flex;
            padding-top: 70px;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(255, 68, 68, 0.2);
            padding: 2rem 1rem;
            position: fixed;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #ff6666;
            margin-bottom: 1rem;
            padding: 0 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar-nav {
            list-style: none;
        }
        .sidebar-nav li {
            margin-bottom: 0.3rem;
        }
        .sidebar-nav a {
            display: block;
            padding: 0.8rem 1rem;
            color: #ccc;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 68, 68, 0.1);
            color: #ff6666;
        }
        .sidebar-nav a.active {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            border-left: 3px solid #ff4444;
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem 3rem;
            max-width: calc(100% - 280px);
        }
        .doc-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .doc-header h1 {
            font-size: 2.5rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .doc-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .content-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-section h2 {
            color: #ff6666;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        .content-section h3 {
            color: #ff8888;
            margin: 1.5rem 0 0.8rem;
            font-size: 1.2rem;
        }
        .content-section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content-section ul, .content-section ol {
            margin: 1rem 0 1rem 2rem;
            color: #ccc;
        }
        .content-section li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
        }
        .code-block code {
            color: #e0e0e0;
        }
        .code-block .comment { color: #6a9955; }
        .code-block .keyword { color: #569cd6; }
        .code-block .string { color: #ce9178; }
        .code-block .function { color: #dcdcaa; }
        .code-block .variable { color: #9cdcfe; }
        .warning-box {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.4);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1rem 0;
        }
        .warning-box h4 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .info-box {
            background: rgba(100, 150, 255, 0.1);
            border: 1px solid rgba(100, 150, 255, 0.4);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1rem 0;
        }
        .info-box h4 {
            color: #88aaff;
            margin-bottom: 0.5rem;
        }
        .danger-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1rem 0;
        }
        .danger-box h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }
        .nav-btn {
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .nav-btn.disabled {
            opacity: 0.3;
            pointer-events: none;
        }
        .flow-diagram {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        .flow-step {
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            text-align: center;
        }
        .flow-step.protected {
            border-color: #00cc00;
        }
        .flow-step.vulnerable {
            border-color: #ff4444;
            background: rgba(255, 68, 68, 0.2);
        }
        .flow-step .step-title {
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.3rem;
        }
        .flow-step .step-desc {
            font-size: 0.85rem;
            color: #888;
        }
        .flow-step .step-status {
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }
        .flow-step.protected .step-status {
            color: #66ff66;
        }
        .flow-step.vulnerable .step-status {
            color: #ff6666;
        }
        .flow-arrow {
            color: #666;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔄 MultiStep Admin</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <?php foreach ($pages as $key => $title): ?>
                    <li>
                        <a href="?page=<?php echo $key; ?>" class="<?php echo $currentPage === $key ? 'active' : ''; ?>">
                            <?php 
                            $icons = [
                                'overview' => '📋',
                                'vulnerability' => '🔓',
                                'exploitation' => '⚡',
                                'prevention' => '🛡️',
                                'examples' => '💻',
                                'references' => '📖'
                            ];
                            echo $icons[$key] . ' ' . $title; 
                            ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <h3 style="margin-top: 2rem;">🔗 Quick Links</h3>
            <ul class="sidebar-nav">
                <li><a href="lab-description.php">Lab Objective</a></li>
                <li><a href="login.php">Start Lab</a></li>
                <li><a href="setup_db.php">Reset Database</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <?php include "docs-{$currentPage}.php"; ?>

            <div class="nav-buttons">
                <?php
                $pageKeys = array_keys($pages);
                $currentIndex = array_search($currentPage, $pageKeys);
                $prevPage = $currentIndex > 0 ? $pageKeys[$currentIndex - 1] : null;
                $nextPage = $currentIndex < count($pageKeys) - 1 ? $pageKeys[$currentIndex + 1] : null;
                ?>
                <a href="<?php echo $prevPage ? "?page={$prevPage}" : '#'; ?>" 
                   class="nav-btn <?php echo !$prevPage ? 'disabled' : ''; ?>">
                    ← Previous: <?php echo $prevPage ? $pages[$prevPage] : 'None'; ?>
                </a>
                <a href="<?php echo $nextPage ? "?page={$nextPage}" : '#'; ?>" 
                   class="nav-btn <?php echo !$nextPage ? 'disabled' : ''; ?>">
                    Next: <?php echo $nextPage ? $pages[$nextPage] : 'None'; ?> →
                </a>
            </div>
        </main>
    </div>
</body>
</html>
