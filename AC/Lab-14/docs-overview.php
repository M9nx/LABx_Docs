<style>
    .doc-section h1 {
        font-size: 2.2rem;
        color: #ff4444;
        margin-bottom: 1rem;
    }
    .doc-section h2 {
        font-size: 1.5rem;
        color: #ff6666;
        margin: 2rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255, 68, 68, 0.3);
    }
    .doc-section h3 {
        font-size: 1.2rem;
        color: #ff8888;
        margin: 1.5rem 0 0.75rem;
    }
    .doc-section p {
        color: #ccc;
        line-height: 1.8;
        margin-bottom: 1rem;
    }
    .doc-section ul, .doc-section ol {
        margin: 1rem 0 1rem 1.5rem;
        color: #ccc;
    }
    .doc-section li {
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }
    .doc-section code {
        background: rgba(255, 68, 68, 0.15);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-family: 'Consolas', monospace;
        font-size: 0.9rem;
        color: #ff8888;
    }
    .doc-section pre {
        background: #0d0d0d;
        border: 1px solid #333;
        border-radius: 10px;
        padding: 1.5rem;
        overflow-x: auto;
        margin: 1rem 0;
    }
    .doc-section pre code {
        background: none;
        padding: 0;
        color: #88ff88;
        font-size: 0.85rem;
    }
    .info-box {
        background: rgba(0, 150, 255, 0.1);
        border: 1px solid rgba(0, 150, 255, 0.4);
        border-radius: 10px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }
    .info-box h4 {
        color: #00aaff;
        margin-bottom: 0.5rem;
    }
    .warning-box {
        background: rgba(255, 200, 0, 0.1);
        border: 1px solid rgba(255, 200, 0, 0.4);
        border-radius: 10px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }
    .warning-box h4 {
        color: #ffcc00;
        margin-bottom: 0.5rem;
    }
    .danger-box {
        background: rgba(255, 68, 68, 0.1);
        border: 1px solid rgba(255, 68, 68, 0.4);
        border-radius: 10px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }
    .danger-box h4 {
        color: #ff6666;
        margin-bottom: 0.5rem;
    }
    .diagram {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 68, 68, 0.3);
        border-radius: 10px;
        padding: 2rem;
        margin: 1.5rem 0;
        font-family: 'Consolas', monospace;
        font-size: 0.85rem;
        text-align: center;
        overflow-x: auto;
    }
    .cwe-tag {
        display: inline-block;
        background: rgba(255, 68, 68, 0.2);
        border: 1px solid #ff4444;
        color: #ff8888;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin: 0.25rem;
    }
    .owasp-tag {
        display: inline-block;
        background: rgba(0, 200, 0, 0.2);
        border: 1px solid #00cc00;
        color: #66ff66;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin: 0.25rem;
    }
</style>

<div class="doc-section">
    <h1>IDOR Overview</h1>
    <p>
        Insecure Direct Object Reference (IDOR) is a type of access control vulnerability that occurs 
        when an application exposes internal implementation objects to users without proper authorization 
        verification. This enables attackers to access, modify, or delete resources belonging to other users.
    </p>

    <div style="margin: 1.5rem 0;">
        <span class="cwe-tag">CWE-639: Authorization Bypass</span>
        <span class="owasp-tag">OWASP Top 10: A01 Broken Access Control</span>
    </div>

    <h2>What is IDOR?</h2>
    <p>
        IDOR occurs when an application uses user-controllable input (such as a URL parameter, form field, 
        or API request body) to directly access objects like database records, files, or functions. The 
        vulnerability arises when the application fails to verify that the authenticated user has permission 
        to access the specific object they're requesting.
    </p>

    <div class="diagram">
        <pre style="color: #88ff88; margin: 0; text-align: left;">
┌─────────────────────────────────────────────────────────────────────────────┐
│                          IDOR Vulnerability Flow                             │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│    Legitimate Request:                                                       │
│    ┌──────────┐     GET /banner?id=1      ┌──────────┐      ┌─────────────┐ │
│    │  User A  │ ───────────────────────►  │  Server  │ ───► │  Banner #1  │ │
│    │ (Owner)  │                           │          │      │  (User A's) │ │
│    └──────────┘                           └──────────┘      └─────────────┘ │
│         │                                                                    │
│         │ User A owns Banner #1 ─ Access Granted ✓                          │
│         │                                                                    │
│    ─────┼────────────────────────────────────────────────────────────────── │
│         │                                                                    │
│    Malicious Request:                                                        │
│    ┌──────────┐     GET /banner?id=5      ┌──────────┐      ┌─────────────┐ │
│    │  User A  │ ───────────────────────►  │  Server  │ ───► │  Banner #5  │ │
│    │(Attacker)│                           │   No     │      │  (User B's) │ │
│    └──────────┘                           │  Owner   │      └─────────────┘ │
│                                           │  Check!  │                       │
│         User A accesses User B's resource ─ IDOR Vulnerability! ✗           │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
        </pre>
    </div>

    <h2>Types of IDOR</h2>
    
    <h3>1. Horizontal IDOR</h3>
    <p>
        User A accesses resources belonging to User B at the same privilege level. This is the most 
        common form of IDOR and is demonstrated in this lab.
    </p>
    <pre><code># User A tries to view User B's profile
GET /profile?user_id=200   # User A is ID 100, accessing ID 200</code></pre>

    <h3>2. Vertical IDOR</h3>
    <p>
        A regular user accesses administrative or higher-privileged functionality.
    </p>
    <pre><code># Regular user tries to access admin panel
GET /admin/delete-user?id=50</code></pre>

    <h3>3. Object Level IDOR</h3>
    <p>
        Manipulation of object identifiers that may include UUIDs, hashes, or encoded values.
    </p>
    <pre><code># Accessing document by encoded reference
GET /document?ref=base64(user_id:doc_id)</code></pre>

    <h2>Real-World Impact</h2>
    <p>
        This lab is based on a real vulnerability in Revive Adserver, where the banner deletion endpoint 
        allowed managers to delete banners belonging to other managers. The vulnerability existed because:
    </p>
    <ul>
        <li>The code validated access to the <strong>client</strong> parameter</li>
        <li>The code validated that <strong>campaign</strong> belonged to the client</li>
        <li>The code <strong>failed to validate</strong> that the banner belonged to the campaign</li>
    </ul>

    <div class="danger-box">
        <h4>⚠️ Critical Security Flaw</h4>
        <p>
            This pattern is extremely common in hierarchical data structures. Developers often validate 
            access to parent objects but forget to validate the relationship between child objects and 
            their parents. Just because you can access a parent doesn't mean you can access any child 
            by ID alone.
        </p>
    </div>

    <h2>Why IDOR Occurs</h2>
    <ul>
        <li><strong>Implicit Trust:</strong> Assuming users won't manipulate IDs or parameters</li>
        <li><strong>Missing Authorization:</strong> No ownership verification at the object level</li>
        <li><strong>Predictable Identifiers:</strong> Sequential integers make enumeration easy</li>
        <li><strong>Partial Validation:</strong> Checking parent access but not child ownership</li>
        <li><strong>Client-Side Trust:</strong> Relying on JavaScript or UI to hide unauthorized options</li>
    </ul>

    <div class="info-box">
        <h4>💡 Lab Context</h4>
        <p>
            In this lab, you'll exploit an IDOR vulnerability where the banner deletion endpoint validates 
            your access to a client and campaign but doesn't verify that the banner ID actually belongs 
            to that campaign. This allows you to delete any banner in the system by knowing just its ID.
        </p>
    </div>
</div>
