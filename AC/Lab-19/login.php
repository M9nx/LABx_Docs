<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['lab19_user_id'] = $user['id'];
            $_SESSION['lab19_username'] = $user['username'];
            $_SESSION['lab19_display_name'] = $user['display_name'];
            $_SESSION['lab19_role'] = $user['role'];
            $_SESSION['lab19_avatar_color'] = $user['avatar_color'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } catch (PDOException $e) {
        $error = 'Database error. Please run setup_db.php first.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ProjectHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
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
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #a5b4fc; text-decoration: none; }
        .nav-links a:hover { color: #c7d2fe; }
        .main {
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
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header .icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        .login-header h1 { color: #e0e0e0; font-size: 1.5rem; margin-bottom: 0.5rem; }
        .login-header p { color: #64748b; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            color: #a5b4fc;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s;
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
            transition: all 0.3s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4); }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .credentials-panel {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        .credentials-panel h3 {
            color: #a5b4fc;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .cred-row:last-child { border: none; }
        .cred-row code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #c7d2fe;
        }
        .role-tag {
            font-size: 0.65rem;
            padding: 0.1rem 0.35rem;
            border-radius: 3px;
            margin-left: 0.4rem;
        }
        .role-tag.victim { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .role-tag.attacker { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .role-tag.admin { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .saved-ids-hint {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #fcd34d;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="icon">🔐</div>
                    <h1>Sign in to ProjectHub</h1>
                    <p>Access your creative portfolio</p>
                </div>

                <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required placeholder="Enter your username">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn-login">Sign In</button>
                </form>

                <div class="credentials-panel">
                    <h3>🔑 Lab Credentials</h3>
                    <div class="cred-row">
                        <span><code>victim_designer</code><span class="role-tag victim">Target</span></span>
                        <code>victim123</code>
                    </div>
                    <div class="cred-row">
                        <span><code>attacker_user</code><span class="role-tag attacker">Use This</span></span>
                        <code>attacker123</code>
                    </div>
                    <div class="cred-row">
                        <span><code>admin</code><span class="role-tag admin">Admin</span></span>
                        <code>admin123</code>
                    </div>
                </div>

                <div class="saved-ids-hint">
                    💡 <strong>Victim's saved project IDs:</strong> 101, 102, 103, 104, 105
                </div>
            </div>
        </div>
    </main>
</body>
</html>
