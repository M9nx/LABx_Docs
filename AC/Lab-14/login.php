<?php
session_start();
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['manager_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM managers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $manager = $result->fetch_assoc();
    
    // Simple password check (intentionally weak for lab)
    if ($manager && $password === $manager['password']) {
        $_SESSION['manager_id'] = $manager['id'];
        $_SESSION['username'] = $manager['username'];
        $_SESSION['full_name'] = $manager['full_name'];
        $_SESSION['role'] = $manager['role'];
        
        // Update last login
        $update = $conn->prepare("UPDATE managers SET last_login = NOW() WHERE id = ?");
        $update->bind_param("i", $manager['id']);
        $update->execute();
        
        // Generate CSRF token
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $tokenStmt = $conn->prepare("INSERT INTO csrf_tokens (manager_id, token, expires_at) VALUES (?, ?, ?)");
        $tokenStmt->bind_param("iss", $manager['id'], $token, $expires);
        $tokenStmt->execute();
        $_SESSION['csrf_token'] = $token;
        
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
    <title>Login - Revive Adserver</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
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
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 20px;
            padding: 3rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .login-header h1 {
            color: #ff4444;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #888;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #aaa;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ff4444;
            box-shadow: 0 0 15px rgba(255, 68, 68, 0.2);
        }
        .error-msg {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid #ff4444;
            color: #ff6666;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .credentials-hint {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            border: 1px dashed rgba(255, 68, 68, 0.3);
        }
        .credentials-hint h4 {
            color: #ff6666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .cred-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .cred-row:last-child { border-bottom: none; }
        .cred-user { color: #aaa; }
        .cred-pass { color: #88ff88; font-family: monospace; }
        .cred-role { 
            font-size: 0.65rem; 
            color: #666; 
            display: block;
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        .nav-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">📢</div>
                <h1>Revive Adserver</h1>
                <p>Manager Portal Login</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus
                           placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Enter your password">
                </div>
                <button type="submit" class="btn-login">Sign In →</button>
            </form>

            <div class="credentials-hint">
                <h4>🔑 Test Credentials:</h4>
                <div class="cred-row">
                    <span class="cred-user">manager_a <span class="cred-role">(Attacker)</span></span>
                    <span class="cred-pass">attacker123</span>
                </div>
                <div class="cred-row">
                    <span class="cred-user">manager_b <span class="cred-role">(Victim)</span></span>
                    <span class="cred-pass">victim456</span>
                </div>
                <div class="cred-row">
                    <span class="cred-user">manager_c</span>
                    <span class="cred-pass">charlie789</span>
                </div>
                <div class="cred-row">
                    <span class="cred-user">admin <span class="cred-role">(Full Access)</span></span>
                    <span class="cred-pass">admin</span>
                </div>
            </div>
        </div>

        <div class="nav-links">
            <a href="index.php">← Back to Home</a>
            <a href="../index.php">All Labs</a>
            <a href="docs.php">Documentation</a>
        </div>
    </div>
</body>
</html>
