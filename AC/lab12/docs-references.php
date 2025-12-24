<div class="doc-header">
    <h1>📖 References</h1>
    <p>Further reading and resources on access control vulnerabilities</p>
</div>

<div class="content-section">
    <h2>OWASP Resources</h2>
    
    <h3>Top 10 - Broken Access Control</h3>
    <p>
        <strong>OWASP Top 10 2021: A01 Broken Access Control</strong><br>
        <a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" 
           target="_blank" style="color: #ff6666;">
            https://owasp.org/Top10/A01_2021-Broken_Access_Control/
        </a>
    </p>
    <p>
        Access control enforces policy such that users cannot act outside of their intended 
        permissions. Failures typically lead to unauthorized information disclosure, 
        modification, or destruction of data, or performing business functions outside 
        the user's limits.
    </p>

    <h3>Access Control Cheat Sheet</h3>
    <p>
        <strong>OWASP Access Control Cheat Sheet</strong><br>
        <a href="https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html" 
           target="_blank" style="color: #ff6666;">
            https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html
        </a>
    </p>
    <p>
        Comprehensive guide on implementing proper access control mechanisms in web applications.
    </p>

    <h3>Testing Guide</h3>
    <p>
        <strong>OWASP Testing Guide - Authorization Testing</strong><br>
        <a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/" 
           target="_blank" style="color: #ff6666;">
            https://owasp.org/www-project-web-security-testing-guide/
        </a>
    </p>
</div>

<div class="content-section">
    <h2>CWE Entries</h2>
    
    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="background: rgba(255, 68, 68, 0.2);">
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">CWE ID</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Name</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Relevance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="https://cwe.mitre.org/data/definitions/285.html" target="_blank" style="color: #ff6666;">CWE-285</a></td>
                <td style="padding: 1rem; border: 1px solid #333;">Improper Authorization</td>
                <td style="padding: 1rem; border: 1px solid #333;">Parent category</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="https://cwe.mitre.org/data/definitions/862.html" target="_blank" style="color: #ff6666;">CWE-862</a></td>
                <td style="padding: 1rem; border: 1px solid #333;">Missing Authorization</td>
                <td style="padding: 1rem; border: 1px solid #333;">Exact match for this lab</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="https://cwe.mitre.org/data/definitions/863.html" target="_blank" style="color: #ff6666;">CWE-863</a></td>
                <td style="padding: 1rem; border: 1px solid #333;">Incorrect Authorization</td>
                <td style="padding: 1rem; border: 1px solid #333;">Related weakness</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="https://cwe.mitre.org/data/definitions/269.html" target="_blank" style="color: #ff6666;">CWE-269</a></td>
                <td style="padding: 1rem; border: 1px solid #333;">Improper Privilege Management</td>
                <td style="padding: 1rem; border: 1px solid #333;">Broader category</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="content-section">
    <h2>Real-World Examples</h2>
    
    <h3>CVE Database Entries</h3>
    <p>Similar vulnerabilities have been found in production software:</p>
    <ul>
        <li>
            <strong>CVE-2021-22986</strong> - F5 BIG-IP iControl REST API authentication bypass
            allowing unauthenticated attackers to execute arbitrary system commands
        </li>
        <li>
            <strong>CVE-2020-1472</strong> (Zerologon) - Microsoft Active Directory privilege
            escalation through authentication bypass
        </li>
        <li>
            <strong>CVE-2019-19781</strong> - Citrix ADC/Gateway directory traversal leading
            to arbitrary code execution without authentication
        </li>
    </ul>

    <div class="warning-box">
        <h4>💡 Research Tip</h4>
        <p>
            Search for "privilege escalation" and "authentication bypass" in CVE databases
            to find similar real-world vulnerabilities.
        </p>
    </div>
</div>

