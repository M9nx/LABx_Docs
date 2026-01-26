<?php
// Lab 29: LinkedPro Newsletter Platform - Success Page
require_once 'config.php';
require_once '../progress.php';

$flag = "FLAG{linkedin_idor_newsletter_subscribers_exposed_2024}";
$labSolved = false;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedFlag = trim($_POST['flag'] ?? '');
    if ($submittedFlag === $flag) {
        $labSolved = true;
        markLabSolved(29);
        // Save progress
        if (isLoggedIn()) {
            logActivity($conn, $_SESSION['user_id'], 'lab_solved', 'lab', '29', 'Lab 29 completed successfully');
        }
        // Set cookie for main page tracking
        setcookie('lab29_solved', 'true', time() + (86400 * 365), '/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $labSolved ? '🎉 Lab Solved!' : 'Submit Flag' ?> - Lab 29</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f1419 50%, #0a0a0f 100%);
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .nav-bar {
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 102, 194, 0.2);
        }
        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .nav-logo span {
            color: #057642;
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(5, 118, 66, 0.3);
            border-radius: 16px;
            padding: 3rem;
            max-width: 500px;
            text-align: center;
        }
        .success-card.solved {
            border-color: rgba(5, 118, 66, 0.5);
            background: linear-gradient(135deg, rgba(5, 118, 66, 0.1), rgba(5, 118, 66, 0.05));
        }
        .trophy {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        .success-card h1 {
            color: #057642;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #888;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .flag-display {
            background: rgba(5, 118, 66, 0.1);
            border: 1px solid rgba(5, 118, 66, 0.3);
            border-radius: 8px;
            padding: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
            color: #057642;
            margin-bottom: 1.5rem;
            word-break: break-all;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #888;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-family: monospace;
        }
        .form-group input:focus {
            outline: none;
            border-color: #0a66c2;
        }
        .error-message {
            color: #ff6b6b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .btn {
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #057642, #034a2a);
            color: white;
            border: none;
            width: 100%;
            justify-content: center;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(5, 118, 66, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #0a66c2;
            border: 1px solid rgba(10, 102, 194, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(10, 102, 194, 0.1);
        }
        .action-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        .celebration {
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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
            top: -20px;
            animation: confetti-fall 3s linear forwards;
        }
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">Linked<span>Pro</span></a>
    </nav>
    
    <div class="main-content">
        <?php if ($labSolved): ?>
            <div class="confetti" id="confetti"></div>
            <div class="success-card solved celebration">
                <div class="trophy">🏆</div>
                <h1>Congratulations!</h1>
                <p>You successfully exploited the IDOR vulnerability to access newsletter subscriber data that you shouldn't have access to!</p>
                <div class="flag-display"><?= htmlspecialchars($flag) ?></div>
                <p style="font-size: 0.9rem;">You demonstrated how missing authorization checks on API endpoints can lead to sensitive data exposure.</p>
                <div class="action-links">
                    <a href="index.php" class="btn btn-secondary">🔬 Lab Home</a>
                    <a href="docs.php" class="btn btn-secondary">📚 Read Docs</a>
                    <a href="../index.php" class="btn btn-primary">🏠 All Labs</a>
                </div>
            </div>
            <script>
                // Confetti animation
                const colors = ['#0a66c2', '#057642', '#ffa500', '#ff6b6b', '#7fc4fd'];
                const confettiContainer = document.getElementById('confetti');
                for (let i = 0; i < 50; i++) {
                    setTimeout(() => {
                        const piece = document.createElement('div');
                        piece.className = 'confetti-piece';
                        piece.style.left = Math.random() * 100 + 'vw';
                        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                        piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
                        piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                        confettiContainer.appendChild(piece);
                        setTimeout(() => piece.remove(), 3000);
                    }, i * 50);
                }
            </script>
        <?php else: ?>
            <div class="success-card">
                <div class="trophy">🚩</div>
                <h1>Submit Your Flag</h1>
                <p>Enter the flag you discovered by exploiting the IDOR vulnerability to view another user's newsletter subscribers.</p>
                
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="error-message">❌ Incorrect flag. Keep trying!</div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="flag">Flag</label>
                        <input type="text" id="flag" name="flag" placeholder="FLAG{...}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">🏁 Submit Flag</button>
                </form>
                
                <div class="action-links">
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                    <a href="docs.php" class="btn btn-secondary">📚 Need Help?</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
