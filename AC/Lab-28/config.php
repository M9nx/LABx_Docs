<?php
/**
 * Lab 28: Configuration & Database Connection
 * MTN Developers Portal - IDOR Team Member Removal
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'ac_lab28');

// Lab configuration
define('LAB_NUMBER', 28);
define('LAB_TITLE', 'IDOR - Remove Users from Any Team');
define('LAB_FLAG', 'FLAG{mtn_idor_team_removal_mass_destruction_2024}');

// Database connection
$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Silently fail - will show setup message on pages
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['lab28_user_id']) && isset($_SESSION['lab28_logged_in']);
}

/**
 * Get current logged-in user's data
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn() || !$pdo) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['lab28_user_id']]);
    return $stmt->fetch();
}

/**
 * Get user by user_id
 */
function getUserByUserId($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Get team by team_id
 */
function getTeamByTeamId($pdo, $teamId) {
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE team_id = ?");
    $stmt->execute([$teamId]);
    return $stmt->fetch();
}

/**
 * Get teams owned by user
 */
function getUserOwnedTeams($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE owner_user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get teams user is a member of
 */
function getUserTeams($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT t.*, tm.role as member_role 
        FROM teams t 
        INNER JOIN team_members tm ON t.team_id = tm.team_id 
        WHERE tm.user_id = ? 
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get team members
 */
function getTeamMembers($pdo, $teamId) {
    $stmt = $pdo->prepare("
        SELECT u.*, tm.role as team_role, tm.joined_at
        FROM users u
        INNER JOIN team_members tm ON u.user_id = tm.user_id
        WHERE tm.team_id = ?
        ORDER BY 
            CASE tm.role 
                WHEN 'owner' THEN 1 
                WHEN 'admin' THEN 2 
                ELSE 3 
            END,
            tm.joined_at ASC
    ");
    $stmt->execute([$teamId]);
    return $stmt->fetchAll();
}

/**
 * Check if user is member of team
 */
function isTeamMember($pdo, $teamId, $userId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM team_members WHERE team_id = ? AND user_id = ?");
    $stmt->execute([$teamId, $userId]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Get user's role in a team
 */
function getUserTeamRole($pdo, $teamId, $userId) {
    $stmt = $pdo->prepare("SELECT role FROM team_members WHERE team_id = ? AND user_id = ?");
    $stmt->execute([$teamId, $userId]);
    $result = $stmt->fetch();
    return $result ? $result['role'] : null;
}

/**
 * VULNERABLE: Remove member from team - NO AUTHORIZATION CHECK!
 * This function is intentionally vulnerable for the lab
 */
function removeMemberFromTeam($pdo, $teamId, $userId) {
    // VULNERABILITY: No check if the requester has permission to remove from this team!
    // Should verify: Is the requester an owner/admin of the target team?
    
    // Get user and team info for response (Information Disclosure)
    $user = getUserByUserId($pdo, $userId);
    $team = getTeamByTeamId($pdo, $teamId);
    
    if (!$user || !$team) {
        return ['success' => false, 'error' => 'User or team not found'];
    }
    
    // Check if membership exists
    if (!isTeamMember($pdo, $teamId, $userId)) {
        return ['success' => false, 'error' => 'User is not a member of this team'];
    }
    
    // Prevent removing owner (basic check, but still vulnerable to IDOR)
    $role = getUserTeamRole($pdo, $teamId, $userId);
    if ($role === 'owner') {
        return [
            'success' => false, 
            'error' => 'Cannot remove team owner',
            // INFORMATION DISCLOSURE: Leaking user/team info even on failure
            'user_name' => $user['full_name'],
            'team_name' => $team['name']
        ];
    }
    
    // VULNERABLE: Directly removes without authorization check
    $stmt = $pdo->prepare("DELETE FROM team_members WHERE team_id = ? AND user_id = ?");
    $stmt->execute([$teamId, $userId]);
    
    // Log the removal (simulates email notification)
    logActivity($pdo, 'member_removed', $_SESSION['lab28_user_id'] ?? 'unknown', $userId, $teamId, 
        "User {$user['username']} was removed from team {$team['name']}");
    
    return [
        'success' => true,
        'message' => 'Member removed successfully',
        // INFORMATION DISCLOSURE: Leaking sensitive PII
        'removed_user' => [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email']
        ],
        'from_team' => [
            'team_id' => $team['team_id'],
            'name' => $team['name'],
            'description' => $team['description']
        ]
    ];
}

/**
 * Log activity (simulates email notification system)
 */
function logActivity($pdo, $actionType, $actorUserId, $targetUserId, $targetTeamId, $details) {
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (action_type, actor_user_id, target_user_id, target_team_id, details, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $actionType,
        $actorUserId,
        $targetUserId,
        $targetTeamId,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
}

/**
 * Get recent activity for a user
 */
function getUserActivity($pdo, $userId, $limit = 20) {
    $stmt = $pdo->prepare("
        SELECT * FROM activity_log 
        WHERE target_user_id = ? OR actor_user_id = ?
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$userId, $userId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get pending invitations for user
 */
function getPendingInvitations($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT i.*, t.name as team_name, u.username as inviter_name
        FROM team_invitations i
        INNER JOIN teams t ON i.team_id = t.team_id
        INNER JOIN users u ON i.inviter_user_id = u.user_id
        WHERE i.invitee_user_id = ? AND i.status = 'pending'
        ORDER BY i.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}
