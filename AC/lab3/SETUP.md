# Lab 3 Setup Guide

## Prerequisites

- XAMPP installed with Apache and MySQL
- Web browser with Developer Tools (Chrome, Firefox, or Edge)
- MySQL Workbench (optional, for database management)

## Installation Steps

### 1. Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### 2. Configure Database Connection

Edit `config.php` if your MySQL credentials differ:

```php
$host = 'localhost';
$dbname = 'lab3_db';
$username = 'root';
$password = 'root';  // Change if different
```

### 3. Setup Database

#### Option A: Using MySQL Workbench

1. Open MySQL Workbench
2. Connect to your local MySQL server
3. Open and execute `database_setup.sql`

#### Option B: Using Command Line

```bash
mysql -u root -p < database_setup.sql
```

#### Option C: Using phpMyAdmin

1. Navigate to `http://localhost/phpmyadmin`
2. Click "Import"
3. Select `database_setup.sql`
4. Click "Go"

### 4. Verify Installation

1. Navigate to `http://localhost/AC/lab3/`
2. You should see the lab landing page
3. Click "Start Lab" to begin
4. Try logging in with `wiener` / `password`

## Database Structure

The lab uses a simple users table:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Test Accounts

| Username | Password | Role  |
|----------|----------|-------|
| admin    | password | admin |
| wiener   | password | user  |
| carlos   | password | user  |
| alice    | password | user  |
| bob      | password | user  |

## Troubleshooting

### Database Connection Error

- Verify MySQL is running in XAMPP
- Check credentials in `config.php`
- Ensure `lab3_db` database exists

### Page Not Found

- Verify Apache is running
- Check that files are in `htdocs/AC/lab3/`
- Verify URL is correct

### Cannot Login

- Run `database_setup.sql` to create users
- Check that the users table has data
- Verify password hashes are correct

## Resetting the Lab

To reset the lab to its initial state:

1. Run `database_setup.sql` again
2. Clear your browser cookies for localhost
3. Restart the lab

## Support

For issues or questions, refer to:
- `docs.php` - Detailed vulnerability documentation
- `LAB_DOCUMENTATION.md` - Technical writeup
