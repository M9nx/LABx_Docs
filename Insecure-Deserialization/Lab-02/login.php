<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Login Page
 */
require_once 'config.php';

$error = '';
$success = '';

// Check if already logged in
if (getCurrentUser()) {
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
                // Create serialized session cookie
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
    <title>Login - TypeJuggle Shop</title>
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
            background: linear-gradient(135deg, #f97316, #ea580c);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(249, 115, 22, 0.4);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .credentials-hint {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
        }
        .credentials-hint h4 {
            color: #22c55e;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .credentials-hint code {
            background: rgba(0, 0, 0, 0.3);
            padding: 3px 8px;
            border-radius: 4px;
            color: #22c55e;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #f97316;
            text-decoration: none;
        }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">TypeJuggle Shop</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="login-container">
            <div class="login-title">
                <h1>Login</h1>
                <p>Access your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required autocomplete="username" placeholder="Enter username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password" placeholder="Enter password">
                </div>
                
                <button type="submit" class="login-button">Login</button>
            </form>
            
            <div class="credentials-hint">
                <h4>Test Credentials</h4>
                <p>Username: <code>wiener</code> &nbsp;|&nbsp; Password: <code>peter</code></p>
            </div>
            
            <div class="back-link">
                <a href="index.php">&larr; Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
