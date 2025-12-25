<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$response = null;
$error = null;
$targetEmail = $_SESSION['email']; // Default to own email
$isExploit = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetEmail = $_POST['target_email'] ?? $_SESSION['email'];
    
    // Make internal API call
    $requestData = [
        'params' => [
            'updates' => [[
                'param' => 'user',
                'value' => ['userEmail' => $targetEmail],
                'op' => 'a'
            ]]
        ]
    ];
    
    // Use cURL to call our own API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/AC/lab15/api/getUserNotes.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $response = json_decode($result, true);
        
        // Check if this was an IDOR exploit
        if (isset($response['_exploit']['detected']) && $response['_exploit']['detected']) {
            $isExploit = true;
            markLabSolved(15);
        }
    } else {
        $error = json_decode($result, true)['error'] ?? 'API request failed';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - MTN MobAd Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 204, 0, 0.3);
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffcc00;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #000;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ffcc00; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #ffcc00;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .page-title p { color: #888; }
        .api-form {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .api-form h2 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 10px;
            color: #e0e0e0;
            font-size: 1rem;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #ffcc00;
        }
        .quick-emails {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .quick-email {
            padding: 0.4rem 0.8rem;
            background: rgba(255, 204, 0, 0.1);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 20px;
            color: #ffcc00;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .quick-email:hover {
            background: rgba(255, 204, 0, 0.2);
        }
        .quick-email.victim {
            background: rgba(255, 68, 68, 0.1);
            border-color: rgba(255, 68, 68, 0.3);
            color: #ff6666;
        }
        .btn-submit {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 204, 0, 0.3);
        }
        .request-preview {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
        }
        .exploit-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .exploit-banner h2 {
            color: #00ff00;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .exploit-banner p { color: #88ff88; }
        .response-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 204, 0, 0.3);
            border-radius: 20px;
            padding: 2rem;
        }
        .response-section h2 {
            color: #ffcc00;
            margin-bottom: 1rem;
        }
        .response-json {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }
        .error-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.4);
            border-radius: 10px;
            padding: 1.5rem;
            color: #ff6666;
        }
        .pii-highlight {
            background: rgba(255, 68, 68, 0.2);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
        }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">MTN</span>
                MobAd Platform
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="docs.php">Documentation</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>🧪 API Endpoint Tester</h1>
            <p>Test the getUserNotes API endpoint. Try changing the email to exploit the IDOR vulnerability!</p>
        </div>

        <?php if ($isExploit): ?>
        <div class="exploit-banner">
            <h2>🎉 Lab Solved!</h2>
            <p>You successfully exploited the IDOR vulnerability and accessed another user's PII!</p>
            <div class="actions" style="justify-content: center; margin-top: 1rem;">
                <a href="success.php?email=<?php echo urlencode($targetEmail); ?>" class="btn btn-secondary">View Success Page →</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="api-form">
            <h2>📤 API Request</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Target Email Address</label>
                    <input type="email" name="target_email" value="<?php echo htmlspecialchars($targetEmail); ?>" 
                           placeholder="Enter email to query" required>
                    <div class="quick-emails">
                        <span class="quick-email" onclick="document.querySelector('input[name=target_email]').value='<?php echo $_SESSION['email']; ?>'">
                            My Email (<?php echo $_SESSION['email']; ?>)
                        </span>
                        <span class="quick-email victim" onclick="document.querySelector('input[name=target_email]').value='victim1@mtnbusiness.com'">
                            🎯 victim1@mtnbusiness.com
                        </span>
                        <span class="quick-email victim" onclick="document.querySelector('input[name=target_email]').value='ceo@bigcorp.ng'">
                            🎯 ceo@bigcorp.ng
                        </span>
                        <span class="quick-email victim" onclick="document.querySelector('input[name=target_email]').value='admin@mtnmobad.com'">
                            🔐 admin@mtnmobad.com
                        </span>
                    </div>
                </div>
                
                <div class="request-preview">
                    <strong>Request Preview:</strong><br><br>
                    POST /api/getUserNotes.php<br>
                    {<br>
                    &nbsp;&nbsp;"params": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"updates": [{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"param": "user",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"value": { "userEmail": "<span id="preview-email"><?php echo htmlspecialchars($targetEmail); ?></span>" },<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"op": "a"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}]<br>
                    &nbsp;&nbsp;}<br>
                    }
                </div>
                
                <button type="submit" class="btn-submit">🚀 Send Request</button>
            </form>
        </div>

        <?php if ($error): ?>
        <div class="error-box">
            <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($response): ?>
        <div class="response-section">
            <h2>📥 API Response</h2>
            <?php if ($isExploit): ?>
            <p style="color: #ff6666; margin-bottom: 1rem;">
                ⚠️ <strong>PII LEAK DETECTED!</strong> The response contains another user's personal information.
            </p>
            <?php endif; ?>
            <div class="response-json">
                <pre><?php echo htmlspecialchars(json_encode($response, JSON_PRETTY_PRINT)); ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="docs.php" class="btn btn-secondary">📚 Read Documentation</a>
        </div>
    </div>

    <script>
        document.querySelector('input[name=target_email]').addEventListener('input', function(e) {
            document.getElementById('preview-email').textContent = e.target.value || 'your-email@example.com';
        });
    </script>
</body>
</html>
