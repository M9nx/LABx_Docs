<?php
session_start();
require_once '../progress.php';
markLabSolved(4);
require_once 'config.php';

// Check if carlos exists
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'carlos'");
$stmt->execute();
$carlosExists = $stmt->fetchColumn() > 0;

// If carlos still exists, redirect back
if ($carlosExists) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - RoleLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 255, 0, 0.3);
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
            color: #00ff00;
            text-decoration: none;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .success-container {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 25px;
            padding: 4rem;
            text-align: center;
            max-width: 600px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 20px rgba(0, 255, 0, 0.3); }
            50% { box-shadow: 0 0 40px rgba(0, 255, 0, 0.6); }
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 1s ease-in-out infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .success-title {
            font-size: 2.5rem;
            color: #00ff00;
            margin-bottom: 1rem;
        }
        .success-message {
            color: #88ff88;
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        .stats-box {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .stats-box h3 {
            color: #00ff00;
            margin-bottom: 1rem;
        }
        .stats-box ul {
            list-style: none;
            color: #aaffaa;
            text-align: left;
        }
        .stats-box li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 255, 0, 0.2);
        }
        .stats-box li:last-child {
            border-bottom: none;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #00cc00, #009900);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin: 0.5rem;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 255, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #00ff00;
            color: #00ff00;
        }
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 1000;
        }
        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #00ff00;
            animation: confetti-fall 3s ease-in-out forwards;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="confetti" id="confetti"></div>
    
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎉 RoleLab - SOLVED!</a>
        </div>
    </header>

    <div class="main-content">
        <div class="success-container">
            <div class="success-icon">🏆</div>
            <h1 class="success-title">Congratulations!</h1>
            <p class="success-message">
                You successfully exploited the <strong>role modification vulnerability</strong> 
                and deleted the target user <strong>carlos</strong>!
            </p>
            
            <div class="stats-box">
                <h3>🔓 Attack Summary</h3>
                <ul>
                    <li>✅ Discovered roleid field in JSON response</li>
                    <li>✅ Added roleid: 2 to profile update request</li>
                    <li>✅ Escalated privileges to administrator</li>
                    <li>✅ Accessed the admin panel at /admin.php</li>
                    <li>✅ Deleted user carlos</li>
                </ul>
            </div>

            <div class="stats-box">
                <h3>📚 Vulnerability Details</h3>
                <ul>
                    <li><strong>Type:</strong> Broken Access Control / Mass Assignment</li>
                    <li><strong>CWE:</strong> CWE-915 (Improperly Controlled Modification of Dynamically-Determined Object Attributes)</li>
                    <li><strong>Root Cause:</strong> Server blindly accepts all JSON fields including sensitive ones like roleid</li>
                    <li><strong>Fix:</strong> Whitelist only expected fields, never trust client input for role changes</li>
                </ul>
            </div>

            <a href="index.php" class="btn">Return to Lab</a>
            <a href="docs.php" class="btn btn-secondary">Read More</a>
        </div>
    </div>

    <script>
        // Create confetti
        const confettiContainer = document.getElementById('confetti');
        const colors = ['#00ff00', '#00cc00', '#88ff88', '#00ff88', '#44ff44'];
        
        for (let i = 0; i < 50; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.left = Math.random() * 100 + 'vw';
            piece.style.background = colors[Math.floor(Math.random() * colors.length)];
            piece.style.animationDelay = Math.random() * 3 + 's';
            piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
            confettiContainer.appendChild(piece);
        }
    </script>
</body>
</html>

