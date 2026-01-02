<?php
require_once 'config.php';
$pageTitle = "Lab Description";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Description - Lab 25 Notes IDOR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* Main Container */
        .main-container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        /* Header Card */
        .header-card {
            background: linear-gradient(135deg, rgba(252, 109, 38, 0.15), rgba(252, 109, 38, 0.05));
            border: 1px solid rgba(252, 109, 38, 0.3);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .lab-number {
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 10px 40px rgba(252, 109, 38, 0.3);
        }

        .header-card h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .header-card p {
            color: #b0b0b0;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .badges-row {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-danger {
            background: rgba(255, 77, 77, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .badge-info {
            background: rgba(252, 109, 38, 0.2);
            color: #fc6d26;
            border: 1px solid rgba(252, 109, 38, 0.3);
        }

        .badge-success {
            background: rgba(76, 217, 100, 0.2);
            color: #4cd964;
            border: 1px solid rgba(76, 217, 100, 0.3);
        }

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.75rem;
        }

        .info-card h3 {
            color: #fc6d26;
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        .info-card ul {
            color: #b0b0b0;
            padding-left: 1.25rem;
            line-height: 1.8;
        }

        .info-card ul li {
            margin-bottom: 0.5rem;
        }

        /* Scenario Card */
        .scenario-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .scenario-card h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .scenario-card h2 i {
            color: #fc6d26;
        }

        .scenario-steps {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .scenario-step {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .step-content strong {
            color: #fff;
            display: block;
            margin-bottom: 0.25rem;
        }

        .step-content span {
            color: #808080;
            font-size: 0.9rem;
        }

        /* Credentials Card */
        .creds-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .creds-card h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .creds-card h2 i {
            color: #fc6d26;
        }

        .creds-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .cred-item {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }

        .cred-item.attacker {
            border-color: rgba(255, 77, 77, 0.3);
            background: rgba(255, 77, 77, 0.05);
        }

        .cred-item.victim {
            border-color: rgba(76, 217, 100, 0.3);
            background: rgba(76, 217, 100, 0.05);
        }

        .cred-role {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .cred-item.attacker .cred-role {
            color: #ff6b6b;
        }

        .cred-item.victim .cred-role {
            color: #4cd964;
        }

        .cred-item .cred-role {
            color: #fc6d26;
        }

        .cred-username {
            font-size: 1.1rem;
            color: #fff;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .cred-password {
            color: #808080;
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
        }

        /* Learning Card */
        .learning-card {
            background: linear-gradient(135deg, rgba(76, 217, 100, 0.1), rgba(76, 217, 100, 0.02));
            border: 1px solid rgba(76, 217, 100, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .learning-card h2 {
            color: #4cd964;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .learning-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .learning-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: #b0b0b0;
        }

        .learning-item i {
            color: #4cd964;
            margin-top: 4px;
        }

        /* Buttons */
        .btn {
            padding: 0.875rem 1.75rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #fc6d26, #e24a0f);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(252, 109, 38, 0.3);
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
            justify-content: center;
            flex-wrap: wrap;
        }

        /* CVE Reference */
        .cve-reference {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }

        .cve-reference p {
            color: #808080;
            font-size: 0.9rem;
        }

        .cve-reference a {
            color: #fc6d26;
            text-decoration: none;
        }

        .cve-reference a:hover {
            text-decoration: underline;
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
                <a href="docs.php"><i class="fas fa-book"></i> Docs</a>
                <a href="lab-description.php" class="active"><i class="fas fa-flask"></i> Lab Info</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- Header -->
        <div class="header-card">
            <div class="lab-number">25</div>
            <h1>Notes IDOR on Personal Snippets</h1>
            <p>
                Exploit an Insecure Direct Object Reference vulnerability to access private 
                code snippets and discover sensitive information through an information leak.
            </p>
            <div class="badges-row">
                <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> High Severity</span>
                <span class="badge badge-info"><i class="fas fa-bug"></i> IDOR</span>
                <span class="badge badge-info"><i class="fas fa-eye"></i> Info Leak</span>
                <span class="badge badge-success"><i class="fas fa-code"></i> Real-World Bug</span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <h3><i class="fas fa-bullseye"></i> Objective</h3>
                <p>
                    Access a victim's private code snippet by exploiting the broken authorization 
                    in the notes API, then extract the snippet title from the activity log.
                </p>
            </div>
            <div class="info-card">
                <h3><i class="fas fa-tools"></i> Skills Practiced</h3>
                <ul>
                    <li>API parameter manipulation</li>
                    <li>Request interception/modification</li>
                    <li>IDOR exploitation</li>
                    <li>Information disclosure discovery</li>
                </ul>
            </div>
            <div class="info-card">
                <h3><i class="fas fa-clock"></i> Estimated Time</h3>
                <p>
                    <strong>15-30 minutes</strong><br>
                    Depending on your familiarity with browser developer tools and API testing.
                </p>
            </div>
            <div class="info-card">
                <h3><i class="fas fa-graduation-cap"></i> Difficulty</h3>
                <p>
                    <strong>Medium</strong><br>
                    Requires understanding of HTTP requests and parameter manipulation.
                </p>
            </div>
        </div>

        <!-- Attack Scenario -->
        <div class="scenario-card">
            <h2><i class="fas fa-map"></i> Attack Scenario</h2>
            <div class="scenario-steps">
                <div class="scenario-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Login as Attacker</strong>
                        <span>Authenticate with the attacker account to access the platform</span>
                    </div>
                </div>
                <div class="scenario-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Navigate to an Issue</strong>
                        <span>Go to any project and select an issue where you can add notes</span>
                    </div>
                </div>
                <div class="scenario-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>Intercept the Note Request</strong>
                        <span>Use DevTools Network tab to capture the POST request when submitting a note</span>
                    </div>
                </div>
                <div class="scenario-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <strong>Modify the Request</strong>
                        <span>Change noteable_type from "issue" to "personal_snippet" and set noteable_id to victim's snippet</span>
                    </div>
                </div>
                <div class="scenario-step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <strong>Discover the Leak</strong>
                        <span>Check your Activity page to see the private snippet title exposed in the log</span>
                    </div>
                </div>
                <div class="scenario-step">
                    <div class="step-number">6</div>
                    <div class="step-content">
                        <strong>Complete the Lab</strong>
                        <span>Submit the leaked snippet title on the success page</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Credentials -->
        <div class="creds-card">
            <h2><i class="fas fa-key"></i> Test Credentials</h2>
            <div class="creds-grid">
                <div class="cred-item attacker">
                    <div class="cred-role">Attacker</div>
                    <div class="cred-username">attacker</div>
                    <div class="cred-password">attacker123</div>
                </div>
                <div class="cred-item victim">
                    <div class="cred-role">Victim</div>
                    <div class="cred-username">victim</div>
                    <div class="cred-password">victim123</div>
                </div>
                <div class="cred-item">
                    <div class="cred-role">User</div>
                    <div class="cred-username">alice</div>
                    <div class="cred-password">alice123</div>
                </div>
                <div class="cred-item">
                    <div class="cred-role">Admin</div>
                    <div class="cred-username">admin</div>
                    <div class="cred-password">admin123</div>
                </div>
            </div>
        </div>

        <!-- Learning Outcomes -->
        <div class="learning-card">
            <h2><i class="fas fa-lightbulb"></i> What You'll Learn</h2>
            <div class="learning-list">
                <div class="learning-item">
                    <i class="fas fa-check"></i>
                    <span>How IDOR vulnerabilities occur when authorization checks are inconsistent across different resource types</span>
                </div>
                <div class="learning-item">
                    <i class="fas fa-check"></i>
                    <span>The importance of validating user permissions for every request, not just the UI-visible ones</span>
                </div>
                <div class="learning-item">
                    <i class="fas fa-check"></i>
                    <span>How logging systems can inadvertently leak sensitive information</span>
                </div>
                <div class="learning-item">
                    <i class="fas fa-check"></i>
                    <span>Techniques for intercepting and modifying API requests using browser developer tools</span>
                </div>
                <div class="learning-item">
                    <i class="fas fa-check"></i>
                    <span>Real-world impact of access control vulnerabilities in code hosting platforms</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="buttons-row">
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-play"></i> Start Lab
            </a>
            <a href="docs.php" class="btn btn-secondary">
                <i class="fas fa-book"></i> Read Documentation
            </a>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> All Labs
            </a>
        </div>

        <!-- CVE Reference -->
        <div class="cve-reference">
            <p>
                This lab is based on a real vulnerability reported to GitLab via HackerOne.<br>
                <a href="https://hackerone.com/reports/1557670" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Original Report
                </a>
            </p>
        </div>
    </div>
</body>
</html>
