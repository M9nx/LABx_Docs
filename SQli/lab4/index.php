<?php
require_once 'config.php';

// Get all available categories for the filter
$conn = getConnection();
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Handle product filtering - VULNERABLE CODE
$products = [];
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$success_detected = false;

// Check for successful UNION payload targeting users table
if (!empty($selected_category)) {
    $category_clean = preg_replace('/\s+/', ' ', strtolower($selected_category));
    if (stripos($category_clean, 'union') !== false && 
        (stripos($category_clean, 'users') !== false || 
         (stripos($category_clean, 'username') !== false && stripos($category_clean, 'password') !== false))) {
        $success_detected = true;
    }
}

if (!empty($selected_category)) {
    // VULNERABLE: Direct string concatenation without sanitization
    $query = "SELECT name, description, price FROM products WHERE category = '" . $selected_category . "'";
} else {
    $query = "SELECT name, description, price FROM products";
}

try {
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
} catch (Exception $e) {
    $error_message = "Database error: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 4 - Product Catalog | SQL Injection UNION Attack</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .product-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .product-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .product-description {
            color: #666;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #27ae60;
        }
        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        select, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .login-link {
            float: right;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
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
    <div class="header">
        <h1>Product Catalog - Lab 4</h1>
        <p>SQL Injection UNION Attack</p>
        <a href="login.php" class="login-link">Admin Login</a>
        <div style="clear: both;"></div>
    </div>

    <div class="target">
        <h3>🎯 TARGET</h3>
        <p><strong>Use UNION-based SQL injection to retrieve admin credentials from the users table, then login as administrator.</strong></p>
    </div>

    <?php if ($success_detected): ?>
        <div class="success-alert">
            <strong>🎉 CONGRATULATIONS! 🎉</strong><br><br>
            <strong>✅ YOU SOLVED THE LAB!</strong><br>
            You successfully extracted user credentials using UNION-based SQL injection.<br>
            Now use the admin credentials to login at the <a href="login.php" style="color: #155724; text-decoration: underline;">Admin Login</a> page!
        </div>
    <?php endif; ?>

    <div class="filter-section">
        <h3>Filter Products by Category</h3>
        <form method="GET" class="filter-form">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category); ?>" 
                            <?php echo ($selected_category === $category) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
            <a href="index.php" style="margin-left: 10px; text-decoration: none; color: #666;">[Clear Filter]</a>
        </form>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-description"><?php echo htmlspecialchars($product['description']); ?></div>
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="product-card">
                <div class="product-name">No products found</div>
                <div class="product-description">Try selecting a different category or clear the filter.</div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>