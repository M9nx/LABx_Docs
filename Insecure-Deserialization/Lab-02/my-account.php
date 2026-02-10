<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * My Account Page
 * 
 * This page displays the current session cookie and user information.
 * The session cookie contains a serialized User object with username and access_token.
 */
require_once 'config.php';

$currentUser = getCurrentUser();
$session = getSessionFromCookie();

// Redirect if not logged in
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

// Get raw cookie for display
$rawCookie = $_COOKIE['session'] ?? '';
$decodedCookie = base64_decode($rawCookie);
$hasAdminPrivs = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - TypeJuggle Shop</title>
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
        .container { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
        .page-title { font-size: 2rem; margin-bottom: 2rem; color: #f97316; }
        .lab-card { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(249,115,22,0.2); 
            border-radius: 15px; 
            padding: 2rem; 
            margin-bottom: 2rem; 
            backdrop-filter: blur(10px); 
        }
        .lab-card h2 { color: #fb923c; margin-bottom: 1rem; font-size: 1.25rem; }
        .lab-card p { color: #ccc; line-height: 1.7; }
        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 0.75rem;
        }
        .info-label { color: #888; }
        .info-value { color: #fff; font-weight: 500; }
        code {
            background: rgba(249,115,22,0.2);
            color: #fb923c;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            word-break: break-all;
        }
        .cookie-display {
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            overflow-x: auto;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85rem;
        }
        .cookie-display .label { color: #888; margin-bottom: 0.5rem; }
        .cookie-display .value { color: #fb923c; word-break: break-all; }
        .btn-primary { 
            display: inline-block;
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 20px rgba(249,115,22,0.4); 
        }
        .hint-box { 
            background: rgba(0,255,255,0.1); 
            border: 1px solid rgba(0,255,255,0.3); 
            border-radius: 10px; 
            padding: 1rem; 
            margin-top: 1rem; 
        }
        .hint-box h3 { color: #00ffff; margin-bottom: 0.5rem; font-size: 1rem; }
        .hint-box p { color: #aadddd; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">TypeJuggle Shop</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="my-account.php">My Account</a>
                <?php if ($hasAdminPrivs): ?>
                    <a href="admin.php" style="color: #fbbf24;">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?= htmlspecialchars($currentUser['username']) ?>)</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">My Account</h1>
        
        <div class="lab-card">
            <h2>Profile Information</h2>
            <div class="info-grid">
                <span class="info-label">Username:</span>
                <span class="info-value"><?= htmlspecialchars($currentUser['username']) ?></span>
                
                <span class="info-label">Full Name:</span>
                <span class="info-value"><?= htmlspecialchars($currentUser['full_name']) ?></span>
                
                <span class="info-label">Email:</span>
                <span class="info-value"><?= htmlspecialchars($currentUser['email']) ?></span>
                
                <span class="info-label">Role:</span>
                <span class="info-value"><?= htmlspecialchars($currentUser['role']) ?></span>
            </div>
            
            <?php if ($hasAdminPrivs): ?>
                <a href="admin.php" class="btn-primary">Access Admin Panel</a>
            <?php endif; ?>
        </div>
        
        <div class="lab-card">
            <h2>Session Cookie (Debug)</h2>
            <p>Your current session is stored in a serialized cookie. Below is the decoded content:</p>
            
            <div class="cookie-display">
                <div class="label">Base64 Encoded Cookie:</div>
                <div class="value"><?= htmlspecialchars($rawCookie) ?></div>
            </div>
            
            <div class="cookie-display">
                <div class="label">Decoded (Serialized PHP Object):</div>
                <div class="value"><?= htmlspecialchars($decodedCookie) ?></div>
            </div>
            
            <div class="hint-box">
                <h3>Attack Hint</h3>
                <p>
                    Notice the serialized object structure. The <code>access_token</code> is a string.
                    What happens if you change it to an integer? PHP uses loose comparison (<code>==</code>) 
                    to validate sessions...
                </p>
            </div>
        </div>
        
        <div class="lab-card">
            <h2>Serialization Format Reference</h2>
            <p>Understanding PHP serialization format:</p>
            <div class="cookie-display" style="line-height: 1.8;">
                <code>O:4:"User"</code> - Object of class "User" (4 chars)<br>
                <code>:2:</code> - Has 2 properties<br>
                <code>s:8:"username"</code> - String key "username" (8 chars)<br>
                <code>s:6:"wiener"</code> - String value "wiener" (6 chars)<br>
                <code>s:12:"access_token"</code> - String key "access_token" (12 chars)<br>
                <code>s:64:"abc123..."</code> - String value (64 chars hash)<br>
                <br>
                <strong style="color: #00ffff;">Type indicators:</strong><br>
                <code>s:</code> = String<br>
                <code>i:</code> = Integer<br>
                <code>b:</code> = Boolean<br>
            </div>
        </div>
    </div>
</body>
</html>
