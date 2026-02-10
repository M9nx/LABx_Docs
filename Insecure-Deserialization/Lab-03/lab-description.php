<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Lab Description Page
 */
require_once '../progress.php';
$isSolved = isLabSolved(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Lab 03</title>
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
        .lab-card { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(249,115,22,0.2); 
            border-radius: 15px; 
            padding: 2.5rem; 
            margin-bottom: 2rem; 
            backdrop-filter: blur(10px); 
        }
        .lab-badge { 
            display: inline-block; 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white; 
            padding: 0.4rem 1rem; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            margin-bottom: 1rem; 
        }
        .lab-card h1 { 
            font-size: 1.75rem; 
            color: #f97316; 
            margin-bottom: 1.5rem; 
        }
        .lab-card h2 { 
            font-size: 1.25rem; 
            color: #fb923c; 
            margin: 1.5rem 0 1rem; 
        }
        .lab-card p { 
            color: #ccc; 
            line-height: 1.8; 
            margin-bottom: 1rem; 
        }
        .lab-card code { 
            background: rgba(249,115,22,0.2); 
            padding: 0.2rem 0.5rem; 
            border-radius: 4px; 
            color: #fb923c; 
        }
        .lab-card pre {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(249,115,22,0.3);
            border-radius: 10px;
            padding: 1.25rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .lab-card pre code {
            background: none;
            padding: 0;
            color: #fb923c;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
        }
        .lab-card ol, .lab-card ul { 
            margin-left: 1.5rem; 
            color: #ccc; 
            line-height: 2; 
        }
        .hint-box { 
            background: rgba(0, 255, 255, 0.05); 
            border: 1px solid rgba(0, 255, 255, 0.2); 
            padding: 1.25rem; 
            border-radius: 10px; 
            margin: 1.5rem 0; 
        }
        .hint-box h3 { color: #00ffff; margin-bottom: 0.5rem; font-size: 1rem; }
        .hint-box p { color: #a0e0e0; font-size: 0.95rem; margin: 0; }
        .target-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 1.25rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }
        .target-box h3 { color: #ef4444; margin-bottom: 0.5rem; font-size: 1rem; }
        .target-box code { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .status-box { 
            padding: 1.5rem; 
            border-radius: 10px; 
            margin-bottom: 1.5rem;
            text-align: center; 
        }
        .status-box.solved { 
            background: rgba(34, 197, 94, 0.2); 
            border: 1px solid rgba(34, 197, 94, 0.4); 
        }
        .status-box.unsolved { 
            background: rgba(239, 68, 68, 0.1); 
            border: 1px solid rgba(239, 68, 68, 0.3); 
        }
        .status-box h3 { font-size: 1.2rem; margin-bottom: 0.5rem; }
        .status-box.solved h3 { color: #22c55e; }
        .status-box.unsolved h3 { color: #ef4444; }
        .btn-primary { 
            display: inline-block; 
            background: linear-gradient(135deg, #f97316, #ea580c); 
            color: white; 
            padding: 0.8rem 1.5rem; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: transform 0.3s; 
        }
        .btn-primary:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">AvatarVault</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="lab-description.php" style="color: #f97316;">Lab Info</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-card">
            <span class="lab-badge">PRACTITIONER</span>
            <h1>Lab 03: Using Application Functionality</h1>
            
            <div class="status-box <?= $isSolved ? 'solved' : 'unsolved' ?>">
                <?php if ($isSolved): ?>
                    <h3>✓ Lab Solved</h3>
                    <p style="color: #86efac;">You've successfully completed this lab!</p>
                <?php else: ?>
                    <h3>Lab Not Solved</h3>
                    <p style="color: #fca5a5;">Complete the objective below to solve the lab.</p>
                <?php endif; ?>
            </div>
            
            <h2>Objective</h2>
            <p>
                This lab uses a serialization-based session mechanism. A certain feature invokes 
                a dangerous method on data provided in a serialized object. To solve the lab, 
                edit the serialized object in the session cookie and use it to delete the 
                <code>morale.txt</code> file from Carlos's home directory.
            </p>
            
            <div class="target-box">
                <h3>Target</h3>
                <p>Delete file: <code>[LAB_PATH]/home/carlos/morale.txt</code></p>
                <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #aaa;">The full path is shown in your decoded session cookie. Replace your avatar path with the path to morale.txt.</p>
            </div>
            
            <h2>Credentials</h2>
            <p>
                <strong>Primary:</strong> <code>wiener</code> : <code>peter</code><br>
                <strong>Backup:</strong> <code>gregg</code> : <code>rosebud</code>
            </p>
            
            <h2>Attack Steps</h2>
            <ol>
                <li>Log in with <code>wiener:peter</code></li>
                <li>Go to "My Account" and observe the session cookie structure</li>
                <li>Notice the <code>avatar_link</code> attribute contains your avatar file path</li>
                <li>Notice the "Delete Account" feature deletes your avatar file</li>
                <li>Modify the cookie to change <code>avatar_link</code> to the absolute path of morale.txt (same base path as your avatar, but ending in <code>/home/carlos/morale.txt</code>)</li>
                <li>Send the delete request with the modified cookie</li>
                <li>Your account is deleted, AND Carlos's morale.txt is also deleted!</li>
            </ol>

            <div class="hint-box">
                <h3>Payload Hint</h3>
                <p>
                    Your existing cookie shows the base path (e.g., <code>C:\xampp\htdocs\...\Lab-03</code>).<br>
                    Replace <code>/home/wiener/avatar.jpg</code> with <code>/home/carlos/morale.txt</code>.
                </p>
            </div>
            
            <pre><code># Example: If your avatar path is:
C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/wiener/avatar.jpg

# Change it to:
C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/carlos/morale.txt</code></pre>
            
            <p>
                <strong>Important:</strong> Update the string length indicator (<code>s:XX:</code>) to match your new path length.
                Count the total characters in your modified path.
            </p>

            <h2>Learning Outcomes</h2>
            <ul>
                <li>Understanding how serialized data can control application behavior</li>
                <li>Exploiting file handling functions via deserialized paths</li>
                <li>Using legitimate application features for unintended purposes</li>
                <li>The dangers of trusting client-side data in sensitive operations</li>
            </ul>

            <a href="login.php" class="btn-primary">Start the Lab</a>
        </div>
    </div>
</body>
</html>
