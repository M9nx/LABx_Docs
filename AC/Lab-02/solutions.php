<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solutions - TechCorp</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%); min-height: 100vh; color: #e0e0e0; }
        .header { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 68, 68, 0.3); padding: 1rem 2rem; }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #ff4444; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e0e0e0; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .nav-links a:hover { color: #ff4444; }
        .container { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; }
        .content { background: rgba(255, 255, 255, 0.05); padding: 3rem; border-radius: 20px; border: 1px solid rgba(255, 68, 68, 0.2); backdrop-filter: blur(20px); }
        h1 { color: #ff4444; text-align: center; margin-bottom: 2rem; }
        .solutions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .solution { background: rgba(255, 68, 68, 0.05); padding: 2rem; border-radius: 15px; border: 1px solid rgba(255, 68, 68, 0.2); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .solution:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(255, 68, 68, 0.2); }
        .solution h3 { color: #ff4444; margin-bottom: 1rem; }
        .solution p { color: #aaa; }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-container">
            <a href="index.php" class="logo">🏢 TechCorp</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="solutions.php">Solutions</a>
                <a href="services.php">Services</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="content">
            <h1>Our Solutions</h1>
            <div class="solutions-grid">
                <div class="solution">
                    <h3>🔒 Enterprise Security</h3>
                    <p>Comprehensive security solutions to protect your business assets and data.</p>
                </div>
                <div class="solution">
                    <h3>☁️ Cloud Migration</h3>
                    <p>Seamless migration to cloud infrastructure with minimal downtime.</p>
                </div>
                <div class="solution">
                    <h3>📊 Data Analytics</h3>
                    <p>Advanced analytics to drive business intelligence and decision making.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>