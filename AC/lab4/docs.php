<?php
/**
 * Lab 4 Documentation - Markdown Parser with Dark Theme
 * Reads LAB_DOCUMENTATION.md and renders it with proper styling
 */
session_start();

// Improved Markdown parser that handles indented code blocks and tables
function parseMarkdown($text) {
    $lines = explode("\n", $text);
    $html = '';
    $inCodeBlock = false;
    $codeBuffer = '';
    $inTable = false;
    $tableRows = [];
    $i = 0;
    $count = count($lines);
    
    while ($i < $count) {
        $line = $lines[$i];
        $trimmedLine = trim($line);
        
        // Code blocks (``` with optional language, handles indented too)
        if (preg_match('/^\s*```(\w*)/', $line, $matches)) {
            if ($inCodeBlock) {
                // End code block
                $html .= '<pre class="code-block"><code>' . htmlspecialchars(rtrim($codeBuffer)) . '</code></pre>';
                $codeBuffer = '';
                $inCodeBlock = false;
            } else {
                // Start code block
                $inCodeBlock = true;
            }
            $i++;
            continue;
        }
        
        if ($inCodeBlock) {
            $codeBuffer .= $line . "\n";
            $i++;
            continue;
        }
        
        // Handle tables
        if (preg_match('/^\|(.+)\|$/', $trimmedLine)) {
            if (!$inTable) {
                $inTable = true;
                $tableRows = [];
            }
            
            // Skip separator row
            if (preg_match('/^\|[\s\-:|]+\|$/', $trimmedLine)) {
                $i++;
                continue;
            }
            
            $tableRows[] = $trimmedLine;
            $i++;
            continue;
        } else if ($inTable) {
            // End table
            $html .= '<div class="table-wrapper"><table class="doc-table">';
            foreach ($tableRows as $idx => $row) {
                $cells = array_map('trim', explode('|', trim($row, '|')));
                $tag = $idx === 0 ? 'th' : 'td';
                $rowHtml = $idx === 0 ? '<thead><tr>' : '<tr>';
                foreach ($cells as $cell) {
                    $rowHtml .= "<{$tag}>" . formatInline($cell) . "</{$tag}>";
                }
                $rowHtml .= $idx === 0 ? '</tr></thead><tbody>' : '</tr>';
                $html .= $rowHtml;
            }
            $html .= '</tbody></table></div>';
            $inTable = false;
            $tableRows = [];
        }
        
        // Empty lines - just skip
        if ($trimmedLine === '') {
            $i++;
            continue;
        }
        
        // Horizontal rule
        if (preg_match('/^-{3,}$/', $trimmedLine)) {
            $html .= '<hr>';
            $i++;
            continue;
        }
        
        // Headers
        if (preg_match('/^(#{1,6})\s+(.+)$/', $trimmedLine, $matches)) {
            $level = strlen($matches[1]);
            $text = formatInline($matches[2]);
            $html .= "<h{$level}>{$text}</h{$level}>";
            $i++;
            continue;
        }
        
        // Ordered list item
        if (preg_match('/^\d+\.\s+(.+)$/', $trimmedLine, $matches)) {
            $html .= '<ol>';
            while ($i < $count) {
                $currentLine = trim($lines[$i]);
                if (preg_match('/^\d+\.\s+(.+)$/', $currentLine, $m)) {
                    $html .= '<li>' . formatInline($m[1]) . '</li>';
                    $i++;
                } elseif (preg_match('/^\s*```/', $lines[$i])) {
                    // Handle code block inside list
                    $i++; // skip opening ```
                    $codeContent = '';
                    while ($i < $count && !preg_match('/^\s*```/', $lines[$i])) {
                        $codeContent .= $lines[$i] . "\n";
                        $i++;
                    }
                    $i++; // skip closing ```
                    $html .= '<pre class="code-block"><code>' . htmlspecialchars(rtrim($codeContent)) . '</code></pre>';
                } elseif ($currentLine === '' || preg_match('/^#{1,6}\s/', $currentLine) || preg_match('/^-{3,}$/', $currentLine)) {
                    break;
                } elseif (preg_match('/^[\-\*]\s+/', $currentLine)) {
                    break;
                } else {
                    $i++;
                }
            }
            $html .= '</ol>';
            continue;
        }
        
        // Unordered list item
        if (preg_match('/^[\-\*]\s+(.+)$/', $trimmedLine, $matches)) {
            $html .= '<ul>';
            while ($i < $count) {
                $currentLine = trim($lines[$i]);
                if (preg_match('/^[\-\*]\s+(.+)$/', $currentLine, $m)) {
                    $html .= '<li>' . formatInline($m[1]) . '</li>';
                    $i++;
                } elseif (preg_match('/^\s*```/', $lines[$i])) {
                    // Handle code block inside list
                    $i++; // skip opening ```
                    $codeContent = '';
                    while ($i < $count && !preg_match('/^\s*```/', $lines[$i])) {
                        $codeContent .= $lines[$i] . "\n";
                        $i++;
                    }
                    $i++; // skip closing ```
                    $html .= '<pre class="code-block"><code>' . htmlspecialchars(rtrim($codeContent)) . '</code></pre>';
                } elseif ($currentLine === '' || preg_match('/^#{1,6}\s/', $currentLine) || preg_match('/^-{3,}$/', $currentLine)) {
                    break;
                } elseif (preg_match('/^\d+\.\s+/', $currentLine)) {
                    break;
                } else {
                    $i++;
                }
            }
            $html .= '</ul>';
            continue;
        }
        
        // Blockquote
        if (preg_match('/^>\s*(.*)$/', $trimmedLine, $matches)) {
            $html .= '<blockquote>' . formatInline($matches[1]) . '</blockquote>';
            $i++;
            continue;
        }
        
        // Regular paragraph
        $html .= '<p>' . formatInline($trimmedLine) . '</p>';
        $i++;
    }
    
    // Handle unclosed table
    if ($inTable && !empty($tableRows)) {
        $html .= '<div class="table-wrapper"><table class="doc-table">';
        foreach ($tableRows as $idx => $row) {
            $cells = array_map('trim', explode('|', trim($row, '|')));
            $tag = $idx === 0 ? 'th' : 'td';
            $rowHtml = $idx === 0 ? '<thead><tr>' : '<tr>';
            foreach ($cells as $cell) {
                $rowHtml .= "<{$tag}>" . formatInline($cell) . "</{$tag}>";
            }
            $rowHtml .= $idx === 0 ? '</tr></thead><tbody>' : '</tr>';
            $html .= $rowHtml;
        }
        $html .= '</tbody></table></div>';
    }
    
    return $html;
}

