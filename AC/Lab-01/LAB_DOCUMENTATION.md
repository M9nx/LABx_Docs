# Lab 1: Unprotected Admin Functionality
## Access Control Vulnerabilities Lab

---

## 1. Lab Overview

### What is Broken Access Control?

Broken Access Control is a security vulnerability that occurs when applications fail to properly restrict what authenticated users are allowed to do. This vulnerability allows attackers to access unauthorized functionality or data by manipulating user IDs, modifying URLs, or bypassing access control checks.

**Key characteristics of Broken Access Control vulnerabilities:**
- Missing or inadequate authorization checks
- Direct access to restricted resources via URL manipulation
- Trust in client-provided data without server-side validation
- Improper implementation of role-based access controls

### Unprotected Admin Functionality

This specific type of access control vulnerability occurs when administrative functions are not properly protected by authentication or authorization mechanisms. Instead of implementing proper access controls, the application relies on "security through obscurity" - hoping that attackers won't discover the admin interface.

**Common causes:**
- Admin panels accessible via predictable URLs
- Missing authentication checks on sensitive pages
- Disclosure of admin paths in robots.txt or source code
- Assumption that "hidden" URLs provide adequate security

### Why This Happens

1. **Developer Assumptions:** Developers assume that if a URL isn't linked publicly, it won't be discovered
2. **Rushed Development:** Time pressure leads to shortcuts in security implementation
3. **Poor Security Awareness:** Lack of understanding about access control principles
4. **Information Disclosure:** Accidental revelation of admin paths through robots.txt, source code, or error messages
5. **Testing Shortcuts:** Development/testing interfaces left accessible in production

---

## 2. Step-by-Step Walkthrough

### Prerequisites
- XAMPP running with Apache and MySQL
- Lab deployed to `http://localhost/AC/lab1/`
- Web browser with developer tools

### Step 1: Initial Reconnaissance

1. **Access the main application:**
   ```
   http://localhost/AC/lab1/
   ```

2. **Explore the application:**
   - Browse through available pages (Home, Products, About, Contact)
   - Try logging in with provided credentials
   - Notice normal user functionality

### Step 2: Information Gathering

1. **Check robots.txt for hidden paths:**
   ```
   http://localhost/AC/lab1/robots.txt
   ```

2. **Observe the disclosed paths:**
   ```
   User-agent: *
   Disallow: /administrator-panel
   Disallow: /admin
   Disallow: /backup
   Disallow: /logs
   Disallow: /private
   ```

3. **Identify the admin panel path:** `/administrator-panel`

### Step 3: Accessing Unprotected Admin Functionality

1. **Access the admin panel directly:**
   ```
   http://localhost/AC/lab1/administrator-panel.php
   ```

2. **Observe complete access without authentication:**
   - No login prompt
   - No authorization check
   - Full administrative interface visible
   - User management functionality exposed

### Step 4: Exploiting the Vulnerability

1. **Locate the target user 'carlos' in the user list**
2. **Click the "Delete" button for carlos**
3. **Confirm the deletion when prompted**
4. **Verify successful deletion:**
   - carlos disappears from the user list
   - Success message confirms deletion
   - User is permanently removed from database

### Step 5: Verification

1. **Attempt to login as carlos:**
   ```
   Username: carlos
   Password: carlos123
   ```
   Result: "Invalid username or password" error

2. **Confirm administrative access persists:**
   - Admin panel remains accessible
   - No authentication required
   - Other users can still be managed

---

## 3. Why The Exploit Works

### Missing Authorization Checks

The administrator panel (`administrator-panel.php`) contains **zero authentication or authorization logic**. The vulnerable code structure:

```php
<?php
// INTENTIONALLY VULNERABLE: No authentication or authorization checks!
// This admin panel is completely unprotected

require_once 'config.php';

// Direct processing of admin functions without any security checks
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    // ... deletion logic executes without verification
}
```

### Dangerous Assumptions

1. **Security Through Obscurity:** The application assumes that hiding the admin URL provides security
2. **No Session Validation:** The code doesn't check if a user is logged in
3. **No Role Verification:** The code doesn't verify if the user has admin privileges
4. **Direct Database Access:** Admin functions execute immediately without permission checks

### Information Disclosure

The `robots.txt` file inadvertently discloses sensitive paths:
```
Disallow: /administrator-panel
```

