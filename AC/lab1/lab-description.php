<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab: Unprotected admin functionality</title>
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
        .lab-badge.not-solved {
            background: rgba(0, 0, 0, 0.8);
            color: #ff0000;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 0, 0, 0.5);
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: white;
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
            text-decoration: none;
            color: #cc0000;
        }
        .description-box {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .description-box h2 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: #ff0000;
            font-weight: 700;
        }
        .description-box p {
            line-height: 1.8;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
        }
        .objective {
            background: rgba(0, 0, 0, 0.5);
            border-left: 4px solid #ff0000;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        .objective h3 {
            margin-bottom: 0.8rem;
            color: #ff0000;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .start-button {
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
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
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        }
        .start-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.6);
            text-decoration: none;
            color: white;
        }
        .hint-box {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
            backdrop-filter: blur(10px);
        }
        .hint-box h4 {
            color: #ff4444;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }
        .hint-box p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }
    </style>
</head>
<body>
    
    
    <div class="container">
        <div class="lab-header">
            <a href="../" class="back-link">
                ← Back to lab description
            </a>
            
            <div class="lab-title">
                <span class="lab-badge not-solved">LAB</span>
                <span class="lab-badge not-solved">Not solved</span>
                <h1>Unprotected admin functionality</h1>
            </div>
        </div>
        
        <div class="description-box">
            <h2>Lab Description</h2>
            <p>This lab has an unprotected admin panel.</p>
            
            <div class="objective">
                <h3>Lab Objective</h3>
                <p>Solve the lab by deleting the user carlos.</p>
            </div>
            
            <p>This lab demonstrates a common access control vulnerability where administrative functionality is left completely unprotected. The admin panel exists but lacks any authentication or authorization mechanisms.</p>
            
            <div class="hint-box">
                <h4>💡 Hint</h4>
                <p>Look for clues about the location of the admin panel. Sometimes applications inadvertently disclose the paths to administrative interfaces.</p>
            </div>
            
            <p><strong>Learning Goals:</strong></p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: #666;">
                <li>Understand the risks of unprotected admin functionality</li>
                <li>Learn about information disclosure vulnerabilities</li>
                <li>Practice basic reconnaissance techniques</li>
                <li>Recognize the importance of proper access controls</li>
            </ul>
            
            <a href="index.php" class="start-button">Access the lab</a>
            <a href="docs.php" class="start-button" style="margin-left: 1rem; background: linear-gradient(135deg, #666666 0%, #444444 100%); box-shadow: 0 4px 15px rgba(102, 102, 102, 0.4);">📖 View Documentation</a>
        </div>
    </div>
</body>
</html>