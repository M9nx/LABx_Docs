<?php
// Reset lab progress
require_once '../progress.php';
resetLab(4);

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS lab4_rolemod");
    $pdo->exec("USE lab4_rolemod");
    
    // Drop existing tables
    $pdo->exec("DROP TABLE IF EXISTS audit_log");
    $pdo->exec("DROP TABLE IF EXISTS users");
    
    // Create users table
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        roleid INT NOT NULL DEFAULT 1,
        department VARCHAR(50),
        phone VARCHAR(20),
        address TEXT,
        notes TEXT,
        api_key VARCHAR(64),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )");
    
    // Create audit log
    $pdo->exec("CREATE TABLE audit_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(100),
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert users
    $pdo->exec("INSERT INTO users (username, password, email, full_name, roleid, department, phone, address, notes, api_key) VALUES
        ('administrator', 'admin123', 'admin@rolelab.local', 'System Administrator', 2, 'IT Security', '+1-555-0100', '100 Admin Tower', 'Master admin', 'sk_admin_a1b2c3d4'),
        ('carlos', 'carlos123', 'carlos@rolelab.local', 'Carlos Rodriguez', 1, 'Marketing', '+1-555-0201', '201 Marketing Ave', 'Target user', 'sk_carlos_q7w8e9r0'),
        ('wiener', 'peter', 'wiener@rolelab.local', 'Peter Wiener', 1, 'Development', '+1-555-0301', '301 Developer Lane', 'Attacker account', 'sk_wiener_z1x2c3v4'),
        ('alice', 'alice123', 'alice@rolelab.local', 'Alice Johnson', 1, 'Finance', '+1-555-0401', '401 Finance Blvd', 'Regular user', 'sk_alice_p0o9i8u7'),
        ('bob', 'bob123', 'bob@rolelab.local', 'Bob Smith', 1, 'HR', '+1-555-0501', '501 HR Street', 'Regular user', 'sk_bob_m1n2b3v4'),
        ('manager', 'manager123', 'manager@rolelab.local', 'Sarah Manager', 2, 'Operations', '+1-555-0601', '601 Operations Center', 'Admin user', 'sk_manager_q1a2z3w4')
    ");
    
    echo "Database lab4_rolemod created successfully!\n\n";
    
    // Verify
    $result = $pdo->query("SELECT id, username, email, roleid FROM users ORDER BY id");
    echo "Users in database:\n";
    echo str_repeat('-', 60) . "\n";
    printf("%-4s %-15s %-25s %-8s\n", "ID", "Username", "Email", "RoleID");
    echo str_repeat('-', 60) . "\n";
    foreach ($result as $row) {
        printf("%-4s %-15s %-25s %-8s\n", $row['id'], $row['username'], $row['email'], $row['roleid']);
    }
    echo str_repeat('-', 60) . "\n";
    echo "\nLab 4 is ready! Login with wiener:peter\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>

