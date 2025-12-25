<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$message = '';
$messageType = '';

// Handle JSON API request for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    header('Content-Type: application/json');
    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if ($data) {
        // VULNERABLE: This code accepts ANY field from the JSON input
        // including roleid, allowing privilege escalation
        $updateFields = [];
        $params = [];
        
        // Allow updating various fields (including roleid - THIS IS THE VULNERABILITY)
        $allowedFields = ['email', 'full_name', 'department', 'phone', 'address', 'notes', 'roleid'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (!empty($updateFields)) {
            $params[] = $_SESSION['user_id'];
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            // Fetch updated user data
            $stmt = $pdo->prepare("SELECT id, username, email, full_name, roleid, department, phone, address, notes, api_key FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $updatedUser = $stmt->fetch();
            
            // Update session with new roleid if it was changed
            if (isset($data['roleid'])) {
                $_SESSION['roleid'] = $updatedUser['roleid'];
            }
            if (isset($data['email'])) {
                $_SESSION['email'] = $updatedUser['email'];
            }
            
            // VULNERABLE: Response includes roleid, giving attacker information
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'username' => $updatedUser['username'],
                    'email' => $updatedUser['email'],
                    'full_name' => $updatedUser['full_name'],
                    'roleid' => $updatedUser['roleid'],
                    'department' => $updatedUser['department'],
                    'phone' => $updatedUser['phone'],
                    'address' => $updatedUser['address']
                ]
            ]);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Update session with current data
$_SESSION['roleid'] = $user['roleid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - RoleLab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        .nav-links a:hover {
            color: #ff4444;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: rgba(255, 68, 68, 0.2);
            border-color: #ff4444;
            color: #ff4444;
        }
        .user-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .user-badge .role {
            color: #ff4444;
            font-weight: 600;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-title {
            font-size: 2rem;
            color: #ff4444;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 68, 68, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .profile-section {
            margin-bottom: 2rem;
        }
        .profile-section:last-child {
            margin-bottom: 0;
        }
        .section-title {
            color: #ff6666;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 68, 68, 0.2);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .info-item {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
        }
        .info-label {
            color: #888;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        .info-value {
            color: #fff;
            font-size: 1.1rem;
        }
        .role-display {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .role-admin {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .role-user {
            background: rgba(100, 100, 100, 0.5);
            color: #ccc;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ccc;
            font-weight: 500;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #ff4444;
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.2);
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border: none;
            font-size: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 68, 68, 0.4);
        }
        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .message-success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }
        .message-error {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid #ff4444;
            color: #ff6666;
        }
        .api-hint {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .api-hint h4 {
            color: #00ffff;
            margin-bottom: 0.8rem;
        }
        .api-hint code {
            background: rgba(0, 0, 0, 0.4);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            color: #00ffff;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
        }
        .api-hint pre {
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.8rem;
            overflow-x: auto;
            color: #00ffff;
            font-family: 'Consolas', monospace;
            font-size: 0.85rem;
        }
        #updateResult {
            display: none;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">🔐 RoleLab</a>
            <nav class="nav-links">
                <a href="../index.php" class="btn-back">← All Labs</a>
                <a href="index.php">Home</a>
                <a href="lab-description.php">Lab Info</a>
                <a href="docs.php">Documentation</a>
                <a href="profile.php">My Profile</a>
                <a href="admin.php">Admin Panel</a>
                <span class="user-badge">
                    👤 <?php echo htmlspecialchars($user['username']); ?>
                    <span class="role">(Role ID: <?php echo $user['roleid']; ?>)</span>
                </span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">👤 My Profile</h1>

        <div class="profile-card">
            <div class="profile-section">
                <h3 class="section-title">Account Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Role</div>
                        <div class="info-value">
                            <span class="role-display <?php echo $user['roleid'] == 2 ? 'role-admin' : 'role-user'; ?>">
                                <?php echo $user['roleid'] == 2 ? '🛡️ Administrator' : '👤 Regular User'; ?>
                            </span>
                            <span style="color: #888; font-size: 0.85rem; margin-left: 0.5rem;">(ID: <?php echo $user['roleid']; ?>)</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Department</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['department'] ?: 'Not set'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">API Key</div>
                        <div class="info-value" style="font-family: monospace; font-size: 0.85rem; color: #888;">
                            <?php echo htmlspecialchars(substr($user['api_key'], 0, 20) . '...'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-section">
                <h3 class="section-title">Update Profile</h3>
                <form id="updateForm">
                    <div class="info-grid">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($user['department'] ?: ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
                
                <div id="updateResult" class="message"></div>

                <div class="api-hint">
                    <h4>💡 Developer Note</h4>
                    <p>Profile updates are sent as JSON to the server. Example request:</p>
                    <pre>POST /lab4/profile.php
Content-Type: application/json

{
    "email": "newemail@example.com"
}</pre>
                    <p style="margin-top: 0.8rem; color: #88dddd;">
                        <strong>Response includes user data with roleid.</strong> Inspect the network tab to see the full response.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const department = document.getElementById('department').value;
            
            // Send JSON request - user can modify this in dev tools/Burp
            const data = {
                email: email,
                department: department
            };
            
            fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                const resultDiv = document.getElementById('updateResult');
                resultDiv.style.display = 'block';
                
                if (result.success) {
                    resultDiv.className = 'message message-success';
                    resultDiv.innerHTML = '✅ ' + result.message + '<br><br><strong>Response data:</strong><pre style="margin-top: 10px; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px; overflow-x: auto;">' + JSON.stringify(result.user, null, 2) + '</pre>';
                    
                    // If roleid was changed to 2, show success message
                    if (result.user.roleid === 2) {
                        resultDiv.innerHTML += '<br><br>🎉 <strong>Your role has been updated to Administrator!</strong> <a href="admin.php" style="color: #00ff00;">Try the Admin Panel now →</a>';
                    }
                    
                    // Reload after a delay to update the page
                    setTimeout(() => location.reload(), 3000);
                } else {
                    resultDiv.className = 'message message-error';
                    resultDiv.innerHTML = '❌ ' + result.message;
                }
            })
            .catch(error => {
                const resultDiv = document.getElementById('updateResult');
                resultDiv.style.display = 'block';
                resultDiv.className = 'message message-error';
                resultDiv.innerHTML = '❌ Error: ' + error.message;
            });
        });
    </script>
</body>
</html>
