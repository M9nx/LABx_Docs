<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureShop - Online Shopping</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .welcome {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .product {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-status {
            float: right;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SecureShop</h1>
        <div class="login-status">
            <?php if (isset($_SESSION['user_id'])): ?>
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
                <a href="logout.php" style="color: #ccc;">Logout</a>
            <?php else: ?>
                <a href="login.php" style="color: #ccc;">Login</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="nav">
        <a href="lab-description.php" style="color: #007bff;">← Back to lab description</a>
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">My Profile</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Welcome to SecureShop!</h2>
            <p>Your trusted online shopping destination. Browse our wide selection of products and enjoy secure shopping.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <p><a href="login.php">Login</a> to access your account and view personalized recommendations.</p>
            <?php endif; ?>
        </div>

        <h3>Featured Products</h3>
        <div class="products">
            <div class="product">
                <h4>Laptop Pro</h4>
                <p>High-performance laptop for professionals</p>
                <p><strong>$1,299.99</strong></p>
            </div>
            <div class="product">
                <h4>Wireless Headphones</h4>
                <p>Premium noise-canceling headphones</p>
                <p><strong>$199.99</strong></p>
            </div>
            <div class="product">
                <h4>Smart Watch</h4>
                <p>Feature-rich smartwatch with health tracking</p>
                <p><strong>$299.99</strong></p>
            </div>
            <div class="product">
                <h4>Gaming Mouse</h4>
                <p>Precision gaming mouse with RGB lighting</p>
                <p><strong>$79.99</strong></p>
            </div>
        </div>
    </div>
</body>
</html>