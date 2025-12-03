<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - SecureShop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .header { background-color: #333; color: white; padding: 1rem; text-align: center; }
        .nav { background-color: #444; padding: 0.5rem; text-align: center; }
        .nav a { color: white; text-decoration: none; margin: 0 1rem; padding: 0.5rem; }
        .nav a:hover { background-color: #555; }
        .container { max-width: 800px; margin: 0 auto; padding: 2rem; }
        .content { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contact Us</h1>
    </div>
    <div class="nav">
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