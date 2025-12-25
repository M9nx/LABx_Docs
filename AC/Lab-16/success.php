<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pollId = $_GET['poll_id'] ?? 2;

// Get poll info for display
$stmt = $pdo->prepare("SELECT * FROM slowvotes WHERE id = ?");
$stmt->execute([$pollId]);
$poll = $stmt->fetch(PDO::FETCH_ASSOC);

markLabSolved(16);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - IDOR Slowvote Bypass</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container {
            text-align: center;
            max-width: 700px;
            padding: 2rem;
        }
        .trophy {
            font-size: 5rem;
            animation: bounce 0.6s ease-in-out infinite alternate;
        }
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-15px); }
        }
        h1 {
            font-size: 2.5rem;
            color: #00ff00;
            margin: 1rem 0;
            text-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
        }
        .subtitle {
            color: #888;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .success-card {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .success-card h2 {
            color: #66ff66;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #ccc;
            line-height: 1.8;
        }
        .leaked-data {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .leaked-data h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .leaked-data code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 8px;
            color: #88ff88;
            font-family: monospace;
            margin-top: 0.5rem;
            white-space: pre-wrap;
        }
        .attack-summary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }
        .attack-summary h3 {
            color: #9370DB;
            margin-bottom: 1rem;
        }
        .attack-summary ul {
            list-style: none;
            padding: 0;
        }
        .attack-summary li {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(106, 90, 205, 0.2);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .attack-summary li:last-child { border-bottom: none; }
        .step-num {
            background: #9370DB;
            color: white;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        .code-block {
            background: #0d0d0d;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00cc00, #009900);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-3px); }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: fall linear forwards;
        }
        @keyframes fall {
            to { transform: translateY(100vh) rotate(720deg); }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="trophy">🏆</div>
        <h1>Congratulations!</h1>
        <p class="subtitle">You've successfully exploited the IDOR vulnerability!</p>

        <div class="success-card">
            <h2>🎯 Lab 16 Solved!</h2>
            <p>
                You bypassed the Slowvote visibility restrictions by directly calling the API endpoint.
                While the web UI properly enforced access controls, the API endpoint only checked 
                if you were logged in (authenticated) without verifying if you had permission to 
                view the specific poll (authorized).
            </p>
        </div>

        <?php if ($poll): ?>
        <div class="leaked-data">
            <h4>🔓 Accessed Restricted Poll:</h4>
            <code>Poll V<?php echo $poll['id']; ?>: <?php echo htmlspecialchars($poll['title']); ?>

Visibility: <?php echo $poll['visibility']; ?>

<?php echo htmlspecialchars(substr($poll['description'], 0, 300)); ?>...</code>
        </div>
        <?php endif; ?>

        <div class="attack-summary">
            <h3>🔓 Attack Summary</h3>
            <ul>
                <li>
                    <span class="step-num">1</span>
                    <span>Logged in as Bob (user without permission)</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Tried to view private poll via UI → Access Denied ✓</span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span>Discovered the /api/slowvote.php endpoint</span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span>Called API directly with poll_id parameter</span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span>API returned poll data without checking permissions!</span>
                </li>
            </ul>
            
            <div class="code-block">
POST /api/slowvote.php HTTP/1.1
Host: localhost
Cookie: PHPSESSID=your_session

action=info&poll_id=<?php echo $pollId; ?>

// Server returned restricted poll data! ❌</div>
        </div>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>

    <script>
        const colors = ['#9370DB', '#00ff00', '#ff6666', '#00ccff', '#ffaa00'];
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 5000);
            }, i * 100);
        }
    </script>
</body>
</html>
