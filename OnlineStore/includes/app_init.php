<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("America/Los_Angeles");

// This file centralizes the shared setup so every page loads the same helper functions.
require_once __DIR__ . "/utilities.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/theme.php";
require_once __DIR__ . "/layout.php";
?>
