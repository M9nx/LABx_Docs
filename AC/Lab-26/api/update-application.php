<?php
/**
 * Lab 26: API Endpoint - Vulnerable Update Handler
 * This can also be called directly via API
 */

require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input or form data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$applicationId = $input['application']['id'] ?? $input['application_id'] ?? 0;
$applicationName = $input['application']['name'] ?? $input['name'] ?? null;

if (!$applicationId) {
    http_response_code(400);
    echo json_encode(['error' => 'Application ID is required']);
    exit;
}

// VULNERABILITY: Fetch application without ownership check!
$app = getApplicationById($pdo, $applicationId);

if (!$app) {
    http_response_code(404);
    echo json_encode(['error' => 'Application not found']);
    exit;
}

// VULNERABILITY: When name is missing, leak the credentials in error response
if (empty($applicationName)) {
    $isExploit = $app['user_id'] != $_SESSION['user_id'];
    
    if ($isExploit) {
        logActivity($pdo, $_SESSION['user_id'], 'api_idor_exploit', 'api_application', $applicationId,
            'API exploit: accessed credentials of user ' . $app['user_id']);
    }
    
    // Return error WITH the application details (the vulnerability!)
    http_response_code(422);
    echo json_encode([
        'error' => 'Name must be provided',
        'application' => [
            'id' => $app['id'],
            'name' => $app['name'],
            'client_id' => $app['client_id'],
            'client_secret' => $app['client_secret'],  // LEAKED!
            'redirect_uri' => $app['redirect_uri'],
            'scopes' => $app['scopes'],
            'status' => $app['status']
        ],
        '_exploit' => $isExploit ? [
            'detected' => true,
            'message' => 'IDOR vulnerability exploited! You accessed another user\'s credentials.'
        ] : null
    ]);
    exit;
}

// If we get here with proper data, check ownership for actual update
if ($app['user_id'] != $_SESSION['user_id']) {
    // Still leak it even on this path for educational purposes
    http_response_code(422);
    echo json_encode([
        'error' => 'Validation failed',
        'application' => [
            'id' => $app['id'],
            'name' => $app['name'],
            'client_id' => $app['client_id'],
            'client_secret' => $app['client_secret'],
            'scopes' => $app['scopes']
        ]
    ]);
    exit;
}

// Legitimate update
$description = $input['application']['description'] ?? $input['description'] ?? $app['description'];
$redirectUri = $input['application']['redirect_uri'] ?? $input['redirect_uri'] ?? $app['redirect_uri'];

$stmt = $pdo->prepare("
    UPDATE api_applications 
    SET name = ?, description = ?, redirect_uri = ?
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$applicationName, $description, $redirectUri, $applicationId, $_SESSION['user_id']]);

echo json_encode([
    'success' => true,
    'message' => 'Application updated successfully',
    'application' => [
        'id' => $app['id'],
        'name' => $applicationName
    ]
]);
