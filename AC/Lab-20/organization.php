<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$org_uuid = $_GET['uuid'] ?? '';

// Get organization details
$stmt = $pdo->prepare("SELECT * FROM organizations WHERE uuid = ?");
$stmt->execute([$org_uuid]);
$org = $stmt->fetch();

if (!$org) {
    header('Location: dashboard.php');
    exit;
}

// Check if user is a member
$stmt = $pdo->prepare("SELECT role FROM org_members WHERE org_id = ? AND user_id = ?");
$stmt->execute([$org['id'], $_SESSION['user_id']]);
$membership = $stmt->fetch();

if (!$membership) {
    header('Location: dashboard.php');
    exit;
}

$userRole = $membership['role'];

// Get all members
$stmt = $pdo->prepare("
    SELECT u.*, om.role, om.joined_at
    FROM users u
    JOIN org_members om ON u.id = om.user_id
    WHERE om.org_id = ?
    ORDER BY 
        CASE om.role 
            WHEN 'owner' THEN 1 
            WHEN 'admin' THEN 2 
            ELSE 3 
        END,
        om.joined_at
");
$stmt->execute([$org['id']]);
$members = $stmt->fetchAll();

// Get API keys (VULNERABLE - shows all keys regardless of role!)
$stmt = $pdo->prepare("
    SELECT ak.*, u.username as created_by_username
    FROM api_keys ak
    LEFT JOIN users u ON ak.created_by = u.id
    WHERE ak.org_id = ?
    ORDER BY ak.created_at DESC
");
$stmt->execute([$org['id']]);
$apiKeys = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($org['name']); ?> - KeyVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }
        .header {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.3);
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
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #14b8a6;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .btn-back {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .btn-logout {
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .org-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        .org-title h1 {
            font-size: 2rem;
            color: #f8fafc;
            margin-bottom: 0.5rem;
        }
        .org-title p { color: #94a3b8; }
        .role-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }
        .role-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        .role-owner {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #000;
        }
        .role-admin {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: #fff;
        }
        .role-member {
            background: rgba(100, 116, 139, 0.3);
            color: #94a3b8;
        }
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
        }
        .tab {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .tab.active {
            background: rgba(20, 184, 166, 0.2);
            color: #5eead4;
        }
        .tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.05);
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            color: #5eead4;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .member-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .member-card {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .member-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .member-info h4 { color: #f8fafc; margin-bottom: 0.25rem; }
        .member-info p { color: #64748b; font-size: 0.85rem; }
        .api-key-item {
            padding: 1.25rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 3px solid #14b8a6;
        }
        .api-key-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        .api-key-name {
            color: #f8fafc;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .api-key-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-action {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-copy {
            background: rgba(20, 184, 166, 0.2);
            color: #5eead4;
        }
        .btn-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        .api-key-value {
            font-family: 'Consolas', monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #5eead4;
            word-break: break-all;
            margin-bottom: 0.75rem;
        }
        .api-key-meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.85rem;
            color: #64748b;
        }
        .btn-create {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .permission-note {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #fcd34d;
        }
        .vuln-highlight {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .vuln-highlight h4 { color: #fca5a5; margin-bottom: 0.5rem; }
        .vuln-highlight code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            color: #5eead4;
        }
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        .modal-content h3 {
            color: #5eead4;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #94a3b8;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.3);
            color: #e0e0e0;
        }
        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn-cancel {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #94a3b8;
            cursor: pointer;
        }
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            background: #22c55e;
            color: white;
            border-radius: 10px;
            display: none;
            z-index: 1001;
        }
        .toast.error { background: #ef4444; }
        .toast.active { display: block; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">
                <div class="logo-icon">🔑</div>
                KeyVault
            </a>
            <div class="user-menu">
                <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="org-header">
            <div class="org-title">
                <h1>🏢 <?php echo htmlspecialchars($org['name']); ?></h1>
                <p><?php echo htmlspecialchars($org['description'] ?? 'Organization'); ?></p>
            </div>
            <div class="role-display">
                <span>Your Role:</span>
                <span class="role-badge role-<?php echo $userRole; ?>">
                    <?php echo ucfirst($userRole); ?>
                </span>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="switchTab('members')">👥 Members</button>
            <button class="tab" onclick="switchTab('apikeys')">🔐 API Keys</button>
        </div>

        <!-- Members Tab -->
        <div id="members-tab" class="tab-content active">
            <div class="card">
                <h2>👥 Organization Members (<?php echo count($members); ?>)</h2>
                <div class="member-grid">
                    <?php foreach ($members as $member): ?>
                        <div class="member-card">
                            <div class="member-avatar">
                                <?php echo strtoupper(substr($member['full_name'], 0, 1)); ?>
                            </div>
                            <div class="member-info">
                                <h4><?php echo htmlspecialchars($member['full_name']); ?></h4>
                                <p>@<?php echo htmlspecialchars($member['username']); ?></p>
                                <span class="role-badge role-<?php echo $member['role']; ?>" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                    <?php echo ucfirst($member['role']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- API Keys Tab -->
        <div id="apikeys-tab" class="tab-content">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2>🔐 API Keys (<?php echo count($apiKeys); ?>)</h2>
                    <button class="btn-create" onclick="openCreateModal()">
                        ➕ Create New Key
                    </button>
                </div>

                <?php if ($userRole === 'member'): ?>
                    <div class="permission-note">
                        ⚠️ <strong>Note:</strong> As a <strong>member</strong>, you should only have <strong>read access</strong> to API keys.
                        Creating, modifying, or deleting keys requires <strong>Admin</strong> or <strong>Owner</strong> role.
                    </div>
                <?php endif; ?>

                <?php if (empty($apiKeys)): ?>
                    <p style="color: #64748b; text-align: center; padding: 2rem;">No API keys found for this organization.</p>
                <?php else: ?>
                    <?php foreach ($apiKeys as $key): ?>
                        <div class="api-key-item" data-key-uuid="<?php echo htmlspecialchars($key['uuid']); ?>">
                            <div class="api-key-header">
                                <div class="api-key-name"><?php echo htmlspecialchars($key['name']); ?></div>
                                <div class="api-key-actions">
                                    <button class="btn-action btn-copy" onclick="copyKey('<?php echo htmlspecialchars($key['api_key']); ?>')">
                                        📋 Copy
                                    </button>
                                    <button class="btn-action btn-delete" onclick="deleteKey('<?php echo htmlspecialchars($key['uuid']); ?>')">
                                        🗑️ Delete
                                    </button>
                                </div>
                            </div>
                            <div class="api-key-value"><?php echo htmlspecialchars($key['api_key']); ?></div>
                            <div class="api-key-meta">
                                <span>🏷️ <?php echo htmlspecialchars($key['scope'] ?? 'full_access'); ?></span>
                                <span>👤 Created by: <?php echo htmlspecialchars($key['created_by_username'] ?? 'Unknown'); ?></span>
                                <span>📅 <?php echo date('M j, Y', strtotime($key['created_at'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="vuln-highlight">
                    <h4>🔓 IDOR Vulnerability</h4>
                    <p style="color: #94a3b8; font-size: 0.9rem;">
                        The API at <code>api/keys.php</code> checks if you're an org member, but doesn't verify your <code>role</code>!
                        A <code>member</code> can VIEW, CREATE, and DELETE API keys - actions that should require <code>admin</code> or <code>owner</code> roles.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Key Modal -->
    <div class="modal" id="createModal">
        <div class="modal-content">
            <h3>➕ Create New API Key</h3>
            <div class="form-group">
                <label>Key Name</label>
                <input type="text" id="keyName" placeholder="e.g., Production API Key">
            </div>
            <div class="form-group">
                <label>Scope</label>
                <select id="keyScope">
                    <option value="read">Read Only</option>
                    <option value="write">Read & Write</option>
                    <option value="full_access">Full Access</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeCreateModal()">Cancel</button>
                <button class="btn-create" onclick="createKey()">Create Key</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        const orgUuid = '<?php echo $org_uuid; ?>';

        function switchTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tab + '-tab').classList.add('active');
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast active' + (isError ? ' error' : '');
            setTimeout(() => toast.classList.remove('active'), 3000);
        }

        function copyKey(key) {
            navigator.clipboard.writeText(key).then(() => {
                showToast('API key copied to clipboard!');
            });
        }

        function openCreateModal() {
            document.getElementById('createModal').classList.add('active');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('active');
        }

        // VULNERABLE: No role check on backend!
        function createKey() {
            const name = document.getElementById('keyName').value;
            const scope = document.getElementById('keyScope').value;

            if (!name) {
                showToast('Please enter a key name', true);
                return;
            }

            fetch('api/keys.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    org_uuid: orgUuid,
                    name: name,
                    scope: scope
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('API key created successfully!');
                    closeCreateModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.error || 'Failed to create key', true);
                }
            })
            .catch(err => showToast('Error creating key', true));
        }

        // VULNERABLE: No role check on backend!
        function deleteKey(keyUuid) {
            if (!confirm('Are you sure you want to delete this API key?')) return;

            fetch('api/keys.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    org_uuid: orgUuid,
                    key_uuid: keyUuid
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('API key deleted!');
                    document.querySelector(`[data-key-uuid="${keyUuid}"]`).remove();
                } else {
                    showToast(data.error || 'Failed to delete key', true);
                }
            })
            .catch(err => showToast('Error deleting key', true));
        }
    </script>
</body>
</html>
