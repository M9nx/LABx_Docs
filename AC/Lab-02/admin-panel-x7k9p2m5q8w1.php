<?php
// INTENTIONALLY VULNERABLE: No authentication or authorization checks!
// Admin panel with unpredictable URL but still unprotected

require_once 'config.php';

$successMessage = '';
$errorMessage = '';

// Handle user deletion
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $pdo = getDBConnection();
    
    // Get user info before deletion for confirmation
    $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        // If carlos was deleted, redirect to success page
        if ($user['username'] === 'carlos') {
            header('Location: success.php');
            exit;
        }
        
        $successMessage = "User '" . htmlspecialchars($user['username']) . "' (" . $user['role'] . ") has been deleted successfully.";
    } else {
        $errorMessage = "User not found.";
    }
}

// Handle role updates
if (isset($_POST['update_role']) && isset($_POST['user_id']) && isset($_POST['new_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        $updateStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $updateStmt->execute([$newRole, $userId]);
        $successMessage = "User '" . htmlspecialchars($user['username']) . "' role updated to '" . htmlspecialchars($newRole) . "'.";
    } else {
        $errorMessage = "User not found.";
    }
}

// Handle salary updates
if (isset($_POST['update_salary']) && isset($_POST['user_id']) && isset($_POST['new_salary'])) {
    $userId = $_POST['user_id'];
    $newSalary = $_POST['new_salary'];
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        $updateStmt = $pdo->prepare("UPDATE users SET salary = ? WHERE id = ?");
        $updateStmt->execute([$newSalary, $userId]);
        $successMessage = "User '" . htmlspecialchars($user['username']) . "' salary updated to $" . number_format($newSalary, 2) . ".";
    } else {
        $errorMessage = "User not found.";
    }
}

