<?php
require_once 'config.php';
$pageTitle = "Documentation";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - Lab 25 Notes IDOR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }

        /* Navigation */
        .navbar {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(252, 109, 38, 0.3);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fc6d26;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .nav-brand svg {
            width: 32px;
            height: 32px;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a {
            color: #b0b0b0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #fc6d26;
        }

        /* Main Layout */
        .docs-layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            min-height: calc(100vh - 70px);
        }

        /* Sidebar */
        .docs-sidebar {
            width: 280px;
            background: rgba(26, 26, 46, 0.8);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 2rem;
        }

        .sidebar-title {
            color: #fc6d26;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a {
            color: #b0b0b0;
            text-decoration: none;
            display: block;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .sidebar-nav a:hover {
            background: rgba(252, 109, 38, 0.1);
            color: #fc6d26;
        }

        .sidebar-nav a.active {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            font-weight: 600;
        }

        /* Main Content */
        .docs-content {
            flex: 1;
            padding: 3rem;
            max-width: 900px;
        }

        .docs-header {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .docs-header h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .docs-header .subtitle {
            color: #808080;
            font-size: 1.1rem;
        }

        .docs-header .badges {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-danger {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
        }

        .badge-info {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
        }

        /* Section */
        .docs-section {
            margin-bottom: 3rem;
        }

        .docs-section h2 {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(252, 109, 38, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .docs-section h2 i {
            color: #fc6d26;
        }

        .docs-section h3 {
            font-size: 1.15rem;
            color: #fff;
            margin: 1.5rem 0 0.75rem;
        }

        .docs-section p {
            color: #b0b0b0;
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .docs-section ul, .docs-section ol {
            color: #b0b0b0;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .docs-section li {
            margin-bottom: 0.5rem;
        }

        /* Code Block */
        .code-block {
            background: #0d1117;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin: 1.25rem 0;
            overflow: hidden;
        }

        .code-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .code-language {
            color: #fc6d26;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .code-content {
            padding: 1.25rem;
            overflow-x: auto;
        }

        .code-content pre {
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            color: #e0e0e0;
            line-height: 1.6;
            margin: 0;
        }

        .code-content .comment {
            color: #6a737d;
        }

        .code-content .keyword {
            color: #ff7b72;
        }

        .code-content .string {
            color: #a5d6ff;
        }

        .code-content .function {
            color: #d2a8ff;
        }

        .code-content .variable {
            color: #ffa657;
        }

        /* Alert Boxes */
        .alert {
            padding: 1.25rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .alert i {
            font-size: 1.25rem;
            margin-top: 2px;
        }

        .alert-danger {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .alert-danger i {
            color: #ff6b6b;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .alert-warning i {
            color: #ffc107;
        }

        .alert-info {
            background: rgba(252, 109, 38, 0.1);
            border: 1px solid rgba(252, 109, 38, 0.3);
        }

        .alert-info i {
            color: #fc6d26;
        }

        .alert-success {
            background: rgba(76, 217, 100, 0.1);
            border: 1px solid rgba(76, 217, 100, 0.3);
        }

        .alert-success i {
            color: #4cd964;
        }

        .alert-content h4 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .alert-content p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            margin: 1.25rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(252, 109, 38, 0.1);
            color: #fc6d26;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        td {
            color: #b0b0b0;
        }

        td code {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Fira Code', monospace;
            font-size: 0.85rem;
            color: #fc6d26;
        }

        /* Steps */
        .steps {
            counter-reset: step;
        }

        .step {
            position: relative;
            padding-left: 60px;
            margin-bottom: 2rem;
        }

        .step::before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }

        .step h4 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .step p {
            color: #b0b0b0;
            margin-bottom: 0.5rem;
        }

        /* Back Button */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(252, 109, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .buttons-row {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        /* Flow Diagram */
        .flow-diagram {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin: 1.5rem 0;
        }

        .flow-steps {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .flow-step {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .flow-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .flow-icon.attacker {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
        }

        .flow-icon.server {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
        }

        .flow-icon.leak {
            background: rgba(76, 217, 100, 0.2);
            color: #4cd964;
        }

        .flow-text {
            flex: 1;
        }

        .flow-text strong {
            color: #fff;
            display: block;
            margin-bottom: 0.25rem;
        }

        .flow-text span {
            color: #808080;
            font-size: 0.9rem;
        }

        .flow-arrow {
            text-align: center;
            color: #404040;
            font-size: 1.5rem;
            padding: 0.5rem 0 0.5rem 25px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <svg viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 0L0 9.14v13.72L16 32l16-9.14V9.14L16 0zm0 4.57l10.29 5.86L16 16.29 5.71 10.43 16 4.57zM3.43 12.57l11.14 6.29v9.71L3.43 22.29v-9.72zm15.14 16v-9.71l11.14-6.29v9.72l-11.14 6.28z"/>
                </svg>
                Lab 25 - Notes IDOR
            </a>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="docs.php" class="active"><i class="fas fa-book"></i> Docs</a>
                <a href="lab-description.php"><i class="fas fa-flask"></i> Lab Info</a>
            </div>
        </div>
    </nav>

    <div class="docs-layout">
        <!-- Sidebar -->
        <aside class="docs-sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title">Getting Started</h3>
                <ul class="sidebar-nav">
                    <li><a href="#overview" class="active">Overview</a></li>
                    <li><a href="#vulnerability">The Vulnerability</a></li>
                    <li><a href="#setup">Setup Instructions</a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <h3 class="sidebar-title">Attack Guide</h3>
                <ul class="sidebar-nav">
                    <li><a href="#attack-flow">Attack Flow</a></li>
                    <li><a href="#exploitation">Exploitation Steps</a></li>
                    <li><a href="#payload">Attack Payload</a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <h3 class="sidebar-title">Technical Details</h3>
                <ul class="sidebar-nav">
                    <li><a href="#vulnerable-code">Vulnerable Code</a></li>
                    <li><a href="#info-leak">Information Leak</a></li>
                    <li><a href="#api-endpoints">API Endpoints</a></li>
                </ul>
            </div>
            <div class="sidebar-section">
                <h3 class="sidebar-title">Defense</h3>
                <ul class="sidebar-nav">
                    <li><a href="#remediation">Remediation</a></li>
                    <li><a href="#secure-code">Secure Code Example</a></li>
                    <li><a href="#references">References</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="docs-content">
            <div class="docs-header">
                <h1>Notes IDOR on Personal Snippets</h1>
                <p class="subtitle">
                    Based on GitLab HackerOne Report - IDOR vulnerability allowing unauthorized 
                    access to private snippets through the notes system
                </p>
                <div class="badges">
                    <span class="badge badge-danger">High Severity</span>
                    <span class="badge badge-info">IDOR + Info Leak</span>
                    <span class="badge badge-info">CVE Reference</span>
                </div>
            </div>

            <!-- Overview Section -->
            <section id="overview" class="docs-section">
                <h2><i class="fas fa-info-circle"></i> Overview</h2>
                <p>
                    This lab simulates a vulnerability originally discovered in GitLab where an attacker 
                    could create, edit, or delete notes on another user's private personal snippets by 
                    manipulating the <code>noteable_type</code> parameter in API requests.
                </p>
                <p>
                    The vulnerability exists because the server properly authorizes access when creating 
                    notes on issues, but fails to perform the same authorization check when the 
                    <code>noteable_type</code> is changed to <code>personal_snippet</code>.
                </p>

                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <h4>Security Impact</h4>
                        <p>
                            An attacker can access and interact with private snippets they shouldn't have 
                            access to. Additionally, the activity log reveals private snippet titles, 
                            causing an information disclosure vulnerability.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Vulnerability Section -->
            <section id="vulnerability" class="docs-section">
                <h2><i class="fas fa-bug"></i> The Vulnerability</h2>
                
                <h3>Root Cause</h3>
                <p>
                    The vulnerability stems from inconsistent authorization checks in the notes API. 
                    When a user creates a note on an issue, the server verifies that the user has 
                    access to that issue. However, when the <code>noteable_type</code> is changed to 
                    <code>personal_snippet</code>, the server retrieves the snippet without checking 
                    if the requesting user has permission to access it.
                </p>

                <h3>Impact Analysis</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Impact Type</th>
                                <th>Description</th>
                                <th>Severity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Unauthorized Access</td>
                                <td>Attacker can create notes on private snippets</td>
                                <td><span class="badge badge-danger">High</span></td>
                            </tr>
                            <tr>
                                <td>Information Disclosure</td>
                                <td>Private snippet titles leaked via activity log</td>
                                <td><span class="badge badge-danger">High</span></td>
                            </tr>
                            <tr>
                                <td>Data Modification</td>
                                <td>Notes can be edited/deleted on private resources</td>
                                <td><span class="badge badge-info">Medium</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Setup Section -->
            <section id="setup" class="docs-section">
                <h2><i class="fas fa-cog"></i> Setup Instructions</h2>
                <p>Follow these steps to set up the lab environment:</p>

                <div class="steps">
                    <div class="step">
                        <h4>Import the Database</h4>
                        <p>Run the SQL setup script to create the database and populate test data:</p>
                        <div class="code-block">
                            <div class="code-header">
                                <span class="code-language">SQL</span>
                            </div>
                            <div class="code-content">
                                <pre>mysql -u root < database_setup.sql</pre>
                            </div>
                        </div>
                    </div>

                    <div class="step">
                        <h4>Configure Database Connection</h4>
                        <p>Update the database credentials in <code>config.php</code> if needed.</p>
                    </div>

                    <div class="step">
                        <h4>Access the Lab</h4>
                        <p>Navigate to <code>http://localhost/AC/Lab-25/</code> in your browser.</p>
                    </div>

                    <div class="step">
                        <h4>Test Credentials</h4>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>attacker</code></td>
                                        <td><code>attacker123</code></td>
                                        <td>Attacker (exploit from here)</td>
                                    </tr>
                                    <tr>
                                        <td><code>victim</code></td>
                                        <td><code>victim123</code></td>
                                        <td>Victim (has private snippets)</td>
                                    </tr>
                                    <tr>
                                        <td><code>alice</code></td>
                                        <td><code>alice123</code></td>
                                        <td>Regular user</td>
                                    </tr>
                                    <tr>
                                        <td><code>admin</code></td>
                                        <td><code>admin123</code></td>
                                        <td>Administrator</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Attack Flow Section -->
            <section id="attack-flow" class="docs-section">
                <h2><i class="fas fa-route"></i> Attack Flow</h2>
                
                <div class="flow-diagram">
                    <div class="flow-steps">
                        <div class="flow-step">
                            <div class="flow-icon attacker"><i class="fas fa-user-secret"></i></div>
                            <div class="flow-text">
                                <strong>1. Attacker logs in</strong>
                                <span>Login as 'attacker' user and navigate to any issue</span>
                            </div>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-down"></i></div>
                        <div class="flow-step">
                            <div class="flow-icon attacker"><i class="fas fa-edit"></i></div>
                            <div class="flow-text">
                                <strong>2. Submit note on issue</strong>
                                <span>Intercept the POST request using DevTools or Burp Suite</span>
                            </div>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-down"></i></div>
                        <div class="flow-step">
                            <div class="flow-icon attacker"><i class="fas fa-exchange-alt"></i></div>
                            <div class="flow-text">
                                <strong>3. Modify the request</strong>
                                <span>Change noteable_type from "issue" to "personal_snippet"</span>
                            </div>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-down"></i></div>
                        <div class="flow-step">
                            <div class="flow-icon server"><i class="fas fa-server"></i></div>
                            <div class="flow-text">
                                <strong>4. Server processes request</strong>
                                <span>No authorization check - note created on victim's private snippet</span>
                            </div>
                        </div>
                        <div class="flow-arrow"><i class="fas fa-arrow-down"></i></div>
                        <div class="flow-step">
                            <div class="flow-icon leak"><i class="fas fa-eye"></i></div>
                            <div class="flow-text">
                                <strong>5. Information leaked</strong>
                                <span>Activity log reveals private snippet title to attacker</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Exploitation Section -->
            <section id="exploitation" class="docs-section">
                <h2><i class="fas fa-crosshairs"></i> Exploitation Steps</h2>

                <div class="steps">
                    <div class="step">
                        <h4>Login as Attacker</h4>
                        <p>Use credentials: <code>attacker</code> / <code>attacker123</code></p>
                    </div>

                    <div class="step">
                        <h4>Navigate to an Issue</h4>
                        <p>Go to Projects → Select any project → Select any issue</p>
                    </div>

                    <div class="step">
                        <h4>Open Developer Tools</h4>
                        <p>Press F12 and go to the Network tab to monitor requests</p>
                    </div>

                    <div class="step">
                        <h4>Submit a Note</h4>
                        <p>Type any content in the note field and click submit</p>
                    </div>

                    <div class="step">
                        <h4>Intercept the Request</h4>
                        <p>
                            Find the POST request to <code>/api/notes.php</code> in the Network tab. 
                            Right-click → "Copy as fetch" or use "Edit and Resend"
                        </p>
                    </div>

                    <div class="step">
                        <h4>Modify and Replay</h4>
                        <p>
                            Change <code>noteable_type</code> from "issue" to "personal_snippet" and 
                            <code>noteable_id</code> to the victim's snippet ID (1-5). Send the request.
                        </p>
                    </div>

                    <div class="step">
                        <h4>Check Activity Log</h4>
                        <p>Go to Activity page to see the leaked private snippet title</p>
                    </div>
                </div>
            </section>

            <!-- Payload Section -->
            <section id="payload" class="docs-section">
                <h2><i class="fas fa-code"></i> Attack Payload</h2>

                <h3>Original Request (Normal)</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">JSON</span>
                    </div>
                    <div class="code-content">
<pre>{
    <span class="string">"noteable_type"</span>: <span class="string">"issue"</span>,
    <span class="string">"noteable_id"</span>: <span class="variable">1</span>,
    <span class="string">"content"</span>: <span class="string">"This is a normal note"</span>
}</pre>
                    </div>
                </div>

                <h3>Modified Request (Attack)</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">JSON</span>
                    </div>
                    <div class="code-content">
<pre>{
    <span class="string">"noteable_type"</span>: <span class="string">"personal_snippet"</span>,  <span class="comment">// Changed from "issue"</span>
    <span class="string">"noteable_id"</span>: <span class="variable">1</span>,                      <span class="comment">// Victim's snippet ID</span>
    <span class="string">"content"</span>: <span class="string">"Attacker was here!"</span>
}</pre>
                    </div>
                </div>

                <h3>Full Fetch Request</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">JavaScript</span>
                    </div>
                    <div class="code-content">
<pre><span class="keyword">fetch</span>(<span class="string">'/AC/Lab-25/api/notes.php'</span>, {
    <span class="variable">method</span>: <span class="string">'POST'</span>,
    <span class="variable">headers</span>: {
        <span class="string">'Content-Type'</span>: <span class="string">'application/json'</span>
    },
    <span class="variable">body</span>: <span class="function">JSON.stringify</span>({
        <span class="variable">noteable_type</span>: <span class="string">'personal_snippet'</span>,
        <span class="variable">noteable_id</span>: <span class="variable">1</span>,
        <span class="variable">content</span>: <span class="string">'IDOR exploit - accessing private data!'</span>
    })
}).<span class="function">then</span>(<span class="variable">r</span> => <span class="variable">r</span>.<span class="function">json</span>())
.<span class="function">then</span>(<span class="variable">console</span>.<span class="function">log</span>);</pre>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i>
                    <div class="alert-content">
                        <h4>Pro Tip</h4>
                        <p>
                            Victim's private snippet IDs are 1-5. Try ID 1 for "SECRET_API_KEYS_DO_NOT_SHARE" 
                            or ID 4 for "Production Database Credentials".
                        </p>
                    </div>
                </div>
            </section>

            <!-- Vulnerable Code Section -->
            <section id="vulnerable-code" class="docs-section">
                <h2><i class="fas fa-file-code"></i> Vulnerable Code</h2>

                <h3>getNoteable() Function (config.php)</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">PHP - Vulnerable</span>
                    </div>
                    <div class="code-content">
<pre><span class="keyword">function</span> <span class="function">getNoteable</span>(<span class="variable">$type</span>, <span class="variable">$id</span>) {
    <span class="keyword">global</span> <span class="variable">$pdo</span>;
    
    <span class="keyword">if</span> (<span class="variable">$type</span> === <span class="string">'issue'</span>) {
        <span class="comment">// Issues have proper project-based access (simplified)</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM issues WHERE id = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$id</span>]);
        <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    } 
    <span class="keyword">elseif</span> (<span class="variable">$type</span> === <span class="string">'personal_snippet'</span>) {
        <span class="comment">// VULNERABILITY: No ownership/permission check!</span>
        <span class="comment">// Anyone can access ANY personal snippet</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM personal_snippets WHERE id = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$id</span>]);
        <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    }
    <span class="keyword">return</span> <span class="keyword">null</span>;
}</pre>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="alert-content">
                        <h4>What's Missing?</h4>
                        <p>
                            The function retrieves personal snippets without checking if the current user 
                            is the owner or if the snippet is public. This allows any authenticated user 
                            to interact with any snippet.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Information Leak Section -->
            <section id="info-leak" class="docs-section">
                <h2><i class="fas fa-eye"></i> Information Leak</h2>

                <p>
                    Even if direct access to snippet content was blocked, the activity logging 
                    system creates a secondary vulnerability by storing the snippet title:
                </p>

                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">PHP - Information Leak</span>
                    </div>
                    <div class="code-content">
<pre><span class="comment">// In api/notes.php - createNote() function</span>

<span class="comment">// Get the title of the target (THIS LEAKS PRIVATE SNIPPET TITLES!)</span>
<span class="variable">$targetTitle</span> = <span class="string">''</span>;
<span class="keyword">if</span> (<span class="variable">$noteableType</span> === <span class="string">'personal_snippet'</span>) {
    <span class="variable">$targetTitle</span> = <span class="variable">$noteable</span>[<span class="string">'title'</span>];  <span class="comment">// Private title exposed!</span>
}

<span class="comment">// Log activity - THIS EXPOSES THE PRIVATE SNIPPET TITLE!</span>
<span class="function">logActivity</span>(
    <span class="variable">$_SESSION</span>[<span class="string">'user_id'</span>],
    <span class="string">'created_note'</span>,
    <span class="variable">$noteableType</span>,
    <span class="variable">$noteableId</span>,
    <span class="variable">$targetTitle</span>,  <span class="comment">// &lt;-- The private snippet title is logged here!</span>
    <span class="string">"Created note on {$noteableType} #{$noteableId}"</span>
);</pre>
                    </div>
                </div>

                <p>
                    This means even if the note creation failed, the activity log would still 
                    contain the private snippet's title, accessible to the attacker.
                </p>
            </section>

            <!-- API Endpoints Section -->
            <section id="api-endpoints" class="docs-section">
                <h2><i class="fas fa-plug"></i> API Endpoints</h2>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                                <th>Vulnerable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>POST</code></td>
                                <td><code>/api/notes.php</code></td>
                                <td>Create a new note</td>
                                <td><span class="badge badge-danger">Yes</span></td>
                            </tr>
                            <tr>
                                <td><code>PUT</code></td>
                                <td><code>/api/notes.php</code></td>
                                <td>Update existing note</td>
                                <td><span class="badge badge-danger">Yes</span></td>
                            </tr>
                            <tr>
                                <td><code>DELETE</code></td>
                                <td><code>/api/notes.php</code></td>
                                <td>Delete a note</td>
                                <td><span class="badge badge-info">Partial</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Remediation Section -->
            <section id="remediation" class="docs-section">
                <h2><i class="fas fa-shield-alt"></i> Remediation</h2>

                <h3>Key Fixes Required:</h3>
                <ul>
                    <li><strong>Authorization Check:</strong> Always verify user permissions before accessing resources</li>
                    <li><strong>Consistent Policy:</strong> Apply the same authorization rules regardless of resource type</li>
                    <li><strong>Sanitize Logs:</strong> Never log sensitive information like private resource titles</li>
                    <li><strong>Input Validation:</strong> Validate that the noteable_type is expected for the context</li>
                </ul>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div class="alert-content">
                        <h4>Defense in Depth</h4>
                        <p>
                            Implement multiple layers of protection: input validation, authorization checks, 
                            output sanitization, and audit logging (without sensitive data).
                        </p>
                    </div>
                </div>
            </section>

            <!-- Secure Code Section -->
            <section id="secure-code" class="docs-section">
                <h2><i class="fas fa-lock"></i> Secure Code Example</h2>

                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">PHP - Secure Version</span>
                    </div>
                    <div class="code-content">
<pre><span class="keyword">function</span> <span class="function">getNoteable</span>(<span class="variable">$type</span>, <span class="variable">$id</span>, <span class="variable">$userId</span>) {
    <span class="keyword">global</span> <span class="variable">$pdo</span>;
    
    <span class="keyword">if</span> (<span class="variable">$type</span> === <span class="string">'issue'</span>) {
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM issues WHERE id = ?"</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$id</span>]);
        <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    } 
    <span class="keyword">elseif</span> (<span class="variable">$type</span> === <span class="string">'personal_snippet'</span>) {
        <span class="comment">// SECURE: Check ownership and visibility</span>
        <span class="variable">$stmt</span> = <span class="variable">$pdo</span>-><span class="function">prepare</span>(<span class="string">"
            SELECT * FROM personal_snippets 
            WHERE id = ? 
            AND (
                author_id = ?              -- Owner can access
                OR visibility = 'public'   -- Public snippets
                OR (visibility = 'internal' AND ? > 0)  -- Logged in users
            )
        "</span>);
        <span class="variable">$stmt</span>-><span class="function">execute</span>([<span class="variable">$id</span>, <span class="variable">$userId</span>, <span class="variable">$userId</span>]);
        <span class="keyword">return</span> <span class="variable">$stmt</span>-><span class="function">fetch</span>();
    }
    <span class="keyword">return</span> <span class="keyword">null</span>;
}

<span class="comment">// And for logging - sanitize sensitive info:</span>
<span class="keyword">function</span> <span class="function">logActivitySecure</span>(<span class="variable">$userId</span>, <span class="variable">$action</span>, <span class="variable">$targetType</span>, <span class="variable">$targetId</span>, <span class="variable">$details</span>) {
    <span class="comment">// Don't log sensitive titles for private resources</span>
    <span class="variable">$safeTitle</span> = (<span class="variable">$targetType</span> === <span class="string">'personal_snippet'</span>) 
        ? <span class="string">'[Private Snippet]'</span> 
        : <span class="variable">$details</span>;
    
    <span class="comment">// Log with sanitized data...</span>
}</pre>
                    </div>
                </div>
            </section>

            <!-- References Section -->
            <section id="references" class="docs-section">
                <h2><i class="fas fa-link"></i> References</h2>
                <ul>
                    <li><a href="https://hackerone.com/reports/1557670" target="_blank" style="color: #fc6d26;">HackerOne Report #1557670 - GitLab Notes IDOR</a></li>
                    <li><a href="https://owasp.org/Top10/A01_2021-Broken_Access_Control/" target="_blank" style="color: #fc6d26;">OWASP Top 10 - Broken Access Control</a></li>
                    <li><a href="https://cheatsheetseries.owasp.org/cheatsheets/Insecure_Direct_Object_Reference_Prevention_Cheat_Sheet.html" target="_blank" style="color: #fc6d26;">OWASP IDOR Prevention Cheat Sheet</a></li>
                    <li><a href="https://cwe.mitre.org/data/definitions/639.html" target="_blank" style="color: #fc6d26;">CWE-639: Authorization Bypass Through User-Controlled Key</a></li>
                </ul>
            </section>

            <div class="buttons-row">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Lab
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-play"></i> Start Practicing
                </a>
            </div>
        </main>
    </div>
</body>
</html>
