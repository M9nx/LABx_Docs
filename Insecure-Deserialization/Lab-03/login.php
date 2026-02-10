<?php
/**
 * Lab 03: Using Application Functionality to Exploit Insecure Deserialization
 * Login Page
 */
require_once 'config.php';

$error = '';
$success = '';

// Check if already logged in
$session = getSessionFromCookie();
if ($session && validateSession($session)) {
    header('Location: my-account.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Create serialized session cookie with username and avatar_link
                $sessionCookie = createSerializedSession($user);
                
                // Set cookie (expires in 1 hour)
                setcookie('session', $sessionCookie, time() + 3600, '/');
                
                $success = 'Login successful! Redirecting...';
                header('Refresh: 1; URL=my-account.php');
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AvatarVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header { 
            background: rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(249,115,22,0.3); 
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
            color: #f97316; 
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
        .nav-links a:hover { color: #f97316; }
        .container { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 2rem; 
        }
        .login-container {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            padding: 3rem;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(10px);
        }
        h1 {
            text-align: center;
            margin-bottom: 0.5rem;
            color: #f97316;
            font-size: 2rem;
        }
        .subtitle {
            text-align: center;
            color: #888;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ccc;
            font-weight: 500;
        }
        .form-input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid rgba(249,115,22,0.3);
            border-radius: 10px;
            background: rgba(0,0,0,0.3);
            color: #fff;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,0.2);
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #f97316, #ea580c);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(249,115,22,0.4);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .success-message {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials-hint {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .credentials-hint h4 {
            color: #00ffff;
            margin-bottom: 0.5rem;
        }
        .credentials-hint code {
            background: rgba(0, 255, 255, 0.1);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #a0e0e0;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">AvatarVault</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="login.php" style="color: #f97316;">Login</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="login-container">
            <h1>Login</h1>
            <p class="subtitle">Access your AvatarVault account</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" 
                           placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn-submit">Login</button>
            </form>
            
            <div class="credentials-hint">
                <h4>Test Credentials</h4>
                <p>
                    Primary: <code>wiener</code> / <code>peter</code><br>
                    Backup: <code>gregg</code> / <code>rosebud</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
