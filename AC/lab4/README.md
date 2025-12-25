# Lab 4: User Role Can Be Modified in User Profile

A vulnerable web application demonstrating privilege escalation through mass assignment in user profile updates.

## Quick Start

1. **Setup Database**
   - Open MySQL Workbench or phpMyAdmin
   - Connect with `root` / `root`
   - Run `database_setup.sql`

2. **Access the Lab**
   - Navigate to: `http://localhost/AC/lab4/`

3. **Login**
   - Username: `wiener`
   - Password: `peter`

## Objective

Access the admin panel at `/admin.php` and delete user `carlos`.

## Vulnerability

When updating your profile, the server accepts a `roleid` parameter in the JSON request, allowing you to escalate your privileges from regular user (roleid=1) to administrator (roleid=2).

## Solution

1. Login as `wiener:peter`
2. Go to Profile page
3. Intercept the profile update request
4. Add `"roleid": 2` to the JSON payload
5. Access admin panel
6. Delete carlos

## Files

- `index.php` - Main page
- `login.php` - Authentication
- `profile.php` - Vulnerable profile update
- `admin.php` - Admin panel (requires roleid=2)
- `docs.php` - Full documentation
