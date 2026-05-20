<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("student"));

$error = "";
$message = "";
$userId = (int) $_SESSION["user_id"];
$formValues = array(
    "item_name" => "",
    "description" => "",
    "price" => ""
);

// Students can create listings here, but only up to 5 active items at one time.
if (isset($_POST["create_item"])) {
    $formValues["item_name"] = test_input($_POST["item_name"] ?? "");
    $formValues["description"] = test_input($_POST["description"] ?? "");
    $formValues["price"] = test_input($_POST["price"] ?? "");

    if (user_unsold_count($dbc, $userId) >= 5) {
        $error = "You already have 5 active listings. Mark an item sold before adding another one.";
    } else {
        $itemName = $formValues["item_name"];
        $description = $formValues["description"];
        $price = (float) ($_POST["price"] ?? 0);
        $imageName = handle_upload("item_image");

        if ($itemName === "" || $description === "" || $price <= 0 || $imageName === "") {
            if ($itemName === "" || $description === "" || $price <= 0) {
                $error = "Please provide an item name, description, and valid price.";
            } else {
                $error = upload_problem_message("item_image");
            }
        } else {
            $safeName = mysqli_real_escape_string($dbc, $itemName);
            $safeDescription = mysqli_real_escape_string($dbc, $description);
            $safeImage = mysqli_real_escape_string($dbc, $imageName);
            // Each item record stores the seller, upload date, price, description, and image filename.
            $sql = "INSERT INTO items (item_name, description, price, image_name, upload_date, seller_id, status)
                    VALUES ('$safeName', '$safeDescription', $price, '$safeImage', CURDATE(), $userId, 'available')";
            if (mysqli_query($dbc, $sql)) {
                $message = "Item posted successfully.";
                $formValues = array(
                    "item_name" => "",
                    "description" => "",
                    "price" => ""
                );
            } else {
                $error = "Item could not be posted.";
            }
        }
    }
}

// Marking an item sold updates its status so it moves into transaction history.
if (isset($_POST["mark_sold"])) {
    $itemId = (int) ($_POST["item_id"] ?? 0);
    mysqli_query($dbc, "UPDATE items SET status = 'sold', sold_date = CURDATE() WHERE item_id = $itemId AND seller_id = $userId");
    $message = "Item marked as sold.";
}

$myItems = fetch_all($dbc, "SELECT * FROM items WHERE seller_id = $userId ORDER BY upload_date DESC, item_id DESC");

render_page_start("List up to five items for sale", "sell.php");
?>
<?php if ($error !== "") { ?><div class="error-message"><?php echo h($error); ?></div><?php } ?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>Post an Item</h2>
    <form method="post" enctype="multipart/form-data" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <label for="item_name">Item Name</label>
            <input type="text" id="item_name" name="item_name" value="<?php echo h($formValues["item_name"]); ?>" required>
        </div>
        <div class="row">
            <label for="description">Short Description</label>
            <textarea id="description" name="description" required><?php echo h($formValues["description"]); ?></textarea>
        </div>
        <div class="row">
            <label for="price">Price</label>
            <input type="number" id="price" name="price" min="1" step="0.01" value="<?php echo h($formValues["price"]); ?>" required>
        </div>
        <div class="row">
            <label for="item_image">Item Picture</label>
            <input type="file" id="item_image" name="item_image" accept=".jpg,.jpeg,.png,.gif,.webp" required>
        </div>
        <div class="actions">
            <input type="submit" name="create_item" value="Post Item">
        </div>
    </form>
</section>

<section class="panel">
    <h2>My Listings</h2>
    <div class="grid-3">
        <?php foreach ($myItems as $item) { ?>
            <article class="item-card">
                <img src="<?php echo h(item_image_path($item["image_name"])); ?>" alt="Item photo">
                <div class="item-copy">
                    <div class="badge"><?php echo h(ucfirst($item["status"])); ?></div>
                    <h3><?php echo h($item["item_name"]); ?></h3>
                    <p><?php echo h($item["description"]); ?></p>
                    <p><strong>$<?php echo number_format((float) $item["price"], 2); ?></strong></p>
                    <?php if ($item["status"] === "available") { ?>
                        <form method="post" class="inline-form" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="item_id" value="<?php echo (int) $item["item_id"]; ?>">
                            <input type="submit" name="mark_sold" value="Mark Sold">
                        </form>
                    <?php } ?>
                </div>
            </article>
        <?php } ?>
    </div>
</section>

<?php render_page_end(); ?>
