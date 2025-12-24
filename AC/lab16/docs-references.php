<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>References - IDOR Slowvote Bypass</title>
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
        .section ul { padding-left: 1.5rem; list-style: none; }
        .reference-item {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(106, 90, 205, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin: 0.75rem 0;
        }
        .reference-item h4 {
            color: #9370DB;
            margin-bottom: 0.5rem;
        }
        .reference-item a {
            color: #b794f4;
            word-break: break-all;
        }
        .reference-item a:hover {
            color: #9370DB;
        }
        .reference-item p {
            font-size: 0.9rem;
            color: #888;
            margin-top: 0.5rem;
        }
        .tag {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 5px;
            font-size: 0.75rem;
            margin-right: 0.25rem;
        }
        .tag.owasp { background: rgba(255, 100, 100, 0.2); color: #ff8888; }
        .tag.cve { background: rgba(255, 170, 0, 0.2); color: #ffaa00; }
        .tag.tutorial { background: rgba(0, 200, 0, 0.2); color: #66ff66; }
        .tag.tool { background: rgba(0, 150, 255, 0.2); color: #66ccff; }
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
                <li><a href="docs-testing.php">Testing Guide</a></li>
                <li><a href="docs-references.php" class="active">References</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="page-title">
                <h1>References & Resources</h1>
                <p style="color: #888;">External documentation and further reading</p>
            </div>

            <div class="section" id="cve-references">
                <h2>🔐 CVE & Vulnerability Databases</h2>
                
                <div class="reference-item">
                    <span class="tag cve">CVE</span>
                    <h4>CVE-2017-7606 - Phabricator Slowvote</h4>
                    <a href="https://nvd.nist.gov/vuln/detail/CVE-2017-7606" target="_blank">
                        https://nvd.nist.gov/vuln/detail/CVE-2017-7606
                    </a>
                    <p>The original vulnerability this lab is based on. Phabricator allowed authenticated users to access Slowvote poll information regardless of visibility settings.</p>
                </div>

                <div class="reference-item">
                    <span class="tag cve">CVE</span>
                    <h4>Phabricator Security Disclosure</h4>
                    <a href="https://secure.phabricator.com/T12364" target="_blank">
                        https://secure.phabricator.com/T12364
                    </a>
                    <p>Original security disclosure on Phabricator's issue tracker.</p>
                </div>
            </div>

            <div class="section" id="owasp">
                <h2>📖 OWASP Resources</h2>
                
                <div class="reference-item">
                    <span class="tag owasp">OWASP</span>
                    <h4>OWASP API Security Top 10</h4>
                    <a href="https://owasp.org/API-Security/editions/2023/en/0xa1-broken-object-level-authorization/" target="_blank">
                        OWASP API Security - Broken Object Level Authorization
                    </a>
                    <p>API1:2023 - Broken Object Level Authorization (BOLA) is the #1 risk in the OWASP API Security Top 10.</p>
                </div>

                <div class="reference-item">
                    <span class="tag owasp">OWASP</span>
                    <h4>OWASP Testing Guide - IDOR</h4>
                    <a href="https://owasp.org/www-project-web-security-testing-guide/latest/4-Web_Application_Security_Testing/05-Authorization_Testing/04-Testing_for_Insecure_Direct_Object_References" target="_blank">
                        Testing for Insecure Direct Object References
                    </a>
                    <p>Comprehensive guide on testing for IDOR vulnerabilities.</p>
                </div>

                <div class="reference-item">
                    <span class="tag owasp">OWASP</span>
                    <h4>OWASP Cheat Sheet - Authorization</h4>
                    <a href="https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html" target="_blank">
                        Authorization Cheat Sheet
                    </a>
                    <p>Best practices for implementing authorization in applications.</p>
                </div>
            </div>

            <div class="section" id="tutorials">
                <h2>📚 Tutorials & Articles</h2>
                
                <div class="reference-item">
                    <span class="tag tutorial">Tutorial</span>
                    <h4>PortSwigger - Access Control Vulnerabilities</h4>
                    <a href="https://portswigger.net/web-security/access-control" target="_blank">
                        https://portswigger.net/web-security/access-control
                    </a>
                    <p>Interactive labs and explanations of access control vulnerabilities from the creators of Burp Suite.</p>
                </div>

                <div class="reference-item">
                    <span class="tag tutorial">Tutorial</span>
                    <h4>HackTricks - IDOR</h4>
                    <a href="https://book.hacktricks.xyz/pentesting-web/idor" target="_blank">
                        https://book.hacktricks.xyz/pentesting-web/idor
                    </a>
                    <p>Comprehensive IDOR exploitation techniques and bypasses.</p>
                </div>

                <div class="reference-item">
                    <span class="tag tutorial">Tutorial</span>
                    <h4>PentesterLab - IDOR</h4>
                    <a href="https://pentesterlab.com/exercises/from_sqli_to_shell" target="_blank">
                        PentesterLab IDOR Exercises
                    </a>
                    <p>Hands-on exercises for practicing IDOR exploitation.</p>
                </div>
            </div>

            <div class="section" id="tools">
                <h2>🛠️ Tools</h2>
                
                <div class="reference-item">
                    <span class="tag tool">Tool</span>
                    <h4>Burp Suite - Autorize Extension</h4>
                    <a href="https://github.com/PortSwigger/autorize" target="_blank">
                        https://github.com/PortSwigger/autorize
                    </a>
                    <p>Automatic authorization testing for Burp Suite. Detects authorization bypasses by replaying requests with different sessions.</p>
                </div>

                <div class="reference-item">
                    <span class="tag tool">Tool</span>
                    <h4>OWASP ZAP - Access Control Testing</h4>
                    <a href="https://www.zaproxy.org/docs/desktop/addons/access-control-testing/" target="_blank">
                        https://www.zaproxy.org/docs/desktop/addons/access-control-testing/
                    </a>
                    <p>ZAP add-on for testing access control in web applications.</p>
                </div>

                <div class="reference-item">
                    <span class="tag tool">Tool</span>
                    <h4>AuthMatrix - Burp Extension</h4>
                    <a href="https://github.com/SecurityInnovation/AuthMatrix" target="_blank">
                        https://github.com/SecurityInnovation/AuthMatrix
                    </a>
                    <p>Grid-based authorization testing tool that tests multiple users against multiple resources.</p>
                </div>
            </div>

            <div class="section" id="related-cves">
                <h2>🔗 Related Vulnerabilities</h2>
                
                <div class="reference-item">
                    <span class="tag cve">CVE</span>
                    <h4>CVE-2019-12814 - FasterXML Jackson IDOR</h4>
                    <a href="https://nvd.nist.gov/vuln/detail/CVE-2019-12814" target="_blank">
                        NVD Link
                    </a>
                    <p>Polymorphic deserialization leading to IDOR.</p>
                </div>

                <div class="reference-item">
                    <span class="tag cve">CVE</span>
                    <h4>CVE-2018-19786 - HashiCorp Consul IDOR</h4>
                    <a href="https://nvd.nist.gov/vuln/detail/CVE-2018-19786" target="_blank">
                        NVD Link
                    </a>
                    <p>API authorization bypass in HashiCorp Consul.</p>
                </div>

                <div class="reference-item">
                    <span class="tag cve">CVE</span>
                    <h4>CVE-2021-32619 - Envoy IDOR</h4>
                    <a href="https://nvd.nist.gov/vuln/detail/CVE-2021-32619" target="_blank">
                        NVD Link
                    </a>
                    <p>Authorization bypass in Envoy proxy.</p>
                </div>
            </div>

            <div class="section" id="standards">
                <h2>📋 Standards & Compliance</h2>
                <ul>
                    <li><strong>CWE-639</strong> - Authorization Bypass Through User-Controlled Key</li>
                    <li><strong>CWE-284</strong> - Improper Access Control</li>
                    <li><strong>CWE-285</strong> - Improper Authorization</li>
                    <li><strong>NIST SP 800-53</strong> - AC-3 Access Enforcement</li>
                    <li><strong>PCI DSS</strong> - Requirement 7: Restrict access to cardholder data</li>
                </ul>
            </div>

            <div class="nav-buttons">
                <a href="docs-testing.php" class="nav-btn">← Testing Guide</a>
                <a href="index.php" class="nav-btn">Back to Lab Home →</a>
            </div>
        </main>
    </div>
</body>
</html>
