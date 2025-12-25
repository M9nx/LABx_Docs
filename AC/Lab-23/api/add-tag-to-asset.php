<?php
/**
 * Lab 23: VULNERABLE AddTagToAssets API Endpoint
 * 
 * VULNERABILITY: Insecure Direct Object Reference (IDOR)
 * 
 * This endpoint accepts any tag ID without verifying that the tag
 * belongs to the current user. An attacker can:
 * 1. Decode the base64 tag ID to get the GID format
 * 2. Extract the internal ID from the GID
 * 3. Bruteforce other internal IDs to discover tags
 * 4. The API response reveals tag name and owner - information leak!
 */

require_once '../config.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// Validate operation name
$operationName = $input['operationName'] ?? '';
if ($operationName !== 'AddTagToAssets') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid operation. Expected: AddTagToAssets']);
    exit;
}

// Get variables
$variables = $input['variables'] ?? [];
$tagId = $variables['tagId'] ?? '';
$assetIds = $variables['assetIds'] ?? [];

if (empty($tagId)) {
    http_response_code(400);
    echo json_encode(['error' => 'tagId is required']);
    exit;
}

if (empty($assetIds) || !is_array($assetIds)) {
    http_response_code(400);
    echo json_encode(['error' => 'assetIds array is required']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Decode the tag ID to get internal ID
    $internalId = decodeTagId($tagId);
    
    if ($internalId === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid tag ID format. Expected base64 encoded GID.']);
        exit;
    }
    
    // VULNERABILITY: Get tag WITHOUT checking ownership!
    // This allows enumeration of other users' tags
    $stmt = $pdo->prepare("
        SELECT t.*, u.username as owner_username, u.organization as owner_org
        FROM tags t
        JOIN users u ON t.user_id = u.user_id
        WHERE t.internal_id = ?
    ");
    $stmt->execute([$internalId]);
    $tag = $stmt->fetch();
    
    if (!$tag) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Tag not found',
            'searched_internal_id' => $internalId,
            'hint' => 'Try a different internal ID. Victim tags: 49790001-49790007'
        ]);
        exit;
    }
    
    // INFORMATION LEAK: Return tag details even if not owned by user
    // Check if this is NOT the current user's tag
    $isOtherUsersTag = ($tag['user_id'] !== $_SESSION['user_id']);
    
    // Try to add tag to assets
    $addedToAssets = [];
    $failedAssets = [];
    
    foreach ($assetIds as $assetId) {
        // Verify the asset belongs to current user
        $stmt = $pdo->prepare("SELECT * FROM assets WHERE asset_id = ? AND user_id = ?");
        $stmt->execute([$assetId, $_SESSION['user_id']]);
        $asset = $stmt->fetch();
        
        if (!$asset) {
            $failedAssets[] = ['asset_id' => $assetId, 'reason' => 'Asset not found or not owned by you'];
            continue;
        }
        
        // Check if already tagged
        $stmt = $pdo->prepare("SELECT * FROM asset_tags WHERE asset_id = ? AND tag_id = ?");
        $stmt->execute([$assetId, $tag['tag_id']]);
        
        if ($stmt->fetch()) {
            $failedAssets[] = ['asset_id' => $assetId, 'reason' => 'Tag already applied'];
            continue;
        }
        
        // VULNERABILITY: Add the tag regardless of ownership!
        // In a secure app, we should check: if ($tag['user_id'] !== $_SESSION['user_id'])
        $stmt = $pdo->prepare("INSERT INTO asset_tags (asset_id, tag_id) VALUES (?, ?)");
        $stmt->execute([$assetId, $tag['tag_id']]);
        
        $addedToAssets[] = $assetId;
    }
    
    // Log the activity
    $action = $isOtherUsersTag ? 'add_other_user_tag' : 'add_own_tag';
    logActivity(
        $_SESSION['user_id'], 
        $action, 
        'asset_tag', 
        $tag['tag_id'], 
        "Added tag '{$tag['tag_name']}' (internal_id: $internalId) to assets. Is other user's tag: " . ($isOtherUsersTag ? 'YES' : 'NO')
    );
    
    // VULNERABILITY RESPONSE: Leaks sensitive information about the tag!
    $response = [
        'success' => true,
        'message' => 'Tag operation completed',
        'tag_name' => $tag['tag_name'],           // LEAKED: Tag name
        'tag_description' => $tag['description'], // LEAKED: Tag description
        'tag_color' => $tag['tag_color'],         // LEAKED: Tag color
        'tag_owner' => $tag['owner_username'],    // LEAKED: Who owns this tag
        'owner_organization' => $tag['owner_org'],// LEAKED: Organization info
        'tag_internal_id' => $internalId,         // LEAKED: Internal ID
        'is_your_tag' => !$isOtherUsersTag,
        'added_to_assets' => $addedToAssets,
        'failed_assets' => $failedAssets
    ];
    
    // Extra info if exploiting IDOR
    if ($isOtherUsersTag) {
        $response['vulnerability_exploited'] = true;
        $response['idor_warning'] = "🎯 IDOR DETECTED! You discovered another user's tag!";
        $response['discovered_tag'] = [
            'name' => $tag['tag_name'],
            'owner' => $tag['owner_username'],
            'organization' => $tag['owner_org'],
            'description' => $tag['description']
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
