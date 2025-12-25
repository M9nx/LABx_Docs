<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Password Disclosure</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
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
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-nav:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .lab-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2.2rem;
            color: #ff4444;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .lab-header p {
            color: #888;
            font-size: 1.1rem;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .content-card h2 {
            color: #ff4444;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .content-card p {
            color: #b0b0b0;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .step-list {
            list-style: none;
            counter-reset: step-counter;
        }
        .step-list li {
            counter-increment: step-counter;
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.2rem;
            color: #b0b0b0;
            line-height: 1.6;
        }
        .step-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .info-box {
            background: rgba(68, 68, 255, 0.1);
            border: 1px solid rgba(68, 68, 255, 0.3);
            border-radius: 10px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .info-box h4 {
            color: #6666ff;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-box p {
            color: #a0a0ff;
            margin: 0;
        }
        .warning-box {
            background: rgba(255, 200, 68, 0.1);
            border: 1px solid rgba(255, 200, 68, 0.3);
            border-radius: 10px;
            padding: 1.2rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #ffcc44;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .warning-box p {
            color: #ffdd88;
            margin: 0;
        }
        .code-inline {
            background: rgba(255, 68, 68, 0.1);
            color: #ff6666;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .tech-tag {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.2);
            color: #ff6666;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔑 PassLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-nav">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo htmlspecialchars($_SESSION['username']); ?>">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-header">
            <span class="lab-badge">🔴 ACCESS CONTROL LAB 8</span>
            <h1>User ID Controlled by Request Parameter with Password Disclosure</h1>
            <p>Exploit an IDOR vulnerability to access administrator credentials</p>
        </div>

        <div class="content-card">
            <h2>📋 Lab Description</h2>
            <p>
                This lab has a user account page that contains the current user's existing password, 
                pre-filled in a masked input field. The page fetches user data based on a user-controllable 
                parameter in the URL, making it vulnerable to Insecure Direct Object Reference (IDOR).
            </p>
            <p>
                To solve the lab, retrieve the administrator's password by exploiting this vulnerability, 
                then use it to log in and delete the user <span class="code-inline">carlos</span>.
            </p>

            <div class="info-box">
                <h4>💡 Credentials</h4>
                <p>You can log in to your own account using: <span class="code-inline">wiener:peter</span></p>
            </div>
        </div>

        <div class="content-card">
            <h2>🎯 Objective</h2>
            <ol class="step-list">
                <li>Log in with the provided credentials <span class="code-inline">wiener:peter</span></li>
                <li>Navigate to your account page and examine the URL structure</li>
                <li>Notice that the URL contains a parameter identifying the user (e.g., <span class="code-inline">?id=wiener</span>)</li>
                <li>Inspect the HTML source of the page to find your password in a hidden input field</li>
                <li>Modify the <span class="code-inline">id</span> parameter to <span class="code-inline">administrator</span></li>
                <li>View the page source to retrieve the administrator's password</li>
                <li>Log out and log back in as administrator using the discovered password</li>
                <li>Access the admin panel and delete the user <span class="code-inline">carlos</span></li>
            </ol>
        </div>

        <div class="content-card">
            <h2>🔍 Vulnerability Details</h2>
            <p>
                The vulnerability exists because the application pre-fills the password field with the user's 
                actual password value. While the field displays masked characters (dots) on screen, the actual 
                password is stored in the HTML <span class="code-inline">value</span> attribute and can be viewed 
                by inspecting the page source.
            </p>
            <p>
                Combined with the IDOR vulnerability (accepting any user ID via URL parameter without proper 
                authorization checks), this allows an attacker to view any user's password by simply changing 
                the ID parameter.
            </p>

            <div class="warning-box">
                <h4>⚠️ Security Impact</h4>
                <p>
                    This vulnerability allows complete account takeover by exposing user credentials. 
                    Never store or display passwords in HTML, even in masked input fields.
                </p>
            </div>
        </div>

        <div class="content-card">
            <h2>🛠️ Technologies Used</h2>
            <div class="tech-list">
                <span class="tech-tag">PHP</span>
                <span class="tech-tag">MySQL</span>
                <span class="tech-tag">Sessions</span>
                <span class="tech-tag">HTML Forms</span>
                <span class="tech-tag">IDOR</span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="index.php" class="btn-primary">🚀 Start Lab</a>
            <a href="docs.php" class="btn-secondary">📚 Full Documentation</a>
            <a href="../index.php" class="btn-secondary">← Back to Labs</a>
        </div>
    </div>
</body>
</html>