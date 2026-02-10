<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Lab Description Page
 */

$labInfo = [
    'number' => 2,
    'title' => 'Modifying Serialized Data Types',
    'difficulty' => 'Practitioner',
    'category' => 'Insecure Deserialization',
    'vulnerability' => 'PHP Type Juggling'
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
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%); 
            min-height: 100vh; 
            color: #e0e0e0; 
        }
        .header { 
            background: rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(249,115,22,0.3); 
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
            color: #f97316; 
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
        .nav-links a:hover { color: #f97316; }
        .container { max-width: 900px; margin: 0 auto; padding: 3rem 2rem; }
        .header-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        h1 { font-size: 2.5rem; margin-bottom: 1.5rem; color: #f97316; }
        .lab-card { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(249,115,22,0.2); 
            border-radius: 15px; 
            padding: 2rem; 
            margin-bottom: 2rem; 
            backdrop-filter: blur(10px); 
        }
        .lab-card h2 {
            color: #fb923c;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .lab-card p { color: #ccc; line-height: 1.7; margin-bottom: 1rem; }
        .lab-card p:last-child { margin-bottom: 0; }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .meta-item {
            background: rgba(249, 115, 22, 0.1);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }
        .meta-item label { color: #888; font-size: 0.85rem; display: block; margin-bottom: 0.25rem; }
        .meta-item span { color: #fb923c; font-weight: 600; }
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
            background: linear-gradient(135deg, #f97316, #ea580c);
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
            background: rgba(249,115,22,0.2);
            color: #fb923c;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        pre {
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 10px;
            overflow-x: auto;
            margin: 1rem 0;
        }
        pre code { background: none; padding: 0; color: #fb923c; }
        .btn-primary { 
            display: inline-block; 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 20px rgba(249,115,22,0.4); 
        }
        .back-link {
            display: inline-block;
            color: #f97316;
            text-decoration: none;
            margin-bottom: 2rem;
        }
        .back-link:hover { text-decoration: underline; }
        .hint-box { 
            background: rgba(0,255,255,0.1); 
            border: 1px solid rgba(0,255,255,0.3); 
            border-left: 3px solid #00ffff;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        .hint-box strong { color: #00ffff; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">TypeJuggle Shop</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Lab Home</a>
        
        <span class="header-badge">Lab <?= $labInfo['number'] ?> - <?= $labInfo['difficulty'] ?></span>
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
                <span><?= $labInfo['vulnerability'] ?></span>
            </div>
            <div class="meta-item">
                <label>Time</label>
                <span>15-20 min</span>
            </div>
        </div>

        <div class="lab-card">
            <h2>Lab Overview</h2>
            <p>
                This lab uses a serialization-based session mechanism and is vulnerable to authentication bypass 
                as a result. The application deserializes session data from a cookie and uses <strong>loose comparison</strong> 
                (<code>==</code>) to validate the access token.
            </p>
            <p>
                To solve the lab, edit the serialized object in the session cookie to access the administrator 
                account. Then, delete the user <code>carlos</code>.
            </p>
        </div>

        <div class="lab-card">
            <h2>Test Credentials</h2>
            <p>You can log in to your own account using the following credentials:</p>
            <pre><code>Username: wiener
Password: peter</code></pre>
        </div>

        <div class="lab-card">
            <h2>Attack Hint</h2>
            <p>
                To access another user's account, you will need to exploit a quirk in how PHP compares data 
                of different types.
            </p>
            <div class="hint-box">
                <strong>PHP Type Juggling:</strong> When comparing a string to an integer using <code>==</code>, 
                PHP converts the string to an integer. Non-numeric strings convert to <code>0</code>.
                <br><br>
                Therefore: <code>"any_string" == 0</code> evaluates to <code>TRUE</code>
            </div>
        </div>

        <div class="lab-card">
            <h2>Attack Steps</h2>
            <ol class="steps-list">
                <li>Log in using the credentials <code>wiener:peter</code></li>
                <li>Examine the session cookie (it contains a serialized PHP object)</li>
                <li>Decode the Base64 cookie to see the serialized structure</li>
                <li>Modify the username to <code>administrator</code> (update string length)</li>
                <li>Change the <code>access_token</code> from a string to boolean <code>true</code></li>
                <li>Update the type indicator from <code>s:</code> to <code>b:</code></li>
                <li>Re-encode the cookie and replace it</li>
                <li>Access the admin panel and delete <code>carlos</code></li>
            </ol>
        </div>

        <div class="lab-card">
            <h2>Expected Payload</h2>
            <p>The modified serialized object should look like:</p>
            <pre><code>O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";b:1;}</code></pre>
            <p><strong>Note:</strong> <code>b:1</code> = boolean <code>true</code> (works in PHP 7 & 8)</p>
            <p>Key changes:</p>
            <ul style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                <li><code>s:6:"wiener"</code> → <code>s:13:"administrator"</code> (update length to 13)</li>
                <li><code>s:64:"token_hash..."</code> → <code>b:1</code> (change type to boolean true)</li>
            </ul>
        </div>

        <div class="lab-card">
            <h2>Learning Outcomes</h2>
            <ul style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                <li>Understanding PHP type juggling vulnerabilities</li>
                <li>Manipulating serialized data types</li>
                <li>Bypassing authentication through type confusion</li>
                <li>The importance of strict comparison operators</li>
            </ul>
        </div>

        <a href="login.php" class="btn-primary">Start the Lab</a>
    </div>
</body>
</html>
