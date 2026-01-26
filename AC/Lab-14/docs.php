<?php
session_start();
$isLoggedIn = isset($_SESSION['manager_id']);

// Documentation pages
$pages = [
    'overview' => ['title' => 'IDOR Overview', 'file' => 'docs-overview.php'],
    'vulnerability' => ['title' => 'Vulnerability Analysis', 'file' => 'docs-vulnerability.php'],
    'exploitation' => ['title' => 'Exploitation Guide', 'file' => 'docs-exploitation.php'],
    'prevention' => ['title' => 'Prevention', 'file' => 'docs-prevention.php'],
    'testing' => ['title' => 'Testing Techniques', 'file' => 'docs-testing.php'],
    'references' => ['title' => 'References', 'file' => 'docs-references.php']
];

$currentPage = $_GET['page'] ?? 'overview';
if (!isset($pages[$currentPage])) {
    $currentPage = 'overview';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pages[$currentPage]['title']; ?> - IDOR Lab Documentation</title>
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
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 0.95rem;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 6px;
        }
        .layout {
            display: flex;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
        }
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.5);
            border-right: 1px solid rgba(255, 68, 68, 0.2);
            padding: 2rem 0;
            position: fixed;
            top: 60px;
            left: 0;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        .sidebar-title {
            color: #ff4444;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1.5rem;
            color: #aaa;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 68, 68, 0.1);
            color: #e0e0e0;
        }
        .sidebar-nav a.active {
            background: rgba(255, 68, 68, 0.15);
            color: #ff4444;
            border-left-color: #ff4444;
        }
        .sidebar-nav .icon {
            width: 20px;
            text-align: center;
        }
        .content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem 3rem;
        }
        .content-inner {
            max-width: 900px;
        }
        .breadcrumb {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: #888;
            text-decoration: none;
        }
        .breadcrumb a:hover { color: #ff4444; }
        .page-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }
        .page-nav a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .page-nav a:hover {
            background: rgba(255, 68, 68, 0.1);
            border-color: #ff4444;
            color: #ff4444;
        }
        .page-nav a.disabled {
            opacity: 0.3;
            pointer-events: none;
        }
        .quick-links {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .quick-links h4 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }
        .quick-links a {
            padding: 0.5rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            color: #ccc;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .quick-links a:hover {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📢 Lab 14 - IDOR Banner Deletion</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Description</a>
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Start Lab</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-title">Documentation</div>
            <nav class="sidebar-nav">
                <a href="?page=overview" class="<?php echo $currentPage === 'overview' ? 'active' : ''; ?>">
                    <span class="icon">📖</span> IDOR Overview
                </a>
                <a href="?page=vulnerability" class="<?php echo $currentPage === 'vulnerability' ? 'active' : ''; ?>">
                    <span class="icon">🔓</span> Vulnerability Analysis
                </a>
                <a href="?page=exploitation" class="<?php echo $currentPage === 'exploitation' ? 'active' : ''; ?>">
                    <span class="icon">⚡</span> Exploitation Guide
                </a>
                <a href="?page=prevention" class="<?php echo $currentPage === 'prevention' ? 'active' : ''; ?>">
                    <span class="icon">🛡️</span> Prevention
                </a>
                <a href="?page=testing" class="<?php echo $currentPage === 'testing' ? 'active' : ''; ?>">
                    <span class="icon">🧪</span> Testing Techniques
                </a>
                <a href="?page=references" class="<?php echo $currentPage === 'references' ? 'active' : ''; ?>">
                    <span class="icon">📚</span> References
                </a>
            </nav>

            <div class="quick-links" style="margin: 2rem 1rem;">
                <h4>Quick Actions</h4>
                <div class="quick-links-grid">
                    <a href="login.php">🚀 Start Lab</a>
                    <a href="lab-description.php">📋 Objectives</a>
                    <a href="setup_db.php">🔄 Reset Lab</a>
                </div>
            </div>
        </aside>

        <main class="content">
            <div class="content-inner">
                <div class="breadcrumb">
                    <a href="index.php">Lab 14</a> / <a href="docs.php">Documentation</a> / <?php echo $pages[$currentPage]['title']; ?>
                </div>

                <?php include $pages[$currentPage]['file']; ?>

                <nav class="page-nav">
                    <?php
                    $pageKeys = array_keys($pages);
                    $currentIndex = array_search($currentPage, $pageKeys);
                    $prevPage = $currentIndex > 0 ? $pageKeys[$currentIndex - 1] : null;
                    $nextPage = $currentIndex < count($pageKeys) - 1 ? $pageKeys[$currentIndex + 1] : null;
                    ?>
                    <a href="<?php echo $prevPage ? "?page=$prevPage" : '#'; ?>" 
                       class="<?php echo !$prevPage ? 'disabled' : ''; ?>">
                        ← <?php echo $prevPage ? $pages[$prevPage]['title'] : 'Previous'; ?>
                    </a>
                    <a href="<?php echo $nextPage ? "?page=$nextPage" : '#'; ?>"
                       class="<?php echo !$nextPage ? 'disabled' : ''; ?>">
                        <?php echo $nextPage ? $pages[$nextPage]['title'] : 'Next'; ?> →
                    </a>
                </nav>
            </div>
        </main>
    </div>
</body>
</html>
