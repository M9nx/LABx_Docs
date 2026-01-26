<?php
/**
 * Lab 26: Logout
 */
session_start();
session_destroy();
header("Location: login.php");
exit;
