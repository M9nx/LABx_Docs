<?php
/**
 * Lab 3 Database Setup Script
 * Run this file to automatically create the database and tables
 */

$host = 'localhost';
$username = 'root';
$password = '';

echo "<h2>Lab 3 Database Setup</h2>";

try {
    // Connect to MySQL server (without specific database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Connected to MySQL server</p>";
    
    // Read and execute the SQL file
    $sql_content = file_get_contents('database.sql');
    
    if ($sql_content === false) {
        throw new Exception("Could not read database.sql file");
    }
    
    // Split SQL statements and execute them
    $statements = explode(';', $sql_content);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p>✅ Database and tables created successfully!</p>";
    echo "<p>✅ Sample data inserted!</p>";
    echo "<p><strong>Setup complete!</strong> You can now access the lab at <a href='index.php'>index.php</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>