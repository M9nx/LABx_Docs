<?php
session_start();
require_once 'config.php';

// Check if vulnerability was exploited
$exploited = false;
$attackDetails = [];

if (isset($_SESSION['user_id'])) {
    // Check if attacker_member created any keys
    $stmt = $pdo->prepare("
        SELECT u.username, u.full_name
        FROM users u
        WHERE u.username = 'attacker_member'
    ");
    $stmt->execute();
    $attacker = $stmt->fetch();
    
    if ($attacker) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'attacker_member'");
        $stmt->execute();
        $attackerId = $stmt->fetchColumn();
        
        // Check keys created by attacker
        $stmt = $pdo->prepare("
            SELECT ak.*, o.name as org_name
            FROM api_keys ak
            JOIN organizations o ON ak.org_id = o.id
            WHERE ak.created_by = ?
        ");
        $stmt->execute([$attackerId]);
        $attackerKeys = $stmt->fetchAll();
        
        if (count($attackerKeys) > 0) {
            $exploited = true;
            $attackDetails['keys_created'] = count($attackerKeys);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Instructions - IDOR API Key Vulnerability</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .nav-links a {
            color: #5eead4;
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .main-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-title h1 {
            font-size: 2.5rem;
            color: #5eead4;
            margin-bottom: 0.5rem;
        }
        .page-title p { color: #94a3b8; }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #5eead4;
            margin-bottom: 1rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card h3 {
            color: #f8fafc;
            margin: 1.5rem 0 1rem;
            font-size: 1.1rem;
        }
        .step-list {
            list-style: none;
            counter-reset: step-counter;
        }
        .step-list li {
            counter-increment: step-counter;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
            position: relative;
            padding-left: 3.5rem;
        }
        .step-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.85rem;
            color: #000;
        }
        .step-list li strong { color: #5eead4; }
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #5eead4;
            font-size: 0.9rem;
        }
        .code-block {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            border-left: 3px solid #14b8a6;
        }
        .code-block .comment { color: #64748b; }
        .code-block .string { color: #a5f3fc; }
        .code-block .key { color: #fcd34d; }
        .objective-box {
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.2), rgba(13, 148, 136, 0.1));
            border: 1px solid rgba(20, 184, 166, 0.4);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .objective-box h4 {
            color: #5eead4;
            margin-bottom: 0.75rem;
        }
        .objective-box ul {
            margin-left: 1.5rem;
            color: #94a3b8;
        }
        .objective-box li { margin-bottom: 0.5rem; }
        .warning-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .warning-box h4 {
            color: #fca5a5;
            margin-bottom: 0.75rem;
        }
        .success-box {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .success-box h4 {
            color: #86efac;
            margin-bottom: 0.75rem;
        }
        .cred-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .cred-table th, .cred-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .cred-table th { color: #94a3b8; font-weight: 500; }
        .cred-table td { color: #e0e0e0; }
        .cred-table .attacker { background: rgba(239, 68, 68, 0.1); }
        .nav-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-btn.primary {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: white;
        }
        .nav-btn.secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .api-endpoint {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 0.75rem 0;
        }
        .api-endpoint .method {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        .method.get { background: #22c55e; color: #000; }
        .method.post { background: #3b82f6; color: #fff; }
        .method.delete { background: #ef4444; color: #fff; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="docs.php">Documentation</a>
                <a href="../index.php">All Labs</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="page-title">
            <h1>📖 Lab Instructions</h1>
            <p>IDOR Lead To VIEW & DELETE & Create API Keys</p>
        </div>

        <?php if ($exploited): ?>
            <div class="success-box">
                <h4>🎉 Congratulations! Vulnerability Exploited!</h4>
                <p>You successfully exploited the IDOR vulnerability and created <?php echo $attackDetails['keys_created']; ?> API key(s) as a member!</p>
                <p style="margin-top: 0.5rem;"><a href="success.php" style="color: #86efac;">→ View Success Page</a></p>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>🎯 Lab Objective</h2>
            <p>Exploit an IDOR vulnerability in the API key management system to perform unauthorized actions.</p>
            
            <div class="objective-box">
                <h4>Your Goals:</h4>
                <ul>
                    <li><strong>VIEW</strong> - Access sensitive API keys you shouldn't be able to see</li>
                    <li><strong>CREATE</strong> - Create new API keys without proper authorization</li>
                    <li><strong>DELETE</strong> - Delete existing API keys owned by others</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h2>📋 Scenario</h2>
            <p>You're a <strong>member</strong> of the "TechCorp Inc" organization on KeyVault, an API key management platform. As a member, you should only have <strong>read-only access</strong> to view (but not manage) API keys.</p>
            
            <p style="margin-top: 1rem;">However, you've discovered that the API endpoints don't properly verify your <strong>role permissions</strong> - they only check if you're a <strong>member</strong> of the organization!</p>

            <h3>Role Permissions (Expected)</h3>
            <table class="cred-table">
                <tr>
                    <th>Role</th>
                    <th>VIEW Keys</th>
                    <th>CREATE Keys</th>
                    <th>DELETE Keys</th>
                </tr>
                <tr>
                    <td><span style="color: #f59e0b;">👑 Owner</span></td>
                    <td>✅</td>
                    <td>✅</td>
                    <td>✅</td>
                </tr>
                <tr>
                    <td><span style="color: #8b5cf6;">🔧 Admin</span></td>
                    <td>✅</td>
                    <td>✅</td>
                    <td>✅</td>
                </tr>
                <tr class="attacker">
                    <td><span style="color: #64748b;">👤 Member</span></td>
                    <td>✅ (Limited)</td>
                    <td>❌</td>
                    <td>❌</td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>🔐 Test Credentials</h2>
            <table class="cred-table">
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Organization</th>
                </tr>
                <tr>
                    <td><code>victim_owner</code></td>
                    <td><code>victim123</code></td>
                    <td>👑 Owner</td>
                    <td>TechCorp Inc</td>
                </tr>
                <tr class="attacker">
                    <td><code>attacker_member</code></td>
                    <td><code>attacker123</code></td>
                    <td>👤 Member</td>
                    <td>TechCorp Inc</td>
                </tr>
                <tr>
                    <td><code>alice_admin</code></td>
                    <td><code>alice123</code></td>
                    <td>🔧 Admin</td>
                    <td>TechCorp Inc</td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>🚀 Exploitation Steps</h2>
            
            <ol class="step-list">
                <li>
                    <strong>Login as attacker</strong><br>
                    Use credentials: <code>attacker_member / attacker123</code>
                </li>
                <li>
                    <strong>Navigate to organization</strong><br>
                    Go to Dashboard → TechCorp Inc → API Keys tab
                </li>
                <li>
                    <strong>Note your role</strong><br>
                    You're a <code>member</code> - you shouldn't be able to create/delete keys
                </li>
                <li>
                    <strong>Test VIEW vulnerability</strong><br>
                    Notice you can see ALL API keys including sensitive production keys!
                </li>
                <li>
                    <strong>Test CREATE vulnerability</strong><br>
                    Click "Create New Key" - it should fail for members, but it doesn't!
                </li>
                <li>
                    <strong>Test DELETE vulnerability</strong><br>
                    Try deleting an existing API key - it works despite being a member!
                </li>
            </ol>
        </div>

        <div class="card">
            <h2>🔧 API Endpoints</h2>
            <p>The vulnerable endpoint is <code>api/keys.php</code>:</p>

            <div class="api-endpoint">
                <span class="method get">GET</span>
                <code>api/keys.php?org_uuid={uuid}</code>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">View all API keys - vulnerable to unauthorized access</p>
            </div>

            <div class="api-endpoint">
                <span class="method post">POST</span>
                <code>api/keys.php</code>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">Create new API key - missing role verification</p>
            </div>

            <div class="api-endpoint">
                <span class="method delete">DELETE</span>
                <code>api/keys.php</code>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">Delete API key - missing role verification</p>
            </div>

            <h3>Example: Create Key (via cURL)</h3>
            <div class="code-block">
<span class="comment"># As attacker_member, create a key (should fail, but doesn't!)</span>
curl -X POST http://localhost/AC/lab20/api/keys.php \
  -H <span class="string">"Content-Type: application/json"</span> \
  -H <span class="string">"Cookie: PHPSESSID=your_session_id"</span> \
  -d '{
    <span class="key">"org_uuid"</span>: <span class="string">"org-aaaaaaaa-1111-1111-1111-111111111111"</span>,
    <span class="key">"name"</span>: <span class="string">"Hacked Key"</span>,
    <span class="key">"scope"</span>: <span class="string">"admin"</span>
  }'
            </div>
        </div>

        <div class="card">
            <h2>🔍 Root Cause</h2>
            <p>The vulnerability exists because the API endpoint only checks <strong>membership</strong> but not <strong>role permissions</strong>:</p>

            <div class="code-block">
<span class="comment">// VULNERABLE CODE (api/keys.php)</span>
$stmt = $pdo->prepare(<span class="string">"SELECT role FROM org_members WHERE org_id = ? AND user_id = ?"</span>);
$stmt->execute([$org['id'], $_SESSION['user_id']]);
$membership = $stmt->fetch();

if (!$membership) {
    <span class="comment">// Only checks if user is a member</span>
    http_response_code(403);
    exit;
}

<span class="comment">// MISSING: Role permission check!</span>
<span class="comment">// Should be:</span>
<span class="comment">// if ($membership['role'] === 'member') {</span>
<span class="comment">//     http_response_code(403);</span>
<span class="comment">//     echo json_encode(['error' => 'Insufficient permissions']);</span>
<span class="comment">//     exit;</span>
<span class="comment">// }</span>
            </div>
        </div>

        <div class="warning-box">
            <h4>⚠️ Security Impact</h4>
            <p>This vulnerability allows attackers with minimal access (member role) to:</p>
            <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                <li>Exfiltrate sensitive API keys (production, payment gateways)</li>
                <li>Create unauthorized API keys for persistent access</li>
                <li>Delete critical API keys causing service disruption</li>
                <li>Escalate privileges by creating admin-scoped keys</li>
            </ul>
        </div>

        <div class="nav-buttons">
            <a href="index.php" class="nav-btn secondary">← Back to Home</a>
            <a href="login.php" class="nav-btn primary">Start Lab →</a>
            <a href="docs.php" class="nav-btn secondary">📚 Documentation</a>
        </div>
    </main>
</body>
</html>
