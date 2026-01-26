<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(18);
if (!$labSolved) {
    // Mark lab as solved when user reaches success page
    markLabSolved(18);
    $labSolved = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - Lab 18 Completed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(150, 191, 72, 0.3);
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #96bf48;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #96bf48; }
        .main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .success-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(0, 200, 100, 0.4);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 0 50px rgba(0, 200, 100, 0.2);
        }
        .trophy { font-size: 5rem; margin-bottom: 1rem; }
        .success-card h1 {
            color: #66ff99;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .success-card h2 {
            color: #888;
            font-size: 1rem;
            font-weight: normal;
            margin-bottom: 2rem;
        }
        .status-box {
            background: rgba(0, 200, 100, 0.1);
            border: 1px solid rgba(0, 200, 100, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .status-box p { color: #aaa; line-height: 1.7; }
        .status-box strong { color: #66ff99; }
        .progress-section {
            margin: 2rem 0;
            text-align: left;
        }
        .progress-section h3 {
            color: #96bf48;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .progress-bar-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }
        .progress-bar {
            background: linear-gradient(135deg, #96bf48, #5c6ac4);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        .progress-text {
            color: #888;
            font-size: 0.85rem;
        }
        .learn-more {
            background: rgba(150, 191, 72, 0.1);
            border: 1px solid rgba(150, 191, 72, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }
        .learn-more h3 { color: #96bf48; margin-bottom: 1rem; }
        .learn-more ul { list-style: none; padding: 0; }
        .learn-more li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .learn-more a { color: #88ccff; text-decoration: none; }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #96bf48, #5c6ac4); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); border: 1px solid #666; color: #ccc; }
        .btn:hover { transform: translateY(-3px); }
        .confetti { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 999; }

        .not-solved {
            background: rgba(255, 100, 100, 0.05);
            border-color: rgba(255, 100, 100, 0.3);
        }
        .not-solved .trophy { filter: grayscale(1); opacity: 0.3; }
        .not-solved h1 { color: #ff8888; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 109 124" fill="none">
                    <path d="M74.7 14.8L62.2 55.4H46.7L34.2 14.8C33.1 11 29.5 8.3 25.5 8.3H0L31.5 115.5H40.8L54.5 67.8L68.2 115.5H77.5L109 8.3H83.5C79.5 8.3 75.8 11 74.7 14.8Z" fill="#96bf48"/>
                </svg>
                Shopify Admin
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <?php if ($labSolved): ?>
        <div class="success-card">
            <div class="trophy">🏆</div>
            <h1>Lab 18 Solved!</h1>
            <h2>IDOR Session Expiration Attack</h2>
            
            <div class="status-box">
                <p>
                    <strong>Excellent work!</strong> You've successfully exploited the IDOR 
                    vulnerability to expire another user's sessions. This attack demonstrates 
                    how missing authorization checks can allow attackers to force logout other users.
                </p>
            </div>

            <?php
            $solvedLabs = getAllSolvedLabs();
            $totalLabs = 18;
            $solvedCount = count($solvedLabs);
            $percentage = ($solvedCount / $totalLabs) * 100;
            ?>
            <div class="progress-section">
                <h3>📊 Your Progress</h3>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <p class="progress-text"><?php echo $solvedCount; ?> of <?php echo $totalLabs; ?> labs completed (<?php echo round($percentage); ?>%)</p>
            </div>

            <div class="learn-more">
                <h3>📚 Continue Learning</h3>
                <ul>
                    <li><a href="docs-vulnerability.php">→ Understanding IDOR Vulnerabilities</a></li>
                    <li><a href="docs-prevention.php">→ How to Prevent IDOR Attacks</a></li>
                    <li><a href="docs-references.php">→ External Resources & HackerOne Reports</a></li>
                </ul>
            </div>

            <div class="actions">
                <a href="../index.php" class="btn btn-primary">🏠 Back to All Labs</a>
                <a href="docs.php" class="btn btn-secondary">📖 Read More</a>
            </div>
        </div>

        <canvas class="confetti" id="confetti"></canvas>
        <script>
        const canvas = document.getElementById('confetti');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const particles = [];
        const colors = ['#96bf48', '#5c6ac4', '#66ff99', '#ffcc00', '#ff88aa'];
        for (let i = 0; i < 150; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                size: Math.random() * 8 + 4,
                speedY: Math.random() * 3 + 2,
                speedX: Math.random() * 2 - 1,
                color: colors[Math.floor(Math.random() * colors.length)],
                rotation: Math.random() * 360
            });
        }
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.rotation * Math.PI / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.size/2, -p.size/2, p.size, p.size);
                ctx.restore();
                p.y += p.speedY;
                p.x += p.speedX;
                p.rotation += 3;
                if (p.y > canvas.height) {
                    p.y = -20;
                    p.x = Math.random() * canvas.width;
                }
            });
            requestAnimationFrame(animate);
        }
        animate();
        setTimeout(() => { canvas.style.opacity = '0'; }, 5000);
        </script>
        <?php else: ?>
        <div class="success-card not-solved">
            <div class="trophy">🔒</div>
            <h1>Lab Not Solved Yet</h1>
            <h2>Complete the challenge to unlock this page</h2>
            
            <div class="status-box" style="background: rgba(255, 100, 100, 0.1); border-color: rgba(255, 100, 100, 0.3);">
                <p>
                    You haven't completed Lab 18 yet. Head to the lab and exploit the IDOR 
                    vulnerability to expire <strong>victim_store's</strong> sessions!
                </p>
            </div>

            <div class="actions">
                <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
                <a href="lab-description.php" class="btn btn-secondary">📋 View Instructions</a>
                <a href="index.php" class="btn btn-secondary">← Back</a>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
