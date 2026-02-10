<?php
/**
 * LABx_Docs - Authentication Category
 * Coming Soon page with same design as main
 */
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Labs - LABx_Docs</title>
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
            --accent: #8b5cf6;
            --accent-muted: #7c3aed;
            --accent-bg: rgba(139, 92, 246, 0.1);
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
            --accent: #7c3aed;
            --accent-muted: #6d28d9;
            --accent-bg: rgba(124, 58, 237, 0.1);
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
                <a href="../AC/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    Access Control
                    <span class="nav-badge">30</span>
                </a>
                <a href="../API/index.php" class="nav-item">
                    <span class="nav-item-icon"><svg viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></span>
                    API Security
                    <span class="nav-badge coming">Soon</span>
                </a>
                <a href="index.php" class="nav-item active">
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
    
    <main class="main-content">
        <div class="container">
            <nav class="breadcrumb">
                <a href="../index.php">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Home
                </a>
                <span>/</span>
                <span class="current">Authentication</span>
            </nav>
            
            <div class="hero">
                <h1>Authentication Labs</h1>
                <p>Explore authentication flaws including brute force attacks, password reset poisoning, multi-factor bypass, and session vulnerabilities</p>
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
                    <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg>
                </div>
                <h2>Coming Soon</h2>
                <p>We're working hard to bring you comprehensive authentication security labs. Stay tuned for updates!</p>
            </div>
            
            <div class="planned-topics">
                <h3>Planned Topics</h3>
                <div class="topics-grid">
                    <div class="topic-card">
                        <h4>Brute Force</h4>
                        <p>Password guessing attacks</p>
                    </div>
                    <div class="topic-card">
                        <h4>Password Reset</h4>
                        <p>Reset flow vulnerabilities</p>
                    </div>
                    <div class="topic-card">
                        <h4>2FA Bypass</h4>
                        <p>Multi-factor authentication flaws</p>
                    </div>
                    <div class="topic-card">
                        <h4>JWT Attacks</h4>
                        <p>Token manipulation techniques</p>
                    </div>
                    <div class="topic-card">
                        <h4>Session Hijacking</h4>
                        <p>Session management flaws</p>
                    </div>
                    <div class="topic-card">
                        <h4>OAuth Vulnerabilities</h4>
                        <p>OAuth implementation flaws</p>
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
