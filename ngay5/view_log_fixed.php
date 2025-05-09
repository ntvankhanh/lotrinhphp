<?php
$log_content = '';
$message = '';
$date = date('Y-m-d'); // Default to today

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    $date = $_POST['date'];
    $log_file = "logs/log_$date.txt";

    if (file_exists($log_file)) {
        $logs = file_get_contents($log_file);
        $log_content = htmlspecialchars($logs);
    } else {
        $message = "Không có nhật ký cho ngày này.";
    }
}
?>
<form method="POST">
    <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
    <button type="submit">Xem nhật ký</button>
</form>

<?php
if ($log_content) {
    echo "<pre>$log_content</pre>";
} elseif ($message) {
    echo "<p>$message</p>";
}
?>
