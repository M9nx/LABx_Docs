# Lab 4: SQL Injection UNION Attack

## 🎦 Quick Start

1. **Setup Database**: Visit `http://localhost/lab4/setup.php`
2. **Start Lab**: Go to `http://localhost/lab4/index.php`
3. **Objective**: Use UNION-based SQL injection to extract admin credentials
4. **Login**: Use discovered credentials at `http://localhost/lab4/login.php`

## 🎣 Lab Goal

Discover the administrator username and password by exploiting SQL injection in the product category filter, then successfully log in as admin.

## 📝 Files Structure

- `index.php` - Main vulnerable application (product catalog)
- `login.php` - Admin login page
- `config.php` - Database configuration
- `database.sql` - Database schema and seed data
- `setup.php` - Automated database setup
- `lab4_documentation.md` - Complete walkthrough and analysis

## ⚠️ Important Notes

- This is an intentionally vulnerable application for educational purposes
- Do not use any of this code in production environments
- The vulnerability allows complete database access

## 🗺️ Hints

1. Try modifying the `category` parameter in the URL
2. Look for SQL error messages
3. Determine how many columns the query returns
4. Use UNION to combine your malicious query with the legitimate one
5. Target the `users` table to find credentials

## 📚 Learn More

Read the complete documentation in `lab4_documentation.md` for:
- Detailed step-by-step solution
- Code analysis (vulnerable vs secure)
- Understanding why the exploit works
- Proper mitigation techniques

Happy hacking! 🔍