<?php
require_once 'config.php';
require_once '../progress.php';

try {
    // Read and execute SQL file
    $sql = file_get_contents('database_setup.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    // Reset lab progress
    resetLab(16);
    
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Lab 16 Setup Complete</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #e0e0e0;
        }
        .container {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
        }
        h1 { color: #9370DB; margin-bottom: 1rem; }
        .success { color: #00ff00; font-size: 4rem; }
        p { color: #aaa; line-height: 1.8; }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn:hover { transform: translateY(-2px); }
        .users-table {
            margin-top: 2rem;
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th, .users-table td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
            text-align: left;
        }
        .users-table th {
            background: rgba(106, 90, 205, 0.2);
            color: #9370DB;
        }
        .role-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.75rem;
        }
        .creator { background: rgba(0,255,0,0.2); color: #00ff00; }
        .no-access { background: rgba(255,68,68,0.2); color: #ff6666; }
        .has-access { background: rgba(0,200,255,0.2); color: #00ccff; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success'>✓</div>
        <h1>Lab 16 Setup Complete!</h1>
        <p>Database 'ac_lab16' has been created with sample slowvotes and users.</p>
        
        <table class='users-table'>
            <tr>
                <th>Username</th>
                <th>Password</th>
                <th>Role</th>
            </tr>
            <tr>
                <td>alice</td>
                <td>alice123</td>
                <td><span class='role-badge creator'>Poll Creator (User A)</span></td>
            </tr>
            <tr>
                <td>bob</td>
                <td>bob123</td>
                <td><span class='role-badge no-access'>No Permission (User B)</span></td>
            </tr>
            <tr>
                <td>charlie</td>
                <td>charlie123</td>
                <td><span class='role-badge has-access'>Has Permission (User C)</span></td>
            </tr>
            <tr>
                <td>admin</td>
                <td>admin123</td>
                <td><span class='role-badge'>Administrator</span></td>
            </tr>
        </table>
        
        <a href='index.php' class='btn'>Go to Lab →</a>
    </div>
</body>
</html>";
    
} catch(PDOException $e) {
    echo "<h1>Setup Error</h1><p style='color:red;'>" . $e->getMessage() . "</p>";
}
?>
