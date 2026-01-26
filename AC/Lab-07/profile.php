<?php
session_start();
require_once 'config.php';

// VULNERABLE: User ID from request parameter
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: login.php");
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = getDBConnection();

// VULNERABLE: Fetch user data based on ID parameter without checking ownership
$stmt = $conn->prepare("SELECT id, username, email, api_key, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$conn->close();

// VULNERABILITY: Data leakage in redirect
// If accessing another user's profile, redirect BUT include the data in response body
if ($user && $user['id'] != $_SESSION['user_id']) {
    // Send redirect header
    header("Location: index.php");
    // BUT continue to output the page content (data leaks in response body!)
    // The page content below will be included in the response body
    // even though a redirect header is sent
}

// Check if this is the user's own profile
$isOwnProfile = ($user && $user['id'] == $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - RedirectLab</title>
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
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .profile-header h1 {
            color: #ff4444;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .profile-header p {
            color: #888;
        }
        .info-group {
            margin-bottom: 1.5rem;
        }
        .info-group label {
            display: block;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .info-group .value {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 68, 68, 0.2);
            color: #e0e0e0;
        }
        .api-key-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .api-key-box h3 {
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .api-key-value {
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Consolas', monospace;
            color: #00ff00;
            word-break: break-all;
        }
        .no-user {
            text-align: center;
            color: #888;
            padding: 3rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔄 RedirectLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($user): ?>
        <div class="profile-card">
            <div class="profile-header">
                <h1>👤 <?php echo htmlspecialchars($user['username']); ?></h1>
                <p>User ID: <?php echo $user['id']; ?></p>
            </div>

            <div class="info-group">
                <label>Username</label>
                <div class="value"><?php echo htmlspecialchars($user['username']); ?></div>
            </div>

            <div class="info-group">
                <label>Email</label>
                <div class="value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <div class="info-group">
                <label>Role</label>
                <div class="value"><?php echo htmlspecialchars($user['role']); ?></div>
            </div>

            <div class="api-key-box">
                <h3>🔑 Your API Key</h3>
                <div class="api-key-value"><?php echo htmlspecialchars($user['api_key']); ?></div>
            </div>
        </div>
        <?php else: ?>
        <div class="profile-card">
            <div class="no-user">
                <h2>User Not Found</h2>
                <p>The requested user does not exist.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>