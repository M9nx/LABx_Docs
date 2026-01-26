<?php
session_start();
require_once 'config.php';

// STEP 1: Admin panel - SELECT USER (Protected)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get all users except current admin
$stmt = $conn->prepare("SELECT id, username, email, role, full_name, department, last_login FROM users WHERE username != ? ORDER BY username");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

$message = '';
if (isset($_GET['updated'])) {
    $message = "User '{$_GET['updated']}' role has been updated successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MultiStep Admin</title>
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
            color: #999;
            font-size: 1.1rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .step-num {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .step.active .step-num {
            background: #ff4444;
            color: white;
        }
        .step.inactive .step-num {
            background: rgba(255, 255, 255, 0.1);
            color: #666;
        }
        .step-label {
            color: #888;
            font-size: 0.9rem;
        }
        .step.active .step-label {
            color: #ff6666;
            font-weight: 600;
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
            font-size: 0.9rem;
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
            font-size: 0.85rem;
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
        .btn-change {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            background: rgba(0, 150, 255, 0.2);
            color: #66aaff;
            border: 1px solid rgba(0, 150, 255, 0.5);
            transition: all 0.3s;
        }
        .btn-change:hover {
            background: rgba(0, 150, 255, 0.3);
        }
        .info-box {
            background: rgba(100, 100, 255, 0.1);
            border: 1px solid rgba(100, 100, 255, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .info-box h3 {
            color: #aaaaff;
            margin-bottom: 0.5rem;
        }
        .info-box p {
            color: #888;
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
                <a href="profile.php">My Account</a>
                <a href="admin.php">Admin Panel</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>👨‍💼 Admin Panel</h1>
            <p>Multi-step user role management</p>
        </div>

        <div class="step-indicator">
            <div class="step active">
                <div class="step-num">1</div>
                <span class="step-label">Select User</span>
            </div>
            <div class="step inactive">
                <div class="step-num">2</div>
                <span class="step-label">Choose Role</span>
            </div>
            <div class="step inactive">
                <div class="step-num">3</div>
                <span class="step-label">Confirm</span>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="success-msg">✅ <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['department']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['last_login'] ? date('M d, H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                        <td>
                            <a href="admin-roles.php?username=<?php echo urlencode($user['username']); ?>" class="btn-change">
                                📝 Change Role
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="info-box">
            <h3>ℹ️ Multi-Step Process</h3>
            <p>Changing a user's role requires 3 steps: Select the user → Choose the new role → Confirm the change. This ensures proper authorization at each step.</p>
        </div>
    </div>
</body>
</html>
