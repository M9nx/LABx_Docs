<h1 class="doc-title">References & Resources</h1>
<p class="doc-subtitle">Further reading and authoritative sources on IDOR and access control</p>

<div class="section">
    <h2>OWASP Resources</h2>
    <table>
        <tr>
            <th>Resource</th>
            <th>Description</th>
        </tr>
        <tr>
            <td><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank" style="color: #ffcc00;">OWASP Top 10 - A01:2021</a></td>
            <td>Broken Access Control - the #1 web application security risk</td>
        </tr>
        <tr>
            <td><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" target="_blank" style="color: #ffcc00;">OWASP Testing Guide - IDOR</a></td>
            <td>Comprehensive testing methodology for IDOR vulnerabilities</td>
        </tr>
        <tr>
            <td><a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" target="_blank" style="color: #ffcc00;">Authorization Cheat Sheet</a></td>
            <td>Best practices for implementing authorization</td>
        </tr>
        <tr>
            <td><a href="https://owasp.org/www-project-api-security/" target="_blank" style="color: #ffcc00;">OWASP API Security Project</a></td>
            <td>API-specific security risks and mitigations</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>CWE References</h2>
    <table>
        <tr>
            <th>CWE ID</th>
            <th>Name</th>
            <th>Relevance</th>
        </tr>
        <tr>
            <td><a href="https://cwe.mitre.org/data/definitions/639.html" target="_blank" style="color: #ffcc00;">CWE-639</a></td>
            <td>Authorization Bypass Through User-Controlled Key</td>
            <td>Primary classification for this vulnerability</td>
        </tr>
        <tr>
            <td><a href="https://cwe.mitre.org/data/definitions/284.html" target="_blank" style="color: #ffcc00;">CWE-284</a></td>
            <td>Improper Access Control</td>
            <td>Parent category for access control issues</td>
        </tr>
        <tr>
            <td><a href="https://cwe.mitre.org/data/definitions/862.html" target="_blank" style="color: #ffcc00;">CWE-862</a></td>
            <td>Missing Authorization</td>
            <td>When authorization checks are absent</td>
        </tr>
        <tr>
            <td><a href="https://cwe.mitre.org/data/definitions/863.html" target="_blank" style="color: #ffcc00;">CWE-863</a></td>
            <td>Incorrect Authorization</td>
            <td>When authorization logic is flawed</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Real-World Case Studies</h2>
    <p>Notable IDOR vulnerabilities discovered in production systems:</p>
    
    <div class="note-box">
        <h4>📱 MTN MobAd (Inspiration for this Lab)</h4>
        <p>
            The getUserNotes endpoint at mtnmobad.mtnbusiness.com.ng allowed any authenticated 
            user to retrieve phone numbers and account details of other users by modifying the 
            userEmail parameter in API requests.
        </p>
    </div>
    
    <table>
        <tr>
            <th>Company</th>
            <th>Vulnerability</th>
            <th>Impact</th>
        </tr>
        <tr>
            <td>Facebook (2019)</td>
            <td>IDOR in account recovery</td>
            <td>Phone numbers of millions of users exposed</td>
        </tr>
        <tr>
            <td>First American (2019)</td>
            <td>Sequential document IDs</td>
            <td>885 million financial records exposed</td>
        </tr>
        <tr>
            <td>Uber (2016)</td>
            <td>IDOR in driver API</td>
            <td>Personal data of 50,000+ drivers leaked</td>
        </tr>
        <tr>
            <td>Twitch (2015)</td>
            <td>IDOR in user settings</td>
            <td>Email addresses of all users accessible</td>
        </tr>
        <tr>
            <td>Parler (2021)</td>
            <td>Sequential post IDs + no auth</td>
            <td>Entire platform's data archived before shutdown</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Security Standards</h2>
    <table>
        <tr>
            <th>Standard</th>
            <th>Relevant Requirements</th>
        </tr>
        <tr>
            <td>PCI DSS</td>
            <td>Requirement 6.5.8 - Improper access control</td>
        </tr>
        <tr>
            <td>GDPR</td>
            <td>Article 32 - Security of processing (PII protection)</td>
        </tr>
        <tr>
            <td>HIPAA</td>
            <td>Technical Safeguards - Access Control (164.312(a)(1))</td>
        </tr>
        <tr>
            <td>SOC 2</td>
            <td>CC6.1 - Logical access security</td>
        </tr>
        <tr>
            <td>ISO 27001</td>
            <td>A.9.4.1 - Information access restriction</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Recommended Reading</h2>
    
    <h3>Books</h3>
    <ul>
        <li><strong>The Web Application Hacker's Handbook</strong> - Stuttard & Pinto (Chapter on Access Controls)</li>
        <li><strong>OWASP Testing Guide v4</strong> - Free online resource</li>
        <li><strong>API Security in Action</strong> - Neil Madden</li>
        <li><strong>Hacking APIs</strong> - Corey Ball</li>
    </ul>
    
    <h3>Online Courses</h3>
    <ul>
        <li>PortSwigger Web Security Academy - Access Control Labs</li>
        <li>HackTheBox Academy - Broken Access Control Module</li>
        <li>PentesterLab - IDOR Badge</li>
    </ul>
    
    <h3>Bug Bounty Write-ups</h3>
    <ul>
        <li>HackerOne Disclosed Reports (filter by "IDOR")</li>
        <li>Bugcrowd University - IDOR Module</li>
        <li>Medium/InfoSec Write-ups - Tagged IDOR</li>
    </ul>