function formatInline($text) {
    // Inline code
    $text = preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $text);
    // Bold and italic
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    // Links
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
    
    return $text;
}

// Read the markdown file
$markdownFile = __DIR__ . '/LAB_DOCUMENTATION.md';
$markdownContent = file_exists($markdownFile) ? file_get_contents($markdownFile) : '# Documentation not found

The LAB_DOCUMENTATION.md file could not be loaded.';
$htmlContent = parseMarkdown($markdownContent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 4 Documentation - User Role Modification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #e0e0e0;
            line-height: 1.8;
            min-height: 100vh;
        }

        .nav-bar {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-content {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            color: #ff4444;
            font-size: 1.3rem;
            font-weight: 700;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            color: #ccc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #ff4444;
        }

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
            font-size: 0.9rem;
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
            padding: 3rem 2rem;
        }

        /* Typography */
        h1 {
            color: #ff4444;
            font-size: 2.2rem;
            margin: 2.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ff4444;
        }

        h2 {
            color: #ff6666;
            font-size: 1.6rem;
            margin: 2rem 0 1rem 0;
            padding-bottom: 0.3rem;
            border-bottom: 1px solid #333;
        }

        h3 {
            color: #ff8888;
            font-size: 1.3rem;
            margin: 1.5rem 0 0.8rem 0;
        }

        h4 {
            color: #ffaaaa;
            font-size: 1.1rem;
            margin: 1.2rem 0 0.6rem 0;
        }

        p {
            margin: 1rem 0;
            color: #ccc;
        }

        strong {
            color: #fff;
        }

        em {
            color: #ffcccc;
            font-style: italic;
        }

        a {
            color: #ff6666;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        hr {
            border: none;
            border-top: 1px solid #333;
            margin: 2.5rem 0;
        }

        /* Lists */
        ul, ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        li {
            margin: 0.5rem 0;
            color: #ccc;
        }

        li strong {
            color: #ff8888;
        }

        /* Blockquotes */
        blockquote {
            border-left: 4px solid #ff4444;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            background: rgba(255, 68, 68, 0.1);
            border-radius: 0 8px 8px 0;
            color: #ddd;
            font-style: italic;
        }

        /* Code */
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1.5rem 0;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            color: #00ff00;
            line-height: 1.5;
        }

        .code-block code {
            color: #00ff00;
            background: none;
            padding: 0;
            border: none;
        }

        .inline-code {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 4px;
            padding: 0.15rem 0.5rem;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9em;
            color: #ff6666;
        }

        /* Tables */
        .table-wrapper {
            overflow-x: auto;
            margin: 1.5rem 0;
        }

        .doc-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            overflow: hidden;
        }

        .doc-table th {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            padding: 0.8rem 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ff4444;
        }

        .doc-table td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.1);
            color: #ccc;
        }

        .doc-table tr:last-child td {
            border-bottom: none;
        }

        .doc-table tr:hover td {
            background: rgba(255, 68, 68, 0.05);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            border-top: 1px solid #333;
            color: #666;
        }

        .footer a {
            color: #ff4444;
            text-decoration: none;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }
    </style>
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-content">
            <a href="index.php" class="nav-brand">🔐 RoleLab - Lab 4</a>
            <div class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php echo $htmlContent; ?>
        
        <div class="nav-buttons">
            <a href="index.php" class="btn btn-primary">← Back to Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">Lab Description</a>
            <a href="../index.php" class="btn btn-secondary">All Labs</a>
        </div>
    </div>

    <footer class="footer">
        <p>Lab 4: User Role Can Be Modified in User Profile | Access Control Vulnerabilities</p>
    </footer>
</body>
</html>
