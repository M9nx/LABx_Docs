# Lab 3: SQL Injection UNION Attack - Column Discovery

## Quick Setup

1. **Start XAMPP services:**
   - Start Apache
   - Start MySQL

2. **Setup the database:**
   - Navigate to: `http://localhost/lab3/setup.php`
   - Or manually import `database.sql` into MySQL

3. **Access the lab:**
   - Navigate to: `http://localhost/lab3/index.php`

## Lab Objective

Determine the number of columns returned by the vulnerable SQL query using a UNION-based SQL injection attack.

## Target

The `category` parameter in the URL is vulnerable to SQL injection.

## Files Structure

```
lab3/
├── index.php          # Main vulnerable application
├── config.php         # Database configuration
├── database.sql       # Database schema and data
├── setup.php         # Automated setup script
└── README.md         # This file
```

## Testing the Vulnerability

1. Start with a normal request: `http://localhost/lab3/index.php?category=Electronics`
2. Try injecting: `http://localhost/lab3/index.php?category=Electronics' UNION SELECT NULL--`
3. Continue adding NULL values until the error disappears

## Security Note

⚠️ **This lab is intentionally vulnerable for educational purposes only!**
- Never deploy this in a production environment
- Use only for learning SQL injection techniques
- Always implement proper security measures in real applications