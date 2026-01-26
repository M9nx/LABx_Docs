# Lab 5: User ID Controlled by Request Parameter

## Overview

This lab demonstrates a **horizontal privilege escalation** vulnerability caused by using user-controllable parameters to access other users' data. This type of vulnerability is classified as an **Insecure Direct Object Reference (IDOR)**.

The application's profile page uses a URL parameter (`id`) to determine which user's profile to display. However, it fails to verify that the logged-in user is authorized to view the requested profile, allowing any authenticated user to view any other user's sensitive information.

## Vulnerability Details

| Property | Value |
|----------|-------|
| **Vulnerability Type** | Insecure Direct Object Reference (IDOR) |
| **Impact Level** | High |
| **Attack Vector** | URL Parameter Manipulation |
| **OWASP Classification** | A01:2021 - Broken Access Control |
| **CWE Reference** | CWE-639: Authorization Bypass Through User-Controlled Key |

## Attack Scenario

### Step 1: Login with Valid Credentials

Login to the application using the provided credentials:

```
Username: wiener
Password: peter
```

### Step 2: Observe the Profile URL

After logging in, you are redirected to your profile page. Notice the URL structure:

```
http://localhost/AC/lab5/profile.php?id=wiener
```

The `id` parameter in the URL determines whose profile is displayed.

### Step 3: Exploit the Vulnerability

Simply change the `id` parameter to view another user's profile:

```
http://localhost/AC/lab5/profile.php?id=carlos
```

### Step 4: Retrieve Sensitive Data

Carlos's profile is now displayed, including his API key:

```
sk-carlos-a1b2c3d4e5f6g7h8-targetkey
```

## Why This Vulnerability Exists

The vulnerability exists because the application:

1. **Trusts user input directly** - Uses the `id` parameter without validation
2. **No authorization check** - Doesn't verify if the logged-in user should see the requested profile
3. **Assumes authentication equals authorization** - Being logged in doesn't mean you can access all data

## Vulnerable Code Analysis

Here's the vulnerable code in `profile.php`:

```php
<?php
session_start();
require_once 'config.php';

// Require authentication (only checks if logged in)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// VULNERABLE: Uses URL parameter directly without authorization check
$requestedUser = $_GET['id'] ?? $_SESSION['username'];

$pdo = getDBConnection();

// VULNERABLE: Fetches ANY user's data based on URL parameter
// No check to verify if logged-in user should see this data
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$requestedUser]);
$user = $stmt->fetch();
?>
```

### What's Wrong

| Issue | Description |
|-------|-------------|
| **No ownership check** | The code never verifies that `$_SESSION['username']` matches `$_GET['id']` |
| **Direct object access** | User-controlled parameter is used directly to fetch database records |
| **Implicit trust** | The application assumes authenticated users will only access their own data |

## Secure Code Implementation

Here's how to fix the vulnerability:

### Option 1: Only Allow Users to View Their Own Profile

```php
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// SECURE: Only use session data, ignore URL parameters
$username = $_SESSION['username'];

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
?>
```

### Option 2: Allow Profile Viewing with Authorization Check

```php
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$requestedUser = $_GET['id'] ?? $_SESSION['username'];

// SECURE: Verify the user is accessing their own profile
if ($requestedUser !== $_SESSION['username']) {
    // Option A: Redirect to their own profile
    header('Location: profile.php?id=' . $_SESSION['username']);
    exit();
    
    // Option B: Show access denied error
    // die('Access denied: You can only view your own profile');
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$requestedUser]);
$user = $stmt->fetch();
?>
```

### Option 3: Role-Based Access Control

```php
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$requestedUser = $_GET['id'] ?? $_SESSION['username'];

// SECURE: Check if user has permission to view this profile
function canViewProfile($currentUser, $requestedUser, $pdo) {
    // Users can always view their own profile
    if ($currentUser === $requestedUser) {
        return true;
    }
    
    // Check if current user is an admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
    $stmt->execute([$currentUser]);
    $user = $stmt->fetch();
    
    return ($user && $user['role'] === 'admin');
}

if (!canViewProfile($_SESSION['username'], $requestedUser, $pdo)) {
    http_response_code(403);
    die('Access denied: Insufficient permissions');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$requestedUser]);
$user = $stmt->fetch();
?>
```

## Comparison: Vulnerable vs Secure Code

| Aspect | Vulnerable Code | Secure Code |
|--------|-----------------|-------------|
| **User Input** | Directly used from URL | Validated and authorized |
| **Authorization** | None (only authentication) | Explicit ownership/role check |
| **Data Access** | Any user's data accessible | Only authorized data accessible |
| **Session Usage** | Ignored for data fetching | Used as source of truth |

## Testing for IDOR Vulnerabilities

### Manual Testing Steps

1. **Identify Object References**: Look for parameters like `id`, `user_id`, `account`, `uid`, etc.
2. **Map Access Patterns**: Understand the expected access control model
3. **Test Horizontal Access**: Try accessing other users' resources at the same privilege level
4. **Test Vertical Access**: Try accessing admin or higher-privilege resources

### Automated Testing

Using Burp Suite or similar tools:

```
# Original Request
GET /profile.php?id=wiener HTTP/1.1
Cookie: PHPSESSID=abc123

# Modified Request (IDOR Test)
GET /profile.php?id=carlos HTTP/1.1
Cookie: PHPSESSID=abc123
```

## Prevention Best Practices

1. **Never trust user input** for authorization decisions
2. **Use session data** to determine the current user
3. **Implement proper authorization checks** at every access point
4. **Use indirect references** (map user IDs to session-specific tokens)
5. **Implement audit logging** to detect unauthorized access attempts
6. **Apply principle of least privilege**

## IDOR Variants to Watch For

| Variant | Example | Risk |
|---------|---------|------|
| **Numeric IDs** | `/profile?id=123` | Sequential enumeration |
| **UUIDs** | `/profile?id=550e8400-e29b-...` | Still exploitable if predictable |
| **Usernames** | `/profile?id=carlos` | Easy to guess |
| **Filenames** | `/download?file=report_carlos.pdf` | Path traversal + IDOR |
| **API Endpoints** | `GET /api/users/123` | REST API IDOR |

## Impact Assessment

This vulnerability allows attackers to:

- **View sensitive personal data** of other users
- **Access API keys** that could grant further system access
- **Harvest user information** for social engineering attacks
- **Violate privacy regulations** (GDPR, CCPA)
- **Potentially escalate privileges** if admin data is exposed

## References

- [OWASP Testing Guide - IDOR](https://owasp.org/www-project-web-security-testing-guide/)
- [CWE-639: Authorization Bypass Through User-Controlled Key](https://cwe.mitre.org/data/definitions/639.html)
- [PortSwigger - Insecure Direct Object References](https://portswigger.net/web-security/access-control/idor)
- [HackerOne IDOR Reports](https://hackerone.com/hacktivity?querystring=idor)
