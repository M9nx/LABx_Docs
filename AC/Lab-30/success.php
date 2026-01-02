<?php
// Lab 30: Stocky - Success/Flag Submission Page
require_once 'config.php';

$submitted = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flag = trim($_POST['flag'] ?? '');
    $correctFlag = 'FLAG{IDOR_STOCKY_SETTINGS_PWNED_2024}';
    
    if ($flag === $correctFlag) {
        $submitted = true;
        markLabSolved(30);
    } else {
        $error = 'Incorrect flag. Try again!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Flag - Lab 30</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: #0a0a0f;
            min-height: 100vh;
        }
        .nav-bar {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-logo { color: white; font-size: 1.4rem; font-weight: bold; text-decoration: none; }
        .nav-links a { color: rgba(255,255,255,0.9); text-decoration: none; margin-left: 1.5rem; }
        .container { max-width: 600px; margin: 0 auto; padding: 3rem 2rem; }
        .card {
            background: linear-gradient(145deg, #1a1a2e, #16162a);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(124, 58, 237, 0.3);
        }
        h1 { color: white; text-align: center; margin-bottom: 2rem; font-size: 2rem; }
        .flag-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(124, 58, 237, 0.5);
            border-radius: 10px;
            background: rgba(0,0,0,0.3);
            color: white;
            font-family: monospace;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .flag-input:focus {
            outline: none;
            border-color: #7c3aed;
        }
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.4);
        }
        .error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #fca5a5;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .success-box {
            background: linear-gradient(145deg, #059669, #047857);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            color: white;
        }
        .success-box h1 { margin-bottom: 1rem; }
        .success-box .trophy { font-size: 5rem; margin-bottom: 1rem; }
        .success-box p { font-size: 1.2rem; margin-bottom: 1.5rem; }
        .back-link {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 1rem;
        }
        .hint {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
            color: #fbbf24;
            font-size: 0.9rem;
        }
        .hint strong { display: block; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">📦 Lab 30: Stocky</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="docs.php">Docs</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($submitted): ?>
        <div class="success-box">
            <div class="trophy">🏆</div>
            <h1>Lab 30 Complete!</h1>
            <p>Excellent work! You successfully exploited the IDOR vulnerability in Stocky's settings system!</p>
            <p>You demonstrated how missing ownership verification allows attackers to access and modify other users' data.</p>
            <a href="../index.php" class="back-link">← Back to All Labs</a>
        </div>
        <?php else: ?>
        <div class="card">
            <h1>🚩 Submit Your Flag</h1>
            
            <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="text" name="flag" class="flag-input" placeholder="FLAG{...}" required>
                <button type="submit" class="btn">Submit Flag</button>
            </form>
            
            <div class="hint">
                <strong>💡 How to find the flag:</strong>
                Login as any user and go to the Settings page. Either:<br>
                1. Change the settings_id to another user's ID and save<br>
                2. Import settings from another user's Settings ID
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