// Get all users with sensitive information
$pdo = getDBConnection();
$stmt = $pdo->query("
    SELECT id, username, email, full_name, role, department, position, salary, 
           phone, emergency_contact, security_clearance, notes, created_at, last_login 
    FROM users 
    ORDER BY id
");
$users = $stmt->fetchAll();

// Get statistics
$statsStmt = $pdo->query("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
        SUM(CASE WHEN role = 'manager' THEN 1 ELSE 0 END) as manager_count,
        SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as user_count,
        AVG(salary) as avg_salary,
        MAX(salary) as max_salary,
        MIN(salary) as min_salary
    FROM users
");
$stats = $statsStmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCorp Admin Panel - Secure Management Console</title>
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
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .logo h1 {
            margin: 0;
            color: #ff4444;
            font-size: 1.8rem;
        }
        .admin-badge {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .warning {
            background: rgba(255, 193, 7, 0.15);
            border: 1px solid rgba(255, 193, 7, 0.5);
            color: #ffc107;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 68, 68, 0.2);
            backdrop-filter: blur(10px);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #ff4444;
            font-size: 2rem;
        }
        .stat-card p {
            margin: 0;
            color: #aaa;
            font-weight: 500;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
        }
        .users-table {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 68, 68, 0.2);
            backdrop-filter: blur(10px);
        }
        .table-header {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            color: white;
            padding: 1.5rem;
        }
        .table-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .table-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: rgba(255, 68, 68, 0.1);
            font-weight: 600;
            color: #ff4444;
        }
        tr:hover {
            background: rgba(255, 68, 68, 0.05);
        }
        .role-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-admin {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
            color: white;
        }
        .role-manager {
            background: linear-gradient(45deg, #fd7e14, #ffc107);
            color: white;
        }
        .role-user {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        .clearance-badge {
            padding: 0.3rem 0.6rem;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .clearance-top-secret {
            background: #dc3545;
            color: white;
        }
        .clearance-secret {
            background: #fd7e14;
            color: white;
        }
        .clearance-confidential {
            background: #ffc107;
            color: #333;
        }
        .clearance-basic {
            background: #28a745;
            color: white;
        }
        .clearance-none {
            background: #6c757d;
            color: white;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0.1rem;
            transition: all 0.3s ease;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            color: white;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.4);
        }
        .btn-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            color: #333;
        }
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        .btn-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
        }
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-secondary:hover {
            background: #ff4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.4);
        }
        .actions {
            white-space: nowrap;
        }
        .salary {
            font-weight: 600;
            color: #28a745;
        }
        .sensitive-data {
            font-size: 0.9rem;
            color: #888;
        }
        .quick-actions {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 68, 68, 0.2);
            backdrop-filter: blur(10px);
        }
        .quick-actions h3 {
            margin: 0 0 1rem 0;
            color: #ff4444;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            margin: 15% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            border: 1px solid rgba(255, 68, 68, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }
        .modal-content h3 {
            color: #ff4444;
            margin-bottom: 1rem;
        }
        .close {
            float: right;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            color: #888;
        }
        .close:hover {
            color: #ff4444;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #ccc;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            box-sizing: border-box;
            color: #e0e0e0;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #ff4444;
        }
        .form-group select option {
            background: #1a1a1a;
            color: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🔒 TechCorp Admin Panel</h1>
                <div class="admin-badge">UNRESTRICTED ACCESS</div>
            </div>
            <a href="index.php" class="btn btn-secondary">← Back to Main Site</a>
        </div>
    </div>
    
    <div class="container">
        <div class="warning">
            <strong>⚠️ CRITICAL SECURITY VULNERABILITY:</strong> This admin panel has no authentication or authorization controls! 
            Anyone who discovers this URL can access all administrative functions and sensitive employee data.
        </div>
        
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Total Employees</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['admin_count']; ?></h3>
                <p>Administrators</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['manager_count']; ?></h3>
                <p>Managers</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['user_count']; ?></h3>
                <p>Regular Users</p>
            </div>
            <div class="stat-card">
                <h3>$<?php echo number_format($stats['avg_salary'], 0); ?></h3>
                <p>Average Salary</p>
            </div>
            <div class="stat-card">
                <h3>$<?php echo number_format($stats['max_salary'], 0); ?></h3>
                <p>Highest Salary</p>
            </div>
        </div>
        
        <div class="users-table">
            <div class="table-header">
                <h2>👥 Employee Management System</h2>
                <p>Complete access to all employee records, salaries, and sensitive information</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Role & Clearance</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Contact Info</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($user['id']); ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong><br>
                            <small class="sensitive-data"><?php echo htmlspecialchars($user['username']); ?> | <?php echo htmlspecialchars($user['email']); ?></small><br>
                            <small class="sensitive-data"><strong>Position:</strong> <?php echo htmlspecialchars($user['position']); ?></small>
                        </td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo strtoupper($user['role']); ?>
                            </span><br><br>
                            <span class="clearance-badge clearance-<?php echo str_replace('-', '-', $user['security_clearance']); ?>">
                                <?php echo strtoupper(str_replace('-', ' ', $user['security_clearance'])); ?>
                            </span>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['department']); ?></strong>
                        </td>
                        <td>
                            <span class="salary">$<?php echo number_format($user['salary'], 2); ?></span>
                        </td>
                        <td>
                            <div class="sensitive-data">
                                <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?><br>
                                <strong>Emergency:</strong> <?php echo htmlspecialchars($user['emergency_contact']); ?><br>
                                <strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?>
                            </div>
                        </td>
                        <td class="actions">
                            <form method="POST" style="display: inline;" 
                                  onsubmit="return confirm('⚠️ DELETE EMPLOYEE?\n\nThis will permanently remove <?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['username']); ?>) from the system.\n\nThis action cannot be undone!')">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger" 
                                        title="Permanently delete employee record">🗑️ Delete</button>
                            </form>
                            
                            <button onclick="openRoleModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')" 
                                    class="btn btn-warning" title="Change employee role">👤 Role</button>
                            
                            <button onclick="openSalaryModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', <?php echo $user['salary']; ?>)" 
                                    class="btn btn-info" title="Adjust employee salary">💰 Salary</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Role Update Modal -->
    <div id="roleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('roleModal')">&times;</span>
            <h3>Update Employee Role</h3>
            <form method="POST" id="roleForm">
                <input type="hidden" name="user_id" id="roleUserId">
                <div class="form-group">
                    <label>Employee:</label>
                    <input type="text" id="roleUsername" readonly>
                </div>
                <div class="form-group">
                    <label for="new_role">New Role:</label>
                    <select name="new_role" id="new_role" required>
                        <option value="user">User</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <button type="submit" name="update_role" class="btn btn-warning">Update Role</button>
            </form>
        </div>
    </div>
    
    <!-- Salary Update Modal -->
    <div id="salaryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('salaryModal')">&times;</span>
            <h3>Update Employee Salary</h3>
            <form method="POST" id="salaryForm">
                <input type="hidden" name="user_id" id="salaryUserId">
                <div class="form-group">
                    <label>Employee:</label>
                    <input type="text" id="salaryUsername" readonly>
                </div>
                <div class="form-group">
                    <label for="new_salary">New Annual Salary ($):</label>
                    <input type="number" name="new_salary" id="new_salary" step="0.01" min="0" required>
                </div>
                <button type="submit" name="update_salary" class="btn btn-info">Update Salary</button>
            </form>
        </div>
    </div>
    
    <script>
        // Modal functions
        function openRoleModal(userId, username, currentRole) {
            document.getElementById('roleUserId').value = userId;
            document.getElementById('roleUsername').value = username;
            document.getElementById('new_role').value = currentRole;
            document.getElementById('roleModal').style.display = 'block';
        }
        
        function openSalaryModal(userId, username, currentSalary) {
            document.getElementById('salaryUserId').value = userId;
            document.getElementById('salaryUsername').value = username;
            document.getElementById('new_salary').value = currentSalary;
            document.getElementById('salaryModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const roleModal = document.getElementById('roleModal');
            const salaryModal = document.getElementById('salaryModal');
            if (event.target === roleModal) {
                roleModal.style.display = 'none';
            }
            if (event.target === salaryModal) {
                salaryModal.style.display = 'none';
            }
        }
        
        // Show vulnerability warning
        window.addEventListener('load', function() {
            if (!sessionStorage.getItem('adminPanelWarning')) {
                setTimeout(function() {
                    alert('🔓 SECURITY BREACH DETECTED!\n\n' +
                          'This admin panel is completely unprotected!\n\n' +
                          '• No authentication required\n' +
                          '• No authorization checks\n' +
                          '• Full access to sensitive employee data\n' +
                          '• Ability to modify salaries and roles\n' +
                          '• Can delete any employee record\n\n' +
                          'This represents a critical security vulnerability!');
                    sessionStorage.setItem('adminPanelWarning', 'true');
                }, 500);
            }
        });
        
        console.log('%cTechCorp Admin Panel - UNSECURED', 'color: #dc3545; font-size: 18px; font-weight: bold;');
        console.log('%cThis panel has no access controls and exposes sensitive data!', 'color: #ff6b6b; font-size: 12px;');
    </script>
</body>
</html>