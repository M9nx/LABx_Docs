<div class="doc-header">
    <h1>References</h1>
    <p>Additional resources and reading materials</p>
</div>

<div class="content-section">
    <h2>Official Documentation</h2>
    
    <h3>HTTP Referer Header</h3>
    <ul>
        <li>
            <strong>MDN Web Docs - Referer</strong><br>
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referer" target="_blank" style="color: #88aaff;">
                https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referer
            </a>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                Comprehensive documentation on the Referer header behavior and limitations.
            </p>
        </li>
        <li>
            <strong>RFC 7231 - HTTP/1.1 Semantics and Content</strong><br>
            <a href="https://tools.ietf.org/html/rfc7231#section-5.5.2" target="_blank" style="color: #88aaff;">
                https://tools.ietf.org/html/rfc7231#section-5.5.2
            </a>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                The official HTTP specification for the Referer header.
            </p>
        </li>
        <li>
            <strong>Referrer-Policy</strong><br>
            <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy" target="_blank" style="color: #88aaff;">
                https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
            </a>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                How to control Referer header behavior using the Referrer-Policy header.
            </p>
        </li>
    </ul>
</div>

<div class="content-section">
    <h2>Security Standards</h2>
    
    <h3>OWASP Resources</h3>
    <ul>
        <li>
            <strong>OWASP Top 10 - A01:2021 Broken Access Control</strong><br>
            <a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank" style="color: #88aaff;">
                https://owasp.org/Top10/A01_2021-Broken_Access_Control/
            </a>
        </li>
        <li>
            <strong>OWASP Access Control Cheat Sheet</strong><br>
            <a href="https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html" target="_blank" style="color: #88aaff;">
                https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html
            </a>
        </li>
        <li>
            <strong>OWASP Testing Guide - Access Control</strong><br>
            <a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/" target="_blank" style="color: #88aaff;">
                https://owasp.org/www-project-web-security-testing-guide/
            </a>
        </li>
    </ul>
    
    <h3>CWE References</h3>
    <ul>
        <li>
            <strong>CWE-293: Using Referer Field for Authentication</strong><br>
            <a href="https://cwe.mitre.org/data/definitions/293.html" target="_blank" style="color: #88aaff;">
                https://cwe.mitre.org/data/definitions/293.html
            </a>
        </li>
        <li>
            <strong>CWE-284: Improper Access Control</strong><br>
            <a href="https://cwe.mitre.org/data/definitions/284.html" target="_blank" style="color: #88aaff;">
                https://cwe.mitre.org/data/definitions/284.html
            </a>
        </li>
    </ul>
</div>

<div class="content-section">
    <h2>PortSwigger Web Security Academy</h2>
    
    <ul>
        <li>
            <strong>Access Control Vulnerabilities</strong><br>
            <a href="https://portswigger.net/web-security/access-control" target="_blank" style="color: #88aaff;">
                https://portswigger.net/web-security/access-control
            </a>
        </li>
        <li>
            <strong>Referer-based Access Control Lab</strong><br>
            <a href="https://portswigger.net/web-security/access-control/lab-referer-based-access-control" target="_blank" style="color: #88aaff;">
                https://portswigger.net/web-security/access-control/lab-referer-based-access-control
            </a>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                The original lab that inspired this implementation.
            </p>
        </li>
    </ul>
</div>

<div class="content-section">
    <h2>Tools</h2>
    
    <h3>Intercepting Proxies</h3>
    <ul>
        <li>
            <strong>Burp Suite</strong><br>
            <a href="https://portswigger.net/burp" target="_blank" style="color: #88aaff;">
                https://portswigger.net/burp
            </a>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                Industry-standard web security testing tool.
            </p>
        </li>
        <li>
            <strong>OWASP ZAP</strong><br>
            <a href="https://www.zaproxy.org/" target="_blank" style="color: #88aaff;">
                https://www.zaproxy.org/
            </a>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                Free and open-source alternative to Burp Suite.
            </p>
        </li>
    </ul>
    
    <h3>Browser Extensions</h3>
    <ul>
        <li>
            <strong>ModHeader (Chrome/Firefox)</strong><br>
            <p style="color: #888; font-size: 0.9rem;">
                Allows modifying HTTP headers directly in the browser.
            </p>
        </li>
        <li>
            <strong>Cookie Editor</strong><br>
            <p style="color: #888; font-size: 0.9rem;">
                Easy cookie viewing and manipulation for testing.
            </p>
        </li>
    </ul>
</div>

<div class="content-section">
    <h2>Related Vulnerabilities</h2>
    
    <div class="info-box info">
        <h4>📚 Related Lab Topics</h4>
        <ul style="color: #ccc; margin-top: 0.5rem;">
            <li><strong>IDOR (Insecure Direct Object References)</strong> - Similar access control bypasses</li>
            <li><strong>Privilege Escalation</strong> - Gaining unauthorized access levels</li>
            <li><strong>CSRF (Cross-Site Request Forgery)</strong> - Related header-based attacks</li>
            <li><strong>Parameter Tampering</strong> - Manipulating request parameters</li>
            <li><strong>Forced Browsing</strong> - Accessing unauthorized resources directly</li>
        </ul>
    </div>
</div>

<div class="content-section">
    <h2>Academic Papers</h2>
    
    <ul>
        <li>
            <strong>"The Tangled Web" by Michal Zalewski</strong>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                Comprehensive book on web security, including HTTP header security.
            </p>
        </li>
        <li>
            <strong>"Web Application Hacker's Handbook"</strong>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.25rem;">
                Classic reference for web security testing methodologies.
            </p>
        </li>
    </ul>
</div>

<div class="content-section">
    <h2>About This Lab</h2>
    
    <div class="info-box success">
        <h4>🎓 Learning Objectives</h4>
        <p>After completing this lab, you should understand:</p>
        <ul style="color: #ccc; margin-top: 0.5rem;">
            <li>Why HTTP headers cannot be trusted for authorization</li>
            <li>How to identify Referer-based access control vulnerabilities</li>
            <li>Techniques for exploiting header-based security</li>
            <li>Proper implementation of server-side access control</li>
            <li>The importance of defense in depth</li>
        </ul>
    </div>
</div>
