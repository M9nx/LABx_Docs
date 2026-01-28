<?php
// Lab 20 - IDOR API Key Management
// Database Configuration
// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'ac_lab20';

if (!$creds['configured']) {
    die('<div style="padding:20px;background:#fee;border:1px solid #c00;margin:20px;border-radius:8px;"><strong>Database not configured.</strong><br>Please configure your database credentials on the <a href="../../index.php">main page</a>.</div>');
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Please run setup_db.php first.");
}

// Helper functions
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function generateAPIKey() {
    return 'ak_' . bin2hex(random_bytes(24));
}

function getUserRole($pdo, $org_uuid, $user_id) {
    $stmt = $pdo->prepare("
        SELECT om.role FROM org_members om
        JOIN organizations o ON om.org_id = o.id
        WHERE o.uuid = ? AND om.user_id = ?
    ");
    $stmt->execute([$org_uuid, $user_id]);
    $result = $stmt->fetch();
    return $result ? $result['role'] : null;
}

function isOrgMember($pdo, $org_uuid, $user_id) {
    return getUserRole($pdo, $org_uuid, $user_id) !== null;
}
?>
