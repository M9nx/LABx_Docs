<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - SecureShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 68, 68, 0.3); padding: 1rem 2rem; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .nav-links a:hover { color: #ff4444; }
        .container { max-width: 900px; margin: 0 auto; padding: 3rem 2rem; }
        .content { background: rgba(255, 255, 255, 0.05); padding: 2.5rem; border-radius: 15px; border: 1px solid rgba(255, 68, 68, 0.2); backdrop-filter: blur(10px); }
        .content h2 { color: #ff4444; margin-bottom: 1.5rem; font-size: 2rem; }
        .content p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
        .content strong { color: #ff6666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🛒 SecureShop</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="content">
            <h2>Get in Touch</h2>
            <p><strong>Email:</strong> support@secureshop.com</p>
            <p><strong>Phone:</strong> +1-555-SECURE</p>
            <p><strong>Address:</strong> 123 Security Blvd, Safe City, SC 12345</p>
        </div>
    </div>
</body>
</html>