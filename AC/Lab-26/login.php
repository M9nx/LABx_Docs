<?php
/**
 * Lab 26: Login Page
 * Pressable-style API Applications Platform
 */

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        logActivity($pdo, $user['id'], 'login', 'user', $user['id'], 'User logged in');
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pressable API Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0e0;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        .logo h1 {
            font-size: 1.75rem;
            color: #00b4d8;
            margin-bottom: 0.25rem;
        }
        .logo p { color: #666; font-size: 0.9rem; }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 180, 216, 0.2);
            border-radius: 16px;
            padding: 2rem;
        }
        .login-card h2 {
            color: #fff;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.25rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #aaa;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #00b4d8;
            box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.1);
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 180, 216, 0.3);
        }
        .test-accounts {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(0, 180, 216, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(0, 180, 216, 0.2);
        }
        .test-accounts h4 {
            color: #00b4d8;
            margin-bottom: 0.75rem;
            font-size: 0.85rem;
        }
        .account-btn {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 20px;
            color: #00b4d8;
            font-size: 0.8rem;
            cursor: pointer;
            margin: 0.2rem;
            transition: all 0.3s;
        }
        .account-btn:hover {
            background: rgba(0, 180, 216, 0.2);
        }
        .account-btn.victim {
            border-color: rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .account-btn.victim:hover {
            background: rgba(255, 68, 68, 0.2);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: #00b4d8; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">⚡</div>
            <h1>Pressable</h1>
            <p>Managed WordPress Hosting</p>
        </div>
        
        <div class="login-card">
            <h2>Sign in to your account</h2>
            
            <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
            
            <div class="test-accounts">
                <h4>🧪 Test Accounts (for lab)</h4>
                <span class="account-btn" onclick="fillCredentials('attacker', 'attacker123')">attacker</span>
                <span class="account-btn victim" onclick="fillCredentials('victim', 'victim123')">victim</span>
                <span class="account-btn victim" onclick="fillCredentials('sarah', 'sarah123')">sarah</span>
                <span class="account-btn victim" onclick="fillCredentials('mike', 'mike123')">mike</span>
                <span class="account-btn victim" onclick="fillCredentials('admin', 'admin123')">admin</span>
            </div>
        </div>
        
        <a href="../index.php" class="back-link">← Back to All Labs</a>
    </div>
    
    <script>
        function fillCredentials(username, password) {
            document.querySelector('input[name="username"]').value = username;
            document.querySelector('input[name="password"]').value = password;
        }
    </script>
</body>
</html>
