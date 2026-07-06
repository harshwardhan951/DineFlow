<?php
session_start();
session_destroy();
header("Location: /DineFlow/login.php");
exit();
?>

