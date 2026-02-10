# Lab 1: Modifying Serialized Objects

**Category:** Insecure Deserialization  
**Difficulty:** Apprentice  
**Objective:** Exploit an insecure deserialization vulnerability to escalate privileges and delete user carlos

---

## Lab Overview

This lab demonstrates an **insecure deserialization vulnerability** that allows privilege escalation through cookie manipulation. The application uses PHP serialization to store session data in a client-side cookie, which can be modified by an attacker.

### What is Insecure Deserialization?

Insecure deserialization occurs when an application deserializes data from an untrusted source without proper validation. Attackers can manipulate the serialized data to achieve malicious goals like:
- Privilege escalation
- Remote code execution
- Data tampering
- Authentication bypass

### Attack Surface

| Component | Details |
|-----------|---------|
| Entry Point | Session cookie containing serialized PHP object |
| Vulnerability | Unvalidated deserialization of user-controlled data |
| Impact | Privilege escalation from regular user to administrator |
| Exploitation | Modify the `admin` attribute in the serialized object |

---

## Step-by-Step Walkthrough

### Step 1: Login and Capture the Cookie

1. Log in using the provided credentials: `wiener:peter`
2. Open browser Developer Tools (F12) → Application/Storage tab
3. Find the `session` cookie under Cookies

Example cookie value:
```
session=TzozMDoic3RkQ2xhc3MiOjM6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjU6ImFkbWluIjtiOjA7czo3OiJ1c2VyX2lkIjtpOjM7fQ%3D%3D
```

### Step 2: Decode the Cookie Value

#### URL Decode
```
TzozMDoic3RkQ2xhc3MiOjM6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjU6ImFkbWluIjtiOjA7czo3OiJ1c2VyX2lkIjtpOjM7fQ==
```

#### Base64 Decode
```php
O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:0;s:7:"user_id";i:3;}
```

### Step 3: Analyze the Serialized Object

```
O:8:"stdClass"     → Object of class "stdClass" (8 characters)
:3:                → The object has 3 properties
s:8:"username"     → String property named "username" (8 chars)
s:6:"wiener"       → String value "wiener" (6 chars)
s:5:"admin"        → String property named "admin" (5 chars)
b:0                → Boolean value false (0 = false, 1 = true)
s:7:"user_id"      → String property named "user_id"
i:3                → Integer value 3
```

### Step 4: Modify the Admin Attribute

Change `b:0` to `b:1` to set admin to true:

**Before:**
```php
O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:0;s:7:"user_id";i:3;}
```

**After:**
```php
O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:1;s:7:"user_id";i:3;}
```

### Step 5: Re-encode the Modified Object

**Base64 Encode:**
```
Tzo4OiJzdGRDbGFzcyI6Mzp7czo4OiJ1c2VybmFtZSI7czo2OiJ3aWVuZXIiO3M6NToiYWRtaW4iO2I6MTtzOjc6InVzZXJfaWQiO2k6Mzt9
```

### Step 6: Replace Cookie and Access Admin

1. Replace the `session` cookie with the modified value
2. Navigate to `/my-account.php` - you should see the Admin Panel link
3. Access `/admin.php`
4. Delete user `carlos`

✅ **Lab Completed!**

---

## Why The Exploit Works

### Root Cause Analysis

The vulnerability exists because of several critical security flaws:

1. **Client-Side Session Storage**
   - Session data containing authorization flags is stored in a cookie
   - Users can modify cookies through browser dev tools or proxies

2. **Lack of Integrity Protection**
   - The serialized data is only encoded (Base64), not signed or encrypted
   - No HMAC or digital signature to detect tampering

3. **Trusting Deserialized Data**
   - The server directly trusts the `admin` property from the deserialized object
   - No verification against the database

4. **No Server-Side Validation**
   - Authorization checks use the cookie data instead of querying the user's actual role

> ⚠️ **Security Anti-Pattern:** Never store authorization data in client-side storage without cryptographic integrity protection.

---

## Vulnerable Code Analysis

### Creating the Vulnerable Session Cookie

