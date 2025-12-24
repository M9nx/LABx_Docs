<?php
session_start();
require_once '../progress.php';
markLabSolved(9);

// Check if user successfully logged in as carlos
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'carlos') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - ChatLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #44ff44;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #44ff44; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(0, 255, 0, 0.2);
            border-color: #44ff44;
            color: #44ff44;
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-card {
            background: rgba(0, 255, 0, 0.05);
            border: 2px solid rgba(0, 255, 0, 0.3);
            border-radius: 20px;
            padding: 4rem;
            text-align: center;
            max-width: 600px;
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(0, 255, 0, 0.2); }
            to { box-shadow: 0 0 40px rgba(0, 255, 0, 0.4); }
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 1s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .success-card h1 {
            color: #44ff44;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #88ff88;
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        .credentials-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 255, 0, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .credentials-box h3 {
            color: #44ff44;
            margin-bottom: 0.5rem;
        }
        .credentials-box code {
            color: #88ff88;
            font-size: 1.1rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #44ff44, #00cc00);
            color: #000;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #44ff44;
            color: #44ff44;
        }
        .btn-secondary:hover {
            background: #44ff44;
            color: #000;
        }
        .vulnerability-info {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            text-align: left;
        }
        .vulnerability-info h3 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .vulnerability-info p {
            color: #ff8888;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🎉 ChatLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="docs.php">Documentation</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h1>Lab Solved!</h1>
            <p>Congratulations! You successfully exploited the IDOR vulnerability to retrieve carlos's password from the chat transcript and logged into their account.</p>
            
            <div class="credentials-box">
                <h3>Compromised Account</h3>
                <code>carlos:h5a2xfj8k3</code>
            </div>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability Exploited</h3>
                <p>The application stored chat transcripts with predictable filenames (1.txt, 2.txt, etc.) and served them without any authentication or authorization checks. This allowed you to access another user's chat history containing sensitive information.</p>
            </div>
            
            <div class="action-buttons">
                <a href="docs.php" class="btn btn-primary">📚 Learn More</a>
                <a href="../index.php" class="btn btn-secondary">🔐 More Labs</a>
            </div>
        </div>
    </div>
</body>
</html>
