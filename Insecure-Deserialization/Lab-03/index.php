<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Main Entry Page
 */
require_once 'config.php';
require_once '../progress.php';

// Check if morale.txt exists (lab completion condition)
// Lab is solved when the file does NOT exist (has been deleted)
$moraleExists = file_exists(__DIR__ . '/home/carlos/morale.txt');
$labSolved = !$moraleExists;

// Sync progress tracking with actual file state
if ($labSolved && !isLabSolved(3)) {
    markLabSolved(3);
} elseif (!$labSolved && isLabSolved(3)) {
    resetLabProgress(3);
}

// Check if user is logged in via session cookie
$session = getSessionFromCookie();
$isLoggedIn = ($session !== null && validateSession($session));
$currentUser = $isLoggedIn ? $session->username : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AvatarVault - Profile Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%); 
            min-height: 100vh; 
            color: #e0e0e0; 
        }
        .header { 
            background: rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(249,115,22,0.3); 
            padding: 1rem 2rem; 
        }
        .header-content { 
            max-width: 1200px; 
            margin: 0 auto; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .logo { 
            font-size: 1.8rem; 
            font-weight: bold; 
            color: #f97316; 
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
        .nav-links a:hover { color: #f97316; }
        .btn-back { 
            display: inline-flex; 
            align-items: center; 
            gap: 0.5rem; 
            padding: 0.6rem 1.2rem; 
            background: rgba(255,255,255,0.1); 
            border: 1px solid rgba(249,115,22,0.3); 
            color: #e0e0e0; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 500; 
            transition: all 0.3s; 
        }
        .btn-back:hover { 
            background: rgba(249,115,22,0.2); 
            border-color: #f97316; 
            color: #f97316; 
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            padding: 3rem 2rem; 
        }
        .lab-card { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(249,115,22,0.2); 
            border-radius: 15px; 
            padding: 2.5rem; 
            margin-bottom: 2rem; 
            backdrop-filter: blur(10px); 
        }
        .lab-badge { 
            display: inline-block; 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white; 
            padding: 0.4rem 1rem; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            margin-bottom: 1rem; 
        }
        .lab-title { 
            font-size: 2rem; 
            color: #f97316; 
            margin-bottom: 1rem; 
        }
        .lab-description { 
            color: #ccc; 
            line-height: 1.8; 
            margin-bottom: 1.5rem; 
        }
        .lab-description code { 
            background: rgba(249,115,22,0.2); 
            padding: 0.2rem 0.5rem; 
            border-radius: 4px; 
            color: #fb923c; 
        }
        .credentials-box { 
            background: rgba(34, 197, 94, 0.1); 
            border: 1px solid rgba(34, 197, 94, 0.3); 
            padding: 1.5rem; 
            border-radius: 10px; 
            margin-bottom: 1.5rem; 
        }
        .credentials-box h3 { 
            color: #22c55e; 
            margin-bottom: 0.75rem; 
            font-size: 1rem; 
        }
        .credentials-box code { 
            background: rgba(34, 197, 94, 0.2); 
            color: #86efac; 
            padding: 0.2rem 0.5rem; 
            border-radius: 4px; 
        }
        .hint-box { 
            background: rgba(0, 255, 255, 0.05); 
            border: 1px solid rgba(0, 255, 255, 0.2); 
            padding: 1.5rem; 
            border-radius: 10px; 
            margin-bottom: 1.5rem; 
        }
        .hint-box h3 { 
            color: #00ffff; 
            margin-bottom: 0.75rem; 
            font-size: 1rem; 
        }
        .hint-box p { 
            color: #a0e0e0; 
            font-size: 0.95rem; 
            line-height: 1.6; 
        }
        .status-box { 
            padding: 1.5rem; 
            border-radius: 10px; 
            margin-bottom: 1.5rem; 
            text-align: center; 
        }
        .status-box.solved { 
            background: rgba(34, 197, 94, 0.2); 
            border: 1px solid rgba(34, 197, 94, 0.4); 
        }
        .status-box.unsolved { 
            background: rgba(239, 68, 68, 0.1); 
            border: 1px solid rgba(239, 68, 68, 0.3); 
        }
        .status-box h3 { 
            font-size: 1.2rem; 
            margin-bottom: 0.5rem; 
        }
        .status-box.solved h3 { color: #22c55e; }
        .status-box.unsolved h3 { color: #ef4444; }
        .features-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 1rem; 
            margin-top: 1.5rem; 
        }
        .feature-item { 
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(249,115,22,0.15); 
            padding: 1.25rem; 
            border-radius: 10px; 
            text-align: center; 
        }
        .feature-item span { 
            display: block; 
            font-size: 2rem; 
            margin-bottom: 0.5rem; 
        }
        .feature-item h4 { color: #fb923c; font-size: 0.95rem; }
        .btn-primary { 
            display: inline-block; 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white; 
            padding: 0.8rem 1.5rem; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: transform 0.3s, box-shadow 0.3s; 
            margin-right: 1rem; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 20px rgba(249,115,22,0.4); 
        }
        .btn-secondary { 
            display: inline-block; 
            background: rgba(255,255,255,0.1); 
            border: 1px solid rgba(249,115,22,0.3); 
            color: #e0e0e0; 
            padding: 0.8rem 1.5rem; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: all 0.3s; 
        }
        .btn-secondary:hover { 
            background: rgba(249,115,22,0.2); 
            color: #f97316; 
        }
        .target-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .target-box h3 {
            color: #ef4444;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }
        .target-box code {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">AvatarVault</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <?php if ($isLoggedIn): ?>
                    <a href="my-account.php">My Account</a>
                    <a href="logout.php">Logout (<?= htmlspecialchars($currentUser) ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Docs</a>
                <a href="../" class="btn-back">← Back to Labs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-card">
            <span class="lab-badge">PRACTITIONER</span>
            <h1 class="lab-title">Lab 03: Using Application Functionality</h1>
            <p class="lab-description">
                This lab uses a serialization-based session mechanism. A certain feature invokes a 
                dangerous method on data provided in a serialized object. To solve the lab, edit 
                the serialized object in the session cookie and use it to delete the 
                <code>morale.txt</code> file from Carlos's home directory.
            </p>
            
            <div class="credentials-box">
                <h3>Your Credentials</h3>
                <p>
                    <strong>Primary Account:</strong> <code>wiener</code> : <code>peter</code><br>
                    <strong>Backup Account:</strong> <code>gregg</code> : <code>rosebud</code>
                </p>
            </div>
            
            <div class="target-box">
                <h3>Target File</h3>
                <p>Delete <code>/home/carlos/morale.txt</code> using the account deletion functionality.</p>
                <p style="margin-top: 0.5rem; color: #aaa; font-size: 0.9rem;">
                    <strong>Note:</strong> Use the ABSOLUTE path shown in your decoded cookie. Replace <code>/home/wiener/avatar.jpg</code> with <code>/home/carlos/morale.txt</code>.
                </p>
                <p style="margin-top: 0.5rem; color: #aaa;">
                    Current status: <?php if ($moraleExists): ?>
                        <span style="color: #fca5a5;">File exists</span>
                    <?php else: ?>
                        <span style="color: #86efac;">File deleted!</span>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="hint-box">
                <h3>Hint</h3>
                <p>
                    The session cookie contains a serialized <code>User</code> object with an 
                    <code>avatar_link</code> attribute. When you delete your account, the server 
                    deletes whatever file is specified in <code>avatar_link</code>. What if you 
                    modified that path before deleting your account?
                </p>
            </div>

            <div class="status-box <?= $labSolved ? 'solved' : 'unsolved' ?>">
                <?php if ($labSolved): ?>
                    <h3>🎉 Lab Solved!</h3>
                    <p style="color: #86efac;">You successfully deleted Carlos's morale.txt file.</p>
                <?php else: ?>
                    <h3>Lab Not Solved</h3>
                    <p style="color: #fca5a5;">Delete morale.txt from Carlos's home directory.</p>
                <?php endif; ?>
            </div>
            
            <?php if ($isLoggedIn): ?>
                <a href="my-account.php" class="btn-primary">Go to My Account</a>
            <?php else: ?>
                <a href="login.php" class="btn-primary">Login to Start</a>
            <?php endif; ?>
            <a href="lab-description.php" class="btn-secondary">View Challenge Details</a>
            <a href="setup_db.php" class="btn-secondary">Reset Lab</a>
        </div>

        <div class="lab-card">
            <h2 style="color: #f97316; margin-bottom: 1rem;">Key Features</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <span>👤</span>
                    <h4>User Profiles</h4>
                </div>
                <div class="feature-item">
                    <span>🖼️</span>
                    <h4>Avatar Management</h4>
                </div>
                <div class="feature-item">
                    <span>🗑️</span>
                    <h4>Account Deletion</h4>
                </div>
                <div class="feature-item">
                    <span>🍪</span>
                    <h4>Serialized Cookies</h4>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
