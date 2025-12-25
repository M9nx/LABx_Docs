<?php
session_start();
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
        $_SESSION['user_uuid'] = $user['uuid'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
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
    <title>Login - KeyVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
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
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .nav-links a { color: #5eead4; text-decoration: none; margin-left: 1.5rem; }
        .main-content {
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
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .login-card h1 {
            text-align: center;
            margin-bottom: 0.5rem;
            color: #5eead4;
        }
        .login-card .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.3);
            color: #e0e0e0;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #14b8a6;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .btn-login:hover { transform: translateY(-2px); }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials-hint {
            background: rgba(20, 184, 166, 0.1);
            border: 1px solid rgba(20, 184, 166, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .credentials-hint h4 { color: #5eead4; margin-bottom: 1rem; font-size: 0.9rem; }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-row span { color: #94a3b8; }
        .cred-row code { color: #5eead4; }
        .cred-row.attacker {
            background: rgba(239, 68, 68, 0.1);
            margin: 0.25rem -0.5rem;
            padding: 0.5rem;
            border-radius: 6px;
        }
        .cred-row.attacker span { color: #fca5a5; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="lab-description.php">Instructions</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="login-container">
            <div class="login-card">
                <h1>🔐 Sign In</h1>
                <p class="subtitle">Access your organization's API keys</p>
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-login">Sign In →</button>
                </form>

                <div class="credentials-hint">
                    <h4>🎯 Test Credentials (TechCorp Inc)</h4>
                    <div class="cred-row">
                        <span>Owner:</span>
                        <code>victim_owner / victim123</code>
                    </div>
                    <div class="cred-row attacker">
                        <span>⚔️ Attacker (Member):</span>
                        <code>attacker_member / attacker123</code>
                    </div>
                    <div class="cred-row">
                        <span>Admin:</span>
                        <code>alice_admin / alice123</code>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
