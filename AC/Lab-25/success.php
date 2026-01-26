<?php
require_once 'config.php';
require_once '../progress.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$success = false;
$error = '';
$submittedTitle = '';

// Get victim's private snippet titles (for validation)
$stmt = $pdo->query("
    SELECT title FROM personal_snippets 
    WHERE author_id = (SELECT id FROM users WHERE username = 'victim')
    AND visibility = 'private'
");
$victimSnippetTitles = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedTitle = trim($_POST['snippet_title'] ?? '');
    
    if (empty($submittedTitle)) {
        $error = 'Please enter the leaked snippet title.';
    } else {
        // Check if the submitted title matches any of victim's private snippets
        $found = false;
        foreach ($victimSnippetTitles as $title) {
            if (stripos($title, $submittedTitle) !== false || stripos($submittedTitle, $title) !== false) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            $success = true;
            markLabSolved(25);
        } else {
            $error = 'Incorrect snippet title. Make sure you exploited the IDOR vulnerability and checked your Activity page for the leaked title.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Complete - Lab 25</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }

        /* Navigation */
        .navbar {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fc6d26;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .nav-brand svg {
            width: 32px;
            height: 32px;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            color: #b0b0b0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #fc6d26;
        }

        .user-badge {
            background: linear-gradient(135deg, rgba(252, 109, 38, 0.2), rgba(252, 109, 38, 0.1));
            border: 1px solid rgba(252, 109, 38, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            color: #fc6d26;
            font-weight: 600;
        }

        /* Main Container */
        .main-container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        /* Success Card */
        .success-card {
            background: linear-gradient(135deg, rgba(76, 217, 100, 0.1), rgba(76, 217, 100, 0.02));
            border: 2px solid rgba(76, 217, 100, 0.5);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #4cd964, #2ecc71);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: #fff;
            box-shadow: 0 10px 40px rgba(76, 217, 100, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .success-card h1 {
            color: #4cd964;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .success-card p {
            color: #b0b0b0;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        /* Submission Form */
        .submission-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .submission-card h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .submission-card h2 i {
            color: #fc6d26;
        }

        .submission-card p {
            color: #b0b0b0;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #fff;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #fc6d26;
            box-shadow: 0 0 20px rgba(252, 109, 38, 0.2);
        }

        .form-group input::placeholder {
            color: #666;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(252, 109, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Error Alert */
        .error-alert {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.3);
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ff6b6b;
        }

        /* Hint Box */
        .hint-box {
            background: linear-gradient(135deg, rgba(252, 109, 38, 0.1), rgba(252, 109, 38, 0.05));
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .hint-box h3 {
            color: #fc6d26;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.75rem;
        }

        .hint-box p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        .hint-box ol {
            color: #b0b0b0;
            padding-left: 1.25rem;
            margin-top: 0.75rem;
        }

        .hint-box ol li {
            margin-bottom: 0.5rem;
        }

        .hint-box a {
            color: #fc6d26;
            text-decoration: none;
        }

        .hint-box a:hover {
            text-decoration: underline;
        }

        /* Buttons Row */
        .buttons-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* Achievement Badge */
        .achievement-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, rgba(252, 109, 38, 0.2), rgba(252, 109, 38, 0.1));
            border: 1px solid rgba(252, 109, 38, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            color: #fc6d26;
            font-weight: 600;
            margin-top: 1rem;
        }

        /* What You Learned */
        .learned-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .learned-card h2 {
            color: #fff;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .learned-card h2 i {
            color: #4cd964;
        }

        .learned-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .learned-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .learned-item i {
            color: #4cd964;
            margin-top: 4px;
        }

        .learned-item p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        .learned-item strong {
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <svg viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 0L0 9.14v13.72L16 32l16-9.14V9.14L16 0zm0 4.57l10.29 5.86L16 16.29 5.71 10.43 16 4.57zM3.43 12.57l11.14 6.29v9.71L3.43 22.29v-9.72zm15.14 16v-9.71l11.14-6.29v9.72l-11.14 6.28z"/>
                </svg>
                Lab 25 - Notes IDOR
            </a>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="projects.php"><i class="fas fa-folder"></i> Projects</a>
                <a href="snippets.php"><i class="fas fa-code"></i> Snippets</a>
                <a href="activity.php"><i class="fas fa-history"></i> Activity</a>
                <div class="user-badge">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </div>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <?php if ($success): ?>
        <!-- Success State -->
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h1>🎉 Lab Completed!</h1>
            <p>
                Excellent work! You have successfully exploited the <strong>Notes IDOR vulnerability</strong> 
                and discovered the information leak in the activity log. The submitted snippet title 
                "<strong><?php echo htmlspecialchars($submittedTitle); ?></strong>" confirms you accessed 
                private information belonging to another user.
            </p>
            <div class="achievement-badge">
                <i class="fas fa-shield-alt"></i>
                IDOR & Information Leak Expert
            </div>
        </div>

        <div class="learned-card">
            <h2><i class="fas fa-graduation-cap"></i> What You Learned</h2>
            <div class="learned-list">
                <div class="learned-item">
                    <i class="fas fa-check-circle"></i>
                    <p><strong>IDOR Exploitation:</strong> Modifying the <code>noteable_type</code> parameter from "issue" to "personal_snippet" bypassed authorization checks.</p>
                </div>
                <div class="learned-item">
                    <i class="fas fa-check-circle"></i>
                    <p><strong>Information Disclosure:</strong> The activity log revealed private snippet titles - sensitive information that should never be exposed to unauthorized users.</p>
                </div>
                <div class="learned-item">
                    <i class="fas fa-check-circle"></i>
                    <p><strong>Defense Strategies:</strong> Proper authorization checks should verify user permissions for EVERY resource type, and logging should sanitize sensitive data.</p>
                </div>
                <div class="learned-item">
                    <i class="fas fa-check-circle"></i>
                    <p><strong>Real-World Impact:</strong> This vulnerability (based on GitLab CVE) could expose confidential code snippets, API keys, and other sensitive developer data.</p>
                </div>
            </div>
        </div>

        <div class="buttons-row">
            <a href="../index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Back to All Labs
            </a>
            <a href="docs.php" class="btn btn-secondary">
                <i class="fas fa-book"></i> Read Documentation
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Try Again
            </a>
        </div>

        <?php else: ?>
        <!-- Submission Form -->
        <div class="submission-card">
            <h2><i class="fas fa-flag-checkered"></i> Verify Your Exploit</h2>
            <p>
                To complete this lab, you need to demonstrate that you successfully exploited the 
                IDOR vulnerability and discovered private information through the information leak.
                Enter the title of one of the victim's private snippets that you discovered through 
                the attack.
            </p>

            <?php if ($error): ?>
            <div class="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="success.php">
                <div class="form-group">
                    <label for="snippet_title">Leaked Private Snippet Title:</label>
                    <input type="text" id="snippet_title" name="snippet_title" 
                           placeholder="Enter the private snippet title you discovered..."
                           value="<?php echo htmlspecialchars($submittedTitle); ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Submit Answer
                </button>
            </form>
        </div>

        <div class="hint-box">
            <h3><i class="fas fa-lightbulb"></i> Need a Hint?</h3>
            <p>If you haven't completed the attack yet, here are the steps:</p>
            <ol>
                <li>Login as <strong>attacker</strong> (attacker/attacker123)</li>
                <li>Go to <a href="projects.php">Projects</a> → Any project → Any issue</li>
                <li>Open browser DevTools (F12) → Network tab</li>
                <li>Submit a note and intercept the request</li>
                <li>Change <code>noteable_type</code> from "issue" to "personal_snippet"</li>
                <li>Change <code>noteable_id</code> to a victim's snippet ID (1-5)</li>
                <li>Check your <a href="activity.php">Activity</a> page for the leaked snippet title</li>
                <li>Submit that title here!</li>
            </ol>
        </div>

        <div class="buttons-row">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="docs.php" class="btn btn-secondary">
                <i class="fas fa-book"></i> View Documentation
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
