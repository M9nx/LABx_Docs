<?php
require_once 'config.php';
requireLogin();

$issueId = $_GET['id'] ?? 0;

// Get issue with project info
$stmt = $pdo->prepare("
    SELECT i.*, p.name as project_name, p.path as project_path, p.owner_id as project_owner_id,
           u.username as author_name
    FROM issues i
    JOIN projects p ON i.project_id = p.id
    JOIN users u ON i.author_id = u.id
    WHERE i.id = ?
");
$stmt->execute([$issueId]);
$issue = $stmt->fetch();

if (!$issue) {
    header('Location: projects.php');
    exit();
}

// Get notes for this issue
$stmt = $pdo->prepare("
    SELECT n.*, u.username as author_name
    FROM notes n
    JOIN users u ON n.author_id = u.id
    WHERE n.noteable_type = 'issue' AND n.noteable_id = ?
    ORDER BY n.created_at ASC
");
$stmt->execute([$issueId]);
$notes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue #<?php echo $issue['id']; ?> - SnippetHub</title>
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
        .user-badge {
            padding: 0.4rem 1rem;
            background: rgba(252, 109, 38, 0.2);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            color: #fc6d26;
            font-size: 0.9rem;
        }
        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .breadcrumb {
            margin-bottom: 1.5rem;
            color: #888;
        }
        .breadcrumb a { color: #fc6d26; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .issue-header {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .issue-title-row {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .issue-title {
            font-size: 1.75rem;
            color: #fff;
        }
        .issue-status {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .issue-status.open {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
        }
        .issue-meta {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .issue-meta span { margin-right: 1.5rem; }
        .issue-desc {
            color: #ccc;
            line-height: 1.7;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .section-header h2 {
            color: #fc6d26;
            font-size: 1.25rem;
        }
        .notes-list { list-style: none; }
        .note-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .note-author {
            font-weight: 600;
            color: #fc6d26;
        }
        .note-time { color: #666; font-size: 0.85rem; }
        .note-content { color: #ccc; line-height: 1.6; }
        .note-form {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .note-form h3 {
            color: #fc6d26;
            margin-bottom: 1rem;
        }
        .note-form textarea {
            width: 100%;
            min-height: 100px;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-family: inherit;
            font-size: 1rem;
            resize: vertical;
            margin-bottom: 1rem;
        }
        .note-form textarea:focus {
            outline: none;
            border-color: #fc6d26;
        }
        .btn {
            padding: 0.75rem 1.5rem;
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
        .attack-hint {
            background: rgba(255, 102, 102, 0.1);
            border: 1px solid rgba(255, 102, 102, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .attack-hint h3 {
            color: #ff6666;
            margin-bottom: 0.75rem;
        }
        .attack-hint p {
            color: #ccc;
            line-height: 1.6;
        }
        .attack-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: #88ff88;
            font-family: 'Consolas', monospace;
        }
        .attack-hint pre {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            overflow-x: auto;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
        }
        .response-msg {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .response-msg.success {
            background: rgba(0, 200, 83, 0.1);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: #66ff99;
        }
        .response-msg.error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff6666;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
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
                SnippetHub
            </a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="projects.php">Projects</a>
                <a href="snippets.php">Snippets</a>
                <a href="activity.php">Activity</a>
                <a href="success.php">Submit Flag</a>
                <div class="user-badge">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <a href="logout.php" style="color: #ff6666;">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="breadcrumb">
            <a href="projects.php">Projects</a> / 
            <a href="project-detail.php?id=<?php echo $issue['project_id']; ?>"><?php echo htmlspecialchars($issue['project_name']); ?></a> / 
            Issue #<?php echo $issue['id']; ?>
        </div>

        <div class="issue-header">
            <div class="issue-title-row">
                <h1 class="issue-title"><?php echo htmlspecialchars($issue['title']); ?></h1>
                <span class="issue-status <?php echo $issue['status']; ?>"><?php echo $issue['status']; ?></span>
            </div>
            <div class="issue-meta">
                <span>👤 <?php echo htmlspecialchars($issue['author_name']); ?></span>
                <span>📅 <?php echo formatDate($issue['created_at']); ?></span>
            </div>
            <div class="issue-desc">
                <?php echo nl2br(htmlspecialchars($issue['description'])); ?>
            </div>
        </div>

        <?php if ($_SESSION['username'] === 'attacker'): ?>
        <div class="attack-hint">
            <h3>🎯 Attack Instructions - INTERCEPT THIS REQUEST!</h3>
            <p>
                When you submit a note below, the following request is sent to <code>/Lab-25/api/notes.php</code>:
            </p>
            <pre>{
    "noteable_type": "issue",
    "noteable_id": <?php echo $issue['id']; ?>,
    "content": "your comment text"
}</pre>
            <p style="margin-top: 1rem;">
                <strong>Modify the request to:</strong>
            </p>
            <pre>{
    "noteable_type": "personal_snippet",  // ← Change this!
    "noteable_id": 1,                     // ← Victim's snippet ID (1-5)
    "content": "@attacker was here"
}</pre>
            <p style="margin-top: 1rem;">
                After sending, check your <a href="activity.php" style="color: #fc6d26;">Activity page</a> 
                to see the leaked private snippet title!
            </p>
        </div>
        <?php endif; ?>

        <div class="section-header">
            <h2>💬 Activity / Notes</h2>
        </div>

        <div id="response-area"></div>

        <?php if (empty($notes)): ?>
        <div class="empty-state">
            <p>No notes yet. Be the first to comment!</p>
        </div>
        <?php else: ?>
        <ul class="notes-list">
            <?php foreach ($notes as $note): ?>
            <li class="note-item">
                <div class="note-header">
                    <span class="note-author">@<?php echo htmlspecialchars($note['author_name']); ?></span>
                    <span class="note-time"><?php echo formatDate($note['created_at']); ?></span>
                </div>
                <div class="note-content"><?php echo nl2br(htmlspecialchars($note['content'])); ?></div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <div class="note-form">
            <h3>Add Note</h3>
            <textarea id="note-content" placeholder="Write a comment... (This request is vulnerable - intercept it!)"></textarea>
            <button class="btn" onclick="submitNote()">💬 Comment</button>
        </div>
    </main>

    <script>
        function submitNote() {
            const content = document.getElementById('note-content').value;
            if (!content.trim()) {
                alert('Please enter a comment');
                return;
            }

            // This is the request that should be intercepted and modified!
            fetch('/Lab-25/api/notes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    noteable_type: 'issue',
                    noteable_id: <?php echo $issue['id']; ?>,
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                const responseArea = document.getElementById('response-area');
                if (data.success) {
                    responseArea.innerHTML = `
                        <div class="response-msg success">
                            ✅ ${data.message}<br>
                            <small>Note ID: ${data.note_id} | Target: ${data.target_type} #${data.target_id}</small>
                        </div>
                    `;
                    document.getElementById('note-content').value = '';
                    // Reload to show new note
                    setTimeout(() => location.reload(), 1500);
                } else {
                    responseArea.innerHTML = `
                        <div class="response-msg error">
                            ❌ ${data.error}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
