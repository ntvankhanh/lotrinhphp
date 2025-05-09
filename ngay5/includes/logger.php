<!-- logger.php -->
<?php
function log_activity($action) {
    $date = date("Y-m-d");
    $file = "logs/log_$date.txt";
    $time = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_entry = "$time - $action - IP: $ip\n";

    // Kiểm tra file log tồn tại chưa, nếu chưa thì tạo mới
    if (!file_exists($file)) {
        file_put_contents($file, "=== Log for $date ===\n");
    }

    // Ghi log vào file
    file_put_contents($file, $log_entry, FILE_APPEND);
}
?>
