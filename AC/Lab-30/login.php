<?php
// Lab 30: Stocky Inventory App - Login Page
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['store_name'] = $user['store_name'];
        logActivity($pdo, $user['id'], 'login', 'User logged in');
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stocky</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .logo { font-size: 1.8rem; font-weight: bold; color: #7c3aed; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .login-container { display: flex; gap: 4rem; max-width: 1000px; align-items: center; }
        .welcome-section { max-width: 400px; }
        .welcome-section h1 { font-size: 2.5rem; color: #7c3aed; margin-bottom: 1rem; line-height: 1.2; }
        .welcome-section p { color: #666; font-size: 1.1rem; line-height: 1.6; }
        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 2rem;
            width: 350px;
        }
        .login-card h2 { font-size: 1.5rem; color: #333; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #666; font-size: 0.9rem; }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 1px #7c3aed; }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 24px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 1rem;
        }
        .btn-login:hover { background: #5b21b6; }
        .error { background: #fee; color: #c00; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.9rem; }
        .nav-links { margin-top: 1.5rem; text-align: center; }
        .nav-links a { color: #7c3aed; text-decoration: none; font-size: 0.9rem; margin: 0 0.5rem; }
        .nav-links a:hover { text-decoration: underline; }
        @media (max-width: 800px) {
            .login-container { flex-direction: column; text-align: center; }
            .welcome-section { max-width: 100%; }
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">📦 Stocky</a>
    </header>
    
    <div class="main-content">
        <div class="login-container">
            <div class="welcome-section">
                <h1>Manage Your Inventory Smarter</h1>
                <p>Stocky helps Shopify merchants track low stock variants, forecast demand, and never miss a sale due to stockouts.</p>
            </div>
            
            <div class="login-card">
                <h2>Sign In</h2>
                
                <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-login">Sign In</button>
                </form>
                
                <div style="background: #f3e8ff; border: 1px solid #c4b5fd; border-radius: 8px; padding: 1rem; margin-top: 1rem; font-size: 0.85rem;">
                    <strong style="color: #7c3aed;">🔑 Test Accounts:</strong>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.25rem; margin-top: 0.5rem; color: #5b21b6;">
                        <span>alice_shop</span><span>password123</span>
                        <span>bob_tech</span><span>password123</span>
                        <span>carol_home</span><span>password123</span>
                    </div>
                </div>
                
                <div class="nav-links">
                    <a href="index.php">← Back to Lab</a>
                    <a href="docs.php">Documentation</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
