<?php
include "includes/app_init.php";

session_unset();
session_destroy();
header("Location:index.php");
exit();
?>
