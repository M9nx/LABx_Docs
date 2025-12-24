<?php
require_once 'progress.php';
$solvedLabs = getAllSolvedLabs();
$solvedCount = getSolvedCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control Labs - WebSecurity Academy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .header-stats {
            display: flex;
            gap: 2rem;
        }
        .header-stat {
            text-align: center;
        }
        .header-stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4444;
        }
        .header-stat-label {
            font-size: 0.75rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        .page-title h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #ff4444, #ff6666);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .page-title p {
            color: #888;
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.7;
        }
        .info-banner {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 12px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .info-banner-icon {
            font-size: 2rem;
        }
        .info-banner-text h3 {
            color: #ff6666;
            margin-bottom: 0.3rem;
        }
        .info-banner-text p {
            color: #aaa;
            font-size: 0.95rem;
        }
        
        /* Table Styles */
        .labs-table-container {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        .labs-table {
            width: 100%;
            border-collapse: collapse;
        }
        .labs-table thead {
            background: rgba(255, 68, 68, 0.15);
        }
        .labs-table th {
            padding: 1.2rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: #ff6666;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
        }
        .labs-table th:first-child {
            width: 80px;
            text-align: center;
        }
        .labs-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .labs-table tbody tr:last-child {
            border-bottom: none;
        }
        .labs-table tbody tr:hover {
            background: rgba(255, 68, 68, 0.08);
        }
        .labs-table td {
            padding: 1.2rem 1.5rem;
            vertical-align: middle;
        }
        .lab-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 12px;
            font-weight: bold;
            font-size: 1.1rem;
            color: white;
            margin: 0 auto;
        }
        .lab-info h3 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 0.4rem;
            font-weight: 600;
        }
        .lab-info p {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }
        .difficulty-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .difficulty-badge.apprentice {
            background: rgba(0, 200, 83, 0.2);
            color: #00c853;
            border: 1px solid rgba(0, 200, 83, 0.3);
        }
        .difficulty-badge.practitioner {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
            border: 1px solid rgba(255, 170, 0, 0.3);
        }
        .difficulty-badge.expert {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .vulnerability-tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(100, 100, 255, 0.15);
            color: #8888ff;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .objective-text {
            color: #aaa;
            font-size: 0.9rem;
            max-width: 250px;
        }
        .btn-start {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .btn-start svg {
            width: 16px;
            height: 16px;
        }
        
        /* Solved Badge */
        .solved-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
            border: 1px solid rgba(0, 255, 0, 0.4);
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 0.5rem;
        }
        .lab-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Footer Stats */
        .footer-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            border-color: rgba(255, 68, 68, 0.5);
            transform: translateY(-3px);
        }
        .stat-card-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .stat-card-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff4444;
        }
        .stat-card-label {
            color: #888;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .labs-table th:nth-child(4),
            .labs-table td:nth-child(4) {
                display: none;
            }
        }
        @media (max-width: 900px) {
            .labs-table th:nth-child(3),
            .labs-table td:nth-child(3) {
                display: none;
            }
            .header-stats {
                display: none;
            }
        }
        @media (max-width: 600px) {
            .labs-table th:nth-child(5),
            .labs-table td:nth-child(5) {
                display: none;
            }
            .page-title h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 AC Labs</a>
            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value">17</div>
                    <div class="header-stat-label">Total Labs</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value"><?php echo $solvedCount; ?></div>
                    <div class="header-stat-label">Solved</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">3</div>
                    <div class="header-stat-label">Apprentice</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">14</div>
                    <div class="header-stat-label">Practitioner</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>🛡️ Access Control Vulnerabilities</h1>
            <p>Master the art of identifying and exploiting access control flaws. These hands-on labs cover IDOR, privilege escalation, broken authentication, and more.</p>
        </div>

        <div class="info-banner">
            <div class="info-banner-icon">⚠️</div>
            <div class="info-banner-text">
                <h3>About Access Control</h3>
                <p>Access control vulnerabilities occur when applications fail to properly restrict access to resources. Attackers can view sensitive data, modify records, or perform admin actions without authorization.</p>
            </div>
        </div>

        <div class="labs-table-container">
            <table class="labs-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lab Name</th>
                        <th>Vulnerability Type</th>
                        <th>Difficulty</th>
                        <th>Objective</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="lab-number">1</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Unprotected Admin Functionality</h3>
                                    <?php if (in_array(1, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Administrative panel exposed without authentication</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Information Disclosure</span></td>
                        <td><span class="difficulty-badge apprentice">🟢 Apprentice</span></td>
                        <td class="objective-text">Access admin panel via robots.txt and delete carlos</td>
                        <td><a href="lab1/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">2</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Unprotected Admin with Unpredictable URL</h3>
                                    <?php if (in_array(2, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Admin URL hidden but leaked in client-side code</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Client-Side Disclosure</span></td>
                        <td><span class="difficulty-badge apprentice">🟢 Apprentice</span></td>
                        <td class="objective-text">Find admin panel in JavaScript and delete carlos</td>
                        <td><a href="lab2/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">3</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>User Role Controlled by Request Parameter</h3>
                                    <?php if (in_array(3, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Cookie-based role can be manipulated client-side</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Cookie Manipulation</span></td>
                        <td><span class="difficulty-badge apprentice">🟢 Apprentice</span></td>
                        <td class="objective-text">Modify Admin cookie to gain admin access</td>
                        <td><a href="lab3/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">4</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>User Role Modified in Profile</h3>
                                    <?php if (in_array(4, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Mass assignment allows roleid manipulation</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Mass Assignment</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Add roleid to JSON request for admin privileges</td>
                        <td><a href="lab4/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">5</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>User ID Controlled by Request Parameter</h3>
                                    <?php if (in_array(5, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Horizontal privilege escalation via IDOR</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Change user ID to access carlos's API key</td>
                        <td><a href="lab5/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">6</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR with Unpredictable User IDs</h3>
                                    <?php if (in_array(6, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>GUIDs leaked through information disclosure</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR + GUID Leak</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Find carlos's GUID in blog to access profile</td>
                        <td><a href="lab6/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">7</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Data Leakage in Redirect Response</h3>
                                    <?php if (in_array(7, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Sensitive data exposed in redirect body</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Redirect Leakage</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Capture API key from redirect response body</td>
                        <td><a href="lab7/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">8</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Password Disclosure in Account Page</h3>
                                    <?php if (in_array(8, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>User password exposed in masked HTML input field</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Password Disclosure</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Extract admin password from HTML source</td>
                        <td><a href="lab8/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">9</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Insecure Direct Object References</h3>
                                    <?php if (in_array(9, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Chat transcripts accessible via predictable URLs</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - File Access</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Find carlos's password in chat transcript</td>
                        <td><a href="lab9/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">10</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>URL-based Access Control Bypass</h3>
                                    <?php if (in_array(10, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>X-Original-URL header bypasses front-end restrictions</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Header Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Bypass blocked /admin path and delete carlos</td>
                        <td><a href="lab10/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">11</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Method-Based Access Control Bypass</h3>
                                    <?php if (in_array(11, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Admin action restricted on POST but not GET method</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">HTTP Method Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Change HTTP method to bypass access control</td>
                        <td><a href="lab11/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">12</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Multi-Step Process Bypass</h3>
                                    <?php if (in_array(12, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Confirmation step lacks authorization check</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Multi-Step Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Skip to unprotected confirmation step</td>
                        <td><a href="lab12/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">13</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Referer-based Access Control</h3>
                                    <?php if (in_array(13, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Admin functions trust HTTP Referer header for authorization</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">Header-based AC</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Spoof Referer header to gain admin privileges</td>
                        <td><a href="lab13/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">14</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Banner Deletion</h3>
                                    <?php if (in_array(14, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Delete endpoint validates parent access but not object ownership</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Object Level</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Delete another manager's banner via IDOR</td>
                        <td><a href="lab14/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">15</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR PII Leakage</h3>
                                    <?php if (in_array(15, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>API returns any user's PII based on email parameter without authorization</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - PII Exposure</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Extract another user's phone number, address, and private notes</td>
                        <td><a href="lab15/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">16</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Slowvote Visibility Bypass</h3>
                                    <?php if (in_array(16, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>API endpoint returns poll data without checking visibility permissions</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - API Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Access restricted polls via API that bypasses UI access controls</td>
                        <td><a href="lab16/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr>
                        <td><div class="lab-number">17</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR External Status Check Info Disclosure</h3>
                                    <?php if (in_array(17, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>API returns status check data from any project without ownership validation</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Status Check</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Access private project's external URL and API key via status check IDOR</td>
                        <td><a href="lab17/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer-stats">
            <div class="stat-card">
                <div class="stat-card-icon">🎯</div>
                <div class="stat-card-value">17</div>
                <div class="stat-card-label">Active Labs</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">🔓</div>
                <div class="stat-card-value">15</div>
                <div class="stat-card-label">Vulnerability Types</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">📚</div>
                <div class="stat-card-value">17</div>
                <div class="stat-card-label">Documentation Pages</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">⏱️</div>
                <div class="stat-card-value">~5h</div>
                <div class="stat-card-label">Total Duration</div>
            </div>
        </div>
    </div>
</body>
</html>