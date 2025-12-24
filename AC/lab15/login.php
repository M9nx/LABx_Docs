<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT user_id, email, full_name, account_type FROM users WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['account_type'] = $user['account_type'];
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Please enter both email and password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MTN MobAd Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 204, 0, 0.3);
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffcc00;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: rgba(255, 204, 0, 0.1);
            color: #ffcc00;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #ffcc00;
            font-size: 1.8rem;
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
            margin-bottom: 0.5rem;
            color: #ccc;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 10px;
            color: #e0e0e0;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ffcc00;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 204, 0, 0.3);
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            color: #ff6666;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials-hint {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 204, 0, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
        }
        .credentials-hint h3 {
            color: #ffcc00;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cred-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
        }
        .cred-item:last-child { border-bottom: none; }
        .cred-item .label { color: #888; }
        .cred-item .value { color: #88ff88; font-family: monospace; }
        .cred-item.attacker .label { color: #ff6666; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">MTN</span>
                MobAd Platform
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>🔐 Sign In</h1>
                    <p>Access your MobAd advertising dashboard</p>
                </div>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="you@company.com" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-login">Sign In →</button>
                </form>
            </div>

            <div class="credentials-hint">
                <h3>🔑 Test Credentials</h3>
                <div class="cred-item attacker">
                    <span class="label">Attacker:</span>
                    <span class="value">attacker@example.com / attacker123</span>
                </div>
                <div class="cred-item">
                    <span class="label">Victim 1:</span>
                    <span class="value">victim1@mtnbusiness.com / victim123</span>
                </div>
                <div class="cred-item">
                    <span class="label">CEO:</span>
                    <span class="value">ceo@bigcorp.ng / ceo2024secure</span>
                </div>
                <div class="cred-item">
                    <span class="label">Finance:</span>
                    <span class="value">finance@acme.com.ng / finance@2024</span>
                </div>
                <div class="cred-item">
                    <span class="label">Admin:</span>
                    <span class="value">admin@mtnmobad.com / admin@mtn2024!</span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
