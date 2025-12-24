<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['manager_id'])) {
    header("Location: login.php");
    exit;
}

$bannerName = $_GET['banner'] ?? 'Unknown';
$bannerId = $_GET['bannerid'] ?? 'Unknown';

markLabSolved(14);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - IDOR Lab</title>
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
            max-width: 800px;
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
        .deleted-banner {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .deleted-banner h4 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .deleted-banner code {
            display: block;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 5px;
            color: #88ff88;
            font-family: monospace;
            margin-top: 0.5rem;
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
        .vulnerability-box {
            background: rgba(255, 100, 0, 0.1);
            border: 1px solid rgba(255, 100, 0, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .vulnerability-box h4 {
            color: #ffaa00;
            margin-bottom: 0.5rem;
        }
        .vulnerability-box p {
            color: #ccc;
            font-size: 0.95rem;
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
        <p class="subtitle">You've successfully exploited the IDOR vulnerability!</p>

        <div class="success-card">
            <h2>🎯 Lab 14 Solved!</h2>
            <p>
                You successfully deleted another manager's banner by exploiting the Insecure Direct 
                Object Reference (IDOR) vulnerability in the banner deletion endpoint. The server 
                validated your access to the client and campaign but failed to verify that the 
                banner actually belonged to your campaign.
            </p>
        </div>

        <div class="deleted-banner">
            <h4>🗑️ Deleted Banner</h4>
            <p style="color: #aaa;">You deleted a banner belonging to another manager:</p>
            <code>
                Banner Name: <?php echo htmlspecialchars($bannerName); ?><br>
                Banner ID: <?php echo htmlspecialchars($bannerId); ?>
            </code>
        </div>

        <div class="vulnerability-box">
            <h4>🔓 Root Cause</h4>
            <p>
                The <code>banner-delete.php</code> endpoint validates access to the <strong>client</strong> 
                and <strong>campaign</strong> but never verifies that the <strong>banner</strong> actually 
                belongs to that campaign. This allows horizontal privilege escalation where Manager A can 
                delete Manager B's banners.
            </p>
        </div>

        <div class="attack-summary">
            <h3>🔓 Attack Summary</h3>
            <ul>
                <li>
                    <span class="step-num">1</span>
                    <span>Logged in as Manager A (attacker) and navigated to your own campaign's banners</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Extracted CSRF token from the delete link URL parameters</span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span>Identified Manager B's banner IDs (6-11) through enumeration or other means</span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span>Crafted malicious URL using YOUR clientid/campaignid but VICTIM's bannerid</span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span>Server validated your access to client/campaign but skipped banner ownership check</span>
                </li>
                <li>
                    <span class="step-num">6</span>
                    <span>Banner belonging to Manager B was successfully deleted!</span>
                </li>
            </ul>
            
            <div class="code-highlight">
GET /AC/lab14/banner-delete.php HTTP/1.1
?token=[YOUR_CSRF_TOKEN]
&clientid=1          // YOUR client (passes auth check ✓)
&campaignid=1        // YOUR campaign (passes auth check ✓)
&bannerid=<?php echo htmlspecialchars($bannerId); ?>         // VICTIM's banner (NO check! ✓)</div>
        </div>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
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
