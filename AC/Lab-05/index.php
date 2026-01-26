<?php
session_start();
require_once '../progress.php';
$isSolved = isLabSolved(5);
require_once 'config.php';

// Check if carlos's API key has been submitted
$labSolved = false;
$carlosApiKey = 'sk-carlos-a1b2c3d4e5f6g7h8-targetkey';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_key'])) {
    $submittedKey = trim($_POST['api_key'] ?? '');
    if ($submittedKey === $carlosApiKey) {
        $labSolved = true;
        $_SESSION['lab_solved'] = true;
        markLabSolved(5);
    }
}

if (isset($_SESSION['lab_solved']) && $_SESSION['lab_solved']) {
    $labSolved = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDORLab - User Account System</title>
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
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
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
        .lab-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .lab-description {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        .credentials-box {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .credentials-box h3 {
            color: #00ff00;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .credentials-box code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            color: #00ff00;
            font-family: 'Consolas', monospace;
        }
        .hint-box {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .hint-box h3 {
            color: #00ffff;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .hint-box p {
            color: #aadddd;
            font-size: 0.95rem;
        }
        .submit-box {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .submit-box h3 {
            color: #ffa500;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .submit-box form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .submit-box input[type="text"] {
            flex: 1;
            min-width: 250px;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            color: #fff;
            font-family: 'Consolas', monospace;
        }
        .submit-box input[type="text"]:focus {
            outline: none;
            border-color: #ffa500;
        }
        .submit-box button {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #ffa500, #cc8400);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .submit-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.4);
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 1rem;
            margin-top: 0.5rem;
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
        .status-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .status-box.solved {
            background: rgba(0, 255, 0, 0.1);
            border-color: rgba(0, 255, 0, 0.3);
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ff4444;
        }
        .status-box.solved .status-indicator {
            background: #00ff00;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            border-color: rgba(255, 68, 68, 0.4);
            transform: translateY(-3px);
        }
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .feature-title {
            color: #ff6666;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .feature-desc {
            color: #999;
            font-size: 0.9rem;
        }
        .user-status {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    .solved-banner { background: rgba(0, 255, 0, 0.1); border: 1px solid rgba(0, 255, 0, 0.3); border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; text-align: center; } .solved-banner h3 { color: #00ff00; margin-bottom: 0.5rem; }
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
                    <span class="user-status">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="lab-card">
            <span class="lab-badge">ACCESS CONTROL LAB</span>
            <h1 class="lab-title">Lab 5: User ID Controlled by Request Parameter</h1>
            
            <p class="lab-description">
                This lab has a <strong>horizontal privilege escalation vulnerability</strong> on the user account page. 
                The application uses a user-controlled parameter in the URL to determine which user's profile to display, 
                without verifying that the logged-in user is authorized to view that profile.
            </p>

            <div class="credentials-box">
                <h3>🔑 Your Credentials</h3>
                <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
            </div>

            <div class="hint-box">
                <h3>💡 Goal</h3>
                <p>
                    Obtain the <strong>API key</strong> for the user <strong>carlos</strong> and submit it below to solve the lab.
                    Pay attention to how the profile page URL is structured when you view your own account.
                </p>
            </div>

            <div class="submit-box">
                <h3>🏁 Submit Carlos's API Key</h3>
                <form method="POST">
                    <input type="text" name="api_key" placeholder="Enter carlos's API key here..." 
                           value="<?php echo $labSolved ? $carlosApiKey : ''; ?>" <?php echo $labSolved ? 'readonly' : ''; ?>>
                    <button type="submit" name="submit_key" <?php echo $labSolved ? 'disabled' : ''; ?>>
                        <?php echo $labSolved ? '✓ Verified' : 'Submit Key'; ?>
                    </button>
                </form>
            </div>

            <div class="status-box <?php echo $labSolved ? 'solved' : ''; ?>">
                <div class="status-indicator"></div>
                <span>
                    <?php if ($labSolved): ?>
                        🎉 <strong>Congratulations!</strong> Lab solved - You obtained carlos's API key!
                    <?php else: ?>
                        Lab Status: <strong>Unsolved</strong> - Find and submit carlos's API key
                    <?php endif; ?>
                </span>
            </div>

            <div style="margin-top: 2rem;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['username']; ?>" class="btn btn-primary">Go to My Account</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login to Start</a>
                <?php endif; ?>
                <a href="docs.php" class="btn btn-secondary">Read Documentation</a>
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">👤</div>
                <h3 class="feature-title">User Profiles</h3>
                <p class="feature-desc">Each user has a profile page with sensitive information including their API key.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔗</div>
                <h3 class="feature-title">URL Parameters</h3>
                <p class="feature-desc">Profile pages are accessed via URL parameters that identify which user to display.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚨</div>
                <h3 class="feature-title">IDOR Vulnerability</h3>
                <p class="feature-desc">Insecure Direct Object Reference allows accessing other users' data by changing the ID.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚠️</div>
                <h3 class="feature-title">Vulnerable Design</h3>
                <p class="feature-desc">This lab contains intentional security flaws for educational purposes.</p>
            </div>
        </div>
    </div>
</body>
</html>



