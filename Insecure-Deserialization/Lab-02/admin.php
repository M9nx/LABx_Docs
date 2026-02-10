<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Admin Panel
 * 
 * This page should only be accessible to administrators.
 * The vulnerability allows bypass through type juggling.
 */
require_once 'config.php';

$currentUser = getCurrentUser();
$session = getSessionFromCookie();

// Check if user is logged in and is admin
if (!$currentUser || !isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied - TypeJuggle Shop</title>
        <style>
            body { 
                font-family: 'Segoe UI', sans-serif; 
                background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%); 
                color: #e0e0e0; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                min-height: 100vh; 
                margin: 0; 
            }
            .error-box { 
                background: rgba(239, 68, 68, 0.1); 
                border: 1px solid rgba(239, 68, 68, 0.3); 
                padding: 3rem; 
                border-radius: 20px; 
                text-align: center; 
            }
            h1 { color: #ef4444; margin-bottom: 1rem; }
            a { color: #f97316; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>403 Forbidden</h1>
            <p>You do not have permission to access the admin panel.</p>
            <p><a href="login.php">Login</a> | <a href="index.php">Home</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle user deletion
$message = '';
$messageType = '';

if (isset($_GET['delete'])) {
    $usernameToDelete = $_GET['delete'];
    
    // Prevent deleting admin
    if ($usernameToDelete === 'administrator') {
        $message = 'Cannot delete the administrator account.';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
            $stmt->execute([$usernameToDelete]);
            
            if ($stmt->rowCount() > 0) {
                $message = "User '$usernameToDelete' has been deleted.";
                $messageType = 'success';
                
                // Check if carlos was deleted for lab completion
                if ($usernameToDelete === 'carlos') {
                    // Mark lab as solved
                    require_once('../progress.php');
                    markLabSolved(2);
                    header('Location: success.php');
                    exit;
                }
            } else {
                $message = "User '$usernameToDelete' not found.";
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'An error occurred while deleting the user.';
            $messageType = 'error';
        }
    }
}

// Get all users
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - TypeJuggle Shop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%); 
            min-height: 100vh; 
            color: #e0e0e0; 
        }
        .header { 
            background: rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(249,115,22,0.3); 
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
            color: #f97316; 
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
        .nav-links a:hover { color: #f97316; }
        .container { max-width: 1000px; margin: 0 auto; padding: 3rem 2rem; }
        .page-title { font-size: 2rem; margin-bottom: 0.5rem; color: #f97316; }
        .page-subtitle { color: #888; margin-bottom: 2rem; }
        .lab-card { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(249,115,22,0.2); 
            border-radius: 15px; 
            padding: 2rem; 
            margin-bottom: 2rem; 
            backdrop-filter: blur(10px); 
        }
        .lab-card h2 { color: #fb923c; margin-bottom: 1rem; font-size: 1.25rem; }
        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .message.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }
        .message.error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th { color: #888; font-weight: 500; font-size: 0.85rem; text-transform: uppercase; }
        tr:hover { background: rgba(249, 115, 22, 0.05); }
        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .role-admin { background: rgba(249, 115, 22, 0.2); color: #fb923c; }
        .role-user { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .delete-btn {
            color: #ef4444;
            text-decoration: none;
            padding: 0.3rem 0.8rem;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .delete-btn:hover { background: rgba(239, 68, 68, 0.2); }
        .admin-warning {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-left: 3px solid #22c55e;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .admin-warning h3 { color: #22c55e; margin-bottom: 0.5rem; }
        .admin-warning p { color: #ccc; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">TypeJuggle Shop</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="my-account.php">My Account</a>
                <a href="admin.php" style="color: #f97316;">Admin Panel</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($currentUser['username']) ?>)</a>
                <a href="docs.php">Docs</a>
            </nav>
        </div>
    </header>
    </header>

    <div class="container">
        <h1 class="page-title">Admin Panel</h1>
        <p class="page-subtitle">Logged in as: <?= htmlspecialchars($currentUser['username']) ?></p>
        
        <div class="admin-warning">
            <h3>Administrator Access Granted</h3>
            <p>You have successfully accessed the admin panel. Delete the user "carlos" to complete the lab.</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <div class="lab-card">
            <h2>User Management</h2>
            <table>
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
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="role-badge role-<?= $user['role'] ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['username'] !== 'administrator'): ?>
                                    <a href="?delete=<?= urlencode($user['username']) ?>" 
                                       class="delete-btn"
                                       onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($user['username']) ?>?')">
                                        Delete
                                    </a>
                                <?php else: ?>
                                    <span style="color: #666;">Protected</span>
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
