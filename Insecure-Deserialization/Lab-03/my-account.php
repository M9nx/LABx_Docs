<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * My Account Page
 * 
 * This page displays user info and allows account deletion.
 * VULNERABILITY: The delete function uses avatar_link from the COOKIE, not the database.
 */
require_once 'config.php';

$session = getSessionFromCookie();
$currentUser = $session ? getCurrentUser() : null;

// Redirect if not logged in
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

// Get raw cookie for display
$rawCookie = $_COOKIE['session'] ?? '';
$decodedCookie = base64_decode($rawCookie);

// Get avatar_link from session (this is what will be deleted!)
$avatarFromSession = $session->avatar_link ?? 'Not set';

// Calculate target path for hint (replace wiener/avatar.jpg with carlos/morale.txt)
$targetPath = __DIR__ . '/home/carlos/morale.txt';
$targetPathLength = strlen($targetPath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - AvatarVault</title>
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
        .info-grid { display: grid; gap: 0.75rem; }
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 0.75rem; 
            background: rgba(0,0,0,0.2); 
            border-radius: 8px; 
        }
        .info-row span:first-child { color: #888; }
        .info-row span:last-child { color: #fff; font-weight: 500; }
        .cookie-display {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(249,115,22,0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            overflow-x: auto;
        }
        .cookie-display h4 { color: #f97316; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .cookie-display pre {
            color: #fb923c;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .hint-box { 
            background: rgba(0, 255, 255, 0.05); 
            border: 1px solid rgba(0, 255, 255, 0.2); 
            padding: 1.25rem; 
            border-radius: 10px; 
            margin-top: 1rem; 
        }
        .hint-box h4 { color: #00ffff; margin-bottom: 0.5rem; font-size: 0.95rem; }
        .hint-box p { color: #a0e0e0; font-size: 0.9rem; line-height: 1.6; }
        .hint-box code {
            background: rgba(0, 255, 255, 0.1);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #a0e0e0;
        }
        .danger-zone {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .danger-zone h2 { color: #ef4444; margin-bottom: 1rem; }
        .danger-zone p { color: #fca5a5; margin-bottom: 1.5rem; line-height: 1.6; }
        .btn-delete {
            display: inline-block;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(239, 68, 68, 0.4);
        }
        .avatar-info {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .avatar-info h4 { color: #ef4444; margin-bottom: 0.5rem; }
        .avatar-info code { 
            background: rgba(239, 68, 68, 0.2); 
            color: #fca5a5; 
            padding: 0.2rem 0.5rem; 
            border-radius: 4px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">AvatarVault</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="my-account.php" style="color: #f97316;">My Account</a>
                <a href="logout.php">Logout</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">My Account</h1>
        
        <div class="lab-card">
            <h2>Account Information</h2>
            <div class="info-grid">
                <div class="info-row">
                    <span>Username:</span>
                    <span><?= htmlspecialchars($currentUser['username']) ?></span>
                </div>
                <div class="info-row">
                    <span>Full Name:</span>
                    <span><?= htmlspecialchars($currentUser['full_name']) ?></span>
                </div>
                <div class="info-row">
                    <span>Email:</span>
                    <span><?= htmlspecialchars($currentUser['email']) ?></span>
                </div>
            </div>
        </div>

        <div class="lab-card">
            <h2>Session Cookie (Base64 Encoded)</h2>
            <div class="cookie-display">
                <h4>Encoded:</h4>
                <pre><?= htmlspecialchars($rawCookie) ?></pre>
            </div>
            <div class="cookie-display" style="margin-top: 1rem;">
                <h4>Decoded (Serialized PHP Object):</h4>
                <pre><?= htmlspecialchars($decodedCookie) ?></pre>
            </div>
            
            <div class="hint-box">
                <h4>💡 Observe the Structure</h4>
                <p>
                    Notice the <code>avatar_link</code> attribute in the serialized object. 
                    This contains the path to your avatar file. When you delete your account, 
                    the server will delete whatever file is specified here.
                </p>
            </div>
        </div>

        <div class="danger-zone">
            <h2>⚠️ Danger Zone</h2>
            <p>
                Deleting your account is permanent. This action will remove your profile data 
                and <strong>delete your avatar file</strong> from the server.
            </p>
            
            <div class="avatar-info">
                <h4>Avatar File to be Deleted:</h4>
                <p><code><?= htmlspecialchars($avatarFromSession) ?></code></p>
            </div>
            
            <form action="delete-account.php" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete your account? This will also delete your avatar file.');">
                <button type="submit" class="btn-delete">Delete My Account</button>
            </form>
            
            <div class="hint-box" style="margin-top: 1.5rem;">
                <h4>🎯 Attack Vector</h4>
                <p>
                    What if you modified the <code>avatar_link</code> in your session cookie 
                    before clicking delete? The server trusts the cookie data...
                </p>
                <p style="margin-top: 0.75rem;">
                    <strong>Target path (<?= $targetPathLength ?> chars):</strong><br>
                    <code style="word-break: break-all;"><?= htmlspecialchars($targetPath) ?></code>
                </p>
                <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #7dd3fc;">
                    Update the string length to <code>s:<?= $targetPathLength ?>:</code> in your payload!
                </p>
            </div>
        </div>
    </div>
</body>
</html>
