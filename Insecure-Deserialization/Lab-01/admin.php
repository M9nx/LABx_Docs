<?php
/**
 * Admin Panel - User Management
 * VULNERABLE: Access control based on deserialized session cookie
 */

require_once 'config.php';
require_once '../progress.php';

// Check if user has admin privileges from deserialized cookie
// VULNERABLE: Trusts the 'admin' flag from client-provided serialized data
if (!isAdmin()) {
    // If not admin, show access denied
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Access Denied - SerialLab</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
                color: #e0e0e0;
            }
            .error-box {
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.3);
                border-radius: 15px;
                padding: 3rem;
                text-align: center;
                max-width: 500px;
            }
            h1 { color: #ef4444; margin-bottom: 1rem; }
            p { color: #999; margin-bottom: 1.5rem; }
            a {
                color: #f97316;
                text-decoration: none;
            }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>🚫 Access Denied</h1>
            <p>You do not have administrator privileges to access this page.</p>
            <a href="my-account.php">← Back to My Account</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle user deletion
$successMessage = '';
$errorMessage = '';

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $usernameToDelete = $_GET['delete'];
    
    $pdo = getDBConnection();
    
    // Get user info before deletion
    $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->execute([$usernameToDelete]);
    $userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userToDelete) {
        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
        $stmt->execute([$usernameToDelete]);
        
        $successMessage = "User '" . htmlspecialchars($usernameToDelete) . "' has been deleted successfully.";
        
        // If carlos was deleted, mark lab as solved
        if ($usernameToDelete === 'carlos') {
            markLabSolved(1);
            header('Location: success.php');
            exit;
        }
    } else {
        $errorMessage = "User not found.";
    }
}

// Get all users
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SerialLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
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
            color: #ef4444;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .subtitle { color: #fb923c; font-size: 1.2rem; }
        .warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-left: 5px solid #f59e0b;
            color: #fcd34d;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .warning strong { color: #fbbf24; }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-left-color: #22c55e;
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .admin-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(249, 115, 22, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }
        .admin-card h2 {
            color: #fb923c;
            font-size: 1.5rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        th {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        tr:hover { background: rgba(249, 115, 22, 0.05); }
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-admin {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }
        .role-user {
            background: linear-gradient(45deg, #22c55e, #16a34a);
            color: white;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }
        .back-link {
            display: inline-block;
            color: #f97316;
            text-decoration: none;
            margin-top: 20px;
            padding: 10px 20px;
            border: 1px solid rgba(249, 115, 22, 0.3);
            border-radius: 8px;
            transition: all 0.3s;
        }
        .back-link:hover {
            background: rgba(249, 115, 22, 0.1);
            border-color: #f97316;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Administrator Panel</h1>
            <p class="subtitle">User Management System</p>
        </div>

        <div class="warning">
            <strong>⚠️ Admin Access Granted!</strong><br>
            You have administrative privileges. You accessed this page by modifying the serialized session cookie.
        </div>

        <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <h2>👥 User Management</h2>
            <p style="color: #999; margin-bottom: 20px;">Manage all registered users. Delete users to remove them from the system.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $u['role']; ?>">
                                <?php echo strtoupper($u['role']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="admin.php?delete=<?php echo urlencode($u['username']); ?>" 
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to delete user <?php echo htmlspecialchars($u['username']); ?>?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="my-account.php" class="back-link">← Back to My Account</a>
        <a href="index.php" class="back-link" style="margin-left: 10px;">← Back to Lab Home</a>
    </div>
</body>
</html>
