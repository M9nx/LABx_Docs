<?php
session_start();
require_once '../progress.php';
$labSolved = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedKey = trim($_POST['api_key'] ?? '');
    $correctKey = 'API-KEY-carlos-Xt7Kp9Qm2Wn5Bv8J';
    
    if ($submittedKey === $correctKey) {
        $labSolved = true;
        markLabSolved(7);
        $message = "🎉 Congratulations! You successfully obtained Carlos's API key!";
    } else {
        $message = "❌ Incorrect API key. Try again!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Solution - RedirectLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            color: #ff4444;
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
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .submit-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .submit-card h1 {
            color: #ff4444;
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ccc;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
            font-family: 'Consolas', monospace;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .message.success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }
        .message.error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #ff6666;
        }
        .success-animation {
            text-align: center;
            padding: 2rem;
        }
        .success-animation .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔄 RedirectLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="submit-card">
            <?php if ($labSolved): ?>
                <div class="success-animation">
                    <div class="icon">🏆</div>
                    <h1 style="color: #00ff00;">Lab Solved!</h1>
                    <p style="color: #ccc; margin-top: 1rem;"><?php echo $message; ?></p>
                    <a href="index.php" style="display: inline-block; margin-top: 2rem; padding: 0.8rem 2rem; background: linear-gradient(135deg, #00aa00, #008800); color: white; text-decoration: none; border-radius: 8px;">Back to Lab</a>
                </div>
            <?php else: ?>
                <h1>🏁 Submit Solution</h1>
                
                <?php if ($message): ?>
                    <div class="message error"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <p style="color: #888; margin-bottom: 1.5rem; text-align: center;">
                    Enter Carlos's API key to complete the lab.
                </p>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="api_key">Carlos's API Key</label>
                        <input type="text" id="api_key" name="api_key" placeholder="Enter the API key..." required>
                    </div>
                    <button type="submit" class="btn-submit">Submit Solution</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>