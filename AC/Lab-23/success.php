<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$message = '';
$success = false;
$discovered_tags = [];

// Process submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discovered_tags'])) {
    $submitted = $_POST['discovered_tags'];
    
    // Target victim tags (internal IDs 49790001-49790007)
    $victim_tags = [
        49790001 => 'Production-Critical',
        49790002 => 'Contains-PII',
        49790003 => 'Payment-Systems',
        49790004 => 'Legacy-Vulnerable',
        49790005 => 'Internal-Only',
        49790006 => 'Needs-Audit',
        49790007 => 'SOC2-Scope'
    ];
    
    // Parse submitted tags
    $found_count = 0;
    foreach ($victim_tags as $id => $name) {
        if (stripos($submitted, $name) !== false || strpos($submitted, (string)$id) !== false) {
            $found_count++;
            $discovered_tags[$id] = $name;
        }
    }
    
    // Check for encoded IDs being submitted
    foreach ($victim_tags as $id => $name) {
        $gid = "gid://tagscope/AsmTag/$id";
        $encoded = base64_encode($gid);
        if (strpos($submitted, $encoded) !== false) {
            if (!isset($discovered_tags[$id])) {
                $found_count++;
                $discovered_tags[$id] = $name;
            }
        }
    }
    
    // Require at least 3 tags to be discovered
    if ($found_count >= 3) {
        $success = true;
        markLabSolved(23);
        $message = "Congratulations! You successfully exploited the IDOR vulnerability and discovered $found_count out of 7 victim tags!";
        
        // Log the successful completion
        if (isset($pdo)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['user_id'] ?? 0,
                    'LAB_COMPLETED',
                    "Discovered $found_count tags: " . implode(', ', $discovered_tags),
                    $_SERVER['REMOTE_ADDR']
                ]);
            } catch (PDOException $e) {
                // Ignore logging errors
            }
        }
    } else {
        $message = "Not enough tags discovered. You found $found_count tag(s). You need to discover at least 3 of the 7 victim tags. Keep enumerating!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Lab Completed!' : 'Submit Solution'; ?> - Lab 23</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
            text-align: center;
        }
        .success-card {
            border-color: rgba(0, 255, 0, 0.3);
            background: rgba(0, 255, 0, 0.05);
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
        }
        .card-title {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .success-card .card-title { color: #00ff00; }
        .card-title.pending { color: #ff4444; }
        .card-message {
            color: #ccc;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        .discovered-list {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 255, 0, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .discovered-list h4 {
            color: #00ff00;
            margin-bottom: 1rem;
        }
        .discovered-list ul {
            list-style: none;
        }
        .discovered-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #aaffaa;
        }
        .discovered-list li:last-child { border: none; }
        .discovered-list code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            color: #00ff00;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            color: #ff6666;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.3);
            color: #e0e0e0;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 150px;
        }
        .form-group textarea:focus {
            outline: none;
            border-color: #ff4444;
        }
        .form-hint {
            color: #888;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 1rem;
            color: #ff6666;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #00cc00, #009900);
            color: white;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 0, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .hint-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .hint-box h4 {
            color: #ffa500;
            margin-bottom: 0.8rem;
        }
        .hint-box p {
            color: #ffcc80;
            margin-bottom: 0.5rem;
        }
        .hint-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            color: #ffa500;
        }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            pointer-events: none;
            animation: confetti-fall 3s ease-in forwards;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🏷️ TagScope</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($success): ?>
            <div class="card success-card">
                <div class="success-icon">🎉</div>
                <h1 class="card-title">Lab Completed!</h1>
                <p class="card-message"><?php echo htmlspecialchars($message); ?></p>
                
                <div class="discovered-list">
                    <h4>🔓 Discovered Victim Tags:</h4>
                    <ul>
                        <?php foreach ($discovered_tags as $id => $name): ?>
                            <li>
                                <code><?php echo $id; ?></code> - 
                                <strong><?php echo htmlspecialchars($name); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="action-buttons">
                    <a href="../index.php" class="btn btn-success">🏠 Back to All Labs</a>
                    <a href="lab-description.php" class="btn btn-secondary">📋 Review Lab</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <h1 class="card-title pending">🏆 Submit Your Solution</h1>
                <p class="card-message">
                    Paste the discovered victim tags below to prove you've successfully exploited the IDOR vulnerability.
                    You need to discover at least <strong>3 out of 7</strong> victim tags.
                </p>

                <?php if ($message): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="discovered_tags">Discovered Tags (paste tag names, IDs, or API responses):</label>
                        <textarea name="discovered_tags" id="discovered_tags" placeholder="Example:
Production-Critical
Contains-PII
49790003 - Payment-Systems
Z2lkOi8vdGFnc2NvcGUvQXNtVGFnLzQ5NzkwMDA0"><?php echo isset($_POST['discovered_tags']) ? htmlspecialchars($_POST['discovered_tags']) : ''; ?></textarea>
                        <p class="form-hint">You can paste tag names, internal IDs, encoded IDs, or full API responses.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">🚀 Submit Solution</button>
                </form>

                <div class="hint-box">
                    <h4>💡 Need Help?</h4>
                    <p>Target internal IDs: <code>49790001</code> to <code>49790007</code></p>
                    <p>These belong to <code>victim_org</code> and are not accessible to your account.</p>
                    <p>Use the enumeration technique to discover tag names!</p>
                </div>

                <div class="action-buttons">
                    <a href="login.php" class="btn btn-primary">🚀 Start Exploiting</a>
                    <a href="docs.php" class="btn btn-secondary">📚 Read Documentation</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
    <script>
        // Confetti celebration
        const colors = ['#ff4444', '#00ff00', '#00aaff', '#ffa500', '#ff66ff', '#ffff00'];
        for (let i = 0; i < 100; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 4000);
            }, i * 30);
        }
    </script>
    <?php endif; ?>
</body>
</html>