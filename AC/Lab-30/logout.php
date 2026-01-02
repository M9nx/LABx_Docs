<?php
// Lab 30: Stocky - Logout
session_start();
session_destroy();
header('Location: login.php');
exit;
?>
