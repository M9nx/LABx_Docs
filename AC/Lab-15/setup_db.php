<?php
require_once 'config.php';
require_once '../progress.php';

try {
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database_setup.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && stripos($statement, '--') !== 0) {
            $pdo->exec($statement);
        }
    }
    
    // Reset lab progress
    resetLab(15);
    
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Lab 15</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0e0;
        }
        .success-box {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
        }
        .success-box h1 {
            color: #00ff00;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .success-box p {
            color: #aaa;
            margin-bottom: 0.5rem;
        }
        .checkmark {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 1.5rem;
            font-weight: 600;
            transition: transform 0.3s;
        }
        .btn:hover {
            transform: translateY(-3px);
        }
        .tables {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .tables code {
            color: #88ff88;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="success-box">
        <div class="checkmark">✅</div>
        <h1>Database Initialized!</h1>
        <p>Lab 15: IDOR PII Leakage has been set up successfully.</p>
        <div class="tables">
            <p><strong>Tables created:</strong></p>
            <code>users, user_notes, ad_campaigns, account_settings, audit_log</code>
            <p style="margin-top: 1rem;"><strong>Sample accounts:</strong></p>
            <code>7 users with notes, campaigns, and PII data</code>
        </div>
        <p>Lab progress has been reset.</p>
        <a href="index.php" class="btn">Start Lab →</a>
    </div>
</body>
</html>';
    
} catch (PDOException $e) {
    echo '<!DOCTYPE html>
<html><head><title>Setup Error</title>
<style>
    body { font-family: sans-serif; background: #1a0a0a; color: #ff4444; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    .error { background: rgba(255,0,0,0.1); border: 2px solid #ff4444; padding: 2rem; border-radius: 10px; max-width: 600px; }
    code { background: #000; padding: 0.5rem; display: block; margin: 1rem 0; border-radius: 5px; }
</style>
</head><body>
<div class="error">
    <h1>⚠️ Setup Error</h1>
    <p>Failed to initialize database:</p>
    <code>' . htmlspecialchars($e->getMessage()) . '</code>
    <p>Make sure MySQL is running and credentials are correct.</p>
</div>
</body></html>';
}
?>
