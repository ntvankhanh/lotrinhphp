<?php
// db.php - Kết nối PDO tới MySQL với xử lý lỗi

$host = '127.0.0.1';
$dbname = 'tech_factory';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Kết nối với cơ sở dữ liệu đã chỉ định
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
