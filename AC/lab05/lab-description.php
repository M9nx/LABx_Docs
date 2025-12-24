<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - IDORLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        .nav-links a:hover {
            color: #ff4444;
        }
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
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
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
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
        .btn-info {
            background: linear-gradient(135deg, #00aaff, #0077cc);
            color: white;
        }
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 170, 255, 0.4);
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .lab-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .page-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 1.5rem;
        }
        .section {
            margin-bottom: 2.5rem;
        }
        .section:last-child {
            margin-bottom: 0;
        }
        .section-title {
            color: #ff6666;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section p:last-child {
            margin-bottom: 0;
        }
        .info-box {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .info-box h4 {
            color: #00ff00;
            margin-bottom: 0.8rem;
        }
        .info-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            color: #00ff00;
            font-family: 'Consolas', monospace;
        }
        .warning-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .warning-box h4 {
            color: #ffa500;
            margin-bottom: 0.8rem;
        }
        .step-list {
            list-style: none;
            counter-reset: step;
        }
        .step-list li {
            counter-increment: step;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.1);
            border-radius: 8px;
            margin-bottom: 0.8rem;
            position: relative;
            padding-left: 3.5rem;
        }
        .step-list li::before {
            content: counter(step);
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .step-list li strong {
            color: #ff6666;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .code-block code {
            font-family: 'Consolas', 'Monaco', monospace;
            color: #ff6666;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔑 IDORLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['username']; ?>">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="content-card">
            <span class="lab-badge">HORIZONTAL PRIVILEGE ESCALATION</span>
            <h1 class="page-title">Lab 5: User ID Controlled by Request Parameter</h1>

            <div class="section">
                <h2 class="section-title">📋 Lab Overview</h2>
                <p>
                    This lab demonstrates a <strong>horizontal privilege escalation</strong> vulnerability 
                    where the application uses a user-controllable parameter to access user account pages.
                </p>
                <p>
                    The profile page is accessed via a URL parameter that identifies the user. However, 
                    the application fails to verify whether the currently logged-in user is authorized 
                    to view the requested profile.
                </p>
            </div>

            <div class="section">
                <h2 class="section-title">🎯 Objective</h2>
                <p>
                    Obtain the <strong>API key</strong> for the user <strong>carlos</strong> by exploiting 
                    the Insecure Direct Object Reference (IDOR) vulnerability in the profile page.
                </p>
                <div class="info-box">
                    <h4>🔑 Provided Credentials</h4>
                    <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">🔍 Vulnerability Type</h2>
                <p>
                    <strong>IDOR (Insecure Direct Object Reference)</strong> is a type of access control 
                    vulnerability where the application uses user-controllable input to directly access 
                    objects (such as database records or files).
                </p>
                <p>
                    When combined with access control failures, IDOR allows attackers to access resources 
                    belonging to other users by simply modifying the identifier in the request.
                </p>
            </div>

            <div class="section">
                <h2 class="section-title">📝 Steps to Solve</h2>
                <ol class="step-list">
                    <li><strong>Login</strong> to the application with your credentials (wiener:peter)</li>
                    <li><strong>Navigate</strong> to your account/profile page after logging in</li>
                    <li><strong>Observe</strong> the URL structure - notice the <code>id</code> parameter</li>
                    <li><strong>Modify</strong> the <code>id</code> parameter to target another user</li>
                    <li><strong>Access</strong> carlos's profile and retrieve his API key</li>
                    <li><strong>Submit</strong> the API key on the home page to complete the lab</li>
                </ol>
            </div>

            <div class="section">
                <h2 class="section-title">💡 Hint</h2>
                <div class="warning-box">
                    <h4>URL Structure</h4>
                    <p>Pay close attention to the URL when viewing your profile:</p>
                    <div class="code-block">
                        <code>profile.php?id=wiener</code>
                    </div>
                    <p>What happens if you change <code>wiener</code> to <code>carlos</code>?</p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">⚠️ Real-World Impact</h2>
                <p>
                    IDOR vulnerabilities are extremely common and can have severe impacts:
                </p>
                <ul style="margin-left: 1.5rem; color: #ccc; line-height: 2;">
                    <li>Unauthorized access to other users' personal data</li>
                    <li>Exposure of API keys, tokens, and credentials</li>
                    <li>Data theft and privacy violations</li>
                    <li>Account takeover in some cases</li>
                    <li>GDPR and compliance violations</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="setup_db.php" target="_blank" class="btn btn-primary">
                    🗄️ Setup Database
                </a>
                <a href="index.php" class="btn btn-info">
                    🚀 Access Lab
                </a>
                <a href="docs.php" class="btn btn-secondary">
                    📚 View Documentation
                </a>
            </div>
        </div>
    </div>
</body>
</html>
