<?php
/**
 * Lab 02: Modifying Serialized Data Types
 * Success Page - Lab Completion
 */
require_once 'config.php';
require_once '../progress.php';

// Mark lab as solved (in case accessed directly after deletion)
markLabSolved(2);

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Completed - TypeJuggle Shop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%); 
            min-height: 100vh; 
            color: #e0e0e0; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-container {
            text-align: center;
            max-width: 600px;
            padding: 3rem;
        }
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 20px 60px rgba(34, 197, 94, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .success-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
            stroke-width: 3;
            fill: none;
        }
        h1 { font-size: 2.5rem; margin-bottom: 1rem; color: #22c55e; }
        .subtitle { color: #ccc; font-size: 1.1rem; margin-bottom: 2rem; }
        .flag-box {
            background: rgba(255,255,255,0.05);
            border: 2px solid #22c55e;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .flag-label { color: #888; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .flag-value {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 1.2rem;
            color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
            padding: 1rem;
            border-radius: 10px;
            word-break: break-all;
        }
        .info-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            text-align: left;
        }
        .info-card h3 { color: #fb923c; margin-bottom: 0.75rem; }
        .info-card p { color: #ccc; line-height: 1.6; }
        .info-card code { 
            background: rgba(249,115,22,0.2); 
            padding: 2px 6px; 
            border-radius: 4px; 
            color: #fb923c;
        }
        .btn-group { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(249,115,22,0.4); }
        .btn-secondary { 
            background: transparent; 
            border: 2px solid #f97316; 
            color: #f97316; 
        }
        .btn-secondary:hover { background: #f97316; color: white; }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1>Congratulations!</h1>
        <p class="subtitle">You have successfully completed Lab 02: Modifying Serialized Data Types</p>
        
        <div class="flag-box">
            <div class="flag-label">Your Flag</div>
            <div class="flag-value">FLAG{php_type_juggling_0_equals_any_string}</div>
        </div>
        
        <div class="info-card">
            <h3>What You Learned</h3>
            <p>
                You exploited PHP's loose type comparison vulnerability. By changing the <code>access_token</code> 
                from a string to integer <code>0</code>, you bypassed authentication because 
                <code>0 == "any_string"</code> evaluates to TRUE in PHP.
            </p>
        </div>
        
        <div class="info-card">
            <h3>Key Takeaways</h3>
            <p>
                • Always use strict comparison (<code>===</code>) instead of loose comparison (<code>==</code>)<br>
                • Never trust client-side serialized data for authentication<br>
                • Validate data types explicitly before comparison<br>
                • Store sessions server-side, not in client cookies
            </p>
        </div>
        
        <div class="btn-group">
            <a href="../" class="btn btn-primary">Back to Labs</a>
            <a href="docs.php" class="btn btn-secondary">View Walkthrough</a>
            <a href="setup_db.php" class="btn btn-secondary">Reset Lab</a>
        </div>
    </div>
</body>
</html>
