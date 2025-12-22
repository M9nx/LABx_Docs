<?php
/**
 * Lab 2 Documentation - Markdown Parser with Dark Theme
 * Reads LAB_DOCUMENTATION.md and renders it with proper styling
 */

// Improved Markdown parser that handles indented code blocks
function parseMarkdown($text) {
    $lines = explode("\n", $text);
    $html = '';
    $inCodeBlock = false;
    $codeBuffer = '';
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
            // Remove common leading whitespace for indented code blocks
            $codeBuffer .= $line . "\n";
            $i++;
            continue;
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
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);
    
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
    <title>Lab 2 Documentation - Unprotected Admin with Unpredictable URL</title>
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
            <a href="index.php" class="nav-brand">🏢 TechCorp - Lab 2</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="login.php">Login</a>
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
        <p>Lab 2: Unprotected Admin with Unpredictable URL | Access Control Vulnerabilities</p>
    </footer>
</body>
</html>
