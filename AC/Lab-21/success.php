<?php
// Lab 21: Success Page - Lab Completion
require_once 'config.php';

$target_id = $_GET['target_id'] ?? '';
$attack_type = $_GET['attack'] ?? '';

// Mark lab as solved
if (file_exists('../progress.php')) {
    require_once '../progress.php';
    markLabSolved(21);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎉 Lab Completed - IDOR Attack Success | Lab 21</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
        }
        .success-container {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(16, 185, 129, 0.5);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            position: relative;
            z-index: 10;
            box-shadow: 0 25px 80px rgba(16, 185, 129, 0.2);
        }
        .trophy {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 1s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #10b981, #34d399);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .exploit-details {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .exploit-details h3 {
            color: #34d399;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(16, 185, 129, 0.2);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #94a3b8; }
        .detail-value {
            color: #34d399;
            font-family: 'Consolas', monospace;
            font-weight: 600;
        }
        .vulnerability-explained {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .vulnerability-explained h3 {
            color: #a5b4fc;
            margin-bottom: 1rem;
        }
        .vulnerability-explained p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        .vulnerability-explained code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #f87171;
            font-size: 0.85rem;
        }
        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        .btn-secondary {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        /* Confetti */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: confetti-fall 3s linear infinite;
        }
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(720deg);
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="trophy">🏆</div>
        <h1>Lab Completed!</h1>
        <p class="subtitle">You successfully exploited the IDOR vulnerability!</p>
        
        <div class="exploit-details">
            <h3>🎯 Attack Summary</h3>
            <div class="detail-row">
                <span class="detail-label">Vulnerability Type</span>
                <span class="detail-value">IDOR (Insecure Direct Object Reference)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Target Resource</span>
                <span class="detail-value">Column Settings ID: <?php echo htmlspecialchars($target_id); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Action Performed</span>
                <span class="detail-value">Modified another user's settings</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Impact</span>
                <span class="detail-value">Unauthorized Data Modification</span>
            </div>
        </div>
        
        <div class="vulnerability-explained">
            <h3>🔍 Why This Worked</h3>
            <p>The application accepts a <code>settings_id</code> parameter from the user without verifying that the current user owns or has permission to modify those settings. The vulnerable code:</p>
            <p style="margin-top: 0.75rem;"><code>$settings_id = $_POST['settings_id'];</code></p>
            <p style="margin-top: 0.75rem;"><code>UPDATE column_settings ... WHERE id = $settings_id</code></p>
            <p style="margin-top: 0.75rem;">The fix should verify that the settings belong to the current user's store before allowing the update.</p>
        </div>
        
        <div class="buttons">
            <a href="docs.php" class="btn btn-primary">
                📚 Read Full Documentation
            </a>
            <a href="login.php" class="btn btn-secondary">
                🔄 Try Again
            </a>
            <a href="../index.php" class="btn btn-secondary">
                🏠 All Labs
            </a>
        </div>
    </div>
    
    <script>
        // Create confetti
        const colors = ['#10b981', '#6366f1', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            document.body.appendChild(confetti);
        }
    </script>
</body>
</html>
