<?php
/**
 * Lab 27: Login Page
 * IDOR in Stats API Endpoint - Exness-style Trading Platform
 */

require_once 'config.php';

$error = '';
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($pdo && $username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['pa_id'] = $user['pa_id'];
            $_SESSION['full_name'] = $user['full_name'];
            
            logActivity($pdo, $user['id'], 'login', 'User logged in successfully');
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            margin-left: 1.5rem;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ffd700; }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 16px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #fff;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #888;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            border: none;
            border-radius: 8px;
            color: #000;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials-box {
            margin-top: 2rem;
            background: rgba(255, 215, 0, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
            padding: 1.25rem;
        }
        .credentials-box h3 {
            color: #ffd700;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            color: #aaa;
            font-size: 0.85rem;
        }
        .credential-item code {
            color: #00ff88;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-family: monospace;
        }
        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.85rem;
        }
        .footer-text a {
            color: #ffd700;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">📈</span>
                Exness PA
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your Personal Area</p>
                </div>

                <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">Sign In</button>
                </form>

                <div class="credentials-box">
                    <h3>🔑 Lab Test Accounts</h3>
                    <div class="credential-item">
                        <span>Attacker:</span>
                        <code>attacker / attacker123</code>
                    </div>
                    <div class="credential-item">
                        <span>Victim (High Value):</span>
                        <code>victim / victim123</code>
                    </div>
                    <div class="credential-item">
                        <span>Whale (Massive):</span>
                        <code>whale / whale123</code>
                    </div>
                </div>

                <p class="footer-text">
                    <a href="lab-description.php">Lab Description</a> • 
                    <a href="docs.php">Documentation</a>
                </p>
            </div>
        </div>
    </main>
</body>
</html>