This is a classic example of **information leakage** that aids attackers in discovering hidden functionality.

### Trust Boundary Violation

The application violates the fundamental security principle of "never trust user input." It directly processes administrative requests without:
- Verifying user identity
- Checking user permissions
- Validating request authenticity
- Implementing proper access controls

---

## 4. Wrong (Vulnerable) Code Explanation

Let's examine the critical security flaws in the vulnerable code:

### administrator-panel.php (Vulnerable Version)

```php
<?php
// INTENTIONALLY VULNERABLE: No authentication or authorization checks!
// This admin panel is completely unprotected

require_once 'config.php';

// Handle user deletion
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $pdo = getDBConnection();
    
    // Get user info before deletion for confirmation
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $successMessage = "User '" . htmlspecialchars($user['username']) . "' has been deleted successfully.";
    } else {
        $errorMessage = "User not found.";
    }
}

// Get all users - no authorization check
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll();
```

#### Critical Flaws:

**1. No Session Check:**
```php
// MISSING: Session validation
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }
```

**2. No Role Authorization:**
```php
// MISSING: Admin role verification
// if ($_SESSION['role'] !== 'admin') {
//     http_response_code(403);
//     die('Access denied');
// }
```

**3. Direct User Input Processing:**
```php
// DANGEROUS: Direct processing without authorization
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    // No check if current user can delete users!
```

**4. Unrestricted Data Access:**
```php
// DANGEROUS: Exposing all user data without permission check
$stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll();
```

### robots.txt Information Disclosure

```
User-agent: *
Disallow: /administrator-panel  ← INFORMATION LEAKAGE
Disallow: /admin
Disallow: /backup
Disallow: /logs
Disallow: /private
```

This robots.txt file inadvertently provides a roadmap to sensitive areas of the application.

---

## 5. Correct Mitigation (Secure Code)

Here's how to properly secure the admin functionality:

### administrator-panel.php (Secure Version)

```php
<?php
session_start();
require_once 'config.php';

// SECURITY FIX 1: Enforce authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=login_required');
    exit;
}

// SECURITY FIX 2: Enforce role-based authorization
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('<!DOCTYPE html><html><head><title>Access Denied</title></head><body><h1>403 - Access Denied</h1><p>You do not have permission to access this page.</p><a href="index.php">Return to Home</a></body></html>');
}

// SECURITY FIX 3: Implement CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
}

// Generate CSRF token for forms
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// SECURITY FIX 4: Add logging for admin actions
function logAdminAction($action, $details) {
    $logFile = 'admin_actions.log';
    $timestamp = date('Y-m-d H:i:s');
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $logEntry = "[$timestamp] User $username (ID: $userId) performed: $action - $details\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Handle user deletion with proper validation
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']); // SECURITY FIX: Input validation
    
    // SECURITY FIX 5: Prevent self-deletion
    if ($userId === $_SESSION['user_id']) {
        $errorMessage = "You cannot delete your own account.";
    } 
    // SECURITY FIX 6: Protect admin accounts
    else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $targetUser = $stmt->fetch();
        
        if (!$targetUser) {
            $errorMessage = "User not found.";
        } elseif ($targetUser['role'] === 'admin') {
            $errorMessage = "Cannot delete admin accounts.";
        } else {
            // Proceed with deletion
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() > 0) {
                $successMessage = "User '" . htmlspecialchars($targetUser['username']) . "' has been deleted successfully.";
                logAdminAction('DELETE_USER', "Deleted user: {$targetUser['username']} (ID: $userId)");
            } else {
                $errorMessage = "Failed to delete user.";
            }
        }
    }
}

// Get all users (still need to be admin to reach this point)
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Administrator Panel - SecureShop</title>
    <!-- ... styles ... -->
</head>
<body>
    <!-- ... header ... -->
    
    <div class="container">
        <div class="security-notice">
            <strong>🔒 SECURE ADMIN PANEL:</strong> 
            Access logged and monitored. Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)
            <a href="logout.php" style="float: right; color: #dc3545;">Logout</a>
        </div>
        
        <!-- ... success/error messages ... -->
        
        <h2>User Management</h2>
        <p>Secure user management with proper authorization and logging.</p>
        
        <table>
            <!-- ... table header ... -->
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <!-- ... user data ... -->
                    <td>
                        <?php if ($user['role'] !== 'admin' && $user['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete user <?php echo htmlspecialchars($user['username']); ?>?')">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                            </form>
                        <?php else: ?>
                            <span class="btn btn-secondary disabled">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 2rem;">
            <a href="index.php" class="btn btn-secondary">← Back to Main Site</a>
        </div>
    </div>
</body>
</html>
```

