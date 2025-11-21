# SQL Injection Lab 2 - Login Bypass Report

## 🎯 Lab Overview
**Target:** Administrator account bypass  
**Method:** SQL injection in login form  
**Impact:** Critical authentication failure  

## 🔍 Attack Flow Analysis

### **Step 1: Normal Authentication Approach**
```
Expected Flow:
User Input → Input Validation → Database Query → Password Verification → Session Creation

Normal Example:
Username: "john_doe"
Password: "password123"
Result: User authenticated successfully if credentials match database
```

### **Step 2: Why This Payload Works?**
```
Attack Payload: administrator'-- 

Why It Works:
1. The single quote (') terminates the username string in SQL
2. The double dash (--) starts a SQL comment
3. Everything after -- is ignored by the database
4. Password check is completely eliminated from the query
5. Database only checks if username 'administrator' exists
```

### **Step 3: How The Attack Mechanism Works?**

#### **Query Transformation Process:**
```sql
-- STEP 1: Original Intended Query
SELECT * FROM users WHERE username = 'USER_INPUT' AND password = 'PASS_INPUT'

-- STEP 2: Normal User Input
SELECT * FROM users WHERE username = 'john_doe' AND password = 'password123'
-- Both conditions must be true for authentication

-- STEP 3: Malicious Input Injection
Input: username = "administrator'-- " and password = "anything"

-- STEP 4: Constructed Malicious Query  
SELECT * FROM users WHERE username = 'administrator'-- ' AND password = 'anything'

-- STEP 5: What Database Actually Executes
SELECT * FROM users WHERE username = 'administrator'
-- The comment (--) makes database ignore everything after it
-- Password condition is completely removed!
```

#### **Why Different Payloads Work:**
```sql
-- Comment-based attacks:
administrator'--     # MySQL requires space after --
administrator'#      # Hash comment works without space
administrator'/*     # Multi-line comment start

-- Boolean logic attacks:
admin' OR '1'='1'--  # OR condition always true
admin' OR 1=1--      # Numeric comparison always true  
' OR 'a'='a'--       # String comparison always true
```

### **Step 4: Analyzing The Wrong Code**
```php
// ❌ VULNERABLE CODE - Lines 23-31 in index.php
$username = $_POST['username'];  // Line 23: Direct assignment, no validation
$password = $_POST['password'];  // Line 24: Direct assignment, no validation

// Line 27: String concatenation vulnerability
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

// Line 31: Direct execution without preparation
$result = $db->query($query);

// Authentication logic
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];  // Session created = successful login
}
```

### **Step 5: Why This Code Is Wrong?**

#### **Critical Security Flaws:**
```
1. STRING CONCATENATION VULNERABILITY
   Problem: User input directly embedded in SQL query
   Impact: User input becomes part of SQL command structure
   Risk: Database cannot distinguish between data and commands

2. NO INPUT VALIDATION
   Problem: Any character accepted without checking
   Impact: Special SQL characters (', --, #) allowed
   Risk: Enables comment injection and query manipulation

3. NO INPUT SANITIZATION  
   Problem: Dangerous characters not escaped or removed
   Impact: SQL metacharacters treated as syntax
   Risk: Query structure can be modified by attacker

4. PLAINTEXT PASSWORD STORAGE
   Problem: Passwords stored without hashing
   Impact: Direct password comparison in database
   Risk: Password exposure if database compromised

5. NO RATE LIMITING OR ACCOUNT LOCKOUT
   Problem: Unlimited login attempts allowed
   Impact: Enables brute force and injection attempts
   Risk: Automated attacks can continue indefinitely
```

#### **Security Architecture Problems:**
```
Database Level:
- Root user used for application connection
- No prepared statement support implemented
- Error messages reveal database structure

Application Level:  
- No session security (fixation vulnerability)
- Missing CSRF protection
- No security headers implemented

Network Level:
- No rate limiting by IP address
- Missing intrusion detection
- No failed attempt monitoring
```

### **Step 6: The Right Code Implementation**

#### **Primary Defense - Prepared Statements:**
```php
// ✅ SECURE APPROACH - Prepared Statements
function secureLogin($username, $password) {
    global $pdo;
    
    // WHY THIS WORKS: Parameters separated from query structure
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    
    // WHY THIS IS SAFE: Parameter binding prevents injection
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // WHY SECURE: Password verification with proper hashing
    if ($user && password_verify($password, $user['password'])) {
        return $user;  // Authentication successful
    }
    return false;  // Authentication failed
}
```

