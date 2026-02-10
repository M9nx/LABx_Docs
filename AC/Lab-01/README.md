<p align="center">
  <img src="https://img.shields.io/badge/Lab_01-Unprotected_Admin-FF4444?style=for-the-badge" alt="Lab 01">
  <img src="https://img.shields.io/badge/Difficulty-Apprentice-22C55E?style=for-the-badge" alt="Apprentice">
  <img src="https://img.shields.io/badge/Type-Access_Control-F97316?style=for-the-badge" alt="Access Control">
</p>

<h1 align="center">🔓 Unprotected Admin Functionality</h1>

<p align="center">
  <strong>Access Control Lab 01</strong><br>
  <em>Discover and exploit an unprotected administrative panel</em>
</p>

---

## 🎯 Objective

**Mission:** Delete the user `carlos` by finding and accessing the unprotected admin panel.

**Scenario:** SecureShop is an e-commerce platform that accidentally exposed their admin panel location in `robots.txt`. The admin panel has no authentication checks.

---

## 📋 Lab Information

| Property | Value |
|----------|-------|
| **Difficulty** | 🟢 Apprentice (Beginner) |
| **Category** | Access Control |
| **Vulnerability Type** | Unprotected Functionality |
| **OWASP Classification** | A01:2021 – Broken Access Control |
| **Time to Complete** | 5-10 minutes |
| **Prerequisites** | None |

---

## 🚀 Quick Start

```bash
# 1. Access the lab
http://localhost/LABx_Docs/AC/Lab-01/

# 2. Check robots.txt
http://localhost/LABx_Docs/AC/Lab-01/robots.txt

# 3. Access the admin panel
http://localhost/LABx_Docs/AC/Lab-01/administrator-panel.php

# 4. Delete carlos to complete the lab
```

---

## 🗂️ Lab Structure

```
Lab-01/
├── 📄 index.php                  # SecureShop homepage
├── 📄 login.php                  # User authentication
├── 📄 logout.php                 # Session termination
├── 📄 profile.php                # User profile page
├── 📄 products.php               # Product catalog
├── 📄 about.php                  # About page
├── 📄 contact.php                # Contact information
├── 🔴 administrator-panel.php    # VULNERABLE: Unprotected admin
├── 🤖 robots.txt                 # Information disclosure
├── ⚙️ config.php                 # Database configuration
├── 🗄️ setup_db.php               # Database initialization
├── 📊 database_setup.sql         # SQL schema
├── ✅ success.php                # Lab completion verification
├── 📄 docs.php                   # Technical documentation
├── 📄 lab-description.php        # Challenge description
└── 📄 README.md                  # This file
```

---

## 👥 Test Credentials

| Username | Password | Role | Notes |
|----------|----------|------|-------|
| `admin` | `admin123` | Administrator | Full access |
| `carlos` | `carlos123` | User | 🎯 **TARGET - Delete this user** |
| `alice` | `alice123` | User | Regular user |
| `bob` | `bob123` | User | Regular user |
| `eve` | `eve123` | User | Regular user |

---

## 🔍 Vulnerability Analysis

### The Flaw

The application commits several critical access control failures:

1. **No Authentication on Admin Panel**
   ```php
   // administrator-panel.php has NO authentication check
   // Anyone can access it directly via URL
   ```

2. **Information Disclosure via robots.txt**
   ```
   User-agent: *
   Disallow: /administrator-panel.php
   ```

3. **Security Through Obscurity**
   - Relying only on URL "obscurity" for protection
   - No session validation
   - No role-based access control

### Impact Assessment

| Impact Area | Severity | Description |
|-------------|----------|-------------|
| Confidentiality | 🔴 Critical | View all user data |
| Integrity | 🔴 Critical | Modify/delete any user |
| Availability | 🟡 High | Delete all accounts |
| Business | 🔴 Critical | Complete system takeover |

---

## 💀 Exploitation Guide

### Method 1: robots.txt Discovery

```bash
# Step 1: Check robots.txt for disallowed paths
curl http://localhost/LABx_Docs/AC/Lab-01/robots.txt

# Output reveals:
# Disallow: /administrator-panel.php

# Step 2: Navigate directly to the admin panel
http://localhost/LABx_Docs/AC/Lab-01/administrator-panel.php

# Step 3: Delete carlos from the user management table
```

### Method 2: Common Path Guessing

```bash
# Try common admin paths
/admin
/admin.php
/administrator
/administrator-panel.php  ← This one works!
/wp-admin
/controlpanel
```

### Method 3: Directory Bruteforce

```bash
# Using gobuster
gobuster dir -u http://localhost/LABx_Docs/AC/Lab-01/ -w common.txt

# Using ffuf  
ffuf -u http://localhost/LABx_Docs/AC/Lab-01/FUZZ -w common.txt
```

---

## 🛡️ Prevention & Mitigation

### Secure Implementation

```php
<?php
// 1. ALWAYS check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. ALWAYS check authorization
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Access Denied: Admin privileges required');
}

// 3. Only then show admin functionality
?>
```

### Security Checklist

- ✅ Require authentication on ALL sensitive pages
- ✅ Implement role-based access control (RBAC)
- ✅ Don't list sensitive paths in robots.txt
- ✅ Use unpredictable URLs as ADDITIONAL layer only
- ✅ Log all admin actions
- ✅ Implement session timeout
- ✅ Add CSRF protection

### Proper robots.txt

```txt
# Don't list sensitive endpoints!
# Use authentication instead

User-agent: *
Disallow: /private/
Sitemap: https://example.com/sitemap.xml
```

---

## 📚 Key Learning Points

### What This Lab Teaches

1. **Never rely on obscurity for security**
   - Hidden URLs will be discovered
   - robots.txt is publicly readable
   - Source code can leak paths

2. **Every endpoint needs protection**
   - Authentication: Is the user logged in?
   - Authorization: Does the user have permission?
   - Both checks are required

3. **Defense in Depth**
   - Multiple layers of security
   - Fail securely
   - Assume breach will happen

### Real-World Examples

- **2017:** Equifax breach - Unpatched admin panel
- **2019:** First American Financial - Direct URL to documents
- **2020:** Multiple SaaS platforms with exposed /admin paths

---

## 🔗 Related Resources

| Resource | Link |
|----------|------|
| OWASP Access Control | [owasp.org/Top10/A01_2021](https://owasp.org/Top10/A01_2021-Broken_Access_Control/) |
| PortSwigger Lab | [portswigger.net/web-security/access-control](https://portswigger.net/web-security/access-control) |
| CWE-425 | [cwe.mitre.org/data/definitions/425](https://cwe.mitre.org/data/definitions/425.html) |

---

## ✅ Completion Checklist

- [ ] Discovered robots.txt
- [ ] Found administrator-panel.php path
- [ ] Accessed admin panel without authentication
- [ ] Located carlos in user list
- [ ] Deleted carlos successfully
- [ ] Reached success.php

---

<p align="center">
  <strong>Lab 01 of 30</strong> • Access Control Series<br>
  <a href="../Lab-02/">Next Lab: Unpredictable Admin URL →</a>
</p>
