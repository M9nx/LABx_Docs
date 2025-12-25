<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Guide - IDOR Slowvote Bypass</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
            padding: 1rem 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            color: #9370DB;
            text-decoration: none;
        }
        .logo-icon {
            background: linear-gradient(135deg, #9370DB, #6A5ACD);
            color: #fff;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; }
        .nav-links a:hover { color: #9370DB; }
        .docs-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            padding-top: 70px;
        }
        .sidebar {
            background: rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(106, 90, 205, 0.3);
            padding: 2rem 0;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .sidebar h3 {
            color: #9370DB;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }
        .sidebar-nav { list-style: none; }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #aaa;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(147, 112, 219, 0.1);
            color: #9370DB;
            border-left-color: #9370DB;
        }
        .sidebar-nav a.sub-item { padding-left: 2.5rem; font-size: 0.9rem; }
        .main-content {
            padding: 2rem 3rem;
            max-width: 900px;
        }
        .page-title {
            margin-bottom: 2rem;
        }
        .page-title h1 {
            color: #9370DB;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #9370DB;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(106, 90, 205, 0.3);
        }
        .section h3 {
            color: #b794f4;
            margin: 1.5rem 0 0.75rem;
        }
        .section p, .section li { line-height: 1.8; color: #ccc; margin-bottom: 0.75rem; }
        .section ul, .section ol { padding-left: 1.5rem; }
        .code-block {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
            color: #88ff88;
            overflow-x: auto;
        }
        .tip-box {
            background: rgba(0, 200, 0, 0.1);
            border: 1px solid rgba(0, 200, 0, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .info-box {
            background: rgba(0, 150, 255, 0.1);
            border: 1px solid rgba(0, 150, 255, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid rgba(106, 90, 205, 0.3);
        }
        th { background: rgba(147, 112, 219, 0.2); color: #9370DB; }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(106, 90, 205, 0.3);
        }
        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            background: rgba(147, 112, 219, 0.2);
            color: #9370DB;
            transition: all 0.3s;
        }
        .nav-btn:hover { background: rgba(147, 112, 219, 0.4); }
        .tool-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .tool-card h4 {
            color: #9370DB;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">P</span>
                Phabricator
            </a>
            <nav class="nav-links">
                <a href="../index.php">← All Labs</a>
                <a href="index.php">Lab Home</a>
                <a href="login.php">Start Lab</a>
            </nav>
        </div>
    </header>

    <div class="docs-layout">
        <nav class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">Overview</a></li>
                <li><a href="docs-vulnerability.php">The Vulnerability</a></li>
                <li><a href="docs-vulnerability.php#auth-vs-authz" class="sub-item">Auth vs AuthZ</a></li>
                <li><a href="docs-vulnerability.php#api-flaw" class="sub-item">API Design Flaw</a></li>
                <li><a href="docs-exploitation.php">Exploitation Guide</a></li>
                <li><a href="docs-exploitation.php#step-by-step" class="sub-item">Step by Step</a></li>
                <li><a href="docs-exploitation.php#payloads" class="sub-item">Attack Payloads</a></li>
                <li><a href="docs-prevention.php">Prevention</a></li>
                <li><a href="docs-prevention.php#secure-code" class="sub-item">Secure Code</a></li>
                <li><a href="docs-prevention.php#best-practices" class="sub-item">Best Practices</a></li>
                <li><a href="docs-testing.php" class="active">Testing Guide</a></li>
                <li><a href="docs-references.php">References</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="page-title">
                <h1>Testing Guide</h1>
                <p style="color: #888;">Methods and tools for identifying IDOR vulnerabilities</p>
            </div>

            <div class="section" id="methodology">
                <h2>🔍 Testing Methodology</h2>
                
                <h3>1. Identify Object References</h3>
                <p>Look for parameters that reference objects:</p>
                <ul>
                    <li>URL parameters: <code>?id=123</code>, <code>?poll_id=5</code></li>
                    <li>POST body: <code>{"user_id": 42}</code></li>
                    <li>Headers: <code>X-Resource-ID: abc123</code></li>
                    <li>Cookies: <code>selected_account=1001</code></li>
                </ul>

                <h3>2. Map Access Control</h3>
                <p>Document what each user role should be able to access:</p>
                <table>
                    <tr>
                        <th>Resource</th>
                        <th>Admin</th>
                        <th>Creator</th>
                        <th>Permitted User</th>
                        <th>Other User</th>
                    </tr>
                    <tr>
                        <td>Public Poll</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                    </tr>
                    <tr>
                        <td>Specific Poll</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>❌</td>
                    </tr>
                    <tr>
                        <td>Private Poll</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>❌</td>
                        <td>❌</td>
                    </tr>
                </table>

                <h3>3. Test Horizontal Privilege Escalation</h3>
                <p>Try accessing another user's resources with the same role:</p>
                <div class="code-block">
# As User A, access User B's private resource
curl -X GET "http://target/api/poll/42" \
  -H "Cookie: session=user_a_session"
  
# Expected: 403 Forbidden
# Vulnerable: 200 OK with data</div>

                <h3>4. Test Vertical Privilege Escalation</h3>
                <p>Try accessing admin-only resources as a regular user:</p>
                <div class="code-block">
# As regular user, access admin endpoint
curl -X GET "http://target/api/admin/users" \
  -H "Cookie: session=regular_user_session"
  
# Expected: 403 Forbidden
# Vulnerable: 200 OK with user list</div>
            </div>

            <div class="section" id="tools">
                <h2>🛠️ Testing Tools</h2>
                
                <div class="tool-card">
                    <h4>Burp Suite</h4>
                    <p>Use the Autorize extension to automatically test authorization:</p>
                    <ul>
                        <li>Configure low-privilege session token</li>
                        <li>Browse as admin, Autorize replays with low-priv token</li>
                        <li>Highlights responses that shouldn't be accessible</li>
                    </ul>
                </div>

                <div class="tool-card">
                    <h4>OWASP ZAP</h4>
                    <p>Use Access Control Testing add-on:</p>
                    <ul>
                        <li>Define user contexts (admin, user, guest)</li>
                        <li>Scan automatically tests cross-context access</li>
                        <li>Reports unauthorized access issues</li>
                    </ul>
                </div>

                <div class="tool-card">
                    <h4>Postman</h4>
                    <p>Manual testing with collections:</p>
                    <ul>
                        <li>Create environment per user role</li>
                        <li>Run same requests across environments</li>
                        <li>Compare responses for access control issues</li>
                    </ul>
                </div>
            </div>

            <div class="section" id="automated-tests">
                <h2>🤖 Automated Testing</h2>
                
                <h3>Python Test Script</h3>
                <div class="code-block">
import requests

class IDORTester:
    def __init__(self, base_url):
        self.base_url = base_url
        self.sessions = {}
    
    def login(self, username, password, role):
        """Create authenticated session for a user role"""
        session = requests.Session()
        session.post(f"{self.base_url}/login.php", 
                    data={'username': username, 'password': password})
        self.sessions[role] = session
        return session
    
    def test_idor(self, endpoint, param, test_values, expected_access):
        """
        Test IDOR vulnerability
        expected_access = {'admin': True, 'user': False, 'guest': False}
        """
        results = []
        for value in test_values:
            url = f"{self.base_url}/{endpoint}?{param}={value}"
            for role, session in self.sessions.items():
                resp = session.get(url)
                has_access = resp.status_code == 200 and 'error' not in resp.text
                
                expected = expected_access.get(role, False)
                status = 'PASS' if has_access == expected else 'FAIL'
                
                results.append({
                    'resource': value,
                    'role': role,
                    'expected': expected,
                    'actual': has_access,
                    'status': status
                })
        return results

# Usage
tester = IDORTester("http://localhost/AC/lab16")
tester.login("admin", "admin123", "admin")
tester.login("bob", "bob123", "user")

results = tester.test_idor(
    endpoint="api/slowvote.php",
    param="poll_id",
    test_values=[1, 2, 3, 4],
    expected_access={'admin': True, 'user': False}
)

for r in results:
    print(f"{r['status']}: {r['role']} accessing poll {r['resource']}")
                </div>

                <h3>Integration Test (PHPUnit)</h3>
                <div class="code-block">
class IDORTest extends TestCase
{
    public function testApiEnforcesVisibility()
    {
        // Login as bob (no permissions)
        $this->actingAs(User::where('username', 'bob')->first());
        
        // Try to access private poll via API
        $response = $this->postJson('/api/slowvote.php', [
            'action' => 'info',
            'poll_id' => 2  // Private poll
        ]);
        
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Forbidden']);
    }
    
    public function testCreatorCanAccessOwnPoll()
    {
        $this->actingAs(User::where('username', 'alice')->first());
        
        $response = $this->postJson('/api/slowvote.php', [
            'action' => 'info',
            'poll_id' => 2  // Alice's private poll
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['poll' => ['id', 'title']]);
    }
}
                </div>
            </div>

            <div class="section" id="checklist">
                <h2>📋 IDOR Testing Checklist</h2>
                <ul>
                    <li>☐ Identify all endpoints that accept object references</li>
                    <li>☐ Document expected access control for each resource type</li>
                    <li>☐ Test with multiple user accounts (different roles)</li>
                    <li>☐ Test both GET and POST methods</li>
                    <li>☐ Try sequential ID enumeration (1, 2, 3...)</li>
                    <li>☐ Try predictable IDs (UUIDs, hashes)</li>
                    <li>☐ Test API endpoints separately from UI</li>
                    <li>☐ Check for information disclosure in error messages</li>
                    <li>☐ Verify rate limiting prevents enumeration</li>
                    <li>☐ Test with unauthenticated requests</li>
                    <li>☐ Check mobile API endpoints separately</li>
                    <li>☐ Review audit logs for access attempts</li>
                </ul>
            </div>

            <div class="nav-buttons">
                <a href="docs-prevention.php" class="nav-btn">← Prevention</a>
                <a href="docs-references.php" class="nav-btn">References →</a>
            </div>
        </main>
    </div>
</body>
</html>
