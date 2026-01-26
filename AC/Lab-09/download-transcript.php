<?php
// VULNERABLE: No authentication or authorization check!
// This file serves transcripts based on a user-controlled filename parameter

// Get the requested file from URL parameter
$file = $_GET['file'] ?? '';

// Basic path traversal prevention (but still vulnerable to IDOR!)
$file = basename($file);

$transcriptDir = __DIR__ . '/transcripts';
$filePath = $transcriptDir . '/' . $file;

// Check if file exists
if (empty($file) || !file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Transcript Not Found</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #e0e0e0;
            }
            .error-box {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 68, 68, 0.3);
                border-radius: 20px;
                padding: 3rem;
                text-align: center;
            }
            h1 { color: #ff4444; }
            a { color: #ff4444; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>❌ Transcript Not Found</h1>
            <p>The requested transcript does not exist.</p>
            <p><a href="chat.php">← Back to Chat</a></p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Read and display the transcript
$content = file_get_contents($filePath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Transcript - <?php echo htmlspecialchars($file); ?></title>
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
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .page-title p {
            color: #888;
        }
        .transcript-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            overflow: hidden;
        }
        .transcript-header {
            background: rgba(255, 68, 68, 0.1);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .transcript-header h2 {
            color: #ff6666;
            font-size: 1.2rem;
        }
        .file-name {
            color: #888;
            font-size: 0.9rem;
            font-family: monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
        }
        .transcript-content {
            padding: 1.5rem;
        }
        .transcript-content pre {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.95rem;
            line-height: 1.8;
            color: #b0b0b0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
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
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
        }
        .url-hint {
            background: rgba(255, 200, 68, 0.1);
            border: 1px solid rgba(255, 200, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
            text-align: center;
        }
        .url-hint p {
            color: #ffcc44;
            font-size: 0.9rem;
        }
        .url-hint code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
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
                <a href="chat.php">Live Chat</a>
                <a href="docs.php">Documentation</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>📄 Chat Transcript</h1>
            <p>Your conversation history</p>
        </div>

        <div class="transcript-box">
            <div class="transcript-header">
                <h2>📜 Transcript Details</h2>
                <span class="file-name"><?php echo htmlspecialchars($file); ?></span>
            </div>
            <div class="transcript-content">
                <pre><?php echo htmlspecialchars($content); ?></pre>
            </div>
        </div>

        <div class="action-buttons">
            <a href="chat.php" class="btn btn-primary">💬 Back to Chat</a>
            <a href="index.php" class="btn btn-secondary">🏠 Home</a>
        </div>

        <div class="url-hint">
            <p>📌 Current URL: <code>download-transcript.php?file=<?php echo htmlspecialchars($file); ?></code></p>
        </div>
    </div>
</body>
</html>