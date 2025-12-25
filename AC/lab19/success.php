<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$labSolved = isLabSolved(19);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success - Lab 19 Completed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
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
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #a5b4fc; text-decoration: none; }
        .nav-links a:hover { color: #c7d2fe; }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(16, 185, 129, 0.4);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 0 50px rgba(16, 185, 129, 0.15);
        }
        .trophy { font-size: 5rem; margin-bottom: 1rem; }
        .success-card h1 {
            color: #6ee7b7;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .success-card h2 {
            color: #64748b;
            font-size: 1rem;
            font-weight: normal;
            margin-bottom: 2rem;
        }
        .status-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .status-box p { color: #94a3b8; line-height: 1.7; }
        .status-box strong { color: #6ee7b7; }
        .progress-section {
            margin: 2rem 0;
            text-align: left;
        }
        .progress-section h3 {
            color: #a5b4fc;
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
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        .progress-text {
            color: #64748b;
            font-size: 0.85rem;
        }
        .learn-more {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }
        .learn-more h3 { color: #a5b4fc; margin-bottom: 1rem; }
        .learn-more ul { list-style: none; padding: 0; }
        .learn-more li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .learn-more a { color: #93c5fd; text-decoration: none; }
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
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); border: 1px solid #444; color: #94a3b8; }
        .btn:hover { transform: translateY(-3px); }
        .confetti { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 999; }
        .not-solved {
            background: rgba(239, 68, 68, 0.05);
            border-color: rgba(239, 68, 68, 0.3);
        }
        .not-solved .trophy { filter: grayscale(1); opacity: 0.3; }
        .not-solved h1 { color: #fca5a5; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
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
            <h1>Lab 19 Solved!</h1>
            <h2>IDOR Delete Saved Projects Attack</h2>
            
            <div class="status-box">
                <p>
                    <strong>Excellent work!</strong> You've successfully exploited the IDOR 
                    vulnerability to delete another user's saved projects. This attack demonstrates 
                    how missing ownership validation can allow attackers to manipulate other users' data.
                </p>
            </div>

            <?php
            $solvedLabs = getAllSolvedLabs();
            $totalLabs = 19;
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
                    <li><a href="docs-references.php">→ External Resources & Bug Bounty Reports</a></li>
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
        const colors = ['#6366f1', '#8b5cf6', '#6ee7b7', '#fcd34d', '#f472b6'];
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
            
            <div class="status-box" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3);">
                <p>
                    You haven't completed Lab 19 yet. Head to the lab and exploit the IDOR 
                    vulnerability to delete <strong>victim_designer's</strong> saved projects!
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
