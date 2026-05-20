<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("admin"));

$message = "";

if (isset($_POST["delete_user"])) {
    $userId = (int) ($_POST["user_id"] ?? 0);
    mysqli_query($dbc, "DELETE FROM users WHERE id = $userId AND role = 'student'");
    $message = "User removed.";
}

if (isset($_POST["save_user"])) {
    $userId = (int) ($_POST["user_id"] ?? 0);
    $firstName = mysqli_real_escape_string($dbc, test_input($_POST["first_name"] ?? ""));
    $lastName = mysqli_real_escape_string($dbc, test_input($_POST["last_name"] ?? ""));
    $email = mysqli_real_escape_string($dbc, test_input($_POST["email"] ?? ""));
    $phone = mysqli_real_escape_string($dbc, test_input($_POST["phone"] ?? ""));
    mysqli_query($dbc, "UPDATE users SET first_name = '$firstName', last_name = '$lastName', email = '$email', phone = '$phone' WHERE id = $userId");
    $message = "User updated.";
}

$editId = (int) ($_GET["edit_id"] ?? 0);
$editUser = null;
if ($editId > 0) {
    $result = mysqli_query($dbc, "SELECT * FROM users WHERE id = $editId LIMIT 1");
    if ($result) {
        $editUser = mysqli_fetch_assoc($result);
    }
}

$users = fetch_all($dbc, "SELECT * FROM users ORDER BY role DESC, registration_date DESC, id DESC");

render_page_start("Edit or remove user accounts", "admin_users.php");
?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<?php if ($editUser) { ?>
    <section class="panel">
        <h2>Edit User</h2>
        <form method="post" action="admin_users.php">
            <input type="hidden" name="user_id" value="<?php echo (int) $editUser["id"]; ?>">
            <div class="row">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo h($editUser["first_name"]); ?>" required>
            </div>
            <div class="row">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo h($editUser["last_name"]); ?>" required>
            </div>
            <div class="row">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo h($editUser["email"]); ?>" required>
            </div>
            <div class="row">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo h($editUser["phone"]); ?>">
            </div>
            <div class="actions">
                <input type="submit" name="save_user" value="Save User">
            </div>
        </form>
    </section>
<?php } ?>

<section class="panel">
    <h2>All Users</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?php echo h($user["first_name"] . " " . $user["last_name"]); ?></td>
                    <td><?php echo h($user["email"]); ?></td>
                    <td><?php echo h($user["phone"]); ?></td>
                    <td><?php echo h($user["role"]); ?></td>
                    <td>
                        <a class="button-link" href="admin_users.php?edit_id=<?php echo (int) $user["id"]; ?>">Edit</a>
                        <?php if ($user["role"] === "student") { ?>
                            <form method="post" class="inline-form" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="user_id" value="<?php echo (int) $user["id"]; ?>">
                                <input type="submit" name="delete_user" value="Remove">
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_page_end(); ?>
