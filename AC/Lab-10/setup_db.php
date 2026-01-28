<?php
// Lab 10 Database Setup Script
// URL-based Access Control Bypass via X-Original-URL Header

// Reset lab progress
require_once '../progress.php';
resetLab(10);

// Use centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

// Connect without database first
$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute SQL file
$sql = file_get_contents('database_setup.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Setup Complete - Lab 10</title>
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
            .setup-box {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(0, 255, 0, 0.3);
                border-radius: 15px;
                padding: 3rem;
                text-align: center;
                max-width: 500px;
            }
            .setup-box h1 {
                color: #00ff00;
                margin-bottom: 1rem;
                font-size: 2rem;
            }
            .setup-box p {
                color: #aaa;
                margin-bottom: 1rem;
                line-height: 1.6;
            }
            .setup-box .icon {
                font-size: 4rem;
                margin-bottom: 1rem;
            }
            .btn {
                display: inline-block;
                padding: 0.8rem 2rem;
                background: linear-gradient(135deg, #ff4444, #cc0000);
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                margin-top: 1rem;
                transition: all 0.3s;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
            }
            .credentials {
                background: rgba(0, 0, 0, 0.3);
                padding: 1rem;
                border-radius: 8px;
                margin: 1rem 0;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class='setup-box'>
            <div class='icon'>✅</div>
            <h1>Database Setup Complete!</h1>
            <p>Lab 10 database has been created successfully with all required tables and sample data.</p>
            <div class='credentials'>
                <strong>Test Credentials:</strong><br>
                Username: wiener<br>
                Password: peter
            </div>
            <p>You can now start the lab!</p>
            <a href='index.php?setup=success' class='btn'>Continue to Lab →</a>
        </div>
    </body>
    </html>";
} else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
?>