#### **Complete Secure Implementation:**
```php
// ✅ FULL SECURITY IMPLEMENTATION
<?php
function authenticateUser($username, $password) {
    // STEP 1: Input Validation
    // WHY: Reject obviously malicious input before processing
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        logSecurityEvent("Invalid username format: " . $username);
        return ['error' => 'Invalid username format'];
    }
    
    if (strlen($password) < 6 || strlen($password) > 100) {
        return ['error' => 'Invalid password length'];
    }
    
    // STEP 2: Rate Limiting Check  
    // WHY: Prevents brute force and automated injection attempts
    if (!checkRateLimit($_SERVER['REMOTE_ADDR'], 5, 300)) {
        logSecurityEvent("Rate limit exceeded for IP: " . $_SERVER['REMOTE_ADDR']);
        return ['error' => 'Too many attempts, please try later'];
    }
    
    // STEP 3: Account Lockout Check
    // WHY: Prevents targeted attacks on specific accounts
    if (!isAccountUnlocked($username)) {
        logSecurityEvent("Login attempt on locked account: " . $username);
        return ['error' => 'Account temporarily locked'];
    }
    
    try {
        global $pdo;
        
        // STEP 4: Prepared Statement Execution
        // WHY: Completely separates code from data
        $stmt = $pdo->prepare("SELECT id, username, password, role, failed_attempts FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // STEP 5: Secure Password Verification
            // WHY: Uses cryptographic hashing instead of plaintext comparison
            if (password_verify($password, $user['password'])) {
                
                // STEP 6: Secure Session Management
                // WHY: Prevents session fixation attacks
                session_regenerate_id(true);
                
                // Set secure session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Clear failed attempts on successful login
                clearFailedAttempts($username);
                
                logSecurityEvent("Successful login: " . $username);
                return ['success' => true, 'user' => $user];
                
            } else {
                // Log failed attempt for monitoring
                logFailedAttempt($username);
                logSecurityEvent("Failed login attempt: " . $username);
                return ['error' => 'Invalid credentials'];
            }
        } else {
            // User doesn't exist
            logSecurityEvent("Login attempt for non-existent user: " . $username);
            return ['error' => 'Invalid credentials'];
        }
        
    } catch (PDOException $e) {
        // Log database errors without revealing details
        error_log("Database error in authentication: " . $e->getMessage());
        return ['error' => 'System error occurred'];
    }
}
?>
```

### **Step 7: Why This Implementation Is Right?**

#### **Security Principles Applied:**

**1. DEFENSE IN DEPTH:**
```
Layer 1: Input Validation - Rejects malicious patterns
Layer 2: Rate Limiting - Prevents automated attacks  
Layer 3: Prepared Statements - Blocks SQL injection
Layer 4: Password Hashing - Protects stored credentials
Layer 5: Session Security - Prevents session attacks
Layer 6: Logging & Monitoring - Detects attack attempts
```

**2. SEPARATION OF CONCERNS:**
```
Query Structure: Fixed at compile time, cannot be modified
User Data: Passed as parameters, treated only as data
SQL Commands: Cannot be injected through user input
Database Logic: Isolated from application input processing
```

**3. PRINCIPLE OF LEAST PRIVILEGE:**
```
Database User: Limited permissions, not root access
Session Data: Only necessary information stored
Error Messages: Generic responses, no system details revealed
Access Control: Role-based permissions enforced
```

**4. SECURE BY DESIGN:**
```
Default Deny: Reject unless explicitly allowed
Fail Securely: Errors don't grant access
Input Validation: Whitelist approach for allowed characters
Cryptographic Security: Strong hashing algorithms used
```

#### **Technical Security Mechanisms:**

**Prepared Statements Protection:**
```sql
-- HOW IT WORKS:
-- 1. Query structure sent to database first
PREPARE stmt FROM "SELECT * FROM users WHERE username = ?"

-- 2. Parameters sent separately  
EXECUTE stmt USING 'administrator\'--'

-- 3. Database treats parameter as pure data, not code
-- Result: String 'administrator\'--' searched literally
-- No SQL injection possible because structure is fixed
```

**Password Security Implementation:**
```php
// SECURE PASSWORD FLOW:
// 1. Registration/Update:
$hash = password_hash($plaintext, PASSWORD_DEFAULT, ['cost' => 12]);
// Creates: $2y$12$randomsalt.hashedpassword

// 2. Authentication:
if (password_verify($input, $stored_hash)) {
    // WHY SECURE: Uses time-constant comparison
    // Prevents timing attacks on password verification
}
```

**Session Security Configuration:**
```php
// SECURE SESSION SETTINGS:
ini_set('session.cookie_secure', '1');        // HTTPS only
ini_set('session.cookie_httponly', '1');      // No JavaScript access  
ini_set('session.use_strict_mode', '1');      // Prevent ID fixation
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection

// WHY THIS WORKS: Multiple layers prevent different attack vectors
```

## 🚨 Impact & Business Risk

**Attack Success Scenario:**
- Complete authentication bypass achieved
- Administrative privileges gained instantly  
- All user data and system functions accessible
- No trace of unauthorized access in normal logs

**Business Impact:**
- Regulatory fines (GDPR: €20M, PCI DSS violations)
- Legal liability for data breaches
- Customer trust and reputation damage
- Operational disruption and recovery costs

## 📝 Key Security Lessons

1. **Input is Code:** Any user input can become executable code without proper handling
2. **Defense Layers:** Multiple security mechanisms provide better protection than single solutions
3. **Secure Defaults:** Systems should fail securely and deny access by default
4. **Monitoring Matters:** Attack detection and logging enable rapid response
5. **Regular Testing:** Security vulnerabilities evolve and require ongoing assessment

---
**⚠️ Educational Purpose:** This demonstrates real attack techniques for learning. Apply security measures in all production systems.

