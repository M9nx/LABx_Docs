<?php
session_start();
require_once 'config.php';

// STEP 2: Role selection page (Protected)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_GET['username'] ?? '';
if (empty($username)) {
    header("Location: admin.php");
    exit;
}

// Get user info
$stmt = $conn->prepare("SELECT username, full_name, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role - MultiStep Admin</title>
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
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-title h1 {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .step-num {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .step.completed .step-num {
            background: #00cc00;
            color: white;
        }
        .step.active .step-num {
            background: #ff4444;
            color: white;
        }
        .step.inactive .step-num {
            background: rgba(255, 255, 255, 0.1);
            color: #666;
        }
        .step-label {
            color: #888;
            font-size: 0.9rem;
        }
        .step.active .step-label {
            color: #ff6666;
            font-weight: 600;
        }
        .step.completed .step-label {
            color: #66ff66;
        }
        .role-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
        }
        .user-info {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .user-info h2 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .user-info p {
            color: #888;
        }
        .current-role {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .role-selection h3 {
            color: #ccc;
            margin-bottom: 1rem;
        }
        .role-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .role-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 68, 68, 0.2);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .role-option:hover {
            border-color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
        }
        .role-option input[type="radio"] {
            width: 1.2rem;
            height: 1.2rem;
            accent-color: #ff4444;
        }
        .role-option label {
            cursor: pointer;
            flex: 1;
        }
        .role-option .role-name {
            color: #ff6666;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .role-option .role-desc {
            color: #888;
            font-size: 0.9rem;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            flex: 1;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #666;
            color: #ccc;
        }
        .btn-secondary:hover {
            border-color: #ff4444;
            color: #ff4444;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔄 MultiStep Admin</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="admin.php">Admin Panel</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>📝 Select New Role</h1>
        </div>

        <div class="step-indicator">
            <div class="step completed">
                <div class="step-num">✓</div>
                <span class="step-label">Select User</span>
            </div>
            <div class="step active">
                <div class="step-num">2</div>
                <span class="step-label">Choose Role</span>
            </div>
            <div class="step inactive">
                <div class="step-num">3</div>
                <span class="step-label">Confirm</span>
            </div>
        </div>

        <div class="role-card">
            <div class="user-info">
                <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                <p>@<?php echo htmlspecialchars($user['username']); ?></p>
                <div class="current-role">
                    Current Role: <strong style="color: #ff6666;"><?php echo strtoupper($user['role']); ?></strong>
                </div>
            </div>

            <form action="admin-confirm.php" method="POST">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                
                <div class="role-selection">
                    <h3>Select New Role:</h3>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" name="role" id="role-admin" value="admin" <?php echo $user['role'] === 'admin' ? 'checked' : ''; ?>>
                            <label for="role-admin">
                                <div class="role-name">👑 Administrator</div>
                                <div class="role-desc">Full access to all features and admin panel</div>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="role-user" value="user" <?php echo $user['role'] === 'user' ? 'checked' : ''; ?>>
                            <label for="role-user">
                                <div class="role-name">👤 Regular User</div>
                                <div class="role-desc">Standard user access, no admin privileges</div>
                            </label>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="action" value="upgrade">
                <input type="hidden" name="confirmed" value="true">

                <div class="form-actions">
                    <a href="admin.php" class="btn btn-secondary">← Back</a>
                    <button type="submit" class="btn btn-primary">Continue to Confirm →</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
