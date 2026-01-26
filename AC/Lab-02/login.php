<?php
session_start();
require_once 'config.php';

$error = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            header('Location: index.php');
            exit;
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
    <title>Login - TechCorp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .login-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-title h1 {
            color: #ff4444;
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .login-title p {
            color: #888;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #cccccc;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(255, 68, 68, 0.2);
            border-radius: 10px;
            font-size: 1rem;
            background: rgba(0, 0, 0, 0.3);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff4444;
            box-shadow: 0 0 15px rgba(255, 68, 68, 0.2);
        }

        .form-input::placeholder {
            color: #666;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.4);
        }

        .error {
            color: #ff6b6b;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            text-align: center;
        }

        .credentials-help {
            background: rgba(0, 0, 0, 0.3);
            border: 1px dashed rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
        }

        .credentials-help h4 {
            color: #ff9999;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .credential-item .role {
            color: #ff6666;
            font-weight: 600;
        }

        .credential-item .creds {
            font-family: 'Courier New', monospace;
        }

        .credential-item .creds .user {
            color: #66ff66;
        }

        .credential-item .creds .pass {
            color: #ffff66;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #ff6666;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            background: rgba(255, 102, 102, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-title">
            <h1>🏢 TechCorp</h1>
            <p>Corporate Solutions Portal</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="login-button">Login</button>
        </form>
        
        <div class="credentials-help">
            <h4>🔑 Demo Credentials</h4>
            <div class="credential-item">
                <span class="role">Admin</span>
                <span class="creds"><span class="user">admin</span> / <span class="pass">admin123</span></span>
            </div>
            <div class="credential-item">
                <span class="role">Manager</span>
                <span class="creds"><span class="user">sarah</span> / <span class="pass">sarah123</span></span>
            </div>
            <div class="credential-item">
                <span class="role">User</span>
                <span class="creds"><span class="user">carlos</span> / <span class="pass">carlos123</span></span>
            </div>
            <div class="credential-item">
                <span class="role">Engineer</span>
                <span class="creds"><span class="user">mike</span> / <span class="pass">mike123</span></span>
            </div>
        </div>
        
        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>