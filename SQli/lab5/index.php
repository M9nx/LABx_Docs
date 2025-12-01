<?php
require_once 'config.php';

// Start timing for performance monitoring
$start_time = microtime(true);

// Get or generate tracking ID and perform vulnerable analytics tracking
$trackingId = getTrackingId();
$success_detected = false;

// Check if time delay attack was successful (for educational feedback)
if (isset($_COOKIE['TrackingId'])) {
    $cookie_value = $_COOKIE['TrackingId'];
    // Detect common time delay payloads
    if (stripos($cookie_value, 'sleep') !== false || 
        stripos($cookie_value, 'delay') !== false ||
        stripos($cookie_value, 'benchmark') !== false ||
        stripos($cookie_value, 'waitfor') !== false) {
        $success_detected = true;
    }
}

// VULNERABLE: Track user activity using the tracking ID
// This is where the blind SQL injection vulnerability exists
trackUserActivity($trackingId);

// Get products for display
$conn = getConnection();
$query = "SELECT * FROM products ORDER BY category, name";
$result = $conn->query($query);
$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get categories for navigation
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

$conn->close();

// Calculate page load time
$end_time = microtime(true);
$load_time = round(($end_time - $start_time), 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureShop - Lab 5 | Blind SQL Injection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .target {
            background: #ffebcd;
            border: 2px solid #daa520;
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .nav-bar {
            background-color: #2c3e50;
            padding: 15px;
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #34495e;
            transition: background-color 0.3s;
        }
        .nav-links a:hover {
            background-color: #4a6741;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-name {
            font-size: 1.1em;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .product-category {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .product-description {
            color: #555;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        .product-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }
        .product-stock {
            font-size: 0.85em;
            color: #95a5a6;
        }
        .footer {
            background-color: #34495e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .tracking-info {
            background-color: #ecf0f1;
            padding: 10px;
            margin: 20px;
            border-radius: 4px;
            font-size: 0.9em;
            color: #7f8c8d;
        }
        .success-alert {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            margin: 20px;
            border: 2px solid #c3e6cb;
            border-radius: 8px;
            text-align: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { background-color: #d4edda; }
            50% { background-color: #b8e6c1; }
            100% { background-color: #d4edda; }
        }
        .performance {
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            color: #6c757d;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ SecureShop</h1>
            <p>Your Trusted Online Store - Lab 5: Blind SQL Injection</p>
        </div>

        <div class="target">
            <h3>🎯 TARGET</h3>
            <p><strong>Exploit the blind SQL injection vulnerability in the tracking cookie to cause a 10 second delay.</strong></p>
        </div>

        <?php if ($success_detected): ?>
            <div class="success-alert">
                <strong>🎉 CONGRATULATIONS! 🎉</strong><br><br>
                <strong>✅ YOU SOLVED THE LAB!</strong><br>
                You successfully triggered a time delay using blind SQL injection in the TrackingId cookie!
            </div>
        <?php endif; ?>

        <div class="nav-bar">
            <div class="nav-links">
                <a href="#">🏠 Home</a>
                <?php foreach($categories as $category): ?>
                    <a href="#<?= strtolower($category) ?>"><?= htmlspecialchars($category) ?></a>
                <?php endforeach; ?>
                <a href="#">🛒 Cart (0)</a>
                <a href="#">👤 Account</a>
            </div>
        </div>

        <div class="products-grid">
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="product-description"><?= htmlspecialchars($product['description']) ?></div>
                    <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                    <div class="product-stock"><?= $product['stock_quantity'] ?> in stock</div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="tracking-info">
            <strong>Analytics:</strong> Session ID: <?= htmlspecialchars(substr($trackingId, 0, 16)) ?>... | 
            Page load time: <?= $load_time ?>s
        </div>

        <div class="footer">
            <p>&copy; 2024 SecureShop - Lab 5: Blind SQL Injection with Time Delays</p>
            <p>Educational Purpose Only - Tracking ID: <?= htmlspecialchars($trackingId) ?></p>
        </div>

        <div class="performance">
            Response Time: <?= $load_time ?> seconds
        </div>
    </div>
</body>
</html>