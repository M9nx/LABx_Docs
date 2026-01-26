<?php
session_start();
require_once '../progress.php';
markLabSolved(2);

// Check if carlos user exists to determine if lab is solved
require_once 'config.php';
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'carlos'");
$stmt->execute();
$carlosExists = $stmt->fetchColumn() > 0;

// If carlos still exists, redirect back
if ($carlosExists) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab: Unprotected admin functionality with unpredictable URL - Solved!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #330000 100%);
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
        }
        .success-banner {
            background: linear-gradient(135deg, #cc0000 0%, #ff0000 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
            backdrop-filter: blur(20px);
        }
        .success-banner h2 {
            font-size: 1.5rem;
            margin: 0 0 1rem 0;
            font-weight: 700;
        }
        .share-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .share-buttons span {
            font-size: 0.9rem;
            font-weight: 500;
        }
        .share-buttons a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.2);
            border-radius: 25px;
            font-size: 0.8rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .share-buttons a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-1px);
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .lab-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }
        .lab-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .lab-badge {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .lab-badge.solved {
            background: linear-gradient(135deg, #00b09b, #96c93d);
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        .back-link {
            color: #64b5f6;
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: none;
            color: white;
        }
        .success-message {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #a5d6a7;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .users-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .users-section h2 {
            color: white;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        .user-list {
            font-family: 'Inter', monospace;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }
        .user-item {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .user-deleted {
            color: #ef5350;
            text-decoration: line-through;
        }
        .continue-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.5rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .continue-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-banner">
        <h2>Congratulations, you solved the lab!</h2>
        <div class="share-buttons">
            <span>Share your skills!</span>
            <a href="#">📱</a>
            <a href="#">💼</a>
            <a href="lab-description.php">Continue learning →</a>
        </div>
    </div>
    
    <div class="container">
        <div class="lab-header">
            <a href="lab-description.php" class="back-link">
                ← Back to lab description
            </a>
            
            <div class="lab-title">
                <span class="lab-badge solved">LAB</span>
                <span class="lab-badge solved">Solved</span>
                <h1>Unprotected admin functionality with unpredictable URL</h1>
            </div>
        </div>
        
        <div class="success-message">
            <strong>User deleted successfully!</strong>
        </div>
        
        <div class="users-section">
            <h2>Users</h2>
            <div class="user-list">
                <?php
                $stmt = $pdo->query("SELECT username FROM users ORDER BY id");
                $users = $stmt->fetchAll();
                
                // Show remaining users
                foreach ($users as $user) {
                    echo '<div class="user-item">' . htmlspecialchars($user['username']) . '</div>';
                }
                
                // Show deleted carlos
                echo '<div class="user-item user-deleted">carlos - Delete</div>';
                ?>
            </div>
            
            <a href="docs.php" class="continue-button">📖 View Documentation</a>
            <a href="../" class="continue-button" style="margin-left: 1rem; background: rgba(255, 255, 255, 0.2);">🏠 Back to Labs</a>
        </div>
    </div>
</body>
</html>
