<?php
/**
 * Success Page - Lab Completed
 */

require_once '../progress.php';

// Ensure lab is marked as solved
markLabSolved(1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Solved! - SerialLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0f0a 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #e0e0e0;
        }
        .success-container {
            background: rgba(34, 197, 94, 0.1);
            border: 2px solid rgba(34, 197, 94, 0.4);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            max-width: 600px;
            backdrop-filter: blur(10px);
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        h1 {
            color: #22c55e;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .subtitle {
            color: #86efac;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .info-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .info-box h3 {
            color: #4ade80;
            margin-bottom: 10px;
        }
        .info-box ul {
            color: #a7f3d0;
            margin-left: 20px;
            line-height: 1.8;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #f97316;
            color: #f97316;
        }
        .btn-secondary:hover {
            background: #f97316;
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">🎉</div>
        <h1>Congratulations!</h1>
        <p class="subtitle">You have successfully solved Lab 1: Modifying Serialized Objects</p>
        
        <div class="info-box">
            <h3>✅ What You Accomplished</h3>
            <ul>
                <li>Identified the serialized PHP object in the session cookie</li>
                <li>Decoded the URL and Base64 encoded cookie value</li>
                <li>Modified the <code>admin</code> attribute from <code>b:0</code> to <code>b:1</code></li>
                <li>Re-encoded the modified object and replaced the cookie</li>
                <li>Gained administrative privileges through insecure deserialization</li>
                <li>Deleted the target user "carlos"</li>
            </ul>
        </div>

        <div class="info-box">
            <h3>📚 Key Takeaways</h3>
            <ul>
                <li>Never trust serialized data from client-side sources</li>
                <li>Session data should be stored server-side, not in cookies</li>
                <li>Authorization checks must verify against the database, not client data</li>
                <li>Consider using signed/encrypted tokens (JWT) instead of raw serialization</li>
            </ul>
        </div>

        <a href="index.php" class="btn btn-secondary">← Back to Lab</a>
        <a href="../index.php" class="btn btn-primary">All Labs →</a>
    </div>
</body>
</html>
