<?php
require_once 'config.php';
require_once '../progress.php';

// Reset lab progress
resetLab(12);

// Read and execute SQL file
$sql = file_get_contents('database_setup.sql');

// Execute multi-query
if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
}

if ($conn->error) {
    echo "Error setting up database: " . $conn->error;
} else {
    header("Location: index.php?setup=success");
    exit;
}
?>
