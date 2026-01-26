<?php
/**
 * Lab 26: Update Application - VULNERABLE ENDPOINT
 * 
 * VULNERABILITY: IDOR + Information Disclosure
 * When updating with only application[id] (removing other fields),
 * the error response leaks the target application's credentials!
 * 
 * Based on Pressable HackerOne Report
 */

require_once 'config.php';
requireLogin();

$message = '';
$messageType = '';
$leakedApp = null;
$isExploit = false;

// Get the application ID from query string for initial load
$appId = $_GET['id'] ?? 0;

// For initial page load - get user's own application
$stmt = $pdo->prepare("SELECT * FROM api_applications WHERE id = ? AND user_id = ?");
$stmt->execute([$appId, $_SESSION['user_id']]);
$userApp = $stmt->fetch();

// Handle POST request - THIS IS WHERE THE VULNERABILITY EXISTS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authenticity_token = $_POST['authenticity_token'] ?? '';
    $applicationId = $_POST['application']['id'] ?? 0;
    $applicationName = $_POST['application']['name'] ?? null;
    $applicationDescription = $_POST['application']['description'] ?? null;
    $applicationRedirectUri = $_POST['application']['redirect_uri'] ?? null;
    
    // Verify CSRF token
    if (!verifyCSRFToken($authenticity_token)) {
        $message = 'Invalid authenticity token';
        $messageType = 'error';
    } else {
        // VULNERABILITY: We fetch the application by ID WITHOUT checking ownership!
        // This is intentionally vulnerable for the lab
        $targetApp = getApplicationById($pdo, $applicationId);
        
        if (!$targetApp) {
            $message = 'Application not found';
            $messageType = 'error';
        } else {
            // VULNERABILITY: When name is empty/missing, we show an error
            // BUT we still render the TARGET application's details including secrets!
            if (empty($applicationName)) {
                // Check if this is accessing another user's application
                if ($targetApp['user_id'] != $_SESSION['user_id']) {
                    $isExploit = true;
                    logActivity($pdo, $_SESSION['user_id'], 'idor_exploit', 'api_application', $applicationId, 
                        'User accessed credentials of application belonging to user ' . $targetApp['user_id']);
                }
                
                $message = 'Name must be provided';
                $messageType = 'error';
                // CRITICAL LEAK: We set the leaked app to show in the error page!
                $leakedApp = $targetApp;
            } else {
                // Normal update flow - but still vulnerable because we don't check ownership
                if ($targetApp['user_id'] != $_SESSION['user_id']) {
                    // VULNERABILITY: Instead of blocking, we could allow the transfer!
                    // For this lab, we'll just show the error with leaked data
                    $isExploit = true;
                    $message = 'Name must be provided';
                    $messageType = 'error';
                    $leakedApp = $targetApp;
                    
                    logActivity($pdo, $_SESSION['user_id'], 'idor_exploit', 'api_application', $applicationId,
                        'Attempted to update application belonging to user ' . $targetApp['user_id']);
                } else {
                    // Legitimate update
                    $stmt = $pdo->prepare("
                        UPDATE api_applications 
                        SET name = ?, description = ?, redirect_uri = ?
                        WHERE id = ? AND user_id = ?
                    ");
                    $stmt->execute([
                        $applicationName,
                        $applicationDescription,
                        $applicationRedirectUri,
                        $applicationId,
                        $_SESSION['user_id']
                    ]);
                    
                    $message = 'Application updated successfully';
                    $messageType = 'success';
                    
                    // Refresh the application data
                    $stmt = $pdo->prepare("SELECT * FROM api_applications WHERE id = ? AND user_id = ?");
                    $stmt->execute([$applicationId, $_SESSION['user_id']]);
                    $userApp = $stmt->fetch();
                }
            }
        }
    }
}

