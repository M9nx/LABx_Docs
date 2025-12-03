<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 1 Documentation - Unprotected Admin Functionality</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #e0e0e0;
            line-height: 1.7;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 60px;
            padding: 40px 0;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            color: #ff4444;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 8px rgba(255, 68, 68, 0.3);
        }

        .header .subtitle {
            color: #999;
            font-size: 1.3rem;
            font-weight: 300;
            font-style: italic;
        }

        /* Blog-style content */
        .blog-content {
            max-width: 100%;
        }

        .blog-content h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin: 50px 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 3px solid #ff4444;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .blog-content h2 {
            color: #ff5555;
            font-size: 2rem;
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #444;
        }

        .blog-content h3 {
            color: #ff6666;
            font-size: 1.4rem;
            margin: 30px 0 15px 0;
            font-weight: 600;
        }

        .blog-content h4 {
            color: #ff8888;
            font-size: 1.2rem;
            margin: 25px 0 10px 0;
            font-weight: 500;
        }

        .blog-content p {
            margin: 15px 0;
            text-align: justify;
            color: #ccc;
        }

        .blog-content ul, .blog-content ol {
            margin: 15px 0;
            padding-left: 25px;
        }

        .blog-content li {
            margin: 8px 0;
            color: #ccc;
        }

        .blog-content strong {
            color: #fff;
            font-weight: 600;
        }

        /* Code blocks */
        .code-block {
            background: #111;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #f0f0f0;
            overflow-x: auto;
            white-space: pre-wrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .inline-code {
            background: #222;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            color: #ff6666;
            border: 1px solid #444;
        }

        /* Alert boxes */
        .alert {
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            border-left: 5px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .alert-danger {
            background: rgba(255, 68, 68, 0.1);
            border-left-color: #ff4444;
            color: #ffcccc;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border-left-color: #ffc107;
            color: #fff3cd;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .alert-info {
            background: rgba(0, 123, 255, 0.1);
            border-left-color: #007bff;
            color: #cce7ff;
            border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: #28a745;
            color: #d4edda;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        /* Navigation */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin: 50px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background: #111;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background: #222;
            color: #ff4444;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }

        /* Blockquotes */
        blockquote {
            border-left: 4px solid #ff4444;
            padding: 20px 25px;
            margin: 25px 0;
            background: rgba(255, 68, 68, 0.05);
            font-style: italic;
            color: #ddd;
            border-radius: 0 8px 8px 0;
        }

        /* Section dividers */
        hr {
            border: none;
            border-top: 2px solid #333;
            margin: 40px 0;
        }

        /* Steps styling */
        .step {
            background: rgba(255, 68, 68, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .step-number {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Lab 1 Documentation</h1>
            <p class="subtitle">Unprotected Admin Functionality Vulnerability Analysis</p>
        </div>

        <div class="blog-content">

            <h1>Introduction to Unprotected Admin Functionality</h1>

            <p>Welcome to this comprehensive guide on one of the most critical security vulnerabilities in web applications: <strong>Unprotected Admin Functionality</strong>.</p>

            <p>This vulnerability represents a fundamental failure in access control that can lead to complete system compromise.</p>

            <p>Unlike other vulnerabilities that require complex exploitation techniques, unprotected admin functionality provides immediate and total access to administrative features.</p>

<div class="alert alert-danger">
<strong>⚠️ Critical Vulnerability Alert</strong><br>
This vulnerability allows complete system takeover with minimal technical skill required from an attacker.
</div>

            <h2>What is Unprotected Admin Functionality?</h2>

            <p>Unprotected admin functionality occurs when administrative interfaces are accessible to anyone who discovers their location. This happens due to:</p>

- **Missing Authentication**: No login requirements for admin pages
- **Weak Authorization**: Improper role-based access controls  
- **Security by Obscurity**: Relying only on hidden URLs
- **Poor Access Control Design**: Fundamental security architecture flaws

            <h3>Real-World Impact</h3>

            <p>This vulnerability has devastating consequences in production environments:</p>

1. **Data Breaches**: Unauthorized access to sensitive customer information

2. **System Manipulation**: Complete control over application settings and data

3. **Service Disruption**: Ability to delete critical data or configurations

4. **Privilege Escalation**: Creating new admin accounts or elevating permissions

5. **Compliance Violations**: Breach of GDPR, HIPAA, PCI-DSS regulations

            <hr>

            <h1>Step-by-Step Exploitation Walkthrough</h1>

            <h2>Prerequisites</h2>

            <p>Before starting this lab, ensure you have:</p>

- XAMPP running with Apache and MySQL services
- Lab deployed to <span class="inline-code">http://localhost/AC/lab1/</span>
- Web browser (Chrome, Firefox, or Edge recommended)
- Basic understanding of web applications

            <h2>Discovery Phase</h2>

<div class="step">
<span class="step-number">1</span>
<strong>Initial Application Analysis</strong>

Navigate to the lab URL and examine the application:

<div class="code-block">http://localhost/AC/lab1/</div>

You'll see a simple employee management interface.

Take note of:
- Available functionality for regular users
- Navigation patterns and URL structure  
- Any hints about administrative features
</div>

<div class="step">
<span class="step-number">2</span>
<strong>Directory Enumeration</strong>

The most common approach is to guess admin panel locations.

Try these URLs:

<div class="code-block">http://localhost/AC/lab1/admin
http://localhost/AC/lab1/admin/
http://localhost/AC/lab1/admin.php
http://localhost/AC/lab1/admin-panel.php
http://localhost/AC/lab1/administrator
http://localhost/AC/lab1/management</div>
</div>

<div class="alert alert-info">
<strong>💡 Pro Tip</strong><br>
In real-world scenarios, attackers use automated tools like dirb, dirbuster, or gobuster to enumerate thousands of potential admin paths.
</div>

<div class="step">
<span class="step-number">3</span>
<strong>Success - Finding the Admin Panel</strong>

When you access <span class="inline-code">http://localhost/AC/lab1/admin-panel.php</span>, you'll immediately gain access to the administrative interface without any authentication!

This demonstrates the complete absence of access controls.
</div>

            <h2>Exploitation Phase</h2>

<div class="step">
<span class="step-number">4</span>
<strong>Exploring Admin Capabilities</strong>

Once inside the admin panel, you can:

1. **View all user data** including sensitive information
2. **Delete user accounts** causing service disruption
3. **Access system configurations** potentially revealing other vulnerabilities
4. **Modify application settings** affecting all users
</div>

<div class="step">
<span class="step-number">5</span>
<strong>Demonstrating Impact</strong>

To complete the lab, delete any user account using the admin interface. 

This action demonstrates:
- Complete bypass of access controls
- Ability to cause data loss and service disruption  
- Potential for massive operational impact
</div>

            <hr>

            <h1>Vulnerable Code Analysis</h1>

            <p>Let's examine what makes this application vulnerable:</p>

            <p>The following code analysis reveals multiple critical security flaws that make this application completely vulnerable to unauthorized access.</p>

            <h2>admin-panel.php - The Vulnerable File</h2>

<div class="code-block">&lt;?php
// VULNERABILITY: No authentication or authorization checks!

require_once 'config.php';

// Direct database access without any security validation
$pdo = new PDO("mysql:host=localhost;dbname=lab1_db", $username, $password);

// Processing admin actions without permission checks
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id']; // No input validation!
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    echo "User deleted successfully!";
}

// Displaying all user data without access control
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?&gt;

&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Admin Panel&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;User Management&lt;/h1&gt;
    
    &lt;!-- No session validation or role checking --&gt;
    &lt;table&gt;
        &lt;tr&gt;
            &lt;th&gt;ID&lt;/th&gt;
            &lt;th&gt;Username&lt;/th&gt;
            &lt;th&gt;Email&lt;/th&gt;
            &lt;th&gt;Actions&lt;/th&gt;
        &lt;/tr&gt;
        &lt;?php foreach ($users as $user): ?&gt;
        &lt;tr&gt;
            &lt;td&gt;&lt;?= $user['id'] ?&gt;&lt;/td&gt;
            &lt;td&gt;&lt;?= $user['username'] ?&gt;&lt;/td&gt;
            &lt;td&gt;&lt;?= $user['email'] ?&gt;&lt;/td&gt;
            &lt;td&gt;
                &lt;!-- Dangerous delete functionality without protection --&gt;
                &lt;form method="post" style="display: inline;"&gt;
                    &lt;input type="hidden" name="delete_user" value="1"&gt;
                    &lt;input type="hidden" name="user_id" value="&lt;?= $user['id'] ?&gt;"&gt;
                    &lt;button type="submit" onclick="return confirm('Delete user?')"&gt;
                        🗑️ Delete
                    &lt;/button&gt;
                &lt;/form&gt;
            &lt;/td&gt;
        &lt;/tr&gt;
        &lt;?php endforeach; ?&gt;
    &lt;/table&gt;
&lt;/body&gt;
&lt;/html&gt;</div>

            <h2>Critical Security Flaws Identified</h2>

<div class="alert alert-danger">
<strong>🚨 Critical Security Flaws</strong>

1. **No Session Management**: Missing `session_start()` and session validation

2. **No Authentication Check**: No verification if user is logged in

3. **No Authorization Control**: No role-based access verification

4. **Direct Database Access**: No abstraction or security layer

5. **Input Validation Missing**: Direct use of `$_POST` data without sanitization

6. **No CSRF Protection**: Forms vulnerable to cross-site request forgery

7. **Information Disclosure**: All user data exposed without filtering
</div>

---

# Comprehensive Security Implementation

Here's how to properly secure this application:

## Secure admin-panel.php Implementation

<div class="code-block">&lt;?php
// SECURITY FIX #1: Proper session management
session_start();

// SECURITY FIX #2: Security configuration
ini_set('session.cookie_secure', '1');     // HTTPS only
ini_set('session.cookie_httponly', '1');   // No JS access
ini_set('session.use_strict_mode', '1');   // Prevent session fixation

require_once 'config.php';
require_once 'security_functions.php';

// SECURITY FIX #3: Authentication validation
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Log unauthorized access attempt
    error_log("Unauthorized admin panel access attempt from IP: " . 
             $_SERVER['REMOTE_ADDR']);
    header('Location: login.php?error=authentication_required');
    exit;
}

// SECURITY FIX #4: Role-based authorization
if ($_SESSION['role'] !== 'admin') {
    // Log authorization violation
    error_log("Non-admin user '{$_SESSION['username']}' attempted admin panel access");
    http_response_code(403);
    include 'error_pages/403_forbidden.php';
    exit;
}

// SECURITY FIX #5: Session validation
if (!validateSessionIntegrity($_SESSION['user_id'], $_SESSION['session_token'])) {
    session_destroy();
    header('Location: login.php?error=session_invalid');
    exit;
}

// SECURITY FIX #6: CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        error_log("CSRF attack detected from user '{$_SESSION['username']}'");
        http_response_code(403);
        die('CSRF token validation failed');
    }
}

$pdo = getDBConnection();
$successMessage = '';
$errorMessage = '';

// SECURITY FIX #7: Secure user deletion with comprehensive validation
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    try {
        // Input validation
        $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
        if (!$userId || $userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID provided');
        }
        
        // Prevent self-deletion
        if ($userId === $_SESSION['user_id']) {
            throw new Exception('Cannot delete your own account');
        }
        
        // Get target user information
        $stmt = $pdo->prepare(
            "SELECT username, role, full_name FROM users WHERE id = ?"
        );
        $stmt->execute([$userId]);
        $targetUser = $stmt->fetch();
        
        if (!$targetUser) {
            throw new Exception('User not found');
        }
        
        // Prevent deletion of other admin accounts
        if ($targetUser['role'] === 'admin') {
            throw new Exception('Cannot delete administrative accounts');
        }
        
        // Begin transaction for data integrity
        $pdo->beginTransaction();
        
        try {
            // Archive user data before deletion (compliance requirement)
            $archiveStmt = $pdo->prepare("
                INSERT INTO deleted_users_archive 
                (original_id, username, full_name, role, deleted_by, deleted_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $archiveStmt->execute([
                $userId, 
                $targetUser['username'], 
                $targetUser['full_name'], 
                $targetUser['role'], 
                $_SESSION['user_id']
            ]);
            
            // Delete user
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->execute([$userId]);
            
            // Commit transaction
            $pdo->commit();
            
            // Audit logging
            logSecurityEvent('USER_DELETE', [
                'admin_user' => $_SESSION['username'],
                'target_user' => $targetUser['username'],
                'target_id' => $userId,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $successMessage = "User '{$targetUser['username']}' has been successfully deleted.";
            
        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        $errorMessage = 'Delete operation failed: ' . 
                       htmlspecialchars($e->getMessage());
        error_log("User deletion failed: " . $e->getMessage());
    }
}

// SECURITY FIX #8: Data minimization - only retrieve necessary fields
$allowedFields = [
    'id', 
    'username', 
    'email', 
    'full_name', 
    'role', 
    'department', 
    'position', 
    'created_at'
];

// Sensitive fields only for super admins
if (isSuperAdmin($_SESSION['user_id'])) {
    $allowedFields[] = 'last_login';
    // Note: Salary and clearance require separate permission level
}

$fieldsQuery = implode(', ', $allowedFields);
$stmt = $pdo->prepare("SELECT {$fieldsQuery} FROM users WHERE id != ? ORDER BY id");
$stmt->execute([$_SESSION['user_id']]); // Exclude current user from list
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate CSRF token for forms
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?&gt;</div>

---

# Advanced Security Measures

## CSRF Protection Implementation

<div class="code-block">&lt;!-- SECURITY FIX #9: CSRF-protected forms --&gt;
&lt;form method="post" 
      style="display: inline;" 
      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"&gt;
    &lt;input type="hidden" name="delete_user" value="1"&gt;
    &lt;input type="hidden" name="user_id" 
           value="&lt;?= htmlspecialchars($user['id']) ?&gt;"&gt;
    &lt;input type="hidden" name="csrf_token" 
           value="&lt;?= htmlspecialchars($_SESSION['csrf_token']) ?&gt;"&gt;
    &lt;button type="submit" class="delete-btn"&gt;
        🗑️ Delete
    &lt;/button&gt;
&lt;/form&gt;</div>

## Data Protection and Masking

<div class="code-block">&lt;!-- SECURITY FIX #10: Data masking and conditional display --&gt;
&lt;td&gt;
    &lt;?php if (isSuperAdmin($_SESSION['user_id'])): ?&gt;
        &lt;?= htmlspecialchars($user['email']) ?&gt;
    &lt;?php else: ?&gt;
        &lt;span class="masked-data"&gt;***@***.***&lt;/span&gt;
    &lt;?php endif; ?&gt;
&lt;/td&gt;</div>

---

# Security Best Practices Summary

## Essential Access Control Principles

<div class="alert alert-success">
<strong>✅ Security Implementation Checklist</strong>

- **Authentication First**: Always verify user identity before granting access

- **Authorization Second**: Check if authenticated user has required permissions

- **Principle of Least Privilege**: Grant minimum necessary permissions

- **Defense in Depth**: Implement multiple layers of security controls

- **Fail Securely**: Default to deny access when errors occur
</div>

## Implementation Status

| Security Control | Status | Implementation |
|------------------|--------|----------------|
| Session Management | ✅ Required | `session_start()` with secure configuration |
| Authentication | ✅ Required | User login validation before access |
| Authorization | ✅ Required | Role-based access control (RBAC) |
| Input Validation | ✅ Required | Sanitize all user inputs |
| CSRF Protection | ✅ Required | Token-based form validation |
| SQL Injection Prevention | ✅ Required | Prepared statements only |
| Error Handling | ✅ Required | Secure error messages and logging |
| Audit Logging | ✅ Recommended | Track all administrative actions |

---

# Conclusion

Unprotected admin functionality represents one of the most serious security vulnerabilities in web applications.

As demonstrated in this lab, the complete absence of access controls can lead to immediate and total system compromise.

This vulnerability showcases how a single security oversight can provide attackers with complete administrative privileges.

## Key Takeaways

1. **Never rely on URL obscurity** for security

2. **Implement proper authentication** before any admin functionality

3. **Use role-based authorization** to control feature access

4. **Apply defense in depth** with multiple security layers

5. **Monitor and log** all administrative activities

<blockquote>
"Security is not a product, but a process. It's more than once; it's a process of continuous vigilance, assessment, and improvement."
<br><em>- Bruce Schneier, Security Technologist</em>
</blockquote>

        </div>

        <div class="nav-buttons">
            <a href="lab-description.php" class="btn btn-secondary">← Lab Description</a>
            <a href="success.php" class="btn btn-secondary">Success Page →</a>
            <a href="../index.php" class="btn btn-primary">← Back to Labs</a>
        </div>
    </div>
</body>
</html>