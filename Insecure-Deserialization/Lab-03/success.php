<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Success Page
 */
require_once '../progress.php';
$isSolved = isLabSolved(3);

// Check if morale.txt exists
$moraleExists = file_exists(__DIR__ . '/home/carlos/morale.txt');
$labSolved = !$moraleExists || $isSolved;

$deleted = isset($_GET['deleted']);
$solved = isset($_GET['solved']) || $labSolved;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $solved ? 'Lab Solved!' : 'Account Deleted' ?> - AvatarVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-container {
            background: rgba(255,255,255,0.05);
            border: 1px solid <?= $solved ? 'rgba(34, 197, 94, 0.3)' : 'rgba(249,115,22,0.2)' ?>;
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        h1 {
            color: <?= $solved ? '#22c55e' : '#f97316' ?>;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .message {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        .flag-box {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .flag-box h3 {
            color: #22c55e;
            margin-bottom: 0.75rem;
        }
        .flag-box code {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-family: 'Consolas', monospace;
            font-size: 1rem;
            display: inline-block;
        }
        .info-card {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(249,115,22,0.2);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: left;
        }
        .info-card h4 {
            color: #f97316;
            margin-bottom: 0.75rem;
        }
        .info-card p {
            color: #aaa;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .info-card code {
            background: rgba(249,115,22,0.2);
            color: #fb923c;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
        }
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
    </style>
</head>
<body>
    <div class="success-container">
        <?php if ($solved): ?>
            <div class="icon">🎉</div>
            <h1>Congratulations!</h1>
            <p class="message">
                You have successfully exploited the insecure deserialization vulnerability!<br>
                Carlos's <code>morale.txt</code> file has been deleted.
            </p>
            
            <div class="flag-box">
                <h3>🏁 Flag</h3>
                <code>FLAG{avatar_link_arbitrary_file_delete}</code>
            </div>
            
            <div class="info-card">
                <h4>What Happened?</h4>
                <p>
                    You modified the <code>avatar_link</code> attribute in the serialized session 
                    cookie to point to <code>/home/carlos/morale.txt</code>. When you triggered 
                    the account deletion, the server trusted your modified cookie data and deleted 
                    Carlos's file instead of your avatar.
                </p>
            </div>
        <?php else: ?>
            <div class="icon">✓</div>
            <h1>Account Deleted</h1>
            <p class="message">
                Your account has been deleted successfully, along with your avatar file.
            </p>
            
            <div class="info-card">
                <h4>Lab Objective</h4>
                <p>
                    The goal is to delete <code>/home/carlos/morale.txt</code> by exploiting 
                    the account deletion feature. Try modifying the <code>avatar_link</code> 
                    in your session cookie before deleting your account.
                </p>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="btn-primary">Back to Lab</a>
        <a href="setup_db.php" class="btn-secondary">Reset Lab</a>
    </div>
</body>
</html>
