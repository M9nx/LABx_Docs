<?php
session_start();
require_once '../progress.php';
$isSolved = isLabSolved(3);
require_once 'config.php';
// $pdo is already created in config.php
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'carlos'");
$stmt->execute();
$carlosExists = $stmt->fetchColumn() > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CookieAuth - Lab 3</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,68,68,0.3); padding: 1rem 2rem; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff4444; }
        .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,68,68,0.3); color: #e0e0e0; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s; }
        .btn-back:hover { background: rgba(255,68,68,0.2); border-color: #ff4444; color: #ff4444; }
        .container { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
        .lab-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,68,68,0.2); border-radius: 15px; padding: 2.5rem; margin-bottom: 2rem; backdrop-filter: blur(10px); }
        .lab-badge { display: inline-block; background: linear-gradient(135deg, #ff4444, #cc0000); color: white; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem; }
        .lab-title { font-size: 2rem; color: #ff4444; margin-bottom: 1rem; }
        .lab-description { color: #ccc; line-height: 1.8; margin-bottom: 1.5rem; }
        .lab-description code { background: rgba(255,68,68,0.2); padding: 0.2rem 0.5rem; border-radius: 4px; color: #ff6666; }
        .credentials-box { background: rgba(0,255,0,0.1); border: 1px solid rgba(0,255,0,0.3); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
        .credentials-box h3 { color: #00ff00; margin-bottom: 0.8rem; font-size: 1rem; }
        .credentials-box code { background: rgba(0,0,0,0.3); padding: 0.3rem 0.6rem; border-radius: 4px; color: #00ff00; font-family: 'Consolas', monospace; }
        .hint-box { background: rgba(0,255,255,0.1); border: 1px solid rgba(0,255,255,0.3); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
        .hint-box h3 { color: #00ffff; margin-bottom: 0.8rem; font-size: 1rem; }
        .hint-box p { color: #aadddd; font-size: 0.95rem; }
        .btn { display: inline-block; padding: 0.8rem 1.8rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; margin-right: 1rem; margin-top: 0.5rem; }
        .btn-primary { background: linear-gradient(135deg, #ff4444, #cc0000); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(255,68,68,0.4); }
        .btn-secondary { background: transparent; border: 2px solid #ff4444; color: #ff4444; }
        .btn-secondary:hover { background: #ff4444; color: white; }
        .status-box { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.3); border-radius: 10px; padding: 1rem 1.5rem; margin-top: 1.5rem; display: flex; align-items: center; gap: 1rem; }
        .status-box.solved { background: rgba(0,255,0,0.1); border-color: rgba(0,255,0,0.3); }
        .status-indicator { width: 12px; height: 12px; border-radius: 50%; background: #ff4444; }
        .status-box.solved .status-indicator { background: #00ff00; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .feature-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,68,68,0.15); border-radius: 12px; padding: 1.5rem; transition: all 0.3s ease; }
        .feature-card:hover { border-color: rgba(255,68,68,0.4); transform: translateY(-3px); }
        .feature-icon { font-size: 2rem; margin-bottom: 1rem; }
        .feature-title { color: #ff6666; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .feature-desc { color: #999; font-size: 0.9rem; }
    .solved-banner { background: rgba(0, 255, 0, 0.1); border: 1px solid rgba(0, 255, 0, 0.3); border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; text-align: center; } .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🍪 CookieAuth</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="admin.php">Admin</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="lab-card">
            <span class="lab-badge">ACCESS CONTROL LAB</span>
            <h1 class="lab-title">Lab 3: User Role Controlled by Request Parameter</h1>
            <p class="lab-description">This lab uses a cookie called <code>Admin</code> to determine if the user has administrative privileges. Manipulate this cookie to gain admin access and delete user <strong>carlos</strong>.</p>
            <div class="credentials-box">
                <h3>🔑 Credentials</h3>
                <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
            </div>
            <div class="hint-box">
                <h3>💡 Hint</h3>
                <p>After logging in, check the cookies in your browser's DevTools. Look for a cookie that controls admin access. What happens if you modify its value?</p>
            </div>
            <div class="status-box <?php echo !$carlosExists ? 'solved' : ''; ?>">
                <div class="status-indicator"></div>
                <span><?php echo !$carlosExists ? '🎉 <strong>Congratulations!</strong> Lab solved!' : 'Lab Status: <strong>Unsolved</strong> - Delete carlos to complete'; ?></span>
            </div>
            <div style="margin-top: 2rem;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn btn-primary">My Profile</a>
                    <a href="admin.php" class="btn btn-secondary">Try Admin Panel</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login to Start</a>
                <?php endif; ?>
                <a href="docs.php" class="btn btn-secondary">Documentation</a>
            </div>
        </div>
        <div class="features-grid">
            <div class="feature-card"><div class="feature-icon">🍪</div><h3 class="feature-title">Cookie Manipulation</h3><p class="feature-desc">Learn how cookies can be tampered to bypass access controls.</p></div>
            <div class="feature-card"><div class="feature-icon">🔐</div><h3 class="feature-title">Client-Side Auth</h3><p class="feature-desc">Understand dangers of trusting client-controlled data.</p></div>
            <div class="feature-card"><div class="feature-icon">🛠️</div><h3 class="feature-title">DevTools Skills</h3><p class="feature-desc">Practice using browser DevTools to inspect and modify cookies.</p></div>
            <div class="feature-card"><div class="feature-icon">⚠️</div><h3 class="feature-title">Vulnerable Design</h3><p class="feature-desc">Intentional security flaws for educational purposes.</p></div>
        </div>
    </div>
</body>
</html>


