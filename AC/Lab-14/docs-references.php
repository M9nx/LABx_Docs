<style>
    .doc-section h1 { font-size: 2.2rem; color: #ff4444; margin-bottom: 1rem; }
    .doc-section h2 { font-size: 1.5rem; color: #ff6666; margin: 2rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 68, 68, 0.3); }
    .doc-section h3 { font-size: 1.2rem; color: #ff8888; margin: 1.5rem 0 0.75rem; }
    .doc-section p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
    .doc-section ul, .doc-section ol { margin: 1rem 0 1rem 1.5rem; color: #ccc; }
    .doc-section li { margin-bottom: 0.5rem; line-height: 1.6; }
    .doc-section code { background: rgba(255, 68, 68, 0.15); padding: 0.2rem 0.5rem; border-radius: 4px; font-family: 'Consolas', monospace; font-size: 0.9rem; color: #ff8888; }
    .doc-section a { color: #ff6666; text-decoration: none; }
    .doc-section a:hover { text-decoration: underline; color: #ff8888; }
    .ref-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 10px; padding: 1.5rem; margin: 1rem 0; }
    .ref-card h4 { color: #ff6666; margin-bottom: 0.5rem; }
    .ref-card p { color: #aaa; font-size: 0.95rem; margin-bottom: 0.5rem; }
    .ref-card .url { color: #66aaff; font-size: 0.85rem; word-break: break-all; }
    .cwe-box { background: rgba(255, 68, 68, 0.1); border: 1px solid rgba(255, 68, 68, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .cwe-box h4 { color: #ff6666; margin-bottom: 0.5rem; }
    .owasp-box { background: rgba(0, 200, 0, 0.1); border: 1px solid rgba(0, 200, 0, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .owasp-box h4 { color: #66ff66; margin-bottom: 0.5rem; }
    .info-box { background: rgba(0, 150, 255, 0.1); border: 1px solid rgba(0, 150, 255, 0.4); border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; }
    .info-box h4 { color: #00aaff; margin-bottom: 0.5rem; }
    .book-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin: 1rem 0; }
    .book-item { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 68, 68, 0.2); border-radius: 8px; padding: 1rem; }
    .book-item h5 { color: #ff8888; margin-bottom: 0.3rem; }
    .book-item .author { color: #888; font-size: 0.85rem; }
</style>

<div class="doc-section">
    <h1>References & Resources</h1>
    <p>
        This section provides links to external resources, standards, and further reading 
        materials for deepening your understanding of IDOR vulnerabilities and access control.
    </p>

    <h2>Standards & Classifications</h2>

    <div class="cwe-box">
        <h4>CWE-639: Authorization Bypass Through User-Controlled Key</h4>
        <p>
            The system's authorization functionality does not prevent one user from gaining access 
            to another user's data or record by modifying the key value identifying the data.
        </p>
        <p class="url">https://cwe.mitre.org/data/definitions/639.html</p>
    </div>

    <div class="owasp-box">
        <h4>OWASP Top 10 2021 - A01: Broken Access Control</h4>
        <p>
            Moving up from the fifth position, 94% of applications were tested for some form of 
            broken access control. Broken access control is the most serious web application 
            security risk.
        </p>
        <p class="url">https://owasp.org/Top10/A01_2021-Broken_Access_Control/</p>
    </div>

    <h2>OWASP Resources</h2>

    <div class="ref-card">
        <h4>OWASP Testing Guide - IDOR</h4>
        <p>Comprehensive guide on testing for Insecure Direct Object References, including methodology and examples.</p>
        <p class="url">https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References</p>
    </div>

    <div class="ref-card">
        <h4>OWASP Cheat Sheet: Authorization</h4>
        <p>Best practices for implementing secure authorization in web applications.</p>
        <p class="url">https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html</p>
    </div>

    <div class="ref-card">
        <h4>OWASP Access Control Cheat Sheet</h4>
        <p>Detailed guidance on designing and implementing access control mechanisms.</p>
        <p class="url">https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html</p>
    </div>

    <h2>Real-World Vulnerability Reports</h2>

    <div class="ref-card">
        <h4>Revive Adserver IDOR Vulnerability</h4>
        <p>This lab is based on a real vulnerability in Revive Adserver's banner deletion functionality, demonstrating how improper authorization checks can lead to data manipulation.</p>
        <p class="url">Based on historical vulnerability research in advertising server software</p>
    </div>

    <div class="ref-card">
        <h4>HackerOne IDOR Bug Reports</h4>
        <p>Collection of disclosed IDOR vulnerabilities from bug bounty programs, showing real-world impact and exploitation techniques.</p>
        <p class="url">https://hackerone.com/hacktivity?querystring=idor</p>
    </div>

    <div class="ref-card">
        <h4>PortSwigger Web Security Academy - Access Control</h4>
        <p>Free interactive labs covering various access control vulnerabilities including IDOR, with hands-on exercises.</p>
        <p class="url">https://portswigger.net/web-security/access-control</p>
    </div>

    <h2>Recommended Reading</h2>

    <div class="book-list">
        <div class="book-item">
            <h5>The Web Application Hacker's Handbook</h5>
            <p class="author">Dafydd Stuttard, Marcus Pinto</p>
            <p>Comprehensive guide to web app security testing with extensive coverage of authorization flaws.</p>
        </div>
        <div class="book-item">
            <h5>Bug Bounty Bootcamp</h5>
            <p class="author">Vickie Li</p>
            <p>Practical guide to finding vulnerabilities with detailed IDOR hunting techniques.</p>
        </div>
        <div class="book-item">
            <h5>OWASP Testing Guide v4.2</h5>
            <p class="author">OWASP Foundation</p>
            <p>Industry-standard methodology for web application security testing.</p>
        </div>
        <div class="book-item">
            <h5>Real-World Bug Hunting</h5>
            <p class="author">Peter Yaworski</p>
            <p>Field guide to finding web vulnerabilities with real-world case studies.</p>
        </div>
    </div>

    <h2>Tools for IDOR Testing</h2>

    <div class="ref-card">
        <h4>Burp Suite Professional</h4>
        <p>Industry-leading web security testing tool with extensions like Autorize for automated authorization testing.</p>
        <p class="url">https://portswigger.net/burp</p>
    </div>

    <div class="ref-card">
        <h4>OWASP ZAP</h4>
        <p>Free and open-source alternative to Burp Suite with active scanning and fuzzing capabilities.</p>
        <p class="url">https://www.zaproxy.org/</p>
    </div>

    <div class="ref-card">
        <h4>Autorize (Burp Extension)</h4>
        <p>Automatically detects authorization issues by replaying requests with different privilege levels.</p>
        <p class="url">https://github.com/PortSwigger/autorize</p>
    </div>

    <div class="ref-card">
        <h4>AuthMatrix (Burp Extension)</h4>
        <p>Creates a matrix of roles and endpoints to systematically test access control.</p>
        <p class="url">https://github.com/SecurityInnovation/AuthMatrix</p>
    </div>

    <h2>Video Tutorials</h2>

    <div class="ref-card">
        <h4>IDOR Vulnerability Explained</h4>
        <p>Step-by-step video demonstrations of finding and exploiting IDOR vulnerabilities in various contexts.</p>
        <p class="url">Search: "IDOR vulnerability tutorial" on YouTube</p>
    </div>

    <div class="ref-card">
        <h4>Bug Bounty Hunter Methodology</h4>
        <p>Real-world bug bounty hunters sharing their IDOR discovery techniques and successful reports.</p>
        <p class="url">Channels: Nahamsec, STÖK, InsiderPhD</p>
    </div>

    <h2>Related Labs in This Series</h2>

    <div class="info-box">
        <h4>🔗 Explore More Access Control Labs</h4>
        <ul>
            <li><strong>Lab 1:</strong> Unprotected Admin Functionality - Basic URL-based access control</li>
            <li><strong>Lab 2:</strong> Parameter-Based Access Control - Role stored in URL parameter</li>
            <li><strong>Lab 3:</strong> User ID Controlled by Request Parameter - User enumeration via IDs</li>
            <li><strong>Lab 5:</strong> IDOR with Unpredictable IDs - GUIDs don't prevent IDOR</li>
            <li><strong>Lab 8:</strong> Multi-Step Process Bypass - Skip authorization checks in workflows</li>
            <li><strong>Lab 13:</strong> Referer-Based Access Control - HTTP header-based authorization</li>
        </ul>
        <p style="margin-top: 1rem;"><a href="../index.php">← View All Labs</a></p>
    </div>

    <h2>Academic Research</h2>

    <div class="ref-card">
        <h4>A Study of Access Control Vulnerabilities</h4>
        <p>Academic papers analyzing the prevalence and impact of authorization vulnerabilities in modern web applications.</p>
        <p class="url">IEEE Security & Privacy, ACM CCS proceedings</p>
    </div>

    <div class="ref-card">
        <h4>Automated Detection of Authorization Flaws</h4>
        <p>Research on automated tools and techniques for detecting IDOR and other access control issues.</p>
        <p class="url">USENIX Security Symposium papers</p>
    </div>
</div>
