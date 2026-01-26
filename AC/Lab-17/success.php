<?php
require_once 'config.php';
require_once '../progress.php';

// Mark lab as solved
markLabSolved(17);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 17 Solved! - GitLab IDOR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0e0;
        }
        .container {
            text-align: center;
            max-width: 700px;
            padding: 2rem;
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 0.5s ease infinite alternate;
        }
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-15px); }
        }
        h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #00c853, #00e676);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .subtitle {
            color: #888;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid #00c853;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .card h2 {
            color: #00c853;
            margin-bottom: 1rem;
            text-align: center;
        }
        .exploit-summary {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .exploit-summary h3 {
            color: #fc6d26;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        .exploit-summary ul {
            list-style: none;
            padding: 0;
        }
        .exploit-summary li {
            padding: 0.4rem 0;
            color: #aaa;
            font-size: 0.9rem;
        }
        .exploit-summary li::before {
            content: '✓ ';
            color: #00c853;
        }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.8rem;
            color: #88ff88;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .vulnerable { color: #ff6666; }
        .secure { color: #00c853; }
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
            background: linear-gradient(135deg, #fc6d26, #e24329);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-3px); }
        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
        }
        .stat {
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #fc6d26;
        }
        .stat-label {
            color: #888;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">🎉</div>
        <h1>Lab 17 Solved!</h1>
        <p class="subtitle">IDOR External Status Check Information Disclosure</p>

        <div class="stats">
            <div class="stat">
                <div class="stat-value">17</div>
                <div class="stat-label">Labs Completed</div>
            </div>
            <div class="stat">
                <div class="stat-value">IDOR</div>
                <div class="stat-label">Vulnerability Type</div>
            </div>
            <div class="stat">
                <div class="stat-value">GitLab</div>
                <div class="stat-label">Real-World CVE</div>
            </div>
        </div>

        <div class="card">
            <h2>🔓 Exploit Summary</h2>
            
            <div class="exploit-summary">
                <h3>What You Exploited</h3>
                <ul>
                    <li>IDOR in the <code>external_status_check_id</code> parameter</li>
                    <li>API returned status check info regardless of project ownership</li>
                    <li>Leaked private project names, external URLs, API keys</li>
                    <li>Cross-project data access without authorization</li>
                </ul>
            </div>

            <div class="exploit-summary">
                <h3>Sensitive Data Leaked</h3>
                <ul>
                    <li>Private project names and IDs</li>
                    <li>External validation endpoint URLs</li>
                    <li>API keys and webhook secrets in URLs</li>
                    <li>Protected branch configurations</li>
                    <li>Project owner usernames</li>
                </ul>
            </div>

            <h3 style="color: #ff6666; margin: 1rem 0 0.5rem;">Vulnerable Code:</h3>
            <div class="code-block">
<span class="vulnerable">// No project ownership check!</span>
$stmt = $pdo->prepare("
    SELECT * FROM external_status_checks 
    WHERE id = ?  <span class="vulnerable">// ANY status check ID works</span>
");
$stmt->execute([$external_status_check_id]);
            </div>

            <h3 style="color: #00c853; margin: 1rem 0 0.5rem;">Secure Code:</h3>
            <div class="code-block">
<span class="secure">// Verify status check belongs to the project</span>
$stmt = $pdo->prepare("
    SELECT * FROM external_status_checks 
    WHERE id = ? <span class="secure">AND project_id = ?</span>
");
$stmt->execute([$external_status_check_id, <span class="secure">$project_id</span>]);
            </div>
        </div>

        <div class="actions">
            <a href="../index.php" class="btn btn-primary">← All Labs</a>
            <a href="index.php" class="btn btn-secondary">Lab Home</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
        </div>
    </div>
</body>
</html>
