<?php
function render_page_start($title, $activePage = "")
{
    $settings = get_theme_settings();
    $palette = $settings["palette"];
    $fontSize = $settings["font_size"];
    ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($title); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --bg: <?php echo $palette["bg"]; ?>;
            --panel: <?php echo $palette["panel"]; ?>;
            --text: <?php echo $palette["text"]; ?>;
            --accent: <?php echo $palette["accent"]; ?>;
            --soft: <?php echo $palette["soft"]; ?>;
            --font-size: <?php echo (int) $fontSize; ?>px;
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="hero">
            <div class="hero-inner">
                <h1>GGC Online Store</h1>
                <p class="subtitle"><?php echo h($title); ?></p>
                <?php render_nav($activePage); ?>
    <?php
}

function render_page_end()
{
    ?>
            </div>
        </div>
    </div>
</body>
</html>
    <?php
}

function render_nav($activePage)
{
    $links = array();

    if (!isset($_SESSION["user_id"])) {
        $links = array(
            "index.php" => "Browse & Login",
            "registration.php" => "Register",
            "forgot_password.php" => "Forgot Password"
        );
    } elseif ($_SESSION["role"] === "admin") {
        $links = array(
            "admin_home.php" => "Admin Home",
            "admin_items.php" => "Manage Items",
            "admin_users.php" => "Manage Users",
            "admin_announcements.php" => "Announcements",
            "profile.php" => "Update Profile",
            "logout.php" => "Logout"
        );
    } else {
        $links = array(
            "user_home.php" => "Home",
            "sell.php" => "I Want to Sell",
            "buy.php" => "I Want to Buy",
            "cart.php" => "Shopping Cart",
            "transactions.php" => "Transaction History",
            "profile.php" => "Update My Profile",
            "logout.php" => "Logout"
        );
    }

    echo '<div class="nav-links">';
    foreach ($links as $file => $label) {
        $active = $activePage === $file ? ' class="active"' : "";
        echo '<a' . $active . ' href="' . h($file) . '">' . h($label) . '</a>';
    }
    echo '</div>';
}
?>
