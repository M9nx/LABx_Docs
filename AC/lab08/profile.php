<?php
session_start();
require_once 'config.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// VULNERABLE: Get username from URL parameter without ownership check
$requestedUser = $_GET['id'] ?? $_SESSION['username'];

$conn = getDBConnection();

// VULNERABLE: Fetch ANY user's data including password based on URL parameter
$stmt = $conn->prepare("SELECT id, username, password, email, role FROM users WHERE username = ?");
$stmt->bind_param("s", $requestedUser);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$conn->close();

// Handle password update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $newPassword = $_POST['new_password'];
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $newPassword, $_SESSION['username']);
    $stmt->execute();
    $conn->close();
    $message = "Password updated successfully!";
    
    // Refresh user data
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, username, password, email, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $requestedUser);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - PassLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            color: #ff4444;
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
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .profile-header h1 {
            color: #ff4444;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .profile-header p {
            color: #888;
        }
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ff4444;
        }
        .form-group input:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .password-section {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .password-section h3 {
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .btn-update {
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .message {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .no-user {
            text-align: center;
            color: #888;
            padding: 3rem;
        }
        .admin-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .admin-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔑 PassLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="profile.php?id=<?php echo htmlspecialchars($_SESSION['username']); ?>">My Account</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($user): ?>
        <div class="profile-card">
            <div class="profile-header">
                <h1>👤 <?php echo htmlspecialchars($user['username']); ?></h1>
                <p>Account Settings</p>
                <?php if ($user['role'] === 'admin'): ?>
                    <span class="admin-badge">Administrator</span>
                <?php endif; ?>
            </div>

            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['role']); ?>" disabled>
                </div>

                <div class="password-section">
                    <h3>🔑 Change Password</h3>
                    <div class="form-group">
                        <label>Current Password</label>
                        <!-- VULNERABILITY: Password is exposed in the value attribute! -->
                        <input type="password" name="current_password" value="<?php echo htmlspecialchars($user['password']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="Enter new password">
                    </div>
                    <button type="submit" class="btn-update">Update Password</button>
                </div>
            </form>

            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin.php" class="admin-link">🛡️ Go to Admin Panel</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="profile-card">
            <div class="no-user">
                <h2>User Not Found</h2>
                <p>The requested user does not exist.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>