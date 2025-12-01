<?php
// Database configuration for Lab 5 - Blind SQL Injection with Time Delays
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lab5_blind_sqli');

// Database connection
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Function to generate or retrieve tracking ID
function getTrackingId() {
    if (isset($_COOKIE['TrackingId'])) {
        return $_COOKIE['TrackingId'];
    }
    
    // Generate new tracking ID
    $trackingId = 'TK' . bin2hex(random_bytes(8));
    setcookie('TrackingId', $trackingId, time() + (86400 * 30), '/'); // 30 days
    return $trackingId;
}

// VULNERABLE: Analytics tracking function with blind SQL injection
function trackUserActivity($trackingId) {
    $conn = getConnection();
    
    // VULNERABLE CODE: Direct concatenation without sanitization
    // This creates a blind SQL injection vulnerability in the tracking system
    $query = "UPDATE analytics SET last_seen = NOW() WHERE tracking_id = '" . $trackingId . "'";
    
    try {
        // Execute the vulnerable query - results are not returned to user
        $conn->query($query);
        
        // If tracking ID doesn't exist, insert new record
        if ($conn->affected_rows == 0) {
            $insertQuery = "INSERT INTO analytics (tracking_id, first_seen, last_seen, page_views) VALUES ('" . $trackingId . "', NOW(), NOW(), 1)";
            $conn->query($insertQuery);
        } else {
            // Increment page views
            $updateQuery = "UPDATE analytics SET page_views = page_views + 1 WHERE tracking_id = '" . $trackingId . "'";
            $conn->query($updateQuery);
        }
    } catch (Exception $e) {
        // Silently fail - this makes it "blind" injection
        error_log("Analytics tracking error: " . $e->getMessage());
    }
    
    $conn->close();
}
?>