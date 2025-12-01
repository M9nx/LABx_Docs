<?php
// Lab 4 Database Setup Script
echo "<h2>Lab 4 - SQL Injection UNION Attack Setup</h2>";

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lab4_sqli_union';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connected to MySQL server</p>";
    
    // Read and execute SQL file
    $sql = file_get_contents('database.sql');
    
    if ($sql === false) {
        throw new Exception("Could not read database.sql file");
    }
    
    // Split SQL statements and execute them
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p style='color: green;'>✓ Database and tables created successfully</p>";
    echo "<p style='color: green;'>✓ Sample data inserted</p>";
    
    // Test the connection to the new database
    $test_pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $test_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count records
    $stmt = $test_pdo->query("SELECT COUNT(*) as count FROM products");
    $product_count = $stmt->fetch()['count'];
    
    $stmt = $test_pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch()['count'];
    
    echo "<p style='color: blue;'>📊 Created $product_count products and $user_count users</p>";
    
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p><strong>Lab is ready to use:</strong></p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Main Product Catalog</a> - Contains the vulnerable SQL injection</li>";
    echo "<li><a href='login.php'>Admin Login</a> - Use discovered credentials here</li>";
    echo "</ul>";
    echo "<p><strong>Target:</strong> Find admin username and password using UNION-based SQL injection</p>";
    echo "<p><strong>Admin credentials:</strong> <code>administrator</code> / <code>admin123!@#</code> (discoverable via SQL injection)</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Make sure:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>database.sql file exists in the same directory</li>";
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lab 4 Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        h2 { color: #333; }
        code { background-color: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
</body>
</html>