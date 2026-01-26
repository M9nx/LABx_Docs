<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            logActivity($user['id'], 'login', 'users', $user['id'], 'User logged in');
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ML Model Registry</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
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
            font-size: 1.3rem;
            font-weight: bold;
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .main-content {
            flex: 1;
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
            gap: 2rem;
            align-items: start;
        }
        .login-form {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }
        .login-form h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #fc6d26;
        }
        .login-form .subtitle {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.85rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #fc6d26;
        }
        .btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #fc6d26 0%, #e24329 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
        .error-msg {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .credentials-panel {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }
        .credentials-panel h2 {
            color: #fc6d26;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cred-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .cred-card:hover {
            border-color: #fc6d26;
            transform: translateX(5px);
        }
        .cred-card.attacker { border-left: 3px solid #ff6666; }
        .cred-card.victim { border-left: 3px solid #ffaa00; }
        .cred-card.admin { border-left: 3px solid #00c853; }
        .cred-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .cred-username {
            font-weight: 600;
            color: #fff;
        }
        .cred-role {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .cred-role.attacker { background: rgba(255, 102, 102, 0.2); color: #ff6666; }
        .cred-role.victim { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .cred-role.admin { background: rgba(0, 200, 83, 0.2); color: #00c853; }
        .cred-password {
            font-family: 'Consolas', monospace;
            color: #888;
            font-size: 0.85rem;
        }
        .cred-desc {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.5rem;
        }
        .hint-box {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            font-size: 0.85rem;
        }
        .hint-box h4 {
            color: #fc6d26;
            margin-bottom: 0.25rem;
        }
        .hint-box p {
            color: #aaa;
            margin: 0;
        }
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                    <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                    <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                    <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
                </svg>
                MLRegistry
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="docs.php">Documentation</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="login-container">
            <div class="login-form">
                <h1>🔐 Sign In</h1>
                <p class="subtitle">Access your ML Model Registry</p>
                
                <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
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
                    <button type="submit" class="btn">Sign In</button>
                </form>
            </div>
            
            <div class="credentials-panel">
                <h2>🧪 Test Credentials</h2>
                
                <div class="cred-card attacker" onclick="fillCredentials('attacker', 'attacker123')">
                    <div class="cred-header">
                        <span class="cred-username">attacker</span>
                        <span class="cred-role attacker">Attacker</span>
                    </div>
                    <div class="cred-password">attacker123</div>
                    <div class="cred-desc">🎯 Use this to exploit the IDOR vulnerability</div>
                </div>
                
                <div class="cred-card victim" onclick="fillCredentials('victim_corp', 'victim123')">
                    <div class="cred-header">
                        <span class="cred-username">victim_corp</span>
                        <span class="cred-role victim">Victim</span>
                    </div>
                    <div class="cred-password">victim123</div>
                    <div class="cred-desc">🏢 Corporate account with 4 private ML models</div>
                </div>
                
                <div class="cred-card victim" onclick="fillCredentials('data_scientist', 'scientist123')">
                    <div class="cred-header">
                        <span class="cred-username">data_scientist</span>
                        <span class="cred-role victim">Victim</span>
                    </div>
                    <div class="cred-password">scientist123</div>
                    <div class="cred-desc">👨‍🔬 Researcher with 3 private models with secrets</div>
                </div>
                
                <div class="cred-card admin" onclick="fillCredentials('admin', 'admin123')">
                    <div class="cred-header">
                        <span class="cred-username">admin</span>
                        <span class="cred-role admin">Admin</span>
                    </div>
                    <div class="cred-password">admin123</div>
                    <div class="cred-desc">⚙️ Platform administrator</div>
                </div>
                
                <div class="hint-box">
                    <h4>💡 Hint</h4>
                    <p>Login as <strong>attacker</strong> to start exploiting the IDOR vulnerability.</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
