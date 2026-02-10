<?php
require_once '../progress.php';
$isSolved = isLabSolved(1);
require_once 'config.php';

// Check if carlos exists (lab completion condition)
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'carlos'");
$stmt->execute();
$carlosExists = $stmt->fetchColumn() > 0;

// Check if user is logged in via session cookie
$session = getSessionFromCookie();
$isLoggedIn = ($session !== null);
$currentUser = $isLoggedIn ? $session->username : null;
$hasAdminPrivs = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SerialLab - Secure Session Management</title>
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
            background: rgba(34,197,94,0.1); 
            border: 1px solid rgba(34,197,94,0.3); 
            border-radius: 10px; 
            padding: 1.5rem; 
            margin: 1.5rem 0; 
        }
        .credentials-box h3 { 
            color: #22c55e; 
            margin-bottom: 0.8rem; 
            font-size: 1rem; 
        }
        .credentials-box code { 
            background: rgba(0,0,0,0.3); 
            padding: 0.3rem 0.6rem; 
            border-radius: 4px; 
            color: #22c55e; 
            font-family: 'Consolas', monospace; 
        }
        .hint-box { 
            background: rgba(0,255,255,0.1); 
            border: 1px solid rgba(0,255,255,0.3); 
            border-radius: 10px; 
            padding: 1.5rem; 
            margin: 1.5rem 0; 
        }
        .hint-box h3 { 
            color: #00ffff; 
            margin-bottom: 0.8rem; 
            font-size: 1rem; 
        }
        .hint-box p { 
            color: #aadddd; 
            font-size: 0.95rem; 
        }
        .btn { 
            display: inline-block; 
            padding: 0.8rem 1.8rem; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            margin-right: 1rem; 
            margin-top: 0.5rem; 
        }
        .btn-primary { 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 20px rgba(249,115,22,0.4); 
        }
        .btn-secondary { 
            background: transparent; 
            border: 2px solid #f97316; 
            color: #f97316; 
        }
        .btn-secondary:hover { 
            background: #f97316; 
            color: white; 
        }
        .status-box { 
            background: rgba(249,115,22,0.1); 
            border: 1px solid rgba(249,115,22,0.3); 
            border-radius: 10px; 
            padding: 1rem 1.5rem; 
            margin-top: 1.5rem; 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
        }
        .status-box.solved { 
            background: rgba(34,197,94,0.1); 
            border-color: rgba(34,197,94,0.3); 
        }
        .status-indicator { 
            width: 12px; 
            height: 12px; 
            border-radius: 50%; 
            background: #f97316; 
        }
        .status-box.solved .status-indicator { 
            background: #22c55e; 
        }
        .features-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 1.5rem; 
            margin-top: 2rem; 
        }
        .feature-card { 
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(249,115,22,0.15); 
            border-radius: 12px; 
            padding: 1.5rem; 
            transition: all 0.3s ease; 
        }
        .feature-card:hover { 
            border-color: rgba(249,115,22,0.4); 
            transform: translateY(-3px); 
        }
        .feature-icon { 
            font-size: 2rem; 
            margin-bottom: 1rem; 
        }
        .feature-title { 
            color: #fb923c; 
            font-size: 1.1rem; 
            margin-bottom: 0.5rem; 
        }
        .feature-desc { 
            color: #999; 
            font-size: 0.9rem; 
        }
        .solved-banner { 
            background: rgba(34,197,94,0.1); 
            border: 1px solid rgba(34,197,94,0.3); 
            border-radius: 10px; 
            padding: 1.5rem; 
            margin-bottom: 2rem; 
            text-align: center; 
        }
        .solved-banner h3 { 
            color: #22c55e; 
            margin-bottom: 0.5rem; 
        }
        .admin-link {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-left: 1rem;
        }
        .admin-link:hover {
            background: rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📦 SerialLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isLoggedIn): ?>
                    <a href="my-account.php">My Account</a>
                    <?php if ($hasAdminPrivs): ?>
                        <a href="admin.php" class="admin-link">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($isSolved || !$carlosExists): ?>
        <div class="solved-banner">
            <h3>✅ Lab Solved!</h3>
            <p>Congratulations! You've successfully exploited the insecure deserialization vulnerability.</p>
        </div>
        <?php endif; ?>

        <div class="lab-card">
            <span class="lab-badge">INSECURE DESERIALIZATION LAB</span>
            <h1 class="lab-title">Lab 1: Modifying Serialized Objects</h1>
            <p class="lab-description">
                This lab uses a serialization-based session mechanism and is vulnerable to privilege escalation. 
                Edit the serialized object in the session cookie to exploit this vulnerability and gain administrative privileges. 
                Then, delete the user <strong>carlos</strong>.
            </p>

            <div class="credentials-box">
                <h3>🔑 Your Credentials</h3>
                <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
            </div>

            <div class="hint-box">
                <h3>💡 Hint</h3>
                <p>After logging in, examine the session cookie. It appears to be URL and Base64-encoded. 
                Decode it to reveal a serialized PHP object. Look for an attribute that controls admin access.</p>
            </div>

            <div class="status-box <?php echo (!$carlosExists) ? 'solved' : ''; ?>">
                <div class="status-indicator"></div>
                <span>
                    <?php if (!$carlosExists): ?>
                        🎉 <strong>Congratulations!</strong> Lab solved - carlos has been deleted!
                    <?php else: ?>
                        Lab Status: <strong>Unsolved</strong> - Delete carlos to complete
                    <?php endif; ?>
                </span>
            </div>

            <div style="margin-top: 2rem;">
                <?php if ($isLoggedIn): ?>
                    <a href="my-account.php" class="btn btn-primary">My Account</a>
                    <?php if ($hasAdminPrivs): ?>
                        <a href="admin.php" class="btn btn-primary">Admin Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login to Start</a>
                <?php endif; ?>
                <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
                <a href="docs.php" class="btn btn-secondary">Documentation</a>
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3 class="feature-title">PHP Serialization</h3>
                <p class="feature-desc">Learn how PHP serialize() and unserialize() work and their security implications.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🍪</div>
                <h3 class="feature-title">Cookie Tampering</h3>
                <p class="feature-desc">Practice manipulating session cookies to modify serialized object data.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⬆️</div>
                <h3 class="feature-title">Privilege Escalation</h3>
                <p class="feature-desc">Understand how trusting client-side data leads to unauthorized access.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3 class="feature-title">Base64 Decoding</h3>
                <p class="feature-desc">Practice decoding and encoding data for web exploitation.</p>
            </div>
        </div>
    </div>
</body>
</html>
