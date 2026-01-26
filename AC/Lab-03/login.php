<?php
session_start();
require_once 'config.php';

$error_message = '';
$success_message = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

// Handle logout message
if (isset($_GET['logged_out'])) {
    $success_message = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, email, full_name, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // VULNERABILITY: Set Admin cookie based on database role
                // This cookie can be modified by the client!
                $admin_status = ($user['role'] === 'admin') ? 'true' : 'false';
                setcookie('Admin', $admin_status, time() + 3600, '/');
                
                header('Location: profile.php');
                exit;
            } else {
                $error_message = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error_message = 'Database connection failed';
        }
    } else {
        $error_message = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3 - Login</title>
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 15px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-title {
            color: #ff4444;
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .login-subtitle {
            color: #cccccc;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: #ff6666;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff4444;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.3);
        }

        .login-button {
            width: 100%;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.4);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid;
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-left-color: #ff4444;
            color: #ffcccc;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: #28a745;
            color: #d4edda;
        }

        .credentials-help {
            background: rgba(0, 0, 0, 0.3);
            border: 1px dashed #666;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .credentials-help h4 {
            color: #ff9999;
            margin-bottom: 10px;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #333;
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .username {
            color: #66ff66;
            font-weight: bold;
        }

        .password {
            color: #ffff66;
            font-weight: bold;
        }

        .nav-link {
            display: inline-block;
            color: #ff6666;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        .nav-link:hover {
            background: rgba(255, 102, 102, 0.1);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">Lab 3 Login</h1>
            <p class="login-subtitle">User role controlled by request parameter</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-input" 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

        <div class="credentials-help">
            <h4>🔑 Test Credentials</h4>
            <div class="credential-item">
                <span class="username">wiener</span>
                <span class="password">password</span>
            </div>
            <div class="credential-item">
                <span class="username">carlos</span>
                <span class="password">password</span>
            </div>
            <div class="credential-item">
                <span class="username">admin</span>
                <span class="password">password</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="index.php" class="nav-link">← Back to Lab</a>
        </div>
    </div>
</body>
</html>