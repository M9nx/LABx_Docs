<?php
/**
 * VULNERABLE SQL INJECTION LAB
 * 
 * WARNING: This code contains deliberate security vulnerabilities for educational purposes only!
 * DO NOT USE THIS CODE IN PRODUCTION ENVIRONMENTS!
 */

require_once 'config.php';

// Connect to database
$db = connect_db();

// Get search parameter
$category = isset($_GET['category']) ? $_GET['category'] : '';
$results = [];
$error = '';
$attack_detected = false;

// Perform search if category is provided
if (!empty($category)) {
    // Build query - by default only show released products
    $query = "SELECT * FROM products WHERE category = '$category' AND is_released = 1";
    
    $result = $db->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $result->free();
        
        // Check if attack was successful (unreleased products found)
        $unreleased_count = 0;
        $total_products = count($results);
        
        foreach ($results as $product) {
            if (!$product['is_released']) {
                $unreleased_count++;
            }
        }
        
        // Detect SQL injection attack with improved logic
        $has_sql_chars = (strpos($category, "'") !== false || strpos($category, '"') !== false);
        $has_sql_keywords = preg_match('/(\bor\b|\band\b|\bunion\b|\bselect\b|\bwhere\b|--|#|\/\*|\*\/)/i', $category);
        $abnormal_results = false;
        
        // Check for abnormal result patterns
        if ($has_sql_chars || $has_sql_keywords) {
            // If SQL injection patterns detected, check if results are suspicious
            if ($total_products > 1 && $unreleased_count > 0) {
                $abnormal_results = true;
            }
            // Also detect if we get way more results than expected for a normal category
            if ($total_products > 3) {
                $abnormal_results = true;
            }
        }
        
        // Attack is successful if:
        // 1. We found unreleased products AND suspicious input, OR
        // 2. We detected SQL injection patterns with abnormal results
        if (($unreleased_count > 0 && ($has_sql_chars || $has_sql_keywords)) || $abnormal_results) {
            $attack_detected = true;
        }
    } else {
        $error = "Query failed: " . $db->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Lab - Product Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .search-form {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .released {
            background-color: #d4edda;
        }
        .unreleased {
            background-color: #f8d7da;
            font-weight: bold;
        }
        .query-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .exploit-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-released {
            background-color: #28a745;
            color: white;
        }
        .status-unreleased {
            background-color: #dc3545;
            color: white;
        }
        .category-filters {
            margin-bottom: 15px;
        }
        .filter-btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px 8px 4px 0;
            background-color: #f8f9fa;
            color: #007bff;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .filter-btn:hover {
            background-color: #e9ecef;
            text-decoration: none;
        }
        .filter-btn.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Search</h1>

        <div class="search-form">
            <h3>Refine your search:</h3>
            <div class="category-filters">
                <a href="?category=" class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
                <a href="?category=electronics" class="filter-btn <?php echo $category === 'electronics' ? 'active' : ''; ?>">Electronics</a>
                <a href="?category=computers" class="filter-btn <?php echo $category === 'computers' ? 'active' : ''; ?>">Computers</a>
                <a href="?category=audio" class="filter-btn <?php echo $category === 'audio' ? 'active' : ''; ?>">Audio & Music</a>
                <a href="?category=clothing" class="filter-btn <?php echo $category === 'clothing' ? 'active' : ''; ?>">Clothing & Accessories</a>
            </div>
            
            <form method="GET" action="" style="margin-top: 15px;">
                <div class="form-group">
                    <label for="category">Or search by category name:</label>
                    <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" placeholder="Enter category">
                </div>
                <button type="submit">Search Products</button>
            </form>
        </div>

        <?php if ($attack_detected): ?>
        <div class="warning" style="background-color: #ff6b6b; color: white; border: none;">
            <strong>🎯 Congratulations, you solved the lab!</strong>
            <br>SQL injection attack successful! You've bypassed security controls and accessed hidden data.
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
        <h3>Search Results (<?php echo count($results); ?> products found)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $product): ?>
                <tr class="<?php echo $product['is_released'] ? 'released' : 'unreleased'; ?>">
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td>
                        <span class="status-badge <?php echo $product['is_released'] ? 'status-released' : 'status-unreleased'; ?>">
                            <?php echo $product['is_released'] ? 'Released' : 'Unreleased'; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php elseif (!empty($category)): ?>
        <div style="text-align: center; padding: 40px; color: #6c757d;">
            <h3>No products found for "<?php echo htmlspecialchars($category); ?>"</h3>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #6c757d;">
            <h3>Use the search form above to find products</h3>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close database connection
close_db();
?>