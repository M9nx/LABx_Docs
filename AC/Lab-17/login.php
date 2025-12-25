<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - GitLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
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
        .logo svg {
            width: 60px;
            height: 60px;
        }
        .logo h1 {
            color: #fc6d26;
            font-size: 1.8rem;
            margin-top: 0.5rem;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }
        .login-box h2 {
            color: #fc6d26;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #fc6d26;
            box-shadow: 0 0 0 3px rgba(252, 109, 38, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #fc6d26, #e24329);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
        .error-message {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.5);
            color: #ff6666;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .credentials-help {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .credentials-help h4 {
            color: #fc6d26;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .cred-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
        }
        .cred-item:last-child { border-bottom: none; }
        .cred-user { color: #fc6d26; }
        .cred-pass { color: #88ff88; font-family: monospace; }
        .cred-role {
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            margin-left: 0.5rem;
        }
        .cred-role.victim { background: rgba(255, 68, 68, 0.3); color: #ff8888; }
        .cred-role.attacker { background: rgba(255, 170, 0, 0.3); color: #ffcc00; }
        .cred-role.admin { background: rgba(0, 150, 255, 0.3); color: #66ccff; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #888;
            text-decoration: none;
        }
        .back-link:hover { color: #fc6d26; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <svg viewBox="0 0 32 32" fill="none">
                <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
            </svg>
            <h1>GitLab</h1>
        </div>
        
        <div class="login-box">
            <h2>Sign In</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>
        
        <div class="credentials-help">
            <h4>🔑 Test Credentials</h4>
            <div class="cred-item">
                <span><span class="cred-user">victim01</span><span class="cred-role victim">Victim</span></span>
                <span class="cred-pass">victim123</span>
            </div>
            <div class="cred-item">
                <span><span class="cred-user">attacker01</span><span class="cred-role attacker">Attacker ⭐</span></span>
                <span class="cred-pass">attacker123</span>
            </div>
            <div class="cred-item">
                <span><span class="cred-user">admin</span><span class="cred-role admin">Admin</span></span>
                <span class="cred-pass">admin123</span>
            </div>
        </div>
        
        <a href="index.php" class="back-link">← Back to Lab Home</a>
    </div>
</body>
</html>
