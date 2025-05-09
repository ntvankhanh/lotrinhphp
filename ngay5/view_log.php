<!-- view_log.php -->
<?php
if (isset($_POST['date'])) {
    $date = $_POST['date'];
    $log_file = "logs/log_$date.txt";

    if (file_exists($log_file)) {
        $logs = file_get_contents($log_file);
        echo "<pre>$logs</pre>";
    } else {
        echo "Không có nhật ký cho ngày này.";
    }
}
?>
<form method="POST">
    <input type="date" name="date" required>
    <button type="submit">Xem nhật ký</button>
</form>