<div class="content-section">
    <h2>Security Testing Tools</h2>
    
    <h3>Burp Suite</h3>
    <p>
        <a href="https://portswigger.net/burp" target="_blank" style="color: #ff6666;">
            https://portswigger.net/burp
        </a>
    </p>
    <p>Industry-standard web security testing tool. Use these features for access control testing:</p>
    <ul>
        <li><strong>Repeater</strong> - Modify and resend requests with different session tokens</li>
        <li><strong>Intruder</strong> - Automate testing with different user credentials</li>
        <li><strong>Compare Site Maps</strong> - Identify authorization differences</li>
        <li><strong>Access Control Extension</strong> - Automated authorization testing</li>
    </ul>

    <h3>OWASP ZAP</h3>
    <p>
        <a href="https://www.zaproxy.org/" target="_blank" style="color: #ff6666;">
            https://www.zaproxy.org/
        </a>
    </p>
    <p>Free and open-source web application security scanner with access control testing capabilities.</p>

    <h3>Autorize (Burp Extension)</h3>
    <p>
        <a href="https://github.com/Quitten/Autorize" target="_blank" style="color: #ff6666;">
            https://github.com/Quitten/Autorize
        </a>
    </p>
    <p>
        Automatically detects authorization enforcement by repeating every request with 
        different session cookies.
    </p>
</div>

<div class="content-section">
    <h2>Books & Training</h2>
    
    <h3>Recommended Books</h3>
    <ul>
        <li>
            <strong>"The Web Application Hacker's Handbook"</strong> by Dafydd Stuttard & Marcus Pinto<br>
            <em>Chapter 8: Attacking Access Controls</em>
        </li>
        <li>
            <strong>"Real-World Bug Hunting"</strong> by Peter Yaworski<br>
            <em>Excellent practical examples of access control vulnerabilities</em>
        </li>
        <li>
            <strong>"OWASP Testing Guide v4"</strong><br>
            <em>Comprehensive testing methodology</em>
        </li>
    </ul>

    <h3>Online Training</h3>
    <ul>
        <li>
            <strong>PortSwigger Web Security Academy</strong><br>
            <a href="https://portswigger.net/web-security/access-control" target="_blank" style="color: #ff6666;">
                Access Control Labs
            </a>
        </li>
        <li>
            <strong>HackTheBox</strong><br>
            <a href="https://www.hackthebox.com/" target="_blank" style="color: #ff6666;">
                Practical penetration testing labs
            </a>
        </li>
        <li>
            <strong>TryHackMe</strong><br>
            <a href="https://tryhackme.com/" target="_blank" style="color: #ff6666;">
                Guided learning paths for web security
            </a>
        </li>
    </ul>
</div>

<div class="content-section">
    <h2>Related Labs in This Series</h2>
    
    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="background: rgba(255, 68, 68, 0.2);">
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Lab</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Vulnerability</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid #333; color: #ff6666;">Link</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Lab 1</td>
                <td style="padding: 1rem; border: 1px solid #333;">Unprotected Admin Functionality</td>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="../lab1/" style="color: #ff6666;">Go to Lab</a></td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Lab 2</td>
                <td style="padding: 1rem; border: 1px solid #333;">Unpredictable URL</td>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="../lab2/" style="color: #ff6666;">Go to Lab</a></td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Lab 3</td>
                <td style="padding: 1rem; border: 1px solid #333;">User Role in Cookie</td>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="../lab3/" style="color: #ff6666;">Go to Lab</a></td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid #333;">Lab 11</td>
                <td style="padding: 1rem; border: 1px solid #333;">Method-Based Access Control</td>
                <td style="padding: 1rem; border: 1px solid #333;"><a href="../lab11/" style="color: #ff6666;">Go to Lab</a></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="content-section">
    <h2>Bug Bounty Programs</h2>
    <p>Practice finding access control issues in real applications:</p>
    <ul>
        <li>
            <strong>HackerOne</strong> - <a href="https://hackerone.com/directory" target="_blank" style="color: #ff6666;">hackerone.com/directory</a>
        </li>
        <li>
            <strong>Bugcrowd</strong> - <a href="https://bugcrowd.com/programs" target="_blank" style="color: #ff6666;">bugcrowd.com/programs</a>
        </li>
        <li>
            <strong>Intigriti</strong> - <a href="https://www.intigriti.com/programs" target="_blank" style="color: #ff6666;">intigriti.com/programs</a>
        </li>
    </ul>

    <div class="info-box">
        <h4>🏆 Success Stories</h4>
        <p>
            Access control vulnerabilities are among the most commonly rewarded issues in 
            bug bounty programs. Many researchers have earned significant bounties finding 
            authorization bypass issues similar to this lab.
        </p>
    </div>
</div>
