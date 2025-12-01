<?php
// SECURE VERSION - For educational comparison
// This file demonstrates how the vulnerable code should be properly fixed

require_once 'config.php';

// Get all available categories for the filter (this query is safe)
$conn = getConnection();
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// SECURE: Handle product filtering with proper validation and parameterized queries
$products = [];
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$error_message = '';

// Input validation using whitelist approach
$valid_categories = array_merge([''], $categories); // Include empty string for "all categories"

if (!empty($selected_category) && !in_array($selected_category, $valid_categories)) {
    // Invalid category provided - reject and show error
    $selected_category = '';
    $error_message = "Invalid category selected. Please choose from the available options.";
}

// Secure query construction using prepared statements
if (!empty($selected_category)) {
    // SECURE: Using prepared statements with parameter binding
    $query = "SELECT name, description, price FROM products WHERE category = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("s", $selected_category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        $stmt->close();
    } else {
        $error_message = "Database query preparation failed.";
    }
} else {
    // Safe query for all products
    $query = "SELECT name, description, price FROM products";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 4 - Secure Version | Product Catalog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #27ae60;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .secure-notice {
            background-color: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            background-color: #27ae60;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #229954;
        }
        .vulnerable-link {
            float: right;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔒 Secure Product Catalog - Lab 4</h1>
        <p>This is the SECURE version with proper SQL injection protection</p>
        <a href="index.php" class="vulnerable-link">Go to Vulnerable Version</a>
        <div style="clear: both;"></div>
    </div>

    <div class="secure-notice">
        <strong>✅ SECURE VERSION</strong><br>
        This version implements proper security measures:
        <ul>
            <li>✓ Input validation using whitelist approach</li>
            <li>✓ Parameterized queries (prepared statements)</li>
            <li>✓ Proper error handling</li>
            <li>✓ SQL injection prevention</li>
        </ul>
    </div>

    <?php if ($error_message): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
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
            <a href="index_secure.php" style="margin-left: 10px; text-decoration: none; color: #666;">[Clear Filter]</a>
        </form>
    </div>

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

    <div style="margin-top: 40px; padding: 20px; background-color: white; border-radius: 8px;">
        <h3>Security Features Implemented:</h3>
        <ol>
            <li><strong>Input Validation:</strong> Only accepts predefined category values</li>
            <li><strong>Prepared Statements:</strong> SQL structure separated from user data</li>
            <li><strong>Parameter Binding:</strong> User input treated as data, not code</li>
            <li><strong>Error Handling:</strong> Safe error messages without information disclosure</li>
            <li><strong>Output Encoding:</strong> All displayed data properly escaped</li>
        </ol>
        
        <h4>Try SQL injection attacks - they won't work!</h4>
        <p>Example payloads that would fail:</p>
        <ul>
            <li><code>Electronics' UNION SELECT username,password,'x' FROM users--</code></li>
            <li><code>'; DROP TABLE products; --</code></li>
            <li><code>' OR 1=1 --</code></li>
        </ul>
    </div>

    <div style="margin-top: 20px; text-align: center; color: #666; font-size: 12px;">
        <p>Lab 4 - Secure Version | Educational Purpose Only</p>
    </div>
</body>
</html>