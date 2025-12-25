<?php
// Lab 23: Create Tag
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tagName = trim($_POST['tag_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tagColor = $_POST['tag_color'] ?? '#6366f1';
    
    if (empty($tagName)) {
        $error = 'Tag name is required.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Generate tag ID and internal ID
            $tagId = 'TAG_' . strtoupper(substr($_SESSION['user_id'], 0, 1)) . '_' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $internalId = 49790000 + rand(200, 999); // Assign new range for new tags
            
            $stmt = $pdo->prepare("INSERT INTO tags (tag_id, user_id, tag_name, description, tag_color, internal_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tagId, $_SESSION['user_id'], $tagName, $description, $tagColor, $internalId]);
            
            $encodedId = encodeTagId($internalId);
            
            logActivity($_SESSION['user_id'], 'create_tag', 'tag', $tagId, 'Created tag: ' . $tagName);
            
            $success = "Tag created! Internal ID: $internalId | Encoded: $encodedId";
            
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Tag - TagScope | Lab 23</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .header {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.5rem; font-weight: bold; color: #a78bfa; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a78bfa;
            text-decoration: none;
            border-radius: 6px;
        }
        .container { max-width: 600px; margin: 0 auto; padding: 2rem; }
        .form-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
        }
        .form-card h1 { color: #a78bfa; margin-bottom: 0.5rem; }
        .form-card .subtitle { color: #64748b; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 1rem;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #6366f1;
        }
        .color-picker {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .color-picker input[type="color"] {
            width: 50px;
            height: 40px;
            padding: 0;
            border: none;
            cursor: pointer;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }
        .btn-secondary {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
        .alert-success code {
            color: #f59e0b;
            background: rgba(0,0,0,0.3);
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">🏷️ TagScope</div>
        <nav class="nav-links">
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="assets.php">📦 Assets</a>
            <a href="tags.php">🏷️ Tags</a>
        </nav>
    </header>

    <div class="container">
        <div class="form-card">
            <h1>🏷️ Create New Tag</h1>
            <p class="subtitle">Create a custom tag to categorize your assets</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?= e($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= e($success) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="tag_name">Tag Name</label>
                    <input type="text" id="tag_name" name="tag_name" placeholder="e.g., Production-Critical" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea id="description" name="description" placeholder="Describe this tag's purpose..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Tag Color</label>
                    <div class="color-picker">
                        <input type="color" id="tag_color" name="tag_color" value="#6366f1">
                        <span id="colorValue">#6366f1</span>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Tag</button>
                    <a href="tags.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tag_color').addEventListener('input', function() {
            document.getElementById('colorValue').textContent = this.value;
        });
    </script>
</body>
</html>
