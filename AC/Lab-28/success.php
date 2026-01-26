<?php
/**
 * Lab 28: Success Page - Flag Submission
 * MTN Developers Portal
 */

require_once 'config.php';
require_once '../progress.php';

$showFlag = false;
$flagMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exploitProof = $_POST['exploit_proof'] ?? '';
    
    // Check if they removed a user from a team they don't own
    if ($pdo && isLoggedIn()) {
        $userId = $_SESSION['lab28_user_id'];
        
        // Check activity log for successful IDOR attacks
        $stmt = $pdo->prepare("
            SELECT * FROM activity_log 
            WHERE actor_user_id = ? 
            AND action_type = 'idor_attack_success'
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $attack = $stmt->fetch();
        
        if ($attack) {
            $showFlag = true;
            markLabSolved(28);
            $flagMessage = "IDOR attack detected! You removed user {$attack['target_user_id']} from team {$attack['target_team_id']} without authorization.";
        } else {
            // Alternative: Check if they just have correct proof
            if (stripos($exploitProof, '1113') !== false && stripos($exploitProof, '0002') !== false) {
                $showFlag = true;
                markLabSolved(28);
                $flagMessage = "Proof validated! You demonstrated the IDOR vulnerability.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Flag - Lab 28</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 45px;
            height: 45px;
            background: #000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            color: #ffcc00;
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #000;
        }
        .nav-links a {
            color: #000;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
        }
        .main-content {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
        .back-link:hover { color: #ffcc00; }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h1 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .card p {
            color: #aaa;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 0.5rem;
        }
        .form-group textarea {
            width: 100%;
            padding: 0.875rem;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 8px;
            color: #fff;
            font-family: 'Consolas', monospace;
            resize: vertical;
            min-height: 120px;
        }
        .form-group textarea:focus {
            outline: none;
            border-color: #ffcc00;
        }
        .btn {
            padding: 0.875rem 1.5rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 204, 0, 0.4);
        }
        .flag-box {
            background: rgba(68, 255, 68, 0.1);
            border: 2px solid #44ff44;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .flag-box h2 {
            color: #44ff44;
            margin-bottom: 1rem;
        }
        .flag-box .flag {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 1.1rem;
            color: #00ff88;
            word-break: break-all;
            margin: 1rem 0;
        }
        .flag-box p {
            color: #aaa;
        }
        .objective-box {
            background: rgba(255, 204, 0, 0.1);
            border-left: 4px solid #ffcc00;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-radius: 0 8px 8px 0;
        }
        .objective-box h3 {
            color: #ffcc00;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">
            <div class="logo-icon">MTN</div>
            <div class="logo-text">Developers <span>Portal</span></div>
        </a>
        <nav class="nav-links">
            <a href="index.php">← Back to Lab</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="docs.php">Docs</a>
        </nav>
    </header>

    <main class="main-content">
        <a href="index.php" class="back-link">← Back to Lab Description</a>

        <?php if ($showFlag): ?>
        <div class="flag-box">
            <h2>🎉 Congratulations!</h2>
            <p><?= htmlspecialchars($flagMessage) ?></p>
            <div class="flag"><?= LAB_FLAG ?></div>
            <p>You have successfully exploited the IDOR vulnerability!</p>
        </div>
        <?php endif; ?>

        <div class="card">
            <h1>🏁 Submit Your Exploit</h1>
            <p>
                Prove that you successfully exploited the IDOR vulnerability by providing evidence 
                of removing a user from a team you don't have permission to manage.
            </p>

            <div class="objective-box">
                <h3>🎯 Primary Objective</h3>
                <p>Remove Carol (user_id: <code>1113</code>) from Bob's Team (team_id: <code>0002</code>) 
                while logged in as the attacker account.</p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="exploit_proof">Paste the API response or describe your exploit:</label>
                    <textarea id="exploit_proof" name="exploit_proof" 
                              placeholder="Paste the JSON response from the remove_member API showing the successful IDOR attack..."></textarea>
                </div>
                <button type="submit" class="btn">Submit & Get Flag 🚩</button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Hints</h2>
            <ol style="color: #aaa; line-height: 2; padding-left: 1.5rem;">
                <li>Login as <code>attacker</code> (user_id: 1111)</li>
                <li>Go to your team management page</li>
                <li>Intercept the "Remove" request with Burp Suite</li>
                <li>Change <code>team_id</code> to <code>0002</code> (Bob's team)</li>
                <li>Change <code>user_id</code> to <code>1113</code> (Carol)</li>
                <li>Send the modified request</li>
                <li>The response will contain Carol's info and Bob's team name (Info Disclosure)</li>
                <li>Carol is now removed from Bob's team!</li>
            </ol>
        </div>
    </main>
</body>
</html>
