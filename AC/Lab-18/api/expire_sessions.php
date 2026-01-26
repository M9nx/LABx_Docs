<?php
/**
 * VULNERABLE ENDPOINT - IDOR Session Expiration
 * 
 * This endpoint expires user sessions but does NOT verify that
 * the account_id belongs to the currently authenticated user.
 * 
 * VULNERABILITY: Any authenticated user can expire ANY other user's sessions
 * by simply changing the account_id parameter.
 */

require_once '../config.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$currentUser = getCurrentUser();

// Get parameters
$account_id = $_POST['account_id'] ?? null;
$action = $_POST['action'] ?? null;
$session_id = $_POST['session_id'] ?? null;

if (!$account_id || !$action) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// ============================================================
// VULNERABLE CODE - NO AUTHORIZATION CHECK!
// The code below does NOT verify that account_id == current user's ID
// This allows any user to expire any other user's sessions
// ============================================================

/*
 * SECURE CODE would look like this:
 * 
 * if ($account_id != $currentUser['user_id']) {
 *     http_response_code(403);
 *     echo json_encode(['error' => 'You can only manage your own sessions']);
 *     exit;
 * }
 */

try {
    if ($action === 'expire_all') {
        // VULNERABLE: Expires ALL sessions for the given account_id
        // without checking if the current user owns that account
        
        $stmt = $pdo->prepare("UPDATE user_sessions SET is_active = 0 WHERE user_id = ?");
        $stmt->execute([$account_id]);
        $affected = $stmt->rowCount();
        
        // Log the action (shows who performed it vs who was affected)
        $stmt = $pdo->prepare("INSERT INTO session_activity_log (user_id, action, target_user_id, details, ip_address) VALUES (?, 'expire_all_sessions', ?, ?, ?)");
        $stmt->execute([
            $currentUser['user_id'],  // Who performed the action
            $account_id,               // Who was targeted (could be different!)
            "Expired $affected sessions for account #$account_id",
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
        
        // Check if this was an IDOR attack (current user != target)
        $isIDOR = ($account_id != $currentUser['user_id']);
        
        // Get target user info for response
        $stmt = $pdo->prepare("SELECT username, store_name FROM users WHERE id = ?");
        $stmt->execute([$account_id]);
        $targetUser = $stmt->fetch();
        
        // Return response
        $response = [
            'success' => true,
            'message' => "Successfully expired $affected session(s)",
            'account_id' => $account_id,
            'sessions_expired' => $affected,
            'target_user' => $targetUser ? $targetUser['username'] : 'Unknown'
        ];
        
        // Add IDOR detection for lab purposes
        if ($isIDOR) {
            $response['warning'] = '⚠️ IDOR DETECTED! You expired sessions for a different user!';
            $response['your_id'] = $currentUser['user_id'];
            $response['target_id'] = $account_id;
            
            // Check if this completes the lab (expired victim's sessions)
            if ($account_id == 2) { // victim_store's ID
                $response['lab_complete'] = true;
                $response['lab_message'] = '🎉 Congratulations! You successfully exploited the IDOR vulnerability and expired victim_store\'s sessions!';
            }
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        
        // If the user expired their OWN sessions, redirect to login
        if (!$isIDOR) {
            // Destroy their PHP session too
            session_destroy();
        }
        
    } elseif ($action === 'expire_single' && $session_id) {
        // VULNERABLE: Expires a specific session without ownership check
        
        $stmt = $pdo->prepare("UPDATE user_sessions SET is_active = 0 WHERE id = ? AND user_id = ?");
        $stmt->execute([$session_id, $account_id]);
        $affected = $stmt->rowCount();
        
        // Log the action
        $stmt = $pdo->prepare("INSERT INTO session_activity_log (user_id, action, target_user_id, details, ip_address) VALUES (?, 'expire_single_session', ?, ?, ?)");
        $stmt->execute([
            $currentUser['user_id'],
            $account_id,
            "Expired session #$session_id for account #$account_id",
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
        
        $isIDOR = ($account_id != $currentUser['user_id']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Session expired successfully',
            'session_id' => $session_id,
            'account_id' => $account_id,
            'idor_detected' => $isIDOR
        ], JSON_PRETTY_PRINT);
        
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
