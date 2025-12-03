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
    <title>My Profile - SecureShop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .nav {
            background-color: #444;
            padding: 0.5rem;
            text-align: center;
        }
        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem;
        }
        .nav a:hover {
            background-color: #555;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .profile-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .info-item {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .info-item label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 0.5rem;
        }
        .info-item span {
            color: #666;
        }
        .role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .role-admin {
            background-color: #dc3545;
            color: white;
        }
        .role-user {
            background-color: #28a745;
            color: white;
        }
        .full-width {
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>My Profile</h1>
    </div>
    
    <div class="nav">
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
                <span class="role-badge role-<?php echo $user['role']; ?>">
                    <?php echo $user['role']; ?>
                </span>
            </div>
            
            <div class="profile-info">
                <div class="info-item">
                    <label>User ID:</label>
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
                
                <div class="info-item full-width">
                    <label>Address:</label>
                    <span><?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></span>
                </div>
                
                <div class="info-item full-width">
                    <label>Account Notes:</label>
                    <span><?php echo htmlspecialchars($user['notes'] ?: 'No notes'); ?></span>
                </div>
                
                <div class="info-item">
                    <label>Member Since:</label>
                    <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                </div>
                
                <div class="info-item">
                    <label>Account Type:</label>
                    <span><?php echo ucfirst($user['role']); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>