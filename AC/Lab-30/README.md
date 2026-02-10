<p align="center">
  <img src="https://img.shields.io/badge/Lab_30-IDOR_Settings-7C3AED?style=for-the-badge" alt="Lab 30">
  <img src="https://img.shields.io/badge/Difficulty-Medium-F59E0B?style=for-the-badge" alt="Medium">
  <img src="https://img.shields.io/badge/Type-Access_Control-F97316?style=for-the-badge" alt="Access Control">
</p>

<h1 align="center">📦 IDOR in Inventory Settings - Stocky</h1>

<p align="center">
  <strong>Access Control Lab 30</strong><br>
  <em>Exploit Insecure Direct Object Reference to access other users' settings</em>
</p>

---

## 🎯 Objective

**Mission:** Exploit the IDOR vulnerability in the settings management system to access or modify another user's column display settings and capture the flag.

**Scenario:** Stocky is an inventory management application for e-commerce stores. Each store owner has custom column settings that control which data columns are displayed in their inventory views. The application has an IDOR vulnerability in its settings management system.

---

## 📋 Lab Information

| Property | Value |
|----------|-------|
| **Difficulty** | 🟡 Medium |
| **Category** | Broken Access Control |
| **Vulnerability Type** | Insecure Direct Object Reference (IDOR) |
| **OWASP Classification** | A01:2021 – Broken Access Control |
| **Time to Complete** | 15-20 minutes |
| **Attack Vectors** | 2 (Direct Modification + Import) |

---

## 🚀 Quick Start

```bash
# 1. Access the lab
http://localhost/LABx_Docs/AC/Lab-30/

# 2. Login with test account
Username: alice_shop
Password: password123

# 3. Navigate to Settings page
# 4. Manipulate settings_id or import_from_id parameters
# 5. Capture the flag!
```

---

## 🗂️ Lab Structure

```
Lab-30/
├── 📄 index.php               # Stocky landing page
├── 📄 login.php               # User authentication
├── 📄 logout.php              # Session termination
├── 📊 dashboard.php           # Main inventory dashboard
├── 📉 low-stock.php           # Low stock alerts
├── 📋 activity.php            # Activity log
├── 🔴 settings.php            # VULNERABLE: IDOR in settings
├── ⚙️ config.php              # Database configuration
├── 🗄️ setup_db.php            # Database initialization
├── 📊 database_setup.sql      # SQL schema
├── 📄 docs.php                # Attack documentation
├── 📄 docs-technical.php      # Technical details
├── 📄 docs-mitigation.php     # Prevention guide
├── 📄 lab-description.php     # Challenge description
├── ✅ success.php             # Flag submission
└── 📄 README.md               # This file
```

---

## 👥 Test Credentials

| Username | Password | Settings ID | Store Name |
|----------|----------|-------------|------------|
| `alice_shop` | `password123` | 1 | Alice's Electronics |
| `bob_store` | `password123` | 2 | Bob's Gadgets |
| `carol_mart` | `password123` | 3 | Carol's Mart |
| `david_outlet` | `password123` | 4 | David's Outlet |

> **💡 Tip:** Each user has a unique `settings_id`. Access another user's settings to verify the IDOR vulnerability.

---

## 🔍 Vulnerability Analysis

### The Flaw

The application has two IDOR attack vectors in `settings.php`:

**1. Direct Modification Attack**
```php
// VULNERABLE CODE - No ownership verification
$settings_id = $_POST['settings_id'];
$stmt = $pdo->prepare("UPDATE display_settings SET 
    show_sku = ?, show_price = ?, show_stock = ? 
    WHERE id = ?");
$stmt->execute([$show_sku, $show_price, $show_stock, $settings_id]);
// Missing: Check if $settings_id belongs to current user!
```

**2. Import Settings Attack**
```php
// VULNERABLE CODE - No authorization check
$import_from_id = $_POST['import_from_id'];
$stmt = $pdo->prepare("SELECT * FROM display_settings WHERE id = ?");
$stmt->execute([$import_from_id]);
$settings = $stmt->fetch();
// Missing: Check if user can access import_from_id!
```

### Impact Assessment

| Impact Area | Severity | Description |
|-------------|----------|-------------|
| Confidentiality | 🟡 Medium | View other users' display preferences |
| Integrity | 🟡 Medium | Modify any user's settings |
| Availability | 🟢 Low | Limited denial of service potential |
| Business | 🟡 Medium | Horizontal privilege escalation |

---

## 💀 Exploitation Guide

