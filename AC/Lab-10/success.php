<?php
session_start();
require_once '../progress.php';
markLabSolved(10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Completed! - SecureCorp</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .nav-links a:hover { color: #00ff00; }
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
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(0, 255, 0, 0.2);
            border-color: #00ff00;
            color: #00ff00;
        }
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-box {
            background: rgba(0, 255, 0, 0.05);
            border: 2px solid rgba(0, 255, 0, 0.3);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 1s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .success-box h1 {
            color: #00ff00;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .success-box p {
            color: #88ff88;
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .success-details {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }
        .success-details h3 {
            color: #00ff00;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .success-details ul {
            list-style: none;
        }
        .success-details li {
            padding: 0.5rem 0;
            color: #aaffaa;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .success-details li::before {
            content: '✓';
            color: #00ff00;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00cc00, #008800);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #00ff00;
            color: #00ff00;
        }
        .btn-secondary:hover {
            background: rgba(0, 255, 0, 0.1);
        }
        .vulnerability-note {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
        }
        .vulnerability-note h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .vulnerability-note p {
            color: #ff8888;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🏢 SecureCorp</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="success-box">
            <div class="success-icon">🎉</div>
            <h1>Lab Completed!</h1>
            <p>Congratulations! You have successfully exploited the URL-based access control bypass vulnerability!</p>
            
            <div class="success-details">
                <h3>What You Accomplished:</h3>
                <ul>
                    <li>Discovered the admin panel at /admin was blocked</li>
                    <li>Identified X-Original-URL header processing</li>
                    <li>Bypassed front-end access control</li>
                    <li>Accessed the admin panel without authentication</li>
                    <li>Successfully deleted the user "carlos"</li>
                </ul>
            </div>
            
            <div class="vulnerability-note">
                <h4>🔒 Security Lesson</h4>
                <p>Never rely solely on front-end or proxy-based access control. Always implement authorization checks in the back-end application. The X-Original-URL header should be carefully handled or disabled in production environments.</p>
            </div>
            
            <div class="action-buttons">
                <a href="../index.php" class="btn btn-primary">← Back to All Labs</a>
                <a href="docs.php" class="btn btn-secondary">📚 Read Documentation</a>
                <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
            </div>
        </div>
    </div>
</body>
</html>
