<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("student"));

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}

$message = "";

// The cart is stored in session so the student can keep selected items while browsing pages.
if (isset($_POST["remove_item"])) {
    $removeId = (int) ($_POST["item_id"] ?? 0);
    $_SESSION["cart"] = array_values(array_filter($_SESSION["cart"], function ($itemId) use ($removeId) {
        return (int) $itemId !== $removeId;
    }));
    $message = "Item removed from cart.";
}

// Checkout turns available items into sold items and records the buyer on each item row.
if (isset($_POST["checkout"])) {
    foreach ($_SESSION["cart"] as $itemId) {
        $itemId = (int) $itemId;
        $buyerId = (int) $_SESSION["user_id"];
        mysqli_query($dbc, "UPDATE items SET buyer_id = $buyerId, sold_date = CURDATE(), status = 'sold' WHERE item_id = $itemId AND status = 'available'");
    }
    $_SESSION["cart"] = array();
    $message = "Checkout completed. Purchased items moved to your transaction history.";
}

$cartItems = array();
$total = 0;
if (!empty($_SESSION["cart"])) {
    // Only items that are still available are shown in the cart total.
    $ids = implode(",", array_map("intval", $_SESSION["cart"]));
    $cartItems = fetch_all($dbc, "SELECT i.*, u.first_name, u.last_name
        FROM items i
        JOIN users u ON u.id = i.seller_id
        WHERE i.item_id IN ($ids) AND i.status = 'available'");
}

foreach ($cartItems as $item) {
    $total += (float) $item["price"];
}

render_page_start("Review your cart and complete purchases", "cart.php");
?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>Shopping Cart</h2>
    <?php if (empty($cartItems)) { ?>
        <p class="muted">Your cart is empty.</p>
    <?php } else { ?>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Item</th>
                    <th>Seller</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($cartItems as $item) { ?>
                    <tr>
                        <td><?php echo h($item["item_name"]); ?></td>
                        <td><?php echo h($item["first_name"] . " " . $item["last_name"]); ?></td>
                        <td>$<?php echo number_format((float) $item["price"], 2); ?></td>
                        <td>
                            <form method="post" class="inline-form" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="item_id" value="<?php echo (int) $item["item_id"]; ?>">
                                <input type="submit" name="remove_item" value="Remove">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
        <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
            <div class="actions">
                <input type="submit" name="checkout" value="Checkout">
            </div>
        </form>
    <?php } ?>
</section>

<?php render_page_end(); ?>
