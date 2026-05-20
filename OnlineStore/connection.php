<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "onlinestore";

$dbc = mysqli_connect($hostname, $username, $password, $dbname);

if (!$dbc) {
    die("Cannot connect to database. Import online_store.sql and update connection.php if your MySQL settings are different.");
}

mysqli_set_charset($dbc, "utf8mb4");
?>
