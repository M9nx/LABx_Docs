# LABx_Docs

**Enterprise-Grade Web Application Security Training Platform**

[![PHP 8.0+](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL 5.7+](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Labs](https://img.shields.io/badge/Labs-40+-22C55E?style=flat-square)](https://github.com/M9nx/LABx_Docs)
[![OWASP](https://img.shields.io/badge/OWASP-Top_10-000000?style=flat-square)](https://owasp.org/Top10/)

Self-hosted vulnerable web application platform with 40+ exploitable labs mapped to OWASP Top 10, CWE/SANS Top 25, and real-world HackerOne disclosures. Designed for penetration testers, security researchers, and developers building secure applications.

---

## Table of Contents

- [Quick Start](#quick-start)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Platform Architecture](#platform-architecture)
- [Vulnerability Categories](#vulnerability-categories)
- [Lab Catalog](#lab-catalog)
- [Progress Tracking System](#progress-tracking-system)
- [Database Configuration API](#database-configuration-api)
- [Exploitation Methodology](#exploitation-methodology)
- [Attack Surface Mapping](#attack-surface-mapping)
- [Toolchain Integration](#toolchain-integration)
- [Development Guide](#development-guide)
- [Troubleshooting](#troubleshooting)
- [Security Considerations](#security-considerations)
- [References](#references)

---

## Quick Start

```bash
# 1. Clone
git clone https://github.com/M9nx/LABx_Docs.git
cd LABx_Docs

# 2. Deploy to web root
# Windows: move to C:\xampp\htdocs\
# Linux: sudo mv to /var/www/html/

# 3. Start services (Apache + MySQL)

# 4. Access http://localhost/LABx_Docs/

# 5. Configure database credentials via UI

# 6. Initialize databases: Sidebar > Setup Databases > Setup All

# 7. Begin exploitation
```

---

## System Requirements

### Minimum Specifications

| Component | Requirement | Verification |
|-----------|-------------|--------------|
| PHP | 8.0.0+ | `php -v` |
| MySQL | 5.7.0+ | `mysql --version` |
| Apache | 2.4.0+ | `httpd -v` or `apache2 -v` |
| Memory | 512MB RAM | - |
| Storage | 100MB | - |

### Required PHP Extensions

```bash
# Verify extensions
php -m | grep -E "mysqli|json|session|mbstring"

# Required
ext-mysqli     # MySQL database connectivity
ext-json       # JSON encode/decode operations
ext-session    # Session management
ext-mbstring   # Multibyte string handling (recommended)
```

### PHP Configuration

```ini
; php.ini recommended settings
session.cookie_httponly = 1
session.use_strict_mode = 1
display_errors = On          ; For lab debugging
error_reporting = E_ALL
```

### Tested Environments

| Environment | Version | Status |
|-------------|---------|--------|
| XAMPP Windows | 8.2.12 | Verified |
| XAMPP Linux | 8.2.4 | Verified |
| WAMP | 3.3.0 | Verified |
| MAMP | 6.8 | Verified |
| Docker php:8.2-apache | Latest | Verified |
| Ubuntu 22.04 + LAMP | Native | Verified |

---

## Installation

### Method 1: Git Clone (Recommended)

```bash
git clone https://github.com/M9nx/LABx_Docs.git
```

### Method 2: Direct Download

```bash
curl -L https://github.com/M9nx/LABx_Docs/archive/main.zip -o LABx_Docs.zip
unzip LABx_Docs.zip
mv LABx_Docs-main LABx_Docs
```

### Method 3: Docker Compose

```yaml
# docker-compose.yml
version: '3.8'
services:
  web:
    image: php:8.2-apache
    ports:
      - "8080:80"
    volumes:
      - ./LABx_Docs:/var/www/html/LABx_Docs
    depends_on:
      - db
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_ALLOW_EMPTY_PASSWORD: "yes"
    ports:
      - "3306:3306"
```

```bash
docker-compose up -d
# Access: http://localhost:8080/LABx_Docs/
# DB Host: db (Docker service name)
```

### Post-Installation Configuration

1. **Verify file permissions**
   ```bash
   # Linux/macOS
   chmod -R 755 LABx_Docs/
   chown -R www-data:www-data LABx_Docs/
   ```

2. **Configure database credentials**
   - Navigate to `http://localhost/LABx_Docs/`
   - Enter MySQL host, username, password
   - Click "Test & Save"

3. **Initialize lab databases**
   - Sidebar > Setup Databases
   - Click "Setup All" or initialize individually

4. **Verify installation**
   - Access any lab (e.g., AC/Lab-01)
   - Complete the lab
   - Verify progress tracking

---

## Platform Architecture

### Directory Structure

```
LABx_Docs/
│
├── index.php                     # Main dashboard, DB configuration UI
├── db-config.php                 # Centralized credential management API
├── index.md                      # Markdown index
├── README.md                     # This documentation
│
├── src/                          # Shared platform components
│   ├── sidebar.php               # Global navigation sidebar
│   ├── sidebar.css               # Sidebar stylesheet
│   ├── setup.php                 # Database initialization wizard
│   └── progress.php              # Cross-category progress aggregator
│
├── AC/                           # Access Control category
│   ├── index.php                 # Category dashboard
│   ├── progress.php              # AC progress helper (ac_progress DB)
│   ├── PROGRESS_TRACKING.md      # Progress system documentation
│   ├── setup-all-databases.php   # Batch database setup
│   └── Lab-01/ ... Lab-30/       # 30 individual lab directories
│
├── Insecure-Deserialization/     # Deserialization category
│   ├── index.php                 # Category dashboard
│   ├── progress.php              # ID progress helper (id_progress DB)
│   ├── PROGRESS_TRACKING.md
│   └── Lab-01/ ... Lab-10/       # 10 individual lab directories
│
├── API/                          # API Security category (planned)
│   ├── index.php
│   ├── progress.php              # api_progress DB
│   └── PROGRESS_TRACKING.md
│
└── Authentication/               # Authentication category (planned)
    ├── index.php
    ├── progress.php              # auth_progress DB
    └── PROGRESS_TRACKING.md
```

### Lab Directory Structure

Each lab follows a standardized structure:

```
Lab-XX/
│
├── index.php                 # Lab entry point, scenario UI
├── lab-description.php       # Challenge objectives, attack hints
├── docs.php                  # Technical documentation, walkthrough
│
├── config.php                # Database connection configuration
├── setup_db.php              # Database initialization script
├── database_setup.sql        # SQL schema, seed data, test accounts
│
├── login.php                 # Authentication endpoint (if applicable)
├── logout.php                # Session termination
├── [vulnerable-endpoint].php # Exploitable functionality
│
├── success.php               # Completion verification, flag display
├── LAB_DOCUMENTATION.md      # Comprehensive markdown documentation
└── README.md                 # Lab-specific README
```

### Component Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                         index.php                                │
│                    (Main Dashboard + DB Config)                  │
└───────────────────────────┬─────────────────────────────────────┘
                            │
              ┌─────────────┴─────────────┐
              ▼                           ▼
┌─────────────────────────┐   ┌─────────────────────────┐
│      db-config.php      │   │     src/sidebar.php     │
│  (Credential Storage)   │   │  (Global Navigation)    │
└─────────────────────────┘   └─────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Category Dashboards                           │
│         AC/index.php | ID/index.php | API/index.php             │
└───────────────────────────┬─────────────────────────────────────┘
                            │
              ┌─────────────┴─────────────┐
              ▼                           ▼
┌─────────────────────────┐   ┌─────────────────────────┐
│   Lab-XX/index.php      │   │   [category]/progress.php│
│   (Lab Scenario)        │   │   (Progress Tracking)   │
└───────────────────────────┘   └─────────────────────────┘
              │
              ▼
┌─────────────────────────┐
│   Lab-XX/success.php    │
│   (Completion Handler)  │
└─────────────────────────┘
```

---

## Vulnerability Categories

### Coverage Matrix

| Category | Labs | OWASP 2021 | CWE ID | Difficulty Range |
|----------|:----:|------------|--------|------------------|
| Access Control | 30 | A01:2021 | CWE-284, CWE-639, CWE-862 | Apprentice - Expert |
| Insecure Deserialization | 10 | A08:2021 | CWE-502 | Apprentice - Expert |
| API Security | TBD | A01, A02, A03 | CWE-284, CWE-287, CWE-200 | Planned |
| Authentication | TBD | A07:2021 | CWE-287, CWE-307, CWE-384 | Planned |

### OWASP Mapping

| OWASP 2021 | Category | Labs |
|------------|----------|:----:|
| A01: Broken Access Control | Access Control | 30 |
| A02: Cryptographic Failures | - | - |
| A03: Injection | - | - |
| A04: Insecure Design | - | - |
| A05: Security Misconfiguration | - | - |
| A06: Vulnerable Components | - | - |
| A07: Auth Failures | Authentication | TBD |
| A08: Data Integrity Failures | Insecure Deserialization | 10 |
| A09: Logging Failures | - | - |
| A10: SSRF | - | - |

---

## Lab Catalog

### Access Control (30 Labs)

| # | Title | Difficulty | CWE | Attack Vector |
|:-:|-------|:----------:|:---:|---------------|
| 01 | Unprotected Admin Functionality | Apprentice | CWE-425 | robots.txt disclosure |
| 02 | Unpredictable Admin URL | Apprentice | CWE-425 | JavaScript source analysis |
| 03 | User Role Manipulation | Apprentice | CWE-639 | Cookie parameter tampering |
| 04 | IDOR Account Takeover | Practitioner | CWE-639 | Direct object reference |
| 05 | User ID via Request Parameter | Practitioner | CWE-639 | GET/POST parameter manipulation |
| 06 | Unpredictable User IDs | Practitioner | CWE-639 | GUID enumeration |
| 07 | Data Leakage in Redirect | Practitioner | CWE-639 | Response body before redirect |
| 08 | Password Disclosure | Practitioner | CWE-639 | Source code exposure |
| 09 | Classic IDOR | Practitioner | CWE-639 | Sequential ID enumeration |
| 10 | URL-Based Access Control Bypass | Practitioner | CWE-284 | X-Original-URL header injection |
| 11 | Method-Based Access Control | Practitioner | CWE-284 | HTTP method override |
| 12 | Multi-Step Process Bypass | Practitioner | CWE-284 | Workflow state manipulation |
| 13 | Referer-Based Access Control | Practitioner | CWE-284 | Referer header spoofing |
| 14 | Mass Assignment IDOR | Practitioner | CWE-915 | Object property injection |
| 15 | Email Change IDOR | Practitioner | CWE-639 | Account takeover via email |
| 16 | Sequential ID Enumeration | Practitioner | CWE-639 | Predictable identifier |
| 17 | Horizontal Privilege Escalation | Practitioner | CWE-639 | Cross-user data access |
| 18 | Parameter Pollution IDOR | Practitioner | CWE-639 | HTTP parameter pollution |
| 19 | API Endpoint IDOR | Practitioner | CWE-639 | REST API object reference |
| 20 | Encoded/Hashed ID IDOR | Practitioner | CWE-639 | Base64/hash decoding |
| 21 | JWT Token Manipulation | Practitioner | CWE-639 | JWT claim modification |
| 22 | Indirect Object Reference | Practitioner | CWE-639 | Indirect reference map bypass |
| 23 | Role Parameter Escalation | Practitioner | CWE-269 | Role field injection |
| 24 | Vertical Privilege Escalation | Practitioner | CWE-269 | Admin function access |
| 25 | File Upload Access Control | Practitioner | CWE-639 | File reference manipulation |
| 26 | Path Traversal Bypass | Practitioner | CWE-22 | Directory traversal + IDOR |
| 27 | HackerOne: PII Disclosure | Practitioner | CWE-639 | Real-world case study |
| 28 | HackerOne: Account Deletion | Practitioner | CWE-639 | Real-world case study |
| 29 | HackerOne: Mass Assignment | Practitioner | CWE-915 | Real-world case study |
| 30 | GraphQL Mutation IDOR | Expert | CWE-639 | GraphQL parameter manipulation |

**Difficulty Distribution:** Apprentice (3) | Practitioner (26) | Expert (1)

---

### Insecure Deserialization (10 Labs)

| # | Title | Difficulty | CWE | Attack Vector |
|:-:|-------|:----------:|:---:|---------------|
| 01 | Modifying Serialized Objects | Apprentice | CWE-502 | Cookie base64 tampering |
| 02 | Serialized Data Types | Apprentice | CWE-502 | PHP type juggling |
| 03 | Application Functionality Abuse | Practitioner | CWE-502 | Serialized method invocation |
| 04 | Arbitrary Object Injection | Practitioner | CWE-502 | Magic method exploitation |
| 05 | Pre-Built Gadget Chain (PHP) | Practitioner | CWE-502 | Known gadget chain |
| 06 | Documented Gadget Chain (Ruby) | Practitioner | CWE-502 | Framework gadget chain |
| 07 | Custom Gadget Chain (PHP) | Expert | CWE-502 | Custom gadget construction |
| 08 | Custom Gadget Chain (Java) | Expert | CWE-502 | Java deserialization |
| 09 | PHAR Deserialization | Expert | CWE-502 | PHAR polyglot upload |
| 10 | Cookie-Based Deserialization | Apprentice | CWE-502 | Session cookie tampering |

**Difficulty Distribution:** Apprentice (3) | Practitioner (4) | Expert (3)

---

## Progress Tracking System

### Database Schema

Each category maintains an isolated progress database:

| Category | Database Name | Connection |
|----------|---------------|------------|
| Access Control | `ac_progress` | AC/progress.php |
| Insecure Deserialization | `id_progress` | Insecure-Deserialization/progress.php |
| API Security | `api_progress` | API/progress.php |
| Authentication | `auth_progress` | Authentication/progress.php |

### Table Structure

```sql
CREATE DATABASE IF NOT EXISTS ac_progress;
USE ac_progress;

CREATE TABLE IF NOT EXISTS solved_labs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lab_number INT NOT NULL UNIQUE,
    solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lab_number (lab_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Progress Helper API

```php
<?php
// Located at: [Category]/progress.php

/**
 * Check if a specific lab is solved
 * @param int $labNumber Lab number (1-30 for AC, 1-10 for ID)
 * @return bool True if solved, false otherwise
 */
function isLabSolved(int $labNumber): bool;

/**
 * Mark a lab as solved (idempotent)
 * @param int $labNumber Lab number to mark
 * @return bool True on success
 */
function markLabSolved(int $labNumber): bool;

/**
 * Get all solved lab numbers
 * @return array Array of solved lab numbers
 */
function getSolvedLabs(): array;

/**
 * Get total count of solved labs
 * @return int Number of solved labs
 */
function getSolvedCount(): int;

/**
 * Reset a lab's solved status
 * @param int $labNumber Lab to reset
 * @return bool True on success
 */
function resetLab(int $labNumber): bool;
```

### Integration Example

```php
<?php
// Lab success.php implementation
require_once('../progress.php');

// Verify completion condition (lab-specific logic)
$isComplete = verifyLabCompletion();

if ($isComplete) {
    markLabSolved(5);  // Mark Lab 05 as complete
    
    // Display success message
    echo "Lab completed!";
    echo "Flag: FLAG{lab_05_solved}";
}

// Check if already solved
if (isLabSolved(5)) {
    echo "You have already completed this lab.";
}
```

---

## Database Configuration API

### Endpoint

`POST /LABx_Docs/db-config.php`

### Actions

#### Test Connection

```http
POST /LABx_Docs/db-config.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

action=test&host=localhost&user=root&pass=
```

**Response:**
```json
{
  "success": true,
  "message": "Connection successful"
}
```

#### Get Status

```http
POST /LABx_Docs/db-config.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

action=status
```

**Response:**
```json
{
  "configured": true,
  "host": "localhost",
  "user": "root"
}
```

#### Clear Credentials

```http
POST /LABx_Docs/db-config.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded

action=clear
```

**Response:**
```json
{
  "success": true,
  "message": "Credentials cleared"
}
```

### PHP Integration

```php
<?php
require_once '/path/to/LABx_Docs/db-config.php';

// Retrieve stored credentials
$credentials = getDbCredentials();
// Returns: ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'configured' => true]

// Use credentials for database connection
$conn = new mysqli(
    $credentials['host'],
    $credentials['user'],
    $credentials['pass'],
    'lab_database'
);
```

---

## Exploitation Methodology

### Phase 1: Reconnaissance

```
1. Read lab-description.php for objectives and hints
2. Explore application functionality
3. Identify entry points (forms, parameters, cookies, headers)
4. Analyze client-side source (JavaScript, HTML comments)
5. Check for information disclosure (robots.txt, .git, backups)
```

### Phase 2: Vulnerability Identification

```
1. Map input vectors to potential vulnerability classes
2. Test for common flaws:
   - IDOR: Modify ID parameters
   - Access Control: Try unauthorized endpoints
   - Deserialization: Analyze cookie structures
3. Use Burp Suite for request/response analysis
4. Check server responses for sensitive data leakage
```

### Phase 3: Exploitation

```
1. Develop proof-of-concept exploit
2. Achieve lab objective:
   - Delete target user
   - Access unauthorized data
   - Escalate privileges
   - Retrieve flag
3. Navigate to success.php for verification
```

### Phase 4: Documentation

```
1. Document exploitation steps
2. Identify root cause
3. Propose remediation
4. Review docs.php for official walkthrough
```

---

## Attack Surface Mapping

### Common Entry Points

| Entry Point | Attack Vectors | Tools |
|-------------|----------------|-------|
| URL Parameters | IDOR, SQLi, XSS, Path Traversal | Burp Repeater, curl |
| POST Body | Mass Assignment, IDOR, CSRF | Burp Repeater, Postman |
| Cookies | Deserialization, Session Hijacking | Browser DevTools, Burp |
| HTTP Headers | X-Original-URL, X-Forwarded-For, Referer | curl, Burp Intruder |
| File Uploads | Path Traversal, RCE, IDOR | Burp, custom scripts |
| GraphQL | IDOR via mutations, Introspection | GraphQL Playground, Altair |

### Vulnerability-Specific Payloads

#### IDOR Testing

```bash
# Enumerate sequential IDs
for i in {1..100}; do
  curl -s "http://localhost/Lab-05/profile.php?id=$i" | grep -q "secret" && echo "Found: $i"
done

# Test encoded IDs
echo -n "2" | base64
# Mg==
curl "http://localhost/Lab-20/profile.php?id=Mg=="
```

#### Deserialization Testing

```php
// Generate malicious serialized object
$payload = 'O:8:"stdClass":2:{s:8:"username";s:6:"wiener";s:5:"admin";b:1;}';
$encoded = base64_encode($payload);
echo $encoded;
// Tzo4OiJzdGRDbGFzcyI6Mjp7czo4OiJ1c2VybmFtZSI7czo2OiJ3aWVuZXIiO3M6NToiYWRtaW4iO2I6MTt9
```

#### Access Control Bypass

```bash
# X-Original-URL bypass
curl -H "X-Original-URL: /admin" http://localhost/Lab-10/

# HTTP method override
curl -X POST -d "_method=DELETE" http://localhost/Lab-11/user/1

# Referer manipulation
curl -H "Referer: http://localhost/admin" http://localhost/Lab-13/admin-action
```

---

## Toolchain Integration

### Burp Suite Configuration

```
1. Configure browser proxy: 127.0.0.1:8080
2. Add LABx_Docs to scope: http://localhost/LABx_Docs/*
3. Disable interception for static files (*.css, *.js, *.png)
4. Use Repeater for parameter manipulation
5. Use Intruder for ID enumeration
```

### Browser DevTools

```
Application Tab:
- Cookies: View/modify session cookies
- Local Storage: Check for sensitive data

Network Tab:
- Monitor requests/responses
- Replay requests with modifications

Console:
- Execute JavaScript for dynamic analysis
- Decode base64: atob("encodedstring")
```

### Command-Line Tools

```bash
# curl for quick testing
curl -c cookies.txt -b cookies.txt \
     -X POST -d "username=wiener&password=peter" \
     http://localhost/LABx_Docs/AC/Lab-01/login.php

# httpie for readable output
http POST localhost/LABx_Docs/AC/Lab-05/profile.php id=2 Cookie:session=abc123

# jq for JSON parsing
curl -s http://localhost/api/user/1 | jq '.data.email'
```

### Automation Scripts

```python
#!/usr/bin/env python3
"""IDOR enumeration script"""
import requests

session = requests.Session()
session.post('http://localhost/LABx_Docs/AC/Lab-05/login.php', 
             data={'username': 'wiener', 'password': 'peter'})

for user_id in range(1, 100):
    resp = session.get(f'http://localhost/LABx_Docs/AC/Lab-05/profile.php?id={user_id}')
    if 'admin' in resp.text.lower():
        print(f'[+] Admin found: ID {user_id}')
        print(resp.text)
        break
```

---

## Development Guide

### Adding New Labs

#### 1. Create Directory Structure

```bash
mkdir -p AC/Lab-31
cd AC/Lab-31
```

#### 2. Required Files

```bash
touch index.php            # Entry point
touch lab-description.php  # Challenge description
touch docs.php             # Technical walkthrough
touch config.php           # Database config
touch setup_db.php         # DB initialization
touch database_setup.sql   # SQL schema
touch login.php            # Auth (if needed)
touch logout.php           # Session cleanup
touch success.php          # Completion handler
touch README.md            # Lab documentation
```

#### 3. Config Template

```php
<?php
// config.php
require_once('../../db-config.php');
$creds = getDbCredentials();

$host = $creds['host'];
$user = $creds['user'];
$pass = $creds['pass'];
$dbname = 'ac_lab31';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

#### 4. Success Handler Template

```php
<?php
// success.php
require_once('../progress.php');

session_start();

// Lab-specific completion logic
$completed = isset($_GET['deleted']) && $_GET['deleted'] === 'carlos';

if ($completed) {
    markLabSolved(31);
}
?>
<!DOCTYPE html>
<html>
<head><title>Lab 31 - Success</title></head>
<body>
    <?php if ($completed): ?>
        <h1>Congratulations!</h1>
        <p>You have successfully completed Lab 31.</p>
        <code>FLAG{ac_lab31_completed}</code>
    <?php else: ?>
        <h1>Not Complete</h1>
        <p>Complete the lab objective to see the flag.</p>
    <?php endif; ?>
</body>
</html>
```

#### 5. Register in Setup

Update `src/setup.php` to include the new lab in the setup wizard.

---

## Troubleshooting

### Common Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| "Connection failed" | Invalid credentials | Verify MySQL host/user/pass on dashboard |
| "Database not found" | Lab not initialized | Run Setup Databases from sidebar |
| PHP errors displayed | Debug mode enabled | Expected in development environment |
| Blank page | PHP fatal error | Check Apache error log |
| Session lost | Cookie issues | Clear browser cookies, restart |

### Log Locations

```bash
# XAMPP Windows
C:\xampp\apache\logs\error.log
C:\xampp\mysql\data\*.err

# Linux Apache
/var/log/apache2/error.log
/var/log/mysql/error.log

# PHP
# Check phpinfo() for error_log path
```

### Debug Commands

```bash
# Test MySQL connection
mysql -h localhost -u root -p -e "SHOW DATABASES;"

# Test PHP
php -r "phpinfo();" | grep mysqli

# Check Apache config
apachectl configtest

# Verify file permissions
ls -la /var/www/html/LABx_Docs/
```

---

## Security Considerations

### Deployment Restrictions

| Environment | Allowed |
|-------------|---------|
| localhost | Yes |
| LAN (isolated) | Yes - with firewall |
| VPN/Private | Yes - authenticated |
| Public Internet | **NO** |
| Production Server | **NO** |
| Cloud VPS (public IP) | **NO** |

### Isolation Requirements

```
1. Run only on isolated development machines
2. Use host-only networking for VMs
3. Block external access via firewall
4. Never expose to internet
5. Do not store real credentials or data
```

### Intended Use Cases

- Security training and education
- Penetration testing practice
- Secure coding demonstrations
- CTF preparation
- Security certification study (OSCP, CEH, CompTIA Security+)

---

## References

### Standards

- [OWASP Top 10:2021](https://owasp.org/Top10/)
- [CWE/SANS Top 25](https://cwe.mitre.org/top25/)
- [OWASP Testing Guide v4.2](https://owasp.org/www-project-web-security-testing-guide/)
- [OWASP ASVS 4.0](https://owasp.org/www-project-application-security-verification-standard/)

### Training Resources

- [PortSwigger Web Security Academy](https://portswigger.net/web-security)
- [HackerOne Hacktivity](https://hackerone.com/hacktivity)
- [PentesterLab](https://pentesterlab.com/)
- [TryHackMe](https://tryhackme.com/)
- [HackTheBox](https://hackthebox.com/)

### CWE References

| CWE | Name | Category |
|-----|------|----------|
| CWE-284 | Improper Access Control | Access Control |
| CWE-639 | Insecure Direct Object Reference | Access Control |
| CWE-862 | Missing Authorization | Access Control |
| CWE-269 | Improper Privilege Management | Access Control |
| CWE-915 | Mass Assignment | Access Control |
| CWE-502 | Deserialization of Untrusted Data | Deserialization |
| CWE-22 | Path Traversal | Access Control |
| CWE-425 | Direct Request (Forced Browsing) | Access Control |

---

## License

Educational use only. Not intended for production deployment.

---

## Contributing

1. Fork repository
2. Create feature branch: `git checkout -b feature/lab-name`
3. Follow lab structure conventions
4. Include documentation (README.md, LAB_DOCUMENTATION.md)
5. Submit pull request

**Issue Reports:** [GitHub Issues](https://github.com/M9nx/LABx_Docs/issues)

---

**LABx_Docs v2.0** | 40+ Labs | 4 Categories | OWASP Top 10 Coverage

[GitHub](https://github.com/M9nx/LABx_Docs) | [Documentation](https://m9nx.me/posts/labx_docs---complete-setup-guide/) | [Author](https://m9nx.me)
