<!-- index.php -->
<?php
include('includes/header.php');
include('includes/logger_fixed.php');
include('includes/upload_fixed.php');

$log_message = '';
$file_message = '';

// Ghi nhật ký hành động và xử lý upload file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = trim($_POST['action']);
        if ($action !== '') {
            log_activity($action);
            $log_message = "Hành động đã được ghi vào nhật ký.";
        } else {
            $log_message = "Hành động không được để trống.";
        }
    }

    if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file_message = upload_file($_FILES['file']);
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Hành động: <input type="text" name="action" required></label><br>
    <label>Upload file minh chứng: <input type="file" name="file"></label><br>
    <button type="submit">Ghi nhật ký</button>
</form>

<?php
if ($log_message !== '') {
    echo "<p>$log_message</p>";
}
if ($file_message !== '') {
    echo "<p>$file_message</p>";
}
?>

<?php include('view_log_fixed.php'); ?>

</body>
</html>
