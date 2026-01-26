<?php
/**
 * Lab 24: Vulnerable GraphQL API Endpoint
 * 
 * VULNERABILITY: IDOR - No authorization check on model/version retrieval
 * Any authenticated user can access ANY model by providing the GID
 * 
 * Model IDs are sequential (1000500, 1000501, ...) making enumeration trivial
 */

require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Csrf-Token');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Require POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$operationName = $data['operationName'] ?? '';
$variables = $data['variables'] ?? [];

/**
 * VULNERABLE: Handle getModel operation
 * Does NOT check if the requesting user owns or has access to the model
 */
function handleGetModel($pdo, $variables) {
    $modelGid = $variables['id'] ?? '';
    
    if (empty($modelGid)) {
        return ['error' => 'Model ID required'];
    }
    
    // Decode the GID (base64 encoded)
    // Format: gid://gitlab/Ml::Model/{internal_id}
    $decoded = base64_decode($modelGid);
    
    if (!preg_match('/Ml::Model\/(\d+)$/', $decoded, $matches)) {
        // Try raw GID format
        if (!preg_match('/Ml::Model\/(\d+)$/', $modelGid, $matches)) {
            return ['error' => 'Invalid model ID format'];
        }
    }
    
    $internalId = (int)$matches[1];
    
    // VULNERABLE: Query retrieves ANY model by internal_id - NO AUTHORIZATION CHECK!
    $stmt = $pdo->prepare("
        SELECT m.*, p.name as project_name, p.visibility as project_visibility,
               u.username as owner_username, u.full_name as owner_name, u.email as owner_email
        FROM ml_models m 
        JOIN projects p ON m.project_id = p.id 
        JOIN users u ON m.owner_id = u.id
        WHERE m.internal_id = ?
    ");
    $stmt->execute([$internalId]);
    $model = $stmt->fetch();
    
    if (!$model) {
        return ['data' => ['mlModel' => null]];
    }
    
    // Get latest version
    $stmt = $pdo->prepare("
        SELECT v.* FROM model_versions v 
        WHERE v.model_id = ? 
        ORDER BY v.created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$model['id']]);
    $latestVersion = $stmt->fetch();
    
    // Get candidate for latest version
    $candidate = null;
    if ($latestVersion) {
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', p.id, 'name', p.name, 'value', p.value)) 
                    FROM model_params p WHERE p.candidate_id = c.id) as params,
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', m.id, 'name', m.name, 'value', m.value)) 
                    FROM model_metadata m WHERE m.candidate_id = c.id) as metadata,
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', mt.id, 'name', mt.name, 'value', mt.value, 'step', mt.step)) 
                    FROM model_metrics mt WHERE mt.candidate_id = c.id) as metrics
            FROM model_candidates c 
            WHERE c.version_id = ?
            LIMIT 1
        ");
        $stmt->execute([$latestVersion['id']]);
        $candidate = $stmt->fetch();
    }
    
    // Log the access
    logActivity($pdo, 'API_GET_MODEL', 'ml_model', $model['id'], 
        "API access to model {$model['name']} (internal_id: {$internalId}) by user " . ($_SESSION['username'] ?? 'unknown'));
    
    // Build response (intentionally verbose to leak information)
    $response = [
        'data' => [
            'mlModel' => [
                'id' => base64_encode("gid://gitlab/Ml::Model/{$model['internal_id']}"),
                '__typename' => 'MlModel',
                'internal_id' => $model['internal_id'],
                'name' => $model['name'],
                'description' => $model['description'],
                'versionCount' => $model['version_count'],
                'candidateCount' => $model['candidate_count'],
                'framework' => $model['framework'],
                'visibility' => $model['visibility'],
                'project' => [
                    'name' => $model['project_name'],
                    'visibility' => $model['project_visibility'],
                    '__typename' => 'Project'
                ],
                'owner' => [
                    'username' => $model['owner_username'],
                    'name' => $model['owner_name'],
                    'email' => $model['owner_email'],
                    '__typename' => 'User'
                ],
                'latestVersion' => $latestVersion ? [
                    'id' => base64_encode("gid://gitlab/Ml::ModelVersion/{$latestVersion['internal_id']}"),
                    '__typename' => 'MlModelVersion',
                    'version' => $latestVersion['version'],
                    'packageId' => $latestVersion['package_id'],
                    'description' => $latestVersion['description'],
                    'accuracy' => $latestVersion['accuracy'],
                    'loss' => $latestVersion['loss'],
                    'trainingDataSize' => $latestVersion['training_data_size'],
                    'hyperparameters' => json_decode($latestVersion['hyperparameters'], true),
                    'artifactPath' => $latestVersion['artifact_path'],
                    'candidate' => $candidate ? [
                        'id' => $candidate['id'],
                        '__typename' => 'MlCandidate',
                        'name' => $candidate['name'],
                        'iid' => $candidate['iid'],
                        'eid' => $candidate['eid'],
                        'status' => $candidate['status'],
                        'params' => [
                            'nodes' => json_decode($candidate['params'] ?? '[]', true) ?: [],
                            '__typename' => 'MlCandidateParamConnection'
                        ],
                        'metadata' => [
                            'nodes' => json_decode($candidate['metadata'] ?? '[]', true) ?: [],
                            '__typename' => 'MlCandidateMetadataConnection'
                        ],
                        'metrics' => [
                            'nodes' => json_decode($candidate['metrics'] ?? '[]', true) ?: [],
                            '__typename' => 'MlCandidateMetricConnection'
                        ],
                        '_links' => [
                            'showPath' => "/ml/models/{$model['internal_id']}/candidates/{$candidate['id']}",
                            'artifactPath' => $latestVersion['artifact_path'],
                            '__typename' => 'MlCandidateLinks'
                        ]
                    ] : null,
                    '_links' => [
                        'showPath' => "/ml/models/{$model['internal_id']}/versions/{$latestVersion['internal_id']}",
                        '__typename' => 'MlModelVersionLinks'
                    ]
                ] : null,
                'createdAt' => $model['created_at'],
                'updatedAt' => $model['updated_at']
            ]
        ]
    ];
    
    return $response;
}

