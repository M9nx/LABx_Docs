<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCorp - Corporate Solutions</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #667eea;
        }
        .login-status {
            color: #666;
            font-size: 0.9rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        .hero {
            text-align: center;
            color: white;
            margin-bottom: 4rem;
        }
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .cta-button {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }
        .feature {
            background: rgba(255,255,255,0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        .feature h3 {
            color: #333;
            margin-bottom: 1rem;
        }
        .feature p {
            color: #666;
            line-height: 1.6;
        }
        .debug-info {
            position: absolute;
            top: -9999px;
            left: -9999px;
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-container">
            <a href="index.php" class="logo">TechCorp</a>
            <div class="nav-links">
                <a href="lab-description.php" style="color: #007bff;">← Back to lab description</a>
                <a href="index.php">Home</a>
                <a href="solutions.php">Solutions</a>
                <a href="services.php">Services</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Dashboard</a>
                    <div class="login-status">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 
                        <a href="logout.php">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="cta-button">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="hero">
            <h1>Corporate Solutions</h1>
            <p>Empowering businesses with cutting-edge technology and innovative solutions</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="cta-button">Get Started Today</a>
            <?php endif; ?>
        </div>

        <div class="features">
            <div class="feature">
                <h3>🚀 Digital Transformation</h3>
                <p>Transform your business with our comprehensive digital solutions. From cloud migration to process automation, we help you stay ahead of the competition.</p>
            </div>
            <div class="feature">
                <h3>🔐 Cybersecurity Solutions</h3>
                <p>Protect your valuable assets with our enterprise-grade security solutions. Advanced threat detection and prevention systems keep your data safe.</p>
            </div>
            <div class="feature">
                <h3>📊 Data Analytics</h3>
                <p>Unlock the power of your data with our advanced analytics platform. Make informed decisions with real-time insights and predictive modeling.</p>
            </div>
            <div class="feature">
                <h3>☁️ Cloud Infrastructure</h3>
                <p>Scalable and reliable cloud infrastructure solutions. Reduce costs while increasing performance and reliability for your critical applications.</p>
            </div>
            <div class="feature">
                <h3>🎯 Consulting Services</h3>
                <p>Expert consultation for your technology challenges. Our certified consultants provide strategic guidance for your digital initiatives.</p>
            </div>
            <div class="feature">
                <h3>🛠️ Custom Development</h3>
                <p>Tailored software solutions built to your exact specifications. From web applications to mobile apps, we bring your vision to life.</p>
            </div>
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
            
            // Initialize features
            initializeAnimations();
            setupEventListeners();
            
            // Development helper functions (should be removed in production)
            if (config.debugMode) {
                console.log('Debug mode enabled');
                console.log('Admin panel available at:', config.apiEndpoints.admin);
            }
        });
        
        function initializeAnimations() {
            // Animate feature cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });
            
            document.querySelectorAll('.feature').forEach(feature => {
                feature.style.opacity = '0';
                feature.style.transform = 'translateY(20px)';
                feature.style.transition = 'all 0.6s ease';
                observer.observe(feature);
            });
        }
        
        function setupEventListeners() {
            // Add smooth scrolling for navigation
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        }
        
        // VULNERABILITY: Exposed admin functions for development
        function getAdminPanelUrl() {
            return window.appConfig.apiEndpoints.admin;
        }
        
        // Quick access function for developers (REMOVE IN PRODUCTION!)
        function quickAdminAccess() {
            window.location.href = getAdminPanelUrl();
        }
        
        // Console easter egg that reveals admin panel
        console.log('%cTechCorp Developer Console', 'color: #667eea; font-size: 16px; font-weight: bold;');
        console.log('%cFor admin access, use: quickAdminAccess()', 'color: #764ba2; font-size: 12px;');
        console.log('%cAdmin panel URL: ' + window.appConfig?.apiEndpoints?.admin, 'color: #ff6b6b; font-size: 10px;');
    </script>
</body>
</html>