<?php
// Lab 29: LinkedPro Newsletter Platform - Login Page
require_once 'config.php';

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['is_creator'] = $user['is_creator'];
            
            logActivity($conn, $user['user_id'], 'login', 'user', $user['user_id'], 'User logged in');
            
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
    <title>Sign In - LinkedPro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #f3f2ef 0%, #e8e6e3 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            padding: 1rem 2rem;
            background: white;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #0a66c2;
            text-decoration: none;
        }
        .logo span {
            color: #057642;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .login-container {
            display: flex;
            gap: 4rem;
            max-width: 1000px;
            align-items: center;
        }
        .welcome-section {
            max-width: 400px;
        }
        .welcome-section h1 {
            font-size: 2.5rem;
            color: #0a66c2;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .welcome-section p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 2rem;
            width: 350px;
        }
        .login-card h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #0a66c2;
            box-shadow: 0 0 0 1px #0a66c2;
        }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: #0a66c2;
            color: white;
            border: none;
            border-radius: 24px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 1rem;
        }
        .btn-login:hover {
            background: #004182;
        }
        .error {
            background: #fee;
            color: #c00;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }
        .test-accounts {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        .test-accounts h4 {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .account-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .account-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .account-item .role {
            color: #0a66c2;
            font-weight: 500;
        }
        .account-item .creds {
            color: #666;
            font-family: monospace;
        }
        .lab-banner {
            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
            color: white;
            padding: 0.5rem 1rem;
            text-align: center;
            font-size: 0.85rem;
        }
        .lab-banner a {
            color: #7fc4fd;
            text-decoration: none;
        }
        .lab-banner a:hover {
            text-decoration: underline;
        }
        .nav-links {
            margin-top: 1.5rem;
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        .nav-links a {
            color: #0a66c2;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="lab-banner">
        🔬 Lab 29: Newsletter Subscriber IDOR | <a href="lab-description.php">Lab Description</a> | <a href="docs.php">Documentation</a> | <a href="index.php">Lab Home</a> | <a href="../index.php">← All Labs</a>
    </div>
    
    <div class="header">
        <a href="index.php" class="logo">Linked<span>Pro</span></a>
    </div>
    
    <div class="main-content">
        <div class="login-container">
            <div class="welcome-section">
                <h1>Welcome to your professional community</h1>
                <p>Connect with professionals, discover newsletters, and stay updated with industry insights from thought leaders.</p>
            </div>
            
            <div class="login-card">
                <h2>Sign in</h2>
                
                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required placeholder="Enter your username">
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn-login">Sign in</button>
                </form>
                
                <div class="test-accounts">
                    <h4>🧪 Test Accounts</h4>
                    <div class="account-list">
                        <div class="account-item">
                            <span class="role">⚔️ Attacker</span>
                            <span class="creds">attacker / attacker123</span>
                        </div>
                        <div class="account-item">
                            <span class="role">👩‍💼 Creator (Alice)</span>
                            <span class="creds">alice_ceo / alice123</span>
                        </div>
                        <div class="account-item">
                            <span class="role">👨‍💼 Creator (Bob)</span>
                            <span class="creds">bob_investor / bob123</span>
                        </div>
                    </div>
                </div>
                
                <div class="nav-links">
                    <a href="index.php">← Back to Lab Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