### Method 1: Direct Settings ID Manipulation

```http
# Original request (own settings)
POST /LABx_Docs/AC/Lab-30/settings.php HTTP/1.1

settings_id=1&show_sku=1&show_price=1&show_stock=1

# Attack: Change settings_id to another user
settings_id=2&show_sku=1&show_price=1&show_stock=1
```

**Steps:**
1. Login as `alice_shop` (settings_id = 1)
2. Navigate to Settings page
3. Open browser DevTools (F12)
4. Find the hidden `settings_id` input field
5. Change value from `1` to `2` (Bob's settings)
6. Submit the form
7. 🎉 Flag is revealed!

### Method 2: Import Settings Attack

```http
# Import from another user's settings
POST /LABx_Docs/AC/Lab-30/settings.php HTTP/1.1

action=import&import_from_id=3
```

**Steps:**
1. Login as any user
2. Go to Settings page
3. Find the "Import Settings" feature
4. Intercept the request with Burp Suite
5. Change `import_from_id` to another user's ID
6. Forward the request
7. 🎉 Flag is revealed!

### Using Burp Suite

```
1. Configure browser to use Burp proxy (127.0.0.1:8080)
2. Login to the application
3. Navigate to Settings
4. Enable interception in Burp
5. Submit the settings form
6. Modify settings_id parameter
7. Forward the request
8. Check response for the flag
```

---

## 🛡️ Prevention & Mitigation

### Secure Implementation

```php
<?php
// SECURE: Always verify ownership
session_start();

// Get settings_id from user's session, NOT from user input
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM display_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch();
$settings_id = $settings['id'];

// Now safely update only this user's settings
$stmt = $pdo->prepare("UPDATE display_settings SET 
    show_sku = ?, show_price = ?, show_stock = ? 
    WHERE id = ? AND user_id = ?");
$stmt->execute([$show_sku, $show_price, $show_stock, $settings_id, $user_id]);
```

### Secure Import with Authorization

```php
<?php
// SECURE: Verify user can access the source settings
// Option 1: Only allow importing from own settings
if ($import_from_id != $user_settings_id) {
    die("You can only import your own saved presets");
}

// Option 2: Check if settings are marked as "public/shared"
$stmt = $pdo->prepare("SELECT * FROM display_settings 
    WHERE id = ? AND (user_id = ? OR is_public = 1)");
$stmt->execute([$import_from_id, $user_id]);
```

### Security Checklist

- ✅ Never trust user-supplied object IDs
- ✅ Always verify object ownership server-side
- ✅ Use indirect references (UUIDs or mappings)
- ✅ Store object ownership in session, not hidden fields
- ✅ Implement proper access control checks
- ✅ Log all access attempts for audit
- ✅ Use parameterized queries (prevent SQLi too)

---

## 📚 Key Learning Points

### What This Lab Teaches

1. **IDOR Vulnerability Pattern**
   - User-controlled object references
   - Missing ownership verification
   - Horizontal privilege escalation

2. **Two Attack Vectors**
   - Direct ID manipulation in forms
   - Import/Copy features as secondary targets

3. **Defense Strategies**
   - Server-side ownership checks
   - Session-based resource binding
   - Indirect object references

### Real-World IDOR Examples

| Year | Company | Impact |
|------|---------|--------|
| 2019 | First American | 885M records exposed |
| 2020 | Parler | All user data scraped |
| 2021 | Peloton | User account data exposed |
| 2022 | T-Mobile | Customer data leak |

---

## 🔗 Related Resources

| Resource | Link |
|----------|------|
| OWASP IDOR | [owasp.org/www-project-web-security-testing-guide](https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References) |
| CWE-639 | [cwe.mitre.org/data/definitions/639](https://cwe.mitre.org/data/definitions/639.html) |
| PortSwigger Academy | [portswigger.net/web-security/access-control/idor](https://portswigger.net/web-security/access-control/idor) |

---

## ✅ Completion Checklist

- [ ] Logged in with test credentials
- [ ] Navigated to Settings page
- [ ] Identified own settings_id
- [ ] Exploited IDOR via Method 1 (Direct) or Method 2 (Import)
- [ ] Captured the flag
- [ ] Submitted flag on success.php
- [ ] Understood the secure fix

---

<p align="center">
  <strong>Lab 30 of 30</strong> • Access Control Series<br>
  <a href="../Lab-29/">← Previous Lab</a> • <a href="../../">🏠 Home</a> • <strong>🎉 Series Complete!</strong>
</p>