/**
 * VULNERABLE: Handle getModelVersion operation
 * Does NOT check if the requesting user has access to the model version
 */
function handleGetModelVersion($pdo, $variables) {
    $modelGid = $variables['modelId'] ?? '';
    $versionGid = $variables['modelVersionId'] ?? '';
    
    if (empty($modelGid) || empty($versionGid)) {
        return ['error' => 'Model ID and Version ID required'];
    }
    
    // Decode GIDs
    $decodedModel = base64_decode($modelGid);
    $decodedVersion = base64_decode($versionGid);
    
    if (!preg_match('/Ml::Model\/(\d+)$/', $decodedModel, $modelMatches)) {
        if (!preg_match('/Ml::Model\/(\d+)$/', $modelGid, $modelMatches)) {
            return ['error' => 'Invalid model ID format'];
        }
    }
    
    if (!preg_match('/Ml::ModelVersion\/(\d+)$/', $decodedVersion, $versionMatches)) {
        if (!preg_match('/Ml::ModelVersion\/(\d+)$/', $versionGid, $versionMatches)) {
            return ['error' => 'Invalid version ID format'];
        }
    }
    
    $modelInternalId = (int)$modelMatches[1];
    $versionInternalId = (int)$versionMatches[1];
    
    // VULNERABLE: No authorization check!
    $stmt = $pdo->prepare("
        SELECT m.*, u.username as owner_username
        FROM ml_models m 
        JOIN users u ON m.owner_id = u.id
        WHERE m.internal_id = ?
    ");
    $stmt->execute([$modelInternalId]);
    $model = $stmt->fetch();
    
    if (!$model) {
        return ['data' => ['mlModel' => null]];
    }
    
    // Get specific version
    $stmt = $pdo->prepare("SELECT * FROM model_versions WHERE internal_id = ? AND model_id = ?");
    $stmt->execute([$versionInternalId, $model['id']]);
    $version = $stmt->fetch();
    
    // Get candidate
    $candidate = null;
    if ($version) {
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', p.id, 'name', p.name, 'value', p.value)) 
                    FROM model_params p WHERE p.candidate_id = c.id) as params,
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', m.id, 'name', m.name, 'value', m.value)) 
                    FROM model_metadata m WHERE m.candidate_id = c.id) as metadata,
                   (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', mt.id, 'name', mt.name, 'value', mt.value, 'step', mt.step)) 
                    FROM model_metrics mt WHERE mt.candidate_id = c.id) as metrics
            FROM model_candidates c 
            WHERE c.version_id = ?
            LIMIT 1
        ");
        $stmt->execute([$version['id']]);
        $candidate = $stmt->fetch();
    }
    
    // Log access
    logActivity($pdo, 'API_GET_VERSION', 'ml_model_version', $version['id'] ?? 0, 
        "API access to version {$versionInternalId} of model {$model['name']}");
    
    return [
        'data' => [
            'mlModel' => [
                'id' => base64_encode("gid://gitlab/Ml::Model/{$model['internal_id']}"),
                '__typename' => 'MlModel',
                'name' => $model['name'],
                'owner' => $model['owner_username'],
                'version' => $version ? [
                    'id' => base64_encode("gid://gitlab/Ml::ModelVersion/{$version['internal_id']}"),
                    '__typename' => 'MlModelVersion',
                    'version' => $version['version'],
                    'packageId' => $version['package_id'],
                    'description' => $version['description'],
                    'accuracy' => $version['accuracy'],
                    'loss' => $version['loss'],
                    'hyperparameters' => json_decode($version['hyperparameters'], true),
                    'artifactPath' => $version['artifact_path'],
                    'candidate' => $candidate ? [
                        'id' => $candidate['id'],
                        '__typename' => 'MlCandidate',
                        'name' => $candidate['name'],
                        'status' => $candidate['status'],
                        'params' => ['nodes' => json_decode($candidate['params'] ?? '[]', true) ?: []],
                        'metadata' => ['nodes' => json_decode($candidate['metadata'] ?? '[]', true) ?: []],
                        'metrics' => ['nodes' => json_decode($candidate['metrics'] ?? '[]', true) ?: []]
                    ] : null
                ] : null
            ]
        ]
    ];
}

// Route operation
switch ($operationName) {
    case 'getModel':
        $response = handleGetModel($pdo, $variables);
        break;
    case 'getModelVersion':
        $response = handleGetModelVersion($pdo, $variables);
        break;
    default:
        $response = ['error' => 'Unknown operation: ' . $operationName];
}

echo json_encode($response, JSON_PRETTY_PRINT);
