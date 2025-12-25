<?php
// Lab 20 - IDOR API Key Management
// Database Configuration

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'ac_lab20';

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
