# Lab 2: Unprotected Admin Functionality with Unpredictable URL
## Access Control Vulnerabilities

![Security Level: Intentionally Vulnerable](https://img.shields.io/badge/Security-Intentionally%20Vulnerable-red)
![Lab Type: Access Control + Information Disclosure](https://img.shields.io/badge/Lab%20Type-Access%20Control%20%2B%20Info%20Disclosure-orange)
![Difficulty: Beginner to Intermediate](https://img.shields.io/badge/Difficulty-Beginner%20to%20Intermediate-yellow)

---

## 🎯 Quick Start

1. **Ensure XAMPP is running** (Apache + MySQL)
2. **Access the lab:** `http://localhost/AC/lab2/`
3. **Analyze the source code** to find the admin panel URL
4. **Access the hidden admin panel:** `http://localhost/AC/lab2/admin-panel-x7k9p2m5q8w1.php`
5. **Delete carlos** to complete the lab

---

## 🔍 Lab Objective

**Goal:** Find and access the unprotected admin panel, then delete the user "carlos".

**Learning Objectives:** 
- Understand information disclosure vulnerabilities
- Learn why "security through obscurity" fails
- Recognize the importance of proper access controls

---

## 🕵️ Discovery Methods

### Method 1: View Page Source
1. Right-click on homepage → "View Page Source"
2. Search for "admin" in the source code
3. Find the JavaScript configuration with admin URL

### Method 2: Browser Developer Tools
1. Press F12 to open developer tools
2. Go to "Sources" tab
3. Examine JavaScript code for configuration objects

### Method 3: Browser Console
1. Open developer console (F12 → Console)
2. Type: `window.appConfig` to see exposed configuration
3. Type: `quickAdminAccess()` for direct navigation
4. Observe console messages that advertise the admin URL

### Method 4: JavaScript Functions
```javascript
getAdminPanelUrl()    // Returns admin panel URL
quickAdminAccess()    // Direct redirect to admin panel
```

---

## 🗂️ Lab Structure

```
AC/lab2/
├── 📄 index.php                          # Main page with JS disclosure
├── 🔐 login.php                          # User authentication
├── 📤 logout.php                         # Session termination
├── 👤 profile.php                        # User dashboard
├── 🚨 admin-panel-x7k9p2m5q8w1.php       # VULNERABLE: Hidden admin panel
├── ⚙️ config.php                         # Database setup
├── 🏢 solutions.php                      # Corporate solutions page
├── 🔧 services.php                       # Services page
├── ℹ️ about.php                         # About page
├── 📞 contact.php                        # Contact page
├── 🗄️ database_setup.sql                 # Manual database script
├── 📖 SETUP.md                           # Installation instructions
├── 📚 LAB_DOCUMENTATION.md                # Complete vulnerability analysis
└── 📄 README.md                          # This file
```

---

## 👥 Demo Accounts

| Username | Password  | Role    | Department      | Purpose |
|----------|-----------|---------|-----------------|---------|
| `admin`  | admin123  | admin   | IT Security     | System administrator |
| `carlos` | carlos123 | user    | Marketing       | **🎯 TARGET for deletion** |
| `sarah`  | sarah123  | manager | Human Resources | HR Manager |
| `mike`   | mike123   | user    | Engineering     | Senior Engineer |
| `emma`   | emma123   | user    | Finance         | Financial Analyst |
| `alex`   | alex123   | manager | Operations      | Operations Manager |
| `lisa`   | lisa123   | user    | Legal           | Legal Counsel |

---

## 🔍 Key Vulnerabilities

### 🚨 Information Disclosure
- ❌ **Admin URL exposed** in JavaScript configuration
- ❌ **Global object exposure** via `window.appConfig`
- ❌ **Debug functions** accessible in production
- ❌ **Console messages** advertising admin access
- ❌ **HTML comments** containing sensitive URLs

### 🚨 Access Control Failures
- ❌ **No authentication** required for admin panel
- ❌ **No authorization** checks for administrative functions
- ❌ **Direct URL access** to sensitive functionality
- ❌ **Complete data exposure** including salaries and personal info
- ❌ **Administrative operations** without permission validation

---

## 🕵️ Walkthrough

### Step 1: Source Code Analysis
**Right-click on homepage → View Page Source**

Look for this vulnerable JavaScript configuration:
```javascript
const config = {
    apiEndpoints: {
        users: '/api/users',
        products: '/api/products',
        admin: '/admin-panel-x7k9p2m5q8w1.php'  // 🚨 EXPOSED!
    }
};
```

### Step 2: Console Exploitation
**Open browser console (F12)**

Observe automatic disclosure:
```
TechCorp Developer Console
For admin access, use: quickAdminAccess()
Admin panel URL: /admin-panel-x7k9p2m5q8w1.php
```

Execute the exposed function:
```javascript
quickAdminAccess()  // Direct navigation to admin panel
```

### Step 3: Admin Panel Access
**Navigate to discovered URL:**
```
http://localhost/AC/lab2/admin-panel-x7k9p2m5q8w1.php
```

### Step 4: Complete the Objective
1. **Find Carlos Rodriguez** in the employee management table
2. **Review exposed sensitive data:**
   - Salary: $65,000.00
   - Security clearance: Basic
   - Personal address and emergency contacts
3. **Delete carlos** using the delete button
4. **Verify deletion** - user no longer exists

---

## 🛠️ Technical Analysis

### Information Disclosure Vectors

**JavaScript Configuration:**
```javascript
// VULNERABLE: Admin endpoint exposed to all users
window.appConfig = {
    apiEndpoints: {
        admin: '/admin-panel-x7k9p2m5q8w1.php'
    }
};
```

**Development Functions:**
```javascript
// VULNERABLE: Debug functions in production
function quickAdminAccess() {
    window.location.href = getAdminPanelUrl();
}
```

**Console Advertising:**
```javascript
// VULNERABLE: Active advertisement of vulnerability
console.log('Admin panel URL: ' + config.apiEndpoints.admin);
```

### Access Control Failures

**No Authentication:**
```php
<?php
// VULNERABLE: No authentication checks
require_once 'config.php';
// Direct access to admin functionality
```

**Unrestricted Operations:**
```php
// VULNERABLE: Direct database operations without permission checks
if (isset($_POST['delete_user'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
}
```

---

## 🛡️ Security Recommendations

### Immediate Fixes
1. **Remove admin URLs** from client-side code
2. **Implement authentication** for all admin interfaces
3. **Add role-based authorization** checks
4. **Clean up debug code** in production
5. **Implement audit logging**

### Defense in Depth
- **Environment-aware configuration** management
- **CSRF protection** for admin operations
- **IP-based restrictions** for admin interfaces
- **Multi-factor authentication** for admin accounts
- **Real-time security monitoring**

---

## 📚 Learning Resources

- **OWASP Top 10 - Broken Access Control:** https://owasp.org/Top10/A01_2021-Broken_Access_Control/
- **OWASP Top 10 - Security Misconfiguration:** https://owasp.org/Top10/A05_2021-Security_Misconfiguration/
- **CWE-200 - Information Exposure:** https://cwe.mitre.org/data/definitions/200.html
- **CWE-425 - Direct Request:** https://cwe.mitre.org/data/definitions/425.html

---

## ⚠️ Important Notes

> **🚨 Educational Use Only**
> 
> This lab contains intentional security vulnerabilities for educational purposes. 
> Never deploy this code in a production environment or on systems containing real data.

### Key Takeaways
- **Security through obscurity fails** when information is disclosed
- **Client-side code is always visible** to attackers
- **Admin interfaces require proper access controls** regardless of URL complexity
- **Defense in depth** provides better security than single-point controls

---

## 🤝 Lab Support

### Troubleshooting
- **Can't find admin URL?** Check browser developer tools and console messages
- **Admin panel returns 404?** Verify the exact filename: `admin-panel-x7k9p2m5q8w1.php`
- **JavaScript not working?** Ensure browser JavaScript is enabled and check console for errors

### Completion Checklist
✅ **Successfully discovered admin panel URL** through source code analysis  
✅ **Accessed the unprotected admin interface** without authentication  
✅ **Located Carlos Rodriguez** in the employee management system  
✅ **Successfully deleted the carlos account** using admin functions  
✅ **Verified deletion** by attempting to log in as carlos  

**Next Steps:** Study the secure code implementation to understand proper access control mechanisms and information security practices.

---

*Lab created for educational purposes • Part of the Access Control Vulnerability series*