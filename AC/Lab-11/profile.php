<?php
session_start();
require_once 'config.php';

// User authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get current user info
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - MethodLab</title>
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
            font-weight: 500;
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
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2.5rem;
            text-align: center;
        }
        .profile-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
        }
        .profile-card h1 {
            color: #ff4444;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        .role-badge {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }
        .role-admin {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            border: 1px solid rgba(255, 68, 68, 0.5);
        }
        .role-user {
            background: rgba(100, 100, 100, 0.2);
            color: #aaa;
            border: 1px solid rgba(100, 100, 100, 0.5);
        }
        .info-section {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #999;
            font-weight: 500;
        }
        .info-value {
            color: #e0e0e0;
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">⚙️ MethodLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="profile.php">My Account</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="profile-card">
            <div class="profile-icon">👤</div>
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <span class="role-badge role-<?php echo $user['role']; ?>">
                <?php echo strtoupper($user['role']); ?>
            </span>
            
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">User ID</span>
                    <span class="info-value">#<?php echo $_SESSION['user_id']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['role']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Privileges</span>
                    <span class="info-value">
                        <?php echo $user['role'] === 'admin' ? 'Full Access' : 'Limited Access'; ?>
                    </span>
                </div>
            </div>

            <div class="action-buttons">
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="admin.php" class="btn btn-primary">👨‍💼 Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-secondary">🚪 Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
