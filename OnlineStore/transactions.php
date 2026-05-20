<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("student"));

$userId = (int) $_SESSION["user_id"];

$soldItems = fetch_all($dbc, "SELECT i.*, u.first_name, u.last_name
    FROM items i
    LEFT JOIN users u ON u.id = i.buyer_id
    WHERE i.seller_id = $userId AND i.status = 'sold'
    ORDER BY i.sold_date DESC, i.item_id DESC");

$boughtItems = fetch_all($dbc, "SELECT i.*, u.first_name, u.last_name
    FROM items i
    LEFT JOIN users u ON u.id = i.seller_id
    WHERE i.buyer_id = $userId
    ORDER BY i.sold_date DESC, i.item_id DESC");

render_page_start("View sold and purchased items", "transactions.php");
?>
<div class="grid-2">
    <section class="panel">
        <h2>Items I Sold</h2>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Item</th>
                    <th>Buyer</th>
                    <th>Price</th>
                </tr>
                <?php foreach ($soldItems as $item) { ?>
                    <tr>
                        <td><?php echo h($item["item_name"]); ?></td>
                        <td><?php echo h(trim(($item["first_name"] ?? "") . " " . ($item["last_name"] ?? ""))); ?></td>
                        <td>$<?php echo number_format((float) $item["price"], 2); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>

    <section class="panel">
        <h2>Items I Purchased</h2>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Item</th>
                    <th>Seller</th>
                    <th>Price</th>
                </tr>
                <?php foreach ($boughtItems as $item) { ?>
                    <tr>
                        <td><?php echo h($item["item_name"]); ?></td>
                        <td><?php echo h(trim(($item["first_name"] ?? "") . " " . ($item["last_name"] ?? ""))); ?></td>
                        <td>$<?php echo number_format((float) $item["price"], 2); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>
</div>

<?php render_page_end(); ?>
