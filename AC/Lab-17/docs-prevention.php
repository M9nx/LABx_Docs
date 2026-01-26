<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prevention - IDOR Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(252, 109, 38, 0.3); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.75rem; font-size: 1.3rem; font-weight: bold; color: #fc6d26; text-decoration: none; }
        .logo svg { width: 32px; height: 32px; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #fc6d26; }
        .layout { display: flex; max-width: 1400px; margin: 0 auto; }
        .sidebar { width: 280px; min-height: calc(100vh - 60px); background: rgba(0, 0, 0, 0.3); border-right: 1px solid rgba(252, 109, 38, 0.2); padding: 1.5rem; position: sticky; top: 60px; height: calc(100vh - 60px); overflow-y: auto; }
        .sidebar h3 { color: #fc6d26; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(252, 109, 38, 0.3); }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a { display: block; padding: 0.6rem 1rem; color: #aaa; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-size: 0.9rem; }
        .sidebar-nav a:hover { background: rgba(252, 109, 38, 0.1); color: #fc6d26; }
        .sidebar-nav a.active { background: rgba(252, 109, 38, 0.2); color: #fc6d26; font-weight: 500; }
        .sidebar-nav .section-title { color: #666; font-size: 0.75rem; text-transform: uppercase; padding: 1rem 1rem 0.5rem; margin-top: 0.5rem; }
        .content { flex: 1; padding: 2rem 3rem; max-width: 900px; }
        .content h1 { color: #fc6d26; font-size: 2rem; margin-bottom: 0.5rem; }
        .content h2 { color: #fc6d26; font-size: 1.5rem; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(252, 109, 38, 0.3); }
        .content h3 { color: #e0e0e0; font-size: 1.2rem; margin: 1.5rem 0 0.75rem; }
        .content p { color: #aaa; line-height: 1.8; margin-bottom: 1rem; }
        .content ul, .content ol { color: #aaa; line-height: 1.8; margin-bottom: 1rem; padding-left: 1.5rem; }
        .content li { margin-bottom: 0.5rem; }
        .content code { background: rgba(0, 0, 0, 0.4); padding: 0.2rem 0.4rem; border-radius: 4px; color: #88ff88; font-family: 'Consolas', monospace; font-size: 0.9em; }
        .code-block { background: #0d0d0d; border: 1px solid #333; border-radius: 10px; padding: 1.25rem; font-family: 'Consolas', monospace; font-size: 0.85rem; color: #88ff88; overflow-x: auto; margin: 1rem 0; line-height: 1.6; }
        .code-block .comment { color: #666; }
        .code-block .vulnerable { color: #ff6666; }
        .code-block .secure { color: #00c853; }
        .info-box { border-radius: 10px; padding: 1rem 1.25rem; margin: 1rem 0; }
        .info-box.success { background: rgba(0, 200, 100, 0.1); border: 1px solid rgba(0, 200, 100, 0.3); }
        .info-box.info { background: rgba(252, 109, 38, 0.1); border: 1px solid rgba(252, 109, 38, 0.3); }
        .info-box h4 { margin-bottom: 0.5rem; }
        .info-box.success h4 { color: #66ff99; }
        .info-box.info h4 { color: #fc6d26; }
        .info-box p { margin-bottom: 0; }
        .nav-buttons { display: flex; justify-content: space-between; margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .nav-btn { padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(252, 109, 38, 0.3); border-radius: 8px; color: #ccc; text-decoration: none; transition: all 0.3s; }
        .nav-btn:hover { background: rgba(252, 109, 38, 0.2); color: #fc6d26; }
        .fix-card { background: rgba(0, 200, 100, 0.05); border: 1px solid rgba(0, 200, 100, 0.3); border-radius: 12px; padding: 1.5rem; margin: 1.5rem 0; }
        .fix-card h3 { color: #66ff99; margin-bottom: 1rem; }
        @media (max-width: 900px) { .sidebar { display: none; } .content { padding: 1.5rem; } }
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
                GitLab
            </a>
            <nav class="nav-links">
                <a href="index.php">Lab Home</a>
                <a href="login.php">Login</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">Overview</a></li>
                <li class="section-title">Understanding</li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li class="section-title">Defense</li>
                <li><a href="docs-prevention.php" class="active">Prevention</a></li>
                <li><a href="docs-testing.php">Testing Techniques</a></li>
                <li class="section-title">Resources</li>
                <li><a href="docs-references.php">References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>Prevention</h1>
            <p>Learn how to fix IDOR vulnerabilities and implement proper authorization controls.</p>

            <h2>Primary Fix: Validate Resource Ownership</h2>
            <p>
                The most important fix is to validate that the requested resource actually belongs to 
                the authorized context (in this case, the project).
            </p>

            <div class="fix-card">
                <h3>✓ Secure Implementation</h3>
                <div class="code-block">
<span class="comment">// Add project ownership check to the query</span>
$stmt = $pdo->prepare("
    SELECT esc.*, p.name as project_name, p.visibility
    FROM external_status_checks esc
    JOIN projects p ON esc.project_id = p.id
    WHERE esc.id = ?
    <span class="secure">AND esc.project_id = ?</span>  <span class="comment">// Validate ownership!</span>
");
$stmt->execute([
    $external_status_check_id,
    <span class="secure">$project_id</span>  <span class="comment">// The authorized project</span>
]);

$statusCheck = $stmt->fetch(PDO::FETCH_ASSOC);

<span class="comment">// If no result, the status check doesn't belong to this project</span>
if (!$statusCheck) {
    http_response_code(404);
    echo json_encode([
        'error' => 'Not Found',
        'message' => 'Status check not found or does not belong to this project'
    ]);
    exit;
}
                </div>
            </div>

            <h2>Defense in Depth Strategies</h2>

            <h3>1. Implement Authorization Layer</h3>
            <p>Create a dedicated authorization function that checks all resource access:</p>
            <div class="code-block">
<span class="secure">function canAccessStatusCheck($user_id, $status_check_id, $project_id) {
    global $pdo;
    
    // Verify status check belongs to project
    $stmt = $pdo->prepare("
        SELECT 1 FROM external_status_checks 
        WHERE id = ? AND project_id = ?
    ");
    $stmt->execute([$status_check_id, $project_id]);
    
    if (!$stmt->fetch()) {
        return false; // Status check doesn't belong to project
    }
    
    // Verify user has access to project
    return userHasProjectAccess($user_id, $project_id);
}</span>
            </div>

            <h3>2. Use Indirect References</h3>
            <p>Instead of exposing database IDs, use indirect references that are mapped server-side:</p>
            <div class="code-block">
<span class="comment">// Instead of: external_status_check_id=1</span>
<span class="comment">// Use: external_status_check_ref=project-3-check-aws</span>

<span class="secure">function resolveStatusCheckRef($ref, $project_id) {
    // Parse reference and validate it matches the project context
    if (!preg_match('/^project-(\d+)-check-(.+)$/', $ref, $matches)) {
        return null;
    }
    
    $refProjectId = $matches[1];
    $checkName = $matches[2];
    
    // Ensure reference project matches authorized project
    if ($refProjectId != $project_id) {
        return null;
    }
    
    // Look up actual status check
    return getStatusCheckByName($project_id, $checkName);
}</span>
            </div>

            <h3>3. Implement Rate Limiting</h3>
            <p>Limit enumeration attempts by rate limiting API requests:</p>
            <div class="code-block">
<span class="secure">// Track API calls per user
$key = "api_calls:{$user_id}:" . date('Y-m-d-H');
$calls = $redis->incr($key);
$redis->expire($key, 3600);

if ($calls > 100) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded']);
    exit;
}</span>
            </div>

            <h3>4. Log Suspicious Activity</h3>
            <p>Monitor for IDOR exploitation attempts:</p>
            <div class="code-block">
<span class="secure">// Log cross-project access attempts
if ($statusCheck['project_id'] !== $project_id) {
    logSecurityEvent([
        'type' => 'IDOR_ATTEMPT',
        'user_id' => $user_id,
        'requested_project' => $project_id,
        'actual_project' => $statusCheck['project_id'],
        'resource_id' => $external_status_check_id,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'timestamp' => time()
    ]);
    
    // Alert security team if threshold exceeded
    if (getRecentAttempts($user_id) > 10) {
        alertSecurityTeam($user_id);
    }
}</span>
            </div>

            <h2>API Design Best Practices</h2>
            
            <div class="info-box info">
                <h4>🔐 Principle of Least Privilege</h4>
                <p>APIs should only return the minimum data necessary. Don't include sensitive fields like API keys in standard responses.</p>
            </div>

            <ul>
                <li><strong>Nest resources under parents:</strong> Use <code>/projects/{id}/status_checks/{id}</code> instead of <code>/status_checks/{id}</code></li>
                <li><strong>Validate all parameters:</strong> Never assume user input is valid or authorized</li>
                <li><strong>Use consistent authorization:</strong> Apply the same checks for all access methods (UI, API, exports)</li>
                <li><strong>Minimize data exposure:</strong> Only return fields the user needs to see</li>
                <li><strong>Implement proper error messages:</strong> Don't reveal whether a resource exists if unauthorized</li>
            </ul>

            <h2>Code Review Checklist</h2>
            <ol>
                <li>✓ Is resource ownership validated before access?</li>
                <li>✓ Are all user-supplied IDs verified against the user's permissions?</li>
                <li>✓ Is authorization checked at every access point?</li>
                <li>✓ Are database queries filtering by the authorized context?</li>
                <li>✓ Are error messages generic enough to prevent enumeration?</li>
                <li>✓ Is suspicious activity being logged?</li>
            </ol>

            <div class="info-box success">
                <h4>✓ Remember</h4>
                <p>
                    Always verify that the user is authorized to access the specific resource instance, 
                    not just the resource type. Authentication != Authorization!
                </p>
            </div>

            <div class="nav-buttons">
                <a href="docs-exploitation.php" class="nav-btn">← Exploitation Guide</a>
                <a href="docs-testing.php" class="nav-btn">Next: Testing Techniques →</a>
            </div>
        </main>
    </div>
</body>
</html>
