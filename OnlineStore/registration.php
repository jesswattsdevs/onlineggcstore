<?php
include "includes/app_init.php";
include "connection.php";

$message = "";
$error = "";
$values = array(
    "first_name" => "",
    "last_name" => "",
    "email" => "",
    "phone" => "",
    "question_1" => "What is your favorite snack?",
    "answer_1" => "",
    "question_2" => "What city were you born in?",
    "answer_2" => ""
);

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    foreach ($values as $key => $value) {
        $values[$key] = test_input($_POST[$key] ?? "");
    }

    $password = test_input($_POST["password"] ?? "");
    $confirmPassword = test_input($_POST["confirm_password"] ?? "");

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $safeEmail = mysqli_real_escape_string($dbc, $values["email"]);
        $check = mysqli_query($dbc, "SELECT id FROM users WHERE email = '$safeEmail' LIMIT 1");

        if ($check && mysqli_num_rows($check) > 0) {
            $error = "That email is already registered.";
        } else {
            $firstName = mysqli_real_escape_string($dbc, $values["first_name"]);
            $lastName = mysqli_real_escape_string($dbc, $values["last_name"]);
            $phone = mysqli_real_escape_string($dbc, $values["phone"]);
            $q1 = mysqli_real_escape_string($dbc, $values["question_1"]);
            $a1 = mysqli_real_escape_string($dbc, $values["answer_1"]);
            $q2 = mysqli_real_escape_string($dbc, $values["question_2"]);
            $a2 = mysqli_real_escape_string($dbc, $values["answer_2"]);
            $pw = mysqli_real_escape_string($dbc, $password);

            $sql = "INSERT INTO users
                (registration_date, first_name, last_name, email, phone, pw, role, security_question_1, security_answer_1, security_question_2, security_answer_2)
                VALUES (CURDATE(), '$firstName', '$lastName', '$safeEmail', '$phone', '$pw', 'student', '$q1', '$a1', '$q2', '$a2')";

            if (mysqli_query($dbc, $sql)) {
                $message = "Registration completed. You can log in now.";
                foreach ($values as $key => $value) {
                    $values[$key] = "";
                }
            } else {
                $error = "Registration could not be completed.";
            }
        }
    }
}

render_page_start("Create your student account", "registration.php");
?>
<?php if ($error !== "") { ?><div class="error-message"><?php echo h($error); ?></div><?php } ?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>Registration</h2>
    <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo h($values["first_name"]); ?>" required>
        </div>
        <div class="row">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo h($values["last_name"]); ?>" required>
        </div>
        <div class="row">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo h($values["email"]); ?>" required>
        </div>
        <div class="row">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo h($values["phone"]); ?>" placeholder="770-555-1111" required>
        </div>
        <div class="row">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="row">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="row">
            <label for="question_1">Security Question 1</label>
            <input type="text" id="question_1" name="question_1" value="<?php echo h($values["question_1"]); ?>" required>
        </div>
        <div class="row">
            <label for="answer_1">Answer 1</label>
            <input type="text" id="answer_1" name="answer_1" value="<?php echo h($values["answer_1"]); ?>" required>
        </div>
        <div class="row">
            <label for="question_2">Security Question 2</label>
            <input type="text" id="question_2" name="question_2" value="<?php echo h($values["question_2"]); ?>" required>
        </div>
        <div class="row">
            <label for="answer_2">Answer 2</label>
            <input type="text" id="answer_2" name="answer_2" value="<?php echo h($values["answer_2"]); ?>" required>
        </div>
        <div class="actions">
            <input type="submit" value="Register">
        </div>
    </form>
</section>

<?php render_page_end(); ?>
