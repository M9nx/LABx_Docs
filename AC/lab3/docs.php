<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 3 Solution - User role controlled by request parameter</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            color: #ffffff;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            color: #ff4444;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .subtitle {
            color: #cccccc;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .difficulty-badge {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
        }

        .solution-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #ff6666;
            font-size: 1.8rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 102, 102, 0.3);
        }

        .step-list {
            list-style: none;
            padding: 0;
            counter-reset: step-counter;
        }

        .step-list li {
            counter-increment: step-counter;
            background: rgba(0, 0, 0, 0.2);
            margin: 15px 0;
            padding: 20px;
            border-left: 4px solid #ff4444;
            border-radius: 5px;
            position: relative;
        }

        .step-list li:before {
            content: "Step " counter(step-counter);
            position: absolute;
            top: -10px;
            left: 15px;
            background: #ff4444;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .step-title {
            color: #ff9999;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .vulnerability-info {
            background: linear-gradient(45deg, rgba(255, 193, 7, 0.15), rgba(255, 152, 0, 0.15));
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .vulnerability-info h4 {
            color: #ffd54f;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .code-block {
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            color: #f8f8f2;
        }

        .code-block pre {
            margin: 0;
            white-space: pre-wrap;
        }

        .highlight {
            background: rgba(255, 68, 68, 0.2);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            border-color: #ff4444;
        }

        .btn-secondary {
            background: transparent;
            color: #ff4444;
            border-color: #ff4444;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
        }

        .screenshot-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px dashed #666;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            text-align: center;
        }

        .screenshot-box p {
            color: #999;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Lab 3 Solution</h1>
            <p class="subtitle">User role controlled by request parameter</p>
            <div class="difficulty-badge">Apprentice</div>
        </div>

        <div class="solution-section">
            <h2 class="section-title">🎯 Solution Walkthrough</h2>
            
            <ol class="step-list">
                <li>
                    <div class="step-title">Access the application</div>
                    <p>Navigate to the lab application and log in using any of the provided credentials, for example:</p>
                    <div class="code-block">
                        <pre>Username: wiener
Password: peter</pre>
                    </div>
                </li>
                
                <li>
                    <div class="step-title">Examine your account</div>
                    <p>After logging in, you'll be redirected to your profile page. Notice that your role is displayed as "user" and the Admin cookie is set to "false".</p>
                </li>
                
                <li>
                    <div class="step-title">Open Developer Tools</div>
                    <p>Press <span class="highlight">F12</span> to open your browser's Developer Tools, then navigate to the <span class="highlight">Application</span> tab (Chrome) or <span class="highlight">Storage</span> tab (Firefox).</p>
                </li>
                
                <li>
                    <div class="step-title">Locate the Admin cookie</div>
                    <p>In the Developer Tools:</p>
                    <ul style="color: #cccccc; margin-left: 20px; margin-top: 10px;">
                        <li>Expand the "Cookies" section</li>
                        <li>Click on your site (localhost)</li>
                        <li>Find the "Admin" cookie with value "false"</li>
                    </ul>
                </li>
                
                <li>
                    <div class="step-title">Modify the cookie value</div>
                    <p>Double-click on the "Admin" cookie value and change it from <span class="highlight">false</span> to <span class="highlight">true</span>.</p>
                    <div class="screenshot-box">
                        <p>[Screenshot: Browser Developer Tools showing cookie modification]</p>
                    </div>
                </li>
                
                <li>
                    <div class="step-title">Access the admin panel</div>
                    <p>Navigate to the admin panel by clicking the "Admin Panel" button or going directly to <span class="highlight">admin.php</span>. You should now have administrative access!</p>
                </li>
                
                <li>
                    <div class="step-title">Delete the target user</div>
                    <p>In the admin panel, locate the user <strong>"carlos"</strong> in the user management table and click the "Delete" button to complete the lab objective.</p>
                </li>
            </ol>
        </div>

        <div class="vulnerability-info">
            <h4>🔐 Vulnerability Explanation</h4>
            <p>
                This lab demonstrates a critical access control vulnerability where the application relies on client-side parameters 
                (specifically, browser cookies) to determine user privileges. The vulnerable code pattern looks like this:
            </p>
            <div class="code-block">
                <pre><span style="color: #66d9ef;">// VULNERABLE: Setting admin status in cookie</span>
<span style="color: #f92672;">$admin_status</span> = (<span style="color: #f92672;">$user</span>[<span style="color: #e6db74;">'role'</span>] === <span style="color: #e6db74;">'admin'</span>) ? <span style="color: #e6db74;">'true'</span> : <span style="color: #e6db74;">'false'</span>;
<span style="color: #a6e22e;">setcookie</span>(<span style="color: #e6db74;">'Admin'</span>, <span style="color: #f92672;">$admin_status</span>, <span style="color: #a6e22e;">time</span>() + <span style="color: #ae81ff;">3600</span>, <span style="color: #e6db74;">'/'</span>);

<span style="color: #66d9ef;">// Later verification only checks the cookie</span>
<span style="color: #f92672;">$is_admin</span> = <span style="color: #a6e22e;">isset</span>(<span style="color: #f92672;">$_COOKIE</span>[<span style="color: #e6db74;">'Admin'</span>]) && <span style="color: #f92672;">$_COOKIE</span>[<span style="color: #e6db74;">'Admin'</span>] === <span style="color: #e6db74;">'true'</span>;</pre>
            </div>
            <p>
                The application trusts the client-side cookie without re-validating the user's actual role against the database, 
                making privilege escalation trivial for any attacker who can modify browser cookies.
            </p>
        </div>

        <div class="solution-section">
            <h2 class="section-title">🛡️ Remediation</h2>
            
            <p style="color: #cccccc; margin-bottom: 20px;">To fix this vulnerability, implement proper server-side authorization:</p>
            
            <div class="code-block">
                <pre><span style="color: #66d9ef;">// SECURE: Validate role from database</span>
<span style="color: #f92672;">function</span> <span style="color: #a6e22e;">isUserAdmin</span>(<span style="color: #f92672;">$user_id</span>) {
    <span style="color: #f92672;">global</span> <span style="color: #f92672;">$pdo</span>;
    <span style="color: #f92672;">$stmt</span> = <span style="color: #f92672;">$pdo</span>-><span style="color: #a6e22e;">prepare</span>(<span style="color: #e6db74;">"SELECT role FROM users WHERE id = ?"</span>);
    <span style="color: #f92672;">$stmt</span>-><span style="color: #a6e22e;">execute</span>([<span style="color: #f92672;">$user_id</span>]);
    <span style="color: #f92672;">$user</span> = <span style="color: #f92672;">$stmt</span>-><span style="color: #a6e22e;">fetch</span>();
    <span style="color: #f92672;">return</span> <span style="color: #f92672;">$user</span> && <span style="color: #f92672;">$user</span>[<span style="color: #e6db74;">'role'</span>] === <span style="color: #e6db74;">'admin'</span>;
}

<span style="color: #66d9ef;">// Use session-based role validation</span>
<span style="color: #f92672;">$is_admin</span> = <span style="color: #a6e22e;">isset</span>(<span style="color: #f92672;">$_SESSION</span>[<span style="color: #e6db74;">'user_id'</span>]) && <span style="color: #a6e22e;">isUserAdmin</span>(<span style="color: #f92672;">$_SESSION</span>[<span style="color: #e6db74;">'user_id'</span>]);</pre>
            </div>
            
            <p style="color: #cccccc;">
                <strong>Key principles:</strong> Never trust client-side data for authorization decisions. 
                Always validate user roles and permissions server-side by consulting authoritative sources like databases or secure session data.
            </p>
        </div>

        <div class="nav-buttons">
            <a href="index.php" class="btn btn-secondary">← Back to Lab</a>
            <a href="login.php" class="btn btn-primary">🚀 Try Lab Again</a>
        </div>
    </div>
</body>
</html>