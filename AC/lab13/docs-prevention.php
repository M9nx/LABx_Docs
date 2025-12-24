<div class="doc-header">
    <h1>Prevention</h1>
    <p>How to properly implement access control</p>
</div>

<div class="content-section">
    <h2>The Golden Rule</h2>
    
    <div class="info-box danger">
        <h4>🔒 Never Trust Client-Controlled Data for Authorization</h4>
        <p>
            HTTP headers, cookies (their values, not server-side session data), URL parameters, 
            and request body data are all client-controlled. Authorization decisions must be 
            based on server-side session data that the client cannot manipulate.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>Secure Implementation</h2>
    
    <h3>Step 1: Store Role in Server-Side Session</h3>
    <p>
        When a user logs in, store their role in the server-side session:
    </p>
    
    <div class="code-block">
        <span class="code-label">Secure Login PHP</span>
        <code>&lt;?php
session_start();

// Verify credentials
$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $hashedPassword);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    // Store user data in server-side session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];  // Role stored server-side!
}
?&gt;</code>
    </div>
    
    <h3>Step 2: Verify Role on Every Protected Action</h3>
    <p>
        Check the server-side session role, not any client-controlled data:
    </p>
    
    <div class="code-block">
        <span class="code-label">Secure Access Control</span>
        <code>&lt;?php
session_start();

// SECURE: Check server-side session data
if (!isset($_SESSION['user_id'])) {
    die('Authentication required');
}

// SECURE: Verify role from session, not from headers!
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Admin privileges required');
}

// Now safe to perform admin action
$newRole = ($_GET['action'] === 'upgrade') ? 'admin' : 'user';
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
$stmt->bind_param("ss", $newRole, $_GET['username']);
$stmt->execute();
?&gt;</code>
    </div>
</div>

<div class="content-section">
    <h2>Defense in Depth</h2>
    
    <h3>1. Centralized Access Control</h3>
    <p>
        Implement access control checks in a single, reusable function or middleware:
    </p>
    
    <div class="code-block">
        <span class="code-label">Access Control Helper</span>
        <code>&lt;?php
function requireAdmin() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
    
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        include '403.php';
        exit;
    }
}

// Usage in protected pages:
requireAdmin();
// ... admin-only code here
?&gt;</code>
    </div>
    
    <h3>2. Re-validate Role from Database</h3>
    <p>
        For critical actions, re-fetch the user's role from the database in case it was 
        changed since the session was created:
    </p>
    
    <div class="code-block">
        <span class="code-label">Role Re-validation</span>
        <code>&lt;?php
function verifyAdminFromDB($conn, $userId) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result && $result['role'] === 'admin';
}

// Before sensitive actions:
if (!verifyAdminFromDB($conn, $_SESSION['user_id'])) {
    die('Admin access denied');
}
?&gt;</code>
    </div>
    
    <h3>3. Audit Logging</h3>
    <p>
        Log all admin actions for forensic analysis:
    </p>
    
    <div class="code-block">
        <span class="code-label">Audit Logging</span>
        <code>&lt;?php
function logAdminAction($conn, $userId, $action, $target) {
    $stmt = $conn->prepare("
        INSERT INTO admin_audit_log 
        (user_id, action, target, ip_address, timestamp) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("isss", $userId, $action, $target, $_SERVER['REMOTE_ADDR']);
    $stmt->execute();
}

// Log every admin action
logAdminAction($conn, $_SESSION['user_id'], 'role_upgrade', $targetUsername);
?&gt;</code>
    </div>
</div>

<div class="content-section">
    <h2>Anti-CSRF Tokens</h2>
    <p>
        While not directly related to Referer-based access control, implementing CSRF tokens 
        adds another layer of protection:
    </p>
    
    <div class="code-block">
        <span class="code-label">CSRF Protection</span>
        <code>&lt;?php
// Generate token on form page
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// In the form
echo '&lt;input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '"&gt;';

// Verify token on submission
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token validation failed');
}
?&gt;</code>
    </div>
    
    <div class="info-box warning">
        <h4>⚠️ CSRF Tokens Are Not Sufficient</h4>
        <p>
            CSRF tokens protect against cross-site request forgery but do not replace proper 
            access control. You must still verify the user's role on the server side. An 
            authenticated attacker with a valid CSRF token could still exploit Referer-based 
            access control.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>When Referer Can Be Used (Safely)</h2>
    <p>
        The Referer header can be used for non-security purposes:
    </p>
    <ul>
        <li><strong>Analytics:</strong> Tracking where users came from</li>
        <li><strong>Debugging:</strong> Understanding navigation patterns</li>
        <li><strong>Cache Optimization:</strong> Varying cached responses based on origin</li>
        <li><strong>Deep Linking:</strong> Returning users to appropriate pages</li>
    </ul>
    
    <div class="info-box success">
        <h4>✅ Safe Usage Pattern</h4>
        <p>
            Referer can be used as a <em>signal</em> for user experience improvements or 
            analytics, but must never be the <em>sole basis</em> for any security decision.
        </p>
    </div>
</div>
