# Lab 6: User ID Controlled by Request Parameter with Unpredictable User IDs

## Overview

This lab demonstrates a **horizontal privilege escalation** vulnerability that exists even when applications use unpredictable identifiers like GUIDs (Globally Unique Identifiers) instead of sequential numeric IDs.

The key insight is that **unpredictable IDs alone do not provide security**. When an application leaks these IDs elsewhere (such as in blog post author links), attackers can discover them and exploit the underlying IDOR vulnerability.

## Vulnerability Details

| Property | Value |
|----------|-------|
| **Vulnerability Type** | IDOR with Information Disclosure |
| **Impact Level** | High |
| **Attack Vector** | URL Parameter Manipulation + Information Gathering |
| **OWASP Classification** | A01:2021 - Broken Access Control |
| **CWE Reference** | CWE-639: Authorization Bypass Through User-Controlled Key |

## What Makes This Different?

Unlike simple IDOR vulnerabilities with predictable IDs:

| Simple IDOR | GUID-based IDOR |
|-------------|-----------------|
| Sequential IDs (1, 2, 3...) | Random GUIDs (f47ac10b-58cc-...) |
| Easy enumeration | Cannot enumerate directly |
| No information gathering needed | Requires finding where GUIDs are leaked |
| Single-step attack | Multi-step attack |

## Attack Scenario

### Step 1: Reconnaissance - Find the Information Leak

Browse to the blog page and examine the author links:

```html
<a href="profile.php?id=f47ac10b-58cc-4372-a567-0e02b2c3d479">
    Carlos Rodriguez
</a>
```

The GUID `f47ac10b-58cc-4372-a567-0e02b2c3d479` belongs to carlos.

### Step 2: Record the Target GUID

Make note of carlos's GUID from the blog:

```
carlos's GUID: f47ac10b-58cc-4372-a567-0e02b2c3d479
```

### Step 3: Login and Observe Your Profile

Login with the provided credentials:

```
Username: wiener
Password: peter
```

After login, observe your profile URL:

```
http://localhost/AC/lab6/profile.php?id=8d7e6f5a-4b3c-2d1e-0f9a-8b7c6d5e4f3a
```

### Step 4: Replace Your GUID with Carlos's

Change the URL to use carlos's GUID:

```
http://localhost/AC/lab6/profile.php?id=f47ac10b-58cc-4372-a567-0e02b2c3d479
```

### Step 5: Retrieve the API Key

Carlos's profile is displayed, including his API key:

```
sk-carlos-x7y8z9a0b1c2d3e4-targetkey
```

## Why This Vulnerability Exists

The vulnerability exists due to several design flaws:

1. **False sense of security** - Developers assumed GUIDs provide protection
2. **Information disclosure** - GUIDs are exposed in blog author links
3. **Missing authorization** - No check if the user owns the requested profile
4. **No access control** - Only authentication is verified, not authorization

## Vulnerable Code Analysis

### Information Disclosure in Blog Template

```php
<!-- blog.php - Leaking GUIDs in author links -->
<?php foreach ($posts as $post): ?>
<article class="blog-card">
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <div class="blog-footer">
        <!-- VULNERABILITY: GUID exposed in public link -->
        <a href="profile.php?id=<?php echo $post['author_guid']; ?>">
            <?php echo htmlspecialchars($post['full_name']); ?>
        </a>
    </div>
</article>
<?php endforeach; ?>
```

### Vulnerable Profile Page

```php
<?php
// profile.php - Missing authorization check

session_start();
require_once 'config.php';

// Authentication check only
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// VULNERABLE: Uses URL parameter directly without authorization
$requestedGuid = $_GET['id'] ?? $_SESSION['user_guid'];

$pdo = getDBConnection();

// VULNERABLE: Fetches ANY user's data based on GUID from URL
$stmt = $pdo->prepare("SELECT * FROM users WHERE guid = ?");
$stmt->execute([$requestedGuid]);
$user = $stmt->fetch();

// Displays sensitive data including API key
?>
```

### What's Wrong

| Issue | Description |
|-------|-------------|
| **GUID Exposure** | Author GUIDs visible in blog links to anyone |
| **No ownership check** | Never verifies requesting user owns the profile |
| **Trust in obscurity** | Relies on GUID unpredictability instead of access control |
| **Sensitive data exposure** | API keys shown without proper authorization |

## Secure Code Implementation

### Option 1: Remove GUID from Public Links

