<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prevention Strategies - Lab 19 Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.4rem;
            font-weight: bold;
            color: #818cf8;
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .nav-links { display: flex; gap: 1.5rem; }
        .nav-links a { color: #a5b4fc; text-decoration: none; }
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
        }
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.02);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem 1rem;
            position: sticky;
            top: 80px;
            height: calc(100vh - 80px);
        }
        .sidebar-title {
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            padding: 0 0.75rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.25rem;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(99, 102, 241, 0.1);
            color: #a5b4fc;
        }
        .sidebar-nav a.active { border-left: 3px solid #6366f1; }
        .main-content {
            flex: 1;
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .breadcrumb {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
        .breadcrumb a { color: #a5b4fc; text-decoration: none; }
        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .page-subtitle {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .content-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .content-section h2 {
            color: #a5b4fc;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .content-section h3 {
            color: #c7d2fe;
            font-size: 1.1rem;
            margin: 1.5rem 0 0.75rem;
        }
        .content-section p {
            color: #94a3b8;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .content-section ul {
            list-style: none;
            margin: 1rem 0;
        }
        .content-section li {
            padding: 0.5rem 0;
            color: #94a3b8;
            padding-left: 1.5rem;
            position: relative;
        }
        .content-section li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
        }
        .code-block {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 1rem;
            overflow-x: auto;
            margin: 1rem 0;
            position: relative;
        }
        .code-block pre {
            color: #e6edf3;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .code-label {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        .defense-card {
            background: rgba(16, 185, 129, 0.05);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .defense-card h4 {
            color: #6ee7b7;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .defense-card p { color: #94a3b8; font-size: 0.9rem; }
        .success-box {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            padding: 1rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
        }
        .success-box p { color: #6ee7b7; margin: 0; }
        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
            padding: 1rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
        }
        .info-box p { color: #a5b4fc; margin: 0; }
        .checklist {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .checklist h4 {
            color: #a5b4fc;
            margin-bottom: 1rem;
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .checklist-item:last-child { border-bottom: none; }
        .checklist-item span { color: #94a3b8; }
        .check-icon { color: #10b981; font-size: 1.2rem; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            color: #a5b4fc;
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: #6366f1;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">📁</div>
                ProjectHub
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="docs.php">Documentation</a>
                <a href="lab-description.php">Instructions</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3 class="sidebar-title">Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">📚 Overview</a></li>
                <li><a href="docs-vulnerability.php">🔓 Vulnerability</a></li>
                <li><a href="docs-exploitation.php">⚡ Exploitation</a></li>
                <li><a href="docs-prevention.php" class="active">🛡️ Prevention</a></li>
                <li><a href="docs-comparison.php">⚖️ Code Comparison</a></li>
                <li><a href="docs-references.php">📖 References</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">
                <a href="docs.php">Documentation</a> / Prevention Strategies
            </div>

            <h1 class="page-title">Prevention Strategies</h1>
            <p class="page-subtitle">Secure coding practices to prevent IDOR vulnerabilities</p>

            <div class="content-section">
                <h2>🛡️ Primary Defense: Ownership Validation</h2>
                <p>
                    The most effective defense against IDOR is to <strong>always verify ownership</strong> 
                    before performing any operation on a resource. This means including the user's ID 
                    in every query that accesses or modifies data.
                </p>

                <h3>Secure Delete Implementation</h3>
                <div class="code-block">
                    <span class="code-label">SECURE ✓</span>
                    <pre><span style="color: #7ee787;">// SECURE: Verify ownership before deletion</span>
session_start();
$user_id = $_SESSION['user_id'];
$saved_id = $_GET['saved_id'];

<span style="color: #7ee787;">// Include user_id in WHERE clause</span>
$stmt = $pdo->prepare("
    DELETE FROM saved_projects 
    WHERE id = ? <span style="color: #7ee787;">AND user_id = ?</span>
");
$stmt->execute([$saved_id, $user_id]);

<span style="color: #7ee787;">// Check if deletion actually occurred</span>
if ($stmt->rowCount() === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authorized or not found'
    ]);
    exit;
}

echo json_encode(['success' => true]);</pre>
                </div>

                <div class="success-box">
                    <p>
                        ✅ <strong>Key Fix:</strong> Adding <code>AND user_id = ?</code> ensures only 
                        the owner can delete their own saved projects.
                    </p>
                </div>
            </div>

            <div class="content-section">
                <h2>🔐 Defense Layers</h2>
                <p>
                    Implement multiple layers of defense to protect against IDOR attacks:
                </p>

                <div class="defense-card">
                    <h4>🔒 Layer 1: Session Validation</h4>
                    <p>Always verify the user is authenticated before processing any request.</p>
                </div>

                <div class="code-block">
                    <pre><span style="color: #7ee787;">// Verify session exists and is valid</span>
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Authentication required']));
}</pre>
                </div>

                <div class="defense-card">
                    <h4>🛡️ Layer 2: Ownership Check</h4>
                    <p>Query should always include the authenticated user's ID.</p>
                </div>

                <div class="code-block">
                    <pre><span style="color: #7ee787;">// Two-step verification approach</span>
$stmt = $pdo->prepare("
    SELECT user_id FROM saved_projects WHERE id = ?
");
$stmt->execute([$saved_id]);
$record = $stmt->fetch();

<span style="color: #7ee787;">// Verify ownership explicitly</span>
if (!$record || $record['user_id'] !== $_SESSION['user_id']) {
    http_response_code(403);
    die(json_encode(['error' => 'Access denied']));
}

<span style="color: #7ee787;">// Now safe to delete</span>
$pdo->prepare("DELETE FROM saved_projects WHERE id = ?")->execute([$saved_id]);</pre>
                </div>

                <div class="defense-card">
                    <h4>📊 Layer 3: Audit Logging</h4>
                    <p>Log all deletion attempts for security monitoring and incident response.</p>
                </div>

                <div class="code-block">
                    <pre><span style="color: #7ee787;">// Log the deletion action</span>
$logStmt = $pdo->prepare("
    INSERT INTO audit_log (user_id, action, resource_type, resource_id, ip_address, timestamp)
    VALUES (?, 'DELETE', 'saved_project', ?, ?, NOW())
");
$logStmt->execute([
    $_SESSION['user_id'],
    $saved_id,
    $_SERVER['REMOTE_ADDR']
]);</pre>
                </div>

                <div class="defense-card">
                    <h4>🎲 Layer 4: Use Non-Sequential IDs</h4>
                    <p>Use UUIDs or random identifiers to prevent enumeration attacks.</p>
                </div>

                <div class="code-block">
                    <pre><span style="color: #7ee787;">// Instead of auto-increment IDs</span>
CREATE TABLE saved_projects (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    ...
);

<span style="color: #7ee787;">// Example UUID: a1b2c3d4-e5f6-7890-abcd-ef1234567890</span></pre>
                </div>
            </div>

            <div class="content-section">
                <h2>✅ Security Checklist</h2>
                
                <div class="checklist">
                    <h4>IDOR Prevention Checklist</h4>
                    
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Always include <code>user_id</code> in WHERE clauses for data operations</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Verify session is valid before processing any request</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Check <code>rowCount()</code> after UPDATE/DELETE to verify operation success</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Return generic error messages (don't reveal if ID exists)</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Implement audit logging for sensitive operations</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Consider using UUIDs instead of sequential IDs</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Add rate limiting to prevent mass enumeration</span>
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon">☐</span>
                        <span>Implement CSRF tokens for state-changing operations</span>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h2>🏗️ Architectural Best Practices</h2>

                <h3>Access Control Layer Pattern</h3>
                <div class="code-block">
                    <pre><span style="color: #7ee787;">// Create a reusable access control function</span>
function canAccessResource($pdo, $resourceType, $resourceId, $userId) {
    $tables = [
        'saved_project' => 'saved_projects',
        'comment' => 'comments',
        'profile' => 'profiles'
    ];
    
    if (!isset($tables[$resourceType])) {
        return false;
    }
    
    $table = $tables[$resourceType];
    $stmt = $pdo->prepare("SELECT id FROM {$table} WHERE id = ? AND user_id = ?");
    $stmt->execute([$resourceId, $userId]);
    
    return $stmt->rowCount() > 0;
}

<span style="color: #7ee787;">// Usage</span>
if (!canAccessResource($pdo, 'saved_project', $saved_id, $_SESSION['user_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Access denied']));
}</pre>
                </div>

                <div class="info-box">
                    <p>
                        💡 <strong>Pro Tip:</strong> Consider using an authorization framework or library 
                        (like RBAC or ABAC) for complex applications with multiple user roles and permissions.
                    </p>
                </div>
            </div>

            <div class="content-section">
                <h2>🔄 Testing for IDOR</h2>
                <p>Regularly test your application for IDOR vulnerabilities:</p>
                
                <ul>
                    <li>Create multiple test accounts with different permission levels</li>
                    <li>Try accessing resources belonging to other users</li>
                    <li>Test all CRUD operations (Create, Read, Update, Delete)</li>
                    <li>Use automated tools like Burp Suite's Autorize extension</li>
                    <li>Include IDOR testing in your CI/CD pipeline</li>
                    <li>Perform regular security audits and penetration testing</li>
                </ul>
            </div>

            <div class="nav-buttons">
                <a href="docs-exploitation.php" class="nav-btn">← Exploitation Techniques</a>
                <a href="docs-comparison.php" class="nav-btn">Code Comparison →</a>
            </div>
        </main>
    </div>
</body>
</html>
