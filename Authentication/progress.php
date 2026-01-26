<?php
// Authentication Labs - Progress Tracking System
// This tracks which labs have been completed

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'auth_progress';

// Create connection
$progress_conn = new mysqli($db_host, $db_user, $db_pass);

// Create database if it doesn't exist
if (!$progress_conn->query("CREATE DATABASE IF NOT EXISTS $db_name")) {
    error_log("Failed to create progress database: " . $progress_conn->error);
}

$progress_conn->select_db($db_name);

// Create solved_labs table if it doesn't exist
$table_sql = "CREATE TABLE IF NOT EXISTS solved_labs (
    lab_number INT PRIMARY KEY,
    solved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reset_count INT DEFAULT 0
)";

if (!$progress_conn->query($table_sql)) {
    error_log("Failed to create solved_labs table: " . $progress_conn->error);
}

/**
 * Mark a lab as solved
 */
function markLabSolved($lab_number) {
    global $progress_conn;
    
    $stmt = $progress_conn->prepare("INSERT INTO solved_labs (lab_number) VALUES (?) ON DUPLICATE KEY UPDATE solved_at = CURRENT_TIMESTAMP");
    $stmt->bind_param("i", $lab_number);
    $stmt->execute();
    $stmt->close();
}

/**
 * Check if a lab is solved
 */
function isLabSolved($lab_number) {
    global $progress_conn;
    
    $stmt = $progress_conn->prepare("SELECT lab_number FROM solved_labs WHERE lab_number = ?");
    $stmt->bind_param("i", $lab_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $solved = $result->num_rows > 0;
    $stmt->close();
    
    return $solved;
}

/**
 * Reset a lab (mark as unsolved)
 */
function resetLab($lab_number) {
    global $progress_conn;
    
    // Delete the solved record
    $stmt = $progress_conn->prepare("DELETE FROM solved_labs WHERE lab_number = ?");
    $stmt->bind_param("i", $lab_number);
    $stmt->execute();
    $stmt->close();
}

/**
 * Get all solved labs
 */
function getAllSolvedLabs() {
    global $progress_conn;
    
    $result = $progress_conn->query("SELECT lab_number FROM solved_labs ORDER BY lab_number");
    $solved = [];
    while ($row = $result->fetch_assoc()) {
        $solved[] = $row['lab_number'];
    }
    
    return $solved;
}

/**
 * Get total solved count
 */
function getSolvedCount() {
    global $progress_conn;
    
    $result = $progress_conn->query("SELECT COUNT(*) as count FROM solved_labs");
    $row = $result->fetch_assoc();
    
    return $row['count'];
}
?>
