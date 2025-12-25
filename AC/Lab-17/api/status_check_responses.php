<?php
// API Endpoint: /api/status_check_responses.php
// VULNERABLE: external_status_check_id not restricted to project's own status checks

header('Content-Type: application/json');
require_once '../config.php';

// Get authorization token
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Authentication token required']);
    exit;
}

// Validate token
$stmt = $pdo->prepare("
    SELECT pat.*, u.username, u.role 
    FROM personal_access_tokens pat
    JOIN users u ON pat.user_id = u.id
    WHERE pat.token = ?
");
$stmt->execute([$token]);
$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tokenData) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Invalid authentication token']);
    exit;
}

// Get parameters
$project_id = $_POST['project_id'] ?? $_GET['project_id'] ?? null;
$merge_request_iid = $_POST['merge_request_iid'] ?? $_GET['merge_request_iid'] ?? null;
$sha = $_POST['sha'] ?? $_GET['sha'] ?? null;
$external_status_check_id = $_POST['external_status_check_id'] ?? $_GET['external_status_check_id'] ?? null;
$status = $_POST['status'] ?? 'pending';

// Validate required parameters
if (!$project_id || !$external_status_check_id) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Bad Request',
        'message' => 'Required parameters: project_id, external_status_check_id'
    ]);
    exit;
}

// Check if user has access to the project
$stmt = $pdo->prepare("
    SELECT p.*, 
           CASE WHEN p.owner_id = ? THEN 'Owner' 
                ELSE (SELECT access_level FROM project_members WHERE project_id = p.id AND user_id = ?) 
           END as user_access
    FROM projects p
    WHERE p.id = ?
");
$stmt->execute([$tokenData['user_id'], $tokenData['user_id'], $project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'message' => 'Project not found']);
    exit;
}

// Check project access
if ($project['visibility'] === 'private' && !$project['user_access']) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden', 'message' => 'You do not have access to this project']);
    exit;
}

// ===== VULNERABILITY =====
// The external_status_check_id is NOT validated to belong to the project!
// An attacker can access ANY status check from ANY project by simply changing this ID
// 
// SECURE CODE WOULD CHECK:
// WHERE esc.id = ? AND esc.project_id = ?
//
// VULNERABLE CODE (current):
// WHERE esc.id = ?

$stmt = $pdo->prepare("
    SELECT esc.*, p.id as check_project_id, p.name as check_project_name, 
           p.path as check_project_path, p.visibility as check_project_visibility,
           u.username as owner_username,
           pb.name as protected_branch_name
    FROM external_status_checks esc
    JOIN projects p ON esc.project_id = p.id
    JOIN users u ON p.owner_id = u.id
    LEFT JOIN protected_branches pb ON esc.protected_branch_id = pb.id
    WHERE esc.id = ?
");
$stmt->execute([$external_status_check_id]);
$statusCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$statusCheck) {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'message' => 'External status check not found']);
    exit;
}

// ===== INFORMATION DISCLOSURE =====
// Returns sensitive information about the status check including:
// - Project name and path (even if private)
// - External URL (may contain internal infrastructure info)
// - API keys and webhook secrets embedded in URLs
// - Protected branch information

// Build response with leaked data
$response = [
    'id' => (int)$statusCheck['id'],
    'name' => $statusCheck['name'],
    'project' => [
        'id' => (int)$statusCheck['check_project_id'],
        'name' => $statusCheck['check_project_name'],
        'path' => $statusCheck['check_project_path'],
        'visibility' => $statusCheck['check_project_visibility'],
        'owner' => $statusCheck['owner_username']
    ],
    'external_url' => $statusCheck['external_url'],
    'protected_branch' => $statusCheck['protected_branch_name'],
    'status' => $status,
    'sha' => $sha,
    'message' => 'Status check response recorded',
    '_debug' => [
        'requested_project_id' => (int)$project_id,
        'status_check_project_id' => (int)$statusCheck['check_project_id'],
        'cross_project_access' => ($project_id != $statusCheck['check_project_id']) ? 'YES - IDOR DETECTED!' : 'No',
        'authenticated_user' => $tokenData['username']
    ]
];

// Check if this is cross-project access (for lab success detection)
if ($project_id != $statusCheck['check_project_id'] && $statusCheck['check_project_visibility'] === 'private') {
    // Log the successful exploit
    $response['_exploit_success'] = true;
    $response['_message'] = 'You successfully accessed a private project\'s status check configuration!';
}

// Return the vulnerable response
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
?>
