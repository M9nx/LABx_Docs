<?php
// Lab 22: Logout
require_once 'config.php';

session_destroy();
header('Location: login.php');
exit;
?>
