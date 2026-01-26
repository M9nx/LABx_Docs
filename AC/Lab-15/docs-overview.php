<h1 class="doc-title">IDOR PII Leakage</h1>
<p class="doc-subtitle">Understanding API-based IDOR vulnerabilities that expose personal data</p>

<div class="section">
    <h2>Introduction</h2>
    <p>
        This lab simulates a real-world vulnerability discovered in MTN MobAd platform, an advertising 
        management system. The vulnerability allows authenticated users to access any other user's 
        personally identifiable information (PII) through an API endpoint that fails to verify data 
        ownership.
    </p>
    <p>
        Insecure Direct Object Reference (IDOR) vulnerabilities occur when an application provides 
        direct access to objects based on user-supplied input without proper authorization checks. 
        In this case, the object reference is an email address used to query user data.
    </p>
</div>

<div class="section">
    <h2>What is PII?</h2>
    <p>
        Personally Identifiable Information (PII) is any data that can be used to identify a specific 
        individual. This lab demonstrates exposure of several PII categories:
    </p>
    <ul>
        <li><strong>Contact Information:</strong> Phone numbers, email addresses</li>
        <li><strong>Physical Information:</strong> Home addresses, office locations</li>
        <li><strong>Financial Data:</strong> Bank account numbers, tax IDs, billing information</li>
        <li><strong>Business Secrets:</strong> API keys, confidential notes, strategic plans</li>
        <li><strong>Account Data:</strong> User preferences, notification settings, account metadata</li>
    </ul>
</div>

<div class="section">
    <h2>The Vulnerable Scenario</h2>
    <p>
        MTN MobAd platform provides an API endpoint for retrieving user notes and profile data. The 
        endpoint is designed to allow users to fetch their own data, but the implementation contains 
        a critical flaw:
    </p>
    
    <div class="code-block">
<span class="comment">// API Endpoint: POST /api/getUserNotes.php</span>
<span class="comment">// Expected: Returns logged-in user's data</span>
<span class="comment">// Actual: Returns ANY user's data based on email parameter</span>

{
  "params": {
    "updates": [{
      "param": "user",
      "value": { "userEmail": "<span class="string">any-email@example.com</span>" },
      "op": "a"
    }]
  }
}
    </div>
    
    <div class="warning-box">
        <h4>⚠️ The Core Problem</h4>
        <p>
            The server checks if the user is <strong>authenticated</strong> (logged in), but never 
            verifies if they are <strong>authorized</strong> to access the requested email's data. 
            Authentication ≠ Authorization!
        </p>
    </div>
</div>

<div class="section">
    <h2>Real-World Impact</h2>
    <p>
        When discovered in production systems, IDOR vulnerabilities exposing PII can lead to:
    </p>
    <div class="impact-grid">
        <div class="impact-item">
            <h4>Identity Theft</h4>
            <p>Attackers can gather enough data to impersonate victims</p>
        </div>
        <div class="impact-item">
            <h4>Financial Fraud</h4>
            <p>Bank accounts and tax IDs enable financial crimes</p>
        </div>
        <div class="impact-item">
            <h4>Social Engineering</h4>
            <p>Personal details make phishing attacks more convincing</p>
        </div>
        <div class="impact-item">
            <h4>Corporate Espionage</h4>
            <p>Business secrets and strategies can be leaked</p>
        </div>
        <div class="impact-item">
            <h4>Regulatory Fines</h4>
            <p>GDPR, CCPA violations can result in massive penalties</p>
        </div>
        <div class="impact-item">
            <h4>Reputation Damage</h4>
            <p>Data breaches destroy customer trust</p>
        </div>
    </div>
</div>

<div class="section">
    <h2>Lab Objectives</h2>
    <ol>
        <li>Log in to the platform as an attacker</li>
        <li>Discover the vulnerable API endpoint</li>
        <li>Understand the request structure and identify the IDOR parameter</li>
        <li>Modify the request to access another user's data</li>
        <li>Successfully retrieve PII belonging to a victim</li>
    </ol>
    
    <div class="note-box">
        <h4>💡 Learning Outcome</h4>
        <p>
            After completing this lab, you will understand how IDOR vulnerabilities in API endpoints 
            can expose sensitive user data, why checking authentication alone is insufficient, and 
            how to identify similar vulnerabilities in real applications.
        </p>
    </div>
</div>

<div class="section">
    <h2>Vulnerability Classification</h2>
    <table>
        <tr>
            <th>Attribute</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Vulnerability Type</td>
            <td>Insecure Direct Object Reference (IDOR)</td>
        </tr>
        <tr>
            <td>CWE ID</td>
            <td>CWE-639: Authorization Bypass Through User-Controlled Key</td>
        </tr>
        <tr>
            <td>OWASP Category</td>
            <td>A01:2021 - Broken Access Control</td>
        </tr>
        <tr>
            <td>Impact</td>
            <td>High (PII Exposure, Privacy Violation)</td>
        </tr>
        <tr>
            <td>Exploitability</td>
            <td>Easy (No special tools required)</td>
        </tr>
    </table>
</div>
