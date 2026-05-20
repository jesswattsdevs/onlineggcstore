<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("student"));

$message = "";

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}

if (isset($_POST["add_to_cart"])) {
    $selected = $_POST["selected_items"] ?? array();
    foreach ($selected as $itemId) {
        $itemId = (int) $itemId;
        if ($itemId > 0 && !in_array($itemId, $_SESSION["cart"], true)) {
            $_SESSION["cart"][] = $itemId;
        }
    }
    $message = "Selected items were added to your cart.";
}

$items = fetch_all($dbc, "SELECT i.*, u.first_name, u.last_name
    FROM items i
    JOIN users u ON u.id = i.seller_id
    WHERE i.status = 'available' AND i.seller_id <> " . (int) $_SESSION["user_id"] . "
    ORDER BY i.is_featured DESC, i.upload_date DESC, i.item_id DESC");

render_page_start("Browse all available items", "buy.php");
?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>Available Items</h2>
    <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
        <div class="grid-3">
            <?php foreach ($items as $item) { ?>
                <article class="item-card">
                    <img src="<?php echo h(item_image_path($item["image_name"])); ?>" alt="Item photo">
                    <div class="item-copy">
                        <?php if ((int) $item["is_featured"] === 1) { ?><div class="badge">Featured</div><?php } ?>
                        <h3><?php echo h($item["item_name"]); ?></h3>
                        <p><?php echo h($item["description"]); ?></p>
                        <p><strong>$<?php echo number_format((float) $item["price"], 2); ?></strong></p>
                        <p class="muted">Seller: <?php echo h($item["first_name"] . " " . $item["last_name"]); ?></p>
                        <label><input type="checkbox" name="selected_items[]" value="<?php echo (int) $item["item_id"]; ?>"> Add to cart</label>
                    </div>
                </article>
            <?php } ?>
        </div>
        <div class="actions">
            <input type="submit" name="add_to_cart" value="Add Selected Items to Cart">
        </div>
    </form>
</section>

<?php render_page_end(); ?>
