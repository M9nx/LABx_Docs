<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$currentPage = $_GET['page'] ?? 'overview';

$pages = [
    'overview' => 'Overview',
    'vulnerability' => 'Vulnerability Analysis',
    'exploitation' => 'Exploitation',
    'prevention' => 'Prevention',
    'testing' => 'Testing Techniques',
    'references' => 'References'
];

if (!array_key_exists($currentPage, $pages)) {
    $currentPage = 'overview';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pages[$currentPage]; ?> - Lab 15 Documentation</title>
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
            border-bottom: 1px solid rgba(255, 204, 0, 0.3);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1600px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #ffcc00;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            font-size: 0.9rem;
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
        }
        .nav-links a:hover { color: #ffcc00; }
        .main-layout {
            display: flex;
            margin-top: 60px;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 204, 0, 0.2);
            min-height: calc(100vh - 60px);
            padding: 2rem 0;
            position: fixed;
            overflow-y: auto;
        }
        .sidebar-title {
            color: #ffcc00;
            font-size: 1rem;
            font-weight: 600;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar-nav {
            list-style: none;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1.5rem;
            color: #aaa;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover {
            background: rgba(255, 204, 0, 0.1);
            color: #ffcc00;
            border-left-color: rgba(255, 204, 0, 0.5);
        }
        .sidebar-nav a.active {
            background: rgba(255, 204, 0, 0.15);
            color: #ffcc00;
            border-left-color: #ffcc00;
        }
        .content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem 3rem;
            max-width: calc(100% - 280px);
        }
        .doc-container {
            max-width: 900px;
        }
        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            color: #666;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: #888;
            text-decoration: none;
        }
        .breadcrumb a:hover { color: #ffcc00; }
        .doc-title {
            font-size: 2.5rem;
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .doc-subtitle {
            color: #888;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .section {
            margin-bottom: 2.5rem;
        }
        .section h2 {
            color: #ffcc00;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 204, 0, 0.2);
        }
        .section h3 {
            color: #ffcc00;
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul, .section ol {
            margin: 1rem 0 1rem 1.5rem;
            color: #ccc;
        }
        .section li {
            margin-bottom: 0.5rem;
            line-height: 1.7;
        }
        code {
            background: rgba(255, 204, 0, 0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #ffcc00;
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
            color: #88ff88;
        }
        .code-block .comment { color: #666; }
        .code-block .keyword { color: #ff79c6; }
        .code-block .string { color: #f1fa8c; }
        .note-box {
            background: rgba(255, 204, 0, 0.1);
            border-left: 4px solid #ffcc00;
            border-radius: 0 10px 10px 0;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .note-box h4 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .note-box p { margin: 0; }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff4444;
            border-radius: 0 10px 10px 0;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .success-box {
            background: rgba(0, 255, 0, 0.1);
            border-left: 4px solid #00ff00;
            border-radius: 0 10px 10px 0;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .success-box h4 {
            color: #00ff00;
            margin-bottom: 0.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 204, 0, 0.2);
        }
        th {
            background: rgba(255, 204, 0, 0.1);
            color: #ffcc00;
        }
        .page-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 204, 0, 0.2);
        }
        .page-nav a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 10px;
            color: #e0e0e0;
            text-decoration: none;
            transition: all 0.3s;
        }
        .page-nav a:hover {
            background: rgba(255, 204, 0, 0.1);
            border-color: #ffcc00;
            color: #ffcc00;
        }
        .page-nav .next { margin-left: auto; }
        .impact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .impact-item {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
        }
        .impact-item h4 {
            color: #ff6666;
            margin-bottom: 0.3rem;
        }
        .impact-item p {
            font-size: 0.9rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">MTN</span>
                MobAd Platform
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Description</a>
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="main-layout">
        <aside class="sidebar">
            <div class="sidebar-title">📚 Documentation</div>
            <ul class="sidebar-nav">
                <?php foreach ($pages as $key => $title): ?>
                <li>
                    <a href="docs.php?page=<?php echo $key; ?>" class="<?php echo $currentPage === $key ? 'active' : ''; ?>">
                        <?php
                        $icons = [
                            'overview' => '📖',
                            'vulnerability' => '🔓',
                            'exploitation' => '🎯',
                            'prevention' => '🛡️',
                            'testing' => '🧪',
                            'references' => '📚'
                        ];
                        echo $icons[$key] . ' ' . $title;
                        ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="content">
            <div class="doc-container">
                <div class="breadcrumb">
                    <a href="index.php">Lab 15</a>
                    <span>/</span>
                    <a href="docs.php">Documentation</a>
                    <span>/</span>
                    <span><?php echo $pages[$currentPage]; ?></span>
                </div>

                <?php include "docs-{$currentPage}.php"; ?>

                <div class="page-nav">
                    <?php
                    $pageKeys = array_keys($pages);
                    $currentIndex = array_search($currentPage, $pageKeys);
                    $prevPage = $currentIndex > 0 ? $pageKeys[$currentIndex - 1] : null;
                    $nextPage = $currentIndex < count($pageKeys) - 1 ? $pageKeys[$currentIndex + 1] : null;
                    ?>
                    
                    <?php if ($prevPage): ?>
                    <a href="docs.php?page=<?php echo $prevPage; ?>" class="prev">
                        ← <?php echo $pages[$prevPage]; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($nextPage): ?>
                    <a href="docs.php?page=<?php echo $nextPage; ?>" class="next">
                        <?php echo $pages[$nextPage]; ?> →
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
