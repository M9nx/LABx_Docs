<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, guid, username, password, full_name FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_guid'] = $user['guid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Redirect to profile with GUID in URL (VULNERABLE DESIGN)
            header("Location: profile.php?id=" . $user['guid']);
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GUIDLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #ff4444;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 450px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .login-title {
            font-size: 1.8rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            color: #999;
            font-size: 0.95rem;
        }
        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ff4444;
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .hint-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hint-title {
            color: #00ff00;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }
        .hint-credentials {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.2);
            border-radius: 8px;
            padding: 1rem;
        }
        .hint-credentials code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #00ff00;
            font-family: 'Consolas', monospace;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 GUIDLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="blog.php">Blog</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1 class="login-title">Account Login</h1>
                <p class="login-subtitle">Sign in to access your profile</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           placeholder="Enter your username" autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Enter your password">
                </div>
                <button type="submit" class="btn-login">Login →</button>
            </form>

            <div class="hint-section">
                <h3 class="hint-title">🔑 Lab Credentials</h3>
                <div class="hint-credentials">
                    <p>Username: <code>wiener</code></p>
                    <p>Password: <code>peter</code></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
