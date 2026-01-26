<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user now has admin role
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: profile.php");
    exit;
}

// Mark lab as solved
markLabSolved(12);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - MultiStep Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        .trophy {
            font-size: 6rem;
            animation: bounce 0.6s ease-in-out infinite alternate;
        }
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-20px); }
        }
        h1 {
            font-size: 3rem;
            color: #00ff00;
            margin: 1rem 0;
            text-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
        }
        .success-card {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
        }
        .success-card h2 {
            color: #66ff66;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #ccc;
            line-height: 1.8;
        }
        .attack-summary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }
        .attack-summary h3 {
            color: #ff6666;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .attack-summary ul {
            list-style: none;
            padding: 0;
        }
        .attack-summary li {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .attack-summary li:last-child {
            border-bottom: none;
        }
        .attack-summary .step-num {
            background: #ff4444;
            color: white;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
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
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: fall linear forwards;
        }
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(720deg);
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="trophy">🏆</div>
        <h1>Congratulations!</h1>
        <p style="font-size: 1.3rem; color: #888;">You've successfully exploited the multi-step process vulnerability!</p>

        <div class="success-card">
            <h2>🎯 Lab 12 Solved!</h2>
            <p>
                You successfully bypassed the multi-step access control by directly sending
                a request to the confirmation endpoint, which lacked proper authorization checks.
            </p>
        </div>

        <div class="attack-summary">
            <h3>🔓 Attack Summary</h3>
            <ul>
                <li>
                    <span class="step-num">1</span>
                    <span>Logged in as <strong>wiener</strong> (low-privileged user)</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Identified that Step 3 (admin-confirm.php) doesn't verify admin role</span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span>Crafted a direct POST request to the confirmation endpoint</span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span>Sent: <code>username=wiener&role=admin&action=upgrade&confirmed=true</code></span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span>Elevated your account to admin without going through protected steps</span>
                </li>
            </ul>
        </div>

        <div class="actions">
            <a href="profile.php" class="btn btn-primary">View Admin Profile</a>
            <a href="admin.php" class="btn btn-secondary">Admin Panel</a>
            <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>

    <script>
        // Confetti effect
        const colors = ['#ff4444', '#00ff00', '#ffff00', '#00ffff', '#ff00ff'];
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
