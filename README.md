# LABx_Docs

**Web Application Security Training Platform**

[![PHP 8.0+](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL 5.7+](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Labs](https://img.shields.io/badge/Labs-40+-22C55E?style=flat-square)](https://github.com/M9nx/LABx_Docs)
[![License](https://img.shields.io/badge/License-Educational-blue?style=flat-square)](LICENSE)

A self-hosted vulnerable web application platform featuring 40+ labs across OWASP Top 10 categories. Each lab contains intentional security flaws with comprehensive documentation, exploitation guides, and automatic progress tracking.

---

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Architecture](#architecture)
- [Lab Categories](#lab-categories)
- [Progress Tracking](#progress-tracking)
- [Configuration](#configuration)
- [Tools](#tools)
- [Contributing](#contributing)
- [Security Notice](#security-notice)

---

## Requirements

| Component | Version | Notes |
|-----------|---------|-------|
| PHP | 8.0+ | mysqli extension required |
| MySQL | 5.7+ | MariaDB 10.3+ compatible |
| Web Server | Apache 2.4+ | mod_rewrite enabled |
| Browser | Modern | Chrome 90+, Firefox 88+, Edge 90+ |

**Tested Environments:** XAMPP 8.2, WAMP 3.3, MAMP 6.8, Docker LAMP stacks

---

## Installation

### Standard Installation

```bash
# Clone repository
git clone https://github.com/M9nx/LABx_Docs.git

# Move to web root
# Windows (XAMPP)
move LABx_Docs C:\xampp\htdocs\

# Linux
sudo mv LABx_Docs /var/www/html/

# macOS (MAMP)
mv LABx_Docs /Applications/MAMP/htdocs/
```

### Post-Installation

1. Start Apache and MySQL services
2. Navigate to `http://localhost/LABx_Docs/`
3. Configure database credentials (host, user, password)
4. Click **Test & Save** to verify connection
5. Open **Setup Databases** from sidebar
6. Initialize all lab databases with **Setup All**

**Full Setup Guide:** [m9nx.me/posts/labx_docs---complete-setup-guide](https://m9nx.me/posts/labx_docs---complete-setup-guide/)

---

## Architecture

```
LABx_Docs/
├── index.php                    # Dashboard + DB configuration UI
├── db-config.php                # Credential management API
├── README.md                    # Documentation
│
├── src/                         # Shared components
│   ├── sidebar.php              # Navigation component
│   ├── sidebar.css              # Sidebar styles
│   ├── setup.php                # Database initialization wizard
│   └── progress.php             # Cross-category progress tracker
│
├── AC/                          # Access Control (30 labs)
│   ├── index.php                # Category dashboard
│   ├── progress.php             # Progress helper functions
│   ├── PROGRESS_TRACKING.md     # Progress documentation
│   └── Lab-01/ ... Lab-30/      # Individual lab directories
│
├── Insecure-Deserialization/    # Deserialization (10 labs)
│   ├── index.php
│   ├── progress.php
│   └── Lab-01/ ... Lab-10/
│
├── API/                         # API Security (planned)
│   ├── index.php
│   └── progress.php
│
└── Authentication/              # Authentication (planned)
    ├── index.php
    └── progress.php
```

### Lab Directory Structure

```
Lab-XX/
├── index.php              # Entry point, scenario description
├── lab-description.php    # Challenge objectives, hints
├── docs.php               # Technical documentation, walkthrough
├── config.php             # Database connection configuration
├── setup_db.php           # Database initialization script
├── database_setup.sql     # SQL schema and seed data
├── login.php              # Authentication (when applicable)
├── logout.php             # Session termination
├── success.php            # Completion verification, flag display
├── LAB_DOCUMENTATION.md   # Markdown documentation
├── README.md              # Lab-specific README
└── [vulnerability files]  # Additional files per vulnerability type
```

---

## Lab Categories

### Access Control — 30 Labs

Authorization and access control vulnerabilities including IDOR, privilege escalation, and role manipulation.

| Lab | Title | Difficulty | Vulnerability Type |
|:---:|-------|:----------:|-------------------|
| 01 | Unprotected Admin Functionality | Apprentice | Robots.txt Disclosure |
| 02 | Unpredictable Admin URL | Apprentice | JavaScript Source Disclosure |
| 03 | User Role Manipulation | Apprentice | Cookie Manipulation |
| 04 | IDOR Account Takeover | Practitioner | Insecure Direct Object Reference |
| 05 | User ID via Request Parameter | Practitioner | IDOR |
| 06 | Unpredictable User IDs | Practitioner | IDOR with GUID |
| 07 | Data Leakage in Redirect | Practitioner | IDOR with Information Leak |
| 08 | Password Disclosure | Practitioner | IDOR with Source Exposure |
| 09 | Classic IDOR | Practitioner | Direct Object Reference |
| 10 | URL-Based Access Control Bypass | Practitioner | X-Original-URL Header |
| 11 | Method-Based Access Control | Practitioner | HTTP Method Override |
| 12 | Multi-Step Process Bypass | Practitioner | Workflow Access Control |
| 13 | Referer-Based Access Control | Practitioner | Header Manipulation |
| 14 | Mass Assignment IDOR | Practitioner | Mass Assignment |
| 15 | Email Change IDOR | Practitioner | Account Takeover via IDOR |
| 16 | Sequential ID Enumeration | Practitioner | Predictable Identifiers |
| 17 | Horizontal Privilege Escalation | Practitioner | Horizontal IDOR |
| 18 | Parameter Pollution IDOR | Practitioner | HTTP Parameter Pollution |
| 19 | API Endpoint IDOR | Practitioner | API IDOR |
| 20 | Encoded/Hashed ID IDOR | Practitioner | Encoded Object References |
| 21 | JWT Token Manipulation | Practitioner | JWT + IDOR |
| 22 | Indirect Object Reference | Practitioner | Indirect IDOR |
| 23 | Role Parameter Escalation | Practitioner | Vertical Escalation |
| 24 | Vertical Privilege Escalation | Practitioner | Privilege Escalation |
| 25 | File Upload Access Control | Practitioner | File-based IDOR |
| 26 | Path Traversal Bypass | Practitioner | Path Traversal + AC |
| 27 | HackerOne: PII Disclosure | Practitioner | Real-world Case Study |
| 28 | HackerOne: Account Deletion | Practitioner | Real-world Case Study |
| 29 | HackerOne: Mass Assignment | Practitioner | Real-world Case Study |
| 30 | GraphQL Mutation IDOR | Expert | GraphQL IDOR |

**Distribution:** 3 Apprentice, 26 Practitioner, 1 Expert

---

### Insecure Deserialization — 10 Labs

PHP object serialization vulnerabilities from basic cookie tampering to advanced gadget chains.

| Lab | Title | Difficulty | Vulnerability Type |
|:---:|-------|:----------:|-------------------|
| 01 | Modifying Serialized Objects | Apprentice | Cookie Tampering |
| 02 | Serialized Data Types | Apprentice | PHP Type Juggling |
| 03 | Application Functionality Abuse | Practitioner | Logic Exploitation |
| 04 | Arbitrary Object Injection | Practitioner | PHP Object Injection |
| 05 | Pre-Built Gadget Chain (PHP) | Practitioner | Gadget Chain |
| 06 | Documented Gadget Chain (Ruby) | Practitioner | Gadget Chain |
| 07 | Custom Gadget Chain (PHP) | Expert | Custom Gadget |
| 08 | Custom Gadget Chain (Java) | Expert | Custom Gadget |
| 09 | PHAR Deserialization | Expert | PHAR Polyglot |
| 10 | Cookie-Based Deserialization | Apprentice | Session Tampering |

**Distribution:** 3 Apprentice, 4 Practitioner, 3 Expert

---

### API Security — Planned

| Focus Area | Description |
|------------|-------------|
| BOLA | Broken Object Level Authorization |
| Authentication | API authentication bypass |
| Data Exposure | Excessive data in API responses |
| Rate Limiting | Bypass rate limiting controls |
| Mass Assignment | API parameter injection |

---

### Authentication — Planned

| Focus Area | Description |
|------------|-------------|
| Brute Force | Credential stuffing, login bypass |
| Password Reset | Reset token poisoning, host injection |
| 2FA/MFA Bypass | Two-factor authentication flaws |
| Session Management | Session fixation, hijacking |
| JWT Vulnerabilities | Algorithm confusion, weak secrets |

---

## Progress Tracking

### Implementation

Progress tracking uses category-specific MySQL databases:

| Category | Database | Table |
|----------|----------|-------|
| Access Control | `ac_progress` | `solved_labs` |
| Insecure Deserialization | `id_progress` | `solved_labs` |
| API Security | `api_progress` | `solved_labs` |
| Authentication | `auth_progress` | `solved_labs` |

### Schema

```sql
CREATE TABLE solved_labs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lab_number INT NOT NULL UNIQUE,
    solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Progress API

```php
// Include progress helper
require_once('../progress.php');

// Check if lab is solved
$solved = isLabSolved(5);  // Returns boolean

// Mark lab as complete
markLabSolved(5);  // Inserts into solved_labs

// Get all solved labs
$solved = getSolvedLabs();  // Returns array of lab numbers
```

### Workflow

1. User completes lab objective (e.g., delete user, escalate privileges)
2. `success.php` validates completion conditions
3. `markLabSolved()` records progress
4. Dashboard reflects updated statistics

---

## Configuration

### Database Credentials

Credentials are session-stored, not file-based:

```php
// db-config.php API
require_once 'db-config.php';

// Get credentials
$creds = getDbCredentials();
// Returns: ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'configured' => true]

// Test connection
$result = testDbConnection($host, $user, $pass);
// Returns: ['success' => true/false, 'message' => '...']
```

### AJAX Endpoints

`db-config.php` accepts POST requests:

```javascript
// Test connection
fetch('db-config.php', {
    method: 'POST',
    body: new URLSearchParams({
        action: 'test',
        host: 'localhost',
        user: 'root',
        pass: ''
    })
});

// Check status
fetch('db-config.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'status' })
});

// Clear credentials
fetch('db-config.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'clear' })
});
```

### Docker Configuration

For containerized MySQL, update the host setting:

```
Host: mysql          # Docker service name
Host: 172.17.0.2     # Container IP
Host: host.docker.internal  # Docker Desktop
```

---

## Tools

### Recommended

| Tool | Purpose | Link |
|------|---------|------|
| Burp Suite Community | HTTP interception, request modification | [portswigger.net](https://portswigger.net/burp/communitydownload) |
| Firefox Developer Edition | Enhanced DevTools, cookie manipulation | [mozilla.org](https://www.mozilla.org/firefox/developer/) |
| Postman | API testing, request crafting | [postman.com](https://www.postman.com/downloads/) |
| VS Code | Source code analysis | [code.visualstudio.com](https://code.visualstudio.com/) |

### Utilities

| Tool | Purpose | Link |
|------|---------|------|
| CyberChef | Encoding/decoding, data transformation | [gchq.github.io/CyberChef](https://gchq.github.io/CyberChef/) |
| jwt.io | JWT debugging, token manipulation | [jwt.io](https://jwt.io/) |
| sqlmap | SQL injection automation | [sqlmap.org](https://sqlmap.org/) |
| ffuf | Web fuzzing, directory bruteforce | [github.com/ffuf/ffuf](https://github.com/ffuf/ffuf) |

---

## Contributing

### Issue Reporting

1. Check existing issues for duplicates
2. Include PHP version, MySQL version, OS
3. Provide reproduction steps
4. Attach relevant error logs

### Lab Submissions

```bash
# Fork and clone
git clone https://github.com/YOUR_USERNAME/LABx_Docs.git

# Create lab directory
mkdir AC/Lab-XX

# Required files
- index.php
- lab-description.php
- docs.php
- config.php
- setup_db.php
- database_setup.sql
- success.php
- README.md

# Submit pull request with:
- Vulnerability description
- Exploitation steps
- Prevention guidance
```

### Documentation

- Fix errors, typos, unclear instructions
- Add exploitation techniques
- Improve code documentation

---

## Security Notice

**This platform contains intentionally vulnerable code.**

| Prohibited | Permitted |
|------------|-----------|
| Production deployment | Local development only |
| Internet exposure | Isolated networks |
| Unauthorized testing | Educational purposes |
| Real user data | Test/dummy data only |

The authors assume no responsibility for misuse. Use exclusively in controlled, isolated environments for security education and research.

---

## References

- [OWASP Top 10](https://owasp.org/Top10/)
- [PortSwigger Web Security Academy](https://portswigger.net/web-security)
- [CWE/SANS Top 25](https://cwe.mitre.org/top25/)
- [HackerOne Hacktivity](https://hackerone.com/hacktivity)

---

## License

Educational use only. Not for production deployment.

---

**LABx_Docs v2.0** — 40+ Labs | 4 Categories | Hands-on Security Training

[GitHub](https://github.com/M9nx/LABx_Docs) | [Author](https://m9nx.me)
