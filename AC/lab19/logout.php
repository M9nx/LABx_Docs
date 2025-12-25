<?php
session_start();
unset($_SESSION['lab19_user_id']);
unset($_SESSION['lab19_username']);
unset($_SESSION['lab19_display_name']);
unset($_SESSION['lab19_role']);
unset($_SESSION['lab19_avatar_color']);
header('Location: login.php');
exit;
?>
