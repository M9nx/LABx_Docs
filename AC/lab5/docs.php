<?php
session_start();

// Read markdown file
$markdownFile = __DIR__ . '/LAB_DOCUMENTATION.md';
$markdownContent = '';

if (file_exists($markdownFile)) {
    $markdownContent = file_get_contents($markdownFile);
}

// Enhanced Markdown parser with full table support and code blocks in lists
function parseMarkdown($text) {
    $lines = explode("\n", $text);
    $html = '';
    $inCodeBlock = false;
    $codeBlockContent = '';
    $codeBlockLang = '';
    $inTable = false;
    $tableRows = [];
    $inList = false;
    $listType = '';
    $listItems = [];
    $currentListItem = '';
    $inListCodeBlock = false;
    $listCodeContent = '';
    $listCodeLang = '';
    
    foreach ($lines as $index => $line) {
        // Handle fenced code blocks (top-level)
        if (!$inList && preg_match('/^```(\w*)/', $line, $matches)) {
            if (!$inCodeBlock) {
                $inCodeBlock = true;
                $codeBlockLang = $matches[1] ?: 'text';
                $codeBlockContent = '';
            } else {
                $html .= '<div class="code-block"><div class="code-header">' . strtoupper($codeBlockLang) . '</div>';
                $html .= '<pre><code class="language-' . $codeBlockLang . '">' . htmlspecialchars($codeBlockContent) . '</code></pre></div>';
                $inCodeBlock = false;
                $codeBlockLang = '';
            }
            continue;
        }
        
        if ($inCodeBlock) {
            $codeBlockContent .= ($codeBlockContent ? "\n" : '') . $line;
            continue;
        }
        
        // Handle tables
        if (preg_match('/^\|(.+)\|$/', $line)) {
            if (!$inTable) {
                // Close any open list
                if ($inList) {
                    if ($currentListItem !== '') {
                        $listItems[] = parseInlineMarkdown($currentListItem);
                    }
                    $html .= '<' . $listType . ' class="doc-list">';
                    foreach ($listItems as $item) {
                        $html .= '<li>' . $item . '</li>';
                    }
                    $html .= '</' . $listType . '>';
                    $inList = false;
                    $listItems = [];
                    $currentListItem = '';
                }
                $inTable = true;
                $tableRows = [];
            }
            // Check if it's a separator row
            if (preg_match('/^\|[\s\-:|]+\|$/', $line)) {
                continue; // Skip separator row
            }
            $tableRows[] = $line;
            continue;
        } else if ($inTable) {
            // End table
            $html .= renderTable($tableRows);
            $inTable = false;
            $tableRows = [];
        }
        
        // Handle list items
        $isOrderedItem = preg_match('/^(\d+)\.\s+(.*)$/', $line, $orderedMatch);
        $isUnorderedItem = preg_match('/^[-*]\s+(.*)$/', $line, $unorderedMatch);
        $isIndentedContent = preg_match('/^(\s{2,}|\t)(.*)$/', $line, $indentMatch);
        
        if ($isOrderedItem || $isUnorderedItem) {
            if ($inList && $currentListItem !== '') {
                $listItems[] = parseListItem($currentListItem);
                $currentListItem = '';
            }
            
            if (!$inList) {
                $inList = true;
                $listType = $isOrderedItem ? 'ol' : 'ul';
                $listItems = [];
            }
            
            $currentListItem = $isOrderedItem ? $orderedMatch[2] : $unorderedMatch[1];
            continue;
        } else if ($inList && $isIndentedContent) {
            // Indented content belongs to current list item
            $indentedLine = $indentMatch[2];
            
            // Check for code block start in list item
            if (preg_match('/^```(\w*)/', $indentedLine, $codeMatch)) {
                if (!$inListCodeBlock) {
                    $inListCodeBlock = true;
                    $listCodeLang = $codeMatch[1] ?: 'text';
                    $listCodeContent = '';
                } else {
                    $currentListItem .= "\n<div class=\"code-block\"><div class=\"code-header\">" . strtoupper($listCodeLang) . "</div>";
                    $currentListItem .= "<pre><code class=\"language-" . $listCodeLang . "\">" . htmlspecialchars($listCodeContent) . "</code></pre></div>";
                    $inListCodeBlock = false;
                }
                continue;
            }
            
            if ($inListCodeBlock) {
                $listCodeContent .= ($listCodeContent ? "\n" : '') . $indentedLine;
                continue;
            }
            
            $currentListItem .= "\n" . $indentedLine;
            continue;
        } else if ($inList && trim($line) === '') {
            // Empty line might end list or be part of multi-paragraph item
            continue;
        } else if ($inList) {
            // Non-indented, non-list content ends the list
            if ($currentListItem !== '') {
                $listItems[] = parseListItem($currentListItem);
            }
            $html .= '<' . $listType . ' class="doc-list">';
            foreach ($listItems as $item) {
                $html .= '<li>' . $item . '</li>';
            }
            $html .= '</' . $listType . '>';
            $inList = false;
            $listItems = [];
            $currentListItem = '';
        }
        
        // Handle headers
        if (preg_match('/^(#{1,6})\s+(.*)$/', $line, $matches)) {
            $level = strlen($matches[1]);
            $text = parseInlineMarkdown($matches[2]);
            $html .= '<h' . $level . ' class="doc-h' . $level . '">' . $text . '</h' . $level . '>';
            continue;
        }
        
        // Handle blockquotes
        if (preg_match('/^>\s*(.*)$/', $line, $matches)) {
            $html .= '<blockquote class="doc-blockquote">' . parseInlineMarkdown($matches[1]) . '</blockquote>';
            continue;
        }
        
        // Handle horizontal rules
        if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', trim($line))) {
            $html .= '<hr class="doc-hr">';
            continue;
        }
        
        // Handle empty lines
        if (trim($line) === '') {
            continue;
        }
        
        // Regular paragraph
        $html .= '<p class="doc-paragraph">' . parseInlineMarkdown($line) . '</p>';
    }
    
    // Close any remaining open elements
    if ($inTable) {
        $html .= renderTable($tableRows);
    }
    
    if ($inList) {
        if ($currentListItem !== '') {
            $listItems[] = parseListItem($currentListItem);
        }
        $html .= '<' . $listType . ' class="doc-list">';
        foreach ($listItems as $item) {
            $html .= '<li>' . $item . '</li>';
        }
        $html .= '</' . $listType . '>';
    }
    
    return $html;
}

