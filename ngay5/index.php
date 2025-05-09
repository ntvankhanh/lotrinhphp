<!-- index.php -->
<?php
include('includes/header.php');
include('includes/logger.php');
include('includes/upload.php');

// Ghi nhật ký hành động
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        log_activity($_POST['action']);
    }

    if (isset($_FILES['file'])) {
        $file_message = upload_file($_FILES['file']);
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Hành động: <input type="text" name="action" required></label><br>
    <label>Upload file minh chứng: <input type="file" name="file"></label><br>
    <button type="submit">Ghi nhật ký</button>
</form>

<?php if (isset($file_message)) echo "<p>$file_message</p>"; ?>

<?php include('view_log.php'); ?>

</body>
</html>
