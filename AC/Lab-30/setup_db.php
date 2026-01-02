<?php
// Lab 30: Database Setup Script
$host = 'localhost';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ac_lab30");
    $pdo->exec("USE ac_lab30");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        store_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create settings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings_for_low_stock_variants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        show_grade TINYINT(1) DEFAULT 1,
        show_product_title TINYINT(1) DEFAULT 1,
        show_variant_title TINYINT(1) DEFAULT 1,
        show_sku TINYINT(1) DEFAULT 1,
        show_lost_per_day TINYINT(1) DEFAULT 0,
        show_reorder_point TINYINT(1) DEFAULT 1,
        show_lead_time TINYINT(1) DEFAULT 0,
        show_need TINYINT(1) DEFAULT 1,
        show_depletion_days TINYINT(1) DEFAULT 0,
        show_depletion_date TINYINT(1) DEFAULT 0,
        show_next_due_date TINYINT(1) DEFAULT 0,
        show_stock TINYINT(1) DEFAULT 1,
        show_on_po TINYINT(1) DEFAULT 0,
        show_on_order TINYINT(1) DEFAULT 0,
        show_shopify_products_only TINYINT(1) DEFAULT 1,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_title VARCHAR(200) NOT NULL,
        variant_title VARCHAR(100),
        sku VARCHAR(50),
        stock INT DEFAULT 0,
        reorder_point INT DEFAULT 10,
        lead_time INT DEFAULT 7,
        grade CHAR(1) DEFAULT 'B',
        on_po INT DEFAULT 0,
        on_order INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Create activity log table
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Clear existing data
    $pdo->exec("DELETE FROM activity_log");
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM settings_for_low_stock_variants");
    $pdo->exec("DELETE FROM users");
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE settings_for_low_stock_variants AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE activity_log AUTO_INCREMENT = 1");
    
    // Insert users
    $users = [
        ['alice_shop', password_hash('password123', PASSWORD_DEFAULT), "Alice's Fashion Boutique", 'alice@fashion.example'],
        ['bob_tech', password_hash('password123', PASSWORD_DEFAULT), "Bob's Tech Store", 'bob@tech.example'],
        ['carol_home', password_hash('password123', PASSWORD_DEFAULT), "Carol's Home Goods", 'carol@home.example'],
        ['david_sports', password_hash('password123', PASSWORD_DEFAULT), "David's Sports Outlet", 'david@sports.example'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, store_name, email) VALUES (?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    
    // Insert settings (one per user with unique configurations)
    $settings = [
        [1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 0, 1], // Alice - Default view
        [2, 1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0], // Bob - Tech detailed
        [3, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0, 1, 1, 0, 1, 1], // Carol - Simple
        [4, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0], // David - All columns
    ];
    
    $stmt = $pdo->prepare("INSERT INTO settings_for_low_stock_variants 
        (user_id, show_grade, show_product_title, show_variant_title, show_sku, 
         show_lost_per_day, show_reorder_point, show_lead_time, show_need,
         show_depletion_days, show_depletion_date, show_next_due_date,
         show_stock, show_on_po, show_on_order, show_shopify_products_only) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($settings as $s) {
        $stmt->execute($s);
    }
    
    // Insert sample products
    $products = [
        // Alice's Fashion products
        [1, 'Summer Dress', 'Blue / Medium', 'DRESS-BLU-M', 5, 10, 5, 'A', 0, 15],
        [1, 'Designer Bag', 'Black Leather', 'BAG-BLK-01', 2, 5, 14, 'B', 5, 0],
        [1, 'Silk Scarf', 'Red Pattern', 'SCARF-RED-01', 8, 15, 7, 'C', 0, 10],
        // Bob's Tech products
        [2, 'Wireless Mouse', 'Black', 'MOUSE-WL-BLK', 15, 20, 10, 'A', 30, 0],
        [2, 'USB-C Hub', '7-Port', 'HUB-USB7-01', 3, 10, 21, 'B', 0, 20],
        [2, 'Mechanical Keyboard', 'RGB', 'KB-MECH-RGB', 7, 8, 14, 'A', 10, 0],
        // Carol's Home products
        [3, 'Cotton Towels', 'Set of 4', 'TOWEL-SET-4', 12, 25, 5, 'C', 0, 30],
        [3, 'Candle Set', 'Vanilla Scent', 'CANDLE-VAN-3', 4, 10, 7, 'B', 15, 0],
        // David's Sports products
        [4, 'Running Shoes', 'Size 10', 'SHOE-RUN-10', 6, 8, 10, 'A', 0, 12],
        [4, 'Yoga Mat', 'Purple', 'MAT-YOGA-PUR', 18, 15, 7, 'B', 0, 0],
        [4, 'Dumbbells', '10kg Pair', 'DUMB-10KG-PR', 2, 5, 30, 'A', 5, 0],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products 
        (user_id, product_title, variant_title, sku, stock, reorder_point, lead_time, grade, on_po, on_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }
    
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Setup - Lab 30</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        h1 { color: #7c3aed; margin-bottom: 1.5rem; font-size: 2rem; }
        .success { color: #059669; background: #d1fae5; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid #059669; }
        h2 { color: #333; font-size: 1.25rem; margin: 1.5rem 0 1rem; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; color: #7c3aed; font-weight: 600; }
        tr:hover td { background: #faf5ff; }
        code { background: #f3e8ff; color: #7c3aed; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.9rem; }
        .highlight { background: #fbbf24; color: #92400e; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class='card'>
        <h1>✅ Lab 30 Database Ready!</h1>
        <div class='success'>
            <strong>Database 'ac_lab30' created successfully!</strong><br>
            All tables created and sample data inserted.
        </div>
        
        <h2>👥 Test Accounts</h2>
        <table>
            <tr><th>Username</th><th>Password</th><th>Store Name</th><th>Settings ID</th></tr>
            <tr><td><code>alice_shop</code></td><td>password123</td><td>Alice's Fashion Boutique</td><td class='highlight'><code>1</code></td></tr>
            <tr><td><code>bob_tech</code></td><td>password123</td><td>Bob's Tech Store</td><td class='highlight'><code>2</code></td></tr>
            <tr><td><code>carol_home</code></td><td>password123</td><td>Carol's Home Goods</td><td class='highlight'><code>3</code></td></tr>
            <tr><td><code>david_sports</code></td><td>password123</td><td>David's Sports Outlet</td><td class='highlight'><code>4</code></td></tr>
        </table>

        <h2>🎯 IDOR Attack Vectors</h2>
        <table>
            <tr><th>Attack Method</th><th>Target Parameter</th></tr>
            <tr><td>Direct Settings Modification</td><td><code>settings_id</code></td></tr>
            <tr><td>Import Settings from User</td><td><code>import_from_id</code></td></tr>
        </table>
        
        <a href='index.php' class='btn'>🚀 Start the Lab</a>
    </div>
</body>
</html>";
    
} catch (PDOException $e) {
    echo "<h1>Database Error</h1><p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>