function parseListItem($content) {
    // Parse inline markdown for list item, preserving HTML already added
    $lines = explode("\n", $content);
    $result = parseInlineMarkdown($lines[0]);
    
    for ($i = 1; $i < count($lines); $i++) {
        $line = $lines[$i];
        if (strpos($line, '<div class="code-block">') !== false) {
            $result .= $line;
        } else {
            $result .= '<br>' . parseInlineMarkdown($line);
        }
    }
    
    return $result;
}

function renderTable($rows) {
    if (empty($rows)) return '';
    
    $html = '<div class="table-container"><table class="doc-table">';
    
    foreach ($rows as $index => $row) {
        $cells = array_map('trim', explode('|', trim($row, '|')));
        $tag = ($index === 0) ? 'th' : 'td';
        $rowTag = ($index === 0) ? 'thead' : (($index === 1) ? 'tbody' : '');
        
        if ($index === 0) $html .= '<thead>';
        if ($index === 1) $html .= '<tbody>';
        
        $html .= '<tr>';
        foreach ($cells as $cell) {
            $html .= '<' . $tag . '>' . parseInlineMarkdown(trim($cell)) . '</' . $tag . '>';
        }
        $html .= '</tr>';
        
        if ($index === 0) $html .= '</thead>';
    }
    
    $html .= '</tbody></table></div>';
    return $html;
}

