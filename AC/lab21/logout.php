<?php
// Lab 21: Logout
session_start();
session_destroy();
header('Location: login.php');
exit;
?>
