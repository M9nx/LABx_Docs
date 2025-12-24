<style>
    .doc-section h1 { font-size: 2.2rem; color: #ff4444; margin-bottom: 1rem; }
    .doc-section h2 { font-size: 1.5rem; color: #ff6666; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 68, 68, 0.3); }
    .doc-section h3 { font-size: 1.2rem; color: #ff8888; margin: 1.5rem 0 0.75rem; }
    .doc-section p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
    .doc-section ul, .doc-section ol { margin: 1rem 0 1rem 1.5rem; color: #ccc; }
    .doc-section li { margin-bottom: 0.5rem; line-height: 1.6; }
    .doc-section code { background: rgba(255, 68, 68, 0.15); padding: 0.2rem 0.5rem; border-radius: 4px; font-family: 'Consolas', monospace; font-size: 0.9rem; color: #ff8888; }
    .doc-section pre { background: #0d0d0d; border: 1px solid #333; border-radius: 10px; padding: 1.5rem; overflow-x: auto; margin: 1rem 0; }
    .doc-section pre code { background: none; padding: 0; color: #88ff88; font-size: 0.85rem; }
    .info-box { background: rgba(0, 150, 255, 0.1); border: 1px solid rgba(0, 150, 255, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .info-box h4 { color: #00aaff; margin-bottom: 0.5rem; }
    .warning-box { background: rgba(255, 200, 0, 0.1); border: 1px solid rgba(255, 200, 0, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .warning-box h4 { color: #ffcc00; margin-bottom: 0.5rem; }
    .tool-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 10px; padding: 1.5rem; margin: 1rem 0; }
    .tool-card h4 { color: #ff6666; margin-bottom: 0.5rem; }
    .methodology-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
    .method-item { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 68, 68, 0.2); border-radius: 8px; padding: 1rem; }
    .method-item h5 { color: #ff8888; margin-bottom: 0.3rem; }
    .method-item p { font-size: 0.9rem; margin: 0; }
</style>

<div class="doc-section">
    <h1>Testing Techniques</h1>
    <p>
        This section covers methodologies and tools for identifying IDOR vulnerabilities during 
        security assessments and penetration testing engagements.
    </p>

    <h2>Testing Methodology</h2>

    <div class="methodology-grid">
        <div class="method-item">
            <h5>1. Identify Object References</h5>
            <p>Map all parameters that reference objects (IDs, GUIDs, filenames)</p>
        </div>
        <div class="method-item">
            <h5>2. Create Test Accounts</h5>
            <p>Use multiple accounts at same privilege level</p>
        </div>
        <div class="method-item">
            <h5>3. Capture Valid Requests</h5>
            <p>Record legitimate requests for owned resources</p>
        </div>
        <div class="method-item">
            <h5>4. Modify Object References</h5>
            <p>Replace IDs with other users' object IDs</p>
        </div>
        <div class="method-item">
            <h5>5. Analyze Responses</h5>
            <p>Check if unauthorized access was granted</p>
        </div>
        <div class="method-item">
            <h5>6. Test All Operations</h5>
            <p>CRUD: Create, Read, Update, Delete</p>
        </div>
    </div>

    <h2>Manual Testing Approach</h2>

    <h3>Step 1: Reconnaissance</h3>
    <p>Identify endpoints that use direct object references:</p>
    <pre><code># Common patterns to look for:
GET /api/users/{id}
GET /profile?user_id=123
POST /banner/delete?id=456
GET /document/download/{document_id}
PUT /account/{account_number}
DELETE /order?orderId=789</code></pre>

    <h3>Step 2: Create Test Data</h3>
    <pre><code># Test Account A (Attacker)
Username: attacker_test
Resources: Profile ID 100, Order ID 200, Document ID 300

# Test Account B (Victim)
Username: victim_test  
Resources: Profile ID 101, Order ID 201, Document ID 301</code></pre>

    <h3>Step 3: Parameter Manipulation</h3>
    <pre><code># Horizontal IDOR Test
# As User A, try to access User B's resources

# Original request (User A viewing own profile)
GET /profile?user_id=100

# Modified request (User A trying to view User B's profile)
GET /profile?user_id=101

# Check response:
# - 200 OK with User B's data = VULNERABLE
# - 403 Forbidden = SECURE
# - 404 Not Found = MAY BE SECURE (could be enumeration protection)</code></pre>

    <h2>Testing Tools</h2>

    <div class="tool-card">
        <h4>🔧 Burp Suite</h4>
        <p>The gold standard for web application testing. Use Intruder for automated ID enumeration:</p>
        <pre><code># Burp Intruder settings:
Attack type: Sniper
Payload position: banner-delete.php?id=§123§
Payload type: Numbers (1-1000)
Payload options: Sequential, step 1

# Analyze results:
- Different response lengths may indicate successful access
- 200 vs 403 status codes reveal authorization decisions
- Response times may leak information</code></pre>
    </div>

    <div class="tool-card">
        <h4>🔧 OWASP ZAP</h4>
        <p>Free alternative to Burp Suite with automated IDOR detection:</p>
        <pre><code># ZAP Active Scan:
1. Spider the application while authenticated
2. Run Active Scan with "Access Control Testing" enabled
3. Review alerts for "Access Control Issue" findings

# ZAP Fuzzer:
1. Right-click on request with ID parameter
2. Select "Fuzz" and add payload list
3. Add list of sequential IDs or other users' IDs
4. Analyze results for unauthorized access</code></pre>
    </div>

    <div class="tool-card">
        <h4>🔧 Autorize (Burp Extension)</h4>
        <p>Automatically detects authorization issues:</p>
        <pre><code># Setup:
1. Install Autorize from BApp Store
2. Set "low privilege" session cookie
3. Browse application as admin/high-privilege user
4. Autorize replays requests with low-privilege session
5. Compares responses to detect authorization bypass

# Results interpretation:
- Green: Access properly denied
- Red: Unauthorized access possible (IDOR!)
- Yellow: Potential issue, needs manual review</code></pre>
    </div>

    <h2>Automated Testing Scripts</h2>

    <h3>Python IDOR Scanner</h3>
    <pre><code class="language-python">import requests
from concurrent.futures import ThreadPoolExecutor

class IDORScanner:
    def __init__(self, base_url, session_cookie):
        self.base_url = base_url
        self.session = requests.Session()
        self.session.cookies.set('PHPSESSID', session_cookie)
    
    def test_endpoint(self, endpoint, id_param, test_id):
        """Test a single ID for IDOR"""
        url = f"{self.base_url}/{endpoint}?{id_param}={test_id}"
        response = self.session.get(url)
        
        return {
            'id': test_id,
            'status': response.status_code,
            'length': len(response.content),
            'potential_idor': response.status_code == 200
        }
    
    def scan_range(self, endpoint, id_param, start_id, end_id):
        """Scan a range of IDs"""
        results = []
        with ThreadPoolExecutor(max_workers=10) as executor:
            futures = [
                executor.submit(self.test_endpoint, endpoint, id_param, i)
                for i in range(start_id, end_id + 1)
            ]
            for future in futures:
                results.append(future.result())
        
        # Filter potential IDORs
        vulnerable = [r for r in results if r['potential_idor']]
        return vulnerable

# Usage
scanner = IDORScanner(
    "http://localhost/AC/lab14",
    "your_session_id"
)

# Scan banner IDs 1-20
vulnerable_ids = scanner.scan_range(
    "banner-delete.php",
    "bannerid",
    1, 20
)
print(f"Potentially vulnerable IDs: {vulnerable_ids}")</code></pre>

    <h3>Burp Suite Macro for IDOR Testing</h3>
    <pre><code># Intruder Payload Processing Rule
# Use when IDs are encoded or hashed

# For base64 encoded IDs:
Payload Processing Rule: Encode → Base64

# For MD5 hashed IDs:
Payload Processing Rule: Hash → MD5

# Example payloads.txt:
1
2
3
...
1000</code></pre>

    <h2>Edge Cases to Test</h2>

    <div class="warning-box">
        <h4>🔍 Don't Forget These Test Cases</h4>
        <ul>
            <li><strong>Negative IDs:</strong> <code>?id=-1</code> - May cause SQL errors revealing info</li>
            <li><strong>Zero ID:</strong> <code>?id=0</code> - May return default/admin records</li>
            <li><strong>Large IDs:</strong> <code>?id=999999999</code> - Test integer overflow</li>
            <li><strong>String IDs:</strong> <code>?id=admin</code> - Test type confusion</li>
            <li><strong>Array IDs:</strong> <code>?id[]=1&id[]=2</code> - Mass assignment</li>
            <li><strong>Encoded IDs:</strong> Test base64, hex, URL encoding</li>
            <li><strong>GUID format:</strong> Try UUIDs from other users</li>
            <li><strong>Wildcard:</strong> <code>?id=*</code> - May return all records</li>
        </ul>
    </div>

    <h2>Response Analysis</h2>
    <p>When testing for IDOR, analyze responses carefully:</p>
    
    <pre><code># Response patterns to identify:

1. Successful IDOR (Vulnerable):
   Status: 200 OK
   Body: Contains victim's data
   → Application is vulnerable!

2. Proper Authorization (Secure):
   Status: 403 Forbidden
   Body: "Access Denied" message
   → Access control working

3. Resource Not Found:
   Status: 404 Not Found
   Body: Generic error
   → May be secure, or ID doesn't exist

4. Enumeration Possible:
   Status: 200 OK (own resource) vs 404 (others)
   → Can enumerate valid IDs even if can't access

5. Timing Attack:
   200ms response (authorized) vs 50ms (not found)
   → Response time reveals if resource exists</code></pre>

    <div class="info-box">
        <h4>💡 Pro Tip</h4>
        <p>
            Always compare the response of your own resource vs. another user's resource. 
            Subtle differences in response length, headers, or timing may reveal authorization 
            issues even when status codes are the same.
        </p>
    </div>
</div>
