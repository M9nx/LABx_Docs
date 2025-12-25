<?php
// Lab 23: Login Page
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->execute([$username, $password]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['api_token'] = $user['api_token'];
                
                logActivity($user['user_id'], 'login', 'user', $user['user_id'], 'User logged in');
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid credentials. Please try again.';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please ensure setup_db.php has been run.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TagScope | Lab 23</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.5rem; font-weight: bold; color: #a78bfa; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a78bfa;
            text-decoration: none;
            border-radius: 6px;
        }
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            max-width: 900px;
            width: 100%;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .login-card h2 { color: #a78bfa; margin-bottom: 0.5rem; }
        .login-card .subtitle { color: #64748b; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4); }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .creds-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            padding: 2rem;
        }
        .creds-card h3 { color: #818cf8; margin-bottom: 1.5rem; }
        .cred-item {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .cred-item .role {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .cred-item .role.victim { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .cred-item .role.attacker { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .cred-item .role.admin { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
        .cred-item h4 { color: #e2e8f0; margin-bottom: 0.25rem; }
        .cred-item p { color: #64748b; font-size: 0.85rem; }
        .cred-item code { color: #f59e0b; }
        .hint-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .hint-box h4 { color: #f59e0b; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .hint-box p { color: #fbbf24; font-size: 0.85rem; }
        @media (max-width: 768px) {
            .login-wrapper { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="index.php">← Back</a>
            <a href="lab-description.php">📖 Guide</a>
            <a href="docs.php">📚 Docs</a>
        </nav>
    </header>

    <div class="container">
        <div class="login-wrapper">
            <div class="login-card">
                <h2>🔐 Sign In</h2>
                <p class="subtitle">Access your asset management dashboard</p>
                
                <?php if ($error): ?>
                    <div class="error-msg">❌ <?= e($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="btn-login">Sign In →</button>
                </form>
            </div>
            
            <div class="creds-card">
                <h3>🔑 Test Credentials</h3>
                
                <div class="cred-item">
                    <span class="role victim">👤 VICTIM</span>
                    <h4>victim_org</h4>
                    <p>Password: <code>victim123</code></p>
                    <p>Has 7 private custom tags to discover</p>
                </div>
                
                <div class="cred-item">
                    <span class="role attacker">☠️ ATTACKER</span>
                    <h4>attacker_user</h4>
                    <p>Password: <code>attacker123</code></p>
                    <p>Use this account to exploit the IDOR</p>
                </div>
                
                <div class="cred-item">
                    <span class="role admin">👑 ADMIN</span>
                    <h4>admin</h4>
                    <p>Password: <code>admin123</code></p>
                </div>
                
                <div class="hint-box">
                    <h4>💡 Attack Hint</h4>
                    <p>Victim's tag IDs: 49790001 - 49790007. Encode as base64 and try adding them to your assets!</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
