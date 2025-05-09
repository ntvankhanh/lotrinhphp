<?php
function log_activity($action) {
    $date = date("Y-m-d");
    $log_dir = "logs/";
    $file = $log_dir . "log_$date.txt";
    $time = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];
    $action = htmlspecialchars(trim($action)); // Sanitize input
    $log_entry = "$time - $action - IP: $ip\n";

    // Ensure logs directory exists
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    // Kiểm tra file log tồn tại chưa, nếu chưa thì tạo mới
    if (!file_exists($file)) {
        file_put_contents($file, "=== Log for $date ===\n");
    }

    // Ghi log vào file
    file_put_contents($file, $log_entry, FILE_APPEND);
}
?>