// If no user application found on GET, redirect
if (!$userApp && !$leakedApp && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Location: applications.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Application - Pressable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
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
            color: #00b4d8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .nav-links a {
            color: #aaa;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover { color: #00b4d8; }
        .user-badge {
            padding: 0.4rem 1rem;
            background: rgba(0, 180, 216, 0.2);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 20px;
            color: #00b4d8;
            font-size: 0.9rem;
        }
        .main-content {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: #00b4d8; }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 0.25rem;
        }
        .page-header p { color: #888; }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
        }
        .message.success {
            background: rgba(0, 200, 83, 0.1);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: #00c853;
        }
        .exploit-banner {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .exploit-banner h2 {
            color: #00ff00;
            margin-bottom: 0.5rem;
        }
        .exploit-banner p { color: #88ff88; }
        .leaked-credentials {
            background: rgba(255, 68, 68, 0.1);
            border: 2px solid rgba(255, 68, 68, 0.5);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .leaked-credentials h3 {
            color: #ff6b6b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .leaked-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }
        .leaked-item .label { color: #888; font-size: 0.85rem; }
        .leaked-item .value {
            font-family: 'Consolas', monospace;
            color: #ff6b6b;
            font-size: 0.9rem;
            word-break: break-all;
        }
        .form-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #00b4d8;
        }
        .form-group small {
            display: block;
            color: #666;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .hidden-field {
            background: rgba(255, 170, 0, 0.1);
            border: 1px solid rgba(255, 170, 0, 0.3);
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .hidden-field p {
            color: #ffaa00;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .hidden-field code {
            display: block;
            background: rgba(0, 0, 0, 0.4);
            padding: 0.5rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 180, 216, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn-success {
            background: linear-gradient(135deg, #00c853, #00a040);
            color: white;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .dev-tools-hint {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(0, 180, 216, 0.1);
            border: 1px solid rgba(0, 180, 216, 0.3);
            border-radius: 8px;
            font-size: 0.85rem;
        }
        .dev-tools-hint h4 {
            color: #00b4d8;
            margin-bottom: 0.5rem;
        }
        .dev-tools-hint p { color: #aaa; }
        .dev-tools-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.3rem;
            border-radius: 3px;
            color: #88ff88;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">⚡</span>
                Pressable
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="applications.php">API Apps</a>
                <a href="docs.php">Docs</a>
                <div class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php" style="color: #ff6b6b;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <a href="applications.php" class="back-link">← Back to Applications</a>
        
        <div class="page-header">
            <h1>Update Application</h1>
            <p>Modify your API application settings</p>
        </div>

        <?php if ($isExploit): ?>
        <div class="exploit-banner">
            <h2>🎉 IDOR Vulnerability Exploited!</h2>
            <p>You successfully accessed another user's API credentials!</p>
            <a href="success.php" class="btn btn-success" style="margin-top: 1rem;">
                🏁 Submit Flag
            </a>
        </div>
        <?php endif; ?>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $messageType === 'error' ? '⚠️' : '✅'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if ($leakedApp): ?>
        <!-- VULNERABILITY: This section leaks another user's credentials! -->
        <div class="leaked-credentials">
            <h3>⚠️ Application Details (LEAKED!)</h3>
            <div class="leaked-item">
                <span class="label">Application ID</span>
                <span class="value"><?php echo $leakedApp['id']; ?></span>
            </div>
            <div class="leaked-item">
                <span class="label">Name</span>
                <span class="value"><?php echo htmlspecialchars($leakedApp['name']); ?></span>
            </div>
            <div class="leaked-item">
                <span class="label">Client ID</span>
                <span class="value"><?php echo htmlspecialchars($leakedApp['client_id']); ?></span>
            </div>
            <div class="leaked-item">
                <span class="label">Client Secret</span>
                <span class="value"><?php echo htmlspecialchars($leakedApp['client_secret']); ?></span>
            </div>
            <div class="leaked-item">
                <span class="label">Scopes</span>
                <span class="value"><?php echo htmlspecialchars($leakedApp['scopes']); ?></span>
            </div>
            <div class="leaked-item">
                <span class="label">Redirect URI</span>
                <span class="value"><?php echo htmlspecialchars($leakedApp['redirect_uri'] ?? 'Not set'); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" action="update-application.php" id="updateForm">
                <input type="hidden" name="authenticity_token" value="<?php echo $csrfToken; ?>">
                
                <div class="hidden-field">
                    <p>🔍 Hidden field (visible to attackers via browser dev tools):</p>
                    <code>application[id] = <?php echo $userApp['id'] ?? $appId; ?></code>
                </div>
                
                <input type="hidden" name="application[id]" value="<?php echo $userApp['id'] ?? $appId; ?>">
                
                <div class="form-group">
                    <label>Application Name *</label>
                    <input type="text" name="application[name]" 
                           value="<?php echo htmlspecialchars($userApp['name'] ?? ''); ?>"
                           placeholder="My API App">
                    <small>Required - Leave empty to trigger the vulnerability</small>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="application[description]" 
                              placeholder="Describe your application"><?php echo htmlspecialchars($userApp['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Redirect URI</label>
                    <input type="url" name="application[redirect_uri]" 
                           value="<?php echo htmlspecialchars($userApp['redirect_uri'] ?? ''); ?>"
                           placeholder="https://example.com/callback">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Application</button>
                    <a href="applications.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="dev-tools-hint">
            <h4>🔧 Attack Instructions</h4>
            <p>
                1. Open Browser DevTools (F12) → Network tab<br>
                2. Submit the form and observe the POST request<br>
                3. Right-click the request → "Edit and Resend" (Firefox) or copy as cURL<br>
                4. Change <code>application[id]</code> to another ID (try <code>2</code>, <code>3</code>, etc.)<br>
                5. Remove <code>application[name]</code> and other fields, keep only <code>application[id]</code> and <code>authenticity_token</code><br>
                6. The error response will leak the victim's credentials!
            </p>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
