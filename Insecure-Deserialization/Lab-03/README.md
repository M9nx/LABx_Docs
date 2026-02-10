# Lab 03: Using Application Functionality to Exploit Insecure Deserialization

## Quick Start

1. **Setup database**: Navigate to `setup_db.php` in browser
2. **Login**: `wiener:peter`
3. **Decode cookie**: Go to My Account, decode the Base64 session cookie
4. **Find path**: Note the absolute path in `avatar_link` (e.g., `C:\xampp\htdocs\...\Lab-03/home/wiener/avatar.jpg`)
5. **Exploit**: Change `/home/wiener/avatar.jpg` to `/home/carlos/morale.txt` (keep the base path!)
6. **Update length**: Update the `s:XX:` to match your new path length
7. **Encode & Replace**: Base64 encode the payload and replace the cookie
8. **Trigger**: Click "Delete My Account"
9. **Win**: Carlos's morale.txt is deleted

## Attack Summary

| Component | Value |
|-----------|-------|
| Cookie Name | `session` |
| Target File | `[LAB_PATH]/home/carlos/morale.txt` (absolute path required) |
| Vulnerable Function | `deleteUserAccount()` |
| Exploit Method | Modify serialized `avatar_link` to target file path |

## Payload

**Important:** Use the ABSOLUTE path from your decoded cookie!

```
# Example payload structure (your path will vary):
O:4:"User":2:{s:8:"username";s:6:"wiener";s:11:"avatar_link";s:XX:"C:\xampp\htdocs\...\Lab-03/home/carlos/morale.txt";}

# Replace s:XX with the actual length of your path string
```

## Base64 Encoding

In PowerShell:
```powershell
[Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes('YOUR_PAYLOAD_HERE'))
```

Or use any online Base64 encoder.

## Files

- [config.php](config.php) - Vulnerable User class
- [my-account.php](my-account.php) - Shows decoded cookie
- [delete-account.php](delete-account.php) - Triggers file deletion
- [docs.php](docs.php) - Full documentation
- [LAB_DOCUMENTATION.md](LAB_DOCUMENTATION.md) - Technical writeup

## Vulnerability

The server uses `avatar_link` from deserialized cookie instead of database:

```php
// VULNERABLE
$avatarPath = $sessionData->avatar_link;  // From cookie!
unlink($avatarPath);  // Deletes any file
```

## Fix

```php
// SECURE
$user = $db->query("SELECT avatar_link FROM users WHERE username = ?");
$avatarPath = $user['avatar_link'];  // From database
```

## Lab Level

PRACTITIONER
