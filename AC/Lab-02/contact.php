<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - TechCorp</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 68, 68, 0.3); padding: 1rem 2rem; }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .nav-links a:hover { color: #ff4444; }
        .container { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; }
        .content { background: rgba(255, 255, 255, 0.05); padding: 3rem; border-radius: 20px; border: 1px solid rgba(255, 68, 68, 0.2); backdrop-filter: blur(20px); }
        h1 { color: #ff4444; text-align: center; margin-bottom: 2rem; }
        p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
        strong { color: #ff6666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-container">
            <a href="index.php" class="logo">🏢 TechCorp</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="solutions.php">Solutions</a>
                <a href="services.php">Services</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="content">
            <h1>Contact TechCorp</h1>
            <p><strong>Email:</strong> info@techcorp.com</p>
            <p><strong>Phone:</strong> +1-555-TECHCORP</p>
            <p><strong>Address:</strong> 100 Corporate Blvd, Tech City, TC 12345</p>
        </div>
    </div>
</body>
</html>