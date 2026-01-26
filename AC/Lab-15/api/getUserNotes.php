<?php
session_start();
require_once '../config.php';

// Set JSON content type
header('Content-Type: application/json');

// Check if user is logged in (basic auth check - but the IDOR is that we don't check if they're requesting their OWN data)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required', 'status' => 'unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Extract email from the complex request structure (mimicking MTN MobAd API)
$targetEmail = null;
if (isset($input['params']['updates'][0]['value']['userEmail'])) {
    $targetEmail = $input['params']['updates'][0]['value']['userEmail'];
}

// Also support simpler format for easier testing
if (!$targetEmail && isset($input['userEmail'])) {
    $targetEmail = $input['userEmail'];
}

if (!$targetEmail) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing userEmail parameter', 'status' => 'bad_request']);
    exit;
}

// VULNERABLE CODE - No authorization check!
// The endpoint returns data for ANY email without verifying if the logged-in user
// has permission to access that email's data

try {
    // Get user by email
    $stmt = $pdo->prepare("SELECT user_id, email, full_name, phone_number, address, 
                           account_type, company_name, tax_id, bank_account, api_key, 
                           created_at, last_login FROM users WHERE email = ?");
    $stmt->execute([$targetEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Log failed attempt
        $logStmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, target_email, ip_address, user_agent, response_status) VALUES (?, 'getUserNotes', ?, ?, ?, 'not_found')");
        $logStmt->execute([$_SESSION['user_id'], $targetEmail, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown']);
        
        http_response_code(404);
        echo json_encode(['error' => 'User not found', 'status' => 'not_found']);
        exit;
    }
    
    // Get user's notes
    $notesStmt = $pdo->prepare("SELECT note_id, title, content, note_type, created_at, updated_at FROM user_notes WHERE user_id = ? ORDER BY created_at DESC");
    $notesStmt->execute([$user['user_id']]);
    $notes = $notesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get user's campaigns
    $campaignsStmt = $pdo->prepare("SELECT campaign_id, campaign_name, budget, target_audience, status, impressions, clicks FROM ad_campaigns WHERE user_id = ?");
    $campaignsStmt->execute([$user['user_id']]);
    $campaigns = $campaignsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get account settings
    $settingsStmt = $pdo->prepare("SELECT notification_email, backup_phone, billing_address, payment_method, card_last_four FROM account_settings WHERE user_id = ?");
    $settingsStmt->execute([$user['user_id']]);
    $settings = $settingsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Log the access attempt
    $logStmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, target_email, ip_address, user_agent, request_data, response_status) VALUES (?, 'getUserNotes', ?, ?, ?, ?, 'success')");
    $logStmt->execute([
        $_SESSION['user_id'], 
        $targetEmail, 
        $_SERVER['REMOTE_ADDR'] ?? 'unknown', 
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        json_encode($input)
    ]);
    
    // Check if this is an IDOR exploit (accessing someone else's data)
    $isExploit = ($targetEmail !== $_SESSION['email']);
    
    // Build response
    $response = [
        'status' => 'success',
        'data' => [
            'user' => [
                'email' => $user['email'],
                'fullName' => $user['full_name'],
                'phoneNumber' => $user['phone_number'],
                'address' => $user['address'],
                'accountType' => $user['account_type'],
                'company' => $user['company_name'],
                'taxId' => $user['tax_id'],
                'bankAccount' => $user['bank_account'],
                'apiKey' => $user['api_key'],
                'memberSince' => $user['created_at'],
                'lastLogin' => $user['last_login']
            ],
            'notes' => $notes,
            'campaigns' => $campaigns,
            'accountSettings' => $settings
        ],
        '_meta' => [
            'requestedBy' => $_SESSION['email'],
            'requestedEmail' => $targetEmail,
            'timestamp' => date('c'),
            'isOwnData' => !$isExploit
        ]
    ];
    
    // If this is an IDOR exploit, include success marker
    if ($isExploit) {
        $response['_exploit'] = [
            'detected' => true,
            'message' => 'IDOR Exploit Successful! You accessed another user\'s PII.',
            'victimEmail' => $targetEmail,
            'attackerEmail' => $_SESSION['email']
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'status' => 'internal_error']);
}
?>
