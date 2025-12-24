<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Refresh user data from database
$stmt = $conn->prepare("SELECT username, email, role, full_name, department FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $_SESSION['role'] = $user['role'];
}

$isAdmin = $user && $user['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - MultiStep Admin</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-title h1 {
            font-size: 2.5rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }
        .profile-header h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .profile-header p {
            color: #888;
        }
        .role-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            margin-top: 1rem;
            font-weight: 600;
        }
        .role-admin {
            background: linear-gradient(135deg, rgba(255, 68, 68, 0.3), rgba(255, 100, 100, 0.2));
            border: 2px solid #ff4444;
            color: #ff6666;
        }
        .role-user {
            background: rgba(100, 100, 100, 0.3);
            border: 2px solid #666;
            color: #aaa;
        }
        .profile-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }
        .detail-label {
            color: #888;
        }
        .detail-value {
            color: #fff;
            font-weight: 500;
        }
        .admin-notice {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        .admin-notice h3 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .admin-notice p {
            color: #999;
            margin-bottom: 1rem;
        }
        .admin-notice a {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        .admin-notice a:hover {
            transform: translateY(-2px);
        }
        .user-notice {
            background: rgba(100, 150, 255, 0.1);
            border: 1px solid rgba(100, 150, 255, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        .user-notice p {
            color: #999;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔄 MultiStep Admin</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isAdmin): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>👤 My Profile</h1>
        </div>

        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar">
                    <?php echo $isAdmin ? '👑' : '👤'; ?>
                </div>
                <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                <p>@<?php echo htmlspecialchars($user['username']); ?></p>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo strtoupper($user['role']); ?>
                </span>
            </div>

            <div class="profile-details">
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Department</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['department']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Account Type</span>
                    <span class="detail-value"><?php echo $isAdmin ? 'Administrator' : 'Regular User'; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">User ID</span>
                    <span class="detail-value">#<?php echo $_SESSION['user_id']; ?></span>
                </div>
            </div>

            <?php if ($isAdmin): ?>
                <div class="admin-notice">
                    <h3>🛡️ Administrator Access</h3>
                    <p>You have admin privileges. Manage user roles from the Admin Panel.</p>
                    <a href="admin.php">Go to Admin Panel</a>
                </div>
            <?php else: ?>
                <div class="user-notice">
                    <p>👤 You have standard user access. Contact an administrator to request elevated privileges.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
