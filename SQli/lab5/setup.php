<?php
// Lab 5 Database Setup Script - Blind SQL Injection with Time Delays
echo "<h2>Lab 5 - Blind SQL Injection with Time Delays Setup</h2>";

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'lab5_blind_sqli';

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
    
    $stmt = $test_pdo->query("SELECT COUNT(*) as count FROM analytics");
    $analytics_count = $stmt->fetch()['count'];
    
    $stmt = $test_pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch()['count'];
    
    echo "<p style='color: blue;'>📊 Created $product_count products, $analytics_count analytics records, and $user_count users</p>";
    
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p><strong>Lab is ready to use:</strong></p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Main Shop Page</a> - Contains the vulnerable tracking cookie</li>";
    echo "<li>Use browser dev tools or Burp Suite to modify the TrackingId cookie</li>";
    echo "</ul>";
    echo "<p><strong>Target:</strong> Cause a 10 second delay using blind SQL injection</p>";
    echo "<p><strong>Vulnerable Cookie:</strong> TrackingId (automatically set when you visit the page)</p>";
    echo "<p><strong>Example Payload:</strong> <code>x'||SLEEP(10)--</code> (MySQL) or <code>x'+WAITFOR+DELAY+'00:00:10'--</code> (SQL Server style)</p>";
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
    <title>Lab 5 Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        h2 { color: #333; }
        code { background-color: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        ul { margin-top: 10px; }
    </style>
</head>
<body>
</body>
</html>