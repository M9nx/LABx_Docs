<?php
/**
 * Lab 26: Create New Application
 */

require_once 'config.php';
requireLogin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $redirectUri = trim($_POST['redirect_uri'] ?? '');
    $scopes = $_POST['scopes'] ?? [];
    
    if (empty($name)) {
        $message = 'Application name is required';
        $messageType = 'error';
    } else {
        $clientId = generateClientId();
        $clientSecret = generateClientSecret();
        $scopesStr = implode(',', $scopes);
        
        $stmt = $pdo->prepare("
            INSERT INTO api_applications (user_id, name, description, client_id, client_secret, redirect_uri, scopes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $name,
            $description,
            $clientId,
            $clientSecret,
            $redirectUri,
            $scopesStr
        ]);
        
        $newAppId = $pdo->lastInsertId();
        logActivity($pdo, $_SESSION['user_id'], 'create_app', 'api_application', $newAppId, 'Created new API application');
        
        header("Location: view-application.php?id=" . $newAppId);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Application - Pressable</title>
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
            max-width: 600px;
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
        }
        .back-link:hover { color: #00b4d8; }
        .page-header h1 {
            font-size: 1.75rem;
            color: #fff;
            margin-bottom: 0.25rem;
        }
        .page-header p { color: #888; margin-bottom: 2rem; }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .message.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6b6b;
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
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
        }
        .checkbox-item input {
            width: auto;
            accent-color: #00b4d8;
        }
        .checkbox-item label {
            color: #ccc;
            font-size: 0.85rem;
            margin: 0;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
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
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
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
            <h1>Create New Application</h1>
            <p>Register a new API application to access Pressable APIs</p>
        </div>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Application Name *</label>
                    <input type="text" name="name" required placeholder="My Awesome App">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="What does your application do?"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Redirect URI</label>
                    <input type="url" name="redirect_uri" placeholder="https://your-app.com/callback">
                </div>
                
                <div class="form-group">
                    <label>Scopes</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="scopes[]" value="read:sites" id="scope1">
                            <label for="scope1">read:sites</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="scopes[]" value="write:sites" id="scope2">
                            <label for="scope2">write:sites</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="scopes[]" value="manage:collaborators" id="scope3">
                            <label for="scope3">manage:collaborators</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="scopes[]" value="billing:read" id="scope4">
                            <label for="scope4">billing:read</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Application</button>
                    <a href="applications.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
