# Lab 30: Stocky Inventory App - IDOR Vulnerability

## Overview

**Stocky** is a fictional inventory management application used by e-commerce store owners. This lab demonstrates an **Insecure Direct Object Reference (IDOR)** vulnerability in the settings management system.

## Vulnerability

The application allows users to modify their column display settings for the "Low Stock Variants" view. However, the server fails to verify that users can only modify their own settings, allowing attackers to:

1. **Direct Modification**: Change the `settings_id` parameter to update another user's settings
2. **Import Settings**: Use the import feature to access another user's configuration

## Quick Start

1. **Setup Database**: Navigate to `http://localhost/AC/Lab-30/setup_db.php`
2. **Access Lab**: Go to `http://localhost/AC/Lab-30/`
3. **Login**: Use any test account (e.g., `alice_shop` / `password123`)

## Test Accounts

| Username | Password | Store Name | Settings ID |
|----------|----------|------------|-------------|
| alice_shop | password123 | Alice's Fashion Boutique | 1 |
| bob_tech | password123 | Bob's Tech Store | 2 |
| carol_home | password123 | Carol's Home Goods | 3 |
| david_sports | password123 | David's Sports Outlet | 4 |

## Attack Vectors

### Method 1: Direct Settings Modification
```
POST /AC/Lab-30/settings.php
settings_id=2  (modify Bob's settings while logged in as Alice)
```

### Method 2: Import Settings
```
POST /AC/Lab-30/settings.php
action=import
import_from_id=2  (import Bob's settings)
```

## Flag

After successfully exploiting the IDOR vulnerability:
```
FLAG{IDOR_STOCKY_SETTINGS_PWNED_2024}
```

## Files

- `index.php` - Lab landing page
- `login.php` - Authentication
- `dashboard.php` - User dashboard
- `settings.php` - **Vulnerable page** (IDOR)
- `low-stock.php` - Inventory view
- `docs.php` - Documentation with sidebar
- `docs-technical.php` - Technical deep dive
- `docs-mitigation.php` - Fix guide
- `setup_db.php` - Database initialization

## Learning Objectives

- Understand IDOR vulnerabilities and their impact
- Learn to identify missing authorization checks
- Implement proper ownership verification
- Apply secure coding practices for access control
