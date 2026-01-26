# LABx_Docs - Web Security Training Platform

## 🎯 Overview

LABx_Docs is a comprehensive web security training platform featuring hands-on vulnerable labs designed to teach real-world security flaws. Learn by exploiting intentional vulnerabilities in a safe, controlled environment.

## 📁 Structure

```
LABx_Docs/
├── index.php                    # Main home page
├── README.md                    # This file
│
├── AC/                          # Access Control Labs
│   ├── index.php               # AC category home
│   ├── progress.php            # Progress tracking system
│   ├── setup-all-databases.php # Master DB setup
│   ├── PROGRESS_TRACKING.md    # Progress docs
│   └── Lab-01 to Lab-30/       # Individual labs
│
├── API/                         # API Security Labs
│   ├── index.php               # API category home
│   ├── progress.php            # Progress tracking
│   ├── PROGRESS_TRACKING.md    # Progress docs
│   └── Lab-XX/                 # (Coming Soon)
│
└── Authentication/              # Authentication Labs
    ├── index.php               # Auth category home
    ├── progress.php            # Progress tracking
    ├── PROGRESS_TRACKING.md    # Progress docs
    └── Lab-XX/                 # (Coming Soon)
```

## 🚀 Getting Started

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- MySQL root password: `root`

### Installation

1. Clone or copy the `LABx_Docs` folder to your XAMPP htdocs:
   ```
   C:\xampp\htdocs\LABx_Docs
   ```

2. Start Apache and MySQL in XAMPP Control Panel

3. Visit: `http://localhost/LABx_Docs/`

### Setting Up Labs

1. Navigate to a category (e.g., Access Control)
2. Click "Setup Databases" to initialize all labs
3. Or use individual lab's `setup_db.php`

## 📚 Categories

### 🔐 Access Control (30 Labs - Active)
Master access control vulnerabilities including:
- IDOR (Insecure Direct Object References)
- Privilege Escalation
- Broken Authorization
- Cookie Manipulation
- Mass Assignment
- Real-world HackerOne case studies

### 🔌 API Security (Coming Soon)
Based on OWASP API Security Top 10:
- Broken Object Level Authorization
- Broken Authentication
- Excessive Data Exposure
- Rate Limiting Issues
- Mass Assignment via API

### 🔑 Authentication (Coming Soon)
Authentication vulnerability labs:
- Brute Force Attacks
- Password Reset Poisoning
- 2FA/MFA Bypass
- Session Management Flaws
- JWT Attacks
- OAuth Exploits

## 📈 Progress Tracking

Each category has its own progress tracking system:
- Labs are automatically marked as solved
- Progress persists across sessions
- Reset individual labs anytime
- View completion statistics

## 🔧 Lab Structure

Each individual lab contains:
```
Lab-XX/
├── index.php           # Lab landing page
├── lab-description.php # Detailed lab info
├── docs.php            # Technical documentation
├── config.php          # Database configuration
├── setup_db.php        # Individual setup script
├── database_setup.sql  # SQL schema
├── login.php           # Login page (if applicable)
├── profile.php         # User profile
├── success.php         # Completion page
└── [other files]       # Lab-specific files
```

## ⚠️ Disclaimer

This platform is for **educational purposes only**. The labs contain intentional vulnerabilities - do not deploy in production environments.

## 📝 License

Educational use only. Not for production deployment.
