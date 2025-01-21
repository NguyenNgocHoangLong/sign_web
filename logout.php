<?php include_once("header.php");?>
<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
?>
<?php include_once("footer.php");?>