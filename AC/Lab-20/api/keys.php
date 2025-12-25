<?php
/**
 * VULNERABLE API ENDPOINT - IDOR Vulnerability
 * 
 * This endpoint handles API key operations (VIEW, CREATE, DELETE)
 * 
 * VULNERABILITY: The endpoint checks if the user is a MEMBER of the organization,
 * but does NOT verify if they have the required ROLE (admin/owner) to perform the action.
 * 
 * A user with "member" role can:
 * - VIEW all API keys (including sensitive production keys)
 * - CREATE new API keys
 * - DELETE existing API keys
 * 
 * These actions should only be allowed for users with "admin" or "owner" roles.
 */

session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$org_uuid = $input['org_uuid'] ?? $_GET['org_uuid'] ?? '';

if (empty($org_uuid)) {
    http_response_code(400);
    echo json_encode(['error' => 'Organization UUID is required']);
    exit;
}

// Get organization
$stmt = $pdo->prepare("SELECT * FROM organizations WHERE uuid = ?");
$stmt->execute([$org_uuid]);
$org = $stmt->fetch();

if (!$org) {
    http_response_code(404);
    echo json_encode(['error' => 'Organization not found']);
    exit;
}

// VULNERABLE CHECK: Only verifies membership, NOT role!
// This allows any member to perform admin/owner actions
$stmt = $pdo->prepare("SELECT role FROM org_members WHERE org_id = ? AND user_id = ?");
$stmt->execute([$org['id'], $_SESSION['user_id']]);
$membership = $stmt->fetch();

if (!$membership) {
    http_response_code(403);
    echo json_encode(['error' => 'You are not a member of this organization']);
    exit;
}

// MISSING ROLE CHECK HERE!
// Should be: if ($membership['role'] !== 'admin' && $membership['role'] !== 'owner') { ... }
// But this check is intentionally missing to demonstrate the IDOR vulnerability

$userRole = $membership['role'];

switch ($method) {
    case 'GET':
        // VIEW API Keys - VULNERABLE: All members can view all keys
        // Should require at least "admin" role to view sensitive keys
        $stmt = $pdo->prepare("
            SELECT ak.uuid, ak.name, ak.api_key, ak.scope, ak.created_at, u.username as created_by
            FROM api_keys ak
            LEFT JOIN users u ON ak.created_by = u.id
            WHERE ak.org_id = ?
            ORDER BY ak.created_at DESC
        ");
        $stmt->execute([$org['id']]);
        $keys = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'organization' => [
                'uuid' => $org['uuid'],
                'name' => $org['name']
            ],
            'your_role' => $userRole,
            'api_keys' => $keys,
            'vulnerability_note' => 'You can view all API keys regardless of your role!'
        ]);
        break;

    case 'POST':
        // CREATE API Key - VULNERABLE: Members can create keys
        // Should require "admin" or "owner" role
        $name = $input['name'] ?? '';
        $scope = $input['scope'] ?? 'read';
        
        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Key name is required']);
            exit;
        }
        
        // MISSING ROLE CHECK!
        // A proper implementation would be:
        // if ($userRole === 'member') {
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Insufficient permissions. Admin or Owner role required.']);
        //     exit;
        // }
        
        $keyUuid = generateUUID();
        $apiKey = generateAPIKey();
        
        $stmt = $pdo->prepare("
            INSERT INTO api_keys (uuid, org_id, name, api_key, scope, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$keyUuid, $org['id'], $name, $apiKey, $scope, $_SESSION['user_id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'API key created successfully',
            'key' => [
                'uuid' => $keyUuid,
                'name' => $name,
                'api_key' => $apiKey,
                'scope' => $scope
            ],
            'your_role' => $userRole,
            'vulnerability_note' => "You created this key as a '$userRole' - this shouldn't be allowed!"
        ]);
        break;

    case 'DELETE':
        // DELETE API Key - VULNERABLE: Members can delete keys
        // Should require "admin" or "owner" role
        $key_uuid = $input['key_uuid'] ?? '';
        
        if (empty($key_uuid)) {
            http_response_code(400);
            echo json_encode(['error' => 'Key UUID is required']);
            exit;
        }
        
        // MISSING ROLE CHECK!
        // A proper implementation would be:
        // if ($userRole === 'member') {
        //     http_response_code(403);
        //     echo json_encode(['error' => 'Insufficient permissions. Admin or Owner role required.']);
        //     exit;
        // }
        
        // Verify key belongs to this organization
        $stmt = $pdo->prepare("SELECT * FROM api_keys WHERE uuid = ? AND org_id = ?");
        $stmt->execute([$key_uuid, $org['id']]);
        $key = $stmt->fetch();
        
        if (!$key) {
            http_response_code(404);
            echo json_encode(['error' => 'API key not found in this organization']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM api_keys WHERE uuid = ?");
        $stmt->execute([$key_uuid]);
        
        echo json_encode([
            'success' => true,
            'message' => 'API key deleted successfully',
            'deleted_key' => $key['name'],
            'your_role' => $userRole,
            'vulnerability_note' => "You deleted this key as a '$userRole' - this shouldn't be allowed!"
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
