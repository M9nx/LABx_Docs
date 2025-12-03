# Lab Setup Instructions

## Prerequisites
- XAMPP with Apache, PHP, and MySQL
- Web browser
- Text editor (optional, for code examination)

## Installation Steps

### 1. XAMPP Setup
1. Ensure XAMPP is installed and running
2. Start Apache and MySQL services from XAMPP Control Panel
3. Verify PHP and MySQL are working by visiting `http://localhost/dashboard/`

### 2. Lab Deployment
1. Copy all lab files to `c:\xampp\htdocs\AC\lab1\`
2. Ensure file structure matches:
   ```
   AC/lab1/
   ├── index.php
   ├── login.php
   ├── logout.php
   ├── profile.php
   ├── administrator-panel.php
   ├── config.php
   ├── robots.txt
   ├── products.php
   ├── about.php
   └── contact.php
   ```

### 3. Database Initialization
1. Access the lab URL: `http://localhost/AC/lab1/`
2. The database and tables will be created automatically
3. Sample users will be inserted automatically

### 4. Verification
1. Visit `http://localhost/AC/lab1/`
2. Try logging in with demo accounts:
   - **Admin:** admin / admin123
   - **User:** carlos / carlos123
3. Verify the database was created by checking phpMyAdmin

## Default User Accounts

| Username | Password | Role  | Purpose |
|----------|----------|-------|----------|
| admin    | admin123 | admin | Administrator account |
| carlos   | carlos123| user  | **Target account for deletion** |
| alice    | alice123 | user  | Regular user account |
| bob      | bob123   | user  | Regular user account |
| eve      | eve123   | user  | Regular user account |

## Lab Access Points

- **Main Site:** `http://localhost/AC/lab1/`
- **Login Page:** `http://localhost/AC/lab1/login.php`
- **Robots.txt:** `http://localhost/AC/lab1/robots.txt`
- **Admin Panel:** `http://localhost/AC/lab1/administrator-panel.php`

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running in XAMPP
- Check if port 3306 is available
- Verify database credentials in `config.php`

### Permission Issues
- Ensure proper file permissions on the lab directory
- Check Apache configuration for directory access

### PHP Errors
- Enable error reporting in PHP configuration
- Check Apache error logs for detailed error messages