```php
<?php
// VULNERABLE: Session data stored in client-side cookie
function createSerializedSession($user) {
    $sessionData = new stdClass();
    $sessionData->username = $user['username'];
    
    // The admin flag is set based on database role
    // but stored in client-controllable cookie
    $sessionData->admin = ($user['role'] === 'admin') ? true : false;
    $sessionData->user_id = $user['id'];
    
    // Serialize and encode - NO SIGNING OR ENCRYPTION
    $serialized = serialize($sessionData);
    $encoded = base64_encode($serialized);
    return urlencode($encoded);
}
```

**Problem:** The session data including authorization flags is stored client-side without any integrity protection.

### Deserializing Without Validation

```php
<?php
// VULNERABLE: Directly deserializes user-controlled data
function getSessionFromCookie() {
    if (!isset($_COOKIE['session'])) {
        return null;
    }
    
    $decoded = urldecode($_COOKIE['session']);
    $unserialized = base64_decode($decoded);
    
    // DANGEROUS: unserialize() on user-controlled data
    $sessionData = @unserialize($unserialized);
    
    return $sessionData;
}
```

**Problem:** The cookie value is deserialized directly without any validation or integrity checking.

### Trusting Deserialized Admin Flag

```php
<?php
// VULNERABLE: Trusts the 'admin' property from cookie
function isAdmin() {
    $session = getSessionFromCookie();
    if (!$session) {
        return false;
    }
    
    // DANGEROUS: Directly trusts client-provided data
    return isset($session->admin) && $session->admin === true;
}
```

**Problem:** Authorization is based entirely on the deserialized cookie data, which the attacker controls.

---

## Secure Implementation

### Option 1: Server-Side Sessions (Recommended)

```php
<?php
// SECURE: Use PHP's built-in session handling
session_start();

function secureLogin($user) {
    // Store only the session ID client-side
    // Actual data is stored server-side
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    // DO NOT store role in session - always query from DB
}

function isAdmin() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // ALWAYS check the database for current role
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    return $user && $user['role'] === 'admin';
}
```

### Option 2: Signed Tokens (JWT-style)

```php
<?php
// SECURE: Use HMAC to sign the session data
define('SECRET_KEY', 'your-secret-key-here'); // Store securely!

function createSecureSession($user) {
    $data = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'exp' => time() + 3600 // Expiration time
    ];
    
    $payload = base64_encode(json_encode($data));
    $signature = hash_hmac('sha256', $payload, SECRET_KEY);
    
    return $payload . '.' . $signature;
}

function verifySecureSession($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 2) return null;
    
    list($payload, $signature) = $parts;
    
    // Verify signature
    $expectedSignature = hash_hmac('sha256', $payload, SECRET_KEY);
    if (!hash_equals($expectedSignature, $signature)) {
        return null; // Tampered!
    }
    
    $data = json_decode(base64_decode($payload), true);
    
    // Check expiration
    if ($data['exp'] < time()) {
        return null;
    }
    
    return $data;
}

function isAdmin() {
    $session = verifySecureSession($_COOKIE['session'] ?? '');
    if (!$session) return false;
    
    // STILL query DB for role - never trust token for authorization
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$session['user_id']]);
    $user = $stmt->fetch();
    
    return $user && $user['role'] === 'admin';
}
```

---

## Code Comparison

| Aspect | Vulnerable Code | Secure Code |
|--------|-----------------|-------------|
| Session Storage | Client-side cookie with serialized PHP object | Server-side session or signed JWT |
| Data Integrity | No protection (Base64 only) | HMAC signature or server-side storage |
| Authorization Check | Trusts cookie's `admin` flag | Always queries database for current role |
| Deserialization | Direct `unserialize()` on user input | JSON decode or avoid serialization entirely |
| Attack Surface | User can modify any session attribute | Modifications are detected and rejected |

---

## Key Takeaways

1. **Never store authorization data in client-side storage** without cryptographic protection
2. **Always verify authorization claims** against a trusted server-side source
3. **Use PHP's built-in session handling** instead of custom cookie-based solutions
4. **If using tokens,** ensure they are properly signed and validated
5. **Avoid `unserialize()` on user-controlled data** - use JSON instead

---

## References

- [OWASP Deserialization Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Deserialization_Cheat_Sheet.html)
- [PHP Object Injection](https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection)
- [CWE-502: Deserialization of Untrusted Data](https://cwe.mitre.org/data/definitions/502.html)
