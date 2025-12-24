<?php
require_once 'config.php';
require_once '../progress.php';

$sqlFile = file_get_contents('database_setup.sql');
$statements = array_filter(array_map('trim', explode(';', $sqlFile)));

$success = true;
$errors = [];

foreach ($statements as $statement) {
    if (!empty($statement) && stripos($statement, '--') !== 0) {
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'syntax') !== false) {
                $errors[] = $e->getMessage();
                $success = false;
            }
        }
    }
}

// Reset lab progress
resetLab(17);

if ($success): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Lab 17</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0e0;
        }
        .container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 500px;
        }
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        h1 { color: #fc6d26; margin-bottom: 1rem; }
        p { color: #aaa; margin-bottom: 2rem; line-height: 1.6; }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #fc6d26, #e24329);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 0.5rem;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-3px); }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Database Ready!</h1>
        <p>Lab 17 database has been initialized with sample projects, merge requests, and external status checks. The IDOR vulnerability is now active.</p>
        <a href="index.php" class="btn">Go to Lab</a>
        <a href="../index.php" class="btn btn-secondary">All Labs</a>
    </div>
</body>
</html>
<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup Error</title>
    <style>
        body { font-family: sans-serif; background: #1a1a2e; color: #e0e0e0; padding: 2rem; }
        .error { background: rgba(255,0,0,0.2); border: 1px solid #ff4444; padding: 1rem; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>Setup Errors</h1>
    <div class="error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
</body>
</html>
<?php endif; ?>
