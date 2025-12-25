<?php
session_start();
require_once 'config.php';

// Admin panel - properly protected
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get all users for management
$stmt = $conn->prepare("SELECT id, username, email, role, full_name, department, last_login FROM users ORDER BY role DESC, username");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

$message = '';
if (isset($_GET['upgraded'])) {
    $message = "User '{$_GET['upgraded']}' has been upgraded to administrator.";
}
if (isset($_GET['downgraded'])) {
    $message = "User '{$_GET['downgraded']}' has been downgraded to regular user.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Referer Lab</title>
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
        }
        .container {
            max-width: 1200px;
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
        .page-title p {
            color: #888;
        }
        .success-msg {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #66ff66;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .info-banner {
            background: rgba(255, 200, 0, 0.1);
            border: 1px solid rgba(255, 200, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-banner h3 {
            color: #ffcc00;
            margin-bottom: 0.5rem;
        }
        .info-banner p {
            color: #ccc;
            font-size: 0.95rem;
        }
        .info-banner code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: monospace;
        }
        .users-table {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: rgba(255, 68, 68, 0.2);
        }
        th, td {
            padding: 1.2rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        th {
            color: #ff6666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        tbody tr {
            transition: background 0.3s;
        }
        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .role-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
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
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-upgrade {
            background: rgba(0, 200, 0, 0.2);
            color: #66ff66;
            border: 1px solid rgba(0, 200, 0, 0.5);
        }
        .btn-upgrade:hover {
            background: rgba(0, 200, 0, 0.3);
        }
        .btn-downgrade {
            background: rgba(255, 100, 0, 0.2);
            color: #ffaa66;
            border: 1px solid rgba(255, 100, 0, 0.5);
        }
        .btn-downgrade:hover {
            background: rgba(255, 100, 0, 0.3);
        }
        .self-badge {
            background: rgba(100, 150, 255, 0.2);
            color: #88aaff;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📋 Referer Lab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="profile.php">My Account</a>
                <a href="admin.php" style="color: #ff6666;">Admin Panel</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>👑 Admin Panel</h1>
            <p>Manage user roles and permissions</p>
        </div>

        <?php if ($message): ?>
            <div class="success-msg">✅ <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="info-banner">
            <h3>⚠️ Security Notice</h3>
            <p>
                Role changes are processed via <code>/admin-roles.php</code>. The endpoint uses 
                <strong>Referer header validation</strong> to ensure requests originate from this admin panel.
                This is the vulnerable mechanism that can be exploited!
            </p>
        </div>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($user['username']); ?>
                            <?php if ($user['username'] === $_SESSION['username']): ?>
                                <span class="self-badge">YOU</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['department']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['last_login'] ? date('M d, H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                        <td>
                            <?php if ($user['username'] !== $_SESSION['username']): ?>
                                <?php if ($user['role'] === 'user'): ?>
                                    <a href="admin-roles.php?username=<?php echo urlencode($user['username']); ?>&action=upgrade" 
                                       class="btn-action btn-upgrade">⬆️ Upgrade</a>
                                <?php else: ?>
                                    <a href="admin-roles.php?username=<?php echo urlencode($user['username']); ?>&action=downgrade" 
                                       class="btn-action btn-downgrade">⬇️ Downgrade</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #666;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
