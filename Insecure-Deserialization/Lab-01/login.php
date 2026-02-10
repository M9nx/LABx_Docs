<?php
require_once 'config.php';

$error = '';

// Check if already logged in
$session = getSessionFromCookie();
if ($session !== null) {
    header('Location: my-account.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // VULNERABLE: Create serialized session cookie
            // The session data includes an 'admin' flag that is trusted without server-side verification
            $sessionCookie = createSerializedSession($user);
            
            // Set the vulnerable session cookie
            setcookie('session', $sessionCookie, time() + 86400, '/');
            
            header('Location: my-account.php');
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
    <title>Login - SerialLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(249, 115, 22, 0.3);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }
        .login-title { text-align: center; margin-bottom: 30px; }
        .login-title h1 {
            color: #f97316;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .login-title p { color: #888; font-size: 1rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #cccccc;
            font-weight: 600;
        }
        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(249, 115, 22, 0.2);
            border-radius: 10px;
            font-size: 1rem;
            background: rgba(0, 0, 0, 0.3);
            color: #ffffff;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 15px rgba(249, 115, 22, 0.2);
        }
        .form-input::placeholder { color: #666; }
        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #f97316, #ea580c);
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
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
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
            border: 1px dashed rgba(249, 115, 22, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
        }
        .credentials-help h4 {
            color: #fb923c;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .credential-item:last-child { border-bottom: none; }
        .credential-item .role {
            color: #fb923c;
            font-weight: 600;
        }
        .credential-item .creds { font-family: 'Courier New', monospace; }
        .credential-item .creds .user { color: #22c55e; }
        .credential-item .creds .pass { color: #fbbf24; }
        .back-link { text-align: center; margin-top: 25px; }
        .back-link a {
            color: #fb923c;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link a:hover { color: #f97316; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-title">
            <h1>📦 SerialLab</h1>
            <p>Secure Session Management</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-input" 
                       placeholder="Enter your username" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" 
                       placeholder="Enter your password" autocomplete="current-password">
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>

        <div class="credentials-help">
            <h4>🔐 Available Accounts</h4>
            <div class="credential-item">
                <span class="role">Your Account:</span>
                <span class="creds"><span class="user">wiener</span>:<span class="pass">peter</span></span>
            </div>
        </div>

        <div class="back-link">
            <a href="index.php">← Back to Lab Home</a>
        </div>
    </div>
</body>
</html>
