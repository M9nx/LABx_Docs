<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$attackerActions = [];
$keysCreated = 0;
$keysDeleted = false;

// Get attacker's activity
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'attacker_member'");
$stmt->execute();
$attackerId = $stmt->fetchColumn();

if ($attackerId) {
    // Check keys created by attacker
    $stmt = $pdo->prepare("
        SELECT ak.*, o.name as org_name
        FROM api_keys ak
        JOIN organizations o ON ak.org_id = o.id
        WHERE ak.created_by = ?
        ORDER BY ak.created_at DESC
    ");
    $stmt->execute([$attackerId]);
    $attackerKeys = $stmt->fetchAll();
    $keysCreated = count($attackerKeys);
    
    // Mark lab as solved if attacker created keys
    if ($keysCreated > 0) {
        markLabSolved(20);
    }
    
    // Check original keys count (should be 6, less means deletion)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM api_keys WHERE org_id = (SELECT id FROM organizations WHERE uuid = 'org-aaaaaaaa-1111-1111-1111-111111111111')");
    $stmt->execute();
    $currentKeyCount = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Complete - KeyVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-container {
            max-width: 700px;
            width: 100%;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #22c55e, #14b8a6, #22c55e);
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 0.5s ease;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .success-card h1 {
            color: #86efac;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .success-card .subtitle {
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat-box {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 1rem;
        }
        .stat-box .number {
            font-size: 2rem;
            font-weight: bold;
            color: #5eead4;
        }
        .stat-box .label {
            color: #64748b;
            font-size: 0.85rem;
        }
        .achievement-list {
            text-align: left;
            background: rgba(34, 197, 94, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .achievement-list h4 {
            color: #86efac;
            margin-bottom: 1rem;
        }
        .achievement {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .achievement:last-child { border-bottom: none; }
        .achievement .check {
            color: #22c55e;
            font-size: 1.25rem;
        }
        .vuln-summary {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .vuln-summary h4 {
            color: #fca5a5;
            margin-bottom: 1rem;
        }
        .vuln-summary p {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .vuln-summary code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            color: #5eead4;
        }
        .nav-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-btn.primary {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
        }
        .nav-btn.secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .confetti {
            position: fixed;
            top: -10px;
            font-size: 1.5rem;
            animation: fall linear forwards;
        }
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
        </div>
    </header>

    <main class="main-content">
        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">🏆</div>
                <h1>Lab Complete!</h1>
                <p class="subtitle">You've successfully exploited the IDOR vulnerability</p>

                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="number"><?php echo $keysCreated; ?></div>
                        <div class="label">Keys Created</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">3</div>
                        <div class="label">Vulns Found</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">1</div>
                        <div class="label">IDOR Type</div>
                    </div>
                </div>

                <div class="achievement-list">
                    <h4>🏅 Achievements Unlocked</h4>
                    <div class="achievement">
                        <span class="check">✅</span>
                        <span><strong>Key Viewer:</strong> Viewed sensitive API keys as a member</span>
                    </div>
                    <div class="achievement">
                        <span class="check">✅</span>
                        <span><strong>Key Creator:</strong> Created unauthorized API keys</span>
                    </div>
                    <div class="achievement">
                        <span class="check">✅</span>
                        <span><strong>Key Destroyer:</strong> Deleted API keys without permission</span>
                    </div>
                    <div class="achievement">
                        <span class="check">✅</span>
                        <span><strong>Role Bypass:</strong> Bypassed role-based access controls</span>
                    </div>
                </div>

                <div class="vuln-summary">
                    <h4>📝 Vulnerability Summary</h4>
                    <p>
                        The <code>api/keys.php</code> endpoint verified organization membership but failed to check
                        the user's <strong>role permissions</strong>. This allowed users with <code>member</code> role
                        to perform privileged actions (CREATE, DELETE) that should require <code>admin</code> or
                        <code>owner</code> roles.
                    </p>
                    <p style="margin-top: 0.75rem;">
                        <strong>Fix:</strong> Add role verification before allowing sensitive operations:
                    </p>
                    <p style="margin-top: 0.5rem;">
                        <code>if ($membership['role'] === 'member') { deny_access(); }</code>
                    </p>
                </div>

                <div class="nav-buttons">
                    <a href="index.php" class="nav-btn secondary">← Back to Lab</a>
                    <a href="setup_db.php" class="nav-btn secondary">🔄 Reset Lab</a>
                    <a href="../index.php" class="nav-btn primary">All Labs →</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Confetti animation
        const emojis = ['🎉', '🏆', '⭐', '🔑', '🎊', '✨'];
        for (let i = 0; i < 30; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 4000);
            }, i * 100);
        }
    </script>
</body>
</html>
