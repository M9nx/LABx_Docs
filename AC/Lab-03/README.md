# Lab 3: User Role Controlled by Request Parameter

## Overview

This lab demonstrates a critical access control vulnerability where user roles are controlled by a client-side cookie that can be easily manipulated.

## Difficulty

**Apprentice** - Basic level vulnerability

## Objective

Access the admin panel and delete the user `carlos`.

## The Vulnerability

The application stores the user's admin status in a browser cookie (`Admin=false` or `Admin=true`). When checking for admin access, it only verifies this cookie value instead of checking the user's actual role in the database.

This allows any authenticated user to:
1. Login with a normal account
2. Modify the `Admin` cookie to `true`
3. Access the admin panel
4. Perform any administrative action

## Quick Start

1. Start XAMPP (Apache + MySQL)
2. Run the database setup script
3. Navigate to `http://localhost/AC/lab3/`
4. Login with credentials: `wiener` / `password`
5. Open Developer Tools (F12) and change the `Admin` cookie to `true`
6. Access `/admin.php` and delete carlos

## Files

- `index.php` - Lab landing page
- `login.php` - Login page (sets vulnerable cookie)
- `profile.php` - User profile page
- `admin.php` - Admin panel (checks cookie for authorization)
- `logout.php` - Logout functionality
- `config.php` - Database configuration
- `docs.php` - Detailed documentation
- `lab-description.php` - Lab description and hints
- `database_setup.sql` - Database setup script
- `LAB_DOCUMENTATION.md` - Detailed technical documentation

## Test Accounts

| Username | Password | Role  |
|----------|----------|-------|
| admin    | password | admin |
| wiener   | password | user  |
| carlos   | password | user  |
| alice    | password | user  |
| bob      | password | user  |

## Key Takeaway

**Never trust client-side data for authorization decisions.** Always verify user permissions on the server side by checking against a secure data source (database or cryptographically signed session).
