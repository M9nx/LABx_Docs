<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 2: Information Disclosure</title>
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
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
            margin-bottom: 40px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff4444;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #cccccc;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: #ff4444;
            background: rgba(255, 68, 68, 0.1);
        }

        .login-status {
            color: #888;
            font-size: 0.9rem;
        }

        .login-status a {
            color: #ff4444;
            text-decoration: none;
        }

        .hero {
            text-align: center;
            margin-bottom: 50px;
        }

        .lab-title {
            color: #ff4444;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(255, 68, 68, 0.3);
        }

        .lab-subtitle {
            color: #cccccc;
            font-size: 1.3rem;
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

        .lab-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #ff6666;
            font-size: 1.5rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .feature-card {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #ff4444;
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.2);
        }

        .feature-card h3 {
            color: #ffffff;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #999;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .hint-box {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .hint-box h4 {
            color: #00ffff;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .hint-box p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin: 0;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
        }

        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #ff4444;
            padding: 15px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid #ff4444;
        }

        .btn-secondary:hover {
            background: rgba(255, 68, 68, 0.1);
            transform: translateY(-3px);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-link {
            color: #ff6666;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #ff6666;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 102, 102, 0.1);
            transform: translateY(-1px);
        }

        .debug-info {
            position: absolute;
            top: -9999px;
            left: -9999px;
            opacity: 0;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .lab-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="index.php" class="logo">🏢 TechCorp</a>
            <div class="nav-links">
                <a href="lab-description.php">← Lab Info</a>
                <a href="index.php">Home</a>
                <a href="solutions.php">Solutions</a>
                <a href="services.php">Services</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Dashboard</a>
                    <span class="login-status">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
                        <a href="logout.php">Logout</a>
                    </span>
                <?php else: ?>
                    <a href="login.php" class="btn-primary" style="padding: 8px 20px;">Login</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero">
            <h1 class="lab-title">🔍 TechCorp</h1>
            <p class="lab-subtitle">Corporate Solutions Platform</p>
            <div class="difficulty-badge">Apprentice Level</div>
        </div>

        <div class="lab-card">
            <h2 class="section-title">🎯 Your Mission</h2>
            <p style="color: #cccccc; line-height: 1.8; margin-bottom: 15px;">
                Welcome to TechCorp! This corporate platform has an 
                <strong style="color: #ff4444;">information disclosure vulnerability</strong>. 
                The admin panel uses an unpredictable URL, but it's exposed somewhere in the application.
            </p>
            <p style="color: #cccccc; line-height: 1.8;">
                <strong>Goal:</strong> Find the hidden admin panel URL and delete the user "carlos" to complete the lab.
            </p>
        </div>

        <div class="hint-box">
            <h4>💡 Hint</h4>
            <p>
                Check the page source code and JavaScript files. Developers sometimes accidentally leave 
                <code style="color: #00ffff; background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 3px;">debug information</code> 
                and configuration data in client-side code. Open DevTools (F12) and inspect the source carefully.
            </p>
        </div>

        <div class="lab-card">
            <h2 class="section-title">🚀 Our Solutions</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>🚀 Digital Transformation</h3>
                    <p>Transform your business with our comprehensive digital solutions. From cloud migration to process automation.</p>
                </div>
                <div class="feature-card">
                    <h3>🔐 Cybersecurity Solutions</h3>
                    <p>Protect your valuable assets with our enterprise-grade security solutions and threat detection.</p>
                </div>
                <div class="feature-card">
                    <h3>📊 Data Analytics</h3>
                    <p>Unlock the power of your data with our advanced analytics platform and real-time insights.</p>
                </div>
                <div class="feature-card">
                    <h3>☁️ Cloud Infrastructure</h3>
                    <p>Scalable and reliable cloud infrastructure solutions. Reduce costs while increasing performance.</p>
                </div>
                <div class="feature-card">
                    <h3>🎯 Consulting Services</h3>
                    <p>Expert consultation for your technology challenges with strategic guidance.</p>
                </div>
                <div class="feature-card">
                    <h3>🛠️ Custom Development</h3>
                    <p>Tailored software solutions built to your exact specifications.</p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn-primary">🔐 Login to Account</a>
            <?php else: ?>
                <a href="profile.php" class="btn-primary">👤 View Dashboard</a>
            <?php endif; ?>
            <a href="solutions.php" class="btn-secondary">💼 View Solutions</a>
            <a href="docs.php" class="btn-secondary">📚 View Documentation</a>
        </div>

        <div class="navigation">
            <a href="../lab1/" class="nav-link">← Lab 1</a>
            <a href="../" class="nav-link">🏠 AC Labs Home</a>
            <a href="../lab3/" class="nav-link">Lab 3 →</a>
        </div>
    </div>

    <!-- Hidden debug information (VULNERABILITY: Admin panel URL disclosed in source) -->
    <div class="debug-info" id="debug-panel">
        <!-- Development Notes: Admin panel located at /admin-panel-x7k9p2m5q8w1.php -->
        <!-- TODO: Remove this before production deployment -->
        <!-- Last updated: 2024-11-15 by dev team -->
    </div>

    <script>
        // Application initialization
        document.addEventListener('DOMContentLoaded', function() {
            console.log('TechCorp Application Loaded');
            
            // VULNERABILITY: Admin panel URL exposed in JavaScript
            const config = {
                apiEndpoints: {
                    users: '/api/users',
                    products: '/api/products',
                    admin: '/admin-panel-x7k9p2m5q8w1.php'  // SECRET: Admin panel location
                },
                environment: 'production',
                debugMode: false
            };
            
            // Store config globally for debugging (SECURITY ISSUE!)
            window.appConfig = config;
            
            // Development helper functions (should be removed in production)
            if (config.debugMode) {
                console.log('Debug mode enabled');
                console.log('Admin panel available at:', config.apiEndpoints.admin);
            }
        });
        
        // VULNERABILITY: Exposed admin functions for development
        function getAdminPanelUrl() {
            return window.appConfig.apiEndpoints.admin;
        }
        
        // Quick access function for developers (REMOVE IN PRODUCTION!)
        function quickAdminAccess() {
            window.location.href = getAdminPanelUrl();
        }
        
        // Console easter egg that reveals admin panel
        console.log('%cTechCorp Developer Console', 'color: #ff4444; font-size: 16px; font-weight: bold;');
        console.log('%cFor admin access, use: quickAdminAccess()', 'color: #ff6666; font-size: 12px;');
        console.log('%cAdmin panel URL: ' + window.appConfig?.apiEndpoints?.admin, 'color: #00ffff; font-size: 10px;');
    </script>
</body>
</html>