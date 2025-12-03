<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3 - User role controlled by request parameter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
        }

        .lab-title {
            color: #ff4444;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .lab-subtitle {
            color: #cccccc;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }

        .difficulty-badge {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
        }

        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #ff6666;
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
        }

        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.1);
        }

        .hint-box {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid #00ffff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .hint-box h4 {
            color: #00ffff;
            margin-bottom: 10px;
        }

        .hint-box p {
            color: #ccffff;
            font-size: 0.9rem;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-link {
            color: #ff6666;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 102, 102, 0.1);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .lab-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="lab-title">Lab 3</h1>
            <p class="lab-subtitle">User role controlled by request parameter</p>
            <div class="difficulty-badge">Apprentice</div>
        </div>

        <div class="lab-card">
            <h2 class="section-title">🎯 Your Mission</h2>
            <p style="color: #cccccc; line-height: 1.6; font-size: 1.1rem;">Access the admin interface and delete the user <strong style="color: #ff6666;">carlos</strong>.</p>
        </div>

        <div class="hint-box">
            <h4>💡 Exploitation Hint</h4>
            <p>
                This lab has an access control vulnerability where user roles are controlled by a client-side parameter. 
                After logging in, examine your browser's cookies and look for parameters that control your role.
            </p>
        </div>

        <div class="action-buttons">
            <a href="login.php" class="btn btn-primary">
                🚀 Start Lab
            </a>
            <a href="docs.php" class="btn btn-secondary">
                📚 View Solution
            </a>
        </div>

        <div class="navigation">
            <a href="../lab2/" class="nav-link">← Lab 2</a>
            <a href="../" class="nav-link">🏠 AC Labs Home</a>
            <a href="#" class="nav-link" style="color: #666; border-color: #666;">Lab 4 →</a>
        </div>
    </div>
</body>
</html>