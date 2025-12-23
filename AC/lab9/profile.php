<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get user's chat logs
$stmt = $conn->prepare("SELECT * FROM chat_logs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$chat_logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - ChatLab</title>
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
        }
        .profile-card h2 {
            color: #ff6666;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1.5rem;
        }
        .profile-info {
            text-align: center;
        }
        .profile-info h3 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .profile-info .role {
            display: inline-block;
            padding: 0.3rem 1rem;
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .info-list {
            margin-top: 1.5rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #888;
        }
        .info-value {
            color: #e0e0e0;
            font-weight: 500;
        }
        .chat-history h2 {
            margin-bottom: 1rem;
        }
        .chat-log-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.8rem;
            transition: all 0.3s;
        }
        .chat-log-item:hover {
            background: rgba(255, 68, 68, 0.1);
        }
        .chat-log-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .chat-log-icon {
            font-size: 1.5rem;
        }
        .chat-log-details h4 {
            color: #fff;
            margin-bottom: 0.2rem;
        }
        .chat-log-details p {
            color: #888;
            font-size: 0.85rem;
        }
        .btn-view {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(255, 68, 68, 0.4);
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #888;
        }
        .empty-state p {
            margin-bottom: 1rem;
        }
        .btn-chat {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            background: linear-gradient(135deg, #44ff44, #00cc00);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-chat:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(68, 255, 68, 0.4);
        }
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">💬 ChatLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="chat.php">Live Chat</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>👤 My Account</h1>
        </div>

        <div class="profile-grid">
            <div class="profile-card">
                <div class="profile-avatar">👤</div>
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <span class="role"><?php echo htmlspecialchars($user['role']); ?></span>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Member Since</span>
                        <span class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Chat Sessions</span>
                        <span class="info-value"><?php echo count($chat_logs); ?></span>
                    </div>
                </div>
            </div>

            <div class="profile-card chat-history">
                <h2>📜 My Chat Transcripts</h2>
                
                <?php if (count($chat_logs) > 0): ?>
                    <?php foreach ($chat_logs as $log): ?>
                    <div class="chat-log-item">
                        <div class="chat-log-info">
                            <span class="chat-log-icon">💬</span>
                            <div class="chat-log-details">
                                <h4>Chat Session #<?php echo $log['id']; ?></h4>
                                <p><?php echo date('M d, Y H:i', strtotime($log['created_at'])); ?></p>
                            </div>
                        </div>
                        <a href="download-transcript.php?file=<?php echo htmlspecialchars($log['filename']); ?>" class="btn-view">View Transcript</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>You haven't started any chat sessions yet.</p>
                        <a href="chat.php" class="btn-chat">💬 Start a Chat</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>