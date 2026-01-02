<?php
/**
 * Lab 28: VULNERABLE API Endpoint - Remove Team Member
 * MTN Developers Portal
 * 
 * VULNERABILITY: This endpoint does NOT verify if the authenticated user
 * has permission to remove members from the specified team!
 * 
 * Anyone can remove ANY user from ANY team by simply knowing the team_id and user_id.
 */

require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized - Please login']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get parameters from request
$teamId = $_POST['team_id'] ?? '';
$userId = $_POST['user_id'] ?? '';

// Validate required parameters
if (empty($teamId) || empty($userId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required parameters: team_id and user_id']);
    exit;
}

// Validate format (4 digits)
if (!preg_match('/^\d{4}$/', $teamId) || !preg_match('/^\d{4}$/', $userId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid ID format. Must be 4 digits.']);
    exit;
}

/*
 * ============================================
 * VULNERABILITY: MISSING AUTHORIZATION CHECK!
 * ============================================
 * 
 * The code below should verify:
 * 1. Is the current user a member of this team?
 * 2. Is the current user an owner or admin of this team?
 * 3. Does the current user have permission to remove this specific member?
 * 
 * Instead, it directly processes the removal request without any authorization!
 * 
 * SECURE CODE WOULD BE:
 * 
 * $currentUserId = $_SESSION['lab28_user_id'];
 * $currentUserRole = getUserTeamRole($pdo, $teamId, $currentUserId);
 * 
 * if (!$currentUserRole) {
 *     http_response_code(403);
 *     echo json_encode(['success' => false, 'error' => 'You are not a member of this team']);
 *     exit;
 * }
 * 
 * if (!in_array($currentUserRole, ['owner', 'admin'])) {
 *     http_response_code(403);
 *     echo json_encode(['success' => false, 'error' => 'You do not have permission to remove members']);
 *     exit;
 * }
 */

// VULNERABLE: Directly removes the member without checking permissions!
$result = removeMemberFromTeam($pdo, $teamId, $userId);

if ($result['success']) {
    http_response_code(200);
    
    // Log the IDOR attempt if current user is not authorized
    $currentUserId = $_SESSION['lab28_user_id'];
    $currentUserRole = getUserTeamRole($pdo, $teamId, $currentUserId);
    
    if (!in_array($currentUserRole, ['owner', 'admin'])) {
        logActivity($pdo, 'idor_attack_success', $currentUserId, $userId, $teamId, 
            "IDOR DETECTED: User {$currentUserId} removed user {$userId} from team {$teamId} without authorization");
    }
} else {
    http_response_code(400);
}

echo json_encode($result);
