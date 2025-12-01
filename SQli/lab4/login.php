<?php
require_once 'config.php';

$error = '';
$success = '';

// Handle login form submission
if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = getConnection();
    
    // Simple login verification (for demonstration)
    $query = "SELECT id, username, role FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] === 'admin') {
            $success = "Welcome Administrator! You have successfully logged in with admin privileges.";
        } else {
            $success = "Login successful! Welcome " . htmlspecialchars($user['username']);
        }
    } else {
        $error = "Invalid username or password.";
    }
    
    $stmt->close();
    $conn->close();
}

// Check if already logged in
if (isset($_SESSION['username'])) {
    $logged_in_user = $_SESSION['username'];
    $user_role = $_SESSION['role'] ?? 'user';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lab 4</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .error {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #27ae60;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
        }
        .user-info {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .logout-btn {
            background-color: #e74c3c;
            margin-top: 10px;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .hint {
            background-color: #f39c12;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h2>Admin Login Portal</h2>
            <p>Lab 4 - SQL Injection UNION Attack</p>
        </div>

        <?php if (isset($logged_in_user)): ?>
            <div class="user-info">
                <strong>Logged in as:</strong> <?php echo htmlspecialchars($logged_in_user); ?><br>
                <strong>Role:</strong> <?php echo htmlspecialchars($user_role); ?>
                <?php if ($user_role === 'admin'): ?>
                    <br><span style="color: #27ae60;">✓ Administrator privileges active</span>
                <?php endif; ?>
            </div>
            <form method="POST" action="logout.php">
                <button type="submit" class="btn logout-btn">Logout</button>
            </form>
        <?php else: ?>
            <div class="hint">
                <strong>Lab Objective:</strong> Use SQL injection on the product filter to discover admin credentials, then login here.
            </div>

            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="index.php">← Back to Product Catalog</a>
        </div>
    </div>
</body>
</html>