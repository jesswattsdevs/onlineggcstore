<?php
include "includes/app_init.php";
include "connection.php";
require_login(array("admin"));

$message = "";

// Admin announcements are saved in the database and then displayed on the public home page.
if (isset($_POST["create_announcement"])) {
    $title = mysqli_real_escape_string($dbc, test_input($_POST["title"] ?? ""));
    $body = mysqli_real_escape_string($dbc, test_input($_POST["body"] ?? ""));
    mysqli_query($dbc, "INSERT INTO announcements (title, body, created_at, active) VALUES ('$title', '$body', NOW(), 1)");
    $message = "Announcement posted.";
}

// Instead of deleting announcements, this toggle lets the admin show or hide them.
if (isset($_POST["toggle_announcement"])) {
    $announcementId = (int) ($_POST["announcement_id"] ?? 0);
    mysqli_query($dbc, "UPDATE announcements SET active = 1 - active WHERE announcement_id = $announcementId");
    $message = "Announcement status updated.";
}

$announcements = fetch_all($dbc, "SELECT * FROM announcements ORDER BY created_at DESC");

render_page_start("Create and manage home page announcements", "admin_announcements.php");
?>
<?php if ($message !== "") { ?><div class="message"><?php echo h($message); ?></div><?php } ?>

<section class="panel">
    <h2>Post Announcement</h2>
    <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="row">
            <label for="body">Message</label>
            <textarea id="body" name="body" required></textarea>
        </div>
        <div class="actions">
            <input type="submit" name="create_announcement" value="Post Announcement">
        </div>
    </form>
</section>

<section class="panel">
    <h2>Existing Announcements</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php foreach ($announcements as $announcement) { ?>
                <tr>
                    <td><?php echo h($announcement["title"]); ?></td>
                    <td><?php echo h($announcement["body"]); ?></td>
                    <td>
                        <form method="post" class="inline-form" action="<?php echo h($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="announcement_id" value="<?php echo (int) $announcement["announcement_id"]; ?>">
                            <input type="submit" name="toggle_announcement" value="<?php echo (int) $announcement["active"] === 1 ? "Hide" : "Show"; ?>">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<?php render_page_end(); ?>
