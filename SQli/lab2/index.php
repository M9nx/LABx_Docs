<?php
/**
 * VULNERABLE LOGIN BYPASS LAB
 * 
 * WARNING: This code contains deliberate security vulnerabilities for educational purposes only!
 * DO NOT USE THIS CODE IN PRODUCTION ENVIRONMENTS!
 */

require_once 'config.php';

// Initialize session
init_session();

// Redirect to dashboard if already logged in
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

// Connect to database
$db = connect_db();

$login_error = '';
$attack_detected = false;

// Handle login attempt
if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Store original payload for tracking
    $original_payload = $username;
    
    // 🚨 VULNERABLE CODE - SQL Injection in login query
    // This query is vulnerable to SQL injection attacks
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    // Debug: Show the actual query being executed (for educational purposes)
    $debug_query = "Query executed: " . str_replace(["\n", "\r"], " ", $query);
    
    $result = $db->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if this was a successful SQL injection attack
        $attack_detected = false;
        if (strpos($original_payload, "'") !== false || 
            preg_match('/(--)|(#)|(\*\/)/i', $original_payload) ||
            preg_match('/\bor\b/i', $original_payload) ||
            ($user['username'] === 'administrator' && strpos($original_payload, 'administrator') !== false)) {
            $attack_detected = true;
        }
        
        // Set session variables (login successful)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['attack_detected'] = $attack_detected;
        $_SESSION['payload_used'] = $original_payload; // Store actual payload
        
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
        
    } else {
        $login_error = 'Invalid username or password.';
        // Debug info for educational purposes
        if (isset($debug_query)) {
            $login_error .= '<br><small style="color:#888;">Debug: ' . htmlspecialchars($debug_query) . '</small>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #333;
            margin: 0;
            font-size: 28px;
        }
        .logo p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .login-btn:hover {
            transform: translateY(-2px);
        }
        .error {
            background-color: #ffe6e6;
            color: #d00;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background-color: #ff6b6b;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .demo-accounts {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
        }
        .demo-accounts h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .demo-accounts p {
            margin: 5px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🏢 SecureCorp</h1>
            <p>Employee Login Portal</p>
        </div>

        <?php if ($attack_detected): ?>
        <div class="success">
            🎯 Congratulations, you solved the lab!<br>
            SQL injection attack successful! You've bypassed the login system.
        </div>
        <?php endif; ?>

        <?php if ($login_error): ?>
        <div class="error">
            <?php echo htmlspecialchars($login_error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="demo-accounts">
            <h4>Demo Accounts (for testing):</h4>
            <p><strong>User:</strong> john_doe / password123</p>
            <p><strong>Manager:</strong> alice_brown / alicepass321</p>
            <p><strong>Target:</strong> administrator / ???</p>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
close_db();
?>