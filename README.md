# LABx_Docs - Web Security Training Platform

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.0-blue?style=for-the-badge" alt="Version 1.0">
  <img src="https://img.shields.io/badge/Labs-30-green?style=for-the-badge" alt="30 Labs">
  <img src="https://img.shields.io/badge/PHP-8.0+-purple?style=for-the-badge" alt="PHP 8.0+">
  <img src="https://img.shields.io/badge/MySQL-5.7+-orange?style=for-the-badge" alt="MySQL 5.7+">
</p>

<p align="center">
  <strong>Learn web security by exploiting real vulnerabilities in a safe, controlled environment.</strong>
</p>

---

## 🎯 Overview

LABx_Docs is a comprehensive web security training platform featuring **30 hands-on vulnerable labs** designed to teach real-world security flaws. Each lab is a complete PHP application with intentional vulnerabilities, detailed documentation, and automatic progress tracking.

> 📖 **Complete Setup Guide:** [https://m9nx.me/posts/labx_docs---complete-setup-guide/](https://m9nx.me/posts/labx_docs---complete-setup-guide/)

## ✨ Features

- **30 Access Control Labs** - From beginner to expert level
- **Real-world scenarios** - Including HackerOne case studies
- **Progress tracking** - Automatic lab completion tracking
- **Detailed documentation** - Each lab has comprehensive docs
- **No hardcoded credentials** - Configure via UI
- **Easy setup** - One-click database initialization

## 📁 Project Structure

```
LABx_Docs/
├── index.php                    # Main dashboard
├── db-config.php               # Centralized DB configuration
├── README.md                   # This file
│
├── AC/                          # Access Control Labs (30 Labs)
│   ├── index.php               # Category dashboard
│   ├── progress.php            # Progress tracking system
│   ├── setup-all-databases.php # Initialize all lab databases
│   └── Lab-01 to Lab-30/       # Individual labs
│
├── API/                         # API Security Labs (Coming Soon)
│   ├── index.php               # Category placeholder
│   └── progress.php            # Progress tracking
│
└── Authentication/              # Authentication Labs (Coming Soon)
    ├── index.php               # Category placeholder
    └── progress.php            # Progress tracking
```

## 🚀 Quick Start

### Prerequisites
- XAMPP, WAMP, or any PHP development environment
- PHP 8.0+
- MySQL 5.7+

### Installation

```bash
# Clone to your web server directory
git clone https://github.com/yourusername/LABx_Docs.git

# For XAMPP users:
# Copy to C:\xampp\htdocs\LABx_Docs
```

### Setup

1. Start Apache and MySQL
2. Visit: `http://localhost/LABx_Docs/`
3. Configure database credentials (first-time setup)
4. Navigate to Access Control → Setup All Databases
5. Start hacking!

> 📖 **Detailed walkthrough:** [https://m9nx.me/posts/labx_docs---complete-setup-guide/](https://m9nx.me/posts/labx_docs---complete-setup-guide/)

## 📚 Lab Categories

### 🔐 Access Control (30 Labs) - V1.0 Release

| # | Lab Title | Difficulty | Type |
|---|-----------|------------|------|
| 1 | Unprotected Admin Functionality | Apprentice | Robots File Disclosure |
| 2 | Unprotected Admin Panel with Unpredictable URL | Apprentice | JS Source Disclosure |
| 3 | Bypassing Admin Panel via User Role Manipulation | Apprentice | Cookie Manipulation |
| 4 | IDOR Leading to Account Takeover | Practitioner | IDOR |
| 5 | User ID Controlled by Request Parameter | Practitioner | IDOR |
| 6 | User ID Controlled by Request Parameter with Unpredictable IDs | Practitioner | IDOR |
| 7 | User ID Controlled by Request Parameter with Data Leakage | Practitioner | IDOR |
| 8 | User ID Controlled by Request Parameter with Password Disclosure | Practitioner | IDOR |
| 9 | Insecure Direct Object Reference (IDOR) | Practitioner | IDOR |
| 10 | URL-Based Access Control Can Be Circumvented | Practitioner | Header Bypass |
| 11 | Method-Based Access Control Can Be Circumvented | Practitioner | HTTP Method |
| 12 | Multi-Step Process with Flawed Access Control | Practitioner | Multi-Step Bypass |
| 13 | Referer-Based Access Control | Practitioner | Header Bypass |
| 14 | IDOR via Mass Assignment | Practitioner | Mass Assignment |
| 15 | IDOR Leads to Account Takeover via Email Change | Practitioner | IDOR |
| 16 | IDOR via Predictable Sequential IDs | Practitioner | IDOR |
| 17 | IDOR with Horizontal Privilege Escalation | Practitioner | IDOR |
| 18 | IDOR via Parameter Pollution | Practitioner | IDOR |
| 19 | IDOR in API Endpoint Leading to Data Breach | Practitioner | API IDOR |
| 20 | IDOR via Encoded/Hashed IDs | Practitioner | IDOR |
| 21 | IDOR with JWT Token Manipulation | Practitioner | JWT IDOR |
| 22 | IDOR via Indirect Object Reference | Practitioner | IDOR |
| 23 | Privilege Escalation via Role Parameter | Practitioner | Privilege Escalation |
| 24 | Vertical Privilege Escalation | Practitioner | Privilege Escalation |
| 25 | Broken Access Control in File Upload | Practitioner | File Upload |
| 26 | Access Control Bypass via Path Traversal | Practitioner | Path Traversal |
| 27 | HackerOne Report #1: Improper Access Control Leading to PII Disclosure | Practitioner | Real Case |
| 28 | HackerOne Report #2: IDOR Allowing Deletion of Any User Account | Practitioner | Real Case |
| 29 | HackerOne Report #3: Mass Assignment Leading to Admin Access | Practitioner | Real Case |
| 30 | IDOR via GraphQL Mutation | Expert | GraphQL IDOR |

**Difficulty Breakdown:** 3 Apprentice | 26 Practitioner | 1 Expert

### 🔌 API Security (Coming Soon)
- Broken Object Level Authorization
- Broken Authentication
- Excessive Data Exposure
- Rate Limiting Issues
- Mass Assignment via API

### 🔑 Authentication (Coming Soon)
- Brute Force Attacks
- Password Reset Poisoning
- 2FA/MFA Bypass
- Session Management Flaws
- JWT Attacks

## 📈 Progress Tracking

- ✅ Automatic completion detection
- 📊 Progress persists across sessions
- 🔄 Reset individual labs anytime
- 📈 View completion statistics on dashboard

## 🔧 Lab Structure

Each lab contains:
```
Lab-XX/
├── index.php           # Lab landing page
├── lab-description.php # Challenge description & hints
├── docs.php            # Technical documentation
├── config.php          # Database configuration
├── setup_db.php        # Database setup script
├── database_setup.sql  # SQL schema
├── login.php           # Login functionality
├── profile.php         # User profile page
├── success.php         # Completion verification
└── [lab-specific]      # Additional files per lab
```

## 🛠️ Recommended Tools

- [Burp Suite Community](https://portswigger.net/burp/communitydownload) - HTTP proxy
- [Firefox Developer Edition](https://www.mozilla.org/firefox/developer/) - Browser DevTools
- [VS Code](https://code.visualstudio.com/) - Code review

## ⚠️ Disclaimer

This platform is for **educational purposes only**. The labs contain intentional vulnerabilities designed for learning. **DO NOT** deploy in production environments.

## 📝 License

Educational use only. Not for production deployment.

## 🙏 Credits

Created for the security community to learn and practice access control vulnerabilities in a safe environment.

---

<p align="center">
  <strong>Version 1.0</strong> | 30 Access Control Labs | Built with PHP & MySQL
</p>
