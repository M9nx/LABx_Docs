<?php
/**
 * LABx_Docs - Global Sidebar Component
 * Reusable sidebar with DB connection status, navigation, and theme toggle
 * 
 * Usage: 
 *   $basePath = '';     // For root pages
 *   $basePath = '../';  // For category pages
 *   $activePage = 'home' | 'ac' | 'api' | 'auth' | 'id' | 'setup' | 'progress';
 *   include __DIR__ . '/src/sidebar.php';
 */

// Determine base path if not set
if (!isset($basePath)) {
    $basePath = '';
}

// Determine active page if not set
if (!isset($activePage)) {
    $activePage = 'home';
}

// Database connection check
require_once __DIR__ . '/../db-config.php';
$creds = getDbCredentials();
$dbConnected = false;
$dbError = '';

if ($creds['configured']) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $testConn = @new mysqli($creds['host'], $creds['user'], $creds['pass']);
    if (!$testConn->connect_error) {
        $dbConnected = true;
        $testConn->close();
    } else {
        $dbError = $testConn->connect_error;
    }
}

// Fetch category progress counts
$categoryStats = [
    'ac' => ['solved' => 0, 'total' => 30],
    'api' => ['solved' => 0, 'total' => 0],
    'auth' => ['solved' => 0, 'total' => 0],
    'id' => ['solved' => 0, 'total' => 10]
];

if ($dbConnected) {
    // AC Progress
    $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass'], 'ac_progress');
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT COUNT(*) as count FROM solved_labs");
        if ($result) {
            $categoryStats['ac']['solved'] = (int)$result->fetch_assoc()['count'];
        }
        $conn->close();
    }
    
    // ID Progress
    $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass'], 'id_progress');
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT COUNT(*) as count FROM solved_labs");
        if ($result) {
            $categoryStats['id']['solved'] = (int)$result->fetch_assoc()['count'];
        }
        $conn->close();
    }
}
?>
<!-- Sidebar Component -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo $basePath; ?>index.php" class="logo">
            <span class="logo-icon">L</span>
            LABx<span>_Docs</span>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Overview</div>
            <a href="<?php echo $basePath; ?>index.php" class="nav-item <?php echo $activePage === 'home' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                Home
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Categories</div>
            <a href="<?php echo $basePath; ?>AC/index.php" class="nav-item <?php echo $activePage === 'ac' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                Access Control
                <span class="nav-badge"><?php echo $categoryStats['ac']['solved']; ?>/<?php echo $categoryStats['ac']['total']; ?></span>
            </a>
            <a href="<?php echo $basePath; ?>API/index.php" class="nav-item <?php echo $activePage === 'api' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></span>
                API Security
                <span class="nav-badge coming">Soon</span>
            </a>
            <a href="<?php echo $basePath; ?>Authentication/index.php" class="nav-item <?php echo $activePage === 'auth' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg></span>
                Authentication
                <span class="nav-badge coming">Soon</span>
            </a>
            <a href="<?php echo $basePath; ?>Insecure-Deserialization/index.php" class="nav-item <?php echo $activePage === 'id' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
                Insecure Deserialization
                <span class="nav-badge"><?php echo $categoryStats['id']['solved']; ?>/<?php echo $categoryStats['id']['total']; ?></span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Quick Actions</div>
            <a href="<?php echo $basePath; ?>src/setup.php" class="nav-item <?php echo $activePage === 'setup' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg></span>
                Setup Databases
            </a>
            <a href="<?php echo $basePath; ?>src/progress.php" class="nav-item <?php echo $activePage === 'progress' ? 'active' : ''; ?>">
                <span class="nav-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
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
        <a href="<?php echo $basePath; ?>src/setup.php" class="sidebar-db-status" title="<?php echo $dbConnected ? 'Database Connected' : ($dbError ?: 'Click to configure database'); ?>">
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
