<?php
// Lab 21: Login Page - Stocky Application
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
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
    <title>Login - Stocky | Lab 21</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        }
        .login-info {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.1));
            padding: 3rem;
            display: flex;
            flex-direction: column;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #6366f1;
            margin-bottom: 2rem;
        }
        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .login-info h2 {
            color: #e2e8f0;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .login-info p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .credentials-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: auto;
        }
        .credentials-box h3 {
            color: #f59e0b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cred-item {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(99, 102, 241, 0.15);
            font-size: 0.85rem;
        }
        .cred-item:last-child {
            border-bottom: none;
        }
        .cred-user {
            color: #a5b4fc;
            font-weight: 500;
        }
        .cred-pass {
            color: #94a3b8;
            font-family: 'Consolas', monospace;
        }
        .tag {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .tag-victim {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }
        .tag-attacker {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
        .login-form-section {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form-section h1 {
            color: #e2e8f0;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .login-form-section .subtitle {
            color: #94a3b8;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #94a3b8;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #a5b4fc;
        }
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            .login-info {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-info">
            <div class="logo">
                <div class="logo-icon">📦</div>
                <span>Stocky</span>
            </div>
            <h2>Welcome to Stocky</h2>
            <p>Manage your inventory, track low stock variants, and customize your dashboard columns. Each store has its own settings that control which columns are visible.</p>
            
            <div class="credentials-box">
                <h3>🔑 Test Accounts</h3>
                <div class="cred-item">
                    <span class="cred-user">user_a <span class="tag tag-victim">VICTIM</span></span>
                    <span class="cred-pass">usera123</span>
                </div>
                <div class="cred-item">
                    <span class="cred-user">user_b <span class="tag tag-attacker">ATTACKER</span></span>
                    <span class="cred-pass">userb123</span>
                </div>
                <div class="cred-item">
                    <span class="cred-user">admin_stocky</span>
                    <span class="cred-pass">admin123</span>
                </div>
                <div class="cred-item">
                    <span class="cred-user">charlie</span>
                    <span class="cred-pass">charlie123</span>
                </div>
                <div class="cred-item">
                    <span class="cred-user">david</span>
                    <span class="cred-pass">david123</span>
                </div>
            </div>
        </div>
        
        <div class="login-form-section">
            <h1>Sign In</h1>
            <p class="subtitle">Access your store dashboard</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn-login">Sign In →</button>
            </form>
            
            <div class="nav-links">
                <a href="index.php">← Back to Home</a>
                <a href="lab-description.php">Lab Instructions</a>
                <a href="docs.php">Documentation</a>
            </div>
        </div>
    </div>
</body>
</html>
