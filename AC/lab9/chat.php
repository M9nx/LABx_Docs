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

// Get user's chat count for display
$chat_count = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM chat_logs WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $chat_count = $row['count'];
}
$stmt->close();

// Get the next transcript number for new chats
$result = $conn->query("SELECT MAX(id) as max_id FROM chat_logs");
$row = $result->fetch_assoc();
$next_transcript = ($row['max_id'] ?? 0) + 1;

// Handle new chat message submission
$message_sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    
    // Create a new transcript file
    $filename = $next_transcript . '.txt';
    $transcript_content = "CHAT TRANSCRIPT - Session #$next_transcript
Date: " . date('Y-m-d H:i:s') . "
User: $username

[" . date('H:i:s') . "] $username: $message

[" . date('H:i:s', strtotime('+30 seconds')) . "] Support Agent: Thank you for your message! Our team will review your inquiry and get back to you shortly.

[" . date('H:i:s', strtotime('+1 minute')) . "] Support Agent: Is there anything else I can help you with today?

[" . date('H:i:s', strtotime('+2 minutes')) . "] Support Agent: Chat session ended. Thank you for contacting us!

--- END OF TRANSCRIPT ---";

    // Save transcript file
    $transcriptDir = __DIR__ . '/transcripts';
    if (!file_exists($transcriptDir)) {
        mkdir($transcriptDir, 0755, true);
    }
    file_put_contents($transcriptDir . '/' . $filename, $transcript_content);
    
    // Save to database
    $stmt = $conn->prepare("INSERT INTO chat_logs (user_id, filename) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $filename);
    $stmt->execute();
    $stmt->close();
    
    $message_sent = true;
    $new_transcript_id = $next_transcript;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat - ChatLab</title>
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
            max-width: 900px;
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
        .page-title p {
            color: #888;
        }
        .chat-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 20px;
            overflow: hidden;
        }
        .chat-header {
            background: rgba(255, 68, 68, 0.1);
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-header h2 {
            color: #ff6666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .online-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #44ff44;
            font-size: 0.9rem;
        }
        .online-dot {
            width: 10px;
            height: 10px;
            background: #44ff44;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .chat-messages {
            padding: 2rem;
            min-height: 300px;
            max-height: 400px;
            overflow-y: auto;
        }
        .message {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }
        .message-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .message-content {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem;
            border-radius: 10px;
            max-width: 80%;
        }
        .message-content.agent {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.2);
        }
        .message-sender {
            color: #ff6666;
            font-weight: 600;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
        }
        .message-text {
            color: #b0b0b0;
            line-height: 1.6;
        }
        .chat-input {
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 68, 68, 0.2);
            background: rgba(0, 0, 0, 0.2);
        }
        .chat-form {
            display: flex;
            gap: 1rem;
        }
        .chat-form textarea {
            flex: 1;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 10px;
            color: #e0e0e0;
            font-size: 1rem;
            resize: none;
            height: 60px;
        }
        .chat-form textarea:focus {
            outline: none;
            border-color: #ff4444;
        }
        .btn-send {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .transcript-alert {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        .transcript-alert h3 {
            color: #44ff44;
            margin-bottom: 0.5rem;
        }
        .transcript-alert p {
            color: #88ff88;
            margin-bottom: 1rem;
        }
        .btn-transcript {
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
        .btn-transcript:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(68, 255, 68, 0.4);
        }
        .info-box {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
            text-align: center;
        }
        .info-box p {
            color: #ff8888;
            font-size: 0.9rem;
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
                <a href="profile.php">My Account</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>💬 Live Chat Support</h1>
            <p>Welcome, <?php echo htmlspecialchars($username); ?>! Start a conversation with our support team.</p>
        </div>

        <div class="chat-container">
            <div class="chat-header">
                <h2>🎧 Support Chat</h2>
                <div class="online-status">
                    <div class="online-dot"></div>
                    Support Online
                </div>
            </div>
            
            <div class="chat-messages">
                <div class="message">
                    <div class="message-avatar">🤖</div>
                    <div class="message-content agent">
                        <div class="message-sender">Support Agent</div>
                        <div class="message-text">Hello <?php echo htmlspecialchars($username); ?>! Welcome to our live chat support. How can I help you today?</div>
                    </div>
                </div>
                
                <?php if ($message_sent): ?>
                <div class="message">
                    <div class="message-avatar">👤</div>
                    <div class="message-content">
                        <div class="message-sender">You</div>
                        <div class="message-text"><?php echo htmlspecialchars($_POST['message']); ?></div>
                    </div>
                </div>
                <div class="message">
                    <div class="message-avatar">🤖</div>
                    <div class="message-content agent">
                        <div class="message-sender">Support Agent</div>
                        <div class="message-text">Thank you for your message! Our team will review your inquiry and get back to you shortly.</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="chat-input">
                <form method="POST" class="chat-form">
                    <textarea name="message" placeholder="Type your message here..." required></textarea>
                    <button type="submit" class="btn-send">Send 📤</button>
                </form>
            </div>
        </div>

        <?php if ($message_sent): ?>
        <div class="transcript-alert">
            <h3>✅ Chat Session Saved!</h3>
            <p>Your chat transcript has been saved. You can download it for your records.</p>
            <a href="download-transcript.php?file=<?php echo $new_transcript_id; ?>.txt" class="btn-transcript">📄 View Transcript</a>
        </div>
        <?php endif; ?>

        <div class="info-box">
            <p>💡 Tip: All chat transcripts are saved and can be accessed via the "View Transcript" button after each conversation.</p>
        </div>
    </div>
</body>
</html>