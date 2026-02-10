# Lab 1: Modifying Serialized Objects

## Overview
This lab uses a serialization-based session mechanism that is vulnerable to privilege escalation. The application stores session data in a client-side cookie as a serialized PHP object.

## Difficulty
**Apprentice** (Beginner-friendly)

## Objective
Log in with the provided credentials, exploit the vulnerability to gain admin privileges, and delete user `carlos`.

## Credentials
- **Username:** wiener
- **Password:** peter

## Vulnerability
The application uses PHP's `serialize()` function to create session data and stores it in a Base64-encoded cookie. The `admin` attribute within this serialized object can be modified by an attacker to escalate privileges.

## Attack Vector
1. Login with provided credentials
2. Capture the session cookie
3. URL-decode and Base64-decode the cookie value
4. Modify `admin` attribute from `b:0` to `b:1`
5. Re-encode and replace the cookie
6. Access admin functionality

## Files
| File | Description |
|------|-------------|
| `config.php` | Database connection and vulnerable serialization functions |
| `index.php` | Lab landing page |
| `login.php` | Login page with vulnerable cookie creation |
| `logout.php` | Logout functionality |
| `my-account.php` | Profile page showing cookie details |
| `admin.php` | Admin panel with user deletion |
| `success.php` | Lab completion page |
| `docs.php` | Full documentation with walkthrough |
| `lab-description.php` | Lab description page |
| `setup_db.php` | Database initialization script |
| `database_setup.sql` | SQL schema |

## Setup
1. Navigate to `setup_db.php` to initialize the database
2. Or use the parent category's `setup-all-databases.php`

## Key Learning Points
- PHP serialization format
- Why client-side session storage is dangerous
- Importance of integrity protection (signing/encryption)
- Always verify authorization server-side
