<?php
session_start();
require_once 'config.php';

// ============================================================
// VULNERABLE CODE: X-Original-URL Header Bypass
// ============================================================
// This simulates a front-end/back-end architecture where:
// 1. Front-end blocks direct access to /admin
// 2. Back-end processes X-Original-URL header for routing
// ============================================================

// Simulate front-end blocking: Direct requests to admin.php are blocked
// Check if this is a direct request (no X-Original-URL bypass)
$originalUrl = $_SERVER['HTTP_X_ORIGINAL_URL'] ?? null;
$requestUri = $_SERVER['REQUEST_URI'];

// Check if direct access to admin.php (blocked by "front-end")
// The front-end would block /admin path, but X-Original-URL bypasses it
$isDirectAdminAccess = (strpos($requestUri, 'admin.php') !== false && !$originalUrl);

// If someone is accessing directly via URL without the header trick
// we block them (simulating front-end proxy block)
if ($isDirectAdminAccess && !$originalUrl) {
    // Check if this is really being accessed directly
    // Only block if no X-Original-URL header is present
    $checkBypass = isset($_SERVER['HTTP_X_ORIGINAL_URL']);
    
    if (!$checkBypass) {
        http_response_code(403);
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-box {
            text-align: center;
            padding: 3rem;
        }
        h1 {
            font-size: 4rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        p {
            color: #888;
            font-size: 1.1rem;
        }
        a {
            color: #ff4444;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>403</h1>
        <p>Access denied</p>
        <p style="margin-top: 1rem; font-size: 0.9rem;">Path "/admin" is blocked by external access control.</p>
        <p style="margin-top: 2rem;"><a href="index.php">← Back to Home</a></p>
    </div>
</body>
</html>';
        exit;
    }
}

// ============================================================
// VULNERABLE: If X-Original-URL header is set, process it
// This allows bypassing the front-end access control
// ============================================================

// Fetch all users for admin panel
$users = [];
$result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Check if carlos was deleted
$carlosDeleted = true;
foreach ($users as $user) {
    if ($user['username'] === 'carlos') {
        $carlosDeleted = false;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SecureCorp</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .admin-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .admin-header h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .admin-header p {
            color: #888;
        }
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        .users-table-container {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            overflow: hidden;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th {
            background: rgba(255, 68, 68, 0.15);
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: #ff6666;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        .users-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .users-table tbody tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }
        .users-table tbody tr:last-child td {
            border-bottom: none;
        }
        .role-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .role-badge.admin {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .role-badge.user {
            background: rgba(0, 200, 100, 0.2);
            color: #00c864;
            border: 1px solid rgba(0, 200, 100, 0.3);
        }
        .btn-delete {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.5);
            color: #ff6666;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: #ff4444;
            color: white;
        }
        .success-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .success-banner h3 {
            color: #00ff00;
            margin-bottom: 0.5rem;
        }
        .success-banner p {
            color: #88ff88;
        }
        .success-banner a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.6rem 1.5rem;
            background: linear-gradient(135deg, #00cc00, #008800);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🏢 SecureCorp</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="admin-header">
            <h1>⚙️ Admin Panel</h1>
            <p>User Management Dashboard</p>
            <span class="admin-badge">🔓 Unauthenticated Access</span>
        </div>

        <?php if ($carlosDeleted): ?>
        <div class="success-banner">
            <h3>🎉 Congratulations!</h3>
            <p>You have successfully deleted the user "carlos" and completed the lab!</p>
            <a href="success.php">View Success Page →</a>
        </div>
        <?php endif; ?>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
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
                        <td>
                            <span class="role-badge <?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <?php if ($user['username'] !== 'administrator'): ?>
                            <a href="admin-delete.php?username=<?php echo urlencode($user['username']); ?>" class="btn-delete">Delete</a>
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
