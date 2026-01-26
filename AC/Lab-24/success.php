<?php
require_once 'config.php';
require_once '../progress.php';

$success = false;
$error = '';
$foundModelName = '';

// List of valid private model names that prove exploitation
$validModelNames = [
    'fraud-detection-v3',
    'anomaly-detector',
    'customer-churn-predictor',
    'credit-risk-model',
    'sentiment-analyzer-bert',
    'medical-xray-classifier',
    'cancer-detection-cnn'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelName = trim($_POST['model_name'] ?? '');
    
    if (empty($modelName)) {
        $error = 'Please enter a model name';
    } elseif (in_array(strtolower($modelName), array_map('strtolower', $validModelNames))) {
        $success = true;
        $foundModelName = $modelName;
        markLabSolved(24);
    } else {
        $error = 'That model name was not found or is not a private model. Make sure you accessed a PRIVATE model belonging to another user.';
    }
}

$labSolved = isLabSolved(24);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Solution - IDOR ML Model Registry</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #fc6d26;
            text-decoration: none;
        }
        .logo svg { width: 32px; height: 32px; }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .main-content {
            max-width: 700px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .success-container {
            text-align: center;
        }
        .trophy {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 1s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        .success-container h1 {
            font-size: 2.5rem;
            color: #00c853;
            margin-bottom: 0.5rem;
        }
        .success-container .subtitle {
            color: #888;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .success-card {
            background: rgba(0, 200, 83, 0.1);
            border: 1px solid rgba(0, 200, 83, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .success-card h3 {
            color: #00c853;
            margin-bottom: 1rem;
        }
        .success-card p {
            color: #aaa;
            line-height: 1.6;
        }
        .success-card code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .form-container h2 {
            color: #fc6d26;
            margin-bottom: 0.5rem;
        }
        .form-container .subtitle {
            color: #888;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.85rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #fc6d26;
        }
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #fc6d26 0%, #e24329 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
        }
        .error-msg {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .already-solved {
            background: rgba(0, 200, 83, 0.1);
            border: 1px solid rgba(0, 200, 83, 0.3);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .already-solved h2 {
            color: #00c853;
            margin-bottom: 0.5rem;
        }
        .hint-section {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .hint-section h3 {
            color: #fc6d26;
            margin-bottom: 1rem;
        }
        .hint-section ol {
            color: #aaa;
            padding-left: 1.25rem;
            line-height: 1.8;
        }
        .hint-section code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.9em;
        }
        .links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .links a {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .links a:hover {
            border-color: #fc6d26;
            color: #fc6d26;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <svg viewBox="0 0 32 32" fill="none">
                    <path d="M16 2L2 12l14 18 14-18L16 2z" fill="#fc6d26"/>
                    <path d="M16 2L2 12h28L16 2z" fill="#e24329"/>
                    <path d="M16 30L2 12l4.5 18H16z" fill="#fca326"/>
                    <path d="M16 30l14-18-4.5 18H16z" fill="#fca326"/>
                </svg>
                MLRegistry
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <?php if ($success): ?>
        <div class="success-container">
            <div class="trophy">🏆</div>
            <h1>Congratulations!</h1>
            <p class="subtitle">You've successfully exploited the IDOR vulnerability!</p>
            
            <div class="success-card">
                <h3>✅ Lab Completed</h3>
                <p>
                    You accessed the private model: <code><?php echo htmlspecialchars($foundModelName); ?></code>
                </p>
                <p style="margin-top: 1rem;">
                    This demonstrates how IDOR vulnerabilities in GraphQL APIs can expose private resources 
                    when proper authorization checks are not implemented.
                </p>
            </div>
            
            <div class="links">
                <a href="index.php">🏠 Lab Home</a>
                <a href="docs.php">📚 Read More</a>
                <a href="../index.php">🔙 All Labs</a>
            </div>
        </div>
        
        <?php elseif ($labSolved): ?>
        <div class="already-solved">
            <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
            <h2>Lab Already Completed!</h2>
            <p style="color: #888;">You've already solved this lab. Great work!</p>
        </div>
        
        <div class="links">
            <a href="index.php">🏠 Lab Home</a>
            <a href="docs.php">📚 Documentation</a>
            <a href="../index.php">🔙 All Labs</a>
        </div>
        
        <?php else: ?>
        <div class="form-container">
            <h2>🏆 Submit Your Solution</h2>
            <p class="subtitle">Enter the name of a private model you accessed via IDOR</p>
            
            <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="model_name">Private Model Name</label>
                    <input type="text" id="model_name" name="model_name" 
                           placeholder="e.g., fraud-detection-v3" required>
                </div>
                <button type="submit" class="btn">Submit Solution</button>
            </form>
        </div>
        
        <div class="hint-section">
            <h3>💡 How to Complete This Lab</h3>
            <ol>
                <li><a href="login.php" style="color: #fc6d26;">Login</a> as <code>attacker / attacker123</code></li>
                <li>Go to your <a href="models.php" style="color: #fc6d26;">Models</a> page and note your model's GID</li>
                <li>Your model has <code>internal_id = 1000500</code></li>
                <li>Private models have IDs <code>1000501</code> through <code>1000507</code></li>
                <li>Craft a GraphQL request with a target GID:
                    <br><code>btoa("gid://gitlab/Ml::Model/1000501")</code>
                </li>
                <li>Send the request to <code>/Lab-24/api/graphql.php</code></li>
                <li>Find the model name in the response and submit it above!</li>
            </ol>
        </div>
        
        <div class="links">
            <a href="login.php">🚀 Start Lab</a>
            <a href="docs.php">📚 Documentation</a>
            <a href="index.php">🏠 Lab Home</a>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
