<?php
session_start();
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
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
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
    <title>Login - Phabricator Slowvote</title>
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
            max-width: 450px;
            padding: 2rem;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            display: inline-block;
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .logo h1 {
            color: #9370DB;
            font-size: 1.5rem;
        }
        .logo p {
            color: #666;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 10px;
            color: #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #9370DB;
            box-shadow: 0 0 15px rgba(106, 90, 205, 0.3);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(106, 90, 205, 0.4);
        }
        .error-msg {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.4);
            color: #ff6666;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(106, 90, 205, 0.2);
        }
        .credentials h3 {
            color: #9370DB;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .cred-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .cred-item {
            background: rgba(106, 90, 205, 0.1);
            border: 1px solid rgba(106, 90, 205, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.8rem;
        }
        .cred-item .user { color: #9370DB; font-weight: 600; }
        .cred-item .pass { color: #888; }
        .cred-item .role {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 8px;
            font-size: 0.7rem;
            margin-top: 0.3rem;
        }
        .cred-item .role.creator { background: rgba(0,255,0,0.2); color: #00ff00; }
        .cred-item .role.no-access { background: rgba(255,68,68,0.2); color: #ff6666; }
        .cred-item .role.has-access { background: rgba(0,200,255,0.2); color: #00ccff; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #888;
            text-decoration: none;
        }
        .back-link:hover { color: #9370DB; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <div class="logo-icon">P</div>
                <h1>Phabricator</h1>
                <p>Slowvote System</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
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

            <div class="credentials">
                <h3>🔑 Test Credentials</h3>
                <div class="cred-grid">
                    <div class="cred-item">
                        <div class="user">alice</div>
                        <div class="pass">alice123</div>
                        <span class="role creator">User A - Creator</span>
                    </div>
                    <div class="cred-item">
                        <div class="user">bob</div>
                        <div class="pass">bob123</div>
                        <span class="role no-access">User B - No Access</span>
                    </div>
                    <div class="cred-item">
                        <div class="user">charlie</div>
                        <div class="pass">charlie123</div>
                        <span class="role has-access">User C - Has Access</span>
                    </div>
                    <div class="cred-item">
                        <div class="user">admin</div>
                        <div class="pass">admin123</div>
                        <span class="role">Administrator</span>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="index.php" class="back-link">← Back to Lab Home</a>
    </div>
</body>
</html>
