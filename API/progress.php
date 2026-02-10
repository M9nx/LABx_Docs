<?php
/**
 * API Security Category - Progress Helper
 * Provides lab progress tracking functions for all API labs
 */

require_once __DIR__ . '/../db-config.php';

/**
 * Check if a specific lab is solved
 * @param int $labNumber - The lab number to check
 * @return bool - True if solved, false otherwise
 */
function isLabSolved($labNumber) {
    $creds = getDbCredentials();
    
    if (!$creds['configured']) {
        return false;
    }
    
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass'], 'api_progress');
    
    if ($conn->connect_error) {
        return false;
    }
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM solved_labs WHERE lab_number = ?");
    if (!$stmt) {
        $conn->close();
        return false;
    }
    
    $stmt->bind_param("i", $labNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $solved = (int)$row['count'] > 0;
    
    $stmt->close();
    $conn->close();
    
    return $solved;
}

/**
 * Mark a lab as solved
 * @param int $labNumber - The lab number to mark as solved
 * @return bool - True if successfully marked, false otherwise
 */
function markLabSolved($labNumber) {
    $creds = getDbCredentials();
    
    if (!$creds['configured']) {
        return false;
    }
    
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass'], 'api_progress');
    
    if ($conn->connect_error) {
        $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass']);
        if ($conn->connect_error) {
            return false;
        }
        
        $conn->query("CREATE DATABASE IF NOT EXISTS api_progress");
        $conn->select_db('api_progress');
        $conn->query("CREATE TABLE IF NOT EXISTS solved_labs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            lab_number INT NOT NULL UNIQUE,
            solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    $stmt = $conn->prepare("INSERT IGNORE INTO solved_labs (lab_number) VALUES (?)");
    if (!$stmt) {
        $conn->close();
        return false;
    }
    
    $stmt->bind_param("i", $labNumber);
    $success = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $success;
}

/**
 * Get all solved lab numbers
 * @return array - Array of solved lab numbers
 */
function getSolvedLabs() {
    $creds = getDbCredentials();
    $solved = [];
    
    if (!$creds['configured']) {
        return $solved;
    }
    
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($creds['host'], $creds['user'], $creds['pass'], 'api_progress');
    
    if ($conn->connect_error) {
        return $solved;
    }
    
    $result = $conn->query("SELECT lab_number FROM solved_labs ORDER BY lab_number");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $solved[] = (int)$row['lab_number'];
        }
    }
    
    $conn->close();
    
    return $solved;
}
?>
