<?php
/**
 * Dashboard page - shows user info after successful login
 */

require_once 'config.php';

// Initialize session
init_session();

// Redirect to login if not logged in
if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

// Get current user info
$current_user = get_logged_user();

// Connect to database to get full user details
$db = connect_db();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $current_user['id']);
$stmt->execute();
$result = $stmt->get_result();
$user_details = $result->fetch_assoc();
$stmt->close();

// Handle logout
if (isset($_GET['logout'])) {
    logout();
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SecureCorp</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo h1 {
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .user-profile {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        .profile-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .profile-section h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #333;
        }
        .value {
            color: #666;
        }
        .role-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .role-admin {
            background-color: #dc3545;
            color: white;
        }
        .role-manager {
            background-color: #ffc107;
            color: #333;
        }
        .role-user {
            background-color: #28a745;
            color: white;
        }
        .admin-panel {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .admin-panel h3 {
            margin-top: 0;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🏢 SecureCorp Dashboard</h1>
            </div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($current_user['username']); ?>!</span>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Welcome to SecureCorp Employee Portal</h2>
            <p>You have successfully logged into the system. Below you can view your account information and access available features based on your role.</p>
        </div>

        <div class="user-profile">
            <div class="profile-section">
                <h3>👤 Account Information</h3>
                <div class="info-row">
                    <span class="label">User ID:</span>
                    <span class="value"><?php echo htmlspecialchars($user_details['id']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Username:</span>
                    <span class="value"><?php echo htmlspecialchars($user_details['username']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($user_details['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Role:</span>
                    <span class="value">
                        <span class="role-badge role-<?php echo $user_details['role']; ?>">
                            <?php echo htmlspecialchars($user_details['role']); ?>
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Account Created:</span>
                    <span class="value"><?php echo date('M d, Y', strtotime($user_details['created_at'])); ?></span>
                </div>
            </div>

            <div class="profile-section">
                <h3>🔐 Access Level</h3>
                <?php if ($user_details['role'] === 'admin'): ?>
                    <p><strong>Administrator Access</strong> - You have full system privileges including:</p>
                    <ul>
                        <li>User management</li>
                        <li>System configuration</li>
                        <li>Security settings</li>
                        <li>Data access controls</li>
                        <li>Audit logs</li>
                    </ul>
                <?php elseif ($user_details['role'] === 'manager'): ?>
                    <p><strong>Manager Access</strong> - You have elevated privileges including:</p>
                    <ul>
                        <li>Team management</li>
                        <li>Report generation</li>
                        <li>Resource allocation</li>
                        <li>Performance monitoring</li>
                    </ul>
                <?php else: ?>
                    <p><strong>Standard User Access</strong> - You have access to:</p>
                    <ul>
                        <li>Personal dashboard</li>
                        <li>Basic features</li>
                        <li>Document viewing</li>
                        <li>Profile management</li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($user_details['role'] === 'admin' || (isset($_SESSION['attack_detected']) && $_SESSION['attack_detected'])): ?>
        <div class="admin-panel">
            <h3>🚨 Administrator Panel</h3>
            <p><strong>Congratulations!</strong> You have successfully accessed the administrator account using SQL injection.</p>
            <p><strong>Attack Method:</strong> SQL Injection Login Bypass</p>
            <p><strong>Payload Used:</strong> <code><?php echo htmlspecialchars($_SESSION['payload_used'] ?? 'unknown'); ?></code></p>
            <p>In a real system, this would give you access to:</p>
            <ul>
                <li>All user accounts and passwords</li>
                <li>System configuration files</li>
                <li>Database administration tools</li>
                <li>Security logs and monitoring</li>
                <li>Company confidential data</li>
            </ul>
            <p><strong>How it works:</strong> 
            <?php if (isset($_SESSION['payload_used'])): ?>
                <?php if (strpos($_SESSION['payload_used'], '--') !== false): ?>
                    The comment syntax <code>--</code> causes the SQL database to ignore everything after it, including the password check!
                <?php elseif (strpos($_SESSION['payload_used'], '#') !== false): ?>
                    The comment syntax <code>#</code> causes the SQL database to ignore everything after it, including the password check!
                <?php else: ?>
                    The SQL injection manipulates the query logic to bypass authentication!
                <?php endif; ?>
            <?php else: ?>
                The SQL injection bypasses the authentication mechanism!
            <?php endif; ?>
            </p>
            <p><strong>Prevention:</strong> Always use prepared statements and proper input validation to prevent such attacks!</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close database connection
close_db();
?>