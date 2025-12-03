# Lab 1: Unprotected Admin Functionality
## Access Control Vulnerabilities

![Security Level: Intentionally Vulnerable](https://img.shields.io/badge/Security-Intentionally%20Vulnerable-red)
![Lab Type: Access Control](https://img.shields.io/badge/Lab%20Type-Access%20Control-orange)
![Difficulty: Beginner](https://img.shields.io/badge/Difficulty-Beginner-green)

---

## 📋 Quick Start

1. **Ensure XAMPP is running** (Apache + MySQL)
2. **Access the lab:** `http://localhost/AC/lab1/`
3. **Check robots.txt:** `http://localhost/AC/lab1/robots.txt`
4. **Find the admin panel:** `http://localhost/AC/lab1/administrator-panel.php`
5. **Delete carlos** to complete the lab

---

## 🎯 Lab Objective

**Goal:** Delete the user "carlos" by accessing the unprotected admin panel.

**Learning Objective:** Understand how unprotected admin functionality can be exploited when proper access controls are not implemented.

---

## 🗂️ Lab Structure

```
AC/lab1/
├── 📄 index.php                 # Main application homepage
├── 🔐 login.php                 # User authentication page
├── 📤 logout.php                # Session termination
├── 👤 profile.php               # User profile page
├── 🚨 administrator-panel.php    # VULNERABLE: Unprotected admin panel
├── ⚙️ config.php                # Database configuration & initialization
├── 🤖 robots.txt                # Information disclosure (reveals admin path)
├── 🛒 products.php              # Product catalog page
├── ℹ️ about.php                 # About page
├── 📞 contact.php               # Contact information
├── 🗄️ database_setup.sql        # Manual database setup script
├── 📖 SETUP.md                  # Installation instructions
├── 📚 LAB_DOCUMENTATION.md       # Complete vulnerability analysis
└── 📄 README.md                 # This file
```

---

## 👥 Demo Accounts

| Username | Password  | Role  | Purpose |
|----------|-----------|-------|---------|
| `admin`  | admin123  | admin | Administrator account |
| `carlos` | carlos123 | user  | **🎯 TARGET for deletion** |
| `alice`  | alice123  | user  | Regular user account |
| `bob`    | bob123    | user  | Regular user account |
| `eve`    | eve123    | user  | Regular user account |

---

## 🔍 Vulnerability Details

### What's Wrong?
- ❌ **No authentication** required for admin panel access
- ❌ **No authorization** checks for administrative functions
- ❌ **Information disclosure** via robots.txt
- ❌ **Direct URL access** to sensitive functionality
- ❌ **Security through obscurity** approach

### Impact
- 🚨 **Complete admin access** without credentials
- 🚨 **User data manipulation** (view, delete accounts)
- 🚨 **System compromise** via administrative functions
- 🚨 **Data breach** potential

---

## 🚀 Lab Walkthrough

### Step 1: Information Gathering
```bash
# Check robots.txt for hidden paths
curl http://localhost/AC/lab1/robots.txt
```

### Step 2: Access Admin Panel
```bash
# Navigate directly to the disclosed admin path
http://localhost/AC/lab1/administrator-panel.php
```

### Step 3: Exploit the Vulnerability
1. Locate "carlos" in the user management table
2. Click the "Delete" button
3. Confirm the deletion
4. Verify carlos is removed from the system

---

## 🛠️ Technical Analysis

### Vulnerable Code Pattern
```php
<?php
// VULNERABLE: No security checks!
require_once 'config.php';

// Direct admin functionality access
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    // No authentication or authorization check
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
}
?>
```

### Information Disclosure
```
# robots.txt
User-agent: *
Disallow: /administrator-panel  ← Reveals admin path!
```

---

## 🔒 Security Recommendations

### Immediate Fixes
1. **Implement authentication checks**
2. **Add role-based authorization**
3. **Remove admin paths from robots.txt**
4. **Add CSRF protection**
5. **Implement audit logging**

### Defense in Depth
- Multi-factor authentication for admin accounts
- IP-based access restrictions
- Session timeout mechanisms
- Real-time monitoring and alerting

---

## 📚 Learning Resources

- **OWASP Top 10 - Broken Access Control:** https://owasp.org/Top10/A01_2021-Broken_Access_Control/
- **OWASP Testing Guide - Access Control Testing:** https://owasp.org/www-project-web-security-testing-guide/
- **CWE-306 - Missing Authentication:** https://cwe.mitre.org/data/definitions/306.html

---

## ⚠️ Important Notes

> **🚨 Educational Use Only**
> 
> This lab contains intentional security vulnerabilities for educational purposes. 
> Never deploy this code in a production environment or on systems containing real data.

### Responsible Disclosure
- Only test on systems you own or have explicit permission to test
- Respect scope and boundaries of security testing
- Follow responsible disclosure practices for real vulnerabilities

---

## 🤝 Lab Support

### Troubleshooting
- **Database not created?** Check MySQL service and visit the lab URL to trigger auto-creation
- **Access denied errors?** Verify Apache/PHP configuration and file permissions
- **Lab not working?** Review `SETUP.md` for detailed installation instructions

### Getting Help
- Review the comprehensive documentation in `LAB_DOCUMENTATION.md`
- Check setup instructions in `SETUP.md`
- Verify your XAMPP installation is properly configured

---

## 📝 Lab Completion

✅ **Successfully completed when:**
- [ ] Discovered the admin panel via robots.txt
- [ ] Accessed the unprotected admin interface
- [ ] Located the user "carlos" in the user management table
- [ ] Successfully deleted the carlos account
- [ ] Verified carlos can no longer log in

**Next Steps:** Review the secure code implementation and understand the defense mechanisms that prevent this vulnerability.

---

*Lab created for educational purposes • Part of the Access Control Vulnerability series*