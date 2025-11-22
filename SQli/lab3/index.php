<?php
require_once 'config.php';

// Get category parameter - VULNERABLE TO SQL INJECTION
$category = isset($_GET['category']) ? $_GET['category'] : '';
$success_detected = false;

// Check for successful UNION payload (5 NULL values)
if (!empty($category)) {
    // Detect successful UNION injection with 5 NULLs
    $category_clean = preg_replace('/\s+/', ' ', strtolower($category));
    if (stripos($category_clean, 'union') !== false && 
        (preg_match('/null\s*,\s*null\s*,\s*null\s*,\s*null\s*,\s*null/i', $category_clean) ||
         substr_count($category_clean, 'null') >= 5)) {
        $success_detected = true;
    }
}

// Vulnerable SQL query - directly concatenating user input
if (!empty($category)) {
    // VULNERABLE CODE: Direct string concatenation without sanitization
    $sql = "SELECT p.id, p.name, p.description, p.price, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE c.name = '" . $category . "'";
    
    try {
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
        $products = [];
    }
} else {
    // Show all products if no category selected
    $sql = "SELECT p.id, p.name, p.description, p.price, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id";
    $stmt = $pdo->query(query: $sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all categories for the filter dropdown
$categories_sql = "SELECT * FROM categories";
$categories_stmt = $pdo->query($categories_sql);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Shop - Lab 3</title>
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .target {
            background: #ffebcd;
            border: 2px solid #daa520;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .filter-section {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .filter-section h3 {
            margin-top: 0;
            color: #2c5aa0;
        }
        .category-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-links a {
            padding: 8px 16px;
            background: #2c5aa0;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .category-links a:hover {
            background: #1e3d6f;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .product-name {
            font-weight: bold;
            font-size: 1.2em;
            color: #333;
            margin-bottom: 8px;
        }
        .product-category {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        .product-description {
            color: #555;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .product-price {
            font-size: 1.1em;
            font-weight: bold;
            color: #2c5aa0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin: 20px 0;
        }
        .success-alert {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border: 2px solid #c3e6cb;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 1.2em;
            text-align: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { background-color: #d4edda; }
            50% { background-color: #b8e6c1; }
            100% { background-color: #d4edda; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Vulnerable Online Shop</h1>
            <h2>Lab 3: SQL Injection UNION Attack</h2>
        </div>

        <div class="target">
            <h3>🎯 TARGET</h3>
            <p><strong>Determine the number of columns returned by the query using UNION-based SQL injection.</strong></p>
        </div>

        <?php if ($success_detected): ?>
            <div class="success-alert">
                <strong>🎉 CONGRATULATIONS! 🎉</strong><br><br>
                <strong>✅ YOU SOLVED THE LAB!</strong><br>
                You successfully discovered that the query returns 5 columns using UNION SELECT with NULL values.
            </div>
        <?php endif; ?>

        <div class="filter-section">
            <h3>Product Categories</h3>
            <div class="category-links">
                <a href="index.php">All Products</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?= urlencode($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="error">
                <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php if (empty($products)): ?>
                <p>No products found for the selected category.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-name"><?= htmlspecialchars($product['name'] ?? '') ?></div>
                        <div class="product-category">Category: <?= htmlspecialchars($product['category_name'] ?? '') ?></div>
                        <div class="product-description"><?= htmlspecialchars($product['description'] ?? '') ?></div>
                        <div class="product-price">$<?= isset($product['price']) ? number_format($product['price'], 2) : '0.00' ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>