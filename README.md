<p align="center">
  <img src="https://img.shields.io/badge/LABx__Docs-v2.0-FF4444?style=for-the-badge&logo=openbugbounty&logoColor=white" alt="LABx_Docs v2.0">
</p>

<h1 align="center">🔐 LABx_Docs</h1>

<p align="center">
  <strong>Comprehensive Web Security Training Platform</strong><br>
  <em>Master OWASP Top 10 vulnerabilities through hands-on exploitation</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Labs-40+-22C55E?style=flat-square" alt="40+ Labs">
  <img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php" alt="PHP 8.0+">
  <img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 5.7+">
  <img src="https://img.shields.io/badge/License-Educational-blue?style=flat-square" alt="Educational License">
</p>

<p align="center">
  <a href="#-quick-start">Quick Start</a> •
  <a href="#-features">Features</a> •
  <a href="#-lab-categories">Categories</a> •
  <a href="#-documentation">Docs</a> •
  <a href="#-contributing">Contributing</a>
</p>

---

## 📖 About

**LABx_Docs** is a self-hosted web security training platform featuring **40+ vulnerable labs** across multiple OWASP categories. Each lab is a complete PHP application with intentional vulnerabilities, comprehensive documentation, step-by-step walkthroughs, and automatic progress tracking.

> 🎓 **Perfect for:** Security researchers, penetration testers, CTF enthusiasts, web developers learning secure coding, and anyone preparing for security certifications.

### 🎯 What You'll Learn

| Category | Skills |
|----------|--------|
| **Access Control** | IDOR exploitation, privilege escalation, authorization bypass, role manipulation |
| **Insecure Deserialization** | PHP object injection, gadget chains, PHAR exploits, cookie tampering |
| **API Security** | BOLA, broken authentication, mass assignment, rate limiting bypass |
| **Authentication** | Brute force, password reset poisoning, 2FA bypass, session attacks |

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🧪 Vulnerable Labs
- **40+ hands-on labs** across 4 categories
- Real-world scenarios based on HackerOne reports
- Difficulty levels: Apprentice → Practitioner → Expert
- Each lab is an isolated, complete application

</td>
<td width="50%">

### 📊 Progress Tracking
- Automatic completion detection
- Visual progress dashboards
- Category-based statistics
- Reset individual labs anytime

</td>
</tr>
<tr>
<td width="50%">

### 📚 Documentation
- Detailed vulnerability explanations
- Step-by-step exploitation guides
- Prevention and mitigation strategies
- Code-level analysis of flaws

</td>
<td width="50%">

### ⚡ Easy Setup
- One-click database initialization
- No hardcoded credentials
- Session-based configuration
- Works with XAMPP/WAMP/MAMP

</td>
</tr>
</table>

---

## 🚀 Quick Start

### Prerequisites

| Requirement | Version | Notes |
|-------------|---------|-------|
| PHP | 8.0+ | With mysqli extension |
| MySQL | 5.7+ | Or MariaDB 10.3+ |
| Web Server | Apache/Nginx | XAMPP recommended |
| Browser | Modern | Chrome, Firefox, Edge |

### Installation

```bash
# Clone the repository
git clone https://github.com/M9nx/LABx_Docs.git

# Move to web server directory (XAMPP example)
mv LABx_Docs /c/xampp/htdocs/

# Or for Linux
sudo mv LABx_Docs /var/www/html/
```

### First-Time Setup

1. **Start your web server** (Apache + MySQL)

2. **Access the platform**
   ```
   http://localhost/LABx_Docs/
   ```

3. **Configure database credentials**
   - Enter your MySQL host, username, and password
   - Click "Test & Save"

4. **Initialize databases**
   - Go to "Setup Databases" in sidebar
   - Click "Setup All" to create all lab databases

5. **Start hacking!**
   - Choose a category
   - Select a lab
   - Read the documentation
   - Exploit the vulnerability
   - Get the flag!

