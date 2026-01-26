<?php
// Lab 29: LinkedPro Newsletter Platform - Subscribe to Newsletter
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($conn);
$newsletter_id = $_GET['id'] ?? 0;

// Get newsletter
$stmt = $conn->prepare("SELECT * FROM newsletters WHERE id = ?");
$stmt->bind_param("i", $newsletter_id);
$stmt->execute();
$newsletter = $stmt->get_result()->fetch_assoc();

if (!$newsletter) {
    header('Location: newsletters.php');
    exit();
}

// Check if already subscribed
$stmt = $conn->prepare("SELECT * FROM subscribers WHERE newsletter_id = ? AND user_id = ?");
$stmt->bind_param("ii", $newsletter_id, $user['user_id']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header('Location: newsletter.php?id=' . $newsletter_id);
    exit();
}

// Subscribe
$stmt = $conn->prepare("INSERT INTO subscribers (newsletter_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $newsletter_id, $user['user_id']);
$stmt->execute();

// Update subscriber count
$conn->query("UPDATE newsletters SET subscriber_count = subscriber_count + 1 WHERE id = $newsletter_id");

// Log activity
logActivity($conn, $user['user_id'], 'subscribe', 'newsletter', $newsletter_id, 'Subscribed to: ' . $newsletter['title']);

// Redirect back
header('Location: newsletter.php?id=' . $newsletter_id . '&subscribed=1');
exit();
?>
