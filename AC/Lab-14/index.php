<?php
session_start();
require_once 'config.php';

$isLoggedIn = isset($_SESSION['manager_id']);
$manager = null;

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT * FROM managers WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['manager_id']);
    $stmt->execute();
    $manager = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDOR Banner Deletion Lab - Revive Adserver Simulation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 68, 68, 0.3);
            padding: 1rem 2rem;
        }
        .header-content {
            max-width: 1200px;
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
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ff4444; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 6px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .hero {
            text-align: center;
            margin-bottom: 4rem;
        }
        .hero h1 {
            font-size: 3rem;
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .hero p {
            color: #888;
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }
        .vulnerability-box {
            background: linear-gradient(135deg, rgba(255, 68, 68, 0.2), rgba(200, 0, 0, 0.1));
            border: 2px solid rgba(255, 68, 68, 0.5);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 3rem;
        }
        .vulnerability-box h2 {
            color: #ff6666;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }
        .vulnerability-box p {
            color: #ccc;
            line-height: 1.8;
        }
        .vulnerability-diagram {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 2rem;
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
        }
        .diagram-box {
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px;
        }
        .diagram-box.attacker {
            background: rgba(255, 100, 0, 0.2);
            border: 1px solid rgba(255, 100, 0, 0.5);
        }
        .diagram-box.victim {
            background: rgba(0, 150, 255, 0.2);
            border: 1px solid rgba(0, 150, 255, 0.5);
        }
        .diagram-arrow {
            font-size: 2rem;
            color: #ff4444;
        }
        .diagram-label {
            font-size: 0.8rem;
            color: #888;
            margin-top: 0.5rem;
        }
        .code-example {
            background: #0d0d0d;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }
        .code-comment { color: #6a9955; }
        .code-keyword { color: #569cd6; }
        .code-string { color: #ce9178; }
        .code-danger { color: #ff6666; }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 15px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            border-color: #ff4444;
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.2);
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .card h3 {
            color: #ff6666;
            margin-bottom: 0.5rem;
        }
        .card p {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .credentials-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 3rem;
        }
        .credentials-section h2 {
            color: #ff6666;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .credential-card {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
        }
        .credential-card h4 {
            color: #ff4444;
            margin-bottom: 0.5rem;
        }
        .credential-card .role {
            font-size: 0.75rem;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .credential-card code {
            display: block;
            padding: 0.5rem;
            background: rgba(255, 68, 68, 0.1);
            border-radius: 5px;
            color: #88ff88;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #666;
            color: #ccc;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
            border-radius: 15px;
            font-size: 0.75rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">📢 Revive Adserver</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($manager['username']); ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="hero">
            <span class="tag">Lab 14 - Access Control</span>
            <h1>🔓 IDOR Banner Deletion</h1>
            <p>
                Discover and exploit an Insecure Direct Object Reference (IDOR) vulnerability in 
                the banner deletion endpoint. Delete banners belonging to other managers without authorization!
            </p>
        </div>

        <div class="vulnerability-box">
            <h2>🎯 The Vulnerability</h2>
            <p>
                The <code>/banner-delete.php</code> endpoint validates access to the parent campaign 
                but <strong>fails to verify ownership of the specific banner being deleted</strong>. 
                This allows Manager A to delete banners owned by Manager B by manipulating the 
                <code>bannerid</code> parameter while using their own valid <code>clientid</code> and <code>campaignid</code>.
            </p>
            
            <div class="vulnerability-diagram">
                <div class="diagram-box attacker">
                    <div style="font-size: 2rem;">👤</div>
                    <strong>Manager A (Attacker)</strong>
                    <div class="diagram-label">
                        Uses own clientid=1, campaignid=1<br>
                        But targets bannerid=6 (Victim's!)
                    </div>
                </div>
                <div class="diagram-arrow">
                    →<br>
                    <span style="font-size: 0.7rem; color: #888;">IDOR Attack</span><br>
                    →
                </div>
                <div class="diagram-box victim">
                    <div style="font-size: 2rem;">🎯</div>
                    <strong>Manager B's Banner</strong>
                    <div class="diagram-label">
                        Banner ID 6-11 (VICTIM)<br>
                        Gets deleted without auth check!
                    </div>
                </div>
            </div>

            <div class="code-example">
                <span class="code-comment">// VULNERABLE CODE - banner-delete.php</span><br><br>
                <span class="code-keyword">if</span> (!OA_Permission::hasAccessToObject(<span class="code-string">'clients'</span>, $clientId)) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-keyword">die</span>(<span class="code-string">'Unauthorized'</span>);<br>
                }<br>
                <span class="code-keyword">if</span> (!OA_Permission::hasAccessToObject(<span class="code-string">'campaigns'</span>, $campaignId)) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-keyword">die</span>(<span class="code-string">'Unauthorized'</span>);<br>
                }<br>
                <span class="code-danger">// MISSING: hasAccessToObject('banners', $bannerId) check!</span><br><br>
                <span class="code-comment">// Deletes ANY banner without ownership validation</span><br>
                $dalBanners->delete($bannerId);
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-icon">🔍</div>
                <h3>Reconnaissance</h3>
                <p>Log in as Manager A, navigate campaigns, observe how banner deletion requests are formed with CSRF tokens.</p>
            </div>
            <div class="card">
                <div class="card-icon">🎯</div>
                <h3>Target Identification</h3>
                <p>Identify victim's banner IDs (sequential integers). Manager B's banners are IDs 6-11 in this lab.</p>
            </div>
            <div class="card">
                <div class="card-icon">⚡</div>
                <h3>Exploit</h3>
                <p>Craft deletion URL with your valid clientid/campaignid but victim's bannerid. Execute the attack!</p>
            </div>
            <div class="card">
                <div class="card-icon">💥</div>
                <h3>Impact</h3>
                <p>Horizontal privilege escalation - sabotage competitors' campaigns, cause revenue loss, bypass audit controls.</p>
            </div>
        </div>

        <div class="credentials-section">
            <h2>🔑 Test Credentials</h2>
            <div class="credentials-grid">
                <div class="credential-card">
                    <h4>Manager A (Attacker)</h4>
                    <div class="role">Agency X - Has Clients 1-2, Campaigns 1-3, Banners 1-5</div>
                    <code>manager_a : attacker123</code>
                </div>
                <div class="credential-card">
                    <h4>Manager B (Victim)</h4>
                    <div class="role">Agency Y - Has Clients 3-4, Campaigns 4-6, Banners 6-11</div>
                    <code>manager_b : victim456</code>
                </div>
                <div class="credential-card">
                    <h4>Manager C</h4>
                    <div class="role">Agency X - Has Client 5, Campaign 7, Banners 12-13</div>
                    <code>manager_c : charlie789</code>
                </div>
                <div class="credential-card">
                    <h4>Administrator</h4>
                    <div class="role">Revive Corp - Full system access</div>
                    <code>admin : admin</code>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="login.php" class="btn btn-primary">🚀 Start Lab</a>
            <a href="lab-description.php" class="btn btn-secondary">📋 View Objective</a>
            <a href="docs.php" class="btn btn-secondary">📚 Documentation</a>
            <a href="setup_db.php" class="btn btn-secondary">🔄 Reset Lab</a>
        </div>
    </div>
</body>
</html>
