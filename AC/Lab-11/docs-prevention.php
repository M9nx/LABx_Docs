<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prevention - MethodLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 68, 68, 0.3); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff4444; }
        .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 68, 68, 0.3); color: #e0e0e0; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.3s; }
        .btn-back:hover { background: rgba(255, 68, 68, 0.2); border-color: #ff4444; color: #ff4444; }
        .docs-container { display: flex; max-width: 1400px; margin: 0 auto; padding: 2rem; gap: 2rem; }
        .sidebar { width: 280px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 15px; padding: 1.5rem; height: fit-content; position: sticky; top: 100px; }
        .sidebar h3 { color: #ff4444; margin-bottom: 1rem; font-size: 1.2rem; }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.5rem; }
        .sidebar-nav a { display: block; color: #ccc; text-decoration: none; padding: 0.7rem 1rem; border-radius: 8px; transition: all 0.3s; }
        .sidebar-nav a:hover { background: rgba(255, 68, 68, 0.2); color: #ff6666; padding-left: 1.5rem; }
        .sidebar-nav a.active { background: rgba(255, 68, 68, 0.3); color: #ff4444; font-weight: 600; }
        .content { flex: 1; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 15px; padding: 2.5rem; }
        .content h1 { color: #ff4444; font-size: 2.5rem; margin-bottom: 1rem; }
        .content h2 { color: #ff6666; font-size: 1.8rem; margin: 2rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(255, 68, 68, 0.3); }
        .content h3 { color: #ff8888; font-size: 1.3rem; margin: 1.5rem 0 1rem 0; }
        .content p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
        .content ul, .content ol { color: #ccc; line-height: 1.8; margin: 1rem 0 1rem 2rem; }
        .content li { margin-bottom: 0.5rem; }
        .code-block { background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 8px; padding: 1rem; margin: 1rem 0; overflow-x: auto; }
        .code-block code { color: #66ff66; font-family: 'Courier New', monospace; font-size: 0.9rem; }
        .success-box { background: rgba(0, 255, 0, 0.1); border-left: 4px solid #00ff00; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .success-box strong { color: #66ff66; display: block; margin-bottom: 0.5rem; }
        .warning-box { background: rgba(255, 150, 0, 0.1); border-left: 4px solid #ff9600; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .warning-box strong { color: #ffaa66; display: block; margin-bottom: 0.5rem; }
        .error-box { background: rgba(255, 0, 0, 0.1); border-left: 4px solid #ff0000; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .error-box strong { color: #ff6666; display: block; margin-bottom: 0.5rem; }
        table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        th, td { padding: 1rem; text-align: left; border: 1px solid rgba(255, 68, 68, 0.3); }
        th { background: rgba(255, 68, 68, 0.2); color: #ff6666; font-weight: 600; }
        td { background: rgba(0, 0, 0, 0.3); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">⚙️ MethodLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="docs-container">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">📖 Overview</a></li>
                <li><a href="docs-http-methods.php">🌐 HTTP Methods</a></li>
                <li><a href="docs-access-control.php">🔒 Access Control</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php" class="active">🛡️ Prevention</a></li>
                <li><a href="docs-references.php">📚 References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>🛡️ Prevention & Remediation</h1>

            <p>This guide provides comprehensive strategies to prevent method-based access control vulnerabilities and implement secure authorization mechanisms.</p>

            <h2>🎯 Core Prevention Principles</h2>

            <h3>1. Method-Agnostic Authorization</h3>
            <p>Always validate authorization <strong>before</strong> checking the HTTP method.</p>

            <div class="error-box">
                <strong>❌ VULNERABLE CODE (from this lab):</strong>
                <div class="code-block"><code>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    // Only checks for POST<br>    if ($_SESSION['role'] !== 'admin') {<br>        die('Access denied');<br>    }<br>    $username = $_POST['username'];<br>} else {<br>    // GET bypasses check!<br>    $username = $_GET['username'];<br>}<br>promote_user($username);</code></div>
            </div>

            <div class="success-box">
                <strong>✅ SECURE CODE:</strong>
                <div class="code-block"><code>// Check authorization FIRST, regardless of method<br>if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {<br>    http_response_code(403);<br>    die('Access denied');<br>}<br><br>// Then handle different methods<br>if ($_SERVER['REQUEST_METHOD'] === 'POST') {<br>    $username = $_POST['username'];<br>} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {<br>    $username = $_GET['username'];<br>} else {<br>    http_response_code(405);<br>    die('Method not allowed');<br>}<br><br>promote_user($username);</code></div>
            </div>

            <h3>2. Restrict HTTP Methods</h3>
            <p>State-changing operations should ONLY accept POST (or PUT/PATCH/DELETE for REST APIs).</p>

            <div class="success-box">
                <strong>✅ BETTER: Explicitly restrict methods</strong>
                <div class="code-block"><code>// Only allow POST for this privileged operation<br>if ($_SERVER['REQUEST_METHOD'] !== 'POST') {<br>    http_response_code(405);<br>    header('Allow: POST');<br>    die('This endpoint only accepts POST requests');<br>}<br><br>// Then check authorization<br>if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {<br>    http_response_code(403);<br>    die('Access denied');<br>}<br><br>$username = $_POST['username'];<br>promote_user($username);</code></div>
            </div>

            <h2>🏗️ Architectural Solutions</h2>

            <h3>1. Centralized Authorization Middleware</h3>
            <p>Create reusable authorization functions:</p>

            <div class="code-block"><code>// auth.php<br>function require_admin() {<br>    if (!isset($_SESSION['user_id'])) {<br>        http_response_code(401);<br>        die('Authentication required');<br>    }<br>    if ($_SESSION['role'] !== 'admin') {<br>        http_response_code(403);<br>        die('Admin privileges required');<br>    }<br>}<br><br>function require_auth() {<br>    if (!isset($_SESSION['user_id'])) {<br>        http_response_code(401);<br>        die('Authentication required');<br>    }<br>}<br><br>function require_post() {<br>    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {<br>        http_response_code(405);<br>        header('Allow: POST');<br>        die('POST method required');<br>    }<br>}</code></div>

            <p>Use in your endpoints:</p>
            <div class="code-block"><code>// admin-upgrade.php<br>require_once 'auth.php';<br><br>require_post();      // Enforce POST only<br>require_admin();     // Enforce admin role<br><br>$username = $_POST['username'];<br>promote_user($username);</code></div>

            <h3>2. Framework-based Solutions</h3>

            <h4>Laravel (PHP):</h4>
            <div class="code-block"><code>// routes/web.php<br>Route::post('/admin/upgrade', [AdminController::class, 'upgrade'])<br>    ->middleware(['auth', 'admin']);<br><br>// app/Http/Middleware/AdminMiddleware.php<br>public function handle($request, Closure $next) {<br>    if ($request->user()->role !== 'admin') {<br>        abort(403, 'Admin access required');<br>    }<br>    return $next($request);<br>}</code></div>

            <h4>Express.js (Node.js):</h4>
            <div class="code-block"><code>// middleware/auth.js<br>function requireAdmin(req, res, next) {<br>  if (!req.session.user || req.session.user.role !== 'admin') {<br>    return res.status(403).json({ error: 'Admin required' });<br>  }<br>  next();<br>}<br><br>// routes.js<br>app.post('/admin/upgrade', requireAdmin, (req, res) => {<br>  // Only executed if user is admin<br>  promoteUser(req.body.username);<br>});</code></div>

            <h4>Django (Python):</h4>
            <div class="code-block"><code>from django.contrib.auth.decorators import user_passes_test<br>from django.views.decorators.http import require_POST<br><br>def is_admin(user):<br>    return user.is_authenticated and user.role == 'admin'<br><br>@require_POST  # Only POST allowed<br>@user_passes_test(is_admin)  # Only admin allowed<br>def upgrade_user(request):<br>    username = request.POST.get('username')<br>    promote_user(username)</code></div>

            <h3>3. Web Server Configuration</h3>

            <h4>Apache (.htaccess):</h4>
            <div class="code-block"><code>&lt;Files "admin-upgrade.php"&gt;<br>    &lt;Limit GET HEAD&gt;<br>        Require all denied<br>    &lt;/Limit&gt;<br>&lt;/Files&gt;</code></div>

            <h4>Nginx:</h4>
            <div class="code-block"><code>location /admin/upgrade {<br>    limit_except POST {<br>        deny all;<br>    }<br>}</code></div>

            <h2>🔐 Complete Secure Implementation</h2>

            <p>Here's a complete, secure version of the vulnerable endpoint:</p>

            <div class="code-block"><code>&lt;?php<br>session_start();<br>require_once 'config.php';<br><br>// 1. Enforce POST method only<br>if ($_SERVER['REQUEST_METHOD'] !== 'POST') {<br>    http_response_code(405);<br>    header('Allow: POST');<br>    header('Content-Type: application/json');<br>    die(json_encode(['error' => 'Method not allowed']));<br>}<br><br>// 2. Check authentication<br>if (!isset($_SESSION['user_id'])) {<br>    http_response_code(401);<br>    header('Content-Type: application/json');<br>    die(json_encode(['error' => 'Authentication required']));<br>}<br><br>// 3. Check authorization<br>if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {<br>    http_response_code(403);<br>    header('Content-Type: application/json');<br>    die(json_encode(['error' => 'Admin privileges required']));<br>}<br><br>// 4. Validate CSRF token<br>if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {<br>    http_response_code(403);<br>    die(json_encode(['error' => 'Invalid CSRF token']));<br>}<br><br>// 5. Validate input<br>if (!isset($_POST['username']) || empty(trim($_POST['username']))) {<br>    http_response_code(400);<br>    die(json_encode(['error' => 'Username required']));<br>}<br><br>$username = trim($_POST['username']);<br><br>// 6. Prevent self-modification if needed<br>if ($username === $_SESSION['username']) {<br>    http_response_code(400);<br>    die(json_encode(['error' => 'Cannot modify your own role']));<br>}<br><br>// 7. Check if user exists<br>$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");<br>$stmt->bind_param("s", $username);<br>$stmt->execute();<br>if ($stmt->get_result()->num_rows === 0) {<br>    http_response_code(404);<br>    die(json_encode(['error' => 'User not found']));<br>}<br><br>// 8. Perform the operation<br>$stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE username = ?");<br>$stmt->bind_param("s", $username);<br><br>if ($stmt->execute()) {<br>    // 9. Log the action<br>    error_log("Admin {$_SESSION['username']} promoted user $username");<br>    <br>    http_response_code(200);<br>    echo json_encode(['success' => true, 'message' => "User $username promoted"]);<br>} else {<br>    http_response_code(500);<br>    echo json_encode(['error' => 'Database error']);<br>}<br>?&gt;</code></div>

            <h2>🛡️ Defense in Depth</h2>

            <h3>Layer 1: Web Server</h3>
            <ul>
                <li>Configure method restrictions</li>
                <li>Rate limiting</li>
                <li>IP whitelisting for admin endpoints</li>
            </ul>

            <h3>Layer 2: Application</h3>
            <ul>
                <li>Centralized authorization checks</li>
                <li>CSRF protection</li>
                <li>Input validation</li>
                <li>Session management</li>
            </ul>

            <h3>Layer 3: Database</h3>
            <ul>
                <li>Prepared statements (prevent SQL injection)</li>
                <li>Row-level security if available</li>
                <li>Audit logging</li>
            </ul>

            <h2>✅ Security Checklist</h2>

            <table>
                <thead>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Authorization checked before method handling</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>State-changing operations restricted to POST/PUT/DELETE</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>GET requests never modify server state</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>CSRF protection implemented</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>Input validation on all parameters</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>Proper HTTP status codes (401, 403, 405)</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>Centralized authorization functions</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>Audit logging for privileged operations</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>Rate limiting on sensitive endpoints</td>
                        <td>☐</td>
                    </tr>
                    <tr>
                        <td>Regular security testing</td>
                        <td>☐</td>
                    </tr>
                </tbody>
            </table>

            <h2>🧪 Testing Your Fixes</h2>

            <h3>Manual Testing:</h3>
            <ol>
                <li>Try accessing admin endpoints with GET instead of POST</li>
                <li>Try accessing as non-admin user</li>
                <li>Try accessing without authentication</li>
                <li>Try with tampered parameters</li>
                <li>Try with missing CSRF tokens</li>
            </ol>

            <h3>Automated Testing:</h3>
            <div class="code-block"><code># Test with curl<br>curl -X GET "http://localhost/admin/upgrade?username=test"<br># Expected: 405 Method Not Allowed<br><br>curl -X POST "http://localhost/admin/upgrade" -d "username=test"<br># Expected: 401 Unauthorized (if not logged in)<br><br>curl -X POST "http://localhost/admin/upgrade" \<br>  -b "PHPSESSID=user_session" -d "username=test"<br># Expected: 403 Forbidden (if not admin)</code></div>

            <h2>📊 Monitoring & Logging</h2>

            <p>Implement comprehensive logging for security events:</p>
            <div class="code-block"><code>// Log all authorization failures<br>function log_security_event($event, $details) {<br>    $log_entry = sprintf(<br>        "[%s] %s | User: %s | IP: %s | Details: %s\n",<br>        date('Y-m-d H:i:s'),<br>        $event,<br>        $_SESSION['username'] ?? 'anonymous',<br>        $_SERVER['REMOTE_ADDR'],<br>        json_encode($details)<br>    );<br>    error_log($log_entry, 3, '/var/log/security.log');<br>}<br><br>// Usage<br>if ($_SESSION['role'] !== 'admin') {<br>    log_security_event('AUTHORIZATION_FAILURE', [<br>        'endpoint' => $_SERVER['REQUEST_URI'],<br>        'method' => $_SERVER['REQUEST_METHOD'],<br>        'required_role' => 'admin',<br>        'user_role' => $_SESSION['role'] ?? 'none'<br>    ]);<br>    die('Access denied');<br>}</code></div>

            <h2>🎓 Key Takeaways</h2>
            <ul>
                <li>Always check authorization before method handling</li>
                <li>Restrict state-changing operations to POST/PUT/DELETE only</li>
                <li>Use centralized authorization middleware</li>
                <li>Implement defense in depth</li>
                <li>Log security events for monitoring</li>
                <li>Test all HTTP methods during security reviews</li>
                <li>Never trust client-controlled data (including HTTP method)</li>
            </ul>

            <div class="success-box">
                <strong>✅ Remember</strong>
                <p>Security is not a one-time fix. Regularly review and test your access controls, especially when adding new features or endpoints.</p>
            </div>
        </main>
    </div>
</body>
</html>
