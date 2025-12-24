<?php
session_start();
require_once '../progress.php';

// Mark lab as solved
markLabSolved(11);

// Get current user info
require_once 'config.php';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - MethodLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container {
            text-align: center;
            max-width: 600px;
            padding: 3rem;
        }
        .success-icon {
            font-size: 8rem;
            animation: bounce 1s ease-in-out;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }
        .success-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(0, 255, 0, 0.5);
            border-radius: 20px;
            padding: 3rem;
            margin-top: 2rem;
            box-shadow: 0 0 40px rgba(0, 255, 0, 0.2);
        }
        .success-card h1 {
            color: #00ff00;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .success-card h2 {
            color: #66ff66;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            font-weight: normal;
        }
        .success-message {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .success-message p {
            color: #aaffaa;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .success-message p:last-child {
            margin-bottom: 0;
        }
        .exploit-summary {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .exploit-summary h3 {
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .exploit-summary code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 8px;
            color: #66ff66;
            font-family: 'Courier New', monospace;
            margin: 0.5rem 0;
            overflow-x: auto;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 1.1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00ff00, #00cc00);
            color: #000;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 25px rgba(0, 255, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
        .btn-info {
            background: linear-gradient(135deg, #00aaff, #0077cc);
            color: white;
        }
        .btn-info:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 25px rgba(0, 170, 255, 0.4);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">🎉</div>
        
        <div class="success-card">
            <h1>Lab Solved!</h1>
            <h2>Method-based Access Control Bypassed</h2>
            
            <div class="success-message">
                <p><strong>Congratulations <?php echo htmlspecialchars($username); ?>!</strong></p>
                <p>You've successfully exploited the method-based access control vulnerability by converting a POST request to GET.</p>
                <p>You promoted yourself to admin by bypassing the method-specific authorization check!</p>
            </div>

            <div class="exploit-summary">
                <h3>🎯 What You Did:</h3>
                <p>1. Logged in as admin and captured the POST request:</p>
                <code>POST /admin-upgrade.php<br>username=carlos</code>
                <p>2. Logged in as wiener and changed the method:</p>
                <code>GET /admin-upgrade.php?username=wiener</code>
                <p>3. The GET request bypassed the admin check and promoted you!</p>
            </div>

            <div class="action-buttons">
                <a href="../index.php" class="btn btn-primary">🏠 All Labs</a>
                <a href="profile.php" class="btn btn-info">👤 My Profile</a>
                <a href="setup_db.php" class="btn btn-secondary">🔄 Try Again</a>
            </div>
        </div>
    </div>
</body>
</html>
