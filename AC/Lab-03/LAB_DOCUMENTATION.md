# Lab 3: User Role Controlled by Request Parameter
## Access Control Vulnerabilities Lab

---

## 1. Lab Overview

### What is Client-Side Role Manipulation?

Client-side role manipulation is a critical access control vulnerability that occurs when web applications trust user-controllable data (such as cookies, hidden form fields, or URL parameters) to make authorization decisions. Instead of verifying user permissions on the server side, the application blindly trusts whatever the client sends.

**Key characteristics of this vulnerability:**
- User roles or permissions stored in client-accessible locations
- No server-side verification of authorization claims
- Trust in cookies or form data for access control decisions
- Easy privilege escalation for any authenticated user

### Why This Vulnerability Exists

1. **Developer Convenience:** Storing role information client-side seems simpler than database lookups
2. **Misunderstanding of Cookies:** Believing cookies are secure because they're "set by the server"
3. **Performance Shortcuts:** Avoiding database queries for authorization checks
4. **Lack of Security Training:** Not understanding that all client data can be manipulated
5. **Copy-Paste Coding:** Using insecure patterns from outdated tutorials or examples

### Real-World Impact

This vulnerability is particularly dangerous because:
- **Trivial to exploit:** Only requires browser developer tools
- **Complete privilege escalation:** Regular users become administrators instantly
- **No technical expertise needed:** Any user can modify cookies
- **Silent attacks:** No unusual network traffic or failed attempts to detect
- **Bypasses authentication:** Even proper login doesn't protect against this

---

## 2. Step-by-Step Walkthrough

### Prerequisites
- XAMPP running with Apache and MySQL
- Lab deployed to `http://localhost/AC/lab3/`
- Web browser with Developer Tools

### Step 1: Initial Access

1. **Navigate to the lab:**
   ```
   http://localhost/AC/lab3/
   ```

2. **Observe the interface:**
   - Simple lab description page
   - Login button available
   - No visible admin functionality

### Step 2: Authentication

1. **Login with provided credentials:**
   ```
   Username: wiener
   Password: password
   
   Alternative accounts:
   - carlos / password
   - alice / password
   - bob / password
   ```

2. **After successful login:**
   - Redirected to profile page
   - Your role shows as "user"
   - Session is established

### Step 3: Cookie Discovery

1. **Open Developer Tools (F12)**

2. **Navigate to cookie storage:**
   - Chrome: Application tab → Storage → Cookies
   - Firefox: Storage tab → Cookies
   - Edge: Application tab → Storage → Cookies

3. **Locate the `Admin` cookie:**
   - Name: `Admin`
   - Value: `false`
   - This cookie controls admin access!

### Step 4: Cookie Manipulation

1. **Modify the Admin cookie:**
   - Double-click on the value `false`
   - Change it to `true`
   - Press Enter to save

2. **Verify the change:**
   - Cookie now shows: `Admin = true`

### Step 5: Accessing Admin Panel

1. **Navigate to the admin panel:**
   ```
   http://localhost/AC/lab3/admin.php
   ```

2. **Observe full administrative access:**
   - User management table visible
   - Delete buttons enabled
   - All user data exposed

### Step 6: Complete the Lab Objective

1. **Find user 'carlos' in the table**
2. **Click the Delete button next to carlos**
3. **Confirm the deletion**
4. **Verify carlos is removed from the system**

---

## 3. Why The Exploit Works

### The Vulnerable Authentication Flow

When a user logs in, the application sets an `Admin` cookie based on their database role:

```php
// During login - the cookie is set correctly based on DB role
$admin_status = ($user['role'] === 'admin') ? 'true' : 'false';
setcookie('Admin', $admin_status, time() + 3600, '/');
```

### The Authorization Failure

When checking admin access, the application only looks at the cookie:

```php
// VULNERABILITY: Only checks cookie, not database!
$is_admin = isset($_COOKIE['Admin']) && $_COOKIE['Admin'] === 'true';

if ($is_admin) {
    // Grant full admin access...
}
```

### Why Cookies Are Not Secure for Authorization

1. **User-Controlled Storage:** Cookies are stored in the browser and fully controllable by users
2. **Easy to Modify:** Browser DevTools make cookie editing trivial
3. **No Integrity Protection:** Plain text cookies can be changed to any value
4. **No Authentication Binding:** Cookie doesn't cryptographically prove identity

