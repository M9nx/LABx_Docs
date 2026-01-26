<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References - MethodLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 68, 68, 0.3); padding: 1rem 2rem; position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #ff4444; }
        .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 68, 68, 0.3); color: #e0e0e0; text-decoration: none; border-radius: 6px; font-weight: 500; transition: all 0.3s; }
        .btn-back:hover { background: rgba(255, 68, 68, 0.2); border-color: #ff4444; color: #ff4444; }
        .docs-container { display: flex; max-width: 1400px; margin: 0 auto; padding: 2rem; gap: 2rem; }
        .sidebar { width: 280px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 15px; padding: 1.5rem; height: fit-content; position: sticky; top: 100px; }
        .sidebar h3 { color: #ff4444; margin-bottom: 1rem; font-size: 1.2rem; }
        .sidebar-nav { list-style: none; }
        .sidebar-nav li { margin-bottom: 0.5rem; }
        .sidebar-nav a { display: block; color: #ccc; text-decoration: none; padding: 0.7rem 1rem; border-radius: 8px; transition: all 0.3s; }
        .sidebar-nav a:hover { background: rgba(255, 68, 68, 0.2); color: #ff6666; padding-left: 1.5rem; }
        .sidebar-nav a.active { background: rgba(255, 68, 68, 0.3); color: #ff4444; font-weight: 600; }
        .content { flex: 1; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 15px; padding: 2.5rem; }
        .content h1 { color: #ff4444; font-size: 2.5rem; margin-bottom: 1rem; }
        .content h2 { color: #ff6666; font-size: 1.8rem; margin: 2rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(255, 68, 68, 0.3); }
        .content h3 { color: #ff8888; font-size: 1.3rem; margin: 1.5rem 0 1rem 0; }
        .content p { color: #ccc; line-height: 1.8; margin-bottom: 1rem; }
        .content ul { color: #ccc; line-height: 1.8; margin: 1rem 0 1rem 2rem; }
        .content li { margin-bottom: 0.5rem; }
        .resource-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 68, 68, 0.2); border-radius: 10px; padding: 1.5rem; margin: 1rem 0; transition: all 0.3s; }
        .resource-card:hover { background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 68, 68, 0.4); }
        .resource-card h3 { margin-top: 0; }
        .resource-card a { color: #6666ff; text-decoration: none; font-weight: 500; }
        .resource-card a:hover { color: #8888ff; text-decoration: underline; }
        .info-box { background: rgba(100, 100, 255, 0.1); border-left: 4px solid #6666ff; border-radius: 8px; padding: 1.5rem; margin: 1.5rem 0; }
        .info-box strong { color: #aaaaff; display: block; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">⚙️ MethodLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">My Account</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="docs-container">
        <aside class="sidebar">
            <h3>📚 Documentation</h3>
            <ul class="sidebar-nav">
                <li><a href="docs.php">📖 Overview</a></li>
                <li><a href="docs-http-methods.php">🌐 HTTP Methods</a></li>
                <li><a href="docs-access-control.php">🔒 Access Control</a></li>
                <li><a href="docs-exploitation.php">⚔️ Exploitation</a></li>
                <li><a href="docs-prevention.php">🛡️ Prevention</a></li>
                <li><a href="docs-references.php" class="active">📚 References</a></li>
            </ul>
        </aside>

        <main class="content">
            <h1>📚 References & Additional Resources</h1>

            <p>Comprehensive collection of resources for learning more about method-based access control and web application security.</p>

            <h2>📖 Official Standards & Documentation</h2>

            <div class="resource-card">
                <h3>RFC 7231 - HTTP/1.1 Semantics and Content</h3>
                <p>The official HTTP specification defining request methods and their intended semantics.</p>
                <p><a href="https://tools.ietf.org/html/rfc7231" target="_blank">https://tools.ietf.org/html/rfc7231</a></p>
            </div>

            <div class="resource-card">
                <h3>RFC 9110 - HTTP Semantics (Latest)</h3>
                <p>Updated HTTP specification (2022) that supersedes RFC 7231.</p>
                <p><a href="https://www.rfc-editor.org/rfc/rfc9110.html" target="_blank">https://www.rfc-editor.org/rfc/rfc9110.html</a></p>
            </div>

            <div class="resource-card">
                <h3>MDN Web Docs - HTTP Request Methods</h3>
                <p>Comprehensive guide to HTTP methods with examples and best practices.</p>
                <p><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods</a></p>
            </div>

            <h2>🔒 OWASP Resources</h2>

            <div class="resource-card">
                <h3>OWASP Top 10:2021 - A01 Broken Access Control</h3>
                <p>Official OWASP documentation on access control vulnerabilities, ranked #1 risk.</p>
                <p><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank">https://owasp.org/Top10/A01_2021-Broken_Access_Control/</a></p>
            </div>

            <div class="resource-card">
                <h3>OWASP Access Control Cheat Sheet</h3>
                <p>Practical guide for implementing proper access controls.</p>
                <p><a href="https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html" target="_blank">https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html</a></p>
            </div>

            <div class="resource-card">
                <h3>OWASP Testing Guide - Authorization Testing</h3>
                <p>Comprehensive guide for testing access control implementations.</p>
                <p><a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/README" target="_blank">OWASP Authorization Testing Guide</a></p>
            </div>

            <h2>🎓 Learning Platforms</h2>

            <div class="resource-card">
                <h3>PortSwigger Web Security Academy</h3>
                <p>Free interactive labs covering access control vulnerabilities.</p>
                <p><a href="https://portswigger.net/web-security/access-control" target="_blank">https://portswigger.net/web-security/access-control</a></p>
            </div>

            <div class="resource-card">
                <h3>HackTheBox Academy</h3>
                <p>Web application security course covering access control topics.</p>
                <p><a href="https://academy.hackthebox.com/" target="_blank">https://academy.hackthebox.com/</a></p>
            </div>

            <div class="resource-card">
                <h3>OWASP WebGoat</h3>
                <p>Deliberately insecure web application for learning security concepts.</p>
                <p><a href="https://owasp.org/www-project-webgoat/" target="_blank">https://owasp.org/www-project-webgoat/</a></p>
            </div>

            <h2>🛠️ Security Testing Tools</h2>

            <div class="resource-card">
                <h3>Burp Suite</h3>
                <p>Industry-standard web application security testing platform.</p>
                <p><a href="https://portswigger.net/burp" target="_blank">https://portswigger.net/burp</a></p>
                <ul>
                    <li>Proxy for intercepting and modifying requests</li>
                    <li>Intruder for automated testing</li>
                    <li>Repeater for manual testing</li>
                    <li>Autorize extension for access control testing</li>
                </ul>
            </div>

            <div class="resource-card">
                <h3>OWASP ZAP (Zed Attack Proxy)</h3>
                <p>Free, open-source security testing tool.</p>
                <p><a href="https://www.zaproxy.org/" target="_blank">https://www.zaproxy.org/</a></p>
            </div>

            <div class="resource-card">
                <h3>curl</h3>
                <p>Command-line tool for transferring data with URLs.</p>
                <p><a href="https://curl.se/" target="_blank">https://curl.se/</a></p>
                <p>Essential for testing HTTP methods and crafting custom requests.</p>
            </div>

            <h2>📚 Books & Publications</h2>

            <div class="resource-card">
                <h3>"The Web Application Hacker's Handbook" by Stuttard & Pinto</h3>
                <p>Comprehensive guide to web application security, including detailed coverage of access control vulnerabilities.</p>
            </div>

            <div class="resource-card">
                <h3>"Real-World Bug Hunting" by Peter Yaworski</h3>
                <p>Practical guide featuring real-world vulnerability discoveries including access control bypasses.</p>
            </div>

            <div class="resource-card">
                <h3>"Web Security Testing Cookbook" by Paco Hope & Ben Walther</h3>
                <p>Practical recipes for testing web application security.</p>
            </div>

            <h2>🎯 Related Vulnerabilities</h2>

            <div class="resource-card">
                <h3>Insecure Direct Object References (IDOR)</h3>
                <p>Related access control vulnerability where applications expose internal object references.</p>
                <p><a href="https://portswigger.net/web-security/access-control/idor" target="_blank">PortSwigger IDOR Guide</a></p>
            </div>

            <div class="resource-card">
                <h3>Privilege Escalation</h3>
                <p>Techniques for gaining higher privileges than intended.</p>
                <p><a href="https://owasp.org/www-community/attacks/Privilege_Escalation" target="_blank">OWASP Privilege Escalation</a></p>
            </div>

            <div class="resource-card">
                <h3>Path Traversal / Directory Traversal</h3>
                <p>Another access control issue involving unauthorized file access.</p>
                <p><a href="https://portswigger.net/web-security/file-path-traversal" target="_blank">Path Traversal Guide</a></p>
            </div>

            <h2>💻 Code Examples & Frameworks</h2>

            <div class="resource-card">
                <h3>Laravel Authorization</h3>
                <p>PHP framework with built-in authorization mechanisms.</p>
                <p><a href="https://laravel.com/docs/authorization" target="_blank">https://laravel.com/docs/authorization</a></p>
            </div>

            <div class="resource-card">
                <h3>Express.js Security Best Practices</h3>
                <p>Security guide for Node.js/Express applications.</p>
                <p><a href="https://expressjs.com/en/advanced/best-practice-security.html" target="_blank">Express Security Best Practices</a></p>
            </div>

            <div class="resource-card">
                <h3>Django Authentication & Authorization</h3>
                <p>Python framework's comprehensive auth system.</p>
                <p><a href="https://docs.djangoproject.com/en/stable/topics/auth/" target="_blank">Django Authentication</a></p>
            </div>

            <h2>🎥 Video Resources</h2>

            <div class="resource-card">
                <h3>OWASP DevSlop YouTube Channel</h3>
                <p>Security-focused video series covering various vulnerabilities.</p>
                <p><a href="https://www.youtube.com/@OWASPDevSlop" target="_blank">OWASP DevSlop on YouTube</a></p>
            </div>

            <div class="resource-card">
                <h3>PwnFunction</h3>
                <p>Animated explanations of web security concepts.</p>
                <p><a href="https://www.youtube.com/@PwnFunction" target="_blank">PwnFunction on YouTube</a></p>
            </div>

            <h2>🌐 Community & Forums</h2>

            <div class="resource-card">
                <h3>OWASP Slack</h3>
                <p>Join the OWASP community for discussions and support.</p>
                <p><a href="https://owasp.org/slack/invite" target="_blank">OWASP Slack Invite</a></p>
            </div>

            <div class="resource-card">
                <h3>HackerOne Community</h3>
                <p>Bug bounty platform with community forums and resources.</p>
                <p><a href="https://www.hackerone.com/" target="_blank">https://www.hackerone.com/</a></p>
            </div>

            <div class="resource-card">
                <h3>Bugcrowd University</h3>
                <p>Free security education platform with practical lessons.</p>
                <p><a href="https://www.bugcrowd.com/hackers/bugcrowd-university/" target="_blank">Bugcrowd University</a></p>
            </div>

            <h2>📊 Security Research & Blogs</h2>

            <div class="resource-card">
                <h3>PortSwigger Research Blog</h3>
                <p>Latest research from the creators of Burp Suite.</p>
                <p><a href="https://portswigger.net/research" target="_blank">https://portswigger.net/research</a></p>
            </div>

            <div class="resource-card">
                <h3>OWASP Blog</h3>
                <p>Community contributions on web security topics.</p>
                <p><a href="https://owasp.org/blog/" target="_blank">https://owasp.org/blog/</a></p>
            </div>

            <div class="resource-card">
                <h3>Google Security Blog</h3>
                <p>Security research and announcements from Google.</p>
                <p><a href="https://security.googleblog.com/" target="_blank">https://security.googleblog.com/</a></p>
            </div>

            <h2>🎓 Certifications</h2>

            <div class="resource-card">
                <h3>Certified Ethical Hacker (CEH)</h3>
                <p>Vendor-neutral certification covering web application security.</p>
                <p><a href="https://www.eccouncil.org/programs/certified-ethical-hacker-ceh/" target="_blank">EC-Council CEH</a></p>
            </div>

            <div class="resource-card">
                <h3>Offensive Security Web Expert (OSWE)</h3>
                <p>Advanced web application penetration testing certification.</p>
                <p><a href="https://www.offensive-security.com/awae-oswe/" target="_blank">Offensive Security OSWE</a></p>
            </div>

            <div class="resource-card">
                <h3>GIAC Web Application Penetration Tester (GWAPT)</h3>
                <p>Certification validating web application security testing skills.</p>
                <p><a href="https://www.giac.org/certifications/web-application-penetration-tester-gwapt/" target="_blank">GIAC GWAPT</a></p>
            </div>

            <h2>🏁 Conclusion</h2>

            <div class="info-box">
                <strong>🎯 Continue Learning</strong>
                <p>Web application security is an ever-evolving field. Stay updated by following security researchers, practicing on vulnerable applications, and participating in the security community.</p>
                <p>Remember: The best way to learn is by doing. Complete this lab, try other challenges, and always practice ethical hacking responsibly!</p>
            </div>

            <h2>🔗 Quick Links</h2>
            <ul>
                <li><a href="index.php">🏠 Lab Home</a></li>
                <li><a href="lab-description.php">📋 Lab Description</a></li>
                <li><a href="docs.php">📖 Documentation Overview</a></li>
                <li><a href="login.php">🚀 Start Lab</a></li>
            </ul>
        </main>
    </div>
</body>
</html>
