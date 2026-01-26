<?php
// Lab 14: Database Setup Script
require_once 'config.php';
require_once '../progress.php';

// Read and execute SQL file
$sql = file_get_contents('database_setup.sql');

// Execute multi-query
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    $success = true;
    $message = "Database 'ac_lab14' initialized successfully!";
} else {
    $success = false;
    $message = "Error setting up database: " . $conn->error;
}

// Reset lab progress
resetLab(14);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - IDOR Lab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0e0;
        }
        .container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid <?php echo $success ? 'rgba(0, 255, 0, 0.3)' : 'rgba(255, 68, 68, 0.3)'; ?>;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: <?php echo $success ? '#00ff00' : '#ff4444'; ?>;
            margin-bottom: 1rem;
        }
        p {
            color: #aaa;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .data-summary {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .data-summary h3 {
            color: #ff6666;
            margin-bottom: 1rem;
        }
        .data-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .data-item:last-child { border-bottom: none; }
        .data-label { color: #888; }
        .data-value { color: #88ff88; font-weight: bold; }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 0.5rem;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon"><?php echo $success ? '✅' : '❌'; ?></div>
        <h1><?php echo $success ? 'Setup Complete!' : 'Setup Failed'; ?></h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        
        <?php if ($success): ?>
        <div class="data-summary">
            <h3>📊 Data Summary</h3>
            <div class="data-item">
                <span class="data-label">Managers</span>
                <span class="data-value">4 accounts</span>
            </div>
            <div class="data-item">
                <span class="data-label">Clients</span>
                <span class="data-value">5 advertisers</span>
            </div>
            <div class="data-item">
                <span class="data-label">Campaigns</span>
                <span class="data-value">7 campaigns</span>
            </div>
            <div class="data-item">
                <span class="data-label">Banners</span>
                <span class="data-value">13 banners</span>
            </div>
            <div class="data-item">
                <span class="data-label">Lab Progress</span>
                <span class="data-value">Reset to unsolved</span>
            </div>
        </div>
        <?php endif; ?>
        
        <a href="index.php" class="btn">Go to Lab →</a>
        <a href="../index.php" class="btn btn-secondary">← All Labs</a>
    </div>
</body>
</html>
