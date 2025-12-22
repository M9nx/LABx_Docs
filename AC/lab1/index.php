<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 1: Unprotected Admin Functionality</title>
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
            max-width: 1200px;
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

        .login-status {
            color: #888;
            font-size: 0.9rem;
        }

        .login-status a {
            color: #ff4444;
            text-decoration: none;
        }

        .hero {
            text-align: center;
            margin-bottom: 50px;
        }

        .lab-title {
            color: #ff4444;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .lab-subtitle {
            color: #cccccc;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }

        .difficulty-badge {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
        }

        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #ff6666;
            font-size: 1.5rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .product-card {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: #ff4444;
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.2);
        }

        .product-emoji {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .product-name {
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-price {
            color: #ff6666;
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-desc {
            color: #999;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .hint-box {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
        }

        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #ff4444;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid #ff4444;
        }

        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.1);
            transform: translateY(-3px);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-link {
            color: #ff6666;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 102, 102, 0.1);
            transform: translateY(-1px);
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
            
            .lab-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="index.php" class="logo">🛒 SecureShop</a>
            <div class="nav-links">
                <a href="lab-description.php">← Lab Info</a>
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <span class="login-status">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
                        <a href="logout.php">Logout</a>
                    </span>
                <?php else: ?>
                    <a href="login.php" class="btn-primary" style="padding: 8px 20px;">Login</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero">
            <h1 class="lab-title">🔓 SecureShop</h1>
            <p class="lab-subtitle">Online Shopping Platform</p>
            <div class="difficulty-badge">Apprentice Level</div>
        </div>

        <div class="lab-card">
            <h2 class="section-title">🎯 Your Mission</h2>
            <p style="color: #cccccc; line-height: 1.8; margin-bottom: 15px;">
                Welcome to SecureShop! This is a vulnerable e-commerce application with an 
                <strong style="color: #ff4444;">unprotected admin panel</strong>. 
                Your objective is to discover and access the hidden administrator functionality.
            </p>
            <p style="color: #cccccc; line-height: 1.8;">
                <strong>Goal:</strong> Find the admin panel and delete the user "carlos" to complete the lab.
            </p>
        </div>

        <div class="hint-box">
            <h4>💡 Hint</h4>
            <p>
                Some web applications disclose sensitive paths in their <code style="color: #00ffff; background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 3px;">robots.txt</code> file. 
                This file tells search engines which paths should not be indexed, but attackers can use it to discover hidden areas.
            </p>
        </div>

        <div class="lab-card">
            <h2 class="section-title">🛍️ Featured Products</h2>
            <div class="products-grid">
                <div class="product-card">
                    <div class="product-emoji">💻</div>
                    <h3 class="product-name">Laptop Pro X1</h3>
                    <div class="product-price">$1,299.99</div>
                    <p class="product-desc">High-performance laptop for professionals</p>
                </div>
                <div class="product-card">
                    <div class="product-emoji">📱</div>
                    <h3 class="product-name">SmartPhone Ultra</h3>
                    <div class="product-price">$899.99</div>
                    <p class="product-desc">Latest flagship smartphone with 5G</p>
                </div>
                <div class="product-card">
                    <div class="product-emoji">🎧</div>
                    <h3 class="product-name">Wireless Headphones</h3>
                    <div class="product-price">$249.99</div>
                    <p class="product-desc">Premium noise-canceling headphones</p>
                </div>
                <div class="product-card">
                    <div class="product-emoji">⌚</div>
                    <h3 class="product-name">Smart Watch</h3>
                    <div class="product-price">$399.99</div>
                    <p class="product-desc">Feature-rich smartwatch with health tracking</p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn-primary">🔐 Login to Account</a>
            <?php else: ?>
                <a href="profile.php" class="btn-primary">👤 View Profile</a>
            <?php endif; ?>
            <a href="products.php" class="btn-secondary">🛒 Browse All Products</a>
            <a href="docs.php" class="btn-secondary">📚 View Documentation</a>
        </div>

        <div class="navigation">
            <a href="../" class="nav-link">🏠 AC Labs Home</a>
            <a href="lab-description.php" class="nav-link">📋 Lab Description</a>
            <a href="../lab2/" class="nav-link">Lab 2 →</a>
        </div>
    </div>
</body>
</html>