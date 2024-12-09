<?php
session_start();
session_destroy();
header("Location: views/signin-signup.php");
exit();
?>