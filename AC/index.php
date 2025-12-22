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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #330000 100%);
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        .page-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .page-header h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 50%, #990000 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .page-header p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
            font-weight: 300;
        }
        .labs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .lab-card {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .lab-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,0,0,0.1) 0%, rgba(0,0,0,0.05) 100%);
            border-radius: 20px;
            z-index: -1;
        }
        .lab-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(255, 0, 0, 0.3);
            border-color: rgba(255, 0, 0, 0.5);
        }
        .lab-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .lab-badge {
            background: rgba(0, 0, 0, 0.8);
            color: #ff0000;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 0, 0, 0.5);
        }
        .lab-badge.beginner {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: white;
            border: none;
        }
        .lab-badge.intermediate {
            background: linear-gradient(135deg, #990000, #cc0000);
            color: white;
            border: none;
        }
        .lab-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            color: white;
            line-height: 1.4;
        }
        .lab-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-weight: 300;
        }
        .lab-objective {
            background: rgba(0, 0, 0, 0.5);
            border-left: 4px solid #ff0000;
            padding: 1.2rem;
            margin-bottom: 2rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        .lab-objective h4 {
            color: #ff0000;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .lab-objective p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.95rem;
            font-weight: 400;
        }
        .lab-button {
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }
        .lab-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .lab-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.6);
            text-decoration: none;
            color: white;
        }
        .lab-button:hover::before {
            left: 100%;
        }
        .vulnerability-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 3rem;
        }
        .vulnerability-info h2 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .vulnerability-info p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            font-size: 1.1rem;
            font-weight: 300;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    
    <div class="container">
        <div class="page-header">
            <h1>Access Control Vulnerabilities</h1>
            <p>Learn about access control flaws and how to exploit them. These labs will teach you to identify and exploit various access control vulnerabilities in web applications.</p>
        </div>
        
        <div class="vulnerability-info">
            <h2>About Access Control Vulnerabilities</h2>
            <p>Access control vulnerabilities occur when an application fails to properly restrict access to resources or functionality. These flaws can allow attackers to view sensitive information, modify data, or perform administrative actions without proper authorization. Understanding these vulnerabilities is crucial for both offensive and defensive security practices.</p>
        </div>
        
        <div class="labs-grid">
            <div class="lab-card">
                <div class="lab-header">
                    <span class="lab-badge beginner">BEGINNER</span>
                    <h3 class="lab-title">Lab 1: Unprotected Admin Functionality</h3>
                </div>
                <p class="lab-description">
                    This lab demonstrates the most basic form of access control vulnerability where administrative functionality is completely unprotected and easily discoverable.
                </p>
                <div class="lab-objective">
                    <h4>Objective</h4>
                    <p>Access the admin panel and delete the user carlos.</p>
                </div>
                <a href="lab1/lab-description.php" class="lab-button">Start Lab 1</a>
            </div>
            
            <div class="lab-card">
                <div class="lab-header">
                    <span class="lab-badge intermediate">INTERMEDIATE</span>
                    <h3 class="lab-title">Lab 2: Unprotected Admin with Unpredictable URL</h3>
                </div>
                <p class="lab-description">
                    This lab shows how security through obscurity fails when sensitive information is disclosed through client-side code, even when admin URLs are unpredictable.
                </p>
                <div class="lab-objective">
                    <h4>Objective</h4>
                    <p>Find the admin panel through information disclosure and delete the user carlos.</p>
                </div>
                <a href="lab2/lab-description.php" class="lab-button">Start Lab 2</a>
            </div>

            <div class="lab-card">
                <div class="lab-header">
                    <span class="lab-badge intermediate">APPRENTICE</span>
                    <h3 class="lab-title">Lab 3: User role controlled by request parameter</h3>
                </div>
                <p class="lab-description">
                    This lab demonstrates how client-side role parameters can be manipulated to escalate privileges through cookie modification attacks.
                </p>
                <div class="lab-objective">
                    <h4>Objective</h4>
                    <p>Escalate privileges by modifying the Admin cookie and delete a user account.</p>
                </div>
                <a href="lab3/lab-description.php" class="lab-button">Start Lab 3</a>
            </div>

            <div class="lab-card">
                <div class="lab-header">
                    <span class="lab-badge intermediate">APPRENTICE</span>
                    <h3 class="lab-title">Lab 4: User role can be modified in user profile</h3>
                </div>
                <p class="lab-description">
                    This lab demonstrates privilege escalation through mass assignment vulnerability where the server accepts role parameters in profile update requests.
                </p>
                <div class="lab-objective">
                    <h4>Objective</h4>
                    <p>Escalate to admin by modifying roleid in JSON request and delete user carlos.</p>
                </div>
                <a href="lab4/lab-description.php" class="lab-button">Start Lab 4</a>
            </div>
        </div>
            
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">4</div>
                <div class="stat-label">Active Labs</div>
            </div>

        </div>
    </div>
</body>
</html>