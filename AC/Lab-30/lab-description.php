<?php
// Lab 30: Lab Description Page
$labInfo = [
    'number' => 30,
    'title' => 'IDOR in Inventory Settings - Stocky',
    'difficulty' => 'Medium',
    'category' => 'Broken Access Control',
    'vulnerability' => 'Insecure Direct Object Reference (IDOR)'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - <?= $labInfo['title'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: #0a0a0f;
            color: #e5e5e5;
            min-height: 100vh;
        }
        .nav-bar {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-logo { color: white; font-size: 1.4rem; font-weight: bold; text-decoration: none; }
        .nav-links a { color: rgba(255,255,255,0.9); text-decoration: none; margin-left: 1.5rem; }
        .container { max-width: 900px; margin: 0 auto; padding: 3rem 2rem; }
        .header-badge {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        h1 { font-size: 2.5rem; margin-bottom: 1.5rem; color: white; }
        .card {
            background: linear-gradient(145deg, #1a1a2e, #16162a);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(124, 58, 237, 0.2);
        }
        .card h2 {
            color: #a78bfa;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card p { color: #a0a0a0; line-height: 1.7; margin-bottom: 1rem; }
        .card p:last-child { margin-bottom: 0; }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .meta-item {
            background: rgba(124, 58, 237, 0.1);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }
        .meta-item label { color: #666; font-size: 0.85rem; display: block; margin-bottom: 0.25rem; }
        .meta-item span { color: #a78bfa; font-weight: 600; }
        .steps-list { list-style: none; counter-reset: step; }
        .steps-list li {
            counter-increment: step;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
            color: #ccc;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .steps-list li::before {
            content: counter(step);
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        code {
            background: rgba(124, 58, 237, 0.2);
            color: #c4b5fd;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
        .back-link {
            display: inline-block;
            color: #a78bfa;
            text-decoration: none;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">📦 Lab 30: Stocky</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="docs.php">Docs</a>
        </div>
    </nav>

    <div class="container">
        <a href="index.php" class="back-link">← Back to Lab Home</a>
        
        <span class="header-badge">Lab <?= $labInfo['number'] ?></span>
        <h1><?= $labInfo['title'] ?></h1>

        <div class="meta-grid">
            <div class="meta-item">
                <label>Difficulty</label>
                <span><?= $labInfo['difficulty'] ?></span>
            </div>
            <div class="meta-item">
                <label>Category</label>
                <span><?= $labInfo['category'] ?></span>
            </div>
            <div class="meta-item">
                <label>Vulnerability</label>
                <span>IDOR</span>
            </div>
        </div>

        <div class="card">
            <h2>📋 Lab Overview</h2>
            <p>
                Stocky is a fictional inventory management application used by e-commerce store owners to track low-stock products. 
                Each store has custom column settings that control which data columns are displayed in their inventory views.
            </p>
            <p>
                The application suffers from an Insecure Direct Object Reference (IDOR) vulnerability in its settings management system. 
                Users can access and modify other users' display settings by manipulating the <code>settings_id</code> parameter.
            </p>
        </div>

        <div class="card">
            <h2>🎯 Objective</h2>
            <p>
                Exploit the IDOR vulnerability to access or modify another user's column display settings. 
                Successfully doing so will reveal the flag. There are <strong>two attack vectors</strong>:
            </p>
            <ol class="steps-list">
                <li><strong>Direct Modification:</strong> Change the <code>settings_id</code> in the settings form to target another user's settings</li>
                <li><strong>Import Settings:</strong> Use the Import feature to copy settings from another user's <code>import_from_id</code></li>
            </ol>
        </div>

        <div class="card">
            <h2>🔬 Attack Steps</h2>
            <ol class="steps-list">
                <li>Login with any test account (e.g., <code>alice_shop</code> / <code>password123</code>)</li>
                <li>Navigate to the Settings page from the dashboard</li>
                <li>Note your Settings ID (Alice = 1, Bob = 2, Carol = 3, David = 4)</li>
                <li>Either change the <code>settings_id</code> field to another user's ID or use Import with another ID</li>
                <li>Submit the form to exploit the IDOR vulnerability</li>
                <li>Capture the flag and submit it to complete the lab</li>
            </ol>
        </div>

        <div class="card">
            <h2>📚 Learning Outcomes</h2>
            <p>After completing this lab, you will understand:</p>
            <ul style="margin-left: 1.5rem; color: #a0a0a0; line-height: 2;">
                <li>How IDOR vulnerabilities allow horizontal privilege escalation</li>
                <li>Why server-side ownership verification is critical</li>
                <li>The importance of validating that users can only access their own resources</li>
                <li>How to identify and exploit object reference vulnerabilities</li>
            </ul>
        </div>

        <a href="login.php" class="btn">🚀 Start the Lab</a>
    </div>
</body>
</html>
