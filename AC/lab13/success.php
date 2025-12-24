<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verify user is now admin
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: profile.php");
    exit;
}

markLabSolved(13);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - Referer Lab</title>
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
            max-width: 700px;
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
        .subtitle {
            font-size: 1.3rem;
            color: #888;
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
        .step-num {
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
        .code-highlight {
            background: rgba(0, 0, 0, 0.4);
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
            to { transform: translateY(100vh) rotate(720deg); }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="trophy">🏆</div>
        <h1>Congratulations!</h1>
        <p class="subtitle">You've successfully exploited the Referer-based access control!</p>

        <div class="success-card">
            <h2>🎯 Lab 13 Solved!</h2>
            <p>
                You successfully bypassed the Referer-based access control by spoofing the 
                HTTP Referer header to make your request appear as if it originated from the 
                admin panel, even though you're not an administrator.
            </p>
        </div>

        <div class="attack-summary">
            <h3>🔓 Attack Summary</h3>
            <ul>
                <li>
                    <span class="step-num">1</span>
                    <span>Logged in as admin (administrator:admin) and captured a role upgrade request</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Observed the request goes to <code>/admin-roles.php</code> with Referer header pointing to <code>/admin</code></span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span>Logged in as wiener in a private browser and obtained session cookie</span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span>Replaced the admin's session cookie with wiener's cookie in the captured request</span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span>Changed the username parameter to 'wiener' and replayed the request</span>
                </li>
                <li>
                    <span class="step-num">6</span>
                    <span>The server trusted the Referer header and upgraded wiener to admin!</span>
                </li>
            </ul>
            
            <div class="code-highlight">
GET /AC/lab13/admin-roles.php?username=wiener&action=upgrade HTTP/1.1
Host: localhost
Cookie: PHPSESSID=[wiener's-session-cookie]
Referer: http://localhost/AC/lab13/admin   <-- Spoofed!</div>
        </div>

        <div class="actions">
            <a href="profile.php" class="btn btn-primary">View Admin Profile</a>
            <a href="admin.php" class="btn btn-secondary">Admin Panel</a>
            <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>

    <script>
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
