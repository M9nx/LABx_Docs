<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - SecureShop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .header { background-color: #333; color: white; padding: 1rem; text-align: center; }
        .nav { background-color: #444; padding: 0.5rem; text-align: center; }
        .nav a { color: white; text-decoration: none; margin: 0 1rem; padding: 0.5rem; }
        .nav a:hover { background-color: #555; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .products { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .product { background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Products</h1>
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
        <h2>Our Products</h2>
        <div class="products">
            <div class="product">
                <h3>Laptop Pro</h3>
                <p>High-performance laptop for professionals</p>
                <p><strong>$1,299.99</strong></p>
            </div>
            <div class="product">
                <h3>Wireless Headphones</h3>
                <p>Premium noise-canceling headphones</p>
                <p><strong>$199.99</strong></p>
            </div>
        </div>
    </div>
</body>
</html>