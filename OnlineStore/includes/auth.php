<?php
function require_login($allowedRoles = array())
{
    if (!isset($_SESSION["user_id"])) {
        header("Location:index.php");
        exit();
    }

    if (!empty($allowedRoles) && !in_array($_SESSION["role"], $allowedRoles, true)) {
        if ($_SESSION["role"] === "admin") {
            header("Location:admin_home.php");
        } else {
            header("Location:user_home.php");
        }
        exit();
    }
}

function save_login_cookie($email)
{
    setcookie("saved_login_email", $email, time() + (86400 * 30), "/");
    $_COOKIE["saved_login_email"] = $email;
}

function clear_login_cookie()
{
    setcookie("saved_login_email", "", time() - 3600, "/");
    unset($_COOKIE["saved_login_email"]);
}
?>
