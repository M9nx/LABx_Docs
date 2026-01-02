<?php
/**
 * Lab 27: VULNERABLE Stats API - Equity Endpoint
 * 
 * VULNERABILITY: No ownership verification!
 * The 'accounts' parameter accepts ANY account number without checking
 * if the authenticated user owns that account.
 * 
 * Endpoint: /api/stats/equity.php?time_range=365&accounts={accountNumber}
 */

require_once '../../config.php';
header('Content-Type: application/json');

// Must be logged in (but that's the only check!)
if (!isLoggedIn()) {
    jsonResponse(['error' => 'Authentication required'], 401);
}

$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(['error' => 'Database connection failed'], 500);
}

// Get parameters
$timeRange = intval($_GET['time_range'] ?? 365);
$accountNumber = $_GET['accounts'] ?? '';

if (empty($accountNumber)) {
    jsonResponse(['error' => 'Account number required'], 400);
}

// VULNERABILITY: We fetch stats for ANY account without verifying ownership!
// A secure implementation would check: WHERE account_number = ? AND user_id = ?

$stats = getAccountStats($pdo, $accountNumber, $timeRange, 'equity');

// Check if this is an IDOR attempt (accessing someone else's account)
$userAccounts = getUserAccounts($pdo, $_SESSION['user_id']);
$ownedAccountNumbers = array_column($userAccounts, 'account_number');
$isIdorAttempt = !in_array($accountNumber, $ownedAccountNumbers);

// Log the API access
logApiAccess($pdo, $_SESSION['user_id'], '/api/stats/equity', $accountNumber, $isIdorAttempt);

if ($isIdorAttempt && !empty($stats)) {
    // Log successful IDOR exploit
    logActivity($pdo, $_SESSION['user_id'], 'idor_exploit', 
        "User accessed equity stats for account: $accountNumber (not their account)");
}

// Get account info for response (also vulnerable - returns any account)
$accountInfo = getAccountByNumber($pdo, $accountNumber);

jsonResponse([
    'success' => true,
    'account' => $accountNumber,
    'stat_type' => 'equity',
    'time_range' => $timeRange,
    'account_type' => $accountInfo['account_type'] ?? 'Unknown',
    'currency' => $accountInfo['currency'] ?? 'USD',
    'current_equity' => $accountInfo['equity'] ?? 0,
    'data' => $stats
]);
