<?php
session_start();
require_once 'config.php';
require_once '../progress.php';

// Check authentication
if (!isset($_SESSION['manager_id'])) {
    header("Location: login.php");
    exit;
}

$managerId = $_SESSION['manager_id'];
$isAdmin = $_SESSION['role'] === 'admin';
$csrfToken = $_SESSION['csrf_token'] ?? '';

// Get parameters
$token = $_GET['token'] ?? '';
$clientId = intval($_GET['clientid'] ?? 0);
$campaignId = intval($_GET['campaignid'] ?? 0);
$bannerId = intval($_GET['bannerid'] ?? 0);

// Validate CSRF token
if ($token !== $csrfToken) {
    die("CSRF token validation failed");
}

// Validate required parameters
if (!$clientId || !$campaignId || !$bannerId) {
    header("Location: dashboard.php?error=Missing+parameters");
    exit;
}

// ============================================================
// ACCESS CONTROL CHECK #1: Verify access to CLIENT
// This check exists - validates user has access to the client
// ============================================================
if (!$isAdmin) {
    $clientCheck = $conn->prepare("SELECT id FROM clients WHERE id = ? AND manager_id = ?");
    $clientCheck->bind_param("ii", $clientId, $managerId);
    $clientCheck->execute();
    if ($clientCheck->get_result()->num_rows === 0) {
        die("Unauthorized: You don't have access to this client.");
    }
}

// ============================================================
// ACCESS CONTROL CHECK #2: Verify access to CAMPAIGN  
// This check exists - validates campaign belongs to client
// ============================================================
$campaignCheck = $conn->prepare("SELECT id FROM campaigns WHERE id = ? AND client_id = ?");
$campaignCheck->bind_param("ii", $campaignId, $clientId);
$campaignCheck->execute();
if ($campaignCheck->get_result()->num_rows === 0) {
    die("Unauthorized: Campaign doesn't belong to this client.");
}

// ============================================================
// VULNERABILITY: MISSING ACCESS CONTROL CHECK #3
// NO CHECK if banner belongs to the campaign!
// This allows IDOR - deleting ANY banner by manipulating bannerid
// ============================================================

// Get banner name for logging (but doesn't validate ownership!)
$bannerStmt = $conn->prepare("SELECT banner_name, campaign_id FROM banners WHERE id = ?");
$bannerStmt->bind_param("i", $bannerId);
$bannerStmt->execute();
$banner = $bannerStmt->get_result()->fetch_assoc();

if (!$banner) {
    header("Location: campaign-banners.php?clientid=$clientId&campaignid=$campaignId&error=Banner+not+found");
    exit;
}

// Check if this is actually someone else's banner (for lab solution detection)
$originalCampaignId = $banner['campaign_id'];
$victimBannerDeleted = ($originalCampaignId != $campaignId);

// VULNERABLE: Delete the banner without verifying it belongs to the provided campaign
$deleteStmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
$deleteStmt->bind_param("i", $bannerId);
$deleteStmt->execute();

// Log the deletion
$logStmt = $conn->prepare("INSERT INTO deletion_logs (manager_id, action, target_type, target_id, target_name, client_id_used, campaign_id_used, ip_address) VALUES (?, 'delete', 'banner', ?, ?, ?, ?, ?)");
$logStmt->bind_param("iissis", $managerId, $bannerId, $banner['banner_name'], $clientId, $campaignId, $_SERVER['REMOTE_ADDR']);
$logStmt->execute();

// If user deleted another manager's banner via IDOR, redirect to success
if ($victimBannerDeleted) {
    markLabSolved(14);
    header("Location: success.php?banner=" . urlencode($banner['banner_name']) . "&bannerid=$bannerId");
    exit;
}

// Normal deletion - redirect back to campaign banners page
header("Location: campaign-banners.php?clientid=$clientId&campaignid=$campaignId&deleted=" . urlencode($banner['banner_name']));
exit;
?>
