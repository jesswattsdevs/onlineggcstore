<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("admin"));

$message = "";

if (isset($_POST["delete_item"])) {
    $itemId = (int) ($_POST["item_id"] ?? 0);
    mysqli_query($dbc, "DELETE FROM items WHERE item_id = $itemId");
    $message = "Item deleted.";
}

if (isset($_POST["toggle_feature"])) {
    $itemId = (int) ($_POST["item_id"] ?? 0);
    mysqli_query($dbc, "UPDATE items SET is_featured = 1 - is_featured WHERE item_id = $itemId");
    $message = "Featured status updated.";
}

$items = fetch_all($dbc, "SELECT i.*, u.first_name, u.last_name
    FROM items i
    JOIN users u ON u.id = i.seller_id
    ORDER BY FIELD(i.status, 'available', 'sold'), i.upload_date DESC, i.item_id DESC");

render_page_start("Review, feature, or remove items", "admin_items.php");
?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>All Store Items</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Owner</th>
                <th>Item</th>
                <th>Price</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Picture</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo h($item["first_name"] . " " . $item["last_name"]); ?></td>
                    <td><?php echo h($item["item_name"]); ?><br><span class="muted"><?php echo h($item["description"]); ?></span></td>
                    <td>$<?php echo number_format((float) $item["price"], 2); ?></td>
                    <td><?php echo h($item["status"]); ?></td>
                    <td><?php echo (int) $item["is_featured"] === 1 ? "Yes" : "No"; ?></td>
                    <td><img src="<?php echo h(item_image_path($item["image_name"])); ?>" alt="Item photo" style="width:90px;height:70px;object-fit:cover;border-radius:8px;"></td>
                    <td>
                        <form method="post" class="inline-form" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="item_id" value="<?php echo (int) $item["item_id"]; ?>">
                            <input type="submit" name="toggle_feature" value="<?php echo (int) $item["is_featured"] === 1 ? "Unfeature" : "Feature"; ?>">
                        </form>
                        <form method="post" class="inline-form" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="item_id" value="<?php echo (int) $item["item_id"]; ?>">
                            <input type="submit" name="delete_item" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_page_end(); ?>
