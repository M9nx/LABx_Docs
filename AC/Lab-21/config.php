<?php
// Lab 21: Stocky Application - IDOR on Column Settings
// Configuration and Database Connection
// Uses centralized database configuration
require_once __DIR__ . '/../../db-config.php';

$creds = getDbCredentials();
$db_host = $creds['host'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];
$db_name = 'ac_lab21';

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

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser($pdo) {
    if (!isLoggedIn()) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function getUserStore($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM stores WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getStoreSettings($pdo, $store_id) {
    $stmt = $pdo->prepare("SELECT * FROM column_settings WHERE store_id = ?");
    $stmt->execute([$store_id]);
    return $stmt->fetch();
}

function getLowStockProducts($pdo, $store_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE store_id = ? AND stock_quantity <= reorder_point
        ORDER BY stock_quantity ASC
    ");
    $stmt->execute([$store_id]);
    return $stmt->fetchAll();
}

function getAllProducts($pdo, $store_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE store_id = ? ORDER BY title ASC");
    $stmt->execute([$store_id]);
    return $stmt->fetchAll();
}

function calculateGrade($stock, $reorder_point) {
    $ratio = $stock / max($reorder_point, 1);
    if ($ratio <= 0.25) return ['grade' => 'A', 'class' => 'critical', 'label' => 'Critical'];
    if ($ratio <= 0.5) return ['grade' => 'B', 'class' => 'warning', 'label' => 'Warning'];
    if ($ratio <= 0.75) return ['grade' => 'C', 'class' => 'moderate', 'label' => 'Moderate'];
    return ['grade' => 'D', 'class' => 'good', 'label' => 'Good'];
}

function calculateDepletionDays($stock, $daily_sales = 2) {
    return round($stock / max($daily_sales, 0.1));
}

function calculateNeed($stock, $reorder_point) {
    return max(0, $reorder_point - $stock);
}

// App Theme Colors
$theme = [
    'primary' => '#6366f1',
    'primary_dark' => '#4f46e5',
    'secondary' => '#8b5cf6',
    'success' => '#10b981',
    'warning' => '#f59e0b',
    'danger' => '#ef4444',
    'bg_dark' => '#0f172a',
    'bg_card' => '#1e293b',
    'text' => '#e2e8f0',
    'text_muted' => '#94a3b8'
];
?>
