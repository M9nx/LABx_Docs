<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

$error_message = '';
$success_message = '';

// VULNERABILITY: Check Admin cookie instead of proper session-based authorization
$is_admin = isset($_COOKIE['Admin']) && $_COOKIE['Admin'] === 'true';

if (!$is_admin) {
    $error_message = 'Access Denied: You need administrator privileges to access this panel.';
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user']) && $is_admin) {
    $user_id = $_POST['user_id'];
    
    try {
        // Check if user exists before deletion
        $check_stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $check_stmt->execute([$user_id]);
        $user_to_delete = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_to_delete) {
            $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delete_stmt->execute([$user_id]);
            $success_message = "User '" . htmlspecialchars($user_to_delete['username']) . "' has been successfully deleted.";
            
            // Mark lab as solved when carlos is deleted
            if (strtolower($user_to_delete['username']) === 'carlos') {
                markLabSolved(3);
            }
        } else {
            $error_message = "User not found.";
        }
    } catch (PDOException $e) {
        $error_message = "Error deleting user: " . $e->getMessage();
    }
}

// Get all users for display
try {
    $stmt = $pdo->query("SELECT id, username, email, role, full_name FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Failed to fetch users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3 - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .subtitle {
            color: #999;
            font-size: 1.2rem;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-left-color: #ff4444;
            color: #ffcccc;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: #28a745;
            color: #d4edda;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .admin-header h2 {
            color: #ff6666;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        .users-table th,
        .users-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        .users-table th {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .users-table tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }

        .role-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .role-admin {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
        }

        .role-user {
            background: linear-gradient(45deg, #4CAF50, #388E3C);
            color: white;
        }

        .delete-btn {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(220, 53, 69, 0.3);
        }

        .delete-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }

        .vulnerability-info {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .vulnerability-info h4 {
            color: #ffd54f;
            margin-bottom: 10px;
        }

        .vulnerability-info p {
            color: #fff3cd;
            font-size: 0.9rem;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Panel</h1>
            <p class="subtitle">User Management System</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <strong>Success:</strong> <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <div class="vulnerability-info">
            <h4>🔍 Vulnerability Information</h4>
            <p>This admin panel checks the "Admin" cookie to determine access rights.</p>
            <p>Current Admin cookie value: <strong><?= isset($_COOKIE['Admin']) ? $_COOKIE['Admin'] : 'not set' ?></strong></p>
            <p>Try modifying this cookie to "true" to gain admin access!</p>
        </div>

        <div class="admin-card">
            <div class="admin-header">
                <h2>User Management</h2>
                <p style="color: #999;">Manage system users and their roles</p>
            </div>

            <?php if ($is_admin && !empty($users)): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="role-badge <?= $user['role'] === 'admin' ? 'role-admin' : 'role-user' ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="delete_user" value="1">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="delete-btn" 
                                            onclick="return confirm('Are you sure you want to delete user <?= htmlspecialchars($user['username']) ?>?')"
                                            <?= $user['role'] === 'admin' ? 'disabled' : '' ?>>
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (!$is_admin): ?>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <h3>Access Denied</h3>
                    <p>You need administrator privileges to view and manage users.</p>
                    <p style="margin-top: 15px; font-size: 0.9rem;">
                        Hint: Check your browser's cookies and modify the "Admin" cookie value!
                    </p>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #999;">
                    <h3>No Users Found</h3>
                    <p>The user database appears to be empty.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="nav-buttons">
            <a href="profile.php" class="btn btn-secondary">← Back to Profile</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
            <a href="docs.php" class="btn btn-primary">📚 Lab Documentation</a>
        </div>
    </div>
</body>
</html>