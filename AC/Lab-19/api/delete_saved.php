<?php
/**
 * Lab 19: VULNERABLE API Endpoint
 * IDOR - Delete Users Saved Projects
 * 
 * VULNERABILITY: No ownership verification!
 * Any authenticated user can delete any saved project by knowing its ID
 */

session_start();
require_once '../config.php';
require_once '../../progress.php';

header('Content-Type: application/json');

// Must be logged in (but we don't check ownership!)
if (!isset($_SESSION['lab19_user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$saved_id = isset($_GET['saved_id']) ? intval($_GET['saved_id']) : 0;

if ($saved_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid saved_id']);
    exit;
}

try {
    // VULNERABLE: No ownership check!
    // Should verify: WHERE id = ? AND user_id = ?
    // But only checks: WHERE id = ?
    
    // First, get info about what we're deleting (for lab completion check)
    $stmt = $pdo->prepare("SELECT user_id, project_id FROM saved_projects WHERE id = ?");
    $stmt->execute([$saved_id]);
    $saved = $stmt->fetch();
    
    if (!$saved) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Saved project not found']);
        exit;
    }
    
    // Check if attacker is deleting victim's saved project (for lab completion)
    $attacker_id = $_SESSION['lab19_user_id'];
    $victim_id = $saved['user_id'];
    
    // VULNERABLE DELETE - No user_id check!
    $stmt = $pdo->prepare("DELETE FROM saved_projects WHERE id = ?");
    $stmt->execute([$saved_id]);
    
    $response = [
        'success' => true,
        'message' => 'Saved project deleted successfully',
        'deleted_id' => $saved_id
    ];
    
    // Lab completion: attacker deletes victim's saved project
    // victim_designer has user_id = 2, their saved projects are IDs 101-105
    if ($attacker_id != $victim_id && $victim_id == 2) {
        markLabSolved(19);
        $response['lab_solved'] = true;
        $response['message'] = 'IDOR Exploited! You deleted another user\'s saved project!';
    }
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
