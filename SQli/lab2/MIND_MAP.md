# SQL Injection Lab 2 - Login Bypass Flow

## 🔄 **Attack & Defense Flow**

### **Step 1: Normal Approach**
```
Expected Flow: Input → Validation → Query → Password Check → Session
Normal Login: username='john_doe', password='password123'
Result: ✅ Authenticated if credentials match
```

### **Step 2: Why This Payload Works?**
```
Payload: administrator'-- 

Why It Works:
├── ' → Closes username string in SQL
├── -- → Starts SQL comment 
├── Everything after -- ignored by database
└── Password check completely eliminated
```

### **Step 3: How It Works?**
```sql
-- Normal Query:
SELECT * FROM users WHERE username = 'john_doe' AND password = 'password123'

-- Attack Input: administrator'-- 
SELECT * FROM users WHERE username = 'administrator'-- ' AND password = 'anything'

-- What Database Executes:
SELECT * FROM users WHERE username = 'administrator'
-- Comment eliminates password condition!
```

### **Step 4: Wrong Code Explanation**
```php
// ❌ VULNERABLE CODE (Lines 23-31)
$username = $_POST['username'];    // No validation
$password = $_POST['password'];    // No sanitization
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $db->query($query);      // Direct execution

// Problems:
// - String concatenation allows injection
// - User input becomes SQL code structure
// - Comment syntax eliminates security checks
```

### **Step 5: Why Code Is Wrong?**
```
Critical Flaws:
├── String Concatenation → User input embedded in query
├── No Input Validation → Special chars (', --, #) allowed
├── No Sanitization → SQL metacharacters treated as syntax
├── Plaintext Passwords → Direct comparison, no hashing
└── No Rate Limiting → Unlimited attack attempts

Result: Database can't distinguish data from commands
```

### **Step 6: Right Code Implementation**
```php
// ✅ SECURE CODE
function secureLogin($username, $password) {
    // Input validation
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        return ['error' => 'Invalid format'];
    }
    
    // Rate limiting check
    if (!checkRateLimit($_SERVER['REMOTE_ADDR'])) {
        return ['error' => 'Too many attempts'];
    }
    
    global $pdo;
    
    // Prepared statement (injection prevention)
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Secure password verification
    if ($user && password_verify($password, $user['password'])) {
        // Secure session creation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return ['success' => true];
    }
    
    return ['error' => 'Invalid credentials'];
}
```

### **Step 7: Why It's Right?**
```
Security Mechanisms:
├── Prepared Statements → Query structure fixed, parameters separated
├── Input Validation → Whitelist approach, malicious patterns rejected
├── Password Hashing → Cryptographic verification, no plaintext
├── Rate Limiting → Prevents brute force and automated attacks
├── Session Security → Regeneration prevents fixation attacks
└── Error Handling → Generic messages, no information disclosure

Defense Layers:
┌─ Input Validation (Reject malicious patterns)
├─ Rate Limiting (Block automated attacks)  
├─ Prepared Statements (Prevent injection)
├─ Password Hashing (Secure verification)
├─ Session Security (Prevent hijacking)
└─ Monitoring (Detect attacks)

Result: Multiple barriers prevent successful attacks
```

## 🎯 **Flow Summary**
```
❌ Vulnerable: Input → Concatenation → Injection → Bypass → Admin Access
✅ Secure: Input → Validation → Preparation → Verification → Secure Session
```

## 🔧 **Key Transformations**
```
String Concat     → Prepared Statements
No Validation     → Regex + Length Checks
Plaintext Pass    → BCrypt Hashing  
Basic Sessions    → Secure + CSRF Protection
No Rate Limits    → IP-based Limiting
Root DB User      → Limited Privileges
```