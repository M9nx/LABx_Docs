<?php
session_start();

// Clear session data
session_unset();
session_destroy();

// Clear cookies
setcookie('Admin', '', time() - 3600, '/', '', false, true);
setcookie('username', '', time() - 3600, '/', '', false, true);

// Redirect to login page
header('Location: login.php?logged_out=1');
exit();
?>