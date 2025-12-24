<?php
require_once 'config.php';
require_once '../progress.php';

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

// Reset lab progress
resetLab(13);

// Redirect with success message
header("Location: index.php?setup=success");
exit;
?>
