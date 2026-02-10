<?php
/**
 * Insecure Deserialization Labs - Setup All Databases
 * Creates/resets all lab databases at once
 */

require_once __DIR__ . '/../db-config.php';

$creds = getDbCredentials();

if (!$creds['configured']) {
    header('Location: ../index.php');
    exit;
}

$results = [];

// Lab 1: Modifying Serialized Objects
$lab1_result = setupLab1($creds);
$results[] = $lab1_result;

// Reset progress database
$progress_result = resetProgress($creds);
$results[] = $progress_result;

function setupLab1($creds) {
    $dbname = 'deserial_lab1';
    try {
        $pdo = new PDO("mysql:host={$creds['host']}", $creds['user'], $creds['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        
        $pdo->exec("DROP TABLE IF EXISTS users");
        $pdo->exec("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $users = [
            ['administrator', 'admin_secret_pass', 'admin@seriallab.com', 'Administrator', 'admin'],
            ['carlos', 'carlos123', 'carlos@example.com', 'Carlos Rodriguez', 'user'],
            ['wiener', 'peter', 'wiener@example.com', 'Peter Wiener', 'user'],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
        foreach ($users as $u) {
            $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3], $u[4]]);
        }
        
        return ['lab' => 'Lab 1', 'status' => 'success', 'message' => 'Database created successfully'];
    } catch (PDOException $e) {
        return ['lab' => 'Lab 1', 'status' => 'error', 'message' => $e->getMessage()];
    }
}

function resetProgress($creds) {
    $dbname = 'id_progress';
    try {
        $pdo = new PDO("mysql:host={$creds['host']}", $creds['user'], $creds['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS solved_labs (
                lab_number INT PRIMARY KEY,
                solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                reset_count INT DEFAULT 0
            )
        ");
        
        $pdo->exec("TRUNCATE TABLE solved_labs");
        
        return ['lab' => 'Progress DB', 'status' => 'success', 'message' => 'Progress reset successfully'];
    } catch (PDOException $e) {
        return ['lab' => 'Progress DB', 'status' => 'error', 'message' => $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup All Databases - Insecure Deserialization</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
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
        h1 { color: #f97316; margin-bottom: 0.5rem; font-size: 2rem; }
        .subtitle { color: #666; margin-bottom: 2rem; }
        .result-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }
        .result-success { background: #d1fae5; border-left: 4px solid #10b981; }
        .result-error { background: #fee2e2; border-left: 4px solid #ef4444; }
        .result-icon { font-size: 1.5rem; margin-right: 1rem; }
        .result-content { flex: 1; }
        .result-lab { font-weight: 600; color: #333; }
        .result-message { font-size: 0.9rem; color: #666; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: all 0.3s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(249, 115, 22, 0.4); }
    </style>
</head>
<body>
    <div class="card">
        <h1>📦 Database Setup Complete</h1>
        <p class="subtitle">All Insecure Deserialization lab databases have been processed.</p>
        
        <?php foreach ($results as $result): ?>
        <div class="result-item result-<?php echo $result['status']; ?>">
            <span class="result-icon"><?php echo $result['status'] === 'success' ? '✅' : '❌'; ?></span>
            <div class="result-content">
                <div class="result-lab"><?php echo htmlspecialchars($result['lab']); ?></div>
                <div class="result-message"><?php echo htmlspecialchars($result['message']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <a href="index.php" class="btn">← Back to Labs</a>
    </div>
</body>
</html>
