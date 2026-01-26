<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$victimEmail = $_GET['email'] ?? 'Unknown Victim';

markLabSolved(15);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - IDOR PII Leakage</title>
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
        .leaked-data {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.5);
            border-radius: 10px;
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
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 5px;
            color: #88ff88;
            font-family: monospace;
            margin-top: 0.5rem;
        }
        .attack-summary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }
        .attack-summary h3 {
            color: #ffcc00;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .attack-summary ul {
            list-style: none;
            padding: 0;
        }
        .attack-summary li {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 204, 0, 0.2);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .attack-summary li:last-child {
            border-bottom: none;
        }
        .step-num {
            background: #ffcc00;
            color: #000;
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
            <h2>🎯 Lab 15 Solved!</h2>
            <p>
                You successfully exploited the IDOR vulnerability in the <code>getUserNotes</code> API endpoint 
                to access another user's personally identifiable information (PII). The server returned 
                sensitive data including phone numbers, addresses, tax IDs, and private notes without 
                verifying if you were authorized to view that data.
            </p>
        </div>

        <div class="leaked-data">
            <h4>🔓 Data Leaked From:</h4>
            <p style="color: #aaa;">Victim Email:</p>
            <code><?php echo htmlspecialchars($victimEmail); ?></code>
            <p style="color: #aaa; margin-top: 1rem;">Data exposed includes:</p>
            <code>
                • Phone Number<br>
                • Physical Address<br>
                • Company Name & Tax ID<br>
                • Bank Account Number<br>
                • Private Notes (including confidential business info)<br>
                • API Keys<br>
                • Account Settings
            </code>
        </div>

        <div class="vulnerability-box">
            <h4>🔓 Root Cause</h4>
            <p>
                The <code>/api/getUserNotes.php</code> endpoint accepts an email parameter and returns all 
                associated user data without checking if the authenticated user is authorized to view that 
                email's data. The server trusts the client-provided email and returns data for <strong>any 
                valid email address</strong>.
            </p>
        </div>

        <div class="attack-summary">
            <h3>🔓 Attack Summary</h3>
            <ul>
                <li>
                    <span class="step-num">1</span>
                    <span>Logged in with valid attacker credentials to establish a session</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Discovered the getUserNotes API endpoint in the dashboard</span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span>Analyzed the request structure showing email-based user identification</span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span>Modified the userEmail parameter from own email to victim's email</span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span>Server returned victim's PII without authorization check!</span>
                </li>
            </ul>
            
            <div class="code-highlight">
POST /api/getUserNotes.php HTTP/1.1

{
  "params": {
    "updates": [{
      "param": "user",
      "value": { "userEmail": "<?php echo htmlspecialchars($victimEmail); ?>" },
      "op": "a"
    }]
  }
}

// Server returned all PII for this email! ❌</div>
        </div>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
            <a href="../index.php" class="btn btn-secondary">← All Labs</a>
        </div>
    </div>

    <script>
        const colors = ['#ffcc00', '#00ff00', '#ff4444', '#00ffff', '#ff00ff'];
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
