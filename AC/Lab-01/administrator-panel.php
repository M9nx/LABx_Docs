<?php
// INTENTIONALLY VULNERABLE: No authentication or authorization checks!
// This admin panel is completely unprotected

require_once 'config.php';

// Handle user deletion
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $pdo = getDBConnection();
    
    // Get user info before deletion for confirmation
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        // If carlos was deleted, redirect to success page
        if ($user['username'] === 'carlos') {
            header('Location: success.php');
            exit;
        }
        
        $successMessage = "User '" . htmlspecialchars($user['username']) . "' has been deleted successfully.";
    } else {
        $errorMessage = "User not found.";
    }
}

// Get all users
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Panel - SecureShop</title>
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
            color: #ff9999;
            font-size: 1.2rem;
        }

        .warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-left: 5px solid #ffc107;
            color: #ffeeba;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .warning strong {
            color: #ffc107;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: #28a745;
            color: #d4edda;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-left-color: #ff4444;
            color: #ffcccc;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .admin-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .admin-card h2 {
            color: #ff6666;
            font-size: 1.5rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .admin-card p {
            color: #cccccc;
            margin-bottom: 20px;
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
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-admin {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
        }

        .role-user {
            background: linear-gradient(45deg, #28a745, #20c997);
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
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #ff6666;
            border: 2px solid #ff6666;
        }

        .btn-secondary:hover {
            background: rgba(255, 102, 102, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .nav-link {
            color: #ff6666;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 102, 102, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Administrator Panel</h1>
            <p class="subtitle">Unprotected Admin Area - No Authentication Required!</p>
        </div>
        
        <div class="warning">
            <strong>🔓 SECURITY WARNING:</strong> This administrator panel is completely unprotected! 
            Anyone who finds this URL can access sensitive administrative functions.
        </div>
        
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <div class="admin-card">
            <h2>👥 User Management</h2>
            <p>Manage all users in the system. You can delete any user account from here.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete user <?php echo htmlspecialchars($user['username']); ?>?')">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="action-buttons">
            <a href="index.php" class="nav-link">🏠 Back to Main Site</a>
            <a href="docs.php" class="nav-link">📚 View Documentation</a>
        </div>
    </div>
    
    <script>
        // Show a warning when the page loads
        window.addEventListener('load', function() {
            if (!sessionStorage.getItem('adminWarningShown')) {
                alert('⚠️ VULNERABILITY DETECTED: This admin panel has no access controls!\n\nAnyone who discovers this URL can access administrative functions.');
                sessionStorage.setItem('adminWarningShown', 'true');
            }
        });
    </script>
</body>
</html>