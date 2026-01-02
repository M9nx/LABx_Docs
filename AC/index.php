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
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 50%, #0a0a0a 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(255, 68, 68, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(255, 68, 68, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        .header {
            background: rgba(10, 10, 10, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            padding: 1rem 2rem;
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
            font-size: 1.6rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }
        .logo:hover {
            text-shadow: 0 0 20px rgba(255, 68, 68, 0.5);
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .header-stats {
            display: flex;
            gap: 1.5rem;
        }
        .header-stat {
            text-align: center;
            padding: 0.5rem 1rem;
            background: rgba(255, 68, 68, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 68, 68, 0.1);
            transition: all 0.3s ease;
        }
        .header-stat:hover {
            border-color: rgba(255, 68, 68, 0.3);
            background: rgba(255, 68, 68, 0.1);
        }
        .header-stat-value {
            font-size: 1.3rem;
            font-weight: bold;
            color: #ff4444;
        }
        .header-stat-label {
            font-size: 0.7rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2.5rem;
            padding: 2rem 0;
        }
        .page-title h1 {
            font-size: 2.8rem;
            background: linear-gradient(135deg, #ff4444, #ff6666, #ff4444);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        .page-title p {
            color: #999;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }
        .quick-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .quick-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            color: #ff6666;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            transform: translateY(-2px);
        }
        .info-banner {
            background: linear-gradient(135deg, rgba(255, 68, 68, 0.1), rgba(255, 68, 68, 0.05));
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .info-banner-icon {
            font-size: 1.5rem;
            width: 50px;
            height: 50px;
            background: rgba(255, 68, 68, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .info-banner-text h3 {
            color: #ff6666;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }
        .info-banner-text p {
            color: #999;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        /* Filter/Search Bar */
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            gap: 0.5rem;
        }
        .filter-btn {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #888;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-btn:hover, .filter-btn.active {
            background: rgba(255, 68, 68, 0.15);
            border-color: rgba(255, 68, 68, 0.4);
            color: #ff6666;
        }
        .search-box {
            flex: 1;
            min-width: 200px;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }
        .search-box:focus {
            border-color: rgba(255, 68, 68, 0.4);
            background: rgba(255, 68, 68, 0.05);
        }
        .search-box::placeholder {
            color: #666;
        }
        
        /* Table Styles */
        .labs-table-container {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 68, 68, 0.15);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .labs-table {
            width: 100%;
            border-collapse: collapse;
        }
        .labs-table thead {
            background: linear-gradient(135deg, rgba(255, 68, 68, 0.12), rgba(255, 68, 68, 0.08));
        }
        .labs-table th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-weight: 600;
            color: #ff6666;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .labs-table th:first-child {
            width: 70px;
            text-align: center;
        }
        .labs-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }
        .labs-table tbody tr:last-child {
            border-bottom: none;
        }
        .labs-table tbody tr:hover {
            background: rgba(255, 68, 68, 0.06);
        }
        .labs-table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }
        .lab-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-radius: 10px;
            font-weight: bold;
            font-size: 0.95rem;
            color: white;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(255, 68, 68, 0.3);
            transition: all 0.3s ease;
        }
        tr:hover .lab-number {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.4);
        }
        .lab-info h3 {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 0.3rem;
            font-weight: 600;
            line-height: 1.4;
        }
        .lab-info p {
            color: #777;
            font-size: 0.85rem;
            line-height: 1.4;
            margin: 0;
        }
        .difficulty-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .difficulty-badge.apprentice {
            background: rgba(0, 200, 83, 0.15);
            color: #00c853;
            border: 1px solid rgba(0, 200, 83, 0.25);
        }
        .difficulty-badge.practitioner {
            background: rgba(255, 170, 0, 0.15);
            color: #ffaa00;
            border: 1px solid rgba(255, 170, 0, 0.25);
        }
        .difficulty-badge.expert {
            background: rgba(255, 68, 68, 0.2);
            color: #ff4444;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }
        .vulnerability-tag {
            display: inline-block;
            padding: 0.3rem 0.7rem;
            background: rgba(100, 100, 255, 0.12);
            color: #9999ff;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }
        .objective-text {
            color: #888;
            font-size: 0.85rem;
            max-width: 220px;
            line-height: 1.4;
        }
        .btn-start {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.1rem;
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(255, 68, 68, 0.3);
        }
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.5);
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
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 68, 68, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.1);
            border-radius: 16px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 68, 68, 0.1);
            border-radius: 12px;
            padding: 1.25rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            border-color: rgba(255, 68, 68, 0.4);
            transform: translateY(-3px);
            background: rgba(255, 68, 68, 0.05);
        }
        .stat-card-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .stat-card-value {
            font-size: 1.75rem;
            font-weight: bold;
            color: #ff4444;
        }
        .stat-card-label {
            color: #777;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.25rem;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 68, 68, 0.1);
            color: #555;
            font-size: 0.85rem;
        }
        .footer a {
            color: #ff6666;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .labs-table th:nth-child(5),
            .labs-table td:nth-child(5) {
                display: none;
            }
            .footer-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 900px) {
            .labs-table th:nth-child(3),
            .labs-table td:nth-child(3),
            .labs-table th:nth-child(4),
            .labs-table td:nth-child(4) {
                display: none;
            }
            .header-stats {
                display: none;
            }
            .quick-actions {
                flex-wrap: wrap;
            }
        }
        @media (max-width: 600px) {
            .page-title h1 {
                font-size: 1.8rem;
            }
            .filter-bar {
                flex-direction: column;
            }
            .footer-stats {
                grid-template-columns: 1fr 1fr;
            }
            .container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">
                <span class="logo-icon">🔐</span>
                <span>AC Labs</span>
            </a>
            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value">30</div>
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
                    <div class="header-stat-value">26</div>
                    <div class="header-stat-label">Practitioner</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>🛡️ Access Control Vulnerabilities</h1>
            <p>Master the art of identifying and exploiting access control flaws. Hands-on labs covering IDOR, privilege escalation, broken authentication, and real-world HackerOne reports.</p>
            <div class="quick-actions">
                <a href="setup-all-databases.php" class="quick-action-btn">🗄️ Setup Databases</a>
                <a href="#apprentice" class="quick-action-btn">🟢 Apprentice Labs</a>
                <a href="#practitioner" class="quick-action-btn">🟠 Practitioner Labs</a>
                <a href="#expert" class="quick-action-btn">🔴 Expert Labs</a>
            </div>
        </div>

        <div class="info-banner">
            <div class="info-banner-icon">⚠️</div>
            <div class="info-banner-text">
                <h3>About Access Control</h3>
                <p>Access control vulnerabilities occur when applications fail to restrict access to resources. Attackers can view sensitive data, modify records, or perform admin actions without proper authorization.</p>
            </div>
        </div>

        <div class="filter-bar">
            <div class="filter-group">
                <button class="filter-btn active" onclick="filterLabs('all')">All (30)</button>
                <button class="filter-btn" onclick="filterLabs('apprentice')">🟢 Apprentice (3)</button>
                <button class="filter-btn" onclick="filterLabs('practitioner')">🟠 Practitioner (26)</button>
                <button class="filter-btn" onclick="filterLabs('expert')">🔴 Expert (1)</button>
            </div>
            <input type="text" class="search-box" placeholder="🔍 Search labs... (Press /)" onkeyup="searchLabs(this.value)">
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
                    <tr data-difficulty="apprentice">
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
                        <td><a href="Lab-01/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="apprentice">
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
                        <td><a href="Lab-02/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="apprentice">
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
                        <td><a href="Lab-03/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-04/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-05/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-06/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-07/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-08/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-09/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-10/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-11/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-12/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-13/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-14/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-15/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-16/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
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
                        <td><a href="Lab-17/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">18</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Expire Other User Sessions</h3>
                                    <?php if (in_array(18, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Session expiration endpoint accepts account_id without ownership validation</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Session Mgmt</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Expire another user's sessions by manipulating account_id parameter</td>
                        <td><a href="Lab-18/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">19</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Delete Users Saved Projects</h3>
                                    <?php if (in_array(19, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Delete endpoint accepts saved_id without checking resource ownership</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Delete Action</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Delete another user's saved projects by manipulating saved_id parameter</td>
                        <td><a href="Lab-19/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">20</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR VIEW & DELETE & Create API Keys</h3>
                                    <?php if (in_array(20, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>API endpoint checks membership but not role permissions for sensitive operations</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - RBAC Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Create, view, and delete API keys as a member without admin/owner role</td>
                        <td><a href="Lab-20/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">21</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Stocky Low Stock Settings Columns</h3>
                                    <?php if (in_array(21, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Settings endpoint accepts settings_id without ownership verification</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Settings Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Modify another store's column visibility settings via settings_id manipulation</td>
                        <td><a href="Lab-21/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">22</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR on Booking Detail & Bids</h3>
                                    <?php if (in_array(22, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Ride-sharing API returns booking details, bids, and driver info for any booking_id</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Info Disclosure</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Access victim's booking details, driver bids, and sensitive location data</td>
                        <td><a href="Lab-22/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">23</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR at AddTagToAssets Operation</h3>
                                    <?php if (in_array(23, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Enumerate private custom tags via base64-encoded sequential GraphQL-style IDs</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Enumeration</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Discover victim's private tags by bruteforcing encoded internal IDs</td>
                        <td><a href="Lab-23/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">24</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Exposes All ML Models</h3>
                                    <?php if (in_array(24, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>GraphQL API returns private ML models and secrets via sequential model IDs</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - GraphQL API</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Enumerate private ML models to extract API keys and credentials from metadata</td>
                        <td><a href="Lab-24/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">25</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>Notes IDOR on Personal Snippets</h3>
                                    <?php if (in_array(25, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Create notes on private snippets by modifying noteable_type parameter in API requests</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR + Info Leak</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Access victim's private snippets and discover leaked titles via activity log</td>
                        <td><a href="Lab-25/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="expert">
                        <td><div class="lab-number">26</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR in API Applications - Credential Leak</h3>
                                    <?php if (in_array(26, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>API application update endpoint leaks OAuth credentials when validation fails</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR + Credential Leak</span></td>
                        <td><span class="difficulty-badge expert">🔴 Expert</span></td>
                        <td class="objective-text">Extract victim's API Client Secret via validation error disclosure</td>
                        <td><a href="Lab-26/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">27</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR in Stats API - Trading Data Exposure</h3>
                                    <?php if (in_array(27, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Stats API endpoints accept any account number without ownership verification</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Financial Data</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">View equity, net profit, and trading volume of any MT trading account</td>
                        <td><a href="Lab-27/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <!-- Lab 28 -->
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">28</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Team Member Removal - MTN Developers Portal</h3>
                                    <?php if (in_array(28, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Remove any user from any team via IDOR + Information Disclosure</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Team Management</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Remove Carol from Bob's team without authorization - HackerOne #1448475</td>
                        <td><a href="Lab-28/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <!-- Lab 29 -->
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">29</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Newsletter Subscriber Exposure - LinkedPro</h3>
                                    <?php if (in_array(29, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>View subscriber lists of any newsletter without ownership verification</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Privacy Leak</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Access subscribers of another user's newsletter via IDOR - LinkedIn Style</td>
                        <td><a href="Lab-29/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                    <!-- Lab 30 -->
                    <tr data-difficulty="practitioner">
                        <td><div class="lab-number">30</div></td>
                        <td>
                            <div class="lab-info">
                                <div class="lab-status">
                                    <h3>IDOR Low Stock Variant Settings - Stocky App</h3>
                                    <?php if (in_array(30, $solvedLabs)): ?>
                                        <span class="solved-badge">✓ Solved</span>
                                    <?php endif; ?>
                                </div>
                                <p>Modify column visibility settings of any store via settings_id manipulation</p>
                            </div>
                        </td>
                        <td><span class="vulnerability-tag">IDOR - Settings Bypass</span></td>
                        <td><span class="difficulty-badge practitioner">🟠 Practitioner</span></td>
                        <td class="objective-text">Change another store's Low Stock Variant column settings - Shopify/Stocky Style</td>
                        <td><a href="Lab-30/lab-description.php" class="btn-start">Start →</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer-stats">
            <div class="stat-card">
                <div class="stat-card-icon">🎯</div>
                <div class="stat-card-value">30</div>
                <div class="stat-card-label">Active Labs</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">🔓</div>
                <div class="stat-card-value">27</div>
                <div class="stat-card-label">Vulnerability Types</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">📚</div>
                <div class="stat-card-value">30</div>
                <div class="stat-card-label">Documentation Pages</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon">⏱️</div>
                <div class="stat-card-value">~19h</div>
                <div class="stat-card-label">Total Duration</div>
            </div>
        </div>
        
        <footer class="footer">
            <p>🔐 AC Labs - Access Control Security Training Platform</p>
            <p style="margin-top: 0.5rem;">Built for educational purposes • <a href="setup-all-databases.php">Setup All Databases</a></p>
        </footer>
    </div>
    
    <script>
        let currentFilter = 'all';
        let currentSearch = '';
        
        function filterLabs(difficulty) {
            currentFilter = difficulty;
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            applyFilters();
            updateFilterCount();
        }
        
        function searchLabs(query) {
            currentSearch = query.toLowerCase();
            applyFilters();
        }
        
        function applyFilters() {
            const rows = document.querySelectorAll('.labs-table tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const rowDifficulty = row.dataset.difficulty;
                const text = row.textContent.toLowerCase();
                
                const matchesFilter = currentFilter === 'all' || rowDifficulty === currentFilter;
                const matchesSearch = currentSearch === '' || text.includes(currentSearch);
                
                if (matchesFilter && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update result count
            const searchBox = document.querySelector('.search-box');
            if (currentSearch !== '') {
                searchBox.style.borderColor = visibleCount > 0 ? 'rgba(0, 200, 83, 0.5)' : 'rgba(255, 68, 68, 0.5)';
            } else {
                searchBox.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            }
        }
        
        function updateFilterCount() {
            const counts = { all: 30, apprentice: 3, practitioner: 26, expert: 1 };
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => {
                const diff = btn.textContent.includes('All') ? 'all' : 
                             btn.textContent.includes('Apprentice') ? 'apprentice' :
                             btn.textContent.includes('Practitioner') ? 'practitioner' : 'expert';
            });
        }
        
        // Add keyboard shortcut for search
        document.addEventListener('keydown', (e) => {
            if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                document.querySelector('.search-box').focus();
            }
            if (e.key === 'Escape') {
                document.querySelector('.search-box').blur();
                document.querySelector('.search-box').value = '';
                searchLabs('');
            }
        });
    </script>
</body>
</html>
