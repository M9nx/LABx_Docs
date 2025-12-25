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
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            margin-bottom: 40px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #cccccc;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin: 0 auto 20px auto;
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.3);
        }

        .profile-header h1 {
            color: #ffffff;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .role-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-right: 10px;
        }

        .role-admin {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.4);
        }

        .role-manager {
            background: linear-gradient(45deg, #fd7e14, #ffc107);
            color: white;
            box-shadow: 0 4px 15px rgba(253, 126, 20, 0.4);
        }

        .role-user {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }

        .clearance-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-section {
            background: rgba(0, 0, 0, 0.3);
            padding: 25px;
            border-radius: 15px;
            border-left: 4px solid #ff4444;
        }

        .info-section h3 {
            color: #ff6666;
            margin-bottom: 20px;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            padding-bottom: 10px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item label {
            display: block;
            font-weight: 600;
            color: #888;
            margin-bottom: 5px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item span {
            color: #ffffff;
            font-size: 1rem;
        }

        .salary {
            font-size: 1.3rem !important;
            font-weight: bold;
            color: #28a745 !important;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .hint-box {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .hint-box h4 {
            color: #00ffff;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .hint-box p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
        }

        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #ff4444;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid #ff4444;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.1);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="index.php" class="logo">🏢 TechCorp</a>
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

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['full_name'], 0, 2)); ?>
                </div>
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo strtoupper($user['role']); ?>
                </span>
                <span class="clearance-badge clearance-<?php echo str_replace('-', '-', $user['security_clearance']); ?>">
                    <?php echo strtoupper(str_replace('-', ' ', $user['security_clearance'])) . ' CLEARANCE'; ?>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="info-section">
                    <h3>👤 Personal Information</h3>
                    
                    <div class="info-item">
                        <label>Employee ID</label>
                        <span><?php echo htmlspecialchars($user['id']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Username</label>
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Email</label>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Phone</label>
                        <span><?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Emergency Contact</label>
                        <span><?php echo htmlspecialchars($user['emergency_contact'] ?: 'Not provided'); ?></span>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>🏢 Professional Information</h3>
                    
                    <div class="info-item">
                        <label>Department</label>
                        <span><?php echo htmlspecialchars($user['department']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Position</label>
                        <span><?php echo htmlspecialchars($user['position']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Annual Salary</label>
                        <span class="salary">$<?php echo number_format($user['salary'], 2); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Security Clearance</label>
                        <span><?php echo ucwords(str_replace('-', ' ', $user['security_clearance'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Member Since</label>
                        <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Last Login</label>
                        <span><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></span>
                    </div>
                </div>
                
                <div class="info-section full-width">
                    <h3>📍 Address & Notes</h3>
                    
                    <div class="info-item">
                        <label>Address</label>
                        <span><?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Notes</label>
                        <span><?php echo htmlspecialchars($user['notes'] ?: 'No notes'); ?></span>
                    </div>
                </div>
            </div>

            <div class="hint-box">
                <h4>💡 Lab Objective</h4>
                <p>
                    You are logged in as a regular user. Your goal is to find the hidden admin panel URL that is exposed somewhere in the application's client-side code. 
                    Check the page source, JavaScript, and browser console for <code style="color: #00ffff; background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 3px;">information disclosure</code> vulnerabilities.
                </p>
            </div>

            <div class="action-buttons">
                <a href="index.php" class="btn-primary">🏠 Back to Home</a>
                <a href="docs.php" class="btn-secondary">📚 View Documentation</a>
                <a href="logout.php" class="btn-secondary">🚪 Logout</a>
            </div>
        </div>
    </div>
</body>
</html>