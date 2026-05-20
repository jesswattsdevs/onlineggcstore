<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("student", "admin"));

$user = current_user($dbc);
$message = "";
$error = "";

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $firstName = test_input($_POST["first_name"] ?? "");
    $lastName = test_input($_POST["last_name"] ?? "");
    $email = test_input($_POST["email"] ?? "");
    $phone = test_input($_POST["phone"] ?? "");
    $password = test_input($_POST["password"] ?? "");
    $theme = test_input($_POST["theme_mode"] ?? "light");
    $fontSize = test_input($_POST["font_size"] ?? "17");
    $imageName = handle_upload("profile_image", $user["profile_image"]);

    if ($firstName === "" || $lastName === "" || $email === "") {
        $error = "First name, last name, and email are required.";
    } else {
        $userId = (int) $_SESSION["user_id"];
        $safeFirst = mysqli_real_escape_string($dbc, $firstName);
        $safeLast = mysqli_real_escape_string($dbc, $lastName);
        $safeEmail = mysqli_real_escape_string($dbc, $email);
        $safePhone = mysqli_real_escape_string($dbc, $phone);
        $safeImage = mysqli_real_escape_string($dbc, $imageName);

        $sql = "UPDATE users SET
            first_name = '$safeFirst',
            last_name = '$safeLast',
            email = '$safeEmail',
            phone = '$safePhone',
            profile_image = '$safeImage'";

        if ($password !== "") {
            $safePassword = mysqli_real_escape_string($dbc, $password);
            $sql .= ", pw = '$safePassword'";
        }

        $sql .= " WHERE id = $userId";

        if (mysqli_query($dbc, $sql)) {
            set_pref_cookie("theme_mode", $theme);
            set_pref_cookie("font_size", $fontSize);
            save_login_cookie($email);
            $message = "Profile updated.";
            $user = current_user($dbc);
        } else {
            $error = "Profile could not be updated.";
        }
    }
}

render_page_start("Update personal information and UI settings", "profile.php");
?>
<?php if ($error !== "") { ?><div class="error-message"><?php echo h($error); ?></div><?php } ?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2><?php echo $_SESSION["role"] === "admin" ? "Update Admin Profile" : "Update My Profile"; ?></h2>
    <form method="post" enctype="multipart/form-data" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo h($user["first_name"]); ?>" required>
        </div>
        <div class="row">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo h($user["last_name"]); ?>" required>
        </div>
        <div class="row">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo h($user["email"]); ?>" required>
        </div>
        <div class="row">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo h($user["phone"]); ?>">
        </div>
        <div class="row">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
        </div>
        <div class="row">
            <label for="profile_image">Profile Picture</label>
            <input type="file" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>
        <div class="row">
            <label for="theme_mode">Theme</label>
            <select id="theme_mode" name="theme_mode">
                <option value="light" <?php echo get_pref("theme_mode", "light") === "light" ? "selected" : ""; ?>>Light</option>
                <option value="dark" <?php echo get_pref("theme_mode", "light") === "dark" ? "selected" : ""; ?>>Dark</option>
            </select>
        </div>
        <div class="row">
            <label for="font_size">Font Size</label>
            <select id="font_size" name="font_size">
                <option value="15" <?php echo get_pref("font_size", "17") === "15" ? "selected" : ""; ?>>Small</option>
                <option value="17" <?php echo get_pref("font_size", "17") === "17" ? "selected" : ""; ?>>Medium</option>
                <option value="20" <?php echo get_pref("font_size", "17") === "20" ? "selected" : ""; ?>>Large</option>
            </select>
        </div>
        <div class="actions">
            <input type="submit" value="Save Changes">
        </div>
    </form>
</section>

<?php render_page_end(); ?>
