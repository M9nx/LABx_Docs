<?php
session_start();

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
    <title>Lab: Unprotected admin functionality - Solved!</title>
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
            padding: 3rem 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(255, 0, 0, 0.3);
        }
        
        .success-banner h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 800;
        }
        
        .share-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .share-buttons span {
            font-weight: 500;
        }
        
        .share-buttons a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .share-buttons a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        
        .lab-header {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 0, 0, 0.3);
            margin-bottom: 2rem;
        }
        
        .back-link {
            color: #ff0000;
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: #cc0000;
        }
        
        .lab-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .lab-badge {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .lab-badge.solved {
            background: linear-gradient(135deg, #00cc00, #00ff00);
            color: white;
        }
        
        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }
        
        .success-message {
            background: linear-gradient(135deg, rgba(0, 204, 0, 0.2), rgba(0, 153, 0, 0.1));
            border: 1px solid #00cc00;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #00ff00;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .users-section {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
        }
        
        .users-section h2 {
            color: #ff0000;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        
        .user-list {
            margin-bottom: 2rem;
        }
        
        .user-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .user-item.user-deleted {
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.2), rgba(204, 0, 0, 0.1));
            border-color: #ff0000;
            color: #ff6666;
            text-decoration: line-through;
            opacity: 0.7;
        }
        
        .continue-button {
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        }
        
        .continue-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.6);
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-banner">
        <h2>🎉 Congratulations, you solved the lab!</h2>
        <div class="share-buttons">
            <span>Share your success!</span>
            <a href="#">📱 Share</a>
            <a href="#">💼 LinkedIn</a>
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
                <h1>Unprotected admin functionality</h1>
            </div>
        </div>
        
        <div class="success-message">
            <strong>✅ User deleted successfully! Lab completed.</strong>
        </div>
        
        <div class="users-section">
            <h2>Current Users</h2>
            <div class="user-list">
                <?php
                $stmt = $pdo->query("SELECT username FROM users ORDER BY id");
                $users = $stmt->fetchAll();
                
                // Show remaining users
                foreach ($users as $user) {
                    echo '<div class="user-item">' . htmlspecialchars($user['username']) . '</div>';
                }
                
                // Show deleted carlos
                echo '<div class="user-item user-deleted">carlos (DELETED)</div>';
                ?>
            </div>
            
            <a href="../lab2/lab-description.php" class="continue-button">Try Next Lab</a>
        </div>
    </div>
</body>
</html>