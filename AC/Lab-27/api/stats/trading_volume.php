<?php
/**
 * Lab 27: VULNERABLE Stats API - Trading Volume Endpoint
 * 
 * VULNERABILITY: No ownership verification!
 * Endpoint: /api/stats/trading_volume.php?time_range=365&accounts={accountNumber}
 */

require_once '../../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Authentication required'], 401);
}

$pdo = getDBConnection();
if (!$pdo) {
    jsonResponse(['error' => 'Database connection failed'], 500);
}

$timeRange = intval($_GET['time_range'] ?? 365);
$accountNumber = $_GET['accounts'] ?? '';

if (empty($accountNumber)) {
    jsonResponse(['error' => 'Account number required'], 400);
}

// VULNERABILITY: No ownership check!
$stats = getAccountStats($pdo, $accountNumber, $timeRange, 'trading_volume');

$userAccounts = getUserAccounts($pdo, $_SESSION['user_id']);
$ownedAccountNumbers = array_column($userAccounts, 'account_number');
$isIdorAttempt = !in_array($accountNumber, $ownedAccountNumbers);

logApiAccess($pdo, $_SESSION['user_id'], '/api/stats/trading_volume', $accountNumber, $isIdorAttempt);

if ($isIdorAttempt && !empty($stats)) {
    logActivity($pdo, $_SESSION['user_id'], 'idor_exploit', 
        "User accessed trading_volume stats for account: $accountNumber");
}

$accountInfo = getAccountByNumber($pdo, $accountNumber);

$totalVolume = 0;
foreach ($stats as $stat) {
    $totalVolume += floatval($stat['value']);
}

jsonResponse([
    'success' => true,
    'account' => $accountNumber,
    'stat_type' => 'trading_volume',
    'time_range' => $timeRange,
    'account_type' => $accountInfo['account_type'] ?? 'Unknown',
    'currency' => $accountInfo['currency'] ?? 'USD',
    'total_volume' => $totalVolume,
    'data' => $stats
]);
