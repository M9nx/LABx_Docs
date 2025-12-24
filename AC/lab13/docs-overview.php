<div class="doc-header">
    <h1>Referer-based Access Control</h1>
    <p>Understanding HTTP header-based authorization vulnerabilities</p>
</div>

<div class="content-section">
    <h2>What is the Referer Header?</h2>
    <p>
        The <strong>HTTP Referer header</strong> (originally a misspelling of "Referrer" that became 
        standardized) is sent by web browsers to indicate the URL of the page that linked to the 
        currently requested resource. It's primarily designed for analytics, logging, and caching 
        optimization purposes.
    </p>
    
    <div class="code-block">
        <span class="code-label">HTTP Request</span>
        <code>GET /admin-roles.php?username=carlos&action=upgrade HTTP/1.1
Host: vulnerable-site.com
Cookie: session=abc123
Referer: http://vulnerable-site.com/admin</code>
    </div>
    
    <p>
        In this example, the Referer header indicates that the request to <code>/admin-roles.php</code> 
        was initiated from the <code>/admin</code> page. However, this header is <strong>entirely 
        client-controlled</strong> and can be trivially modified or spoofed.
    </p>
</div>

<div class="content-section">
    <h2>The Problem with Referer-based Security</h2>
    <p>
        Some applications mistakenly use the Referer header as a security mechanism, assuming that:
    </p>
    <ul>
        <li>If the Referer points to an admin page, the user must be an admin</li>
        <li>If the Referer is absent, the request is illegitimate</li>
        <li>The Referer header cannot be manipulated by attackers</li>
    </ul>
    
    <div class="info-box danger">
        <h4>⚠️ Critical Misconception</h4>
        <p>
            <strong>All of these assumptions are fundamentally flawed.</strong> HTTP headers are 
            client-controlled data and must never be trusted for authorization decisions. An 
            attacker can set any Referer header value they want using tools like Burp Suite, 
            curl, or custom scripts.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>How This Lab Works</h2>
    <p>
        In this lab, the admin panel at <code>/admin</code> allows administrators to upgrade 
        or downgrade user roles. When an admin clicks to upgrade a user, it sends a request to 
        <code>/admin-roles.php</code> with the Referer header set to the admin page.
    </p>
    
    <div class="diagram">
        <div class="diagram-row">
            <div class="diagram-box">Admin Panel<br>/admin</div>
            <span class="diagram-arrow">→</span>
            <div class="diagram-box">Role Endpoint<br>/admin-roles.php</div>
        </div>
        <p style="color: #888; margin-top: 1rem;">
            The endpoint checks Referer header contains '/admin'<br>
            but doesn't verify actual admin privileges!
        </p>
    </div>
    
    <p>
        The vulnerable code only checks if the Referer header contains the string '/admin':
    </p>
    
    <div class="code-block">
        <span class="code-label">Vulnerable Code</span>
        <code><span style="color: #ff6666;">// VULNERABLE: Referer-based access control</span>
$referer = $_SERVER['HTTP_REFERER'] ?? '';

if (strpos($referer, '/admin') === false) {
    die('Unauthorized');
}

<span style="color: #ff6666;">// If Referer contains '/admin', allow the action!</span>
$stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
$stmt->bind_param("ss", $newRole, $username);</code>
    </div>
</div>

<div class="content-section">
    <h2>Attack Scenario</h2>
    <p>
        An attacker can exploit this vulnerability by:
    </p>
    <ol>
        <li>Observing legitimate admin requests (or guessing the endpoint)</li>
        <li>Crafting their own request with a spoofed Referer header</li>
        <li>Using their own session cookie to perform unauthorized actions</li>
    </ol>
    
    <div class="info-box info">
        <h4>💡 Key Insight</h4>
        <p>
            The attacker doesn't need to steal an admin's session. They can use their own 
            (non-privileged) session cookie combined with a spoofed Referer header. The server 
            trusts the Referer and doesn't verify the user's actual role before performing the action.
        </p>
    </div>
</div>