---

## 4. Vulnerable Code Analysis

### login.php (Setting the Vulnerable Cookie)

```php
<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Session is set correctly
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];  // Role IS in session
            
            // VULNERABILITY: But admin status is also put in cookie!
            $admin_status = ($user['role'] === 'admin') ? 'true' : 'false';
            setcookie('Admin', $admin_status, time() + 3600, '/');
            
            header('Location: profile.php');
            exit;
        }
    }
}
?>
```

### admin.php (Trusting the Cookie)

```php
<?php
session_start();
require_once 'config.php';

// VULNERABILITY: Checking cookie instead of session or database!
$is_admin = isset($_COOKIE['Admin']) && $_COOKIE['Admin'] === 'true';

if (!$is_admin) {
    $error_message = 'Access Denied: You need administrator privileges.';
}

// Admin operations proceed if cookie says "true"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user']) && $is_admin) {
    $user_id = $_POST['user_id'];
    $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->execute([$user_id]);
    $success_message = "User deleted successfully.";
}
?>
```

---

## 5. Secure Implementation

### Correct Authorization Check

```php
<?php
session_start();
require_once 'config.php';

// SECURITY FIX #1: Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=not_authenticated');
    exit;
}

// SECURITY FIX #2: Verify role from DATABASE, not client data
function isUserAdmin($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['role'] === 'admin';
}

// SECURITY FIX #3: Use the secure function
$is_admin = isUserAdmin($pdo, $_SESSION['user_id']);

if (!$is_admin) {
    // Log the unauthorized access attempt
    error_log("Unauthorized admin access by user ID: " . $_SESSION['user_id']);
    http_response_code(403);
    die('Access Denied: You do not have administrative privileges.');
}

// SECURITY FIX #4: Re-verify for sensitive operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    // Double-check admin status
    if (!isUserAdmin($pdo, $_SESSION['user_id'])) {
        die('Unauthorized operation');
    }
    
    // SECURITY FIX #5: Validate CSRF token
    if (!isset($_POST['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF validation failed');
    }
    
    // Safe to proceed
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($user_id && $user_id > 0) {
        $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->execute([$user_id]);
    }
}
?>
```

---

## 6. Key Security Principles

### Never Trust Client Data

All data from the client can be manipulated:
- Cookies
- Form fields (including hidden ones)
- URL parameters
- HTTP headers
- LocalStorage/SessionStorage

### Server-Side Authorization is Mandatory

Authorization decisions must always be made on the server using authoritative data sources:
- Database lookups
- Secure session data
- Properly signed tokens (JWT with signature verification)

### Defense in Depth

Implement multiple layers of security:
1. Authentication check (is user logged in?)
2. Authorization check (does user have permission?)
3. Re-validation before sensitive operations
4. CSRF protection
5. Audit logging

### The Golden Rule

**If a user can modify it, you cannot trust it for security decisions.**

---

## 7. Testing Checklist

- [ ] Try modifying the Admin cookie from `false` to `true`
- [ ] Access `/admin.php` after cookie modification
- [ ] Attempt to delete a user
- [ ] Verify the deletion persists
- [ ] Check if other users can be modified
- [ ] Test if logging out and back in resets the cookie

---

## 8. Lab Credentials

| Username | Password | Role  |
|----------|----------|-------|
| admin    | password | admin |
| carlos   | password | user  |
| wiener   | password | user  |
| alice    | password | user  |
| bob      | password | user  |

---

## 9. Related Vulnerabilities

- **IDOR (Insecure Direct Object Reference):** Accessing other users' data
- **Parameter Tampering:** Modifying request parameters to bypass controls
- **JWT Vulnerabilities:** Weak or missing signature verification
- **Session Fixation:** Taking over user sessions
- **Privilege Escalation:** Gaining higher access than authorized

---

## 10. Further Reading

- OWASP Broken Access Control: https://owasp.org/Top10/A01_2021-Broken_Access_Control/
- OWASP Testing for Privilege Escalation: https://owasp.org/www-project-web-security-testing-guide/
- CWE-639: Authorization Bypass Through User-Controlled Key
