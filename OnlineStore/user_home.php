<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("student"));

$user = current_user($dbc);
$announcements = fetch_all($dbc, "SELECT title, body, created_at FROM announcements WHERE active = 1 ORDER BY created_at DESC LIMIT 5");
$recentItems = fetch_all($dbc, "SELECT * FROM items WHERE status = 'available' ORDER BY upload_date DESC, item_id DESC LIMIT 5");
$myActiveCount = user_unsold_count($dbc, $_SESSION["user_id"]);

render_page_start("Student dashboard", "user_home.php");
?>
<div class="grid-3">
    <div class="hero-stat">
        <strong>Welcome</strong>
        <p class="muted"><?php echo h($user["first_name"]); ?>, use the menu to sell, buy, review transactions, or update your profile.</p>
    </div>
    <div class="hero-stat">
        <strong>Active Listings</strong>
        <p class="muted"><?php echo $myActiveCount; ?> of 5 active items are currently listed.</p>
    </div>
    <div class="hero-stat">
        <strong>Ready to Shop</strong>
        <p class="muted"><?php echo count($recentItems); ?> recent items are highlighted below.</p>
    </div>
</div>

<section class="panel">
    <h2>Announcements</h2>
    <?php if (empty($announcements)) { ?>
        <p class="muted">No announcements right now.</p>
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
    <h2>Most Recently Listed Items</h2>
    <div class="grid-3">
        <?php foreach ($recentItems as $item) { ?>
            <article class="item-card">
                <img src="<?php echo h(item_image_path($item["image_name"])); ?>" alt="Item photo">
                <div class="item-copy">
                    <h3><?php echo h($item["item_name"]); ?></h3>
                    <p><?php echo h($item["description"]); ?></p>
                    <strong>$<?php echo number_format((float) $item["price"], 2); ?></strong>
                </div>
            </article>
        <?php } ?>
    </div>
</section>

<?php render_page_end(); ?>