</div>

<div class="section">
    <h2>Tools</h2>
    <table>
        <tr>
            <th>Tool</th>
            <th>Purpose</th>
        </tr>
        <tr>
            <td><a href="https://portswigger.net/burp" target="_blank" style="color: #ffcc00;">Burp Suite</a></td>
            <td>Industry-standard web security testing platform</td>
        </tr>
        <tr>
            <td><a href="https://github.com/PortSwigger/autorize" target="_blank" style="color: #ffcc00;">Autorize</a></td>
            <td>Burp extension for authorization testing</td>
        </tr>
        <tr>
            <td><a href="https://www.zaproxy.org/" target="_blank" style="color: #ffcc00;">OWASP ZAP</a></td>
            <td>Free alternative to Burp Suite</td>
        </tr>
        <tr>
            <td><a href="https://github.com/assetnote/kiterunner" target="_blank" style="color: #ffcc00;">Kiterunner</a></td>
            <td>API discovery and enumeration</td>
        </tr>
        <tr>
            <td><a href="https://github.com/s0md3v/Arjun" target="_blank" style="color: #ffcc00;">Arjun</a></td>
            <td>HTTP parameter discovery</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Related Labs in This Series</h2>
    <p>Continue your access control learning journey:</p>
    <ul>
        <li><a href="../lab1/index.php" style="color: #ffcc00;">Lab 1</a> - Unprotected Admin Panel</li>
        <li><a href="../lab2/index.php" style="color: #ffcc00;">Lab 2</a> - Security Through Obscurity</li>
        <li><a href="../lab3/index.php" style="color: #ffcc00;">Lab 3</a> - Cookie-Based Access Control</li>
        <li><a href="../lab11/index.php" style="color: #ffcc00;">Lab 11</a> - IDOR User ID (Horizontal Privilege Escalation)</li>
        <li><a href="../lab12/index.php" style="color: #ffcc00;">Lab 12</a> - IDOR Unpredictable IDs</li>
        <li><a href="../lab13/index.php" style="color: #ffcc00;">Lab 13</a> - Multi-Step Process IDOR</li>
        <li><a href="../lab14/index.php" style="color: #ffcc00;">Lab 14</a> - IDOR Banner Deletion</li>
    </ul>
</div>

<div class="section">
    <h2>Glossary</h2>
    <table>
        <tr>
            <th>Term</th>
            <th>Definition</th>
        </tr>
        <tr>
            <td><strong>IDOR</strong></td>
            <td>Insecure Direct Object Reference - accessing objects via user-controlled references without authorization</td>
        </tr>
        <tr>
            <td><strong>PII</strong></td>
            <td>Personally Identifiable Information - data that can identify an individual</td>
        </tr>
        <tr>
            <td><strong>Horizontal Privilege Escalation</strong></td>
            <td>Accessing resources of other users with the same privilege level</td>
        </tr>
        <tr>
            <td><strong>Vertical Privilege Escalation</strong></td>
            <td>Accessing resources requiring higher privileges than the attacker has</td>
        </tr>
        <tr>
            <td><strong>Object Reference</strong></td>
            <td>An identifier used to access a specific resource (ID, email, filename, etc.)</td>
        </tr>
        <tr>
            <td><strong>Authorization</strong></td>
            <td>Verifying what actions a user is permitted to perform</td>
        </tr>
        <tr>
            <td><strong>Authentication</strong></td>
            <td>Verifying the identity of a user</td>
        </tr>
    </table>
</div>

<div class="success-box">
    <h4>🎓 Congratulations!</h4>
    <p>
        You've completed the documentation for Lab 15 - IDOR PII Leakage. Apply these concepts 
        when testing real applications, and always remember: authentication without authorization 
        is incomplete security!
    </p>
</div>
