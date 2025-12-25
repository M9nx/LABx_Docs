<?php
// Lab 22: Login Page
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
                $_SESSION['access_token'] = $user['access_token'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please run setup_db.php first.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RideKea | Lab 22</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #164e63 100%);
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .nav-top {
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-top a {
            color: #22d3ee;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-top a:hover { background: rgba(6, 182, 212, 0.1); }
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header .logo {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .login-header h1 {
            color: #22d3ee;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #64748b;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
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
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3);
        }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(6, 182, 212, 0.2);
        }
        .credentials-section h4 {
            color: #10b981;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }
        .cred-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        .cred-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .cred-item:hover {
            border-color: #06b6d4;
            background: rgba(6, 182, 212, 0.1);
        }
        .cred-item.victim { border-color: rgba(239, 68, 68, 0.3); }
        .cred-item.attacker { border-color: rgba(245, 158, 11, 0.3); }
        .cred-item .role {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.25rem;
        }
        .cred-item.victim .role { color: #f87171; }
        .cred-item.attacker .role { color: #f59e0b; }
        .cred-item .user {
            color: #e2e8f0;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .cred-item .pass {
            color: #64748b;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <nav class="nav-top">
        <a href="index.php">← Back to Lab</a>
        <a href="lab-description.php">📖 Lab Guide</a>
    </nav>

    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">🚗</div>
                <h1>RideKea Login</h1>
                <p>Sign in to your passenger account</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn-login">🔑 Sign In</button>
            </form>

            <div class="credentials-section">
                <h4>🧪 Test Credentials (Click to fill)</h4>
                <div class="cred-grid">
                    <div class="cred-item victim" onclick="fillCredentials('victim_user', 'victim123')">
                        <div class="role">👤 Victim</div>
                        <div class="user">victim_user</div>
                        <div class="pass">victim123</div>
                    </div>
                    <div class="cred-item attacker" onclick="fillCredentials('attacker_user', 'attacker123')">
                        <div class="role">😈 Attacker</div>
                        <div class="user">attacker_user</div>
                        <div class="pass">attacker123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
