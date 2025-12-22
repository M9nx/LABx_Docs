<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Refresh user data from database to get current roleid
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();
$_SESSION['roleid'] = $currentUser['roleid'];

// Check if user has admin role (roleid = 2)
if ($_SESSION['roleid'] != 2) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied - RoleLab</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
                min-height: 100vh;
                color: #e0e0e0;
                display: flex;
                flex-direction: column;
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
            }
            .nav-links a {
                color: #e0e0e0;
                text-decoration: none;
                font-weight: 500;
            }
            .nav-links a:hover { color: #ff4444; }
            .btn-back {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.6rem 1.2rem;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 68, 68, 0.3);
                color: #e0e0e0;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.3s;
            }
            .btn-back:hover {
                background: rgba(255, 68, 68, 0.2);
                border-color: #ff4444;
                color: #ff4444;
            }
            .main-content {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 2rem;
            }
            .access-denied {
                background: rgba(255, 68, 68, 0.1);
                border: 2px solid #ff4444;
                border-radius: 20px;
                padding: 3rem;
                text-align: center;
                max-width: 500px;
            }
            .access-denied h1 {
                font-size: 4rem;
                color: #ff4444;
                margin-bottom: 1rem;
            }
            .access-denied h2 {
                color: #ff6666;
                margin-bottom: 1rem;
            }
            .access-denied p {
                color: #999;
                margin-bottom: 1.5rem;
            }
            .role-info {
                background: rgba(0, 0, 0, 0.3);
                padding: 1rem;
                border-radius: 10px;
                margin: 1.5rem 0;
            }
            .role-info code {
                color: #ff4444;
                font-family: monospace;
            }
            .btn {
                display: inline-block;
                padding: 0.8rem 1.5rem;
                background: linear-gradient(135deg, #ff4444, #cc0000);
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                margin: 0.5rem;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
            }
        </style>
    </head>
    <body>
        <header class="header">
            <div class="header-content">
                <a href="index.php" class="logo">🔐 RoleLab</a>
                <nav class="nav-links">
                    <a href="../index.php" class="btn-back">← All Labs</a>
                    <a href="index.php">Home</a>
                    <a href="profile.php">My Profile</a>
                    <a href="logout.php">Logout</a>
                </nav>
            </div>
        </header>
        <div class="main-content">
            <div class="access-denied">
                <h1>🚫</h1>
                <h2>Admin Access Only</h2>
                <p>This admin panel is only accessible to users with administrator privileges.</p>
                <div class="role-info">
                    <p>Your current role ID: <code><?php echo $_SESSION['roleid']; ?></code></p>
                    <p>Required role ID: <code>2</code> (Administrator)</p>
                </div>
                <p style="color: #00ffff; font-size: 0.9rem;">
                    💡 Hint: Can you find a way to change your role?
                </p>
                <a href="profile.php" class="btn">Go to Profile</a>
                <a href="index.php" class="btn">Back to Home</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle user deletion
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    
    // Prevent deleting yourself or administrator
    if ($userId == $_SESSION['user_id']) {
        $message = "You cannot delete your own account!";
        $messageType = 'error';
    } elseif ($userId == 1) {
        $message = "Cannot delete the primary administrator account!";
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $deletedUser = $stmt->fetch();
        
        if ($deletedUser) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Check if carlos was deleted - lab completion
            if ($deletedUser['username'] === 'carlos') {
                header('Location: success.php');
                exit;
            }
            
            $message = "User '{$deletedUser['username']}' has been deleted successfully!";
            $messageType = 'success';
        } else {
            $message = "User not found!";
            $messageType = 'error';
        }
    }
}

// Get all users
$stmt = $pdo->query("SELECT id, username, email, full_name, roleid, department, last_login FROM users ORDER BY id");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - RoleLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .admin-badge {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            color: #888;
            margin-bottom: 2rem;
        }
        .admin-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .section-title {
            color: #ff6666;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .message-success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }
        .message-error {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid #ff4444;
            color: #ff6666;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 68, 68, 0.1);
        }
        .users-table th {
            background: rgba(255, 68, 68, 0.1);
            color: #ff6666;
            font-weight: 600;
        }
        .users-table tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }
        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .role-admin {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .role-user {
            background: rgba(100, 100, 100, 0.5);
            color: #ccc;
        }
        .btn-delete {
            background: transparent;
            border: 1px solid #ff4444;
            color: #ff4444;
            padding: 0.4rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: #ff4444;
            color: white;
        }
        .btn-delete:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .target-user {
            background: rgba(255, 255, 0, 0.1) !important;
        }
        .target-badge {
            background: #ffaa00;
            color: #000;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .success-box {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .success-box h2 {
            color: #00ff00;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 RoleLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="profile.php">My Profile</a>
                <span class="admin-badge">🛡️ Administrator</span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">🛡️ Admin Panel</h1>
        <p class="page-subtitle">Manage users and system settings. You have administrator privileges (roleid: 2).</p>

        <?php if ($message): ?>
            <div class="message message-<?php echo $messageType; ?>">
                <?php echo $messageType === 'success' ? '✅' : '❌'; ?> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <h2 class="section-title">👥 User Management</h2>
            <p style="color: #888; margin-bottom: 1.5rem;">Delete the target user <strong style="color: #ffaa00;">carlos</strong> to complete this lab.</p>
            
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="<?php echo $user['username'] === 'carlos' ? 'target-user' : ''; ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($user['username']); ?>
                                <?php if ($user['username'] === 'carlos'): ?>
                                    <span class="target-badge">🎯 TARGET</span>
                                <?php endif; ?>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <span style="color: #00ff00; font-size: 0.8rem;">(You)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo $user['roleid'] == 2 ? 'role-admin' : 'role-user'; ?>">
                                    <?php echo $user['roleid'] == 2 ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['department'] ?: '-'); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id'] && $user['id'] != 1): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn-delete">🗑️ Delete</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-delete" disabled>Protected</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-card">
            <h2 class="section-title">📊 System Information</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 10px;">
                    <div style="color: #888; font-size: 0.85rem;">Total Users</div>
                    <div style="font-size: 1.5rem; color: #ff4444;"><?php echo count($users); ?></div>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 10px;">
                    <div style="color: #888; font-size: 0.85rem;">Administrators</div>
                    <div style="font-size: 1.5rem; color: #ff4444;"><?php echo count(array_filter($users, fn($u) => $u['roleid'] == 2)); ?></div>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 10px;">
                    <div style="color: #888; font-size: 0.85rem;">Regular Users</div>
                    <div style="font-size: 1.5rem; color: #ff4444;"><?php echo count(array_filter($users, fn($u) => $u['roleid'] == 1)); ?></div>
                </div>
                <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 10px;">
                    <div style="color: #888; font-size: 0.85rem;">Your Role ID</div>
                    <div style="font-size: 1.5rem; color: #00ff00;"><?php echo $_SESSION['roleid']; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
