<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Main Entry Point / Index Page
 */
require_once '../progress.php';
$isSolved = isLabSolved(2);
require_once 'config.php';

$currentUser = getCurrentUser();
$isLoggedIn = $currentUser !== null;
$hasAdminPrivs = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TypeJuggle Shop - Secure Session Management</title>
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
            text-decoration: none;
        }
        .feature-card:hover { 
            border-color: rgba(249,115,22,0.4); 
            transform: translateY(-3px); 
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
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">TypeJuggle Shop</a>
            <nav class="nav-links">
                <a href="../" class="btn-back">Back to Labs</a>
                <a href="index.php">Home</a>
                <?php if ($isLoggedIn): ?>
                    <a href="my-account.php">My Account</a>
                    <?php if ($hasAdminPrivs): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout (<?= htmlspecialchars($currentUser['username']) ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($isSolved): ?>
        <div class="solved-banner">
            <h3>Lab Solved!</h3>
            <p>Congratulations! You've successfully exploited PHP type juggling.</p>
        </div>
        <?php endif; ?>

        <div class="lab-card">
            <span class="lab-badge">Lab 02 - Practitioner</span>
            <h1 class="lab-title">Modifying Serialized Data Types</h1>
            <p class="lab-description">
                This lab uses a serialization-based session mechanism and is vulnerable to authentication bypass 
                through PHP type juggling. Your goal is to exploit this vulnerability to access the 
                <strong>administrator</strong> account and delete the user <code>carlos</code>.
            </p>
            
            <div class="credentials-box">
                <h3>Test Credentials</h3>
                <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
            </div>
            
            <div class="hint-box">
                <h3>Hint</h3>
                <p>PHP's loose comparison operator (<code>==</code>) behaves unexpectedly when comparing different data types. When comparing a string to an integer, PHP converts the string to an integer first. What happens when an integer <code>0</code> is compared to a non-numeric string?</p>
            </div>
            
            <div>
                <a href="login.php" class="btn btn-primary">Start Lab</a>
                <a href="lab-description.php" class="btn btn-secondary">View Details</a>
                <a href="docs.php" class="btn btn-secondary">Documentation</a>
            </div>
            
            <div class="status-box <?= $isSolved ? 'solved' : '' ?>">
                <div class="status-indicator"></div>
                <span><?= $isSolved ? 'Solved' : 'Not Solved' ?></span>
            </div>
        </div>

        <h2 style="color: #fb923c; margin-bottom: 1rem;">Quick Navigation</h2>
        <div class="features-grid">
            <a href="lab-description.php" class="feature-card">
                <h3 class="feature-title">Lab Description</h3>
                <p class="feature-desc">Detailed challenge information and hints</p>
            </a>
            <a href="docs.php" class="feature-card">
                <h3 class="feature-title">Technical Docs</h3>
                <p class="feature-desc">Vulnerability analysis and walkthrough</p>
            </a>
            <a href="my-account.php" class="feature-card">
                <h3 class="feature-title">My Account</h3>
                <p class="feature-desc">View your profile after login</p>
            </a>
            <a href="../" class="feature-card">
                <h3 class="feature-title">Back to Labs</h3>
                <p class="feature-desc">Return to lab selection</p>
            </a>
        </div>
    </div>
</body>
</html>
