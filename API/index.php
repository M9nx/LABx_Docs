<?php
/**
 * LABx_Docs - API Security Category
 * Coming Soon page with same design as main
 */

// Use centralized database configuration
require_once __DIR__ . '/../db-config.php';

// Sidebar configuration
$basePath = '../';
$activePage = 'api';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Security Labs - LABx_Docs</title>
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
            --accent: #06b6d4;
            --accent-muted: #0891b2;
            --accent-bg: rgba(6, 182, 212, 0.1);
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
            --accent: #0891b2;
            --accent-muted: #0e7490;
            --accent-bg: rgba(8, 145, 178, 0.1);
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
        
        .hero h1 { font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; }
        .hero p { color: var(--text-muted); font-size: 1rem; margin-bottom: 1.5rem; }
        .hero-stats { display: flex; gap: 2rem; flex-wrap: wrap; }
        
        .hero-stat {
            text-align: left;
            padding: 1rem 1.5rem;
            background: var(--bg-tertiary);
            border-radius: 10px;
            min-width: 100px;
        }
        
        .hero-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
        .hero-stat-value.accent { color: var(--accent); }
        .hero-stat-label { color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Coming Soon */
        .coming-soon {
            background: var(--bg-card);
            border: 2px dashed var(--border-color);
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
        }
        
        .coming-soon-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--accent-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .coming-soon-icon svg { width: 40px; height: 40px; stroke: var(--accent); stroke-width: 2; fill: none; }
        .coming-soon h2 { font-size: 1.75rem; color: var(--text-primary); margin-bottom: 0.75rem; }
        .coming-soon p { color: var(--text-muted); font-size: 1rem; max-width: 500px; margin: 0 auto 2rem; }
        
        /* Planned Topics */
        .planned-topics { margin-top: 3rem; }
        .planned-topics h3 { font-size: 1.1rem; color: var(--text-primary); margin-bottom: 1rem; text-align: center; }
        
        .topics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .topic-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .topic-card:hover { border-color: var(--accent); background: var(--accent-bg); }
        .topic-card h4 { font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.35rem; }
        .topic-card p { font-size: 0.8rem; color: var(--text-muted); }
        
        /* Footer */
        .footer { padding: 2rem 0; border-top: 1px solid var(--border-color); margin-top: 2rem; text-align: center; }
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
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
    </button>
    
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <?php include __DIR__ . '/../src/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <nav class="breadcrumb">
                <a href="../index.php">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Home
                </a>
                <span>/</span>
                <span class="current">API Security</span>
            </nav>
            
            <div class="hero">
                <h1>API Security Labs</h1>
                <p>Learn to identify and exploit API vulnerabilities including broken authentication, excessive data exposure, and rate limiting issues</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value accent">0</div>
                        <div class="hero-stat-label">Labs</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">Coming</div>
                        <div class="hero-stat-label">Soon</div>
                    </div>
                </div>
            </div>
            
            <div class="coming-soon">
                <div class="coming-soon-icon">
                    <svg viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                </div>
                <h2>Coming Soon</h2>
                <p>We're working hard to bring you comprehensive API security labs. Stay tuned for updates!</p>
            </div>
            
            <div class="planned-topics">
                <h3>Planned Topics</h3>
                <div class="topics-grid">
                    <div class="topic-card">
                        <h4>BOLA/IDOR</h4>
                        <p>Broken Object Level Authorization</p>
                    </div>
                    <div class="topic-card">
                        <h4>Broken Authentication</h4>
                        <p>API authentication flaws</p>
                    </div>
                    <div class="topic-card">
                        <h4>Data Exposure</h4>
                        <p>Excessive data in responses</p>
                    </div>
                    <div class="topic-card">
                        <h4>Rate Limiting</h4>
                        <p>Missing rate limit controls</p>
                    </div>
                    <div class="topic-card">
                        <h4>Mass Assignment</h4>
                        <p>Unsafe property binding</p>
                    </div>
                    <div class="topic-card">
                        <h4>Injection</h4>
                        <p>SQL, NoSQL, Command injection</p>
                    </div>
                </div>
            </div>
            
            <footer class="footer">
                <p>LABx_Docs — <a href="../index.php">Back to Home</a></p>
            </footer>
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
        
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }
    </script>
</body>
</html>