### Additional Security Measures

**1. Secure robots.txt:**
```
User-agent: *
Disallow: /private-content/
Disallow: /temp/
# Remove admin path disclosure
```

**2. Web Server Configuration (.htaccess):**
```apache
# Protect admin area
<Files "administrator-panel.php">
    # Additional IP-based restrictions (optional)
    # Require ip 192.168.1.0/24
</Files>

# Block direct access to sensitive files
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

**3. Database Schema Improvements:**
```sql
-- Add audit trail table
CREATE TABLE admin_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_user_id INT,
    details TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (admin_user_id) REFERENCES users(id)
);

-- Add session management
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 6. Comparison (Wrong Code vs Fixed Code)

### Authentication & Authorization

**Vulnerable Code:**
```php
// NO authentication check
// NO authorization check
require_once 'config.php';
// Direct access to admin functionality
```

**Secure Code:**
```php
session_start();
require_once 'config.php';

// Enforce authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=login_required');
    exit;
}

// Enforce role-based authorization
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Access Denied');
}
```

### Input Validation & CSRF Protection

**Vulnerable Code:**
```php
// Direct user input processing
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id']; // Raw input
    // No CSRF protection
```

**Secure Code:**
```php
// CSRF token validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
}

// Input validation
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']); // Validated input
```

### Business Logic Protection

**Vulnerable Code:**
```php
// No restrictions on user deletion
if ($user) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    // Anyone can be deleted
}
```

**Secure Code:**
```php
// Protect critical accounts
if ($userId === $_SESSION['user_id']) {
    $errorMessage = "You cannot delete your own account.";
} else {
    // Check target user role
    $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $targetUser = $stmt->fetch();
    
    if ($targetUser['role'] === 'admin') {
        $errorMessage = "Cannot delete admin accounts.";
    } else {
        // Safe deletion with logging
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$userId]);
        logAdminAction('DELETE_USER', "Deleted user: {$targetUser['username']}");
    }
}
```

### Information Disclosure

**Vulnerable robots.txt:**
```
User-agent: *
Disallow: /administrator-panel  ← REVEALS ADMIN PATH
```

**Secure robots.txt:**
```
User-agent: *
Disallow: /private-content/
Disallow: /temp/
# No sensitive path disclosure
```

### Why the Secure Code Prevents Exploitation

1. **Authentication Layer:** Users must log in before accessing any admin functionality
2. **Authorization Layer:** Only users with 'admin' role can access the panel
3. **CSRF Protection:** Prevents cross-site request forgery attacks
4. **Input Validation:** Prevents injection and ensures data integrity
5. **Business Logic Controls:** Protects critical accounts and maintains system integrity
6. **Audit Logging:** Provides accountability and monitoring capabilities
7. **Information Security:** Eliminates path disclosure vulnerabilities

### Impact of Security Fixes

- **Before:** Any visitor could access admin functions
- **After:** Only authenticated admin users can access admin functions
- **Before:** No protection against CSRF attacks
- **After:** CSRF tokens prevent unauthorized actions
- **Before:** Any user account could be deleted
- **After:** Admin accounts and self-deletion are protected
- **Before:** No audit trail of admin actions
- **After:** All admin actions are logged for monitoring
- **Before:** Admin paths disclosed in robots.txt
- **After:** No sensitive information disclosed

The secure implementation follows the principle of "defense in depth" with multiple layers of security controls, making it extremely difficult for attackers to bypass all protections and gain unauthorized access.

---

## Conclusion

This lab demonstrates how easily unprotected admin functionality can be exploited when proper access controls are not implemented. The vulnerability exists because developers relied on security through obscurity instead of implementing proper authentication and authorization mechanisms.

The key takeaway is that **all administrative functionality must be protected by robust authentication and authorization controls**, regardless of how "hidden" the access points might seem. Security should never rely on the assumption that attackers won't discover certain URLs or interfaces.