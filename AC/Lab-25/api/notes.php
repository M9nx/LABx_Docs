<?php
/**
 * VULNERABLE API ENDPOINT - Notes API
 * 
 * This endpoint is intentionally vulnerable to demonstrate
 * Broken Access Control / IDOR vulnerability.
 * 
 * The vulnerability: When noteable_type is changed to "personal_snippet",
 * the server doesn't check if the user has access to that snippet.
 */

require_once '../config.php';

header('Content-Type: application/json');

// Require login
if (!isLoggedIn()) {
    jsonResponse(['error' => 'Authentication required'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        createNote();
        break;
    case 'PUT':
        updateNote();
        break;
    case 'DELETE':
        deleteNote();
        break;
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * VULNERABLE: Create a note without proper authorization check
 */
function createNote() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $noteableType = $input['noteable_type'] ?? '';
    $noteableId = intval($input['noteable_id'] ?? 0);
    $content = $input['content'] ?? '';
    
    if (empty($noteableType) || empty($noteableId) || empty($content)) {
        jsonResponse(['error' => 'Missing required fields'], 400);
    }
    
    // Validate noteable_type
    if (!in_array($noteableType, ['issue', 'personal_snippet'])) {
        jsonResponse(['error' => 'Invalid noteable_type'], 400);
    }
    
    // VULNERABILITY: No ownership/permission check for personal_snippet!
    // The getNoteable function returns ANY snippet without checking permissions
    $noteable = getNoteable($noteableType, $noteableId);
    
    if (!$noteable) {
        jsonResponse(['error' => 'Target not found'], 404);
    }
    
    // Get the title of the target (THIS LEAKS PRIVATE SNIPPET TITLES!)
    $targetTitle = '';
    if ($noteableType === 'issue') {
        $targetTitle = $noteable['title'];
    } elseif ($noteableType === 'personal_snippet') {
        // VULNERABILITY: We store the private snippet's title in activity log!
        $targetTitle = $noteable['title'];
    }
    
    // Create the note
    $stmt = $pdo->prepare("
        INSERT INTO notes (author_id, noteable_type, noteable_id, content)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$_SESSION['user_id'], $noteableType, $noteableId, $content]);
    $noteId = $pdo->lastInsertId();
    
    // Log activity - THIS EXPOSES THE PRIVATE SNIPPET TITLE!
    logActivity(
        $_SESSION['user_id'],
        'created_note',
        $noteableType,
        $noteableId,
        $targetTitle,  // <-- The private snippet title is logged here!
        "Created note on {$noteableType} #{$noteableId}"
    );
    
    jsonResponse([
        'success' => true,
        'message' => 'Note created successfully',
        'note_id' => $noteId,
        'target_type' => $noteableType,
        'target_id' => $noteableId,
        'target_title' => $targetTitle  // Also returned in response for immediate feedback
    ]);
}

/**
 * VULNERABLE: Update a note - attacker can update notes on ANY snippet
 */
function updateNote() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $noteId = intval($input['note_id'] ?? $_GET['id'] ?? 0);
    $noteableType = $input['noteable_type'] ?? '';
    $noteableId = intval($input['noteable_id'] ?? 0);
    $content = $input['content'] ?? '';
    
    if (empty($noteId) || empty($content)) {
        jsonResponse(['error' => 'Missing required fields'], 400);
    }
    
    // Check if note belongs to current user (this is correct)
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ? AND author_id = ?");
    $stmt->execute([$noteId, $_SESSION['user_id']]);
    $note = $stmt->fetch();
    
    if (!$note) {
        jsonResponse(['error' => 'Note not found or not authorized'], 404);
    }
    
    // VULNERABILITY: If noteable_type/id provided, we don't re-check permissions
    // This allows updating the note's target to any personal_snippet
    $updateType = !empty($noteableType) ? $noteableType : $note['noteable_type'];
    $updateId = !empty($noteableId) ? $noteableId : $note['noteable_id'];
    
    $stmt = $pdo->prepare("
        UPDATE notes 
        SET content = ?, noteable_type = ?, noteable_id = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$content, $updateType, $updateId, $noteId]);
    
    // Get title for activity log
    $noteable = getNoteable($updateType, $updateId);
    $targetTitle = $noteable ? ($noteable['title'] ?? 'Unknown') : 'Unknown';
    
    logActivity(
        $_SESSION['user_id'],
        'updated_note',
        $updateType,
        $updateId,
        $targetTitle,
        "Updated note #{$noteId}"
    );
    
    jsonResponse([
        'success' => true,
        'message' => 'Note updated successfully',
        'note_id' => $noteId,
        'target_title' => $targetTitle
    ]);
}

/**
 * Delete a note - user can delete their own notes
 */
function deleteNote() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $noteId = intval($input['note_id'] ?? $_GET['id'] ?? 0);
    
    if (empty($noteId)) {
        jsonResponse(['error' => 'Note ID required'], 400);
    }
    
    // Check ownership
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ? AND author_id = ?");
    $stmt->execute([$noteId, $_SESSION['user_id']]);
    $note = $stmt->fetch();
    
    if (!$note) {
        jsonResponse(['error' => 'Note not found or not authorized'], 404);
    }
    
    // Get title for logging before deletion
    $noteable = getNoteable($note['noteable_type'], $note['noteable_id']);
    $targetTitle = $noteable ? ($noteable['title'] ?? 'Unknown') : 'Unknown';
    
    // Delete the note
    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->execute([$noteId]);
    
    logActivity(
        $_SESSION['user_id'],
        'deleted_note',
        $note['noteable_type'],
        $note['noteable_id'],
        $targetTitle,
        "Deleted note #{$noteId}"
    );
    
    jsonResponse([
        'success' => true,
        'message' => 'Note deleted successfully'
    ]);
}
