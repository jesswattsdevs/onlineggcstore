<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("admin"));

mysqli_query($dbc, "DELETE FROM items WHERE status = 'sold' AND sold_date IS NOT NULL AND sold_date < DATE_SUB(CURDATE(), INTERVAL 100 DAY)");

$stats = array(
    "users" => fetch_all($dbc, "SELECT COUNT(*) AS total FROM users WHERE role = 'student'")[0]["total"] ?? 0,
    "available" => fetch_all($dbc, "SELECT COUNT(*) AS total FROM items WHERE status = 'available'")[0]["total"] ?? 0,
    "sold_month" => fetch_all($dbc, "SELECT COUNT(*) AS total FROM items WHERE status = 'sold' AND sold_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")[0]["total"] ?? 0
);

$recentSold = fetch_all($dbc, "SELECT i.item_name, i.price, i.description, i.sold_date,
    seller.first_name AS seller_first, seller.last_name AS seller_last,
    buyer.first_name AS buyer_first, buyer.last_name AS buyer_last
    FROM items i
    LEFT JOIN users seller ON seller.id = i.seller_id
    LEFT JOIN users buyer ON buyer.id = i.buyer_id
    WHERE i.status = 'sold' AND i.sold_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY i.sold_date DESC, i.item_id DESC");

render_page_start("Administrator dashboard", "admin_home.php");
?>
<div class="grid-3">
    <div class="hero-stat">
        <strong>Students</strong>
        <p class="muted"><?php echo (int) $stats["users"]; ?> registered student users.</p>
    </div>
    <div class="hero-stat">
        <strong>Items For Sale</strong>
        <p class="muted"><?php echo (int) $stats["available"]; ?> active listings available now.</p>
    </div>
    <div class="hero-stat">
        <strong>Sold This Month</strong>
        <p class="muted"><?php echo (int) $stats["sold_month"]; ?> items sold in the past 30 days.</p>
    </div>
</div>

<section class="panel">
    <h2>Sold Items In The Past Month</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Seller</th>
                <th>Buyer</th>
                <th>Item</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
            <?php foreach ($recentSold as $item) { ?>
                <tr>
                    <td><?php echo h($item["seller_first"] . " " . $item["seller_last"]); ?></td>
                    <td><?php echo h($item["buyer_first"] . " " . $item["buyer_last"]); ?></td>
                    <td><?php echo h($item["item_name"]); ?></td>
                    <td>$<?php echo number_format((float) $item["price"], 2); ?></td>
                    <td><?php echo h($item["description"]); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_page_end(); ?>
