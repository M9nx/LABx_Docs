<?php
/**
 * Lab 28: Login Page
 * MTN Developers Portal
 */

require_once 'config.php';

$error = '';
$success = '';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } elseif ($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['lab28_user_id'] = $user['user_id'];
            $_SESSION['lab28_username'] = $user['username'];
            $_SESSION['lab28_logged_in'] = true;
            
            // Update last login
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?")->execute([$user['user_id']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Database connection failed. Please run the database setup first.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MTN Developers Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a0a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        .logo-text span {
            font-weight: normal;
            opacity: 0.7;
        }
        .nav-links a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
            opacity: 0.8;
        }
        .nav-links a:hover { opacity: 1; }
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
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 16px;
            padding: 2.5rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #ffcc00;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #888;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ffcc00;
            box-shadow: 0 0 0 3px rgba(255, 204, 0, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 8px;
            color: #000;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 204, 0, 0.4);
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
            padding: 0.875rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            text-align: center;
        }
        .credentials-box {
            margin-top: 2rem;
            padding: 1.25rem;
            background: rgba(255, 204, 0, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 10px;
        }
        .credentials-box h3 {
            color: #ffcc00;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
        }
        .credential-item:last-child { border-bottom: none; }
        .credential-item .role {
            color: #888;
        }
        .credential-item code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #00ff88;
            font-family: 'Consolas', monospace;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover { color: #ffcc00; }
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
        </nav>
    </header>

    <main class="main-content">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>🔐 Developer Login</h1>
                    <p>Sign in to manage your teams and API access</p>
                </div>

                <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter your username"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn-login">Sign In →</button>
                </form>

                <div class="credentials-box">
                    <h3>🧪 Test Accounts (POC Scenario)</h3>
                    <div class="credential-item">
                        <span class="role">Account A (Attacker):</span>
                        <span><code>attacker</code> / <code>attacker123</code></span>
                    </div>
                    <div class="credential-item">
                        <span class="role">Account B (Bob - Team Owner):</span>
                        <span><code>bob_dev</code> / <code>bob123</code></span>
                    </div>
                    <div class="credential-item">
                        <span class="role">Account C (Carol - Victim):</span>
                        <span><code>carol_admin</code> / <code>carol123</code></span>
                    </div>
                </div>
            </div>

            <a href="index.php" class="back-link">← Return to Lab Description</a>
        </div>
    </main>
</body>
</html>