> 📘 **Complete Setup Guide:** [m9nx.me/posts/labx_docs---complete-setup-guide](https://m9nx.me/posts/labx_docs---complete-setup-guide/)

---

## 📂 Project Architecture

```
LABx_Docs/
│
├── 📄 index.php                 # Main dashboard & DB configuration
├── 📄 db-config.php             # Centralized database management
├── 📄 README.md                 # This documentation
│
├── 📁 src/                      # Shared components
│   ├── sidebar.php              # Global navigation sidebar
│   ├── sidebar.css              # Unified sidebar styles
│   ├── setup.php                # Global database setup wizard
│   └── progress.php             # Cross-category progress tracking
│
├── 📁 AC/                       # Access Control (30 Labs)
│   ├── index.php                # Category dashboard
│   ├── progress.php             # AC progress helper
│   └── Lab-01/ to Lab-30/       # Individual labs
│
├── 📁 Insecure-Deserialization/ # Deserialization (10 Labs)
│   ├── index.php                # Category dashboard
│   ├── progress.php             # ID progress helper
│   └── Lab-01/ to Lab-10/       # Individual labs
│
├── 📁 API/                      # API Security (Coming Soon)
│   ├── index.php                # Category dashboard
│   └── progress.php             # API progress helper
│
└── 📁 Authentication/           # Authentication (Coming Soon)
    ├── index.php                # Category dashboard
    └── progress.php             # Auth progress helper
```

### Lab Structure

Each lab follows a consistent structure:

```
Lab-XX/
├── 📄 index.php              # Lab entry point & scenario
├── 📄 lab-description.php    # Challenge description & hints
├── 📄 docs.php               # Full technical documentation
├── 📄 config.php             # Database configuration
├── 📄 setup_db.php           # Database initialization
├── 📄 database_setup.sql     # SQL schema
├── 📄 login.php              # Authentication (if applicable)
├── 📄 success.php            # Flag verification & completion
└── 📄 [vulnerability-specific files]
```

---

## 📚 Lab Categories

### 🔐 Access Control — 30 Labs

Master authorization vulnerabilities from beginner IDOR to advanced GraphQL exploitation.

<details>
<summary><strong>View All 30 Labs</strong></summary>

| # | Lab Title | Difficulty | Type |
|:-:|-----------|:----------:|------|
| 1 | Unprotected Admin Functionality | 🟢 Apprentice | Robots Disclosure |
| 2 | Unprotected Admin Panel with Unpredictable URL | 🟢 Apprentice | JS Source Disclosure |
| 3 | Bypassing Admin Panel via User Role Manipulation | 🟢 Apprentice | Cookie Manipulation |
| 4 | IDOR Leading to Account Takeover | 🟡 Practitioner | IDOR |
| 5 | User ID Controlled by Request Parameter | 🟡 Practitioner | IDOR |
| 6 | User ID Controlled by Request Parameter with Unpredictable IDs | 🟡 Practitioner | IDOR + GUID |
| 7 | User ID Controlled by Request Parameter with Data Leakage | 🟡 Practitioner | IDOR + Redirect |
| 8 | User ID Controlled by Request Parameter with Password Disclosure | 🟡 Practitioner | IDOR + Source |
| 9 | Insecure Direct Object Reference (IDOR) | 🟡 Practitioner | Classic IDOR |
| 10 | URL-Based Access Control Bypass | 🟡 Practitioner | X-Original-URL |
| 11 | Method-Based Access Control Bypass | 🟡 Practitioner | HTTP Method |
| 12 | Multi-Step Process with Flawed Access Control | 🟡 Practitioner | Workflow Bypass |
| 13 | Referer-Based Access Control | 🟡 Practitioner | Header Bypass |
| 14 | IDOR via Mass Assignment | 🟡 Practitioner | Mass Assignment |
| 15 | IDOR Leads to Account Takeover via Email Change | 🟡 Practitioner | Email IDOR |
| 16 | IDOR via Predictable Sequential IDs | 🟡 Practitioner | Sequential IDOR |
| 17 | IDOR with Horizontal Privilege Escalation | 🟡 Practitioner | Horizontal IDOR |
| 18 | IDOR via Parameter Pollution | 🟡 Practitioner | HPP + IDOR |
| 19 | IDOR in API Endpoint Leading to Data Breach | 🟡 Practitioner | API IDOR |
| 20 | IDOR via Encoded/Hashed IDs | 🟡 Practitioner | Encoded IDOR |
| 21 | IDOR with JWT Token Manipulation | 🟡 Practitioner | JWT + IDOR |
| 22 | IDOR via Indirect Object Reference | 🟡 Practitioner | Indirect IDOR |
| 23 | Privilege Escalation via Role Parameter | 🟡 Practitioner | Role Escalation |
| 24 | Vertical Privilege Escalation | 🟡 Practitioner | Vertical Escalation |
| 25 | Broken Access Control in File Upload | 🟡 Practitioner | File IDOR |
| 26 | Access Control Bypass via Path Traversal | 🟡 Practitioner | Path Traversal |
| 27 | HackerOne: PII Disclosure via IDOR | 🟡 Practitioner | Real Case Study |
| 28 | HackerOne: Account Deletion IDOR | 🟡 Practitioner | Real Case Study |
| 29 | HackerOne: Mass Assignment to Admin | 🟡 Practitioner | Real Case Study |
| 30 | IDOR via GraphQL Mutation | 🔴 Expert | GraphQL IDOR |

</details>

**Difficulty Breakdown:** 🟢 3 Apprentice • 🟡 26 Practitioner • 🔴 1 Expert

---

### 📦 Insecure Deserialization — 10 Labs

Exploit PHP serialization flaws from basic cookie tampering to PHAR polyglots.

<details>
<summary><strong>View All 10 Labs</strong></summary>

| # | Lab Title | Difficulty | Type |
|:-:|-----------|:----------:|------|
| 1 | Modifying Serialized Objects | 🟢 Apprentice | Cookie Tampering |
| 2 | Modifying Serialized Data Types | 🟢 Apprentice | Type Juggling |
| 3 | Using Application Functionality to Exploit | 🟡 Practitioner | Logic Abuse |
| 4 | Arbitrary Object Injection in PHP | 🟡 Practitioner | Object Injection |
| 5 | PHP Pre-Built Gadget Chain | 🟡 Practitioner | Gadget Chain |
| 6 | Ruby Documented Gadget Chain | 🟡 Practitioner | Gadget Chain |
| 7 | Custom PHP Gadget Chain | 🔴 Expert | Custom Gadget |
| 8 | Custom Java Gadget Chain | 🔴 Expert | Custom Gadget |
| 9 | PHAR Deserialization | 🔴 Expert | PHAR Exploit |
| 10 | Deserialization via Cookie Tampering | 🟢 Apprentice | Cookie Tampering |

</details>

**Difficulty Breakdown:** 🟢 3 Apprentice • 🟡 4 Practitioner • 🔴 3 Expert

---

### 🔌 API Security — Coming Soon

| Focus Areas |
|-------------|
| Broken Object Level Authorization (BOLA) |
| Broken Authentication |
| Excessive Data Exposure |
| Rate Limiting Bypass |
| Mass Assignment via API |

---

### 🔑 Authentication — Coming Soon

| Focus Areas |
|-------------|
| Brute Force Attacks |
| Password Reset Poisoning |
| 2FA/MFA Bypass |
| Session Management Flaws |
| JWT Implementation Bugs |

---

## 🛠️ Recommended Tools

| Tool | Purpose | Download |
|------|---------|----------|
| **Burp Suite Community** | HTTP proxy, request manipulation | [portswigger.net](https://portswigger.net/burp/communitydownload) |
| **Firefox Developer Edition** | Browser with enhanced DevTools | [mozilla.org](https://www.mozilla.org/firefox/developer/) |
| **Postman** | API testing and exploration | [postman.com](https://www.postman.com/downloads/) |
| **VS Code** | Source code analysis | [code.visualstudio.com](https://code.visualstudio.com/) |
| **sqlmap** | SQL injection automation | [sqlmap.org](https://sqlmap.org/) |
| **jwt.io** | JWT debugging and manipulation | [jwt.io](https://jwt.io/) |

---

## 📈 Progress System

### How It Works

1. **Solve the lab** by achieving the objective (e.g., delete user, escalate privileges)
2. **Reach success.php** which validates completion
3. **Progress is saved** automatically to your database
4. **View statistics** on dashboards and progress pages

### Database Structure

Each category has its own progress database:
- `ac_progress` — Access Control
- `id_progress` — Insecure Deserialization
- `api_progress` — API Security
- `auth_progress` — Authentication

### Reset Progress

- **Individual lab:** Click "Reset" on the progress page
- **Full category:** Use setup page to reinitialize
- **Everything:** Clear all progress databases

---

## 🔧 Configuration

### Database Credentials

Credentials are stored in PHP sessions (not files):

```php
// Access current credentials
require_once 'db-config.php';
$creds = getDbCredentials();
// Returns: ['host', 'user', 'pass', 'configured']
```

### Custom Database Host

For Docker or remote MySQL:
1. Visit homepage
2. Update Host field (e.g., `mysql`, `192.168.1.100`)
3. Test & Save

### Adding New Labs

1. Create folder: `CategoryName/Lab-XX/`
2. Copy template files from existing lab
3. Update `database_setup.sql` with schema
4. Register in category's `index.php`
5. Update setup.php arrays

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

### Report Issues
- Found a bug? [Open an issue](https://github.com/M9nx/LABx_Docs/issues)
- Lab not working? Include error messages and steps to reproduce

### Submit Labs
1. Fork the repository
2. Create a new lab following existing structure
3. Include comprehensive documentation
4. Submit a pull request

### Documentation
- Fix typos or unclear instructions
- Add exploitation tips
- Translate to other languages

---

## ⚠️ Security Notice

<table>
<tr>
<td>

### ⛔ DO NOT

- Deploy to production servers
- Expose to the internet
- Use for unauthorized testing
- Store real user data

</td>
<td>

### ✅ DO

- Run locally only
- Use for learning purposes
- Practice in isolated environments
- Follow responsible disclosure

</td>
</tr>
</table>

> **This platform contains intentionally vulnerable code.** It is designed exclusively for educational purposes in controlled environments. The authors are not responsible for misuse.

---

## 📄 License

This project is for **educational use only**. Not intended for production deployment.

---

## 🙏 Acknowledgments

- [PortSwigger Web Security Academy](https://portswigger.net/web-security) — Inspiration for lab format
- [OWASP](https://owasp.org/) — Vulnerability classifications
- [HackerOne](https://hackerone.com/) — Real-world case studies
- Security community — Continuous learning and sharing

---

<p align="center">
  <strong>LABx_Docs v2.0</strong><br>
  <em>40+ Labs • 4 Categories • Unlimited Learning</em>
</p>

<p align="center">
  Made with ❤️ for the security community
</p>

<p align="center">
  <a href="https://github.com/M9nx/LABx_Docs">⭐ Star on GitHub</a> •
  <a href="https://m9nx.me">🌐 Author's Blog</a>
</p>