function parseInlineMarkdown($text) {
    // Bold: **text** or __text__
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);
    
    // Italic: *text* or _text_
    $text = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $text);
    $text = preg_replace('/_([^_]+)_/', '<em>$1</em>', $text);
    
    // Inline code: `code`
    $text = preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $text);
    
    // Links: [text](url)
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" class="doc-link" target="_blank">$1</a>', $text);
    
    return $text;
}

$parsedContent = parseMarkdown($markdownContent);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - IDORLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
            max-width: 950px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .docs-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 3rem;
            backdrop-filter: blur(10px);
        }
        
        /* Documentation Typography */
        .doc-h1 {
            font-size: 2.2rem;
            color: #ff4444;
            margin: 0 0 1.5rem 0;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid rgba(255, 68, 68, 0.3);
        }
        .doc-h2 {
            font-size: 1.6rem;
            color: #ff6666;
            margin: 2.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .doc-h3 {
            font-size: 1.3rem;
            color: #ff8888;
            margin: 2rem 0 0.8rem 0;
        }
        .doc-h4 {
            font-size: 1.1rem;
            color: #ffaaaa;
            margin: 1.5rem 0 0.6rem 0;
        }
        .doc-paragraph {
            color: #ccc;
            line-height: 1.9;
            margin: 1rem 0;
        }
        
        /* Inline Code */
        .inline-code {
            background: rgba(255, 68, 68, 0.15);
            color: #ff8888;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9em;
        }
        
        /* Code Blocks */
        .code-block {
            margin: 1.5rem 0;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255, 68, 68, 0.2);
        }
        .code-header {
            background: rgba(255, 68, 68, 0.2);
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            color: #ff8888;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .code-block pre {
            background: rgba(0, 0, 0, 0.4);
            margin: 0;
            padding: 1.2rem;
            overflow-x: auto;
        }
        .code-block code {
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            color: #e0e0e0;
            line-height: 1.6;
        }
        
        /* Tables */
        .table-container {
            overflow-x: auto;
            margin: 1.5rem 0;
        }
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        .doc-table th {
            background: rgba(255, 68, 68, 0.2);
            color: #ff8888;
            padding: 0.9rem 1rem;
            text-align: left;
            font-weight: 600;
            border: 1px solid rgba(255, 68, 68, 0.2);
        }
        .doc-table td {
            padding: 0.8rem 1rem;
            border: 1px solid rgba(255, 68, 68, 0.1);
            color: #ccc;
        }
        .doc-table tr:nth-child(even) td {
            background: rgba(255, 255, 255, 0.02);
        }
        .doc-table tr:hover td {
            background: rgba(255, 68, 68, 0.05);
        }
        
        /* Lists */
        .doc-list {
            margin: 1rem 0 1rem 1.5rem;
            color: #ccc;
            line-height: 1.9;
        }
        .doc-list li {
            margin: 0.6rem 0;
            padding-left: 0.5rem;
        }
        .doc-list .code-block {
            margin: 1rem 0;
        }
        
        /* Blockquotes */
        .doc-blockquote {
            border-left: 4px solid #ff4444;
            background: rgba(255, 68, 68, 0.1);
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 8px 8px 0;
            color: #ddd;
            font-style: italic;
        }
        
        /* Links */
        .doc-link {
            color: #ff6666;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 102, 102, 0.3);
            transition: all 0.3s;
        }
        .doc-link:hover {
            color: #ff8888;
            border-bottom-color: #ff8888;
        }
        
        /* Horizontal Rule */
        .doc-hr {
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 68, 68, 0.5), transparent);
            margin: 2rem 0;
        }

        /* Strong emphasis */
        strong {
            color: #ff8888;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔑 IDORLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php?id=<?php echo $_SESSION['username']; ?>">My Account</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="docs-card">
            <?php echo $parsedContent; ?>
        </div>
    </div>
</body>
</html>
