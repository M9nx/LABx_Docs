# SQL Injection Educational Report

  

  
##  **Understanding the Problem**

  

### **The Vulnerable Code (Lines 18-19)**

  

Let's look at the problematic code in our `index.php` file:

  

```php

// Get user input

$category = isset($_GET['category']) ? $_GET['category'] : '';

  

// Build SQL query - THIS IS WRONG!

$query = "SELECT * FROM products WHERE category = '$category' AND is_released = 1";

  

// Execute the query

$result = $db->query($query);

```

  

### **Why This is Dangerous**

  

The problem is in how we build the SQL query. We take user input (`$category`) and directly put it into our SQL string. This is like giving a stranger the keys to your house!

  

**What we intended:**

```sql

SELECT * FROM products WHERE category = 'electronics' AND is_released = 1

```

  

**What an attacker can do:**

When someone enters: `' OR 1=1--`

  

**The query becomes:**

```sql

SELECT * FROM products WHERE category = '' OR 1=1--' AND is_released = 1

```

  

**Which the database reads as:**

```sql

SELECT * FROM products WHERE category = '' OR 1=1

```

(Everything after `--` is ignored as a comment)

  

##  **Step-by-Step Attack Example**

  

### **Normal User Input:**

- User types: `electronics`

- URL becomes: `?category=electronics`

- SQL query: `SELECT * FROM products WHERE category = 'electronics' AND is_released = 1`

- Result: Shows only released electronics products 

  

### **Malicious User Input:**

- Attacker types: `' OR 1=1--`

- URL becomes: `?category=' OR 1=1--`

- SQL query:  `SELECT * FROM products WHERE category = '' OR 1=1--' AND is_released = 1`

- Database sees:  `SELECT * FROM products WHERE category = '' OR 1=1`

- Result: Shows ALL products including secret unreleased ones! 

  

##  **Wrong vs Right Approaches**

  

###  WRONG WAY - String Concatenation

  

```php

// DON'T DO THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

$category = $_GET['category'];

$query = "SELECT * FROM products WHERE category = '$category'";

$result = $db->query($query);

```

  

**Problems:**

- User input goes directly into SQL

- No validation or checking

- Attackers can inject malicious code

- Can expose sensitive data

  

### **RIGHT WAY - Prepared Statements**

  

```php

// DO THIS INSTEAD!

$category = $_GET['category'];

$stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND is_released = 1");

$stmt->bind_param("s", $category);

$stmt->execute();

$result = $stmt->get_result();

```

  

**Why this works:**

- SQL structure is separated from data

- Database treats user input as data only, not code

- No way to inject malicious SQL

- Much safer approach

  

### **BETTER WAY - Add Input Validation**

  

```php

// EVEN BETTER - Validate input first!

function isValidCategory($category) {

    $allowed = ['electronics', 'computers', 'audio', 'clothing'];

    return in_array($category, $allowed);

}

  

$category = $_GET['category'];

  

// Check if input is valid

if (!isValidCategory($category)) {

    echo "Invalid category!";

    exit;

}

  

// Use prepared statement

$stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND is_released = 1");

$stmt->bind_param("s", $category);

$stmt->execute();

$result = $stmt->get_result();

```

  

##  **How to Fix Our Code**

  

### **Step 1: Replace the Vulnerable Query**

  

**Change this:**

```php

$query = "SELECT * FROM products WHERE category = '$category' AND is_released = 1";

$result = $db->query($query);

```

  

**To this:**

```php

$stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND is_released = 1");

$stmt->bind_param("s", $category);

$stmt->execute();

$result = $stmt->get_result();

```

  

### **Step 2: Add Input Validation**

  

**Add before the database query:**

```php

// Define what categories we allow

$allowed_categories = ['electronics', 'computers', 'audio', 'clothing'];

  

// Check if the input is in our allowed list

if (!in_array($category, $allowed_categories)) {

    $error = "Please select a valid category";

    // Don't run the database query

    exit;

}

```

  

### **Step 3: Handle Results Safely**

  

**When displaying data, escape output:**

```php

// Always escape data when displaying to prevent XSS

echo htmlspecialchars($product['name']);

echo htmlspecialchars($product['description']);

```

  

---
