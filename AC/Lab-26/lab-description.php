<?php
/**
 * Lab 26: Lab Description Page
 * IDOR in API Applications - Pressable-Style
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 26: IDOR in API Applications</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #888;
            text-decoration: none;
            margin-bottom: 2rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: #00b4d8; }
        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }
        .lab-header {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            padding: 2rem;
        }
        .lab-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lab-header h1 {
            font-size: 2rem;
            color: white;
            margin-bottom: 0.5rem;
        }
        .lab-header p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.1rem;
        }
        .lab-content {
            padding: 2rem;
        }
        .section {
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #00b4d8;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 180, 216, 0.2);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section p {
            color: #ccc;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        .section ul {
            color: #ccc;
            padding-left: 1.5rem;
            line-height: 1.9;
        }
        .section li { margin-bottom: 0.5rem; }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .info-item {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }
        .info-item .label {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        .info-item .value {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .tag {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            background: rgba(0, 180, 216, 0.2);
            color: #00b4d8;
            border-radius: 50px;
            font-size: 0.8rem;
            margin: 0.25rem;
        }
        .tag-critical {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6b6b;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', monospace;
            color: #00ff88;
            font-size: 0.9em;
        }
        .highlight-box {
            background: rgba(0, 180, 216, 0.1);
            border-left: 4px solid #00b4d8;
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
            margin: 1rem 0;
        }
        .highlight-box p { margin: 0; }
        .warning-box {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
            margin: 1rem 0;
        }
        .warning-box p { margin: 0; color: #ff9999; }
        .cta-section {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn {
            padding: 0.85rem 1.75rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 180, 216, 0.3); }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e0e0e0;
        }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.15); }
        .credential-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.25rem;
            margin-top: 1rem;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .credential-item:last-child { border-bottom: none; }
        .credential-item .label { color: #888; }
        .credential-item .value { color: #00ff88; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="back-link">← Back to All Labs</a>
        
        <div class="lab-card">
            <div class="lab-header">
                <span class="lab-badge">Lab 26</span>
                <h1>🔑 IDOR in API Applications</h1>
                <p>Exploit API credential leak through object reference manipulation</p>
            </div>
            
            <div class="lab-content">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Difficulty</div>
                        <div class="value" style="color: #ff6b6b;">Critical</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Category</div>
                        <div class="value">Access Control</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Time Required</div>
                        <div class="value">20-30 min</div>
                    </div>
                    <div class="info-item">
                        <div class="label">OWASP API</div>
                        <div class="value">BOLA (API1)</div>
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <span class="tag tag-critical">IDOR</span>
                    <span class="tag tag-critical">Credential Leak</span>
                    <span class="tag">OAuth Secrets</span>
                    <span class="tag">Account Takeover</span>
                    <span class="tag">API Security</span>
                </div>

                <section class="section">
                    <h2>🎯 Objective</h2>
                    <p>
                        This lab simulates a managed WordPress hosting platform's API application management 
                        system. Your goal is to exploit an IDOR vulnerability to leak another user's API 
                        credentials (Client ID and Client Secret) which could lead to full account takeover.
                    </p>
                    <div class="highlight-box">
                        <p>
                            <strong>Flag:</strong> Obtain the Client Secret for victim's "BigCompany Production API" 
                            application and submit it to complete the lab.
                        </p>
                    </div>
                </section>

                <section class="section">
                    <h2>📖 Background</h2>
                    <p>
                        Based on a real HackerOne report where a researcher discovered an IDOR vulnerability 
                        in a managed WordPress hosting platform. The vulnerability existed in the API application 
                        update endpoint where changing the <code>application[id]</code> parameter while 
                        omitting the <code>application[name]</code> field caused a validation error that 
                        exposed the target application's credentials.
                    </p>
                    <p>
                        The sequential nature of application IDs made enumeration trivial, allowing an 
                        attacker to extract credentials for any API application on the platform.
                    </p>
                </section>

                <section class="section">
                    <h2>🔬 What You'll Learn</h2>
                    <ul>
                        <li>How IDOR vulnerabilities manifest in API management interfaces</li>
                        <li>Exploiting validation errors to extract sensitive information</li>
                        <li>Understanding the impact of sequential ID enumeration</li>
                        <li>How credential leaks enable account takeover attacks</li>
                        <li>Proper authorization patterns for API endpoints</li>
                    </ul>
                </section>

                <section class="section">
                    <h2>🔐 Test Credentials</h2>
                    <p>Use these credentials to access the lab:</p>
                    <div class="credential-box">
                        <div class="credential-item">
                            <span class="label">Attacker Account</span>
                            <span class="value">attacker / attacker123</span>
                        </div>
                        <div class="credential-item">
                            <span class="label">Victim Account</span>
                            <span class="value">victim / victim123</span>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <h2>💡 Hints</h2>
                    <ul>
                        <li>Explore the application update functionality in your dashboard</li>
                        <li>Pay attention to form field names and what data is sent</li>
                        <li>Try removing certain form fields to trigger validation errors</li>
                        <li>Application IDs are sequential - other users have IDs you might want to try</li>
                        <li>Error pages sometimes reveal more than they should...</li>
                    </ul>
                    
                    <div class="warning-box">
                        <p>
                            <strong>⚠️ Note:</strong> This lab intentionally contains vulnerable code 
                            for educational purposes. Never deploy this in a production environment.
                        </p>
                    </div>
                </section>

                <div class="cta-section">
                    <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
                    <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
                    <a href="setup_db.php" class="btn btn-secondary">🔧 Setup Database</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
