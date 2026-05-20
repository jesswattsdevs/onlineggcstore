<?php
function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES);
}

function test_input($data)
{
    return htmlspecialchars(stripslashes(trim((string) $data)), ENT_QUOTES);
}

function fetch_all($dbc, $sql)
{
    // Reusable helper that turns a mysqli result into a plain PHP array for easier page rendering.
    $rows = array();
    $result = mysqli_query($dbc, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);
    }
    return $rows;
}

function item_image_path($filename)
{
    // If an item has no uploaded image yet, fall back to the default store placeholder.
    if ($filename && file_exists(__DIR__ . "/../uploads/" . $filename)) {
        return "uploads/" . rawurlencode($filename);
    }
    return "assets/ggc-placeholder.png";
}

function current_user($dbc)
{
    if (!isset($_SESSION["user_id"])) {
        return null;
    }

    $userId = (int) $_SESSION["user_id"];
    $sql = "SELECT * FROM users WHERE id = $userId LIMIT 1";
    $result = mysqli_query($dbc, $sql);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function user_unsold_count($dbc, $userId)
{
    $userId = (int) $userId;
    $sql = "SELECT COUNT(*) AS total FROM items WHERE seller_id = $userId AND status = 'available'";
    $result = mysqli_query($dbc, $sql);
    $row = $result ? mysqli_fetch_assoc($result) : array("total" => 0);
    return (int) ($row["total"] ?? 0);
}

function handle_upload($fieldName, $existing = "")
{
    // This helper validates the file type and saves uploaded images into the shared uploads folder.
    if (!isset($_FILES[$fieldName]) || ($_FILES[$fieldName]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }

    if (($_FILES[$fieldName]["error"] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return $existing;
    }

    $original = basename((string) $_FILES[$fieldName]["name"]);
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowed = array("jpg", "jpeg", "png", "gif", "webp");
    if (!in_array($extension, $allowed, true)) {
        return $existing;
    }

    $newName = uniqid("img_", true) . "." . $extension;
    $target = __DIR__ . "/../uploads/" . $newName;
    if (move_uploaded_file($_FILES[$fieldName]["tmp_name"], $target)) {
        return $newName;
    }

    return $existing;
}

function upload_problem_message($fieldName)
{
    if (!isset($_FILES[$fieldName])) {
        return "Please choose an image file.";
    }

    $errorCode = $_FILES[$fieldName]["error"] ?? UPLOAD_ERR_NO_FILE;

    if ($errorCode === UPLOAD_ERR_NO_FILE) {
        return "Please choose an image file.";
    }

    if ($errorCode !== UPLOAD_ERR_OK) {
        return "The image upload failed. Please try a smaller JPG or PNG file.";
    }

    $original = basename((string) ($_FILES[$fieldName]["name"] ?? ""));
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowed = array("jpg", "jpeg", "png", "gif", "webp");

    if (!in_array($extension, $allowed, true)) {
        return "Please upload a JPG, JPEG, PNG, GIF, or WEBP image.";
    }

    if (!is_writable(__DIR__ . "/../uploads/")) {
        return "The uploads folder is not writable yet. Fix the folder permission and try again.";
    }

    return "The image could not be saved to the uploads folder.";
}
?>
