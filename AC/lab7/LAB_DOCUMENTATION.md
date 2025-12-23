# Lab 7: User ID Controlled by Request Parameter with Data Leakage in Redirect

## Lab Documentation

### Overview
This lab demonstrates a critical access control vulnerability where sensitive information is leaked in the body of HTTP redirect responses. The application attempts to protect resources by redirecting unauthorized users but fails to stop code execution after sending the redirect header.

---

## 1. Lab Overview

### What is Data Leakage in Redirects?

Data leakage in redirect occurs when a web application:

1. Detects an unauthorized access attempt
2. Sends a redirect header to send the user elsewhere  
3. **Fails to terminate script execution**
4. Continues to output sensitive data in the response body

While browsers automatically follow redirects and users never see the body content, attackers using proxy tools like Burp Suite can capture and read the full response.

### Why This Happens

In PHP (and many other server-side languages), the `header()` function only sends HTTP headers - it does NOT stop script execution. Without an explicit `exit` or `die()` call, the script continues running and outputs content that gets included in the response body.

```php
// VULNERABLE: No exit after redirect
if ($unauthorized) {
    header("Location: index.php");
    // Script continues executing!
}
echo $sensitive_data; // This still gets output!
```

---

## 2. Step-by-Step Walkthrough

### Step 1: Login to Your Account
Navigate to the login page and authenticate:
- Username: `wiener`
- Password: `peter`

### Step 2: Observe the Profile URL
After login, note the URL structure:
```
http://localhost/AC/lab7/profile.php?id=2
```
The `id` parameter identifies which user's profile to display.

### Step 3: Configure Burp Suite
Set up Burp Suite to intercept traffic:
1. Open Burp Suite
2. Go to Proxy → Intercept
3. Configure browser to use proxy (127.0.0.1:8080)

### Step 4: Capture and Modify Request
Capture the profile request and send to Repeater:
```http
GET /AC/lab7/profile.php?id=2 HTTP/1.1
Host: localhost
Cookie: PHPSESSID=your_session_id
```

### Step 5: Change the ID Parameter
Modify `id=2` to `id=3` (carlos):
```http
GET /AC/lab7/profile.php?id=3 HTTP/1.1
Host: localhost
Cookie: PHPSESSID=your_session_id
```

### Step 6: Analyze the Response
The response has a redirect header BUT the body contains sensitive data:
```http
HTTP/1.1 302 Found
Location: index.php
Content-Type: text/html

<!DOCTYPE html>
...
<div class="api-key-value">API-KEY-carlos-Xt7Kp9Qm2Wn5Bv8J</div>
...
```

### Step 7: Submit the Solution
Submit carlos's API key: `API-KEY-carlos-Xt7Kp9Qm2Wn5Bv8J`

---

## 3. Why The Exploit Works

### Root Cause Analysis

| Issue | Description |
|-------|-------------|
| Missing exit after redirect | `header("Location: ...")` not followed by `exit` |
| Data fetched before authorization | User data queried before checking ownership |
| Full page rendering | Entire profile page rendered regardless of redirect |

### The Attack Chain

1. **Request:** Attacker sends request with victim's ID
2. **Data Query:** Server fetches victim's data from database
3. **Check:** Server detects unauthorized access
4. **Redirect:** Server sends redirect header
5. **Leak:** Script continues, outputting sensitive data
6. **Capture:** Attacker's proxy captures full response

---

## 4. Wrong (Vulnerable) Code Explanation

```php
<?php
session_start();
require_once 'config.php';

$id = $_GET['id'] ?? null;

if (!$id || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;  // Correct here
}

$conn = getDBConnection();

// VULNERABILITY: Fetch data BEFORE checking ownership
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// VULNERABILITY: Redirect but DON'T exit!
if ($user && $user['id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    // MISSING: exit;
}
?>
<!-- Full page with sensitive data -->
<div class="api-key"><?php echo $user['api_key']; ?></div>
```

### Problems:

1. **Line 13-17:** Fetches ALL user data before authorization check
2. **Line 20-22:** Sends redirect but NO `exit` - critical flaw
3. **Line 25+:** Outputs sensitive data to response body

---

## 5. Correct Mitigation (Secure Code)

```php
<?php
session_start();
require_once 'config.php';

$id = $_GET['id'] ?? null;

// FIX 1: Early validation with exit
if (!$id || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// FIX 2: Check ownership BEFORE fetching data
if ($id != $_SESSION['user_id']) {
    header("Location: index.php");
    exit;  // CRITICAL: Always exit!
}

// FIX 3: Only fetch data for authorized user
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// FIX 4: Additional ownership verification
if (!$user || $user['id'] != $_SESSION['user_id']) {
    header("Location: index.php");
    exit;
}
?>
<!-- Now safe to output -->
```

### Key Improvements:

1. **Always exit after redirect**
2. **Check before fetch:** Verify authorization before querying
3. **Defense in depth:** Multiple checks for security
4. **Principle of least privilege:** Only fetch authorized data

---

## 6. Comparison (Wrong Code vs Fixed Code)

| Aspect | Vulnerable Code | Secure Code |
|--------|-----------------|-------------|
| After redirect | Script continues | Script terminates |
| Data fetching | Before auth check | After auth check |
| Response body | Contains sensitive data | Empty or safe |
| Security model | Client-side redirect only | Server-side termination |

### Vulnerable Pattern:
```php
if ($unauthorized) {
    header("Location: index.php");
    // No exit!
}
// Sensitive data output...
```

### Secure Pattern:
```php
if ($unauthorized) {
    header("Location: index.php");
    exit;  // Terminates!
}
// Only reached if authorized
```

---

## 7. Prevention Strategies

1. **Always exit after redirect:**
```php
header("Location: /safe-page.php");
exit;
```

2. **Create a redirect helper:**
```php
function redirect($url) {
    header("Location: " . $url);
    exit;
}
```

3. **Use framework methods:** Laravel, Symfony handle this correctly

4. **Check authorization first:** Before fetching any sensitive data

5. **Code review:** Look for `header("Location` without `exit`

---

## 8. Database Schema

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    api_key VARCHAR(64) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Sample Users:
| ID | Username | Password | API Key |
|----|----------|----------|---------|
| 1 | administrator | admin123 | ADMIN-KEY-a9f8e7... |
| 2 | wiener | peter | USER-KEY-wiener-... |
| 3 | carlos | montoya | API-KEY-carlos-Xt7Kp9Qm2Wn5Bv8J |

---

## 9. File Structure

```
lab7/
├── config.php           # Database configuration
├── database_setup.sql   # SQL schema
├── setup_db.php         # Database setup script
├── index.php            # Main page
├── login.php            # Login form
├── logout.php           # Logout handler
├── profile.php          # VULNERABLE profile page
├── submit.php           # Solution submission
├── lab-description.php  # Lab description
├── docs.php             # Full documentation
└── LAB_DOCUMENTATION.md # This file
```

---

## 10. References

- [OWASP Access Control](https://owasp.org/www-community/Access_Control)
- [CWE-698: Redirect Without Exit](https://cwe.mitre.org/data/definitions/698.html)
- [PortSwigger: Access Control Vulnerabilities](https://portswigger.net/web-security/access-control)

---

## Lab Credentials
- **Username:** wiener
- **Password:** peter
- **Target:** carlos (id=3)
- **Goal:** Obtain carlos's API key