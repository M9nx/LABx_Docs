# Lab 1 Setup Guide

## Prerequisites
- XAMPP or similar PHP/MySQL stack
- PHP 7.4+ with mysqli extension
- MySQL 5.7+ or MariaDB 10.3+

## Database Setup

### Option 1: Automatic Setup
Navigate to `setup_db.php` in your browser:
```
http://localhost/LABx_Docs/Insecure-Deserialization/Lab-01/setup_db.php
```

### Option 2: Category Batch Setup
Use the parent category setup script:
```
http://localhost/LABx_Docs/src/setup.php
```

### Option 3: Manual SQL Import
1. Open phpMyAdmin or MySQL CLI
2. Create database: `CREATE DATABASE deserial_lab1;`
3. Import: `database_setup.sql`

## Database Configuration
The lab uses centralized database credentials from `db-config.php` in the root directory.

Default credentials:
- **Host:** localhost
- **User:** root
- **Password:** (empty)

To change, edit `/db-config.php`.

## Database Structure

### Database: `deserial_lab1`
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Database: `id_progress`
```sql
CREATE TABLE solved_labs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_number INT NOT NULL UNIQUE,
    solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Test Users
| Username | Password | Role |
|----------|----------|------|
| administrator | admin123 | admin |
| carlos | carlos123 | user |
| wiener | peter | user |

## Verification
After setup, visit the lab index page:
```
http://localhost/LABx_Docs/Insecure-Deserialization/Lab-01/index.php
```

Login with `wiener:peter` to verify the lab is working correctly.

## Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify credentials in `/db-config.php`
- Ensure the user has CREATE DATABASE permissions

### "Table not found"
- Run `setup_db.php` again
- Check for SQL errors in phpMyAdmin

### Cookie not being set
- Ensure PHP sessions are enabled
- Check browser allows cookies from localhost
