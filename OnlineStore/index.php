<?php
include "includes/app_init.php";
include "connection.php";

$email = $_COOKIE["saved_login_email"] ?? "";
$message = "";
$error = "";

// The login form checks the user table, stores the account in session, and redirects by role.
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $email = test_input($_POST["email"] ?? "");
    $password = test_input($_POST["password"] ?? "");
    $remember = isset($_POST["remember_me"]);
    $safeEmail = mysqli_real_escape_string($dbc, $email);
    $safePassword = mysqli_real_escape_string($dbc, $password);

    $sql = "SELECT * FROM users WHERE email = '$safeEmail' AND pw = '$safePassword' LIMIT 1";
    $result = mysqli_query($dbc, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION["user_id"] = (int) $user["id"];
        $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];
        $_SESSION["role"] = $user["role"];

        if ($remember) {
            save_login_cookie($user["email"]);
        } else {
            clear_login_cookie();
        }

        if ($user["role"] === "admin") {
            header("Location:admin_home.php");
        } else {
            header("Location:user_home.php");
        }
        exit();
    } else {
        $error = "Login failed. Please check your email and password.";
    }
}

// Guests can see featured items and announcements before logging in.
$featured = fetch_all($dbc, "SELECT i.*, u.first_name, u.last_name
    FROM items i
    JOIN users u ON u.id = i.seller_id
    WHERE i.status = 'available'
    ORDER BY i.is_featured DESC, i.upload_date DESC
    LIMIT 6");

$announcements = fetch_all($dbc, "SELECT title, body FROM announcements WHERE active = 1 ORDER BY created_at DESC LIMIT 3");

render_page_start("Browse items, login, or create your account", "index.php");
?>
<?php if ($error !== "") { ?><div class="error-message"><?php echo h($error); ?></div><?php } ?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<div class="grid-2">
    <section class="panel">
        <h2>Login</h2>
        <p>Students can browse without logging in. To buy, sell, or track transactions, log in here.</p>
        <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
            <div class="row">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo h($email); ?>" required>
            </div>
            <div class="row">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="row">
                <label for="remember_me">Remember Login</label>
                <div><input type="checkbox" id="remember_me" name="remember_me" value="1"> Save my email in a cookie</div>
            </div>
            <div class="actions">
                <input type="submit" value="Login">
                <a class="button-link secondary-button" href="registration.php">Register</a>
                <a class="button-link secondary-button" href="forgot_password.php">Forgot Password</a>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>How This Works</h2>
        <p>GGC students can post used books, electronics, computers, and small furniture. Each seller can keep up to 5 active listings at a time.</p>
        <div class="grid-3">
            <div class="hero-stat">
                <strong>Sell</strong>
                <p class="muted">Upload a photo, short description, and price for each item you want to list.</p>
            </div>
            <div class="hero-stat">
                <strong>Buy</strong>
                <p class="muted">Browse all available items and add them to a simple shopping cart before checkout.</p>
            </div>
            <div class="hero-stat">
                <strong>Review</strong>
                <p class="muted">Review sold and purchased items anytime in your transaction history.</p>
            </div>
        </div>
    </section>
</div>

<section class="panel">
    <h2>Latest Announcements</h2>
    <?php if (empty($announcements)) { ?>
        <p class="muted">No announcements have been posted yet.</p>
    <?php } else { ?>
        <?php foreach ($announcements as $announcement) { ?>
            <div class="announcement">
                <strong><?php echo h($announcement["title"]); ?></strong>
                <p><?php echo nl2br(h($announcement["body"])); ?></p>
            </div>
        <?php } ?>
    <?php } ?>
</section>

<section class="panel">
    <h2>Featured & Recent Items</h2>
    <div class="grid-3">
        <?php foreach ($featured as $item) { ?>
            <article class="item-card">
                <img src="<?php echo h(item_image_path($item["image_name"])); ?>" alt="Item photo">
                <div class="item-copy">
                    <?php if ((int) $item["is_featured"] === 1) { ?><div class="badge">Featured</div><?php } ?>
                    <h3><?php echo h($item["item_name"]); ?></h3>
                    <p><?php echo h($item["description"]); ?></p>
                    <p><strong>$<?php echo number_format((float) $item["price"], 2); ?></strong></p>
                    <p class="muted">Seller: <?php echo h($item["first_name"] . " " . $item["last_name"]); ?></p>
                </div>
            </article>
        <?php } ?>
    </div>
</section>

<?php render_page_end(); ?>
