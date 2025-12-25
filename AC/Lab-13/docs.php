<?php
session_start();
$current_page = $_GET['page'] ?? 'overview';
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$pages = [
    'overview' => 'Overview',
    'vulnerability' => 'The Vulnerability',
    'exploitation' => 'Exploitation Guide',
    'prevention' => 'Prevention',
    'testing' => 'Testing Techniques',
    'references' => 'References'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Referer Lab</title>
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
            position: sticky;
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
        }
        .docs-layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 70px);
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 68, 68, 0.2);
            padding: 2rem 1rem;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #ff6666;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        .sidebar-nav {
            list-style: none;
        }
        .sidebar-nav li {
            margin-bottom: 0.25rem;
        }
        .sidebar-nav a {
            display: block;
            padding: 0.8rem 1rem;
            color: #aaa;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 68, 68, 0.1);
            color: #ff4444;
        }
        .sidebar-nav a.active {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
            border-left: 3px solid #ff4444;
        }
        .main-content {
            flex: 1;
            padding: 3rem;
            max-width: 900px;
        }
        .doc-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .doc-header h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .doc-header p {
            color: #888;
        }
        .content-section {
            margin-bottom: 3rem;
        }
        .content-section h2 {
            color: #ff6666;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .content-section h3 {
            color: #e0e0e0;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }
        .content-section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content-section ul, .content-section ol {
            color: #ccc;
            margin: 1rem 0 1rem 1.5rem;
            line-height: 1.8;
        }
        .content-section li {
            margin-bottom: 0.5rem;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            margin: 1rem 0;
            position: relative;
        }
        .code-block code {
            color: #88ff88;
        }
        .code-label {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(255, 68, 68, 0.3);
            color: #ff6666;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .info-box {
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .info-box h4 {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-box.warning {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.3);
        }
        .info-box.warning h4 { color: #ffcc00; }
        .info-box.danger {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .info-box.danger h4 { color: #ff6666; }
        .info-box.success {
            background: rgba(0, 200, 0, 0.1);
            border: 1px solid rgba(0, 200, 0, 0.3);
        }
        .info-box.success h4 { color: #66ff66; }
        .info-box.info {
            background: rgba(100, 150, 255, 0.1);
            border: 1px solid rgba(100, 150, 255, 0.3);
        }
        .info-box.info h4 { color: #88aaff; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }
        .nav-btn {
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            color: #e0e0e0;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(255, 68, 68, 0.1);
            border-color: #ff4444;
        }
        .nav-btn.disabled {
            opacity: 0.3;
            pointer-events: none;
        }
        .diagram {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 2rem;
            margin: 1.5rem 0;
            text-align: center;
        }
        .diagram-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 1rem 0;
        }
        .diagram-box {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid #ff4444;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: #ff6666;
        }
        .diagram-arrow {
            color: #666;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📋 Referer Lab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php">My Account</a>
                    <?php if ($isAdmin): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="docs-layout">
        <aside class="sidebar">
            <h3>Documentation</h3>
            <ul class="sidebar-nav">
                <?php foreach ($pages as $key => $title): ?>
                    <li>
                        <a href="?page=<?php echo $key; ?>" class="<?php echo $current_page === $key ? 'active' : ''; ?>">
                            <?php echo $title; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="main-content">
            <?php include "docs-{$current_page}.php"; ?>
            
            <div class="nav-buttons">
                <?php
                $keys = array_keys($pages);
                $currentIndex = array_search($current_page, $keys);
                $prevPage = $currentIndex > 0 ? $keys[$currentIndex - 1] : null;
                $nextPage = $currentIndex < count($keys) - 1 ? $keys[$currentIndex + 1] : null;
                ?>
                <a href="<?php echo $prevPage ? "?page={$prevPage}" : '#'; ?>" 
                   class="nav-btn <?php echo !$prevPage ? 'disabled' : ''; ?>">
                    ← <?php echo $prevPage ? $pages[$prevPage] : 'Previous'; ?>
                </a>
                <a href="<?php echo $nextPage ? "?page={$nextPage}" : '#'; ?>" 
                   class="nav-btn <?php echo !$nextPage ? 'disabled' : ''; ?>">
                    <?php echo $nextPage ? $pages[$nextPage] : 'Next'; ?> →
                </a>
            </div>
        </main>
    </div>
</body>
</html>
