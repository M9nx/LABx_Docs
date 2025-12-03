<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TechCorp</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .profile-card {
            background: rgba(255,255,255,0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(20px);
            margin-bottom: 2rem;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem auto;
        }
        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .info-section {
            background: rgba(102, 126, 234, 0.05);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        .info-section h3 {
            margin: 0 0 1rem 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }
        .info-item {
            margin-bottom: 1rem;
        }
        .info-item label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 0.3rem;
        }
        .info-item span {
            color: #666;
        }
        .role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-admin {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
            color: white;
        }
        .role-manager {
            background: linear-gradient(45deg, #fd7e14, #ffc107);
            color: white;
        }
        .role-user {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        .clearance-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }
        .clearance-top-secret {
            background: #dc3545;
            color: white;
        }
        .clearance-secret {
            background: #fd7e14;
            color: white;
        }
        .clearance-confidential {
            background: #ffc107;
            color: #333;
        }
        .clearance-basic {
            background: #28a745;
            color: white;
        }
        .clearance-none {
            background: #6c757d;
            color: white;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        .salary {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-container">
            <a href="index.php" class="logo">TechCorp</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="solutions.php">Solutions</a>
                <a href="services.php">Services</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="profile.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['full_name'], 0, 2)); ?>
                </div>
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo strtoupper($user['role']); ?>
                </span>
                <br>
                <span class="clearance-badge clearance-<?php echo str_replace('-', '-', $user['security_clearance']); ?>">
                    <?php echo strtoupper(str_replace('-', ' ', $user['security_clearance'])) . ' CLEARANCE'; ?>
                </span>
            </div>
            
            <div class="profile-info">
                <div class="info-section">
                    <h3>👤 Personal Information</h3>
                    
                    <div class="info-item">
                        <label>Employee ID:</label>
                        <span><?php echo htmlspecialchars($user['id']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Username:</label>
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Phone:</label>
                        <span><?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Emergency Contact:</label>
                        <span><?php echo htmlspecialchars($user['emergency_contact'] ?: 'Not provided'); ?></span>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>🏢 Professional Information</h3>
                    
                    <div class="info-item">
                        <label>Department:</label>
                        <span><?php echo htmlspecialchars($user['department']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Position:</label>
                        <span><?php echo htmlspecialchars($user['position']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Annual Salary:</label>
                        <span class="salary">$<?php echo number_format($user['salary'], 2); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Security Clearance:</label>
                        <span><?php echo ucwords(str_replace('-', ' ', $user['security_clearance'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Member Since:</label>
                        <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Last Login:</label>
                        <span><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></span>
                    </div>
                </div>
                
                <div class="info-section full-width">
                    <h3>📍 Address & Notes</h3>
                    
                    <div class="info-item">
                        <label>Address:</label>
                        <span><?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Notes:</label>
                        <span><?php echo htmlspecialchars($user['notes'] ?: 'No notes'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>