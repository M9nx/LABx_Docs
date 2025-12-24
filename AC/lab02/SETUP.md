# Lab 2 Setup Instructions
## Unprotected Admin Functionality with Unpredictable URL

## Prerequisites
- XAMPP with Apache, PHP, and MySQL
- Web browser with developer tools
- Text editor (optional, for code examination)

## Installation Steps

### 1. XAMPP Setup
1. Ensure XAMPP is installed and running
2. Start Apache and MySQL services from XAMPP Control Panel
3. Verify PHP and MySQL are working by visiting `http://localhost/dashboard/`

### 2. Lab Deployment
1. Copy all lab files to `c:\xampp\htdocs\AC\lab2\`
2. Ensure file structure matches:
   ```
   AC/lab2/
   ├── index.php
   ├── login.php
   ├── logout.php
   ├── profile.php
   ├── admin-panel-x7k9p2m5q8w1.php  (unpredictable URL)
   ├── config.php
   ├── solutions.php
   ├── services.php
   ├── about.php
   └── contact.php
   ```

### 3. Database Initialization
1. Access the lab URL: `http://localhost/AC/lab2/`
2. The database and tables will be created automatically
3. Sample users with corporate data will be inserted automatically

### 4. Verification
1. Visit `http://localhost/AC/lab2/`
2. Try logging in with demo accounts:
   - **Admin:** admin / admin123
   - **Manager:** sarah / sarah123
   - **User:** carlos / carlos123
3. Verify the database was created by checking phpMyAdmin

## User Accounts for Testing

| Username | Password | Role    | Department     | Purpose |
|----------|----------|---------|----------------|----------|
| admin    | admin123 | admin   | IT Security    | System administrator |
| carlos   | carlos123| user    | Marketing      | **TARGET for deletion** |
| sarah    | sarah123 | manager | Human Resources| Manager account |
| mike     | mike123  | user    | Engineering    | Engineer with secret clearance |
| emma     | emma123  | user    | Finance        | Finance analyst |
| alex     | alex123  | manager | Operations     | Operations manager |
| lisa     | lisa123  | user    | Legal          | Legal counsel |

## Lab Access Points

- **Main Site:** `http://localhost/AC/lab2/`
- **Login Page:** `http://localhost/AC/lab2/login.php`
- **Admin Panel:** `http://localhost/AC/lab2/admin-panel-x7k9p2m5q8w1.php`

## Lab Objectives

1. **Primary Goal:** Delete the user "carlos" by accessing the unprotected admin panel
2. **Discovery Method:** Find the admin panel URL through source code analysis
3. **Learning Goal:** Understand how information disclosure in client-side code can expose sensitive functionality

## Vulnerability Discovery Methods

### Method 1: Browser Developer Tools
1. Visit `http://localhost/AC/lab2/`
2. Right-click and select "Inspect" or press F12
3. Go to the "Sources" tab
4. Examine the JavaScript code in `index.php`
5. Look for admin panel URL in the configuration object

### Method 2: View Page Source
1. Visit `http://localhost/AC/lab2/`
2. Right-click and select "View Page Source"
3. Search for "admin" in the source code
4. Find the JavaScript configuration with admin panel URL

### Method 3: Browser Console
1. Visit `http://localhost/AC/lab2/`
2. Open browser developer tools (F12)
3. Go to the "Console" tab
4. Type: `quickAdminAccess()` or `getAdminPanelUrl()`
5. Observe the admin panel URL or direct redirection

### Method 4: JavaScript Global Objects
1. Open browser console on the main page
2. Type: `window.appConfig`
3. Explore the configuration object
4. Find the admin endpoint in `apiEndpoints.admin`

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running in XAMPP
- Check if port 3306 is available
- Verify database credentials in `config.php`

### Admin Panel Not Found
- Verify the exact URL: `admin-panel-x7k9p2m5q8w1.php`
- Check file permissions and case sensitivity
- Ensure the file was deployed correctly

### JavaScript Not Loading
- Check browser console for errors
- Verify JavaScript is enabled in browser
- Check for any content blockers or security extensions

### Permission Issues
- Ensure proper file permissions on the lab directory
- Check Apache configuration for directory access
- Verify PHP is processing files correctly