```php
<!-- blog.php - Use usernames instead of GUIDs -->
<?php foreach ($posts as $post): ?>
<article class="blog-card">
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <div class="blog-footer">
        <!-- SECURE: Use public identifier, not internal GUID -->
        <span class="author-name">
            <?php echo htmlspecialchars($post['full_name']); ?>
        </span>
    </div>
</article>
<?php endforeach; ?>
```

### Option 2: Proper Authorization Check

```php
<?php
// profile.php - With authorization check

session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$requestedGuid = $_GET['id'] ?? $_SESSION['user_guid'];

// SECURE: Verify the user is accessing their own profile
if ($requestedGuid !== $_SESSION['user_guid']) {
    http_response_code(403);
    die('Access denied: You can only view your own profile');
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE guid = ?");
$stmt->execute([$requestedGuid]);
$user = $stmt->fetch();
?>
```

### Option 3: Role-Based Access Control

```php
<?php
// profile.php - With RBAC

session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$requestedGuid = $_GET['id'] ?? $_SESSION['user_guid'];

// SECURE: Check permissions
function canViewProfile($currentUserGuid, $requestedGuid, $pdo) {
    // Users can always view their own profile
    if ($currentUserGuid === $requestedGuid) {
        return true;
    }
    
    // Check if current user is admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE guid = ?");
    $stmt->execute([$currentUserGuid]);
    $user = $stmt->fetch();
    
    return ($user && $user['role'] === 'admin');
}

if (!canViewProfile($_SESSION['user_guid'], $requestedGuid, $pdo)) {
    http_response_code(403);
    die('Access denied');
}
?>
```

### Option 4: Remove GUID from URL Entirely

```php
<?php
// profile.php - No GUID in URL

session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// SECURE: Only use session data, never URL parameters
$userGuid = $_SESSION['user_guid'];

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE guid = ?");
$stmt->execute([$userGuid]);
$user = $stmt->fetch();
?>
```

## Comparison: Vulnerable vs Secure Code

| Aspect | Vulnerable Code | Secure Code |
|--------|-----------------|-------------|
| **GUID Exposure** | Leaked in blog links | Hidden or not used |
| **URL Parameter** | Directly trusted | Validated or not used |
| **Authorization** | None (only authentication) | Explicit ownership/role check |
| **Session Usage** | Optional fallback | Primary source of truth |
| **Error Handling** | Silent access | Clear access denied |

## Defense in Depth

For maximum security, implement multiple layers:

```php
<?php
// Multi-layer protection

// 1. Don't expose GUIDs in public links
// 2. Use session-based profile access (no URL param)
// 3. Add authorization check as backup
// 4. Log unauthorized access attempts
// 5. Rate limit profile access

session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Layer 1: Ignore URL parameters, use session only
$userGuid = $_SESSION['user_guid'];

// Layer 2: Verify session is valid (in case of session fixation)
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE guid = ? AND id = ?");
$stmt->execute([$userGuid, $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    // Layer 3: Log suspicious activity
    error_log("Invalid session for user_id: " . $_SESSION['user_id']);
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
```

## Key Takeaways

1. **GUIDs are not access control** - They only make enumeration harder, not impossible
2. **Information leaks negate obscurity** - Any exposed GUID can be exploited
3. **Always implement proper authorization** - Check if the user has permission
4. **Use session data for sensitive operations** - Don't trust URL parameters
5. **Defense in depth** - Multiple security layers are better than one

## Testing for This Vulnerability

### Manual Testing

1. Browse the application without logging in
2. Look for places where user identifiers are exposed (URLs, HTML source, API responses)
3. Record any discovered identifiers
4. Login with a low-privilege account
5. Try accessing resources using the discovered identifiers

### Automated Testing

```python
# Simple GUID discovery script
import requests
from bs4 import BeautifulSoup
import re

def find_guids(url):
    response = requests.get(url)
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # Find all links containing GUIDs
    guid_pattern = r'[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}'
    guids = set()
    
    for link in soup.find_all('a', href=True):
        matches = re.findall(guid_pattern, link['href'], re.IGNORECASE)
        guids.update(matches)
    
    return guids

# Usage
discovered_guids = find_guids('http://localhost/AC/lab6/blog.php')
print(f"Found GUIDs: {discovered_guids}")
```

## References

- [OWASP Testing Guide - IDOR](https://owasp.org/www-project-web-security-testing-guide/)
- [CWE-639: Authorization Bypass Through User-Controlled Key](https://cwe.mitre.org/data/definitions/639.html)
- [PortSwigger - Access Control Vulnerabilities](https://portswigger.net/web-security/access-control)
- [OWASP - Broken Access Control](https://owasp.org/Top10/A01_2021-Broken_Access_Control/)
