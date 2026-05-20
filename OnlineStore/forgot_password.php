<?php
include "includes/app_init.php";
include "connection.php";

$email = test_input($_POST["email"] ?? "");
$message = "";
$error = "";
$user = null;

if (isset($_POST["load_questions"])) {
    $safeEmail = mysqli_real_escape_string($dbc, $email);
    $result = mysqli_query($dbc, "SELECT * FROM users WHERE email = '$safeEmail' LIMIT 1");
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Email not found.";
    }
}

if (isset($_POST["reset_password"])) {
    $email = test_input($_POST["email"] ?? "");
    $answer1 = test_input($_POST["answer_1"] ?? "");
    $answer2 = test_input($_POST["answer_2"] ?? "");
    $newPassword = test_input($_POST["new_password"] ?? "");
    $confirmPassword = test_input($_POST["confirm_password"] ?? "");
    $safeEmail = mysqli_real_escape_string($dbc, $email);
    $result = mysqli_query($dbc, "SELECT * FROM users WHERE email = '$safeEmail' LIMIT 1");

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if ($newPassword !== $confirmPassword) {
            $error = "The new passwords do not match.";
        } elseif (strcasecmp($answer1, $user["security_answer_1"]) !== 0 || strcasecmp($answer2, $user["security_answer_2"]) !== 0) {
            $error = "Security answers do not match our records.";
        } else {
            $safePassword = mysqli_real_escape_string($dbc, $newPassword);
            mysqli_query($dbc, "UPDATE users SET pw = '$safePassword' WHERE id = " . (int) $user["id"]);
            $message = "Password updated. You can log in now.";
            $user = null;
            $email = "";
        }
    } else {
        $error = "Email not found.";
    }
}

render_page_start("Recover your password with security questions", "forgot_password.php");
?>
<?php if ($error !== "") { ?><div class="error-message"><?php echo h($error); ?></div><?php } ?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>Forgot Password</h2>
    <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <label for="email">Registered Email</label>
            <input type="email" id="email" name="email" value="<?php echo h($email); ?>" required>
        </div>
        <?php if ($user) { ?>
            <div class="row">
                <label><?php echo h($user["security_question_1"]); ?></label>
                <input type="text" name="answer_1" required>
            </div>
            <div class="row">
                <label><?php echo h($user["security_question_2"]); ?></label>
                <input type="text" name="answer_2" required>
            </div>
            <div class="row">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="row">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="actions">
                <input type="submit" name="reset_password" value="Reset Password">
            </div>
        <?php } else { ?>
            <div class="actions">
                <input type="submit" name="load_questions" value="Load Security Questions">
            </div>
        <?php } ?>
    </form>
</section>

<?php render_page_end(); ?>
